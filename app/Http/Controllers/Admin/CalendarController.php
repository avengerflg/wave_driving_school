<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Booking;
use App\Models\Availability;
use App\Models\User;
use App\Models\Instructor;
use App\Models\Service;
use App\Models\Suburb;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CalendarController extends Controller
{
    /**
     * Show the admin calendar dashboard with all instructors overview
     */
    public function index()
    {
        try {
            // Get all active instructors with their user relationship
            $instructors = User::where('role', 'instructor')
                ->where('status', 'active')
                ->with('instructor')
                ->get();
            
            // Get the current date for "today" reference
            $today = Carbon::today();
            
            // Get services for booking creation
            $services = Service::where('active', true)->get();
            
            // Get suburbs for booking creation
            $suburbs = Suburb::where('active', true)->get();
            
            return view('admin.calendar.index', compact('today', 'instructors', 'services', 'suburbs'));
            
        } catch (\Exception $e) {
            Log::error('Admin Calendar Error: ' . $e->getMessage());
            return back()->with('error', 'Error loading calendar data');
        }
    }

    /**
     * Get booking form data for AJAX requests
     */
    public function getBookingFormData()
    {
        try {
            // Get all users (customers) with basic info
            $users = User::where('role', 'student')
                ->where('status', 'active')
                ->select('id', 'name', 'email', 'phone')
                ->orderBy('name')
                ->get();

            // Get all active services
            $services = Service::where('active', true)
                ->select('id', 'name', 'duration', 'price', 'description')
                ->orderBy('name')
                ->get();

            // Get all active suburbs
            $suburbs = Suburb::where('active', true)
                ->select('id', 'name')
                ->orderBy('name')
                ->get();

            return response()->json([
                'users' => $users,
                'services' => $services,
                'suburbs' => $suburbs
            ]);

        } catch (\Exception $e) {
            Log::error('Error loading booking form data: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to load form data',
                'users' => [],
                'services' => [],
                'suburbs' => []
            ], 500);
        }
    }
    
    /**
     * Get calendar data (availabilities and bookings) for AJAX requests
     */
    public function getCalendarData(Request $request)
        {
            try {
                // Get instructor ID from request or show all if not specified
                $instructorId = $request->input('instructor_id');
                
                // Fetch data for current and next 2 months
                $startDate = Carbon::now()->startOfMonth();
                $endDate = Carbon::now()->addMonths(2)->endOfMonth();
                
                $query = Availability::whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);
                
                if ($instructorId) {
                    $query->where('instructor_id', $instructorId);
                }
                
                // Get raw availabilities (15-minute slots)
                $rawAvailabilities = $query->orderBy('date')
                    ->orderBy('start_time')
                    ->where('is_available', true) // Only get available slots
                    ->get();
                
                // Group 15-minute slots into 30-minute display slots for the calendar
                $availabilities = collect();
                $groupedByDateAndInstructor = $rawAvailabilities->groupBy(function($availability) {
                    return $availability->date->format('Y-m-d') . '_' . $availability->instructor_id;
                });
                
                foreach ($groupedByDateAndInstructor as $dayInstructorKey => $daySlots) {
                    // Sort slots by start time
                    $sortedSlots = $daySlots->sortBy('start_time');
                    
                    // Group consecutive 15-minute slots into 30-minute periods
                    $currentGroup = [];
                    $lastEndTime = null;
                    
                    foreach ($sortedSlots as $slot) {
                        $slotStart = Carbon::parse($slot->start_time);
                        
                        // If this is the first slot or consecutive to the last one
                        if (empty($currentGroup) || ($lastEndTime && $lastEndTime->eq($slotStart))) {
                            $currentGroup[] = $slot;
                            $lastEndTime = Carbon::parse($slot->end_time);
                        } else {
                            // Process the previous group if it has enough duration (30+ minutes)
                            if (!empty($currentGroup)) {
                                $this->addGroupedAvailability($currentGroup, $availabilities);
                            }
                            
                            // Start new group
                            $currentGroup = [$slot];
                            $lastEndTime = Carbon::parse($slot->end_time);
                        }
                    }
                    
                    // Process the last group
                    if (!empty($currentGroup)) {
                        $this->addGroupedAvailability($currentGroup, $availabilities);
                    }
                }
                
                // Get bookings query
                $bookingQuery = Booking::whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);
                
                if ($instructorId) {
                    $bookingQuery->where('instructor_id', $instructorId);
                }
                
                // Get bookings
