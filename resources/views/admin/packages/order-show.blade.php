@extends('layouts.admin')

@section('title', 'Order Details')

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold m-0">
                <span class="text-muted fw-light">Admin / Packages / Orders /</span> #{{ $order->id }}
            </h4>
            <div>
                <a href="{{ route('admin.packages.orders') }}" class="btn btn-outline-secondary">
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
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Order #{{ $order->id }}</h5>
                        <span class="badge bg-{{ $order->status === 'completed' ? 'success' : ($order->status === 'cancelled' ? 'danger' : 'warning') }} rounded-pill">
                            {{ ucfirst($order->status) }}
                        </span>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <h6 class="small text-muted mb-1">Order Date</h6>
                                    <p class="mb-0">{{ $order->created_at->format('F j, Y, g:i a') }}</p>
                                </div>
                                <div>
                                    <h6 class="small text-muted mb-1">Order Total</h6>
                                    <p class="mb-0 fw-semibold">${{ number_format($order->total, 2) }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <h6 class="small text-muted mb-1">Last Update</h6>
                                    <p class="mb-0">{{ $order->updated_at->format('F j, Y, g:i a') }}</p>
                                </div>
                                <div>
                                    <h6 class="small text-muted mb-1">Payment Method</h6>
                                    <p class="mb-0">{{ $order->payment ? $order->payment->method : 'Not recorded' }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="divider">
                            <div class="divider-text">Order Items</div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Package</th>
                                        <th class="text-center">Quantity</th>
                                        <th class="text-end">Unit Price</th>
                                        <th class="text-end">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($order->items as $item)
                                    <tr>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <a href="{{ route('admin.packages.show', $item->package_id) }}" class="fw-semibold text-body">
                                                    {{ $item->package->name }}
                                                </a>
                                                <small class="text-muted">
                                                    {{ $item->package->lessons }} lessons x {{ $item->package->duration }} minutes
                                                </small>
                                            </div>
                                        </td>
                                        <td class="text-center">{{ $item->quantity }}</td>
                                        <td class="text-end">${{ number_format($item->price, 2) }}</td>
                                        <td class="text-end">${{ number_format($item->price * $item->quantity, 2) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3" class="text-end fw-semibold">Subtotal:</td>
                                        <td class="text-end">${{ number_format($order->subtotal, 2) }}</td>
                                    </tr>
                                    @if($order->discount > 0)
                                    <tr>
                                        <td colspan="3" class="text-end fw-semibold">Discount:</td>
                                        <td class="text-end">-${{ number_format($order->discount, 2) }}</td>
                                    </tr>
                                    @endif
                                    <tr>
                                        <td colspan="3" class="text-end fw-semibold">Total:</td>
                                        <td class="text-end fw-bold">${{ number_format($order->total, 2) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        @if($order->notes)
                        <div class="mt-3">
                            <h6>Order Notes</h6>
                            <p class="mb-0">{{ $order->notes }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                @if($order->payment)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Payment Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <h6 class="small text-muted mb-1">Payment ID</h6>
                                    <p class="mb-0">{{ $order->payment->transaction_id }}</p>
                                </div>
                                <div>
                                    <h6 class="small text-muted mb-1">Payment Method</h6>
                                    <p class="mb-0">{{ $order->payment->method }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <h6 class="small text-muted mb-1">Payment Date</h6>
                                    <p class="mb-0">{{ $order->payment->created_at->format('F j, Y, g:i a') }}</p>
                                </div>
                                <div>
                                    <h6 class="small text-muted mb-1">Amount</h6>
                                    <p class="mb-0 fw-semibold">${{ number_format($order->payment->amount, 2) }}</p>
                                </div>
                            </div>
                        </div>
                        
                        @if($order->payment->status !== 'completed')
                        <div class="alert alert-warning mt-3 mb-0">
                            <div class="d-flex align-items-center">
                                <i class="bx bx-error-circle fs-4 me-2"></i>
                                <div>
                                    <h6 class="alert-heading mb-1">Payment Status: {{ ucfirst($order->payment->status) }}</h6>
                                    <p class="mb-0">There may be an issue with this payment. Please check your payment processor dashboard for more details.</p>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                @endif
            </div>

            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Customer Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar avatar-md me-2">
                                <span class="avatar-initial rounded-circle bg-primary">
                                    {{ substr($order->user->name, 0, 1) }}
                                </span>
                            </div>
                            <div>
                                <h6 class="mb-0">{{ $order->user->name }}</h6>
                                <small class="text-muted">{{ $order->user->email }}</small>
                            </div>
                        </div>
                        
                        <div class="mt-3">
                            <a href="{{ route('admin.clients.show', $order->user_id) }}" class="btn btn-outline-primary btn-sm w-100">
                                <i class="bx bx-user me-1"></i> View Customer Profile
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Order Status</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.packages.orders.update-status', $order->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <div class="mb-3">
                                <label class="form-label">Current Status</label>
                                <div>
                                    <span class="badge bg-{{ $order->status === 'completed' ? 'success' : ($order->status === 'cancelled' ? 'danger' : 'warning') }} rounded-pill">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="status" class="form-label">Update Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                            </div>
                            
                            <div class="alert alert-warning mb-3">
                                <div class="d-flex">
                                    <i class="bx bx-info-circle me-2 mt-1"></i>
                                    <div>
                                        <h6 class="alert-heading fw-bold mb-1">Important</h6>
                                        <p class="mb-0">Changing the order status may affect package credits and student access to lessons.</p>
                                    </div>
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100">Update Status</button>
                        </form>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Package Credits</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span>Total Credits Added:</span>
                            <span class="badge bg-primary rounded-pill">
                                {{ $order->items->sum(function($item) { return $item->quantity * $item->package->lessons; }) }} lessons
                            </span>
                        </div>
                        
                        <a href="/" class="btn btn-outline-primary btn-sm w-100">
                            <i class="bx bx-credit-card me-1"></i> View Customer Credits
                        </a>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="#" class="btn btn-outline-secondary btn-sm">
                                <i class="bx bx-printer me-1"></i> Print Invoice
                            </a>
                            <a href="mailto:{{ $order->user->email }}" class="btn btn-outline-primary btn-sm">
                                <i class="bx bx-envelope me-1"></i> Email Customer
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection