<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\PackageCredit;
use App\Models\PackageOrder;
use App\Models\Booking;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    /**
     * Display a listing of available packages.
     */
    public function index()
    {
        $packages = Package::where('active', true)->get();
        return view('student.packages.index', compact('packages'));
    }
    
    /**
     * Display the specified package.
     */
    public function show(Package $package)
    {
        return view('student.packages.show', compact('package'));
    }
    
    /**
     * Display student's package credits.
     */
    public function credits()
    {
        $userId = auth()->id();
        $credits = PackageCredit::where('user_id', $userId)
            ->with('package')
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('student.packages.credits', compact('credits'));
    }
    
    /**
     * Display student's package orders.
     */
    public function orders()
    {
        $userId = auth()->id();
        $orders = PackageOrder::where('user_id', $userId)
            ->with(['items.package', 'payment'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('student.packages.orders', compact('orders'));
    }
    
    /**
     * Display details of a specific order.
     */
    public function showOrder(PackageOrder $order)
    {
        // Ensure the order belongs to the authenticated user
        if ($order->user_id !== auth()->id()) {
            return redirect()->route('student.packages.orders')
                ->with('error', 'You do not have permission to view this order.');
        }
        
        $order->load(['items.package', 'payment']);
        
        // Get credits generated from this order (assuming they're linked by order_id)
        $credits = PackageCredit::where('order_id', $order->id)->get();
        
        // Get bookings made with these credits
        $bookings = Booking::whereIn('package_credit_id', $credits->pluck('id'))
            ->with(['instructor.user', 'service'])
            ->orderBy('date', 'desc')
            ->get();
            
        return view('student.packages.order-show', compact('order', 'credits', 'bookings'));
    }
    
    /**
     * Redeem a package credit for a booking.
     */
    public function redeem(PackageCredit $packageCredit)
    {
        // Redemption logic here
        
        return redirect()->back()->with('success', 'Credit redeemed successfully.');
    }
}