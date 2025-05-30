@extends('layouts.admin')

@section('title', 'Package Orders')

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold m-0">
                <span class="text-muted fw-light">Admin / Packages /</span> Orders
            </h4>
            <div>
                <a href="{{ route('admin.packages.index') }}" class="btn btn-outline-secondary">
                    <i class="bx bx-package me-1"></i> Manage Packages
                </a>
            </div>
        </div>

        @if(session('success'))
        <div class="alert alert-success alert-dismissible mb-4" role="alert">
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <!-- Stats Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body d-flex align-items-center">
                        <div class="avatar avatar-lg bg-primary me-3">
                            <span class="avatar-initial rounded"><i class="bx bx-shopping-bag"></i></span>
                        </div>
                        <div>
                            <h6 class="card-title mb-0">Total Orders</h6>
                            <h3 class="fw-bold mb-0">{{ $totalOrders ?? count($orders) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body d-flex align-items-center">
                        <div class="avatar avatar-lg bg-success me-3">
                            <span class="avatar-initial rounded"><i class="bx bx-check-circle"></i></span>
                        </div>
                        <div>
                            <h6 class="card-title mb-0">Completed</h6>
                            <h3 class="fw-bold mb-0">{{ $completedOrders ?? $orders->where('status', 'completed')->count() }}</h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body d-flex align-items-center">
                        <div class="avatar avatar-lg bg-warning me-3">
                            <span class="avatar-initial rounded"><i class="bx bx-time"></i></span>
                        </div>
                        <div>
                            <h6 class="card-title mb-0">Pending</h6>
                            <h3 class="fw-bold mb-0">{{ $pendingOrders ?? $orders->where('status', 'pending')->count() }}</h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body d-flex align-items-center">
                        <div class="avatar avatar-lg bg-danger me-3">
                            <span class="avatar-initial rounded"><i class="bx bx-x-circle"></i></span>
                        </div>
                        <div>
                            <h6 class="card-title mb-0">Cancelled</h6>
                            <h3 class="fw-bold mb-0">{{ $cancelledOrders ?? $orders->where('status', 'cancelled')->count() }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <form action="{{ route('admin.packages.orders') }}" method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label for="search" class="form-label">Search</label>
                        <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Order ID or customer name">
                    </div>
                    <div class="col-md-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">All Statuses</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="date_from" class="form-label">Date From</label>
                        <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="date_to" class="form-label">Date To</label>
                        <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-12 d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.packages.orders') }}" class="btn btn-outline-secondary">Reset</a>
                        <button type="submit" class="btn btn-primary">Filter</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Orders Table -->
        <div class="card">
            <div class="table-responsive text-nowrap">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Date</th>
                            <th>Packages</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse($orders as $order)
                        <tr>
                            <td><strong>#{{ $order->id }}</strong></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm me-2">
                                        <span class="avatar-initial rounded-circle bg-label-primary">
                                            {{ substr($order->user->name ?? 'U', 0, 1) }}
                                        </span>
                                    </div>
                                    <div>
                                        <a href="{{ route('admin.students.show', $order->user_id) }}">
                                            {{ $order->user->name ?? 'Unknown User' }}
                                        </a>
                                        <small class="d-block text-muted">{{ $order->user->email ?? '' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                {{ $order->created_at->format('M d, Y') }}
                                <small class="d-block text-muted">{{ $order->created_at->format('g:i A') }}</small>
                            </td>
                            <td>
                                <span class="badge bg-label-primary rounded-pill">
                                    {{ $order->items->count() }} items
                                </span>
                                <small class="d-block text-muted mt-1">
                                    {{ $order->items->sum('quantity') }} total quantities
                                </small>
                            </td>
                            <td>
                                <strong>${{ number_format($order->total, 2) }}</strong>
                            </td>
                            <td>
                                <span class="badge bg-{{ $order->status === 'completed' ? 'success' : ($order->status === 'cancelled' ? 'danger' : 'warning') }} rounded-pill">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <!-- Using admin.packages.orders.show route -->
                                        <a class="dropdown-item" href="{{ route('admin.packages.orders.show', $order->id) }}">
                                            <i class="bx bx-show me-1"></i> View Details
                                        </a>
                                        <a class="dropdown-item" href="{{ route('admin.students.show', $order->user_id) }}">
                                            <i class="bx bx-user me-1"></i> View Customer
                                        </a>
                                        <div class="dropdown-divider"></div>
                                        @if($order->status === 'pending')
                                        <!-- Using admin.packages.orders.update-status route -->
                                        <form action="{{ route('admin.packages.orders.update-status', $order->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="completed">
                                            <button type="submit" class="dropdown-item">
                                                <i class="bx bx-check-circle me-1"></i> Mark Completed
                                            </button>
                                        </form>
                                        <!-- Using admin.packages.orders.update-status route -->
                                        <form action="{{ route('admin.packages.orders.update-status', $order->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="cancelled">
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class="bx bx-x-circle me-1"></i> Cancel Order
                                            </button>
                                        </form>
                                        @elseif($order->status === 'cancelled')
                                        <!-- Using admin.packages.orders.update-status route -->
                                        <form action="{{ route('admin.packages.orders.update-status', $order->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="pending">
                                            <button type="submit" class="dropdown-item">
                                                <i class="bx bx-revision me-1"></i> Reactivate Order
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="bx bx-shopping-bag text-secondary mb-2" style="font-size: 3rem;"></i>
                                    <h5 class="mb-1">No Orders Found</h5>
                                    <p class="text-muted">No package orders match your search criteria</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($orders->count() > 0)
            <div class="card-footer">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        Showing {{ $orders->firstItem() ?? 0 }} to {{ $orders->lastItem() ?? 0 }} of {{ $orders->total() }} orders
                    </div>
                    <div>
                        {{ $orders->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
            @endif
        </div>
        
        <!-- Export/Reporting Options -->
        <div class="card mt-4">
            <div class="card-body">
                <h5 class="card-title">Export & Reports</h5>
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="d-grid">
                            <a href="{{ route('admin.packages.orders', ['export' => 'csv'] + request()->all()) }}" class="btn btn-outline-primary">
                                <i class="bx bx-download me-1"></i> Export Orders (CSV)
                            </a>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-grid">
                            <a href="{{ route('admin.packages.reports') }}" class="btn btn-outline-secondary">
                                <i class="bx bx-bar-chart-alt-2 me-1"></i> View Order Reports
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Confirm order status changes
        const statusForms = document.querySelectorAll('form[action*="update-status"]');
        statusForms.forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const status = this.querySelector('input[name="status"]').value;
                let message = '';
                
                if (status === 'completed') {
                    message = 'Are you sure you want to mark this order as completed? This will activate any package credits.';
                } else if (status === 'cancelled') {
                    message = 'Are you sure you want to cancel this order? This may affect any associated credits.';
                } else {
                    message = 'Are you sure you want to change the status of this order?';
                }
                
                if (confirm(message)) {
                    this.submit();
                }
            });
        });
    });
</script>
@endsection