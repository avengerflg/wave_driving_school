@extends('layouts.instructor')

@section('title', 'Dashboard')

@section('styles')
<style>
/* Color Variables */
:root {
    --primary: #696cff;
    --primary-light: #8592ff;
    --success: #71dd37;
    --info: #03c3ec;
    --warning: #ffab00;
    --danger: #ff3e1d;
    --dark: #233446;
    --gray: #697a8d;
}

/* Card Styles */
.dashboard-card {
    border-radius: 1.5rem;
    border: none;
    box-shadow: 0 4px 24px 0 rgba(34, 41, 47, 0.1);
    transition: all 0.3s ease-in-out;
}

.dashboard-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(34, 41, 47, 0.15);
}

/* Welcome Card */
.welcome-card {
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
    color: white;
    position: relative;
    overflow: hidden;
}

.welcome-card::before {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 300px;
    height: 100%;
    background: linear-gradient(45deg, rgba(255, 255, 255, 0.15), transparent);
    transform: skewX(-30deg) translateX(100px);
}

/* Stats Cards */
.stats-card {
    background: linear-gradient(to right, #ffffff, #f8f9fa);
    border-left: 4px solid transparent;
    transition: all 0.3s ease;
}

.stats-card:hover {
    transform: translateY(-5px);
}

.stats-card.primary { border-left-color: var(--primary); }
.stats-card.success { border-left-color: var(--success); }
.stats-card.info { border-left-color: var(--info); }
.stats-card.warning { border-left-color: var(--warning); }

/* Avatar Styles */
.avatar-initial {
    width: 42px;
    height: 42px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    border-radius: 12px;
    transition: transform 0.2s;
}

.avatar-initial:hover {
    transform: scale(1.1);
}

/* Schedule Items */
.schedule-item {
    transition: all 0.3s ease;
    border-radius: 1rem;
    padding: 1rem;
    margin-bottom: 1rem;
    background: linear-gradient(to right, #ffffff, #f8f9fa);
    border-left: 4px solid var(--primary);
}

.schedule-item:hover {
    background: linear-gradient(to right, rgba(105, 108, 255, 0.08), rgba(105, 108, 255, 0.03));
    transform: translateX(5px);
}

/* Timeline Styles */
.timeline-item {
    position: relative;
    padding-left: 1.5rem;
    border-left: 2px solid #e7e7e8;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
}

.timeline-point {
    position: absolute;
    left: -0.6rem;
    top: 0;
    width: 1.2rem;
    height: 1.2rem;
    border-radius: 50%;
    border: 2px solid #fff;
    box-shadow: 0 0 0 4px rgba(105, 108, 255, 0.1);
}

.timeline-point.success { background-color: var(--success); }
.timeline-point.danger { background-color: var(--danger); }
.timeline-point.primary { background-color: var(--primary); }
.timeline-point.warning { background-color: var(--warning); }

/* Button Styles */
.btn-light {
    background: rgba(255, 255, 255, 0.9);
    border: none;
    backdrop-filter: blur(4px);
}

.btn-light:hover {
    background: #ffffff;
    transform: translateY(-2px);
}

.btn-outline-light {
    border: 1px solid rgba(255, 255, 255, 0.5);
}

.btn-outline-light:hover {
    background: rgba(255, 255, 255, 0.1);
}

/* Chart Container */
#bookingAnalyticsChart {
    padding: 1rem;
    background: linear-gradient(to bottom, #ffffff, #f8f9fa);
    border-radius: 1rem;
}

/* Custom Scrollbar */
::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: #555;
}

/* Animations */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.card {
    animation: fadeIn 0.6s ease-out;
}

.stats-card {
    animation: fadeIn 0.6s ease-out;
    animation-fill-mode: both;
}

.stats-card:nth-child(1) { animation-delay: 0.1s; }
.stats-card:nth-child(2) { animation-delay: 0.2s; }
.stats-card:nth-child(3) { animation-delay: 0.3s; }
.stats-card:nth-child(4) { animation-delay: 0.4s; }
</style>
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Welcome Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card dashboard-card welcome-card">
                <div class="d-flex align-items-center row m-0">
                    <div class="col-sm-7 col-12 p-4">
                        <h2 class="mb-1">Welcome back, {{ Auth::user()->name }}! 👋</h2>
                        <p class="mb-4">
                            You have <span class="fw-bold">{{ $stats['todayBookingsCount'] }}</span> lessons today.<br>
                            Completion rate: <span class="fw-bold">{{ $performanceMetrics['completion_rate'] }}%</span>
                        </p>
                        <div class="d-flex gap-3">
                            <a href="{{ route('instructor.bookings.calendar') }}" class="btn btn-light">
                                <i class="bx bx-calendar me-2"></i>View Schedule
                            </a>
                            <a href="/" class="btn btn-outline-light">
                                <i class="bx bx-user me-2"></i>My Profile
                            </a>
                        </div>
                    </div>
                    <div class="col-sm-5 text-center p-4">
                        <img src="{{ asset('assets/img/illustrations/man-with-laptop-light.png') }}" 
                             alt="Welcome" class="img-fluid" style="max-height: 200px;">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        @foreach([
            ['title' => 'Total Bookings', 'value' => $stats['totalBookings'], 'icon' => 'bx-calendar', 'color' => 'primary'],
            ['title' => 'Completed', 'value' => $stats['completedBookings'], 'icon' => 'bx-check-circle', 'color' => 'success'],
            ['title' => 'Monthly Revenue', 'value' => '$'.number_format($stats['monthlyRevenue'], 2), 'icon' => 'bx-dollar', 'color' => 'info'],
            ['title' => 'Students', 'value' => $stats['totalStudents'], 'icon' => 'bx-group', 'color' => 'warning']
        ] as $stat)
        <div class="col-sm-6 col-lg-3 mb-4">
            <div class="card dashboard-card stats-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar flex-shrink-0 me-3">
                            <span class="avatar-initial rounded bg-label-{{ $stat['color'] }}">
                                <i class="bx {{ $stat['icon'] }}"></i>
                            </span>
                        </div>
                        <div>
                            <small class="text-muted d-block">{{ $stat['title'] }}</small>
                            <div class="h4 mb-0">{{ $stat['value'] }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Charts and Schedule -->
    <div class="row mb-4">
        <div class="col-12 col-lg-8 mb-4">
            <div class="card dashboard-card">
                <div class="card-header border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Booking Analytics</h5>
                        <div class="btn-group">
                            <button type="button" class="btn btn-outline-primary btn-sm active">Monthly</button>
                            <button type="button" class="btn btn-outline-primary btn-sm">Weekly</button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div id="bookingAnalyticsChart" style="min-height: 350px;"></div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-4 mb-4">
            <div class="card dashboard-card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">Today's Schedule</h5>
                </div>
                <div class="card-body p-0">
                    <div class="p-3">
                        @forelse($todayBookings as $booking)
                        <div class="schedule-item mb-3">
                            <div class="d-flex align-items-center">
                                <div class="avatar flex-shrink-0 me-3">
                                    <span class="avatar-initial rounded bg-label-primary">
                                        {{ substr($booking->user->name, 0, 2) }}
                                    </span>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0">{{ $booking->user->name }}</h6>
                                    <small class="text-muted">
                                        {{ Carbon\Carbon::parse($booking->start_time)->format('h:i A') }} - 
                                        {{ $booking->service->name }}
                                    </small>
                                </div>
                                <a href="{{ route('instructor.bookings.show', $booking) }}" 
                                   class="btn btn-icon btn-sm btn-outline-primary">
                                    <i class="bx bx-right-arrow-alt"></i>
                                </a>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-5">
                            <img src="{{ asset('assets/img/illustrations/empty-schedule.svg') }}" 
                                 alt="No bookings" class="mb-3" height="120">
                            <h6 class="text-muted">No lessons scheduled for today</h6>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities & Performance -->
    <div class="row">
        <div class="col-12 col-md-6 col-lg-4 mb-4">
            <div class="card dashboard-card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">Recent Activities</h5>
                </div>
                <div class="card-body">
                    @foreach($recentActivities as $activity)
                    <div class="timeline-item">
                        <span class="timeline-point bg-label-{{ $activity['type'] }}"></span>
                        <div class="timeline-event">
                            <div class="timeline-header mb-1">
                                <h6 class="mb-0">{{ $activity['message'] }}</h6>
                                <small class="text-muted">{{ $activity['time'] }}</small>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Add Performance Metrics and Busy Days sections here -->
    </div>
</div>
@endsection

@push('scripts')
<script>
const bookingAnalyticsChart = new ApexCharts(document.querySelector("#bookingAnalyticsChart"), {
    series: [{
        name: 'Bookings',
        type: 'area',
        data: {{ json_encode($chartData['monthlyBookings']) }}
    }, {
        name: 'Revenue',
        type: 'line',
        data: {{ json_encode($chartData['monthlyRevenue']) }}
    }],
    chart: {
        height: 350,
        type: 'line',
        toolbar: { show: false },
        zoom: { enabled: false },
        fontFamily: 'Public Sans, sans-serif',
        background: 'transparent'
    },
    stroke: {
        curve: 'smooth',
        width: [2, 3],
        dashArray: [0, 5]
    },
    fill: {
        type: ['gradient', 'solid'],
        gradient: {
            shade: 'dark',
            type: "vertical",
            opacityFrom: 0.5,
            opacityTo: 0.1,
            stops: [0, 100]
        }
    },
    colors: ['#696cff', '#03c3ec'],
    grid: {
        borderColor: '#f1f1f1',
        strokeDashArray: 5,
        xaxis: { lines: { show: true } },
        yaxis: { lines: { show: true } },
        padding: { top: 0, right: 0, bottom: 0, left: 0 }
    },
    markers: {
        size: 4,
        strokeColors: ['#696cff', '#03c3ec'],
        strokeWidth: 2,
        strokeOpacity: 0.9,
        fillOpacity: 1,
        discrete: [],
        hover: { size: 6 }
    },
    xaxis: {
        categories: {{ json_encode($chartData['labels']) }},
        labels: {
            style: {
                fontSize: '12px',
                colors: '#697a8d'
            }
        },
        axisBorder: { show: false },
        axisTicks: { show: false }
    },
    yaxis: [{
        title: { 
            text: 'Bookings',
            style: { fontSize: '13px', color: '#697a8d' }
        },
        labels: {
            style: { colors: '#697a8d' }
        }
    }, {
        opposite: true,
        title: { 
            text: 'Revenue ($)',
            style: { fontSize: '13px', color: '#697a8d' }
        },
        labels: {
            style: { colors: '#697a8d' }
        }
    }],
    tooltip: {
        shared: true,
        intersect: false,
        theme: 'dark',
        style: { fontSize: '12px' },
        y: [{
            formatter: value => Math.round(value)
        }, {
            formatter: value => `$${value.toFixed(2)}`
        }]
    },
    legend: {
        show: true,
        position: 'top',
        horizontalAlign: 'right',
        markers: { radius: 12 }
    }
});

bookingAnalyticsChart.render();
</script>
@endpush