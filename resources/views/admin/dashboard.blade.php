<!-- resources/views/admin/dashboard.blade.php -->
@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4">
    <h1 class="h3 mb-4">Admin Dashboard</h1>
    
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-primary text-white rounded-circle p-3 me-3">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1">Total Bookings</h6>
                            <h4 class="mb-0">{{ $totalBookings }}</h4>
                        </div>
                    </div>
                    <div class="progress" style="height: 5px;">
                        <div class="progress-bar bg-primary" role="progressbar" style="width: 100%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-success text-white rounded-circle p-3 me-3">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1">Total Revenue</h6>
                            <h4 class="mb-0">${{ number_format($totalRevenue, 2) }}</h4>
                        </div>
                    </div>
                    <div class="progress" style="height: 5px;">
                        <div class="progress-bar bg-success" role="progressbar" style="width: 100%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-info text-white rounded-circle p-3 me-3">
                            <i class="fas fa-users"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1">Total Users</h6>
                            <h4 class="mb-0">{{ $totalUsers }}</h4>
                        </div>
                    </div>
                    <div class="progress" style="height: 5px;">
                        <div class="progress-bar bg-info" role="progressbar" style="width: 100%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-warning text-white rounded-circle p-3 me-3">
                            <i class="fas fa-id-card"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1">Instructors</h6>
                            <h4 class="mb-0">{{ $totalInstructors }}</h4>
                        </div>
                    </div>
                    <div class="progress" style="height: 5px;">
                        <div class="progress-bar bg-warning" role="progressbar" style="width: 100%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row g-4 mb-4">
        <div class="col-xl-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title mb-4">Booking Statistics</h5>
                    <canvas id="bookingsChart" height="300"></canvas>
                </div>
            </div>
        </div>
        
        <div class="col-xl-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title mb-4">Booking Status</h5>
                    <canvas id="bookingStatusChart" height="300"></canvas>
                    
                    <div class="row g-0 text-center mt-3">
                        <div class="col-3">
                            <div class="p-2">
                                <h6 class="mb-0">{{ $pendingBookings }}</h6>
                                <small class="text-muted">Pending</small>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="p-2">
                                <h6 class="mb-0">{{ $confirmedBookings }}</h6>
                                <small class="text-muted">Confirmed</small>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="p-2">
                                <h6 class="mb-0">{{ $completedBookings }}</h6>
                                <small class="text-muted">Completed</small>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="p-2">
                                <h6 class="mb-0">{{ $cancelledBookings }}</h6>
                                <small class="text-muted">Cancelled</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row g-4 mb-4">
        <div class="col-xl-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="card-title mb-0">Recent Bookings</h5>
                        <a href="{{ route('admin.bookings.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Student</th>
                                    <th>Instructor</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentBookings as $booking)
                                    <tr>
                                        <td>{{ $booking->id }}</td>
                                        <td>
                                            @if($booking->booking_for === 'other')
                                                {{ $booking->other_name }}
                                            @else
                                                {{ $booking->user->name }}
                                            @endif
                                        </td>
                                        <td>{{ $booking->instructor->user->name }}</td>
                                        <td>{{ \Carbon\Carbon::parse($booking->date)->format('d M Y') }}</td>
                                        <td>
                                            @if($booking->status === 'pending')
                                                <span class="badge bg-warning">Pending</span>
                                            @elseif($booking->status === 'confirmed')
                                                <span class="badge bg-success">Confirmed</span>
                                            @elseif($booking->status === 'completed')
                                                <span class="badge bg-info">Completed</span>
                                            @elseif($booking->status === 'cancelled')
                                                <span class="badge bg-danger">Cancelled</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.bookings.show', $booking) }}" class="btn btn-sm btn-outline-primary">View</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title mb-4">Revenue Overview</h5>
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h6 class="text-muted mb-1">Total Revenue</h6>
                            <h3 class="mb-0">${{ number_format($totalRevenue, 2) }}</h3>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1">This Month</h6>
                            <h3 class="mb-0">${{ number_format($monthlyRevenue, 2) }}</h3>
                        </div>
                    </div>
                    <canvas id="revenueChart" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row g-4">
        <div class="col-xl-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="card-title mb-0">Top Instructors</h5>
                        <a href="{{ route('admin.instructors.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                    
                    @if($topInstructors->isEmpty())
                        <p class="text-center">No instructor data available</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Instructor</th>
                                        <th>Bookings</th>
                                        <th>Rating</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($topInstructors as $instructor)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="{{ $instructor->profile_image ? asset('storage/' . $instructor->profile_image) : 'https://via.placeholder.com/40x40?text=Instructor' }}" class="rounded-circle me-2" width="40" height="40" alt="{{ $instructor->user->name }}">
                                                    <div>
                                                        <div>{{ $instructor->user->name }}</div>
                                                        <small class="text-muted">{{ $instructor->user->email }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ $instructor->bookings_count }}</td>
                                            <td>
                                                <div class="text-warning">
                                                    @for($i = 0; $i < 5; $i++)
                                                        @if($i < $instructor->rating)
                                                            <i class="fas fa-star"></i>
                                                        @else
                                                            <i class="far fa-star"></i>
                                                        @endif
                                                    @endfor
                                                </div>
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.instructors.show', $instructor) }}" class="btn btn-sm btn-outline-primary">View</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-xl-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="card-title mb-0">Top Suburbs</h5>
                        <a href="{{ route('admin.suburbs.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                    
                    @if($topSuburbs->isEmpty())
                        <p class="text-center">No suburb data available</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Suburb</th>
                                        <th>State</th>
                                        <th>Bookings</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($topSuburbs as $suburb)
                                        <tr>
                                            <td>{{ $suburb->name }}</td>
                                            <td>{{ $suburb->state }}</td>
                                            <td>{{ $suburb->bookings_count }}</td>
                                            <td>
                                                <a href="{{ route('admin.suburbs.show', $suburb) }}" class="btn btn-sm btn-outline-primary">View</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Bookings Chart
        const bookingsCtx = document.getElementById('bookingsChart').getContext('2d');
        const bookingsChart = new Chart(bookingsCtx, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                    label: 'Bookings',
                    data: {{ json_encode($monthlyBookingsData) }},
                    backgroundColor: 'rgba(13, 110, 253, 0.7)',
                    borderColor: 'rgba(13, 110, 253, 1)',
                    borderWidth: 1
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
                }
            }
        });
        
        // Booking Status Chart
        const statusCtx = document.getElementById('bookingStatusChart').getContext('2d');
        const statusChart = new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: ['Pending', 'Confirmed', 'Completed', 'Cancelled'],
                datasets: [{
                    data: [{{ $pendingBookings }}, {{ $confirmedBookings }}, {{ $completedBookings }}, {{ $cancelledBookings }}],
                    backgroundColor: [
                        'rgba(255, 193, 7, 0.8)',
                        'rgba(25, 135, 84, 0.8)',
                        'rgba(13, 202, 240, 0.8)',
                        'rgba(220, 53, 69, 0.8)'
                    ],
                    borderColor: [
                        'rgba(255, 193, 7, 1)',
                        'rgba(25, 135, 84, 1)',
                        'rgba(13, 202, 240, 1)',
                        'rgba(220, 53, 69, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
        
        // Revenue Chart
        const revenueCtx = document.getElementById('revenueChart').getContext('2d');
        const revenueChart = new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                    label: 'Revenue',
                    data: {{ json_encode($monthlyRevenueChartData) }},
                    backgroundColor: 'rgba(25, 135, 84, 0.1)',
                    borderColor: 'rgba(25, 135, 84, 1)',
                    borderWidth: 2,
                    tension: 0.3,
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
                            callback: function(value) {
                                return '$' + value;
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endpush
