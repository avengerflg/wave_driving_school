<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\User;
use App\Models\Instructor;
use App\Models\Service;
use App\Models\Suburb;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Get counts
        $totalUsers = User::where('role', 'user')->count();
        $totalInstructors = Instructor::count();
        $totalBookings = Booking::count();
        $totalSuburbs = Suburb::count();
        
        // Get recent bookings
        $recentBookings = Booking::with(['user', 'instructor.user', 'service', 'suburb'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        // Get booking statistics
        $pendingBookings = Booking::where('status', 'pending')->count();
        $confirmedBookings = Booking::where('status', 'confirmed')->count();
        $completedBookings = Booking::where('status', 'completed')->count();
        $cancelledBookings = Booking::where('status', 'cancelled')->count();
        
        // Get revenue statistics
        $totalRevenue = DB::table('payments')->where('status', 'completed')->sum('amount');
        $monthlyRevenue = DB::table('payments')
            ->where('status', 'completed')
            ->whereYear('created_at', Carbon::now()->year)
            ->whereMonth('created_at', Carbon::now()->month)
            ->sum('amount');
        
        // Get monthly bookings for the current year
        $monthlyBookings = DB::table('bookings')
            ->selectRaw('MONTH(date) as month, COUNT(*) as count')
            ->whereYear('date', Carbon::now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();
        
        // Format monthly bookings data for chart
        $monthlyBookingsData = array_fill(0, 12, 0);
        foreach ($monthlyBookings as $booking) {
            $monthlyBookingsData[$booking->month - 1] = $booking->count;
        }
        
        // Get monthly revenue for the current year
        $monthlyRevenueData = DB::table('payments')
            ->selectRaw('MONTH(created_at) as month, SUM(amount) as total')
            ->where('status', 'completed')
            ->whereYear('created_at', Carbon::now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();
        
        // Format monthly revenue data for chart
        $monthlyRevenueChartData = array_fill(0, 12, 0);
        foreach ($monthlyRevenueData as $revenue) {
            $monthlyRevenueChartData[$revenue->month - 1] = $revenue->total;
        }
        
        // Get top instructors by booking count
        $topInstructors = Instructor::withCount(['bookings' => function($query) {
                $query->where('status', '!=', 'cancelled');
            }])
            ->with('user')
            ->orderBy('bookings_count', 'desc')
            ->take(5)
            ->get();
        
        // Get top suburbs by booking count
        $topSuburbs = Suburb::withCount(['bookings' => function($query) {
                $query->where('status', '!=', 'cancelled');
            }])
            ->orderBy('bookings_count', 'desc')
            ->take(5)
            ->get();
        
        return view('admin.dashboard', compact(
            'totalUsers',
            'totalInstructors',
            'totalBookings',
            'totalSuburbs',
            'recentBookings',
            'pendingBookings',
            'confirmedBookings',
            'completedBookings',
            'cancelledBookings',
            'totalRevenue',
            'monthlyRevenue',
            'monthlyBookingsData',
            'monthlyRevenueChartData',
            'topInstructors',
            'topSuburbs'
        ));
    }
}