$bookings = $bookingQuery->with(['user', 'service', 'suburb', 'instructor.user'])
    ->orderBy('date')
    ->orderBy('start_time')
    ->get()
    ->map(function($booking) {
        // Handle different time formats
        $startTime = $booking->start_time;
        $endTime = $booking->end_time;
        
        // If start_time contains a date, extract just the time part
        if (strlen($startTime) > 8) {
            $startTime = Carbon::parse($startTime)->format('H:i:s');
        }
        
        if (strlen($endTime) > 8) {
            $endTime = Carbon::parse($endTime)->format('H:i:s');
        }
        
        return [
            'id' => $booking->id,
            'date' => $booking->date->format('Y-m-d'),
            'start_time' => $startTime,
            'end_time' => $endTime,
            'status' => $booking->status,
            'instructor_id' => $booking->instructor_id,
            'instructor_name' => $booking->instructor->user->name ?? 'Unknown',
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
                    'availabilities' => $availabilities->values(), // Reset array keys
                    'bookings' => $bookings
                ]);
                
            } catch (\Exception $e) {
                Log::error('Admin Calendar Data Error: ' . $e->getMessage());
                return response()->json(['error' => 'Failed to load calendar data'], 500);
            }
        }

        /**
 * Helper method to add grouped availability to the collection
 */
private function addGroupedAvailability($slots, &$availabilities)
{
    if (empty($slots)) return;
    
    $firstSlot = $slots[0];
    $lastSlot = end($slots);
    
    // Calculate total duration
    $startTime = Carbon::parse($firstSlot->start_time);
    $endTime = Carbon::parse($lastSlot->end_time);
    $durationMinutes = $startTime->diffInMinutes($endTime);
    
    // Only create availability entries for groups that have at least 30 minutes
    if ($durationMinutes >= 30) {
        // Get instructor info
        $instructor = Instructor::with('user')->find($firstSlot->instructor_id);
        
        // Create 30-minute slots from the continuous availability
        $currentStart = $startTime->copy();
        
        while ($currentStart->copy()->addMinutes(30)->lte($endTime)) {
            $slotEnd = $currentStart->copy()->addMinutes(30);
            
            $availabilities->push([
                'id' => $firstSlot->id,
                'date' => $firstSlot->date->format('Y-m-d'),
                'start_time' => $currentStart->format('H:i:s'),
                'end_time' => $slotEnd->format('H:i:s'),
                'visibility' => $firstSlot->visibility ?? 'public',
                'instructor_id' => $firstSlot->instructor_id,
                'instructor_name' => $instructor->user->name ?? 'Unknown',
                'is_available' => true,
                'is_booked' => false,
                'booking_id' => null,
                'booking_details' => null
            ]);
            
            $currentStart->addMinutes(30);
        }
    }
}

/**
 * Debug method to list all instructors and their IDs
 */
