<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Suburb;
use App\Models\Instructor;
use App\Models\Service;
use App\Models\Availability;
use App\Models\Booking;
use App\Models\Payment;
use App\Notifications\BookingConfirmation;
use App\Notifications\BookingReminderOneDay;
use App\Notifications\BookingReminderTwoDays;
use App\Notifications\NewBookingReceived;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Notification;

class BookingController extends Controller
{
    /**
     * Display the booking start page
     */
    public function index()
    {
        $this->clearBookingSession();
        return view('booking.index');
    }

    /**
     * Clear all booking related session data
     */
    protected function clearBookingSession()
    {
        Session::forget([
            'booking.suburb_id',
            'booking.instructor_id',
            'booking.date',
            'booking.start_time',
            'booking.end_time',
            'booking.service_id',
            'booking.booking_for',
            'booking.other_name',
            'booking.other_email',
            'booking.other_phone',
            'booking.address',
            'booking.slot_locked_until',
            'booking.completed_id'
        ]);
    }

    /**
     * Display available suburbs
     */
    public function suburbs()
    {
        $suburbs = Suburb::where('active', true)
            ->orderBy('name')
            ->get();
        
        return view('booking.suburbs', compact('suburbs'));
    }

    /**
     * Handle suburb selection
     */
    public function selectSuburb(Request $request)
    {
        $validated = $request->validate([
            'suburb_id' => 'required|exists:suburbs,id',
        ]);
        
        Session::put('booking.suburb_id', $validated['suburb_id']);
        
        return redirect()->route('booking.instructors', $validated['suburb_id']);
    }

    /**
     * Display available instructors for selected suburb
     */
    public function instructors($suburb)
    {
        $suburb = Suburb::findOrFail($suburb);
        $instructors = Instructor::getActiveInSuburb($suburb->id);
        
        return view('booking.instructors', compact('instructors', 'suburb'));
    }

    /**
     * Handle instructor selection
     */
    public function selectInstructor(Request $request)
    {
        $validated = $request->validate([
            'instructor_id' => 'required|exists:instructors,id',
        ]);
        
        Session::put('booking.instructor_id', $validated['instructor_id']);
        
        return redirect()->route('booking.availability', $validated['instructor_id']);
    }

    /**
     * Display instructor availability
     */
    public function availability($instructor, Request $request)
{
    $instructor = Instructor::with('user')->findOrFail($instructor);

    // Handle week navigation - fix the date parsing
    $weekStart = $request->get('week_start');
    if ($weekStart) {
        try {
            $startDate = Carbon::createFromFormat('Y-m-d', $weekStart)->startOfDay();
        } catch (\Exception $e) {
            $startDate = Carbon::today()->startOfWeek();
        }
    } else {
        $startDate = Carbon::today()->startOfWeek();
    }
    
    if ($request->get('direction') === 'prev') {
        $startDate->subWeek();
    } elseif ($request->get('direction') === 'next') {
        $startDate->addWeek();
    }
    
    $endDate = $startDate->copy()->addDays(6);

    // Get availability slots - FIX: Use whereDate for proper date comparison
    $availabilitySlots = Availability::where('instructor_id', $instructor->id)
        ->whereDate('date', '>=', $startDate->format('Y-m-d'))
        ->whereDate('date', '<=', $endDate->format('Y-m-d'))
        ->where('is_available', true)
        ->orderBy('date')
        ->orderBy('start_time')
        ->get()
        ->groupBy(function($slot) {
            return Carbon::parse($slot->date)->format('Y-m-d');
        });

    // Get existing bookings - FIX: Use whereDate and handle time format properly
    $existingBookings = Booking::where('instructor_id', $instructor->id)
        ->whereDate('date', '>=', $startDate->format('Y-m-d'))
        ->whereDate('date', '<=', $endDate->format('Y-m-d'))
        ->whereIn('status', ['confirmed', 'pending'])
        ->get()
        ->groupBy(function($booking) {
            return Carbon::parse($booking->date)->format('Y-m-d');
        });

    // Get services
    $services = Service::where('active', true)->orderBy('name')->get();
    
    // Generate time slots
    $timeSlots = [];
    $startTime = Carbon::createFromTimeString('06:30');
    $endTime = Carbon::createFromTimeString('18:00');
    
    while ($startTime < $endTime) {
        $slotEndTime = $startTime->copy()->addMinutes(15);
        $timeSlots[] = [
            'start' => $startTime->format('H:i'),
            'label' => $startTime->format('H:i') . ' - ' . $slotEndTime->format('H:i'),
        ];
        $startTime->addMinutes(15);
    }

    return view('booking.availability', compact(
        'instructor',
        'availabilitySlots',
        'existingBookings',
        'startDate',
        'endDate',
        'services',
        'timeSlots'
    ));
}

