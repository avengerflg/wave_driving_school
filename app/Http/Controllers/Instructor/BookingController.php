<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $instructor = Auth::user()->instructor;
        
        $query = Booking::with(['user', 'service', 'suburb'])
            ->where('instructor_id', $instructor->id);
        
        // Apply filters
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('date', '>=', $request->date_from);
        }
        
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('date', '<=', $request->date_to);
        }
        
        // Default sorting
        $query->orderBy('date', 'desc')->orderBy('start_time', 'desc');
        
        $bookings = $query->paginate(10);
        
        return view('instructor.bookings.index', compact('bookings'));
    }
    
    public function show(Booking $booking)
    {
        // Ensure the booking belongs to the authenticated instructor
        if ($booking->instructor_id !== Auth::user()->instructor->id) {
            abort(403, 'Unauthorized action.');
        }
        
        $booking->load(['user', 'service', 'suburb', 'payment']);
        
        return view('instructor.bookings.show', compact('booking'));
    }
    
    public function updateStatus(Request $request, Booking $booking)
    {
        // Ensure the booking belongs to the authenticated instructor
        if ($booking->instructor_id !== Auth::user()->instructor->id) {
            abort(403, 'Unauthorized action.');
        }
        
        $request->validate([
            'status' => 'required|in:confirmed,completed,cancelled',
            'notes' => 'nullable|string|max:500',
        ]);
        
        $booking->status = $request->status;
        
        if ($request->has('notes')) {
            $booking->notes = $request->notes;
        }
        
        $booking->save();
        
        return redirect()->route('instructor.bookings.show', $booking)
            ->with('success', 'Booking status has been updated successfully.');
    }
}
