@extends('layouts.admin')

@section('title', 'Package Details')

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold m-0">
                <span class="text-muted fw-light">Admin / Packages /</span> View
            </h4>
            <div>
                <a href="{{ route('admin.packages.index') }}" class="btn btn-outline-secondary">
                    <i class="bx bx-arrow-back me-1"></i> Back to Packages
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-md-7">
                <!-- Package Information -->
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Package Information</h5>
                        <div>
                            <a href="{{ route('admin.packages.edit', $package->id) }}" class="btn btn-primary btn-sm">
                                <i class="bx bx-edit-alt me-1"></i> Edit
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <h4 class="mb-1">{{ $package->name }}</h4>
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-label-{{ $package->active ? 'success' : 'danger' }} me-2">
                                        {{ $package->active ? 'Active' : 'Inactive' }}
                                    </span>
                                    @if($package->featured)
                                        <span class="badge bg-label-warning me-2">
                                            <i class="bx bx-star"></i> Featured
                                        </span>
                                    @endif
                                    <span class="text-muted">Created {{ $package->created_at->format('M d, Y') }}</span>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <h6 class="fw-semibold">Description</h6>
                                <p class="mb-0">
                                    {{ $package->description ?: 'No description provided.' }}
                                </p>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <h6 class="fw-semibold">Price</h6>
                                <h4 class="text-primary mb-0">${{ number_format($package->price, 2) }}</h4>
                            </div>
                            <div class="col-md-4">
                                <h6 class="fw-semibold">Lessons</h6>
                                <h4 class="mb-0">{{ $package->lessons }}</h4>
                            </div>
                            <div class="col-md-4">
                                <h6 class="fw-semibold">Duration</h6>
                                <h4 class="mb-0">{{ $package->duration }} min</h4>
                            </div>
                        </div>

                        <hr>

                        <dl class="row mb-0">
                            <dt class="col-sm-3">ID</dt>
                            <dd class="col-sm-9">{{ $package->id }}</dd>
                            
                            <dt class="col-sm-3">Status</dt>
                            <dd class="col-sm-9">
                                <span class="badge bg-label-{{ $package->active ? 'success' : 'danger' }}">
                                    {{ $package->active ? 'Active' : 'Inactive' }}
                                </span>
                            </dd>
                            
                            <dt class="col-sm-3">Created at</dt>
                            <dd class="col-sm-9">{{ $package->created_at->format('M d, Y h:i A') }}</dd>
                            
                            <dt class="col-sm-3">Last Updated</dt>
                            <dd class="col-sm-9">{{ $package->updated_at->format('M d, Y h:i A') }}</dd>
                        </dl>
                    </div>
                </div>

                <!-- Recent Orders with this Package -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Recent Orders</h5>
                        @if($orderCounts['total'] > 0)
                            <div>
                                <a href="{{ route('admin.packages.orders') }}" class="btn btn-outline-primary btn-sm">
                                    View All Orders
                                </a>
                            </div>
                        @endif
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Customer</th>
                                    <th>Quantity</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentOrders as $item)
                                    <tr>
                                        <td>
                                            <div>{{ $item->created_at->format('M d, Y') }}</div>
                                            <small class="text-muted">{{ $item->created_at->format('h:i A') }}</small>
                                        </td>
                                        <td>{{ $item->order->user->name }}</td>
                                        <td>{{ $item->quantity }}</td>
                                        <td>
                                            <span class="badge bg-label-{{ 
                                                $item->order->status === 'completed' ? 'success' : 
                                                ($item->order->status === 'cancelled' ? 'danger' : 'warning') 
                                            }}">
                                                {{ ucfirst($item->order->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.packages.orders.show', $item->order_id) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="bx bx-show"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-3">No orders found for this package</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <div class="col-md-5">
                <!-- Statistics -->
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div class="card-info">
                                        <p class="card-text mb-1">Total Orders</p>
                                        <div class="d-flex align-items-end">
                                            <h4 class="card-title mb-0 me-2">{{ $orderCounts['total'] }}</h4>
                                        </div>
                                    </div>
                                    <div class="card-icon">
                                        <span class="badge bg-label-primary rounded p-2">
                                            <i class="bx bx-shopping-bag bx-sm"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div class="card-info">
                                        <p class="card-text mb-1">Revenue</p>
                                        <div class="d-flex align-items-end">
                                            <h4 class="card-title mb-0 me-2">
                                                ${{ number_format($orderCounts['completed'] * $package->price, 2) }}
                                            </h4>
                                        </div>
                                    </div>
                                    <div class="card-icon">
                                        <span class="badge bg-label-success rounded p-2">
                                            <i class="bx bx-dollar bx-sm"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Monthly Sales Chart -->
                <div class="card mb-4">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="mb-0">Monthly Sales</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="packageSalesChart" height="200"></canvas>
                    </div>
                </div>
                
                <!-- Package Card Preview -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Package Card Preview</h5>
                    </div>
                    <div class="card-body">
                        <div class="border rounded p-4 mb-3">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <h5 class="m-0">{{ $package->name }}</h5>
                                @if($package->featured)
                                    <span class="badge bg-warning">
                                        <i class="bx bx-star"></i> Featured
                                    </span>
                                @else
                                    <span class="badge bg-{{ $package->active ? 'primary' : 'secondary' }}">
                                        {{ $package->active ? 'Available' : 'Unavailable' }}
                                    </span>
                                @endif
                            </div>
                            <p class="text-muted small mb-3">{{ Str::limit($package->description, 100) }}</p>
                            <hr>
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div><i class="bx bx-check-circle text-primary me-1"></i> {{ $package->lessons }} lessons</div>
                                    <div><i class="bx bx-time text-primary me-1"></i> {{ $package->duration }} min each</div>
                                </div>
                                <div class="fw-bold">${{ number_format($package->price, 2) }}</div>
                            </div>
                        </div>
                        <p class="text-muted text-center small mb-0">
                            This is how the package appears to users on the packages page
                        </p>
                    </div>
                </div>
                
                <!-- Actions Card -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('admin.packages.edit', $package->id) }}" class="btn btn-primary">
                                <i class="bx bx-edit me-1"></i> Edit Package
                            </a>
                            
                            <form action="{{ route('admin.packages.toggle-status', $package->id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-outline-{{ $package->active ? 'warning' : 'success' }} w-100">
                                    <i class="bx {{ $package->active ? 'bx-hide' : 'bx-show' }} me-1"></i>
                                    {{ $package->active ? 'Deactivate Package' : 'Activate Package' }}
                                </button>
                            </form>
                            
                            <form action="{{ route('admin.packages.toggle-featured', $package->id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-outline-{{ $package->featured ? 'secondary' : 'warning' }} w-100">
                                    <i class="bx bx-star me-1"></i>
                                    {{ $package->featured ? 'Remove from Featured' : 'Set as Featured' }}
                                </button>
                            </form>
                            
                            <button onclick="confirmDelete()" class="btn btn-outline-danger">
                                <i class="bx bx-trash me-1"></i> Delete Package
                            </button>
                            <form id="delete-form" action="{{ route('admin.packages.destroy', $package->id) }}" method="POST" class="d-none">
                                @csrf
                                @method('DELETE')
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- Order Status Distribution -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Order Status Distribution</h5>
                    </div>
                    <div class="card-body pb-0">
                        @if($orderCounts['total'] > 0)
                            <div class="progress-stacked mb-4">
                                @if($orderCounts['completed'] > 0)
                                    <div class="progress-bar bg-success" role="progressbar" 
                                        style="width: {{ ($orderCounts['completed'] / $orderCounts['total']) * 100 }}%" 
                                        aria-valuenow="{{ $orderCounts['completed'] }}" aria-valuemin="0" 
                                        aria-valuemax="{{ $orderCounts['total'] }}" 
                                        data-bs-toggle="tooltip" 
                                        title="Completed: {{ $orderCounts['completed'] }}">
                                    </div>
                                @endif
                                @if($orderCounts['pending'] > 0)
                                    <div class="progress-bar bg-warning" role="progressbar" 
                                        style="width: {{ ($orderCounts['pending'] / $orderCounts['total']) * 100 }}%" 
                                        aria-valuenow="{{ $orderCounts['pending'] }}" aria-valuemin="0" 
                                        aria-valuemax="{{ $orderCounts['total'] }}"
                                        data-bs-toggle="tooltip" 
                                        title="Pending: {{ $orderCounts['pending'] }}">
                                    </div>
                                @endif
                                @if($orderCounts['cancelled'] > 0)
                                    <div class="progress-bar bg-danger" role="progressbar" 
                                        style="width: {{ ($orderCounts['cancelled'] / $orderCounts['total']) * 100 }}%" 
                                        aria-valuenow="{{ $orderCounts['cancelled'] }}" aria-valuemin="0" 
                                        aria-valuemax="{{ $orderCounts['total'] }}"
                                        data-bs-toggle="tooltip" 
                                        title="Cancelled: {{ $orderCounts['cancelled'] }}">
                                    </div>
                                @endif
                            </div>
                            <div class="d-flex justify-content-between mb-4">
                                <div>
                                    <span class="badge bg-success me-1"></span>
                                    <span>Completed ({{ $orderCounts['completed'] }})</span>
                                </div>
                                <div>
                                    <span class="badge bg-warning me-1"></span>
                                    <span>Pending ({{ $orderCounts['pending'] }})</span>
                                </div>
                                <div>
                                    <span class="badge bg-danger me-1"></span>
                                    <span>Cancelled ({{ $orderCounts['cancelled'] }})</span>
                                </div>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="bx bx-bar-chart text-muted" style="font-size: 2rem;"></i>
                                <p class="mt-2 mb-0">No order data available</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    function confirmDelete() {
        if (confirm('Are you sure you want to delete this package? This action cannot be undone and may affect existing orders.')) {
            document.getElementById('delete-form').submit();
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
        
        // Monthly sales chart
        var ctx = document.getElementById('packageSalesChart').getContext('2d');
        var months = @json($months);
        var counts = @json($counts);
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: months,
                datasets: [{
                    label: 'Packages Sold',
                    data: counts,
                    backgroundColor: 'rgba(105, 108, 255, 0.3)',
                    borderColor: '#696cff',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    });
</script>
@endsection