public function debugInstructors(Request $request)
{
    $instructors = User::where('role', 'instructor')
        ->with('instructor')
        ->get();
    
    $result = [
        'total_instructor_users' => $instructors->count(),
        'instructors' => $instructors->map(function($user) {
            return [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_email' => $user->email,
                'user_status' => $user->status,
                'instructor_profile_exists' => $user->instructor ? true : false,
                'instructor_id' => $user->instructor ? $user->instructor->id : null,
                'instructor_details' => $user->instructor ? [
                    'id' => $user->instructor->id,
                    'license_number' => $user->instructor->license_number,
                    'vehicle_type' => $user->instructor->vehicle_type,
                    'status' => $user->instructor->status
                ] : null
            ];
        })
    ];
    
    return response()->json($result);
}
    
    /**
     * Show availability management page for all instructors
     */
    public function availability(Request $request)
    {
        // Get instructor ID from request or show all if not specified
        $instructorId = $request->input('instructor_id');
        $instructor = null;
        
        if ($instructorId) {
            $instructor = User::where('role', 'instructor')->with('instructor')->findOrFail($instructorId);
        }
        
        // Get all active instructors for the dropdown
        $instructors = User::where('role', 'instructor')
            ->where('status', 'active')
            ->with('instructor')
            ->get();
        
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
        
        // Get the current date for "today" reference
        $today = Carbon::today();

        // Fetch availabilities
        $query = Availability::whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);
        
        if ($instructorId) {
            $query->where('instructor_id', $instructorId);
        }
        
        $availabilities = $query->orderBy('date')
            ->orderBy('start_time')
            ->with('instructor.user')
            ->get();
        
        // Group availabilities by date for easier use in template
        $groupedAvailabilities = $availabilities->groupBy(function($availability) {
            return $availability->date->format('Y-m-d');
        });

        return view('admin.calendar.availability', compact(
            'instructors',
            'instructor',
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
            'instructor_id' => 'required|exists:instructors,id',
            'date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'duration' => 'required|integer|min:15',
            'visibility' => 'required|in:public,private,hidden,note',
            'private_note' => 'nullable|string|max:500',
            'public_note' => 'nullable|string|max:500',
            'suburbs' => 'nullable|array',
            'is_recurring' => 'sometimes|boolean',
            'recur_until' => 'required_if:is_recurring,1|nullable|date|after:date',
            'days_of_week' => 'required_if:is_recurring,1|array',
            'days_of_week.*' => 'integer|min:0|max:6',
        ]);

        DB::beginTransaction();
        
        try {
            // Convert duration to integer and calculate end time
            $durationMinutes = (int) $validated['duration'];
            $startTime = Carbon::createFromFormat('H:i', $validated['start_time']);
            $endTime = $startTime->copy()->addMinutes($durationMinutes);
            
            // Handle suburbs
            $suburbs = $validated['suburbs'] ?? ['all'];
            
            // Handle recurring availabilities
            if (isset($validated['is_recurring']) && $validated['is_recurring']) {
                $startDate = Carbon::parse($validated['date']);
                $endDate = Carbon::parse($validated['recur_until']);
                $daysOfWeek = $validated['days_of_week'];
                
                $createdCount = 0;
                $currentDate = $startDate->copy();

                while ($currentDate->lte($endDate)) {
                    if (in_array((int)$currentDate->dayOfWeek, $daysOfWeek)) {
                        $createdCount += $this->createSlotsForDate(
                            $validated['instructor_id'],
                            $currentDate,
                            $validated['start_time'],
                            $endTime->format('H:i:s'),
                            $validated,
                            $suburbs
                        );
                    }
                    $currentDate->addDay();
                }
            } else {
                // Create single availability
                $createdCount = $this->createSlotsForDate(
                    $validated['instructor_id'],
                    Carbon::parse($validated['date']),
                    $validated['start_time'],
                    $endTime->format('H:i:s'),
                    $validated,
                    $suburbs
                );
            }
            
            DB::commit();
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "{$createdCount} availability slots created successfully"
                ]);
            }
            
            return redirect()->back()->with('success', "{$createdCount} availability slots created successfully.");
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error adding availability: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            if ($request->ajax()) {
                return response()->json([
                    'error' => 'Error adding availability: ' . $e->getMessage()
                ], 500);
            }
            
            return back()->with('error', 'Error adding availability: ' . $e->getMessage());
        }
    }

    /**
     * Create availability slots for a specific date - based on instructor controller
     */
    private function createSlotsForDate($instructorId, Carbon $date, $startTime, $endTime, $validated, $suburbs)
    {
        $createdCount = 0;
        $start = Carbon::parse($startTime);
        $end = Carbon::parse($endTime);
        
        // Create slots based on duration (15-minute intervals like instructor controller)
        $slotDuration = 15; // minutes
        
        while ($start->lt($end)) {
            $slotEnd = $start->copy()->addMinutes($slotDuration);
            
            // Don't create a slot that would exceed the end time
            if ($slotEnd->gt($end)) {
                break;
            }
            
            // Check if this exact slot already exists
            $existingSlot = Availability::where('instructor_id', $instructorId)
                ->where('date', $date->format('Y-m-d'))
                ->where('start_time', $start->format('H:i:s'))
                ->where('end_time', $slotEnd->format('H:i:s'))
                ->first();
            
            if (!$existingSlot) {
                Availability::create([
                    'instructor_id' => $instructorId,
                    'date' => $date->format('Y-m-d'),
                    'start_time' => $start->format('H:i:s'),
                    'end_time' => $slotEnd->format('H:i:s'),
                    'is_available' => true,
                    'visibility' => $validated['visibility'] ?? 'public',
                    'private_note' => $validated['private_note'] ?? null,
                    'public_note' => $validated['public_note'] ?? null,
                    'suburbs' => $suburbs,
                ]);
                $createdCount++;
            }
            
            $start = $slotEnd;
        }
        
        return $createdCount;
    }
    
    /**
     * Delete an availability
     */
    public function destroyAvailability($availabilityId)
    {
        try {
            $availability = Availability::findOrFail($availabilityId);
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
            $booking = Booking::where('id', $bookingId)
                ->with(['user', 'service', 'suburb', 'instructor.user'])
                ->firstOrFail();
                
            return response()->json([
                'id' => $booking->id,
                'date' => $booking->date->format('Y-m-d'),
                'start_time' => $booking->start_time,
                'end_time' => $booking->end_time,
                'status' => $booking->status,
                'notes' => $booking->notes,
                'pickup_location' => $booking->pickup_location,
                'total_amount' => $booking->total_amount,
                'booking_type' => $booking->booking_type,
                'user' => [
                    'id' => $booking->user->id,
                    'name' => $booking->user->name,
                    'email' => $booking->user->email,
                    'phone' => $booking->user->phone
                ],
                'instructor' => [
                    'id' => $booking->instructor->id,
                    'name' => $booking->instructor->user->name,
                    'email' => $booking->instructor->user->email,
                    'phone' => $booking->instructor->user->phone
                ],
                'service' => [
                    'id' => $booking->service->id,
                    'name' => $booking->service->name,
                    'duration' => $booking->service->duration,
                    'price' => $booking->service->price
                ],
                'suburb' => $booking->suburb ? [
                    'id' => $booking->suburb->id,
                    'name' => $booking->suburb->name
                ] : null
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting booking details: ' . $e->getMessage());
            return response()->json(['error' => 'Booking not found'], 404);
        }
    }

    /**
     * Store a new booking created by admin
     */
    public function store(Request $request)
    {
        // Enhanced validation for both new and existing customers
        $rules = [
            'instructor_id' => 'required|exists:instructors,id',
            'date' => 'required|date|after_or_equal:today',
            'start_time' => 'required',
            'service_id' => 'required|exists:services,id',
            'suburb_id' => 'required|exists:suburbs,id',
            'pickup_location' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:1000',
            'status' => 'nullable|in:pending,confirmed,completed,cancelled'
        ];

        // Check if creating new customer or using existing
        if ($request->has('customer_name') && $request->filled('customer_name')) {
            // New customer validation
            $rules['customer_name'] = 'required|string|max:255';
            $rules['customer_email'] = 'required|email|max:255|unique:users,email';
            $rules['customer_phone'] = 'required|string|max:20';
            $rules['customer_license'] = 'nullable|string|max:50';
        } else {
            // Existing customer validation
            $rules['user_id'] = 'required|exists:users,id';
        }

        $validated = $request->validate($rules);

        try {
            DB::beginTransaction();

            // Get the service to calculate duration and end time
            $service = Service::findOrFail($validated['service_id']);
            
            // Ensure duration is an integer
            $serviceDuration = (int) $service->duration;
            
            // Calculate end time
            $startDateTime = Carbon::parse($validated['date'] . ' ' . $validated['start_time']);
            $endDateTime = $startDateTime->copy()->addMinutes($serviceDuration);

            // Check if instructor has enough consecutive availability slots for this booking
            $hasAvailability = $this->checkAvailabilityForBooking(
                $validated['instructor_id'],
                $validated['date'],
                $validated['start_time'],
                $serviceDuration
            );

            if (!$hasAvailability) {
                // Let's get more specific error information
                $availabilitySlots = Availability::where('instructor_id', $validated['instructor_id'])
                    ->where('date', $validated['date'])
                    ->where('is_available', true)
                    ->orderBy('start_time')
                    ->get();
                
                $errorMessage = "No availability found for the selected time slot. ";
                if ($availabilitySlots->isEmpty()) {
                    $errorMessage .= "The instructor has no available slots on this date.";
                } else {
                    $errorMessage .= "Available slots: " . $availabilitySlots->map(function($slot) {
                        return $slot->start_time . '-' . $slot->end_time;
                    })->join(', ') . ". Service requires {$serviceDuration} minutes starting at {$validated['start_time']}.";
                }
                
                Log::warning("Booking creation failed - availability check", [
                    'instructor_id' => $validated['instructor_id'],
                    'date' => $validated['date'],
                    'start_time' => $validated['start_time'],
                    'service_duration' => $serviceDuration,
                    'available_slots' => $availabilitySlots->toArray()
                ]);
                
                if ($request->ajax()) {
                    return response()->json(['error' => $errorMessage], 422);
                }
                return back()->withInput()->with('error', $errorMessage);
            }

            // Check for conflicts with existing bookings
            $hasConflict = Booking::where('instructor_id', $validated['instructor_id'])
                ->where('date', $validated['date'])
                ->where('status', '!=', 'cancelled')
                ->where(function ($query) use ($validated, $endDateTime) {
                    $query->whereBetween('start_time', [$validated['start_time'], $endDateTime->format('H:i:s')])
                        ->orWhereBetween('end_time', [$validated['start_time'], $endDateTime->format('H:i:s')])
                        ->orWhere(function ($q) use ($validated, $endDateTime) {
                            $q->where('start_time', '<=', $validated['start_time'])
                                ->where('end_time', '>=', $endDateTime->format('H:i:s'));
                        });
                })
                ->exists();

            if ($hasConflict) {
                if ($request->ajax()) {
                    return response()->json([
                        'error' => 'There is already a booking for this time slot.'
                    ], 422);
                }
                return back()->withInput()->with('error', 'There is already a booking for this time slot.');
            }

            // Handle customer - new or existing
            if (isset($validated['customer_name']) && $validated['customer_name']) {
                // Create new customer
                $tempPassword = Str::random(12);
                $customer = User::create([
                    'name' => $validated['customer_name'],
                    'email' => $validated['customer_email'],
                    'phone' => $validated['customer_phone'],
                    'role' => 'student',
                    'email_verified_at' => now(),
                    'password' => Hash::make($tempPassword),
                    'status' => 'active',
                ]);

                // Create student profile if it doesn't exist
                if (!$customer->student) {
                    $customer->student()->create([
                        'date_of_birth' => null,
                        'license_number' => $validated['customer_license'] ?? null,
                        'emergency_contact_name' => null,
                        'emergency_contact_phone' => null,
                    ]);
                }
            } else {
                // Use existing customer
                $customer = User::findOrFail($validated['user_id']);
            }

            // Create the booking
            $booking = Booking::create([
                'user_id' => $customer->id,
                'instructor_id' => $validated['instructor_id'],
                'service_id' => $service->id,
                'suburb_id' => $validated['suburb_id'],
                'date' => $validated['date'],
                'start_time' => $validated['start_time'],
                'end_time' => $endDateTime->format('H:i:s'),
                'pickup_location' => $validated['pickup_location'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'status' => $validated['status'] ?? 'confirmed',
                'total_amount' => $service->price,
                'booking_type' => 'admin_created',
            ]);

            // Mark the availability slots as booked (optional)
            $this->markSlotsAsBooked($validated['instructor_id'], $validated['date'], $validated['start_time'], $serviceDuration);

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Booking created successfully!',
                    'booking' => $booking->load(['user', 'service', 'suburb', 'instructor.user'])
                ]);
            }

            return redirect()->route('admin.calendar.index')
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

/**
 * Check if instructor has enough consecutive availability slots for a booking
 */
    private function checkAvailabilityForBooking($instructorId, $date, $startTime, $serviceDurationMinutes)
        {
            $serviceDurationMinutes = (int) $serviceDurationMinutes;
            $bufferMinutes = 30; // 30 min rest after booking

            $startDateTime = Carbon::parse($date . ' ' . $startTime);
            $endDateTime = $startDateTime->copy()->addMinutes($serviceDurationMinutes + $bufferMinutes);

            // Check for existing bookings that would conflict (including buffer)
            $conflictingBookings = Booking::where('instructor_id', $instructorId)
                ->whereDate('date', $date)
                ->where('status', '!=', 'cancelled')
                ->where(function($query) use ($startDateTime, $endDateTime) {
                    $query->where(function($q) use ($startDateTime, $endDateTime) {
                        $q->whereRaw("DATE(date) = ? AND (
                            (TIME(CASE WHEN start_time LIKE '%-%' THEN start_time ELSE CONCAT(DATE(date), ' ', start_time) END) < ? AND 
                            TIME(CASE WHEN end_time LIKE '%-%' THEN end_time ELSE CONCAT(DATE(date), ' ', end_time) END) > ?) OR
                            (TIME(CASE WHEN start_time LIKE '%-%' THEN start_time ELSE CONCAT(DATE(date), ' ', start_time) END) < ? AND 
                            TIME(CASE WHEN end_time LIKE '%-%' THEN end_time ELSE CONCAT(DATE(date), ' ', end_time) END) > ?)
                        )", [
                            $startDateTime->format('Y-m-d'),
                            $endDateTime->format('H:i:s'),
                            $startDateTime->format('H:i:s'),
                            $startDateTime->format('H:i:s'),
                            $endDateTime->format('H:i:s')
                        ]);
                    });
                })
                ->exists();

            if ($conflictingBookings) {
                Log::info("Conflicting booking found for instructor {$instructorId} on {$date} from {$startTime}");
                return false;
            }

            // Get all availability slots for this instructor on this date that are available
            $availabilitySlots = Availability::where('instructor_id', $instructorId)
                ->whereDate('date', $date)
                ->where('is_available', true)
                ->orderBy('start_time')
                ->get();

            if ($availabilitySlots->isEmpty()) {
                Log::info("No availability slots found for instructor {$instructorId} on {$date}");
                return false;
            }

            // Check if we have continuous coverage for the entire booking duration + buffer
            $currentTime = $startDateTime->copy();
            $bookingEndTime = $endDateTime->copy();

            while ($currentTime->lt($bookingEndTime)) {
                $found = false;

                foreach ($availabilitySlots as $slot) {
                    $slotStart = Carbon::parse($date . ' ' . $slot->start_time);
                    $slotEnd = Carbon::parse($date . ' ' . $slot->end_time);

                    if ($slotStart->lte($currentTime) && $slotEnd->gt($currentTime)) {
                        $nextTime = $slotEnd->lt($bookingEndTime) ? $slotEnd : $bookingEndTime;
                        $currentTime = $nextTime;
                        $found = true;
                        break;
                    }
                }

                if (!$found) {
                    Log::info("Gap found in availability at {$currentTime->format('H:i:s')} for instructor {$instructorId} on {$date}");
                    return false;
                }
            }

            Log::info("Sufficient availability (with buffer) found for instructor {$instructorId} on {$date} from {$startTime} for {$serviceDurationMinutes} + buffer");
            return true;
        }


