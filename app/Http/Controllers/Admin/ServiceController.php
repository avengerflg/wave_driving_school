<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ServiceController extends Controller
{
    /**
     * Display a listing of the services.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');
        $sortField = $request->input('sort', 'name');
        $sortDirection = $request->input('direction', 'asc');
        
        $services = Service::query()
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when($status !== null, function ($query) use ($status) {
                return $query->where('active', $status === 'active');
            })
            ->orderBy($sortField, $sortDirection)
            ->paginate(10)
            ->withQueryString();

        // Get stats for dashboard widgets
        $stats = [
            'total' => Service::count(),
            'active' => Service::where('active', true)->count(),
            'inactive' => Service::where('active', false)->count(),
            'min_price' => Service::min('price') ?: 0,
            'max_price' => Service::max('price') ?: 0,
            'avg_price' => round(Service::avg('price') ?: 0, 2),
        ];
        
        return view('admin.services.index', compact('services', 'search', 'status', 'sortField', 'sortDirection', 'stats'));
    }

    /**
     * Show the form for creating a new service.
     */
    public function create()
    {
        // Get default settings for new services
        try {
            $defaultDuration = Setting::where('key', 'default_service_duration')->first();
            $defaultDuration = $defaultDuration ? $defaultDuration->value : 60;
        } catch (\Exception $e) {
            Log::error('Error fetching default duration: ' . $e->getMessage());
            $defaultDuration = 60;
        }
        
        return view('admin.services.create', compact('defaultDuration'));
    }

    /**
     * Store a newly created service.
     */
    public function store(Request $request)
    {
        try {
            // Validate the request
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'required|numeric|min:0',
                'duration' => 'required|integer|min:15',
                'category' => 'nullable|string|max:50',
                'image_path' => 'nullable|string|max:255',
            ]);

            // Handle boolean fields
            $validated['active'] = $request->has('active');
            $validated['featured'] = $request->has('featured');

            // Create the service
            $service = Service::create($validated);

            return redirect()
                ->route('admin.services.index')
                ->with('success', 'Service created successfully');

        } catch (\Exception $e) {
            Log::error('Service creation failed: ' . $e->getMessage());
            
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to create service. ' . $e->getMessage());
        }
    }

    /**
     * Display the specified service.
     */
    public function show(Service $service)
    {
        $recentBookings = $service->bookings()
            ->with(['user', 'instructor.user'])
            ->latest()
            ->take(5)
            ->get();
            
        $bookingCounts = [
            'total' => $service->bookings()->count(),
            'completed' => $service->bookings()->where('status', 'completed')->count(),
            'pending' => $service->bookings()->where('status', 'pending')->count(),
            'cancelled' => $service->bookings()->where('status', 'cancelled')->count(),
        ];
        
        // Monthly booking stats with cross-database compatibility
        $query = $service->bookings()
            ->where('date', '>=', now()->subMonths(6)->startOfMonth()->format('Y-m-d'));
            
        // Check database connection type and use appropriate date formatting
        if (DB::connection()->getDriverName() === 'mysql') {
            $monthlyStats = $query->select(
                DB::raw('DATE_FORMAT(date, "%Y-%m") as month'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('count', 'month')
            ->toArray();
        } else {
            // SQLite and others
            $monthlyStats = $query->select(
                DB::raw('strftime("%Y-%m", date) as month'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('count', 'month')
            ->toArray();
        }
        
        $months = [];
        $counts = [];
        
        // Fill in missing months with 0 counts
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i)->format('Y-m');
            $months[] = now()->subMonths($i)->format('M Y');
            $counts[] = $monthlyStats[$month] ?? 0;
        }
        
        return view('admin.services.show', compact(
            'service',
            'recentBookings',
            'bookingCounts',
            'months',
            'counts'
        ));
    }

    /**
     * Show the form for editing the specified service.
     */
    public function edit(Service $service)
    {
        return view('admin.services.edit', compact('service'));
    }

    /**
     * Update the specified service.
     */
    public function update(Request $request, Service $service)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:services,name,'.$service->id,
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'duration' => 'required|integer|min:15',
            'active' => 'boolean',
            'featured' => 'boolean',
        ]);
        
        $validated['active'] = $request->has('active');
        $validated['featured'] = $request->has('featured');
        
        $service->update($validated);
        
        return redirect()->route('admin.services.show', $service)
            ->with('success', 'Service updated successfully.');
    }

    /**
     * Remove the specified service.
     */
    public function destroy(Service $service)
    {
        // Check if service has bookings
        if ($service->bookings()->count() > 0) {
            return back()->with('error', 'Cannot delete service with existing bookings.');
        }
        
        $service->delete();
        
        return redirect()->route('admin.services.index')
            ->with('success', 'Service deleted successfully.');
    }
    
    /**
     * Toggle service active status.
     */
    public function toggleStatus(Service $service)
    {
        $service->active = !$service->active;
        $service->save();
        
        $status = $service->active ? 'activated' : 'deactivated';
        
        return back()->with('success', "Service {$status} successfully.");
    }
    
    /**
     * Toggle service featured status.
     */
    public function toggleFeatured(Service $service)
    {
        $service->featured = !$service->featured;
        $service->save();
        
        $status = $service->featured ? 'set as featured' : 'removed from featured';
        
        return back()->with('success', "Service {$status} successfully.");
    }
    
    /**
     * Bulk update service prices.
     */
    public function bulkUpdatePrices(Request $request)
    {
        $validated = $request->validate([
            'adjustment_type' => 'required|in:percentage,fixed',
            'adjustment_value' => 'required|numeric',
            'selected_services' => 'required|array',
            'selected_services.*' => 'exists:services,id',
        ]);
        
        $type = $validated['adjustment_type'];
        $value = $validated['adjustment_value'];
        $serviceIds = $validated['selected_services'];
        
        try {
            DB::beginTransaction();
            
            $services = Service::whereIn('id', $serviceIds)->get();
            
            foreach ($services as $service) {
                $originalPrice = $service->price;
                $newPrice = $originalPrice;
                
                if ($type === 'percentage') {
                    // Calculate new price based on percentage
                    $newPrice = $originalPrice * (1 + ($value / 100));
                } else {
                    // Fixed amount adjustment
                    $newPrice = $originalPrice + $value;
                }
                
                // Ensure price doesn't go below zero
                $newPrice = max(0, $newPrice);
                
                // Round to 2 decimal places
                $newPrice = round($newPrice, 2);
                
                $service->price = $newPrice;
                $service->save();
            }
            
            DB::commit();
            
            return redirect()->route('admin.services.index')
                ->with('success', count($services) . ' services updated successfully.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating service prices: ' . $e->getMessage());
            return back()->with('error', 'Error updating service prices: ' . $e->getMessage());
        }
    }
}