    /**
     * Handle time slot selection
     */
    public function selectTime(Request $request)
{
    $validated = $request->validate([
        'date' => 'required|date',
        'start_time' => 'required|regex:/^\d{2}:\d{2}$/',
        'end_time' => 'required|regex:/^\d{2}:\d{2}$/',
        'service_id' => 'required|exists:services,id',
    ]);
    
    $instructorId = Session::get('booking.instructor_id');
    if (!$instructorId) {
        return redirect()->route('booking.index')->with('error', 'Please start the booking process from the beginning.');
    }
    
    // Store the service selection (from availability page)
    Session::put('booking.service_id', $validated['service_id']);
    
    // Parse times properly
    $date = $validated['date'];
    $startTime = Carbon::createFromFormat('H:i', $validated['start_time']);
    $endTime = Carbon::createFromFormat('H:i', $validated['end_time']);
    
    // Check availability in 15-minute increments
    $current = $startTime->copy();
    $allSlotsAvailable = true;
    $unavailableSlots = [];
    
    while ($current < $endTime) {
        $slotEnd = $current->copy()->addMinutes(15);
        
        // FIX: Use whereDate and whereTime for proper comparison
        $available = Availability::where('instructor_id', $instructorId)
            ->whereDate('date', $date)
            ->whereTime('start_time', $current->format('H:i:s'))
            ->where('is_available', true)
            ->exists();
            
        if (!$available) {
            $allSlotsAvailable = false;
            $unavailableSlots[] = $current->format('H:i');
        }
        $current = $slotEnd;
    }
    
    if (!$allSlotsAvailable) {
        $slotsText = implode(', ', $unavailableSlots);
        return back()->with('error', "The following time slots are not available: {$slotsText}. Please select another time.");
    }
    
    // Check for existing bookings that would conflict
    $hasConflict = Booking::where('instructor_id', $instructorId)
        ->whereDate('date', $date) // FIX: Use whereDate
        ->whereIn('status', ['confirmed', 'pending'])
        ->where(function($query) use ($validated) {
            $query->where(function($q) use ($validated) {
                $q->whereTime('start_time', '<', $validated['end_time'] . ':00')
                  ->whereTime('end_time', '>', $validated['start_time'] . ':00');
            });
        })
        ->exists();
        
    if ($hasConflict) {
        return back()->with('error', 'This time slot conflicts with an existing booking.');
    }
    
    // Check buffer time conflicts (30 minutes before/after)
    $bufferStartTime = $startTime->copy()->subMinutes(30);
    $bufferEndTime = $endTime->copy()->addMinutes(30);
    
    $hasBufferConflict = Booking::where('instructor_id', $instructorId)
        ->whereDate('date', $date) // FIX: Use whereDate
        ->whereIn('status', ['confirmed', 'pending'])
        ->where(function($query) use ($bufferStartTime, $bufferEndTime, $startTime, $endTime) {
            $query->where(function($q) use ($bufferStartTime, $startTime) {
                $q->whereTime('end_time', '>', $bufferStartTime->format('H:i:s'))
                  ->whereTime('end_time', '<=', $startTime->format('H:i:s'));
            })
            ->orWhere(function($q) use ($bufferEndTime, $endTime) {
                $q->whereTime('start_time', '>=', $endTime->format('H:i:s'))
                  ->whereTime('start_time', '<', $bufferEndTime->format('H:i:s'));
            });
        })
        ->exists();
        
    if ($hasBufferConflict) {
        return back()->with('error', 'This time slot conflicts with the 30-minute buffer required between lessons. Please select a different time.');
    }
    
    // Lock the slots and store session data
    Session::put([
        'booking.date' => $date,
        'booking.start_time' => $validated['start_time'],
        'booking.end_time' => $validated['end_time'],
        'booking.slot_locked_until' => now()->addMinutes(15)->toDateTimeString(),
        'booking.booking_for' => 'self'
    ]);
    
    // Go directly to details (skip services)
    return redirect()->route('booking.details');
}

    /**
     * Display available services
     */
    public function services()
    {
        // Check if we already have a service selected
        if (Session::has('booking.service_id')) {
            // Service already selected, proceed to details
            return redirect()->route('booking.details');
        }
        
        if (!$this->checkSlotLock()) {
            return redirect()->route('booking.availability', Session::get('booking.instructor_id'))
                ->with('error', 'Your selected time slot has expired. Please choose another time.');
        }

        $services = Service::where('active', true)->get();
        
        return view('booking.services', compact('services'));
    }