/**
 * Mark availability slots as booked (optional method)
 */
private function markSlotsAsBooked($instructorId, $date, $startTime, $serviceDurationMinutes)
    {
        $serviceDurationMinutes = (int) $serviceDurationMinutes;
        $bufferMinutes = 30; // 30 min rest after booking

        $startDateTime = Carbon::parse($date . ' ' . $startTime);
        $endDateTime = $startDateTime->copy()->addMinutes($serviceDurationMinutes + $bufferMinutes);

        // Update availability slots to mark them as not available (including buffer)
        $affectedSlots = Availability::where('instructor_id', $instructorId)
            ->whereDate('date', $date)
            ->where(function($query) use ($startDateTime, $endDateTime) {
                $query->where(function($q) use ($startDateTime, $endDateTime) {
                    $q->where('start_time', '>=', $startDateTime->format('H:i:s'))
                    ->where('start_time', '<', $endDateTime->format('H:i:s'));
                })->orWhere(function($q) use ($startDateTime, $endDateTime) {
                    $q->where('end_time', '>', $startDateTime->format('H:i:s'))
                    ->where('end_time', '<=', $endDateTime->format('H:i:s'));
                })->orWhere(function($q) use ($startDateTime, $endDateTime) {
                    $q->where('start_time', '<=', $startDateTime->format('H:i:s'))
                    ->where('end_time', '>=', $endDateTime->format('H:i:s'));
                });
            })
            ->update(['is_available' => false]);

        Log::info("Marked {$affectedSlots} availability slots as booked (with buffer) for instructor {$instructorId} on {$date} from {$startTime} for {$serviceDurationMinutes} + buffer");
    }

    /**
 * Debug method to check availability - you can call this to see what's happening
 */
