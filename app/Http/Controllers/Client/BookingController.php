<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    public function index()
    {
        $bookings = Booking::with(['instructor.user', 'service', 'suburb', 'payment'])
            ->where('user_id', Auth::id())
            ->orderBy('date', 'desc')
            ->orderBy('start_time', 'desc')
            ->paginate(10);
        
        return view('client.bookings.index', compact('bookings'));
    }
    
    public function show(Booking $booking)
    {
        // Ensure the booking belongs to the authenticated user
        if ($booking->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        $booking->load(['instructor.user', 'service', 'suburb', 'payment']);
        
        return view('client.bookings.show', compact('booking'));
    }
    
    public function cancel(Request $request, Booking $booking)
    {
        // Ensure the booking belongs to the authenticated user
        if ($booking->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        // Check if the booking can be cancelled (not completed or already cancelled)
        if ($booking->status === 'completed' || $booking->status === 'cancelled') {
            return back()->with('error', 'This booking cannot be cancelled.');
        }
        
        // Check if the booking is within 24 hours
        $bookingDateTime = \Carbon\Carbon::parse($booking->date . ' ' . $booking->start_time);
        $now = \Carbon\Carbon::now();
        $hoursUntilBooking = $now->diffInHours($bookingDateTime, false);
        
        if ($hoursUntilBooking < 24) {
            return back()->with('error', 'Bookings can only be cancelled at least 24 hours in advance.');
        }
        
        $booking->status = 'cancelled';
        $booking->save();
        
        return redirect()->route('client.bookings.index')->with('success', 'Booking cancelled successfully.');
    }
}
