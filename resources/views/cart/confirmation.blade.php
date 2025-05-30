@extends('layouts.app')

@section('content')
<div class="container py-4 py-md-5">
    <div class="row justify-content-center mb-4">
        <div class="col-12 col-lg-8 text-center">
            <h1 class="text-primary fw-bold mb-2 mb-md-3">Order Confirmation</h1>
            <p class="text-secondary">Thank you for your purchase with Wave Driving School.</p>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-12 col-lg-9">
            <!-- Success Message -->
            <div class="card shadow-sm rounded-4 border-0 bg-success bg-opacity-10 mb-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-3">
                            <i class="fas fa-check-circle text-success fa-3x"></i>
                        </div>
                        <div>
                            <h4 class="mb-1 fw-semibold">Thank you for your order!</h4>
                            <p class="mb-0">Your order has been received and is being processed. You will receive a confirmation email shortly.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Details -->
            <div class="card shadow-sm rounded-4 border-0 mb-4">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="mb-0 fw-semibold">Order Details</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <tbody>
                                <tr>
                                    <th class="ps-4 bg-light" style="width: 35%;">Order Number:</th>
                                    <td class="ps-3">#{{ $order->id }}</td>
                                </tr>
                                <tr>
                                    <th class="ps-4 bg-light">Order Date:</th>
                                    <td class="ps-3">{{ $order->created_at->format('F j, Y, g:i a') }}</td>
                                </tr>
                                <tr>
                                    <th class="ps-4 bg-light">Status:</th>
                                    <td class="ps-3">
                                        <span class="badge bg-success rounded-pill px-3 py-2">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="ps-4 bg-light">Total Amount:</th>
                                    <td class="ps-3 fw-semibold">${{ number_format($order->total_amount, 2) }}</td>
                                </tr>
                                <tr>
                                    <th class="ps-4 bg-light">Payment Method:</th>
                                    <td class="ps-3">
                                        @if($order->payment->payment_method == 'paypal')
                                            <i class="fab fa-paypal text-primary me-1"></i>
                                        @elseif($order->payment->payment_method == 'credit_card')
                                            <i class="fas fa-credit-card text-primary me-1"></i>
                                        @endif
                                        {{ ucfirst($order->payment->payment_method) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Order Items -->
            <div class="card shadow-sm rounded-4 border-0 mb-4">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="mb-0 fw-semibold">Purchased Packages</h5>
                </div>
                <div class="card-body p-0">
                    <!-- Table for medium screens and up -->
                    <div class="table-responsive d-none d-md-block">
                        <table class="table mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">Package</th>
                                    <th>Quantity</th>
                                    <th>Unit Price</th>
                                    <th>GST</th>
                                    <th class="text-end pe-4">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                <tr>
                                    <td class="ps-4 fw-semibold">{{ $item->package->name }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>${{ number_format($item->unit_price, 2) }}</td>
                                    <td>${{ number_format($item->gst * $item->quantity, 2) }}</td>
                                    <td class="text-end pe-4 fw-semibold">${{ number_format($item->total, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-group-divider">
                                <tr>
                                    <td colspan="4" class="text-end pe-4 fw-bold">Total:</td>
                                    <td class="text-end pe-4 fw-bold">${{ number_format($order->total_amount, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    
                    <!-- Mobile view for small screens -->
                    <div class="d-md-none p-3">
                        <div class="list-group list-group-flush">
                            @foreach($order->items as $item)
                            <div class="list-group-item px-0 py-3 border-top-0 border-start-0 border-end-0">
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="fw-semibold">{{ $item->package->name }}</span>
                                    <span class="fw-semibold">${{ number_format($item->total, 2) }}</span>
                                </div>
                                <div class="row text-secondary small">
                                    <div class="col-6">
                                        <div>Quantity: {{ $item->quantity }}</div>
                                        <div>Unit: ${{ number_format($item->unit_price, 2) }}</div>
                                    </div>
                                    <div class="col-6 text-end">
                                        <div>GST: ${{ number_format($item->gst * $item->quantity, 2) }}</div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                            <div class="list-group-item px-0 py-3 border-top-0 border-start-0 border-end-0 bg-light">
                                <div class="d-flex justify-content-between">
                                    <span class="fw-bold">Total:</span>
                                    <span class="fw-bold">${{ number_format($order->total_amount, 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Next Steps -->
            <div class="card shadow-sm rounded-4 border-0 mb-4">
                <div class="card-body p-4">
                    <h5 class="mb-3 fw-semibold">What's Next?</h5>
                    <div class="d-flex mb-3">
                        <div class="flex-shrink-0 me-3">
                            <div class="bg-primary bg-opacity-10 rounded-circle p-2">
                                <i class="fas fa-envelope text-primary"></i>
                            </div>
                        </div>
                        <div>
                            <h6 class="fw-semibold mb-1">Check Your Email</h6>
                            <p class="text-secondary mb-0">We've sent a confirmation to your email with all the details of your order.</p>
                        </div>
                    </div>
                    <div class="d-flex">
                        <div class="flex-shrink-0 me-3">
                            <div class="bg-primary bg-opacity-10 rounded-circle p-2">
                                <i class="fas fa-calendar-alt text-primary"></i>
                            </div>
                        </div>
                        <div>
                            <h6 class="fw-semibold mb-1">Book Your Lessons</h6>
                            <p class="text-secondary mb-0">Use your purchased lessons to book driving sessions with our instructors.</p>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-white p-4 d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                    <a href="{{ route('student.dashboard') }}" class="btn btn-primary rounded-pill px-4 py-2 w-100 w-md-auto">
                        <i class="fas fa-home me-2"></i> Go to Dashboard
                    </a>
                    <a href="{{ route('student.packages.credits') }}" class="btn btn-outline-primary rounded-pill px-4 py-2 w-100 w-md-auto">
                        <i class="fas fa-ticket-alt me-2"></i> View My Lesson Credits
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection