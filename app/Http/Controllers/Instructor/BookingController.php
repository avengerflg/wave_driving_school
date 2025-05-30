<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Service;
use App\Models\Suburb;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $instructor = Auth::user()->instructor;
        
        $query = Booking::with(['user', 'service', 'suburb'])
            ->where('instructor_id', $instructor->id);
        
        // Search filter
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%$search%");
            })
            ->orWhereHas('service', function ($q) use ($search) {
                $q->where('name', 'like', "%$search%");
            });
        }

        // Status filter
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Date filters
        if ($request->has('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }
        if ($request->has('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        $bookings = $query->orderBy('date', 'desc')
                         ->orderBy('start_time', 'desc')
                         ->paginate(10);

        return view('instructor.bookings.index', compact('bookings'));
    }

    public function show(Booking $booking)
    {
        // Ensure the booking belongs to the authenticated instructor
        if ($booking->instructor_id !== Auth::user()->instructor->id) {
            abort(403, 'Unauthorized action.');
        }

        $booking->load(['user', 'service', 'suburb']);

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

    $oldStatus = $booking->status;
    $newNotes = $request->notes;
    
    // Add a timestamp note if notes were provided
    if ($newNotes) {
        $notesAddition = "[" . now()->format('Y-m-d H:i') . "] Status changed to " . $request->status . ": " . $newNotes;
        $updatedNotes = $booking->notes 
            ? $booking->notes . "\n\n" . $notesAddition 
            : $notesAddition;
    } else {
        $updatedNotes = $booking->notes;
    }
    
    $booking->update([
        'status' => $request->status,
        'notes' => $updatedNotes
    ]);

    // Send notification to student about status change
    if ($booking->user && $oldStatus !== $request->status) {
        $booking->user->notify(new \App\Notifications\BookingStatusChanged(
            $booking, 
            $oldStatus, 
            $newNotes
        ));
    }

    return redirect()->route('instructor.bookings.show', $booking)
        ->with('success', 'Booking status updated successfully and the student has been notified.');
}

    public function calendar()
    {
        $instructor = Auth::user()->instructor;
        
        $bookings = $instructor->bookings()
            ->with(['user', 'service'])
            ->get()
            ->map(function ($booking) {
                return [
                    'id' => $booking->id,
                    'title' => $booking->user->name . ' - ' . $booking->service->name,
                    'start' => $booking->date->format('Y-m-d') . ' ' . $booking->start_time->format('H:i:s'),
                    'end' => $booking->date->format('Y-m-d') . ' ' . $booking->end_time->format('H:i:s'),
                    'className' => $this->getStatusClass($booking->status),
                    'extendedProps' => [
                        'status' => $booking->status,
                        'service' => $booking->service->name,
                        'location' => $booking->suburb->name
                    ]
                ];
            });

        return view('instructor.bookings.calendar', compact('bookings'));
    }

    public function rescheduleForm(Booking $booking)
    {
        // Ensure the booking belongs to the authenticated instructor
        if ($booking->instructor_id !== Auth::user()->instructor->id) {
            abort(403, 'Unauthorized action.');
        }

        // Can't reschedule completed or cancelled bookings
        if (in_array($booking->status, ['completed', 'cancelled'])) {
            return redirect()->route('instructor.bookings.show', $booking)
                ->with('error', 'Cannot reschedule a ' . $booking->status . ' booking.');
        }

        $booking->load(['user', 'service', 'suburb']);
        
        return view('instructor.bookings.reschedule', compact('booking'));
    }

    public function reschedule(Request $request, Booking $booking)
{
    // Ensure the booking belongs to the authenticated instructor
    if ($booking->instructor_id !== Auth::user()->instructor->id) {
        abort(403, 'Unauthorized action.');
    }
    
    $request->validate([
        'date' => 'required|date|date_format:Y-m-d|after_or_equal:today',
        'start_time' => 'required|date_format:H:i',
        'end_time' => 'required|date_format:H:i|after:start_time',
        'notes' => 'nullable|string|max:500',
    ]);

    // Store old values for notification
    $oldDate = $booking->date->format('Y-m-d');
    $oldStartTime = $booking->start_time->format('H:i:s');

    // Calculate duration in minutes
    $start = Carbon::parse($request->start_time);
    $end = Carbon::parse($request->end_time);
    $duration = $start->diffInMinutes($end);

    // Check instructor availability for the new time
    $conflictingBookings = Booking::where('instructor_id', $booking->instructor_id)
        ->where('id', '!=', $booking->id)
        ->where('date', $request->date)
        ->where(function ($query) use ($request) {
            $query->whereBetween('start_time', [$request->start_time, $request->end_time])
                ->orWhereBetween('end_time', [$request->start_time, $request->end_time])
                ->orWhere(function ($q) use ($request) {
                    $q->where('start_time', '<', $request->start_time)
                      ->where('end_time', '>', $request->end_time);
                });
        })
        ->exists();

    if ($conflictingBookings) {
        return back()->withErrors(['time_conflict' => 'You already have a booking scheduled during this time.'])->withInput();
    }

    $notesForReschedule = $request->notes;
    
    // Update booking notes
    $noteAddition = "[" . now()->format('Y-m-d H:i') . "] Rescheduled: ";
    $noteAddition .= $notesForReschedule ? $notesForReschedule : "No additional notes provided.";
    
    $updatedNotes = $booking->notes 
        ? $booking->notes . "\n\n" . $noteAddition 
        : $noteAddition;
    
    $booking->update([
        'date' => $request->date,
        'start_time' => $request->start_time,
        'end_time' => $request->end_time,
        'notes' => $updatedNotes,
        'status' => 'confirmed' // Reset status to confirmed when rescheduled
    ]);

    // Send notification to student about rescheduling
    if ($booking->user) {
        $booking->user->notify(new \App\Notifications\BookingRescheduled(
            $booking, 
            $oldDate, 
            $oldStartTime, 
            $notesForReschedule
        ));
    }

    return redirect()->route('instructor.bookings.show', $booking)
        ->with('success', 'Booking has been rescheduled successfully and the student has been notified.');
}

    private function getStatusClass($status)
    {
        return match($status) {
            'confirmed' => 'bg-light-primary',
            'completed' => 'bg-light-success',
            'cancelled' => 'bg-light-danger',
            'pending' => 'bg-light-warning',
            default => 'bg-light-secondary'
        };
    }

    
}