public function debugAvailability(Request $request)
{
    $instructorId = $request->get('instructor_id');
    $date = $request->get('date');
    $startTime = $request->get('start_time');
    $serviceDuration = $request->get('duration', 60);
    
    // Check if instructor exists
    $instructor = Instructor::with('user')->find($instructorId);
    $instructorExists = $instructor ? true : false;
    
    // Get all availability slots for this instructor (not just the specific date)
    $allInstructorSlots = Availability::where('instructor_id', $instructorId)
        ->orderBy('date')
        ->orderBy('start_time')
        ->get();
    
    // Get slots for the specific date - FIX the date comparison
    $specificDateSlots = Availability::where('instructor_id', $instructorId)
        ->whereDate('date', $date) // Use whereDate instead of where
        ->orderBy('start_time')
        ->get();
    
    $result = [
        'instructor_id' => $instructorId,
        'instructor_exists' => $instructorExists,
        'instructor_name' => $instructor ? $instructor->user->name : 'Not found',
        'date' => $date,
        'requested_start_time' => $startTime,
        'service_duration' => $serviceDuration,
        'total_slots_for_instructor' => $allInstructorSlots->count(),
        'specific_date_slots_count' => $specificDateSlots->count(),
        'all_instructor_slots' => $allInstructorSlots->map(function($slot) {
            return [
                'id' => $slot->id,
                'date' => $slot->date->format('Y-m-d'), // Format date for easier reading
                'start_time' => $slot->start_time,
                'end_time' => $slot->end_time,
                'is_available' => $slot->is_available,
                'visibility' => $slot->visibility ?? 'public'
            ];
        }),
        'specific_date_slots' => $specificDateSlots->map(function($slot) {
            return [
                'id' => $slot->id,
                'start_time' => $slot->start_time,
                'end_time' => $slot->end_time,
                'is_available' => $slot->is_available,
                'visibility' => $slot->visibility ?? 'public'
            ];
        }),
        'available_slots_only' => $specificDateSlots->where('is_available', true)->map(function($slot) {
            return [
                'id' => $slot->id,
                'start_time' => $slot->start_time,
                'end_time' => $slot->end_time
            ];
        }),
        'has_sufficient_availability' => $specificDateSlots->isNotEmpty() ? $this->checkAvailabilityForBooking($instructorId, $date, $startTime, $serviceDuration) : false
    ];
    
    return response()->json($result);
}


    /**
     * Update booking status
     */
    public function updateBookingStatus(Request $request, $bookingId)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,completed,cancelled,no-show'
        ]);

        try {
            $booking = Booking::findOrFail($bookingId);
            $booking->status = $request->status;
            $booking->save();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Booking status updated successfully!'
                ]);
            }

            return back()->with('success', 'Booking status updated successfully!');
        } catch (\Exception $e) {
            Log::error('Error updating booking status: ' . $e->getMessage());
            
            if ($request->ajax()) {
                return response()->json([
                    'error' => 'An error occurred while updating the booking status.'
                ], 500);
            }
            
            return back()->with('error', 'An error occurred while updating the booking status.');
        }
    }
}