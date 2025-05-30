<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Package;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    /**
     * Add item to cart
     */
    public function addToCart(Request $request)
    {
        $validated = $request->validate([
            'package_id' => 'required|exists:packages,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $package = Package::findOrFail($validated['package_id']);
        
        // Initialize cart if it doesn't exist
        if (!Session::has('cart')) {
            Session::put('cart', []);
        }
        
        $cart = Session::get('cart');
        
        // Check if package already exists in cart
        $found = false;
        foreach ($cart as &$item) {
            if ($item['id'] == $package->id) {
                $item['quantity'] += $validated['quantity'];
                $found = true;
                break;
            }
        }
        
        // Add new item if not found
        if (!$found) {
            $cart[] = [
                'id' => $package->id,
                'name' => $package->name,
                'price' => $package->price,
                'quantity' => $validated['quantity'],
                'gst' => $package->price * 0.1, // Assuming 10% GST
                'total' => $package->price * $validated['quantity'] * 1.1 // Price with GST
            ];
        }
        
        Session::put('cart', $cart);
        
        return redirect()->route('cart.show')->with('success', 'Package added to cart');
    }
    
    /**
     * Display cart contents
     */
    public function show()
    {
        $cart = Session::get('cart', []);
        $total = 0;
        
        foreach ($cart as $item) {
            $total += $item['total'];
        }
        
        return view('cart.show', compact('cart', 'total'));
    }
    
    /**
     * Update cart quantities
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'quantities' => 'required|array',
            'quantities.*' => 'integer|min:0',
        ]);
        
        $cart = Session::get('cart', []);
        $updatedCart = [];
        
        foreach ($cart as $index => $item) {
            if (isset($validated['quantities'][$index]) && $validated['quantities'][$index] > 0) {
                $item['quantity'] = $validated['quantities'][$index];
                $item['total'] = $item['price'] * $item['quantity'] * 1.1; // Update total with GST
                $updatedCart[] = $item;
            }
        }
        
        Session::put('cart', $updatedCart);
        
        return redirect()->route('cart.show')->with('success', 'Cart updated');
    }
    
    /**
     * Remove item from cart
     */
    public function remove($index)
    {
        $cart = Session::get('cart', []);
        
        if (isset($cart[$index])) {
            array_splice($cart, $index, 1);
            Session::put('cart', $cart);
        }
        
        return redirect()->route('cart.show')->with('success', 'Item removed from cart');
    }
    
    /**
     * Proceed to checkout
     */
    public function checkout()
    {
        if (!Session::has('cart') || count(Session::get('cart')) === 0) {
            return redirect()->route('packages.index')->with('error', 'Your cart is empty');
        }
        
        // If user is not logged in, redirect to login
        if (!Auth::check()) {
            Session::put('url.intended', route('cart.details'));
            return redirect()->route('login')->with('info', 'Please log in or register to complete your purchase');
        }
        
        return redirect()->route('cart.details');
    }
    
    /**
     * Display checkout details form
     */
    public function details()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please log in to continue');
        }
        
        if (!Session::has('cart') || count(Session::get('cart')) === 0) {
            return redirect()->route('packages.index')->with('error', 'Your cart is empty');
        }
        
        $suburbs = \App\Models\Suburb::where('active', true)->orderBy('name')->get();
        
        return view('cart.details', compact('suburbs'));
    }
    
    /**
     * Save checkout details
     */
    public function saveDetails(Request $request)
    {
        $validated = $request->validate([
            'booking_for' => 'required|in:self,other',
            'address' => 'required|string|max:255',
            'other_name' => 'required_if:booking_for,other|string|max:255|nullable',
            'other_email' => 'required_if:booking_for,other|email|max:255|nullable',
            'other_phone' => 'required_if:booking_for,other|string|max:20|nullable',
        ]);
        
        // Store details in session
        foreach ($validated as $key => $value) {
            Session::put('package_booking.' . $key, $value);
        }
        
        return redirect()->route('cart.payment');
    }
    
    /**
     * Display payment page
     */
    public function payment()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please log in to continue');
        }
        
        if (!Session::has('cart') || count(Session::get('cart')) === 0) {
            return redirect()->route('packages.index')->with('error', 'Your cart is empty');
        }
        
        $cart = Session::get('cart', []);
        $total = 0;
        
        foreach ($cart as $item) {
            $total += $item['total'];
        }
        
        return view('cart.payment', compact('cart', 'total'));
    }
    
    /**
     * Process payment
     */
    public function processPayment(Request $request)
    {
        $validated = $request->validate([
            'payment_method' => 'required|in:credit_card,paypal',
            'stripe_token' => 'required_if:payment_method,credit_card',
        ]);
        
        try {
            \DB::beginTransaction();
            
            // Create package order
            $order = new \App\Models\PackageOrder();
            $order->user_id = Auth::id();
            $order->total_amount = 0; // Will be calculated below
            $order->status = 'pending';
            $order->booking_for = Session::get('package_booking.booking_for', 'self');
            $order->other_name = Session::get('package_booking.other_name');
            $order->other_email = Session::get('package_booking.other_email');
            $order->other_phone = Session::get('package_booking.other_phone');
            $order->address = Session::get('package_booking.address');
            $order->save();
            
            // Save order items
            $cart = Session::get('cart', []);
            $totalAmount = 0;
            
            foreach ($cart as $item) {
                $orderItem = new \App\Models\PackageOrderItem();
                $orderItem->order_id = $order->id;
                $orderItem->package_id = $item['id'];
                $orderItem->quantity = $item['quantity'];
                $orderItem->unit_price = $item['price'];
                $orderItem->gst = $item['gst'];
                $orderItem->total = $item['total'];
                $orderItem->save();
                
                $totalAmount += $item['total'];
            }
            
            // Update order total
            $order->total_amount = $totalAmount;
            $order->save();
            
            // Process payment
            $payment = new \App\Models\Payment();
            $payment->order_id = $order->id;
            $payment->user_id = Auth::id();
            $payment->amount = $totalAmount;
            $payment->payment_method = $validated['payment_method'];
            $payment->transaction_id = 'PKG-' . time() . '-' . $order->id;
            $payment->status = 'completed';
            $payment->payment_date = now();
            $payment->save();
            
            // Send notifications
            // TODO: Create and send notifications
            
            \DB::commit();
            
            // Clear cart and booking details
            Session::forget(['cart', 'package_booking']);
            Session::put('package_order.completed_id', $order->id);
            
            return redirect()->route('cart.confirmation', $order->id);
            
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Package order failed: ' . $e->getMessage());
            return back()->with('error', 'Order failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Display order confirmation
     */
    public function confirmation($orderId = null)
    {
        if (!$orderId) {
            $orderId = Session::get('package_order.completed_id');
        }
        
        if (!$orderId) {
            return redirect()->route('packages.index')
                ->with('error', 'No order found.');
        }
        
        $order = \App\Models\PackageOrder::with(['user', 'items.package', 'payment'])
            ->findOrFail($orderId);
        
        // Verify the order belongs to the current user
        if ($order->user_id !== Auth::id()) {
            return redirect()->route('packages.index')
                ->with('error', 'Unauthorized access.');
        }
        
        Session::forget('package_order.completed_id');
        
        return view('cart.confirmation', compact('order'));
    }
}