<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\PackageCredit;
use App\Models\PackageOrder;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    /**
     * Display a listing of packages available.
     */
    public function index()
    {
        $packages = Package::where('active', true)->get();
        return view('instructor.packages.index', compact('packages'));
    }

    /**
     * Display the specified package.
     */
    public function show(Package $package)
    {
        return view('instructor.packages.show', compact('package'));
    }

    /**
     * Display list of orders related to instructor's students.
     */
    public function orders(Request $request)
    {
        $instructor = auth()->user()->instructor;
        
        // Get student IDs who have bookings with this instructor
        $studentIds = $instructor->bookings()->pluck('user_id')->unique();
        
        // Get orders for these students
        $orders = PackageOrder::whereIn('user_id', $studentIds)
            ->with(['user', 'items.package'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('instructor.packages.orders', compact('orders'));
    }

    /**
     * Display a specific order.
     */
    public function showOrder(PackageOrder $order)
    {
        $instructor = auth()->user()->instructor;
        
        // Check if this order belongs to one of the instructor's students
        $studentIds = $instructor->bookings()->pluck('user_id')->unique();
        
        if (!$studentIds->contains($order->user_id)) {
            return redirect()->route('instructor.packages.orders')
                ->with('error', 'You do not have permission to view this order.');
        }
        
        $order->load(['user', 'items.package']);
        $credits = PackageCredit::where('order_id', $order->id)->get();
        
        return view('instructor.packages.order-show', compact('order', 'credits'));
    }

    /**
     * Display package credits for instructor's students.
     */
    public function credits(Request $request)
    {
        $instructor = auth()->user()->instructor;
        
        // Get student IDs who have bookings with this instructor
        $studentIds = $instructor->bookings()->pluck('user_id')->unique();
        
        // Get credits for these students
        $credits = PackageCredit::whereIn('user_id', $studentIds)
            ->with(['user', 'package'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        return view('instructor.packages.credits', compact('credits'));
    }

    /**
     * Display a specific student's credits.
     */
    public function studentCredits($studentId)
    {
        $instructor = auth()->user()->instructor;
        
        // Check if this student has bookings with the instructor
        $hasBookings = $instructor->bookings()->where('user_id', $studentId)->exists();
        
        if (!$hasBookings) {
            return redirect()->route('instructor.clients.index')
                ->with('error', 'You do not have permission to view this student\'s credits.');
        }
        
        $student = \App\Models\User::findOrFail($studentId);
        $credits = PackageCredit::where('user_id', $studentId)
            ->with('package')
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('instructor.packages.student-credits', compact('student', 'credits'));
    }

    /**
     * Display upcoming lessons scheduled using package credits.
     */
    public function packageLessons()
    {
        $instructor = auth()->user()->instructor;
        
        // Get bookings that used package credits
        $bookings = $instructor->bookings()
            ->where('package_credit_id', '!=', null)
            ->with(['user', 'service', 'packageCredit.package'])
            ->orderBy('date', 'asc')
            ->paginate(15);
            
        return view('instructor.packages.lessons', compact('bookings'));
    }
}