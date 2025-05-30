<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $instructor = Auth::user()->instructor;
        
        $search = $request->input('search');
        $status = $request->input('status');
        
        $clients = User::where('role', 'student')
            ->whereHas('bookings', function($query) use ($instructor) {
                $query->where('instructor_id', $instructor->id);
            })
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
            ->withCount(['bookings' => function($query) use ($instructor) {
                $query->where('instructor_id', $instructor->id);
            }])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('instructor.clients.index', compact('clients', 'search', 'status'));
    }

    public function show(User $client)
    {
        $instructor = Auth::user()->instructor;
        
        if (!$client->bookings()->where('instructor_id', $instructor->id)->exists()) {
            abort(403, 'This client is not associated with you');
        }
        
        $bookings = $client->bookings()
            ->where('instructor_id', $instructor->id)
            ->with(['service', 'suburb'])
            ->latest()
            ->get();
            
        $stats = [
            'total_lessons' => $bookings->count(),
            'completed_lessons' => $bookings->where('status', 'completed')->count(),
            'upcoming_lessons' => $bookings->where('status', 'scheduled')->count(),
            'cancelled_lessons' => $bookings->where('status', 'cancelled')->count(),
        ];
            
        return view('instructor.clients.show', compact('client', 'bookings', 'stats'));
    }

    public function bookings(User $client)
    {
        $instructor = Auth::user()->instructor;
        
        if (!$client->bookings()->where('instructor_id', $instructor->id)->exists()) {
            abort(403, 'This client is not associated with you');
        }
        
        $bookings = $client->bookings()
            ->where('instructor_id', $instructor->id)
            ->with(['service', 'suburb'])
            ->orderBy('date', 'desc')
            ->orderBy('start_time', 'desc')
            ->paginate(10);
            
        return view('instructor.clients.bookings', compact('client', 'bookings'));
    }
}