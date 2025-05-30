<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Booking;
use App\Models\Availability;
use App\Models\Service; // Add this import
use App\Models\User; // Add this import
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth; // Add this import

class CalendarController extends Controller
{
    /**
     * Show the instructor calendar dashboard
     */
    public function index()
    {
        try {
            $instructor = auth()->user()->instructor;
            
            // Get the current date for "today" reference
            $today = Carbon::today();
            
            // Initialize empty collections to avoid undefined variable errors
            $bookings = collect();
            $availabilities = collect();
            
            return view('instructor.calendar.index', compact('today', 'bookings', 'availabilities'));
            
        } catch (\Exception $e) {
            Log::error('Calendar Error: ' . $e->getMessage());
            return back()->with('error', 'Error loading calendar data');
        }
    }
    
    /**
     * Get calendar data (availabilities and bookings) for AJAX requests
     */
    public function getCalendarData()
    {
        try {
            $instructor = auth()->user()->instructor;
            
            // Fetch data for current and next 2 months
            $startDate = Carbon::now()->startOfMonth();
            $endDate = Carbon::now()->addMonths(2)->endOfMonth();
            
            // Get availabilities
            $availabilities = Availability::where('instructor_id', $instructor->id)
                ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                ->orderBy('date')
                ->orderBy('start_time')
                ->get()
                ->map(function($availability) {
                    return [
                        'id' => $availability->id,
                        'date' => $availability->date->format('Y-m-d'),
                        'start_time' => $availability->start_time,
                        'end_time' => $availability->end_time,
                        'visibility' => $availability->visibility ?? 'public',
                    ];
                });
            
            // Get bookings
            $bookings = Booking::where('instructor_id', $instructor->id)
                ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                ->with(['user', 'service', 'suburb'])
                ->orderBy('date')
                ->orderBy('start_time')
                ->get()
                ->map(function($booking) {
                    return [
                        'id' => $booking->id,
                        'date' => $booking->date->format('Y-m-d'),
                        'start_time' => $booking->start_time,
                        'end_time' => $booking->end_time,
                        'status' => $booking->status,
                        'user' => [
                            'id' => $booking->user->id,
                            'name' => $booking->user->name
                        ],
                        'service' => [
                            'id' => $booking->service->id,
                            'name' => $booking->service->name
                        ],
                        'suburb' => $booking->suburb ? [
                            'id' => $booking->suburb->id,
                            'name' => $booking->suburb->name
                        ] : null
                    ];
                });
                
            return response()->json([
                'availabilities' => $availabilities,
                'bookings' => $bookings
            ]);
            
        } catch (\Exception $e) {
            Log::error('Calendar Data Error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to load calendar data'], 500);
        }
    }
    
    /**
     * Show availability management page for the instructor
     */
    public function availability(Request $request)
    {
        // Get month parameter or default to current month
        $monthParam = $request->query('month');
        
        if ($monthParam) {
            try {
                $viewMonth = Carbon::createFromFormat('Y-m', $monthParam);
            } catch (\Exception $e) {
                $viewMonth = now();
            }
        } else {
            $viewMonth = now();
        }
        
        // Set start and end dates for calendar display
        $startDate = $viewMonth->copy()->startOfMonth()->startOfWeek();
        $endDate = $viewMonth->copy()->endOfMonth()->endOfWeek();
        
        // Navigation variables
        $prevMonth = $viewMonth->copy()->subMonth()->format('Y-m');
        $nextMonth = $viewMonth->copy()->addMonth()->format('Y-m');
        
        $instructor = auth()->user()->instructor;
        
        // Get the current date for "today" reference
        $today = Carbon::today();

        // Fetch availabilities for the instructor (for the current view period)
        $availabilities = Availability::where('instructor_id', $instructor->id)
            ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->orderBy('date')
            ->orderBy('start_time')
            ->get();
        
        // Group availabilities by date for easier use in template
        $groupedAvailabilities = $availabilities->groupBy(function($availability) {
            return $availability->date->format('Y-m-d');
        });

        return view('instructor.calendar.availability', compact(
            'availabilities', 
            'today',
            'viewMonth',
            'startDate',
            'endDate',
            'prevMonth',
            'nextMonth',
            'groupedAvailabilities'
        ));
    }
    
    /**
     * Store a new availability
     */
    public function storeAvailability(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'duration' => 'required|integer|min:15',
            'visibility' => 'required|in:public,private,hidden,note',
            'private_note' => 'nullable|string|max:500',
            'public_note' => 'nullable|string|max:500',
            'suburbs' => 'nullable|array',
            'bulk_action' => 'nullable|boolean',
        ]);

        DB::beginTransaction();
        
        try {
            $instructor = auth()->user()->instructor;
            
            // Convert duration to integer to ensure it's not a string
            $durationMinutes = (int) $validated['duration'];
            
            // Calculate end time based on duration
            $startTime = Carbon::createFromFormat('H:i', $validated['start_time']);
            $endTime = $startTime->copy()->addMinutes($durationMinutes);
            
            // Check for overlapping availability
            $existingAvailability = Availability::where('instructor_id', $instructor->id)
                ->where('date', $validated['date'])
                ->where(function($query) use ($validated, $endTime) {
                    $query->whereBetween('start_time', [$validated['start_time'], $endTime->format('H:i:s')])
                        ->orWhereBetween('end_time', [$validated['start_time'], $endTime->format('H:i:s')])
                        ->orWhere(function($q) use ($validated, $endTime) {
                            $q->where('start_time', '<=', $validated['start_time'])
                                ->where('end_time', '>=', $endTime->format('H:i:s'));
                        });
                })
                ->exists();
                
            if ($existingAvailability) {
                return back()->with('error', 'This time slot overlaps with existing availability.');
            }
            
            // Handle suburbs - ensure it's an array and has default value
            $suburbs = $validated['suburbs'] ?? [];
            if (empty($suburbs)) {
                $suburbs = ['all'];
            }
            
            // Prepare data for creation
            $availabilityData = [
                'instructor_id' => $instructor->id,
                'date' => $validated['date'],
                'start_time' => $validated['start_time'],
                'end_time' => $endTime->format('H:i:s'),
                'is_available' => true,
                'visibility' => $validated['visibility'],
                'private_note' => $validated['private_note'],
                'public_note' => $validated['public_note'],
                'suburbs' => $suburbs,
                'duration_minutes' => $durationMinutes,
            ];
            
            // Create availability
            Availability::create($availabilityData);
            
            DB::commit();
            
            return redirect()->back()->with('success', 'Availability added successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error adding availability: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return back()->with('error', 'Error adding availability: ' . $e->getMessage());
        }
    }
    
    /**
     * Delete an availability
     */
    public function destroyAvailability($availabilityId)
    {
        $instructor = auth()->user()->instructor;
        
        $availability = Availability::where('instructor_id', $instructor->id)
            ->findOrFail($availabilityId);
        
        try {
            $availability->delete();
            return redirect()->back()->with('success', 'Availability removed successfully.');
        } catch (\Exception $e) {
            Log::error('Error removing availability: ' . $e->getMessage());
            return back()->with('error', 'Error removing availability: ' . $e->getMessage());
        }
    }
    
    /**
     * Get booking details 
     */
    public function getBookingDetails($bookingId)
    {
        try {
            $instructor = auth()->user()->instructor;
            
            $booking = Booking::where('instructor_id', $instructor->id)
                ->where('id', $bookingId)
                ->with(['user', 'service', 'suburb'])
                ->firstOrFail();
                
            return response()->json($booking);
        } catch (\Exception $e) {
            Log::error('Error getting booking details: ' . $e->getMessage());
            return response()->json(['error' => 'Booking not found'], 404);
        }
    }

    /**
     * Store a new booking created by instructor
     */
    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'start_time' => 'required',
            'service_id' => 'required|exists:services,id',
            'suburb_id' => 'required',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_type' => 'required|in:new,returning',
            'pickup_location' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            // Get the service to calculate duration and end time
            $service = Service::findOrFail($request->service_id);
            
            // Calculate end time
            $startDateTime = Carbon::parse($request->date . ' ' . $request->start_time);
            $endDateTime = $startDateTime->copy()->addMinutes($service->duration);

            // Check if instructor has availability for this time slot
            $instructor = Auth::user()->instructor;
            $hasAvailability = $instructor->availabilities()
                ->where('date', $request->date)
                ->where('start_time', '<=', $request->start_time)
                ->where('end_time', '>=', $endDateTime->format('H:i:s'))
                ->exists();

            if (!$hasAvailability) {
                return response()->json([
                    'error' => 'No availability found for the selected time slot.'
                ], 422);
            }

            // Check for conflicts with existing bookings
            $hasConflict = Booking::where('instructor_id', $instructor->id)
                ->where('date', $request->date)
                ->where('status', '!=', 'cancelled')
                ->where(function ($query) use ($request, $endDateTime) {
                    $query->whereBetween('start_time', [$request->start_time, $endDateTime->format('H:i:s')])
                          ->orWhereBetween('end_time', [$request->start_time, $endDateTime->format('H:i:s')])
                          ->orWhere(function ($q) use ($request, $endDateTime) {
                              $q->where('start_time', '<=', $request->start_time)
                                ->where('end_time', '>=', $endDateTime->format('H:i:s'));
                          });
                })
                ->exists();

            if ($hasConflict) {
                return response()->json([
                    'error' => 'There is already a booking for this time slot.'
                ], 422);
            }

            // Find or create customer
            $customer = User::where('email', $request->customer_email)->first();
            
            if (!$customer) {
                // Create new customer
                $customer = User::create([
                    'name' => $request->customer_name,
                    'email' => $request->customer_email,
                    'phone' => $request->customer_phone,
                    'role' => 'student',
                    'email_verified_at' => now(), // Auto-verify for instructor-created accounts
                    'password' => bcrypt(str_random(12)), // Generate a random secure password
                    'status' => 'active',
                ]);

                // Create student profile
                $customer->student()->create([
                    'date_of_birth' => null,
                    'license_number' => null,
                    'emergency_contact_name' => null,
                    'emergency_contact_phone' => null,
                ]);
                
                // You may want to trigger a welcome email here with account details
                // Mail::to($customer->email)->send(new NewStudentAccountCreated($customer));
            }

            // Create the booking
            $booking = Booking::create([
                'user_id' => $customer->id,
                'instructor_id' => $instructor->id,
                'service_id' => $service->id,
                'suburb_id' => $request->suburb_id,
                'date' => $request->date,
                'start_time' => $request->start_time,
                'end_time' => $endDateTime->format('H:i:s'),
                'pickup_location' => $request->pickup_location,
                'notes' => $request->notes,
                'status' => 'confirmed',
                'total_amount' => $service->price,
                'booking_type' => 'instructor_created',
            ]);

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Booking created successfully!',
                    'booking' => $booking->load(['user', 'service', 'suburb'])
                ]);
            }

            return redirect()->route('instructor.bookings.index')
                ->with('success', 'Booking created successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error creating booking: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            if ($request->ajax()) {
                return response()->json([
                    'error' => 'An error occurred while creating the booking: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'An error occurred while creating the booking: ' . $e->getMessage());
        }
    }

}
