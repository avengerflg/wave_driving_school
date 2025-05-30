<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\PackageOrder;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;

class OrderController extends Controller
{
    /**
     * Display a listing of all orders (packages and bookings).
     */
    public function index(Request $request)
    {
        // Get filter parameters
        $search = $request->input('search');
        $type = $request->input('type');
        $status = $request->input('status');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        
        // Query for package orders
        $packageOrdersQuery = PackageOrder::with(['user', 'items.package'])
            ->when($search, function($query) use ($search) {
                return $query->where('id', 'like', "%{$search}%")
                    ->orWhereHas('user', function($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%");
                    });
            })
            ->when($status, function($query) use ($status) {
                return $query->where('status', $status);
            })
            ->when($dateFrom, function($query) use ($dateFrom) {
                return $query->whereDate('created_at', '>=', $dateFrom);
            })
            ->when($dateTo, function($query) use ($dateTo) {
                return $query->whereDate('created_at', '<=', $dateTo);
            });
            
        // Query for bookings
        $bookingOrdersQuery = Booking::with(['user', 'instructor', 'service'])
            ->when($search, function($query) use ($search) {
                return $query->where('id', 'like', "%{$search}%")
                    ->orWhereHas('user', function($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%");
                    });
            })
            ->when($status, function($query) use ($status) {
                return $query->where('status', $status);
            })
            ->when($dateFrom, function($query) use ($dateFrom) {
                return $query->whereDate('date', '>=', $dateFrom);
            })
            ->when($dateTo, function($query) use ($dateTo) {
                return $query->whereDate('date', '<=', $dateTo);
            });
        
        // If filtering by type, only get the specified order type
        if ($type === 'package') {
            $bookingOrdersQuery = $bookingOrdersQuery->where('id', 0); // Force empty
        } elseif ($type === 'booking') {
            $packageOrdersQuery = $packageOrdersQuery->where('id', 0); // Force empty
        }
        
        // Get paginated results
        $packageOrders = $packageOrdersQuery->latest()->paginate(10, ['*'], 'package_page');
        $bookingOrders = $bookingOrdersQuery->latest()->paginate(10, ['*'], 'booking_page');
        
        // Combine package orders and bookings for the "All Orders" tab
        $allOrders = $this->combineAndPaginateOrders($packageOrdersQuery, $bookingOrdersQuery, $request);
        
        // Calculate total revenue
        $totalRevenue = $packageOrdersQuery->sum('total') + $bookingOrdersQuery->sum('price');
        
        // Get chart data for monthly summary
        $chartData = $this->getMonthlyOrderData();
        
        // Get recent activity data
        $recentActivity = $this->getRecentActivity();
        
        return view('admin.orders.index', [
            'packageOrders' => $packageOrders,
            'bookingOrders' => $bookingOrders,
            'allOrders' => $allOrders,
            'totalRevenue' => $totalRevenue,
            'chartData' => $chartData,
            'recentActivity' => $recentActivity
        ]);
    }
    
    /**
     * Combine package orders and bookings into a single paginated collection.
     */
    private function combineAndPaginateOrders($packageOrdersQuery, $bookingOrdersQuery, Request $request)
    {
        // Get all package orders
        $packageOrders = $packageOrdersQuery->get()->map(function($order) {
            $customerName = isset($order->user) && isset($order->user->name) ? $order->user->name : 'Unknown User';
            return [
                'id' => $order->id,
                'type' => 'package',
                'customer_id' => $order->user_id,
                'customer_name' => $customerName,
                'date' => $order->created_at->format('M d, Y'),
                'amount' => $order->total,
                'status' => $order->status,
                'created_at' => $order->created_at,
            ];
        });
        
        // Get all bookings - using user relationship instead of student
        $bookingOrders = $bookingOrdersQuery->get()->map(function($booking) {
            $customerName = isset($booking->user) && isset($booking->user->name) ? $booking->user->name : 'Unknown Student';
            return [
                'id' => $booking->id,
                'type' => 'booking',
                'customer_id' => $booking->user_id, // Using user_id instead of student_id
                'customer_name' => $customerName,
                'date' => $booking->date->format('M d, Y'),
                'amount' => $booking->price ?? 0,
                'status' => $booking->status,
                'created_at' => $booking->created_at,
            ];
        });
        
        // Combine and sort by date
        $combined = $packageOrders->concat($bookingOrders)->sortByDesc('created_at');
        
        // Create paginator
        $perPage = 15;
        $currentPage = $request->input('page', 1);
        $offset = ($currentPage - 1) * $perPage;
        $items = $combined->slice($offset, $perPage);
        
        $paginator = new LengthAwarePaginator(
            $items,
            $combined->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );
        
        return $paginator;
    }
    
    /**
     * Get monthly order data for the chart.
     */
    private function getMonthlyOrderData()
    {
        $months = [];
        $packageCounts = [];
        $bookingCounts = [];
        
        // Get data for last 6 months
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthName = $month->format('M');
            $startOfMonth = $month->startOfMonth();
            $endOfMonth = $month->endOfMonth();
            
            $months[] = $monthName;
            
            // Count package orders
            $packageCount = PackageOrder::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count();
            $packageCounts[] = $packageCount;
            
            // Count bookings
            $bookingCount = Booking::whereBetween('date', [$startOfMonth, $endOfMonth])->count();
            $bookingCounts[] = $bookingCount;
        }
        
        return [
            'months' => $months,
            'packageCounts' => $packageCounts,
            'bookingCounts' => $bookingCounts,
        ];
    }
    
    /**
     * Get recent order activity.
     */
    private function getRecentActivity()
    {
        $activity = [];
        
        // Get 5 most recent package orders
        $recentPackages = PackageOrder::with('user')->latest()->take(3)->get();
        foreach ($recentPackages as $order) {
            $userName = isset($order->user) && isset($order->user->name) ? $order->user->name : 'Unknown User';
            $activity[] = [
                'type' => 'package',
                'title' => "Package Order #{$order->id}",
                'time' => $order->created_at->diffForHumans(),
                'description' => "{$userName} purchased package(s) for \${$order->total}",
                'status' => ucfirst($order->status),
                'status_color' => $order->status === 'completed' ? 'success' : 
                                 ($order->status === 'cancelled' ? 'danger' : 'warning'),
            ];
        }
        
        // Get 5 most recent bookings - using user relationship instead of student
        $recentBookings = Booking::with(['user', 'service'])->latest()->take(3)->get();
        foreach ($recentBookings as $booking) {
            $userName = isset($booking->user) && isset($booking->user->name) ? $booking->user->name : 'Unknown Student';
            $serviceName = isset($booking->service) && isset($booking->service->name) ? $booking->service->name : 'a lesson';
            $activity[] = [
                'type' => 'booking',
                'title' => "Lesson Booking #{$booking->id}",
                'time' => $booking->created_at->diffForHumans(),
                'description' => "{$userName} booked {$serviceName} for {$booking->date->format('M d')}",
                'status' => ucfirst($booking->status),
                'status_color' => $booking->status === 'completed' ? 'success' : 
                                 ($booking->status === 'cancelled' ? 'danger' : 
                                 ($booking->status === 'confirmed' ? 'info' : 'warning')),
            ];
        }
        
        // Sort by most recent
        usort($activity, function($a, $b) {
            return strtotime($b['time']) - strtotime($a['time']);
        });
        
        return array_slice($activity, 0, 5);
    }
}