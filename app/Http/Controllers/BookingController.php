<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Suburb;
use App\Models\Instructor;
use App\Models\Service;
use App\Models\Availability;
use App\Models\Booking;
use App\Models\Payment;
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
        
        // Handle week navigation
        $startDate = Carbon::parse($request->get('week_start', Carbon::today()));
        if ($request->get('direction') === 'prev') {
            $startDate->subWeek();
        } elseif ($request->get('direction') === 'next') {
            $startDate->addWeek();
        }
        
        $endDate = $startDate->copy()->addDays(6);
        
        // Get availability slots
        $availabilitySlots = Availability::where('instructor_id', $instructor->id)
            ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->where('is_available', true)
            ->orderBy('date')
            ->orderBy('start_time')
            ->get()
            ->groupBy(function($slot) {
                return Carbon::parse($slot->date)->format('Y-m-d');
            });

        // Get existing bookings
        $existingBookings = Booking::where('instructor_id', $instructor->id)
            ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->get()
            ->groupBy(function($booking) {
                return Carbon::parse($booking->date)->format('Y-m-d');
            });

        return view('booking.availability', compact(
            'instructor', 
            'availabilitySlots', 
            'existingBookings', 
            'startDate', 
            'endDate'
        ));
    }

    /**
     * Handle time slot selection
     */
    public function selectTime(Request $request)
    {
        $validated = $request->validate([
            'availability_id' => 'required|exists:availabilities,id', // <-- changed here
        ]);
        
        $availability = \App\Models\Availability::findOrFail($validated['availability_id']);
        
        if (!$this->isSlotAvailable($availability)) {
            return back()->with('error', 'This time slot is no longer available. Please select another time.');
        }
        
        $this->lockSlot($availability);
        
        Session::put([
            'booking.date' => $availability->date,
            'booking.start_time' => $availability->start_time,
            'booking.end_time' => $availability->end_time,
            'booking.slot_locked_until' => now()->addMinutes(15)
        ]);
        
        return redirect()->route('booking.services');
    }

    /**
     * Display available services
     */
    public function services()
    {
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
            return redirect()->route('booking.index');
        }
        
        $suburbs = Suburb::where('active', true)->orderBy('name')->get();
        return view('booking.details', compact('suburbs'));
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
            return redirect()->route('booking.index');
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

            return redirect()->route('booking.confirmation');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Booking failed: ' . $e->getMessage());
            return back()->with('error', 'Booking failed: ' . $e->getMessage());
        }
    }

    /**
     * Display booking confirmation
     */
    public function confirmation()
    {
        $bookingId = Session::get('booking.completed_id');
        
        if (!$bookingId) {
            return redirect()->route('booking.index');
        }
        
        $booking = Booking::with(['user', 'instructor.user', 'service', 'suburb', 'payment'])
            ->findOrFail($bookingId);
        
        Session::forget('booking.completed_id');
        
        return view('booking.confirmation', compact('booking'));
    }

    /**
     * Helper Methods
     */
    protected function isSlotAvailable($availability)
    {
        if (!$availability->is_available) {
            return false;
        }

        return !Booking::where('instructor_id', $availability->instructor_id)
            ->where('date', $availability->date)
            ->where(function($query) use ($availability) {
                $query->whereBetween('start_time', [$availability->start_time, $availability->end_time])
                    ->orWhereBetween('end_time', [$availability->start_time, $availability->end_time]);
            })
            ->exists();
    }

    protected function lockSlot($availability)
    {
        Cache::put(
            "slot_lock:{$availability->id}", 
            Auth::id(), 
            now()->addMinutes(15)
        );
    }

    protected function checkSlotLock()
    {
        return Session::has('booking.slot_locked_until') && 
               now()->lt(Carbon::parse(Session::get('booking.slot_locked_until')));
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
            'start_time' => Session::get('booking.start_time'),
            'end_time' => Session::get('booking.end_time'),
            'status' => 'pending',
            'booking_for' => Session::get('booking.booking_for'),
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
        // Add your payment gateway integration here
        $payment = new Payment();
        $payment->booking_id = $booking->id;
        $payment->user_id = Auth::id();
        $payment->amount = Service::find(Session::get('booking.service_id'))->price;
        $payment->payment_method = $validated['payment_method'];
        $payment->transaction_id = 'TRANS-' . time();
        $payment->status = 'completed';
        $payment->save();
        
        return $payment;
    }

    protected function updateAvailability($booking)
    {
        Availability::where('instructor_id', $booking->instructor_id)
            ->where('date', $booking->date)
            ->where('start_time', $booking->start_time)
            ->update(['is_available' => false]);
    }

    protected function sendBookingNotifications($booking)
    {
        // Add your notification logic here
        // Example:
        // Notification::send($booking->instructor->user, new NewBookingNotification($booking));
        // Notification::send($booking->user, new BookingConfirmationNotification($booking));
    }
}
