<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Availability;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $instructor = Auth::user()->instructor;
        
        // Get upcoming bookings
        $upcomingBookings = Booking::with(['user', 'service', 'suburb'])
            ->where('instructor_id', $instructor->id)
            ->whereIn('status', ['confirmed', 'pending'])
            ->where(function($query) {
                $query->whereDate('date', '>=', Carbon::today())
                    ->orWhere(function($q) {
                        $q->whereDate('date', '=', Carbon::today())
                            ->whereTime('start_time', '>=', Carbon::now()->format('H:i:s'));
                    });
            })
            ->orderBy('date')
            ->orderBy('start_time')
            ->take(5)
            ->get();
        
        // Get booking statistics
        $totalBookings = Booking::where('instructor_id', $instructor->id)->count();
        $completedBookings = Booking::where('instructor_id', $instructor->id)
            ->where('status', 'completed')
            ->count();
        $cancelledBookings = Booking::where('instructor_id', $instructor->id)
            ->where('status', 'cancelled')
            ->count();
        $pendingBookings = Booking::where('instructor_id', $instructor->id)
            ->where('status', 'pending')
            ->count();
        
        // Get earnings statistics
        $totalEarnings = Booking::where('instructor_id', $instructor->id)
            ->whereHas('payment', function($query) {
                $query->where('status', 'completed');
            })
            ->sum('amount');
        
        // Get monthly earnings
        $monthlyEarnings = Booking::where('instructor_id', $instructor->id)
            ->whereHas('payment', function($query) {
                $query->where('status', 'completed');
            })
            ->whereYear('date', Carbon::now()->year)
            ->whereMonth('date', Carbon::now()->month)
            ->sum('amount');
        
        // Get availability count
        $availabilityCount = Availability::where('instructor_id', $instructor->id)
            ->whereDate('date', '>=', Carbon::today())
            ->count();
        
        return view('instructor.dashboard', compact(
            'upcomingBookings',
            'totalBookings',
            'completedBookings',
            'cancelledBookings',
            'pendingBookings',
            'totalEarnings',
            'monthlyEarnings',
            'availabilityCount'
        ));
    }
}
