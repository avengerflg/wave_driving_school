<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class BookingController extends Controller
{
    public function index()
    {
        $bookings = collect(); // Empty collection for now
        
        try {
            if (method_exists(Auth::user(), 'bookings')) {
                $bookings = Auth::user()->bookings()
                    ->with(['instructor', 'service'])
                    ->orderBy('date', 'desc')
                    ->paginate(10);
            }
        } catch (\Exception $e) {
            Log::error('Error getting student bookings', ['error' => $e->getMessage()]);
        }

        return view('student.bookings.index', compact('bookings'));
    }

    public function show(Booking $booking)
    {
        // Ensure user can only view their own bookings
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }

        return view('student.bookings.show', compact('booking'));
    }

    public function cancel(Booking $booking)
    {
        // Ensure user can only cancel their own bookings
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }

        $booking->update(['status' => 'cancelled']);

        return redirect()->route('student.bookings.index')
            ->with('success', 'Booking cancelled successfully.');
    }

    public function storeReview(Request $request, Booking $booking)
    {
        // Ensure user can only review their own completed bookings
        if ($booking->user_id !== Auth::id() || $booking->status !== 'completed') {
            abort(403);
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string|max:1000'
        ]);

        $booking->update([
            'rating' => $request->rating,
            'review' => $request->review
        ]);

        return redirect()->route('student.bookings.show', $booking)
            ->with('success', 'Review submitted successfully.');
    }
}
