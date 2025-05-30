
@extends('layouts.admin')

@section('title', 'Service Details')

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold m-0">
                <span class="text-muted fw-light">Admin / Services /</span> View
            </h4>
            <div>
                <a href="{{ route('admin.services.index') }}" class="btn btn-outline-secondary">
                    <i class="bx bx-arrow-back me-1"></i> Back to Services
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-md-7">
                <!-- Service Information -->
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Service Information</h5>
                        <div>
                            <a href="{{ route('admin.services.edit', $service->id) }}" class="btn btn-primary btn-sm">
                                <i class="bx bx-edit-alt me-1"></i> Edit
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <h4 class="mb-1">{{ $service->name }}</h4>
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-label-{{ $service->active ? 'success' : 'danger' }} me-2">
                                        {{ $service->active ? 'Active' : 'Inactive' }}
                                    </span>
                                    @if($service->featured)
                                        <span class="badge bg-label-warning me-2">
                                            <i class="bx bx-star"></i> Featured
                                        </span>
                                    @endif
                                    <span class="text-muted">Created {{ $service->created_at->format('M d, Y') }}</span>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <h6 class="fw-semibold">Description</h6>
                                <p class="mb-0">
                                    {{ $service->description ?: 'No description provided.' }}
                                </p>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <h6 class="fw-semibold">Price</h6>
                                <h4 class="text-primary mb-0">${{ number_format($service->price, 2) }}</h4>
                            </div>
                            <div class="col-md-6">
                                <h6 class="fw-semibold">Duration</h6>
                                <h4 class="mb-0">{{ $service->duration }} minutes</h4>
                            </div>
                        </div>

                        <hr>

                        <dl class="row mb-0">
                            <dt class="col-sm-3">ID</dt>
                            <dd class="col-sm-9">{{ $service->id }}</dd>
                            
                            <dt class="col-sm-3">Status</dt>
                            <dd class="col-sm-9">
                                <span class="badge bg-label-{{ $service->active ? 'success' : 'danger' }}">
                                    {{ $service->active ? 'Active' : 'Inactive' }}
                                </span>
                            </dd>
                            
                            <dt class="col-sm-3">Created at</dt>
                            <dd class="col-sm-9">{{ $service->created_at->format('M d, Y h:i A') }}</dd>
                            
                            <dt class="col-sm-3">Last Updated</dt>
                            <dd class="col-sm-9">{{ $service->updated_at->format('M d, Y h:i A') }}</dd>
                        </dl>
                    </div>
                </div>

                <!-- Recent Bookings with this Service -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Recent Bookings</h5>
                        @if($bookingCounts['total'] > 0)
                            <div>
                                <a href="{{ route('admin.bookings.index', ['service_id' => $service->id]) }}" class="btn btn-outline-primary btn-sm">
                                    View All Bookings
                                </a>
                            </div>
                        @endif
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Student</th>
                                    <th>Instructor</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentBookings as $booking)
                                    <tr>
                                        <td>
                                            <div>{{ \Carbon\Carbon::parse($booking->date)->format('M d, Y') }}</div>
                                            <small class="text-muted">{{ \Carbon\Carbon::parse($booking->start_time)->format('h:i A') }}</small>
                                        </td>
                                        <td>{{ $booking->user->name }}</td>
                                        <td>{{ $booking->instructor->user->name }}</td>
                                        <td>
                                            <span class="badge bg-label-{{ 
                                                $booking->status === 'completed' ? 'success' : 
                                                ($booking->status === 'cancelled' ? 'danger' : 'warning') 
                                            }}">
                                                {{ ucfirst($booking->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.bookings.show', $booking->id) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="bx bx-show"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-3">No bookings found for this service</td>
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
                                        <p class="card-text mb-1">Total Bookings</p>
                                        <div class="d-flex align-items-end">
                                            <h4 class="card-title mb-0 me-2">{{ $bookingCounts['total'] }}</h4>
                                        </div>
                                    </div>
                                    <div class="card-icon">
                                        <span class="badge bg-label-primary rounded p-2">
                                            <i class="bx bx-calendar bx-sm"></i>
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
                                                ${{ number_format($bookingCounts['completed'] * $service->price, 2) }}
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
                
                <!-- Monthly Booking Chart -->
                <div class="card mb-4">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="mb-0">Monthly Bookings</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="serviceBookingChart" height="200"></canvas>
                    </div>
                </div>
                
                <!-- Service Card Preview -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Service Card Preview</h5>
                    </div>
                    <div class="card-body">
                        <div class="border rounded p-4 mb-3">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <h5 class="m-0">{{ $service->name }}</h5>
                                @if($service->featured)
                                    <span class="badge bg-warning">
                                        <i class="bx bx-star"></i> Featured
                                    </span>
                                @else
                                    <span class="badge bg-{{ $service->active ? 'primary' : 'secondary' }}">
                                        {{ $service->active ? 'Available' : 'Unavailable' }}
                                    </span>
                                @endif
                            </div>
                            <p class="text-muted small mb-3">{{ Str::limit($service->description, 100) }}</p>
                            <hr>
                            <div class="d-flex justify-content-between">
                                <div>
                                    <i class="bx bx-time text-primary me-1"></i> {{ $service->duration }} min
                                </div>
                                <div class="fw-bold">${{ number_format($service->price, 2) }}</div>
                            </div>
                        </div>
                        <p class="text-muted text-center small mb-0">
                            This is how the service appears to users on the booking page
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
                            <a href="{{ route('admin.services.edit', $service->id) }}" class="btn btn-primary">
                                <i class="bx bx-edit me-1"></i> Edit Service
                            </a>
                            
                            <form action="{{ route('admin.services.toggle-status', $service->id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-outline-{{ $service->active ? 'warning' : 'success' }} w-100">
                                    <i class="bx {{ $service->active ? 'bx-hide' : 'bx-show' }} me-1"></i>
                                    {{ $service->active ? 'Deactivate Service' : 'Activate Service' }}
                                </button>
                            </form>
                            
                            <form action="{{ route('admin.services.toggle-featured', $service->id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-outline-{{ $service->featured ? 'secondary' : 'warning' }} w-100">
                                    <i class="bx bx-star me-1"></i>
                                    {{ $service->featured ? 'Remove from Featured' : 'Set as Featured' }}
                                </button>
                            </form>
                            
                            <button onclick="confirmDelete()" class="btn btn-outline-danger">
                                <i class="bx bx-trash me-1"></i> Delete Service
                            </button>
                            <form id="delete-form" action="{{ route('admin.services.destroy', $service->id) }}" method="POST" class="d-none">
                                @csrf
                                @method('DELETE')
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- Booking Status Distribution -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Booking Status Distribution</h5>
                    </div>
                    <div class="card-body pb-0">
                        @if($bookingCounts['total'] > 0)
                            <div class="progress-stacked mb-4">
                                @if($bookingCounts['completed'] > 0)
                                    <div class="progress-bar bg-success" role="progressbar" 
                                        style="width: {{ ($bookingCounts['completed'] / $bookingCounts['total']) * 100 }}%" 
                                        aria-valuenow="{{ $bookingCounts['completed'] }}" aria-valuemin="0" 
                                        aria-valuemax="{{ $bookingCounts['total'] }}" 
                                        data-bs-toggle="tooltip" 
                                        title="Completed: {{ $bookingCounts['completed'] }}">
                                    </div>
                                @endif
                                @if($bookingCounts['pending'] > 0)
                                    <div class="progress-bar bg-warning" role="progressbar" 
                                        style="width: {{ ($bookingCounts['pending'] / $bookingCounts['total']) * 100 }}%" 
                                        aria-valuenow="{{ $bookingCounts['pending'] }}" aria-valuemin="0" 
                                        aria-valuemax="{{ $bookingCounts['total'] }}"
                                        data-bs-toggle="tooltip" 
                                        title="Pending: {{ $bookingCounts['pending'] }}">
                                    </div>
                                @endif
                                @if($bookingCounts['cancelled'] > 0)
                                    <div class="progress-bar bg-danger" role="progressbar" 
                                        style="width: {{ ($bookingCounts['cancelled'] / $bookingCounts['total']) * 100 }}%" 
                                        aria-valuenow="{{ $bookingCounts['cancelled'] }}" aria-valuemin="0" 
                                        aria-valuemax="{{ $bookingCounts['total'] }}"
                                        data-bs-toggle="tooltip" 
                                        title="Cancelled: {{ $bookingCounts['cancelled'] }}">
                                    </div>
                                @endif
                            </div>
                            <div class="d-flex justify-content-between mb-4">
                                <div>
                                    <span class="badge bg-success me-1"></span>
                                    <span>Completed ({{ $bookingCounts['completed'] }})</span>
                                </div>
                                <div>
                                    <span class="badge bg-warning me-1"></span>
                                    <span>Pending ({{ $bookingCounts['pending'] }})</span>
                                </div>
                                <div>
                                    <span class="badge bg-danger me-1"></span>
                                    <span>Cancelled ({{ $bookingCounts['cancelled'] }})</span>
                                </div>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="bx bx-bar-chart text-muted" style="font-size: 2rem;"></i>
                                <p class="mt-2 mb-0">No booking data available</p>
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
        if (confirm('Are you sure you want to delete this service? This action cannot be undone and may affect existing bookings.')) {
            document.getElementById('delete-form').submit();
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
        
        // Monthly booking chart
        var ctx = document.getElementById('serviceBookingChart').getContext('2d');
        var months = @json($months);
        var counts = @json($counts);
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: months,
                datasets: [{
                    label: 'Bookings',
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