    /**
     * Handle service selection
     */
    public function selectService(Request $request)
    {
        $validated = $request->validate([
            'service_id' => 'required|exists:services,id',
            'booking_for' => 'required|in:self,other',
        ]);
        
        Session::put([
            'booking.service_id' => $validated['service_id'],
            'booking.booking_for' => $validated['booking_for']
        ]);
        
        return redirect()->route('booking.details');
    }

    /**
     * Display booking details form
     */
    public function details()
    {
        if (!Session::has('booking.service_id') || !$this->checkSlotLock()) {
            return redirect()->route('booking.index')
                ->with('error', 'Your session has expired. Please start the booking process again.');
        }
        
        $suburbs = Suburb::where('active', true)->orderBy('name')->get();
        $bookingFor = Session::get('booking.booking_for', 'self');
        
        return view('booking.details', compact('suburbs', 'bookingFor'));
    }

    /**
     * Handle booking details submission
     */
    public function saveDetails(Request $request)
    {
        if (!$this->checkSlotLock()) {
            return redirect()->route('booking.availability', Session::get('booking.instructor_id'))
                ->with('error', 'Your selected time slot has expired. Please choose another time.');
        }

        $bookingFor = Session::get('booking.booking_for', 'self');
        
        $rules = ['address' => 'required|string|max:255'];
        
        if ($bookingFor === 'other') {
            $rules = array_merge($rules, [
                'other_name' => 'required|string|max:255',
                'other_email' => 'required|email|max:255',
                'other_phone' => 'required|string|max:20',
            ]);
        }
        
        $validated = $request->validate($rules);
        
        foreach ($validated as $key => $value) {
            Session::put('booking.' . $key, $value);
        }
        
        if (!Auth::check()) {
            Session::put('url.intended', route('booking.payment'));
            return redirect()->route('login')
                ->with('info', 'Please log in or register to complete your booking.');
        }
        
        return redirect()->route('booking.payment');
    }

    /**
     * Display payment page
     */
    public function payment()
    {
        if (!Session::has('booking.service_id') || !Auth::check() || !$this->checkSlotLock()) {
            return redirect()->route('booking.index')
                ->with('error', 'Your session has expired. Please start the booking process again.');
        }
        
        $bookingData = $this->getBookingData();
        return view('booking.payment', compact('bookingData'));
    }

    /**
     * Process payment and create booking
     */
    public function processPayment(Request $request)
    {
        $validated = $request->validate([
            'payment_method' => 'required|in:credit_card,paypal',
            'stripe_token' => 'required_if:payment_method,credit_card',
        ]);

        try {
            DB::beginTransaction();

            if (!$this->checkSlotLock()) {
                throw new \Exception('Booking time slot has expired. Please start over.');
            }

            $booking = $this->createBooking();
            $payment = $this->processPaymentTransaction($booking, $validated);
            $this->updateAvailability($booking);
            $this->sendBookingNotifications($booking);

            DB::commit();
            $this->clearBookingSession();
            Session::put('booking.completed_id', $booking->id);

            return redirect()->route('booking.confirmation', $booking->id);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Booking failed: ' . $e->getMessage());
            return back()->with('error', 'Booking failed: ' . $e->getMessage());
        }
    }

    /**
     * Display booking confirmation
     */
    public function confirmation($bookingId = null)
    {
        // Try to get booking ID from parameter or session
        if (!$bookingId) {
            $bookingId = Session::get('booking.completed_id');
        }
        
        if (!$bookingId) {
            return redirect()->route('booking.index')
                ->with('error', 'No booking found.');
        }
        
        $booking = Booking::with(['user', 'instructor.user', 'service', 'suburb', 'payment'])
            ->findOrFail($bookingId);
        
        // Verify the booking belongs to the current user
        if ($booking->user_id !== Auth::id()) {
            return redirect()->route('booking.index')
                ->with('error', 'Unauthorized access.');
        }
        
        Session::forget('booking.completed_id');
        
        return view('booking.confirmation', compact('booking'));
    }

    /**
     * Helper Methods
     */
    protected function checkSlotLock()
    {
        if (!Session::has('booking.slot_locked_until')) {
            return false;
        }
        
        try {
            $lockUntil = Carbon::parse(Session::get('booking.slot_locked_until'));
            return now()->lt($lockUntil);
        } catch (\Exception $e) {
            return false;
        }
    }

