<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use App\Models\Booking;

class DashboardController extends Controller
{
    public function index()
    {
        $instructor = Auth::user()->instructor;
        $today = Carbon::today();
        
        // Fetch basic statistics
        $stats = [
            'todayBookingsCount' => $this->getTodayBookingsCount($instructor),
            'totalBookings' => $this->getTotalBookings($instructor),
            'completedBookings' => $this->getCompletedBookings($instructor),
            'pendingBookings' => $this->getPendingBookings($instructor),
            'confirmedBookings' => $this->getConfirmedBookings($instructor),
            'cancelledBookings' => $this->getCancelledBookings($instructor),
            'totalRevenue' => $this->getTotalRevenue($instructor),
            'monthlyRevenue' => $this->getMonthlyRevenue($instructor),
            'totalStudents' => $this->getTotalStudents($instructor),
        ];

        // Get today's bookings
        $todayBookings = $instructor->bookings()
            ->whereDate('date', $today)
            ->with(['user', 'service', 'suburb'])
            ->orderBy('start_time')
            ->get();

        // Get upcoming bookings
        $upcomingBookings = $instructor->bookings()
            ->where('date', '>=', $today)
            ->where('status', '!=', 'cancelled')
            ->with(['user', 'service'])
            ->orderBy('date')
            ->orderBy('start_time')
            ->take(5)
            ->get();

        // Prepare chart data
        $chartData = $this->prepareChartData($instructor);

        // Get recent activities
        $recentActivities = $this->getRecentActivities($instructor);

        // Get performance metrics
        $performanceMetrics = $this->getPerformanceMetrics($instructor);

        return view('instructor.dashboard', compact(
            'stats',
            'todayBookings',
            'upcomingBookings',
            'chartData',
            'recentActivities',
            'performanceMetrics'
        ));
    }

    private function getTodayBookingsCount($instructor)
    {
        return $instructor->bookings()
            ->whereDate('date', Carbon::today())
            ->count();
    }

    private function getTotalBookings($instructor)
    {
        return $instructor->bookings()->count();
    }

    private function getCompletedBookings($instructor)
    {
        return $instructor->bookings()
            ->where('status', 'completed')
            ->count();
    }

    private function getPendingBookings($instructor)
    {
        return $instructor->bookings()
            ->where('status', 'pending')
            ->count();
    }

    private function getConfirmedBookings($instructor)
    {
        return $instructor->bookings()
            ->where('status', 'confirmed')
            ->where('date', '>=', Carbon::today())
            ->count();
    }

    private function getCancelledBookings($instructor)
    {
        return $instructor->bookings()
            ->where('status', 'cancelled')
            ->count();
    }

    private function getTotalRevenue($instructor)
    {
        return $instructor->bookings()
            ->where('status', 'completed')
            ->sum('price');
    }

    private function getMonthlyRevenue($instructor)
    {
        return $instructor->bookings()
            ->where('status', 'completed')
            ->whereMonth('date', Carbon::now()->month)
            ->whereYear('date', Carbon::now()->year)
            ->sum('price');
    }

    private function getTotalStudents($instructor)
    {
        return $instructor->bookings()
            ->distinct('user_id')
            ->count('user_id');
    }

    private function prepareChartData($instructor)
    {
        // Prepare monthly bookings data
        $monthlyBookings = collect();
        $monthlyRevenue = collect();
        $labels = collect();

        // Get data for the last 6 months
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $labels->push($date->format('M Y'));

            $bookings = $instructor->bookings()
                ->whereYear('date', $date->year)
                ->whereMonth('date', $date->month)
                ->count();
            $monthlyBookings->push($bookings);

            $revenue = $instructor->bookings()
                ->whereYear('date', $date->year)
                ->whereMonth('date', $date->month)
                ->where('status', 'completed')
                ->sum('price');
            $monthlyRevenue->push($revenue);
        }

        // Weekly data
        $weeklyData = $this->getWeeklyBookingsData($instructor);

        return [
            'labels' => $labels,
            'monthlyBookings' => $monthlyBookings,
            'monthlyRevenue' => $monthlyRevenue,
            'weekly' => $weeklyData,
        ];
    }

    private function getWeeklyBookingsData($instructor)
    {
        $period = CarbonPeriod::create(
            Carbon::now()->startOfWeek(),
            Carbon::now()->endOfWeek()
        );

        $weeklyData = collect();
        foreach ($period as $date) {
            $weeklyData->push([
                'date' => $date->format('D'),
                'bookings' => $instructor->bookings()
                    ->whereDate('date', $date)
                    ->count()
            ]);
        }

        return $weeklyData;
    }

    private function getRecentActivities($instructor)
    {
        return $instructor->bookings()
            ->with(['user', 'service'])
            ->latest('updated_at')
            ->take(5)
            ->get()
            ->map(function ($booking) {
                return [
                    'type' => $this->getActivityType($booking),
                    'message' => $this->getActivityMessage($booking),
                    'time' => $booking->updated_at->diffForHumans(),
                    'booking' => $booking
                ];
            });
    }

    private function getActivityType($booking)
    {
        switch ($booking->status) {
            case 'completed':
                return 'success';
            case 'cancelled':
                return 'danger';
            case 'confirmed':
                return 'primary';
            default:
                return 'warning';
        }
    }

    private function getActivityMessage($booking)
    {
        switch ($booking->status) {
            case 'completed':
                return "Completed lesson with {$booking->user->name}";
            case 'cancelled':
                return "Booking cancelled by {$booking->user->name}";
            case 'confirmed':
                return "New booking confirmed with {$booking->user->name}";
            default:
                return "New booking request from {$booking->user->name}";
        }
    }

    private function getPerformanceMetrics($instructor)
    {
        $totalBookings = $instructor->bookings()->count();
        $completedBookings = $instructor->bookings()->where('status', 'completed')->count();
        
        return [
            'completion_rate' => $totalBookings > 0 
                ? round(($completedBookings / $totalBookings) * 100, 1) 
                : 0,
            'average_rating' => $instructor->bookings()
                ->whereNotNull('rating')
                ->avg('rating') ?? 0,
            'repeat_students' => $this->getRepeatStudentsCount($instructor),
            'busy_days' => $this->getBusyDays($instructor),
        ];
    }

    private function getRepeatStudentsCount($instructor)
    {
        return $instructor->bookings()
            ->select('user_id')
            ->groupBy('user_id')
            ->havingRaw('COUNT(*) > 1')
            ->count();
    }

    private function getBusyDays($instructor)
    {
        return $instructor->bookings()
            ->select('date')
            ->selectRaw('COUNT(*) as booking_count')
            ->groupBy('date')
            ->orderByDesc('booking_count')
            ->limit(5)
            ->get();
    }
}