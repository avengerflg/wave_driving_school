@extends('layouts.student')

@section('title', 'Order Details')

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Breadcrumb -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold m-0">
                <span class="text-muted fw-light">Student / Packages / Orders /</span> Order #{{ $order->id }}
            </h4>
            <div>
                <a href="{{ route('student.packages.orders') }}" class="btn btn-outline-secondary">
                    <i class="bx bx-arrow-back me-1"></i> Back to Orders
                </a>
            </div>
        </div>

        <div class="row">
            <!-- Order summary -->
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Order Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-center mb-3">
                            <div class="rounded-circle p-3 bg-label-{{ $order->status === 'completed' ? 'success' : ($order->status === 'cancelled' ? 'danger' : 'warning') }}">
                                <i class="bx bx-{{ $order->status === 'completed' ? 'check-circle' : ($order->status === 'cancelled' ? 'x-circle' : 'time') }} fs-3 text-{{ $order->status === 'completed' ? 'success' : ($order->status === 'cancelled' ? 'danger' : 'warning') }}"></i>
                            </div>
                        </div>

                        <ul class="list-unstyled mb-4">
                            <li class="mb-2">
                                <span class="fw-semibold me-1">Order Number:</span>
                                <span>#{{ $order->id }}</span>
                            </li>
                            <li class="mb-2">
                                <span class="fw-semibold me-1">Date:</span>
                                <span>{{ $order->created_at->format('M d, Y g:i A') }}</span>
                            </li>
                            <li class="mb-2">
                                <span class="fw-semibold me-1">Status:</span>
                                <span class="badge bg-{{ $order->status === 'completed' ? 'success' : ($order->status === 'cancelled' ? 'danger' : 'warning') }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </li>
                            <li class="mb-2">
                                <span class="fw-semibold me-1">Total Amount:</span>
                                <span>${{ number_format($order->total_amount, 2) }}</span>
                            </li>
                            @if($order->payment)
                            <li class="mb-2">
                                <span class="fw-semibold me-1">Payment Method:</span>
                                <span>{{ ucfirst($order->payment->payment_method ?? 'Credit Card') }}</span>
                            </li>
                            <li class="mb-2">
                                <span class="fw-semibold me-1">Transaction ID:</span>
                                <span>{{ $order->payment->transaction_id ?? 'N/A' }}</span>
                            </li>
                            @endif
                        </ul>

                        @if($order->booking_for === 'other')
                        <div class="divider">
                            <div class="divider-text">Recipient Information</div>
                        </div>
                        
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2">
                                <span class="fw-semibold me-1">Recipient:</span>
                                <span>{{ $order->other_name }}</span>
                            </li>
                            @if($order->other_email)
                            <li class="mb-2">
                                <span class="fw-semibold me-1">Email:</span>
                                <span>{{ $order->other_email }}</span>
                            </li>
                            @endif
                            @if($order->other_phone)
                            <li class="mb-2">
                                <span class="fw-semibold me-1">Phone:</span>
                                <span>{{ $order->other_phone }}</span>
                            </li>
                            @endif
                            @if($order->address)
                            <li class="mb-2">
                                <span class="fw-semibold me-1">Address:</span>
                                <span>{{ $order->address }}</span>
                            </li>
                            @endif
                        </ul>
                        @endif

                        @if($order->status === 'pending')
                        <div class="alert alert-warning mt-4 mb-0">
                            <div class="d-flex">
                                <i class="bx bx-info-circle me-2 mt-1"></i>
                                <div>
                                    <h6 class="alert-heading mb-1">Payment Processing</h6>
                                    <p class="mb-0">Your payment is being processed. Your package credits will be available once payment is confirmed.</p>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Order details -->
            <div class="col-md-8">
                <!-- Purchased packages -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Purchased Packages</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Package</th>
                                    <th>Lessons</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                <tr>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <h6 class="mb-0">{{ $item->package->name ?? 'Unknown Package' }}</h6>
                                            <small class="text-muted">{{ Str::limit($item->package->description ?? '', 60) }}</small>
                                        </div>
                                    </td>
                                    <td>{{ $item->package->lessons ?? '0' }}</td>
                                    <td>${{ number_format($item->price, 2) }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>${{ number_format($item->price * $item->quantity, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-border-bottom-0">
                                <tr>
                                    <td colspan="4" class="text-end fw-bold">Total:</td>
                                    <td class="fw-bold">${{ number_format($order->total_amount, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <!-- Package credits -->
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Package Credits</h5>
                        <a href="{{ route('student.packages.credits') }}" class="btn btn-sm btn-outline-primary">
                            View All My Credits
                        </a>
                    </div>
                    
                    @if(count($credits) > 0)
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Package</th>
                                    <th>Total</th>
                                    <th>Remaining</th>
                                    <th>Status</th>
                                    <th>Expires</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($credits as $credit)
                                <tr>
                                    <td>{{ $credit->package->name ?? 'Unknown Package' }}</td>
                                    <td>{{ $credit->total }} lessons</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="progress w-100 me-3" style="height: 8px;">
                                                <div class="progress-bar bg-success" 
                                                    style="width: {{ ($credit->remaining / $credit->total) * 100 }}%" 
                                                    role="progressbar"></div>
                                            </div>
                                            <span>{{ $credit->remaining }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $credit->status === 'active' ? 'success' : ($credit->status === 'expired' ? 'warning' : 'secondary') }}">
                                            {{ ucfirst($credit->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($credit->expires_at)
                                            {{ $credit->expires_at->format('M d, Y') }}
                                            @if($credit->expires_at->isPast())
                                                <span class="badge bg-danger ms-1">Expired</span>
                                            @elseif($credit->expires_at->diffInDays(now()) < 30)
                                                <span class="badge bg-warning ms-1">Soon</span>
                                            @endif
                                        @else
                                            No expiry
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="card-body py-5 text-center">
                        <i class="bx bx-credit-card-front text-secondary mb-2" style="font-size: 3rem;"></i>
                        <h6 class="mb-1">No Credits Available</h6>
                        <p class="text-muted mb-0">
                            @if($order->status === 'completed')
                                All credits from this order have been used.
                            @else
                                Credits will be available when order status is completed.
                            @endif
                        </p>
                    </div>
                    @endif
                </div>

                <!-- Lessons booked with these credits -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Lessons Booked With These Credits</h5>
                    </div>
                    
                    @if(count($bookings) > 0)
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Date & Time</th>
                                    <th>Instructor</th>
                                    <th>Service</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($bookings as $booking)
                                <tr>
                                    <td>
                                        {{ $booking->date->format('M d, Y') }}<br>
                                        <small class="text-muted">
                                            {{ $booking->start_time->format('g:i A') }} - 
                                            {{ $booking->end_time->format('g:i A') }}
                                        </small>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm me-2">
                                                <span class="avatar-initial rounded-circle bg-label-primary">
                                                    {{ substr($booking->instructor->user->name ?? 'I', 0, 1) }}
                                                </span>
                                            </div>
                                            <span>{{ $booking->instructor->user->name ?? 'Unknown Instructor' }}</span>
                                        </div>
                                    </td>
                                    <td>{{ $booking->service->name ?? 'Unknown Service' }}</td>
                                    <td>
                                        <span class="badge bg-{{ $booking->status === 'completed' ? 'success' : ($booking->status === 'cancelled' ? 'danger' : ($booking->status === 'confirmed' ? 'info' : 'warning')) }}">
                                            {{ ucfirst($booking->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('student.bookings.show', $booking->id) }}" class="btn btn-sm btn-primary">
                                            <i class="bx bx-show"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="card-body py-5 text-center">
                        <i class="bx bx-calendar-x text-secondary mb-2" style="font-size: 3rem;"></i>
                        <h6 class="mb-1">No Lessons Booked Yet</h6>
                        <p class="text-muted mb-0">You haven't booked any lessons using these package credits yet.</p>
                        <a href="{{ route('booking.index') }}" class="btn btn-primary mt-3">
                            <i class="bx bx-calendar-plus me-1"></i> Book a Lesson
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection