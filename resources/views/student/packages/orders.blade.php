@extends('layouts.student')

@section('title', 'My Package Orders')

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Breadcrumb -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold m-0">
                <span class="text-muted fw-light">Student / Packages /</span> My Orders
            </h4>
            <div>
                <a href="{{ route('student.packages.index') }}" class="btn btn-primary">
                    <i class="bx bx-package me-1"></i> Browse Packages
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

        <!-- Package orders list -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">My Package Orders</h5>
                <a href="{{ route('student.packages.credits') }}" class="btn btn-outline-primary btn-sm">
                    <i class="bx bx-credit-card me-1"></i> View My Credits
                </a>
            </div>
            
            @if($orders->count() > 0)
            <div class="table-responsive text-nowrap">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Date</th>
                            <th>Packages</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @foreach($orders as $order)
                        <tr>
                            <td><strong>#{{ $order->id }}</strong></td>
                            <td>{{ $order->created_at->format('M d, Y') }}</td>
                            <td>
                                <ul class="list-unstyled mb-0">
                                    @foreach($order->items as $item)
                                    <li>
                                        {{ $item->package->name ?? 'Unknown Package' }}
                                        @if($item->quantity > 1)
                                        <span class="badge bg-label-info">x{{ $item->quantity }}</span>
                                        @endif
                                    </li>
                                    @endforeach
                                </ul>
                            </td>
                            <td>${{ number_format($order->total_amount, 2) }}</td>
                            <td>
                                <span class="badge bg-{{ $order->status === 'completed' ? 'success' : ($order->status === 'cancelled' ? 'danger' : 'warning') }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('student.packages.orders.show', $order->id) }}" class="btn btn-sm btn-primary">
                                    <i class="bx bx-show me-1"></i> View
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                {{ $orders->links() }}
            </div>
            @else
            <div class="card-body text-center py-5">
                <img src="{{ asset('assets/img/illustrations/empty-orders.svg') }}" alt="No orders" class="mb-3" style="max-width: 180px;">
                <h5>No Package Orders Yet</h5>
                <p class="mb-4">You haven't purchased any driving lesson packages yet.</p>
                <a href="{{ route('packages.index') }}" class="btn btn-primary">
                    <i class="bx bx-package me-1"></i> Browse Available Packages
                </a>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection