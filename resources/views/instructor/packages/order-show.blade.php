@extends('layouts.instructor')

@section('title', 'Order Details')

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Breadcrumb and actions header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold m-0">
                <span class="text-muted fw-light">Instructor / Packages / Orders /</span> Order #{{ $order->id }}
            </h4>
            <div>
                <a href="{{ route('instructor.packages.orders') }}" class="btn btn-outline-secondary">
                    <i class="bx bx-arrow-back me-1"></i> Back to Orders
                </a>
            </div>
        </div>

        @if(session('success'))
        <div class="alert alert-success alert-dismissible mb-4" role="alert">
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger alert-dismissible mb-4" role="alert">
            <div>{{ session('error') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <div class="row">
            <!-- Order summary card -->
            <div class="col-xl-4 col-lg-5 col-md-12 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Order Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="info-container">
                            <div class="d-flex justify-content-center mb-4">
                                <div class="rounded p-3 bg-label-{{ $order->status === 'completed' ? 'success' : ($order->status === 'cancelled' ? 'danger' : 'warning') }}">
                                    <span class="badge bg-{{ $order->status === 'completed' ? 'success' : ($order->status === 'cancelled' ? 'danger' : 'warning') }} p-2">
                                        <i class="bx bx-{{ $order->status === 'completed' ? 'check-circle' : ($order->status === 'cancelled' ? 'x-circle' : 'time') }} me-1"></i>
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </div>
                            </div>
                            
                            <ul class="list-unstyled">
                                <li class="mb-3">
                                    <span class="fw-bold me-2">Order ID:</span>
                                    <span>#{{ $order->id }}</span>
                                </li>
                                <li class="mb-3">
                                    <span class="fw-bold me-2">Date:</span>
                                    <span>{{ $order->created_at->format('M d, Y g:i A') }}</span>
                                </li>
                                <li class="mb-3">
                                    <span class="fw-bold me-2">Payment Method:</span>
                                    <span>{{ ucfirst($order->payment_method ?? 'Credit Card') }}</span>
                                </li>
                                <li class="mb-3">
                                    <span class="fw-bold me-2">Total Amount:</span>
                                    <span class="fw-semibold">${{ number_format($order->total, 2) }}</span>
                                </li>
                                <li class="mb-3">
                                    <span class="fw-bold me-2">Items:</span>
                                    <span>{{ $order->items->count() }}</span>
                                </li>
                                @if($order->transaction_id)
                                <li class="mb-3">
                                    <span class="fw-bold me-2">Transaction ID:</span>
                                    <span>{{ $order->transaction_id }}</span>
                                </li>
                                @endif
                            </ul>
                            
                            <div class="divider">
                                <div class="divider-text">Customer Information</div>
                            </div>
                            
                            <div class="d-flex align-items-center mb-3">
                                <div class="avatar avatar-md me-3">
                                    <span class="avatar-initial rounded-circle bg-label-primary">
                                        {{ substr($order->user->name ?? 'U', 0, 1) }}
                                    </span>
                                </div>
                                <div>
                                    <h6 class="mb-0">{{ $order->user->name ?? 'Unknown User' }}</h6>
                                    <small class="text-muted">{{ $order->user->email ?? 'No email' }}</small>
                                </div>
                            </div>
                            
                            <div class="mt-4">
                                <a href="{{ route('instructor.clients.show', $order->user_id) }}" class="btn btn-primary d-grid w-100">
                                    <i class="bx bx-user me-1"></i> View Student Profile
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Order details -->
            <div class="col-xl-8 col-lg-7 col-md-12">
                <!-- Purchased packages -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Purchased Packages</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover">
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
                                            <small class="text-muted">{{ Str::limit($item->package->description ?? '', 50) }}</small>
                                        </div>
                                    </td>
                                    <td>{{ $item->package->lessons ?? 0 }}</td>
                                    <td>${{ number_format($item->price, 2) }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>${{ number_format($item->price * $item->quantity, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4" class="text-end fw-bold">Subtotal:</td>
                                    <td>${{ number_format($order->subtotal ?? $order->total, 2) }}</td>
                                </tr>
                                @if(isset($order->discount) && $order->discount > 0)
                                <tr>
                                    <td colspan="4" class="text-end fw-bold">Discount:</td>
                                    <td>-${{ number_format($order->discount, 2) }}</td>
                                </tr>
                                @endif
                                @if(isset($order->tax) && $order->tax > 0)
                                <tr>
                                    <td colspan="4" class="text-end fw-bold">Tax:</td>
                                    <td>${{ number_format($order->tax, 2) }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <td colspan="4" class="text-end fw-bold">Total:</td>
                                    <td class="fw-bold">${{ number_format($order->total, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                
                <!-- Credits generated -->
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Package Credits</h5>
                        <span class="badge bg-label-info">{{ count($credits) }} Credits Generated</span>
                    </div>
                    @if(count($credits) > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Package</th>
                                    <th>Total Lessons</th>
                                    <th>Remaining</th>
                                    <th>Status</th>
                                    <th>Expiry</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($credits as $credit)
                                <tr>
                                    <td>{{ $credit->package->name ?? 'Unknown Package' }}</td>
                                    <td>{{ $credit->total }}</td>
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
                                        <span class="badge bg-{{ $credit->status === 'active' ? 'success' : ($credit->status === 'used' ? 'info' : ($credit->status === 'expired' ? 'warning' : 'secondary')) }}">
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
                    <div class="card-body">
                        <div class="d-flex flex-column align-items-center py-4">
                            <i class="bx bx-credit-card-front text-secondary mb-2" style="font-size: 3rem;"></i>
                            <h6 class="mb-1">No Credits Generated</h6>
                            <p class="text-muted">This order has not generated any package credits yet.</p>
                        </div>
                    </div>
                    @endif
                </div>
                
                <!-- Order notes -->
                @if($order->notes)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Order Notes</h5>
                    </div>
                    <div class="card-body">
                        <div class="border p-3 rounded bg-light">
                            {{ $order->notes }}
                        </div>
                    </div>
                </div>
                @endif
                
                <!-- Bookings made with these credits -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Lessons Booked With These Credits</h5>
                    </div>
                    <div class="card-body">
                        @php
                            $packageBookings = \App\Models\Booking::whereIn('package_credit_id', $credits->pluck('id'))
                                ->with(['user', 'service'])
                                ->orderBy('date', 'asc')
                                ->get();
                        @endphp
                        
                        @if(count($packageBookings) > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Booking ID</th>
                                            <th>Date & Time</th>
                                            <th>Service</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($packageBookings as $booking)
                                        <tr>
                                            <td>#{{ $booking->id }}</td>
                                            <td>
                                                {{ $booking->date->format('M d, Y') }}<br>
                                                <small class="text-muted">
                                                    {{ $booking->start_time->format('g:i A') }} - 
                                                    {{ $booking->end_time->format('g:i A') }}
                                                </small>
                                            </td>
                                            <td>{{ $booking->service->name ?? 'Unknown Service' }}</td>
                                            <td>
                                                <span class="badge bg-{{ $booking->status === 'completed' ? 'success' : ($booking->status === 'cancelled' ? 'danger' : ($booking->status === 'confirmed' ? 'info' : 'warning')) }}">
                                                    {{ ucfirst($booking->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <a href="{{ route('instructor.bookings.show', $booking->id) }}" class="btn btn-sm btn-primary">
                                                    <i class="bx bx-show"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="d-flex flex-column align-items-center py-4">
                                <i class="bx bx-calendar-x text-secondary mb-2" style="font-size: 3rem;"></i>
                                <h6 class="mb-1">No Lessons Booked Yet</h6>
                                <p class="text-muted">The student has not yet booked any lessons using these package credits.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection