<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Debug logging
        Log::info('Student Dashboard accessed', [
            'user_id' => Auth::id(),
            'user_role' => Auth::user()->role,
            'route' => request()->route()->getName()
        ]);
        
        $user = Auth::user();
        
        // Initialize stats with fallbacks
        $stats = [
            'totalBookings' => 0,
            'completedBookings' => 0,
            'upcomingBookings' => 0,
            'totalHours' => 0,
            'favoriteSuburbs' => 'N/A',
            'nextLessonDate' => null
        ];
        
        // Initialize collections
        $recentBookings = collect();
        $upcomingBookings = collect();
        
        // Try to get bookings safely
        try {
            if (method_exists($user, 'bookings')) {
                $bookings = $user->bookings()->with(['instructor', 'service'])->get();
                
                $stats['totalBookings'] = $bookings->count();
                $stats['completedBookings'] = $bookings->where('status', 'completed')->count();
                
                // Get upcoming bookings
                $upcomingBookingsQuery = $bookings->where('date', '>=', now()->toDateString())
                                                 ->where('status', '!=', 'cancelled');
                $stats['upcomingBookings'] = $upcomingBookingsQuery->count();
                
                // Calculate total hours
                $stats['totalHours'] = $bookings->where('status', 'completed')->sum('duration') ?? 0;
                
                // Get next lesson date
                $nextLesson = $upcomingBookingsQuery->sortBy('date')->first();
                if ($nextLesson) {
                    $stats['nextLessonDate'] = Carbon::parse($nextLesson->date)->format('M d, Y');
                }
                
                // Get recent and upcoming bookings for display
                $recentBookings = $bookings->sortByDesc('date')->take(5);
                $upcomingBookings = $upcomingBookingsQuery->sortBy('date')->take(3);
                
                // Get favorite suburbs
                $suburbCounts = $bookings->where('status', 'completed')
                                        ->groupBy('suburb_id')
                                        ->map->count()
                                        ->sortDesc();
                
                if ($suburbCounts->isNotEmpty()) {
                    $favoriteSuburbId = $suburbCounts->keys()->first();
                    $favoriteSuburb = \App\Models\Suburb::find($favoriteSuburbId);
                    $stats['favoriteSuburbs'] = $favoriteSuburb ? $favoriteSuburb->name : 'N/A';
                }
            }
        } catch (\Exception $e) {
            Log::error('Error getting bookings for student dashboard', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);
        }
        
        // Enhanced chart data with real data when available
        $chartData = $this->generateChartData($bookings ?? collect());
        
        // Get favorite instructors
        $favoriteInstructors = $this->getFavoriteInstructors($bookings ?? collect());
        
        // Calculate skills progress
        $skillsProgress = $this->calculateSkillsProgress($bookings ?? collect());
        
        return view('student.dashboard', compact(
            'recentBookings', 
            'upcomingBookings', 
            'stats', 
            'favoriteInstructors', 
            'skillsProgress', 
            'chartData'
        ));
    }
    
    /**
     * Generate chart data based on actual bookings
     */
    private function generateChartData($bookings)
    {
        $months = [];
        $lessonsCompleted = [];
        $hoursDriven = [];
        
        // Get last 6 months
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $months[] = $month->format('M');
            
            $monthBookings = $bookings->filter(function ($booking) use ($month) {
                return Carbon::parse($booking->date)->isSameMonth($month) && 
                       $booking->status === 'completed';
            });
            
            $lessonsCompleted[] = $monthBookings->count();
            $hoursDriven[] = $monthBookings->sum('duration') ?? 0;
        }
        
        return [
            'lessonsCompleted' => $lessonsCompleted,
            'hoursDriven' => $hoursDriven,
            'labels' => $months
        ];
    }
    
    /**
     * Get favorite instructors based on booking history
     */
    private function getFavoriteInstructors($bookings)
    {
        return $bookings->where('status', 'completed')
                       ->groupBy('instructor_id')
                       ->map(function ($instructorBookings) {
                           $instructor = $instructorBookings->first()->instructor;
                           return [
                               'instructor' => $instructor,
                               'lessons_count' => $instructorBookings->count(),
                               'total_hours' => $instructorBookings->sum('duration')
                           ];
                       })
                       ->sortByDesc('lessons_count')
                       ->take(3);
    }
    
    /**
     * Calculate skills progress based on completed lessons
     */
    private function calculateSkillsProgress($bookings)
    {
        $completedLessons = $bookings->where('status', 'completed');
        $totalLessons = $completedLessons->count();
        
        if ($totalLessons === 0) {
            return [
                'basic_control' => 0,
                'traffic_navigation' => 0,
                'parking' => 0,
                'highway_driving' => 0
            ];
        }
        
        // Simple progress calculation based on lesson count
        $basicControl = min(100, ($totalLessons / 5) * 100);
        $trafficNavigation = min(100, max(0, (($totalLessons - 3) / 7) * 100));
        $parking = min(100, max(0, (($totalLessons - 5) / 5) * 100));
        $highwayDriving = min(100, max(0, (($totalLessons - 8) / 7) * 100));
        
        return [
            'basic_control' => round($basicControl),
            'traffic_navigation' => round($trafficNavigation),
            'parking' => round($parking),
            'highway_driving' => round($highwayDriving)
        ];
    }
}
