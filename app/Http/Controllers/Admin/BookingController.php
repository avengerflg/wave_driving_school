<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\User;
use App\Models\Instructor;
use App\Models\Service;
use App\Models\Suburb;

class BookingController extends Controller
{
    /**
     * Display a listing of all bookings.
     */
    public function index(Request $request)
{
    $search = $request->input('search'); // Search query
    $status = $request->input('status'); // Status filter

    $bookings = Booking::with(['user', 'instructor.user', 'service', 'suburb'])
        ->when($search, function ($query, $search) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%$search%");
            })
            ->orWhereHas('instructor.user', function ($q) use ($search) {
                $q->where('name', 'like', "%$search%");
            })
            ->orWhereHas('service', function ($q) use ($search) {
                $q->where('name', 'like', "%$search%");
            });
        })
        ->when($status, function ($query, $status) {
            $query->where('status', $status);
        })
        ->orderBy('created_at', 'desc')
        ->paginate(10);

    // Add these lines to fetch data needed for the booking creation modal
    $users = User::all();
    $instructors = Instructor::with('user')->get();
    $services = Service::all();
    $suburbs = Suburb::all();

    return view('admin.bookings.index', compact('bookings', 'search', 'status', 'users', 'instructors', 'services', 'suburbs'));
}

    /**
     * Show the form for creating a new booking.
     */
    public function create()
    {
        $users = User::all();
        $instructors = Instructor::with('user')->get();
        $services = Service::all();
        $suburbs = Suburb::all();

        return view('admin.bookings.create', compact('users', 'instructors', 'services', 'suburbs'));
    }

    /**
     * Store a newly created booking in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'instructor_id' => 'required|exists:instructors,id',
            'service_id' => 'required|exists:services,id',
            'suburb_id' => 'required|exists:suburbs,id',
            'date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
            'status' => 'required|in:pending,confirmed,completed,cancelled',
        ]);

        Booking::create($validated);

        return redirect()->route('admin.bookings.index')->with('success', 'Booking created successfully.');
    }

    /**
     * Display the specified booking.
     */
    public function show($id)
    {
        $booking = Booking::with(['user', 'instructor.user', 'service', 'suburb'])->findOrFail($id);

        return view('admin.bookings.show', compact('booking'));
    }

    /**
     * Show the form for editing the specified booking.
     */
    public function edit($id)
    {
        $booking = Booking::findOrFail($id);
        $statuses = ['pending', 'confirmed', 'cancelled'];
        $users = User::all();
        $instructors = Instructor::with('user')->get();
        $services = Service::all();
        $suburbs = Suburb::all();

        return view('admin.bookings.edit', compact('booking', 'statuses', 'users', 'instructors', 'services', 'suburbs'));
    }

    /**
     * Update the specified booking in storage.
     */
    public function update(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'instructor_id' => 'required|exists:instructors,id',
            'service_id' => 'required|exists:services,id',
            'suburb_id' => 'required|exists:suburbs,id',
            'date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
            'status' => 'required|in:pending,confirmed,completed,cancelled',
        ]);

        $booking->update($validated);

        return redirect()->route('admin.bookings.index')->with('success', 'Booking updated successfully.');
    }

    /**
     * Remove the specified booking from storage.
     */
    public function destroy($id)
    {
        $booking = Booking::findOrFail($id);
        $booking->delete();

        return redirect()->route('admin.bookings.index')->with('success', 'Booking deleted successfully.');
    }

    /**
     * Update the status of the specified booking.
     */
    public function updateStatus(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,completed,cancelled',
        ]);

        $booking->update($validated);

        return redirect()->route('admin.bookings.index')->with('success', 'Booking status updated successfully.');
    }
}