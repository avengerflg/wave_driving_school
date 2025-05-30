@extends('layouts.instructor')

@section('title', 'Package Orders')

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold m-0">
                <span class="text-muted fw-light">Instructor / Packages /</span> Orders
            </h4>
            <div>
                <a href="{{ route('instructor.packages.index') }}" class="btn btn-outline-secondary">
                    <i class="bx bx-arrow-back me-1"></i> Back to Packages
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

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Student Package Orders</h5>
                <div>
                    <small class="text-muted">Showing orders from your students only</small>
                </div>
            </div>
            <div class="table-responsive text-nowrap">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Student</th>
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
                                        <a href="{{ route('instructor.clients.show', $order->user_id) }}">
                                            {{ $order->user->name ?? 'Unknown User' }}
                                        </a>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $order->created_at->format('M d, Y') }}</td>
                            <td>
                                <span class="badge bg-label-primary">
                                    {{ $order->items->count() }} items
                                </span>
                            </td>
                            <td>${{ number_format($order->total, 2) }}</td>
                            <td>
                                <span class="badge bg-{{ $order->status === 'completed' ? 'success' : ($order->status === 'cancelled' ? 'danger' : 'warning') }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('instructor.packages.orders.show', $order->id) }}" class="btn btn-sm btn-primary">
                                    <i class="bx bx-show"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-3">
                                <div class="d-flex flex-column align-items-center py-4">
                                    <i class="bx bx-shopping-bag text-secondary mb-2" style="font-size: 3rem;"></i>
                                    <h5 class="mb-1">No Orders Found</h5>
                                    <p class="text-muted">None of your students have purchased packages yet</p>
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
                        {{ $orders->links() }}
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection