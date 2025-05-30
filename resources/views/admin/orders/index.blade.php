@extends('layouts.admin')

@section('title', 'Order Management')

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold m-0">
                <span class="text-muted fw-light">Admin /</span> Order Management
            </h4>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.packages.orders') }}" class="btn btn-outline-primary">
                    <i class="bx bx-package me-1"></i> Package Orders
                </a>
                <a href="{{ route('admin.bookings.index') }}" class="btn btn-outline-primary">
                    <i class="bx bx-calendar me-1"></i> Lesson Bookings
                </a>
            </div>
        </div>

        @if(session('success'))
        <div class="alert alert-success alert-dismissible mb-4" role="alert">
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <!-- Overview Cards -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6">
                <div class="card">
                    <div class="card-body d-flex align-items-center">
                        <div class="avatar avatar-lg bg-primary me-3">
                            <span class="avatar-initial rounded"><i class="bx bx-shopping-bag"></i></span>
                        </div>
                        <div>
                            <h6 class="card-title mb-0">Total Orders</h6>
                            <h3 class="fw-bold mb-0">{{ $totalOrders ?? number_format($packageOrders->total() + $bookingOrders->total()) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card">
                    <div class="card-body d-flex align-items-center">
                        <div class="avatar avatar-lg bg-info me-3">
                            <span class="avatar-initial rounded"><i class="bx bx-package"></i></span>
                        </div>
                        <div>
                            <h6 class="card-title mb-0">Package Orders</h6>
                            <h3 class="fw-bold mb-0">{{ $packageOrders->total() }}</h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card">
                    <div class="card-body d-flex align-items-center">
                        <div class="avatar avatar-lg bg-warning me-3">
                            <span class="avatar-initial rounded"><i class="bx bx-calendar"></i></span>
                        </div>
                        <div>
                            <h6 class="card-title mb-0">Lesson Bookings</h6>
                            <h3 class="fw-bold mb-0">{{ $bookingOrders->total() }}</h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card">
                    <div class="card-body d-flex align-items-center">
                        <div class="avatar avatar-lg bg-success me-3">
                            <span class="avatar-initial rounded"><i class="bx bx-dollar"></i></span>
                        </div>
                        <div>
                            <h6 class="card-title mb-0">Total Revenue</h6>
                            <h3 class="fw-bold mb-0">${{ number_format($totalRevenue ?? 0, 2) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <form action="{{ route('admin.orders.index') }}" method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label for="search" class="form-label">Search</label>
                        <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Order ID or customer name">
                    </div>
                    <div class="col-md-2">
                        <label for="type" class="form-label">Order Type</label>
                        <select class="form-select" id="type" name="type">
                            <option value="">All Orders</option>
                            <option value="package" {{ request('type') == 'package' ? 'selected' : '' }}>Package Orders</option>
                            <option value="booking" {{ request('type') == 'booking' ? 'selected' : '' }}>Lesson Bookings</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">All Statuses</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="date_from" class="form-label">Date From</label>
                        <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-2">
                        <label for="date_to" class="form-label">Date To</label>
                        <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">Filter</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Nav tabs for order types -->
        <ul class="nav nav-tabs mb-3" role="tablist">
            <li class="nav-item">
                <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#all-orders" aria-selected="true">
                    All Orders
                </button>
            </li>
            <li class="nav-item">
                <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#package-orders" aria-selected="false">
                    Package Orders
                </button>
            </li>
            <li class="nav-item">
                <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#lesson-bookings" aria-selected="false">
                    Lesson Bookings
                </button>
            </li>
        </ul>

        <!-- Tab content -->
        <div class="tab-content">
            <!-- All Orders Tab -->
            <div class="tab-pane fade show active" id="all-orders" role="tabpanel">
                <div class="card">
                    <div class="table-responsive text-nowrap">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Type</th>
                                    <th>Customer</th>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                                @forelse($allOrders as $order)
                                <tr>
                                    <td><strong>#{{ $order['id'] }}</strong></td>
                                    <td>
                                        <span class="badge bg-label-{{ $order['type'] === 'package' ? 'primary' : 'warning' }}">
                                            {{ $order['type'] === 'package' ? 'Package' : 'Lesson' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm me-2">
                                                <span class="avatar-initial rounded-circle bg-label-primary">
                                                    {{ substr($order['customer_name'] ?? 'U', 0, 1) }}
                                                </span>
                                            </div>
                                            <div>
                                                <a href="{{ route('admin.clients.show', $order['customer_id']) }}">
                                                    {{ $order['customer_name'] }}
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $order['date'] }}</td>
                                    <td>${{ number_format($order['amount'], 2) }}</td>
                                    <td>
                                        <span class="badge bg-{{ $order['status'] === 'completed' ? 'success' : ($order['status'] === 'cancelled' ? 'danger' : 'warning') }}">
                                            {{ ucfirst($order['status']) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                <i class="bx bx-dots-vertical-rounded"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                @if($order['type'] === 'package')
                                                <a class="dropdown-item" href="{{ route('admin.packages.orders.show', $order['id']) }}">
                                                    <i class="bx bx-show me-1"></i> View Details
                                                </a>
                                                @else
                                                <a class="dropdown-item" href="{{ route('admin.bookings.show', $order['id']) }}">
                                                    <i class="bx bx-show me-1"></i> View Details
                                                </a>
                                                @endif
                                                <a class="dropdown-item" href="{{ route('admin.clients.show', $order['customer_id']) }}">
                                                    <i class="bx bx-user me-1"></i> View Customer
                                                </a>
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
                                            <p class="text-muted">No orders match your search criteria</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="card-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                Showing results
                            </div>
                            <div>
                                {{ $allOrders->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Package Orders Tab -->
            <div class="tab-pane fade" id="package-orders" role="tabpanel">
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
                                @forelse($packageOrders as $order)
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
                                                <a href="{{ route('admin.clients.show', $order->user_id) }}">
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
                                    <td>${{ number_format($order->total_amount, 2) }}</td>
                                    <td>
                                        <span class="badge bg-{{ $order->status === 'completed' ? 'success' : ($order->status === 'cancelled' ? 'danger' : 'warning') }}">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.packages.orders.show', $order->id) }}" class="btn btn-sm btn-icon btn-primary">
                                            <i class="bx bx-show"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">No package orders found</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="card-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                Showing {{ $packageOrders->firstItem() ?? 0 }} to {{ $packageOrders->lastItem() ?? 0 }} of {{ $packageOrders->total() }} package orders
                            </div>
                            <div>
                                {{ $packageOrders->appends(request()->except('page'))->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Lesson Bookings Tab -->
            <div class="tab-pane fade" id="lesson-bookings" role="tabpanel">
                <div class="card">
                    <div class="table-responsive text-nowrap">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Booking ID</th>
                                    <th>Client</th>
                                    <th>Instructor</th>
                                    <th>Date & Time</th>
                                    <th>Service</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                                @forelse($bookingOrders as $booking)
                                <tr>
                                    <td><strong>#{{ $booking->id }}</strong></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm me-2">
                                                <span class="avatar-initial rounded-circle bg-label-primary">
                                                    {{ substr($booking->user->name ?? 'C', 0, 1) }}
                                                </span>
                                            </div>
                                            <div>
                                                <a href="{{ route('admin.clients.show', $booking->user_id) }}">
                                                    {{ $booking->user->name ?? 'Unknown Client' }}
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm me-2">
                                                <span class="avatar-initial rounded-circle bg-label-info">
                                                    {{ substr($booking->instructor->name ?? 'I', 0, 1) }}
                                                </span>
                                            </div>
                                            <div>
                                                <a href="{{ route('admin.instructors.show', $booking->instructor_id) }}">
                                                    {{ $booking->instructor->name ?? 'Unknown Instructor' }}
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        {{ $booking->date->format('M d, Y') }}
                                        <small class="d-block text-muted">{{ $booking->start_time->format('g:i A') }} - {{ $booking->end_time->format('g:i A') }}</small>
                                    </td>
                                    <td>{{ $booking->service->name ?? 'Unknown Service' }}</td>
                                    <td>
                                        <span class="badge bg-{{ $booking->status === 'completed' ? 'success' : ($booking->status === 'cancelled' ? 'danger' : ($booking->status === 'confirmed' ? 'info' : 'warning')) }}">
                                            {{ ucfirst($booking->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.bookings.show', $booking->id) }}" class="btn btn-sm btn-icon btn-primary">
                                            <i class="bx bx-show"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">No lesson bookings found</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="card-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                Showing {{ $bookingOrders->firstItem() ?? 0 }} to {{ $bookingOrders->lastItem() ?? 0 }} of {{ $bookingOrders->total() }} bookings
                            </div>
                            <div>
                                {{ $bookingOrders->appends(request()->except('page'))->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Analytics Summary -->
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="m-0">Recent Order Activity</h5>
                        <button class="btn btn-sm btn-outline-primary">View All</button>
                    </div>
                    <div class="card-body">
                        <ul class="timeline timeline-dashed mb-0">
                            @forelse($recentActivity as $activity)
                            <li class="timeline-item timeline-item-transparent">
                                <span class="timeline-indicator timeline-indicator-{{ $activity['type'] === 'package' ? 'primary' : 'warning' }}">
                                    <i class="bx {{ $activity['type'] === 'package' ? 'bx-package' : 'bx-calendar' }}"></i>
                                </span>
                                <div class="timeline-event">
                                    <div class="timeline-header">
                                        <h6 class="mb-0">{{ $activity['title'] }}</h6>
                                        <small class="text-muted">{{ $activity['time'] }}</small>
                                    </div>
                                    <p class="mb-0">{{ $activity['description'] }}</p>
                                    <div class="mt-1">
                                        <span class="badge bg-{{ $activity['status_color'] }}">{{ $activity['status'] }}</span>
                                    </div>
                                </div>
                            </li>
                            @empty
                            <li class="text-center py-4">No recent activity found</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="m-0">Monthly Order Summary</h5>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                This Year
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#">This Year</a></li>
                                <li><a class="dropdown-item" href="#">Last Year</a></li>
                                <li><a class="dropdown-item" href="#">All Time</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="orderSummaryChart" style="min-height: 265px;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Chart options
        var options = {
            series: [{
                name: 'Package Orders',
                data: [{{ implode(',', $chartData['packageCounts'] ?? [0,0,0,0,0,0]) }}]
            }, {
                name: 'Lesson Bookings',
                data: [{{ implode(',', $chartData['bookingCounts'] ?? [0,0,0,0,0,0]) }}]
            }],
            chart: {
                height: 265,
                type: 'bar',
                stacked: false,
                toolbar: {
                    show: false
                }
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '55%',
                    borderRadius: 5,
                    dataLabels: {
                        position: 'top',
                    },
                },
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                show: true,
                width: 2,
                colors: ['transparent']
            },
            xaxis: {
                categories: [{!! implode(',', array_map(function($month) { return "'$month'"; }, $chartData['months'] ?? ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'])) !!}],
            },
            yaxis: {
                title: {
                    text: 'Orders'
                }
            },
            fill: {
                opacity: 1
            },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return val + " orders"
                    }
                }
            },
            colors: ['#696cff', '#ffab00']
        };

        var chart = new ApexCharts(document.querySelector("#orderSummaryChart"), options);
        chart.render();

        // Tab handling to make charts resize correctly
        $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
            chart.render();
        });
    });
</script>
@endsection