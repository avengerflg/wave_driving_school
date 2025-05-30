<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Instructor;
use App\Models\Suburb;
use App\Models\Availability;
use App\Models\Booking;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class InstructorController extends Controller
{
    /**
     * Display a listing of instructors.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');
        
        $instructors = User::where('role', 'instructor')
            ->with('instructor')
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->when($status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('admin.instructors.index', compact('instructors', 'search', 'status'));
    }

    /**
     * Show the form for creating a new instructor.
     */
    public function create()
    {
        $suburbs = Suburb::orderBy('name')->get();
        return view('admin.instructors.create', compact('suburbs'));
    }

    /**
     * Store a newly created instructor.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
                        'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'suburb_id' => 'nullable|exists:suburbs,id',
            'password' => 'required|string|min:8|confirmed',
            'license_number' => 'required|string|max:50',
            'bio' => 'nullable|string',
            'profile_image' => 'nullable|image|max:2048',
            'service_suburbs' => 'required|array|min:1',
            'service_suburbs.*' => 'exists:suburbs,id',
            'status' => 'required|in:active,inactive',
        ]);

        DB::beginTransaction();
        
        try {
            // Create user
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'address' => $validated['address'] ?? null,
                'suburb_id' => $validated['suburb_id'] ?? null,
                'password' => Hash::make($validated['password']),
                'role' => 'instructor',
                'status' => $validated['status'],
            ]);

            // Handle profile image upload
            $profileImage = null;
            if ($request->hasFile('profile_image')) {
                $profileImage = $request->file('profile_image')->store('instructors', 'public');
            }

            // Create instructor profile
            $instructor = Instructor::create([
                'user_id' => $user->id,
                'license_number' => $validated['license_number'],
                'bio' => $validated['bio'] ?? null,
                'profile_image' => $profileImage,
                'active' => $validated['status'] == 'active',
                'suburbs' => $validated['service_suburbs'],
            ]);

            DB::commit();

            return redirect()->route('admin.instructors.index')
                ->with('success', 'Instructor created successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating instructor: ' . $e->getMessage() . ' | ' . $e->getTraceAsString());
            return back()->withInput()->with('error', 'Error creating instructor: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified instructor.
     */
    public function show(User $instructor)
    {
        if ($instructor->role !== 'instructor') {
            abort(404);
        }
        
        $instructor->load('instructor');

        // Get service suburbs
        $serviceSuburbs = [];
        if (!empty($instructor->instructor->suburbs)) {
            $suburIds = is_array($instructor->instructor->suburbs) 
                ? $instructor->instructor->suburbs 
                : json_decode($instructor->instructor->suburbs);
            $serviceSuburbs = Suburb::whereIn('id', $suburIds ?: [])->get();
        }

        // Get instructor's recent bookings
        $bookings = Booking::where('instructor_id', $instructor->instructor->id)
            ->with('user', 'service')
            ->latest()
            ->take(5)
            ->get();
        
        // Calculate statistics
        $totalBookings = Booking::where('instructor_id', $instructor->instructor->id)->count();
        $completedBookings = Booking::where('instructor_id', $instructor->instructor->id)
            ->where('status', 'completed')
            ->count();
        $pendingBookings = Booking::where('instructor_id', $instructor->instructor->id)
            ->where('status', 'pending')
            ->count();

        // Calculate total revenue from completed bookings
        $totalRevenue = Booking::where('instructor_id', $instructor->instructor->id)
            ->where('status', 'completed')
            ->join('services', 'bookings.service_id', '=', 'services.id')
            ->sum('services.price');
            
        return view('admin.instructors.show', compact(
            'instructor', 
            'serviceSuburbs',
            'bookings',
            'totalBookings',
            'completedBookings',
            'pendingBookings',
            'totalRevenue'
        ));
    }

    /**
     * Show the form for editing the specified instructor.
     */
    public function edit(User $instructor)
    {
        if ($instructor->role !== 'instructor') {
            abort(404);
        }
        
        $instructor->load('instructor');
        $suburbs = Suburb::orderBy('name')->get();
        
        $serviceSuburbIds = [];
        if (!empty($instructor->instructor->suburbs)) {
            $serviceSuburbIds = is_array($instructor->instructor->suburbs)
                ? $instructor->instructor->suburbs
                : json_decode($instructor->instructor->suburbs);
        }
        
        return view('admin.instructors.edit', compact('instructor', 'suburbs', 'serviceSuburbIds'));
    }

    /**
     * Update the specified instructor.
     */
    public function update(Request $request, User $instructor)
    {
        if ($instructor->role !== 'instructor') {
            abort(404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($instructor->id)
            ],
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'suburb_id' => 'nullable|exists:suburbs,id',
            'password' => 'nullable|string|min:8|confirmed',
            'license_number' => 'required|string|max:50',
            'bio' => 'nullable|string',
            'profile_image' => 'nullable|image|max:2048',
            'service_suburbs' => 'required|array|min:1',
            'service_suburbs.*' => 'exists:suburbs,id',
            'status' => 'required|in:active,inactive',
        ]);

        DB::beginTransaction();
        
        try {
            // Update user
            $userData = [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'address' => $validated['address'] ?? null,
                'suburb_id' => $validated['suburb_id'] ?? null,
                'status' => $validated['status'],
            ];
            
            if (!empty($validated['password'])) {
                $userData['password'] = Hash::make($validated['password']);
            }
            
            $instructor->update($userData);

            // Handle profile image upload
            if ($request->hasFile('profile_image')) {
                if ($instructor->instructor->profile_image) {
                    Storage::disk('public')->delete($instructor->instructor->profile_image);
                }
                
                $profileImage = $request->file('profile_image')->store('instructors', 'public');
                $instructor->instructor->profile_image = $profileImage;
                $instructor->instructor->save();
            }

            // Update instructor
            $instructor->instructor->update([
                'license_number' => $validated['license_number'],
                'bio' => $validated['bio'] ?? null,
                'active' => $validated['status'] == 'active' ? 1 : 0,
                'suburbs' => $validated['service_suburbs']
            ]);

            // Update suburb relationships
            DB::table('instructor_suburb')->where('instructor_id', $instructor->instructor->id)->delete();
            $insertData = [];
            foreach ($validated['service_suburbs'] as $suburbId) {
                $insertData[] = [
                    'instructor_id' => $instructor->instructor->id,
                    'suburb_id' => $suburbId,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }
            DB::table('instructor_suburb')->insert($insertData);

            DB::commit();

            return redirect()->route('admin.instructors.show', $instructor)
                ->with('success', 'Instructor updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating instructor: ' . $e->getMessage() . ' | ' . $e->getTraceAsString());
            return back()->withInput()->with('error', 'Error updating instructor: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified instructor.
     */
    public function destroy(User $instructor)
    {
        if ($instructor->role !== 'instructor') {
            abort(404);
        }

        $bookingsCount = Booking::where('instructor_id', $instructor->instructor->id)->count();
        if ($bookingsCount > 0) {
            return back()->with('error', 'Cannot delete instructor with existing bookings.');
        }

        DB::beginTransaction();
        
        try {
            DB::table('instructor_suburb')->where('instructor_id', $instructor->instructor->id)->delete();
            Availability::where('instructor_id', $instructor->instructor->id)->delete();
            $instructor->instructor->delete();
            $instructor->delete();
            
            DB::commit();
            
            return redirect()->route('admin.instructors.index')
                ->with('success', 'Instructor deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error deleting instructor: ' . $e->getMessage());
        }
    }
    
    /**
     * Update instructor status.
     */
    public function updateStatus(Request $request, User $instructor)
    {
        if ($instructor->role !== 'instructor') {
            abort(404);
        }
        
        $validated = $request->validate([
            'status' => 'required|in:active,inactive',
        ]);
        
        $instructor->update(['status' => $validated['status']]);
        
        $instructor->instructor->update([
            'active' => $validated['status'] == 'active' ? 1 : 0
        ]);
        
        return back()->with('success', 'Instructor status updated successfully');
    }
    
    /**
     * Show the availability management page for an instructor.
     */
    public function availability(User $instructor, Request $request)
    {
        if ($instructor->role !== 'instructor') {
            abort(404);
        }

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
        
        $startDate = $viewMonth->copy()->startOfMonth()->startOfWeek();
        $endDate = $viewMonth->copy()->endOfMonth()->endOfWeek();
        
        $prevMonth = $viewMonth->copy()->subMonth()->format('Y-m');
        $nextMonth = $viewMonth->copy()->addMonth()->format('Y-m');
        
        $instructor->load('instructor');
        $today = Carbon::today();

        $availabilities = Availability::where('instructor_id', $instructor->instructor->id)
            ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->orderBy('date')
            ->orderBy('start_time')
            ->get();
        
        $groupedAvailabilities = $availabilities->groupBy(function($availability) {
            return $availability->date->format('Y-m-d');
        });

        return view('admin.instructors.availability', compact(
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
     * Store a new availability for an instructor.
     */
    public function storeAvailability(Request $request, User $instructor)
    {
        if ($instructor->role !== 'instructor') {
            abort(404);
        }

        $validated = $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'is_recurring' => 'sometimes|boolean',
            'recur_until' => 'required_if:is_recurring,1|nullable|date|after:date',
            'days_of_week' => 'required_if:is_recurring,1|array',
            'days_of_week.*' => 'integer|min:0|max:6',
        ]);

        DB::beginTransaction();
        
        try {
            if (isset($validated['is_recurring']) && $validated['is_recurring']) {
                $startDate = Carbon::parse($validated['date']);
                $endDate = Carbon::parse($validated['recur_until']);
                $daysOfWeek = $validated['days_of_week'];
                
                for ($date = $startDate; $date->lte($endDate); $date->addDay()) {
                    if (in_array($date->dayOfWeek, $daysOfWeek)) {
                        Availability::create([
                            'instructor_id' => $instructor->instructor->id,
                            'date' => $date->format('Y-m-d'),
                            'start_time' => $validated['start_time'],
                            'end_time' => $validated['end_time'],
                        ]);
                    }
                }
            } else {
                Availability::create([
                    'instructor_id' => $instructor->instructor->id,
                    'date' => $validated['date'],
                    'start_time' => $validated['start_time'],
                    'end_time' => $validated['end_time'],
                ]);
            }
            
            DB::commit();
            
            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Availability added successfully.']);
            }
            
            return redirect()->back()->with('success', 'Availability added successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error adding availability: ' . $e->getMessage());
            
            if ($request->ajax()) {
                return response()->json(['error' => 'Error adding availability: ' . $e->getMessage()], 500);
            }
            
            return back()->with('error', 'Error adding availability: ' . $e->getMessage());
        }
    }

    /**
     * Delete an availability.
     */
    public function destroyAvailability(User $instructor, $availabilityId)
    {
        if ($instructor->role !== 'instructor') {
            abort(404);
        }

        $availability = Availability::where('instructor_id', $instructor->instructor->id)
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
     * Bulk delete availabilities.
     */
    public function bulkDeleteAvailability(Request $request, User $instructor)
    {
        if ($instructor->role !== 'instructor') {
            abort(404);
        }

        $validated = $request->validate([
            'availability_ids' => 'required|array',
            'availability_ids.*' => 'exists:availabilities,id',
        ]);

        try {
            Availability::whereIn('id', $validated['availability_ids'])
                ->where('instructor_id', $instructor->instructor->id)
                ->delete();
            
            return redirect()->back()->with('success', 'Availabilities removed successfully.');
        } catch (\Exception $e) {
            Log::error('Error removing availabilities: ' . $e->getMessage());
            return back()->with('error', 'Error removing availabilities: ' . $e->getMessage());
        }
    }

    /**
     * Generate availabilities for an instructor.
     */
    public function generateAvailability(Request $request, User $instructor)
    {
        if ($instructor->role !== 'instructor') {
            abort(404);
        }

        $validated = $request->validate([
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'days_of_week' => 'required|array',
            'days_of_week.*' => 'integer|min:0|max:6',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        DB::beginTransaction();
        
        try {
            $startDate = Carbon::parse($validated['start_date']);
            $endDate = Carbon::parse($validated['end_date']);
            $daysOfWeek = $validated['days_of_week'];
            
            for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
                if (in_array($date->dayOfWeek, $daysOfWeek)) {
                    Availability::create([
                        'instructor_id' => $instructor->instructor->id,
                        'date' => $date->format('Y-m-d'),
                        'start_time' => $validated['start_time'],
                        'end_time' => $validated['end_time'],
                    ]);
                }
            }
            
            DB::commit();
            
            return redirect()->back()->with('success', 'Availabilities generated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error generating availabilities: ' . $e->getMessage());
            return back()->with('error', 'Error generating availabilities: ' . $e->getMessage());
        }
    }

    /**
     * Copy availabilities from one week to another.
     */
    public function copyAvailability(Request $request, User $instructor)
    {
        if ($instructor->role !== 'instructor') {
            abort(404);
        }

        $validated = $request->validate([
            'source_week' => 'required|date',
            'target_week' => 'required|date|after:source_week',
        ]);

        DB::beginTransaction();
        
        try {
            $sourceStart = Carbon::parse($validated['source_week'])->startOfWeek();
            $sourceEnd = $sourceStart->copy()->endOfWeek();
            
            $targetStart = Carbon::parse($validated['target_week'])->startOfWeek();
            $dayDifference = $targetStart->diffInDays($sourceStart);
            
            $availabilities = Availability::where('instructor_id', $instructor->instructor->id)
                ->whereBetween('date', [$sourceStart->format('Y-m-d'), $sourceEnd->format('Y-m-d')])
                ->get();
            
            foreach ($availabilities as $availability) {
                $sourceDate = Carbon::parse($availability->date);
                $targetDate = $sourceDate->copy()->addDays($dayDifference);
                
                Availability::create([
                    'instructor_id' => $instructor->instructor->id,
                    'date' => $targetDate->format('Y-m-d'),
                    'start_time' => $availability->start_time,
                    'end_time' => $availability->end_time,
                ]);
            }
            
            DB::commit();
            
            return redirect()->back()->with('success', 'Availabilities copied successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error copying availabilities: ' . $e->getMessage());
            return back()->with('error', 'Error copying availabilities: ' . $e->getMessage());
        }
    }

    /**
     * Show instructor's schedule.
     */
    public function schedule(User $instructor)
    {
        if ($instructor->role !== 'instructor') {
            abort(404);
        }
        
        $instructor->load('instructor');
        
        $startDate = Carbon::now()->startOfWeek();
        $endDate = Carbon::now()->endOfWeek()->addWeek();
        
        $availabilities = Availability::where('instructor_id', $instructor->instructor->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date')
            ->orderBy('start_time')
            ->get();
            
        $bookings = Booking::where('instructor_id', $instructor->instructor->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->with('user', 'service')
            ->orderBy('date')
            ->orderBy('start_time')
            ->get();
        
        return view('admin.instructors.schedule', compact('instructor', 'availabilities', 'bookings', 'startDate', 'endDate'));
    }
    
    /**
     * Show instructor's bookings.
     */
    public function bookings(User $instructor)
    {
        if ($instructor->role !== 'instructor') {
            abort(404);
        }
        
        $instructor->load('instructor');
        
        $bookings = Booking::where('instructor_id', $instructor->instructor->id)
            ->with(['user', 'service'])
            ->orderBy('date', 'desc')
            ->orderBy('start_time', 'desc')
            ->paginate(15);
        
        return view('admin.instructors.bookings', compact('instructor', 'bookings'));
    }

    /**
     * Show the instructor's calendar.
     */
    public function calendar(User $instructor)
    {
        if ($instructor->role !== 'instructor') {
            abort(404);
        }

        $instructor->load('instructor');
        $today = Carbon::today();

        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->addMonths(2)->endOfMonth();
        
        $availabilities = Availability::where('instructor_id', $instructor->instructor->id)
            ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->orderBy('date')
            ->orderBy('start_time')
            ->get();

        $bookings = Booking::where('instructor_id', $instructor->instructor->id)
            ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->with(['user', 'service'])
            ->orderBy('date')
            ->orderBy('start_time')
            ->get();

        return view('admin.instructors.calendar', compact('instructor', 'availabilities', 'bookings'));
    }

    /**
     * Store a new booking for an instructor (Admin creating booking)
     */
    public function storeBooking(Request $request, User $instructor)
    {
        if ($instructor->role !== 'instructor') {
            abort(404);
        }

        $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'start_time' => 'required',
            'service_id' => 'required|exists:services,id',
            'suburb_id' => 'required|exists:suburbs,id',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_type' => 'required|in:new,returning',
            'pickup_location' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $service = Service::findOrFail($request->service_id);
            
            $startDateTime = Carbon::parse($request->date . ' ' . $request->start_time);
            $endDateTime = $startDateTime->copy()->addMinutes($service->duration);

            // Check availability
            $hasAvailability = Availability::where('instructor_id', $instructor->instructor->id)
                ->where('date', $request->date)
                ->where('start_time', '<=', $request->start_time)
                ->where('end_time', '>=', $endDateTime->format('H:i:s'))
                ->exists();

            if (!$hasAvailability) {
                return response()->json([
                    'error' => 'No availability found for the selected time slot.'
                ], 422);
            }

            // Check for conflicts
            $hasConflict = Booking::where('instructor_id', $instructor->instructor->id)
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
                $customer = User::create([
                    'name' => $request->customer_name,
                    'email' => $request->customer_email,
                    'phone' => $request->customer_phone,
                    'role' => 'student',
                    'email_verified_at' => now(),
                    'password' => Hash::make('temporary_password_' . rand(100000, 999999)),
                ]);

                $customer->student()->create([
                    'date_of_birth' => null,
                    'license_number' => null,
                    'emergency_contact_name' => null,
                    'emergency_contact_phone' => null,
                ]);
            }

            // Create the booking
            $booking = Booking::create([
                'user_id' => $customer->id,
                'instructor_id' => $instructor->instructor->id,
                'service_id' => $service->id,
                'suburb_id' => $request->suburb_id,
                'date' => $request->date,
                'start_time' => $request->start_time,
                'end_time' => $endDateTime->format('H:i:s'),
                'pickup_location' => $request->pickup_location,
                'notes' => $request->notes,
                'status' => 'confirmed',
                'total_amount' => $service->price,
                'booking_type' => 'admin_created',
            ]);

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Booking created successfully!',
                    'booking' => $booking->load(['user', 'service', 'suburb'])
                ]);
            }

            return redirect()->route('admin.instructors.calendar', $instructor)
                ->with('success', 'Booking created successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            
            if ($request->ajax()) {
                return response()->json([
                    'error' => 'An error occurred while creating the booking: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'An error occurred while creating the booking.');
        }
    }

    /**
     * Get calendar data for instructor (AJAX endpoint)
     */
    public function getCalendarData(Request $request, User $instructor)
    {
        if ($instructor->role !== 'instructor') {
            abort(404);
        }

        $startDate = $request->input('start', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end', Carbon::now()->endOfMonth()->format('Y-m-d'));

        $availabilities = Availability::where('instructor_id', $instructor->instructor->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date')
            ->orderBy('start_time')
            ->get();

        $bookings = Booking::where('instructor_id', $instructor->instructor->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->with(['user', 'service', 'suburb'])
            ->orderBy('date')
            ->orderBy('start_time')
            ->get();

        return response()->json([
            'availabilities' => $availabilities,
            'bookings' => $bookings
        ]);
    }

    /**
     * Get booking details for modal display
     */
    public function getBookingDetails(User $instructor, Booking $booking)
    {
        if ($instructor->role !== 'instructor' || $booking->instructor_id !== $instructor->instructor->id) {
            abort(404);
        }

        $booking->load(['user', 'service', 'suburb']);

        return response()->json([
            'id' => $booking->id,
            'date' => $booking->date,
            'start_time' => $booking->start_time,
            'end_time' => $booking->end_time,
            'status' => $booking->status,
            'notes' => $booking->notes,
            'pickup_location' => $booking->pickup_location,
            'total_amount' => $booking->total_amount,
            'user' => [
                'id' => $booking->user->id,
                'name' => $booking->user->name,
                'email' => $booking->user->email,
                'phone' => $booking->user->phone,
            ],
            'service' => [
                'id' => $booking->service->id,
                'name' => $booking->service->name,
                'duration' => $booking->service->duration,
                'price' => $booking->service->price,
            ],
            'suburb' => $booking->suburb ? [
                'id' => $booking->suburb->id,
                'name' => $booking->suburb->name,
            ] : null,
        ]);
    }

    /**
     * Get availabilities for an instructor.
     */
    public function getAvailabilities(Request $request, $instructorId)
    {
        $date = $request->input('date', Carbon::today()->format('Y-m-d'));
        
        $availabilities = Availability::where('instructor_id', $instructorId)
            ->where('date', $date)
            ->orderBy('start_time')
            ->get()
                        ->map(function($availability) {
                return [
                    'id' => $availability->id,
                    'date' => $availability->date,
                    'start_time' => $availability->start_time,
                    'end_time' => $availability->end_time,
                    'formatted_start' => Carbon::parse($availability->start_time)->format('g:i A'),
                    'formatted_end' => Carbon::parse($availability->end_time)->format('g:i A'),
                ];
            });
            
        $bookings = Booking::where('instructor_id', $instructorId)
            ->where('date', $date)
            ->where('status', '!=', 'cancelled')
            ->with(['user', 'service'])
            ->orderBy('start_time')
            ->get()
            ->map(function($booking) {
                return [
                    'id' => $booking->id,
                    'date' => $booking->date,
                    'start_time' => $booking->start_time,
                    'end_time' => $booking->end_time,
                    'formatted_start' => Carbon::parse($booking->start_time)->format('g:i A'),
                    'formatted_end' => Carbon::parse($booking->end_time)->format('g:i A'),
                    'status' => $booking->status,
                    'user' => [
                        'id' => $booking->user->id,
                        'name' => $booking->user->name
                    ],
                    'service' => [
                        'id' => $booking->service->id,
                        'name' => $booking->service->name
                    ]
                ];
            });
            
        return response()->json([
            'availabilities' => $availabilities,
            'bookings' => $bookings
        ]);
    }
}


