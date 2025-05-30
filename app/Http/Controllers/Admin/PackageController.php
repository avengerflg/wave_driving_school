<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\PackageOrder;
use App\Models\PackageOrderItem;
use App\Models\PackageCredit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PackageController extends Controller
{
    /**
     * Display a listing of the packages.
     */
    public function index(Request $request)
    {
        // Get sort parameters or set defaults
        $sortField = $request->input('sort', 'name');
        $sortDirection = $request->input('direction', 'asc');
        
        // Build query with filters
        $query = Package::query();
        
        // Apply search filter
        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%");
            });
        }
        
        // Apply status filter
        if ($request->filled('status')) {
            $status = $request->input('status');
            $query->where('active', $status === 'active');
        }
        
        // Apply sorting
        $query->orderBy($sortField, $sortDirection);
        
        // Get paginated results
        $packages = $query->paginate(10);
        
        // Calculate statistics
        $stats = [
            'total' => Package::count(),
            'active' => Package::where('active', true)->count(),
            'min_price' => Package::min('price') ? number_format(Package::min('price'), 2) : '0.00',
            'max_price' => Package::max('price') ? number_format(Package::max('price'), 2) : '0.00',
            'avg_price' => Package::avg('price') ? number_format(Package::avg('price'), 2) : '0.00',
        ];
        
        return view('admin.packages.index', compact('packages', 'stats', 'sortField', 'sortDirection'));
    }

    /**
     * Show the form for creating a new package.
     */
    public function create()
    {
        return view('admin.packages.create');
    }

    /**
     * Store a newly created package in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'description' => 'nullable',
            'price' => 'required|numeric|min:0',
            'lessons' => 'required|integer|min:1',
            'duration' => 'required|integer|min:15',
            'active' => 'boolean',
            'featured' => 'boolean',
        ]);
        
        // Handle boolean fields
        $validated['active'] = $request->has('active');
        $validated['featured'] = $request->has('featured');
        
        Package::create($validated);
        
        return redirect()->route('admin.packages.index')
            ->with('success', 'Package created successfully.');
    }

    /**
     * Display the specified package.
     */
    public function show(Package $package)
    {
        // Get recent orders for this package
        $recentOrders = PackageOrderItem::where('package_id', $package->id)
            ->with(['order', 'order.user'])
            ->latest()
            ->take(5)
            ->get();
        
        // Get counts of orders by status
        $orderCounts = [
            'total' => PackageOrderItem::where('package_id', $package->id)->count(),
            'completed' => PackageOrderItem::where('package_id', $package->id)
                ->whereHas('order', function($q) {
                    $q->where('status', 'completed');
                })->count(),
            'pending' => PackageOrderItem::where('package_id', $package->id)
                ->whereHas('order', function($q) {
                    $q->where('status', 'pending');
                })->count(),
            'cancelled' => PackageOrderItem::where('package_id', $package->id)
                ->whereHas('order', function($q) {
                    $q->where('status', 'cancelled');
                })->count(),
        ];
        
        // Get monthly sales data for chart
        $monthlyData = $this->getMonthlyOrderData($package->id);
        
        return view('admin.packages.show', [
            'package' => $package,
            'recentOrders' => $recentOrders,
            'orderCounts' => $orderCounts,
            'months' => $monthlyData['months'],
            'counts' => $monthlyData['counts'],
        ]);
    }

    /**
     * Show the form for editing the specified package.
     */
    public function edit(Package $package)
    {
        return view('admin.packages.edit', compact('package'));
    }

    /**
     * Update the specified package in storage.
     */
    public function update(Request $request, Package $package)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'description' => 'nullable',
            'price' => 'required|numeric|min:0',
            'lessons' => 'required|integer|min:1',
            'duration' => 'required|integer|min:15',
            'active' => 'boolean',
            'featured' => 'boolean',
        ]);
        
        // Handle boolean fields
        $validated['active'] = $request->has('active');
        $validated['featured'] = $request->has('featured');
        
        $package->update($validated);
        
        return redirect()->route('admin.packages.show', $package)
            ->with('success', 'Package updated successfully.');
    }

    /**
     * Remove the specified package from storage.
     */
    public function destroy(Package $package)
    {
        // Check if package has any orders
        $hasOrders = PackageOrderItem::where('package_id', $package->id)->exists();
        
        if ($hasOrders) {
            return redirect()->route('admin.packages.show', $package)
                ->with('error', 'This package cannot be deleted because it has associated orders. Consider deactivating it instead.');
        }
        
        $package->delete();
        
        return redirect()->route('admin.packages.index')
            ->with('success', 'Package deleted successfully.');
    }

    /**
     * Toggle the status of the specified package.
     */
    public function toggleStatus(Package $package)
    {
        $package->active = !$package->active;
        $package->save();
        
        $status = $package->active ? 'activated' : 'deactivated';
        
        return redirect()->back()
            ->with('success', "Package {$status} successfully.");
    }

    /**
     * Toggle the featured status of the specified package.
     */
    public function toggleFeatured(Package $package)
    {
        $package->featured = !$package->featured;
        $package->save();
        
        $status = $package->featured ? 'featured' : 'unfeatured';
        
        return redirect()->back()
            ->with('success', "Package {$status} successfully.");
    }

    /**
     * Show a list of all package orders.
     */
    public function orders()
    {
        $orders = PackageOrder::with(['user', 'items.package'])
            ->latest()
            ->paginate(15);
            
        return view('admin.packages.orders', compact('orders'));
    }

    /**
     * Show details of a specific order.
     */
    public function showOrder(PackageOrder $order)
    {
        $order->load(['user', 'items.package', 'payment']);
        
        return view('admin.packages.order-show', compact('order'));
    }

    /**
     * Update the status of an order.
     */
    public function updateOrderStatus(Request $request, PackageOrder $order)
    {
        $request->validate([
            'status' => 'required|in:pending,completed,cancelled',
        ]);
        
        $order->status = $request->status;
        $order->save();
        
        return redirect()->back()
            ->with('success', 'Order status updated successfully.');
    }

    /**
     * Get monthly order data for a package.
     */
    private function getMonthlyOrderData($packageId)
    {
        $sixMonthsAgo = now()->subMonths(5)->startOfMonth();
        
        // Use Carbon to manually process this instead of relying on database-specific date functions
        $orderItems = PackageOrderItem::where('package_id', $packageId)
            ->where('created_at', '>=', $sixMonthsAgo)
            ->select('quantity', 'created_at')
            ->get();
            
        // Group by month using PHP
        $orderData = [];
        foreach ($orderItems as $item) {
            $monthKey = Carbon::parse($item->created_at)->format('Y-m');
            
            if (!isset($orderData[$monthKey])) {
                $orderData[$monthKey] = 0;
            }
            
            $orderData[$monthKey] += $item->quantity;
        }
        
        $months = [];
        $counts = [];
        
        // Create data for last 6 months
        for ($i = 0; $i < 6; $i++) {
            $monthDate = now()->subMonths(5 - $i)->startOfMonth();
            $monthKey = $monthDate->format('Y-m');
            $monthName = $monthDate->format('M Y');
            
            $months[] = $monthName;
            $counts[] = $orderData[$monthKey] ?? 0;
        }
        
        return [
            'months' => $months,
            'counts' => $counts,
        ];
    }
}