    protected function getBookingData()
    {
        return [
            'service' => Service::findOrFail(Session::get('booking.service_id')),
            'instructor' => Instructor::with('user')->findOrFail(Session::get('booking.instructor_id')),
            'suburb' => Suburb::findOrFail(Session::get('booking.suburb_id')),
            'date' => Session::get('booking.date'),
            'start_time' => Session::get('booking.start_time'),
            'end_time' => Session::get('booking.end_time'),
            'booking_for' => Session::get('booking.booking_for'),
            'other_name' => Session::get('booking.other_name'),
            'other_email' => Session::get('booking.other_email'),
            'other_phone' => Session::get('booking.other_phone'),
            'address' => Session::get('booking.address'),
        ];
    }

    protected function createBooking()
{
    $booking = new Booking();
    $booking->fill([
        'user_id' => Auth::id(),
        'instructor_id' => Session::get('booking.instructor_id'),
        'service_id' => Session::get('booking.service_id'),
        'suburb_id' => Session::get('booking.suburb_id'),
        'date' => Session::get('booking.date'),
        // Always store as H:i:s, regardless of input format
        'start_time' => Carbon::parse(Session::get('booking.start_time'))->format('H:i:s'),
        'end_time' => Carbon::parse(Session::get('booking.end_time'))->format('H:i:s'),
        'status' => 'pending',
        'booking_for' => Session::get('booking.booking_for', 'self'),
        'other_name' => Session::get('booking.other_name'),
        'other_email' => Session::get('booking.other_email'),
        'other_phone' => Session::get('booking.other_phone'),
        'address' => Session::get('booking.address'),
    ]);
    $booking->save();

    return $booking;
}

    protected function processPaymentTransaction($booking, $validated)
{
    $service = Service::find($booking->service_id);
    
    // Generate invoice first
    $invoiceService = new \App\Services\InvoiceService();
    $invoice = $invoiceService->generateInvoiceForBooking($booking);
    
    // Create payment
    $payment = new Payment();
    $payment->booking_id = $booking->id;
    $payment->user_id = Auth::id();
    $payment->invoice_id = $invoice->id;
    $payment->amount = $service->price;
    $payment->payment_method = $validated['payment_method'];
    $payment->transaction_id = 'TRANS-' . time() . '-' . $booking->id;
    $payment->status = 'completed';
    $payment->payment_date = now();
    $payment->save();
    
    // Mark invoice as paid
    $invoiceService->markInvoiceAsPaid($invoice, $payment);
    
    return $payment;
}

    protected function updateAvailability($booking)
{
    // Always parse as H:i:s
    $startTime = Carbon::parse($booking->start_time);
    $endTime = Carbon::parse($booking->end_time);
    $current = $startTime->copy();

    while ($current < $endTime) {
        $slotEnd = $current->copy()->addMinutes(15);

        Availability::where('instructor_id', $booking->instructor_id)
            ->where('date', $booking->date)
            ->whereTime('start_time', $current->format('H:i:s'))
            ->update(['is_available' => false]);

        $current = $slotEnd;
    }
}

    /**
     * Send booking notifications to relevant parties
     */
    protected function sendBookingNotifications($booking)
{
    // Ensure booking is eager loaded with relationships
    $booking = Booking::with(['user', 'instructor.user', 'service', 'suburb', 'payment'])
        ->findOrFail($booking->id);
        
    // Send booking confirmation to student
    try {
        if ($booking->user) {
            // Send both database and email notification
            $booking->user->notify(new BookingConfirmation($booking));
            
            // Mark confirmation as sent in database
            $booking->confirmation_sent = true;
            $booking->save();
            
            Log::info('Booking confirmation notification (database + email) sent to user #' . $booking->user_id . ' for booking #' . $booking->id);
        }
    } catch (\Exception $e) {
        Log::error('Failed to send booking confirmation: ' . $e->getMessage());
    }
    
    // Send notification to instructor
    try {
        if ($booking->instructor && $booking->instructor->user) {
            // Send both database and email notification
            $booking->instructor->user->notify(new NewBookingReceived($booking));
            Log::info('New booking notification (database + email) sent to instructor #' . $booking->instructor_id . ' for booking #' . $booking->id);
        }
    } catch (\Exception $e) {
        Log::error('Failed to send instructor notification: ' . $e->getMessage());
    }
    
    // Send notification to admin (optional)
    try {
        $adminUsers = \App\Models\User::where('role', 'admin')->get();
        foreach ($adminUsers as $admin) {
            $admin->notify(new \App\Notifications\NewBookingAdmin($booking));
        }
        Log::info('New booking notification sent to admins for booking #' . $booking->id);
    } catch (\Exception $e) {
        Log::error('Failed to send admin notification: ' . $e->getMessage());
    }
}
}