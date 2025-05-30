<!-- filepath: resources/views/admin/instructors/show.blade.php -->

@extends('layouts.admin')

@section('title', 'Instructor Details')

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Header Card -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="fw-bold m-0">
                                <i class="bx bx-id-card text-primary me-2"></i>
                                <span class="text-muted fw-light">Admin / Instructors /</span> Instructor Details
                            </h4>
                            <div>
                                <a href="{{ route('admin.instructors.index') }}" class="btn btn-secondary me-2">
                                    <i class="bx bx-arrow-back me-1"></i> Back
                                </a>
                                <a href="{{ route('admin.instructors.edit', $instructor->id) }}" class="btn btn-warning">
                                    <i class="bx bx-edit me-1"></i> Edit
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Left Column - Profile Information -->
            <div class="col-xl-4 col-lg-5 col-md-12">
                <!-- Profile Card -->
                <div class="card mb-4">
                    <div class="card-body text-center pt-4 pb-4">
                        <div class="avatar avatar-xl mb-3 mx-auto">
                            @if($instructor->instructor && $instructor->instructor->profile_image)
                                <img src="{{ Storage::url($instructor->instructor->profile_image) }}" alt="Profile" class="rounded-circle">
                            @else
                                <span class="avatar-initial rounded-circle bg-primary">
                                    {{ substr($instructor->name, 0, 1) }}
                                </span>
                            @endif
                        </div>
                        <h4 class="mb-1">{{ $instructor->name }}</h4>
                        <span class="badge bg-label-{{ $instructor->status == 'active' ? 'success' : 'danger' }} mb-3">
                            {{ ucfirst($instructor->status) }}
                        </span>
                        
                        <div class="d-flex justify-content-center gap-2 mb-3">
                            <form action="{{ route('admin.instructors.update-status', $instructor->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="status" value="{{ $instructor->status == 'active' ? 'inactive' : 'active' }}">
                                <button type="submit" class="btn btn-sm btn-outline-{{ $instructor->status == 'active' ? 'danger' : 'success' }}">
                                    <i class="bx {{ $instructor->status == 'active' ? 'bx-block' : 'bx-check' }} me-1"></i> 
                                    {{ $instructor->status == 'active' ? 'Deactivate' : 'Activate' }}
                                </button>
                            </form>
                        </div>
                        
                        <div class="divider">
                            <div class="divider-text">Contact Information</div>
                        </div>
                        
                        <div class="info-container">
                            <div class="d-flex justify-content-start align-items-center mb-3">
                                <span class="badge bg-label-primary rounded p-2 me-2">
                                    <i class="bx bx-envelope fs-5"></i>
                                </span>
                                <div class="text-start">
                                    <h6 class="mb-0">Email</h6>
                                    <a href="mailto:{{ $instructor->email }}">{{ $instructor->email }}</a>
                                </div>
                            </div>
                            
                            @if($instructor->phone)
                            <div class="d-flex justify-content-start align-items-center mb-3">
                                <span class="badge bg-label-primary rounded p-2 me-2">
                                    <i class="bx bx-phone fs-5"></i>
                                </span>
                                <div class="text-start">
                                    <h6 class="mb-0">Phone</h6>
                                    <a href="tel:{{ $instructor->phone }}">{{ $instructor->phone }}</a>
                                </div>
                            </div>
                            @endif
                            
                            @if($instructor->address)
                            <div class="d-flex justify-content-start align-items-center mb-3">
                                <span class="badge bg-label-primary rounded p-2 me-2">
                                    <i class="bx bx-map fs-5"></i>
                                </span>
                                <div class="text-start">
                                    <h6 class="mb-0">Address</h6>
                                    <p class="mb-0">{{ $instructor->address }}</p>
                                </div>
                            </div>
                            @endif
                            
                            @if($instructor->suburb)
                            <div class="d-flex justify-content-start align-items-center">
                                <span class="badge bg-label-primary rounded p-2 me-2">
                                    <i class="bx bx-map-pin fs-5"></i>
                                </span>
                                <div class="text-start">
                                    <h6 class="mb-0">Suburb</h6>
                                    <p class="mb-0">{{ $instructor->suburb->name }}</p>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Instructor Details -->
                <div class="card mb-4">
                    <div class="card-header d-flex align-items-center">
                        <i class="bx bx-detail text-primary me-2"></i>
                        <h5 class="card-title mb-0">Instructor Details</h5>
                    </div>
                    <div class="card-body">
                        @if($instructor->instructor && $instructor->instructor->license_number)
                        <div class="mb-3">
                            <div class="d-flex align-items-center mb-2">
                                <div class="flex-shrink-0">
                                    <span class="badge bg-label-info rounded p-2">
                                        <i class="bx bx-id-card fs-5"></i>
                                    </span>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-0">License Number</h6>
                                    <p class="mb-0 text-muted">{{ $instructor->instructor->license_number }}</p>
                                </div>
                            </div>
                        </div>
                        @endif
                        
                        @if($instructor->instructor && $instructor->instructor->driving_experience)
                        <div class="mb-3">
                            <div class="d-flex align-items-center mb-2">
                                <div class="flex-shrink-0">
                                    <span class="badge bg-label-info rounded p-2">
                                        <i class="bx bx-timer fs-5"></i>
                                    </span>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-0">Driving Experience</h6>
                                    <p class="mb-0 text-muted">{{ $instructor->instructor->driving_experience }} years</p>
                                </div>
                            </div>
                        </div>
                        @endif
                        
                        @if($instructor->instructor && $instructor->instructor->languages)
                        <div class="mb-3">
                            <div class="d-flex align-items-center mb-2">
                                <div class="flex-shrink-0">
                                    <span class="badge bg-label-info rounded p-2">
                                        <i class="bx bx-globe fs-5"></i>
                                    </span>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-0">Languages</h6>
                                    <p class="mb-0 text-muted">{{ $instructor->instructor->languages }}</p>
                                </div>
                            </div>
                        </div>
                        @endif
                        
                        @if($instructor->instructor && $instructor->instructor->car_model)
                        <div class="mb-3">
                            <div class="d-flex align-items-center mb-2">
                                <div class="flex-shrink-0">
                                    <span class="badge bg-label-info rounded p-2">
                                        <i class="bx bx-car fs-5"></i>
                                    </span>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-0">Vehicle</h6>
                                    <p class="mb-0 text-muted">
                                        {{ $instructor->instructor->car_year ?? '' }} {{ $instructor->instructor->car_model }}
                                        @if($instructor->instructor->car_transmission)
                                        <span class="badge bg-secondary ms-1">{{ ucfirst($instructor->instructor->car_transmission) }}</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                        @endif
                        
                        @if($instructor->instructor && $instructor->instructor->hourly_rate)
                        <div class="mb-3">
                            <div class="d-flex align-items-center mb-2">
                                <div class="flex-shrink-0">
                                    <span class="badge bg-label-info rounded p-2">
                                        <i class="bx bx-money fs-5"></i>
                                    </span>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-0">Hourly Rate</h6>
                                    <p class="mb-0 text-muted">${{ number_format($instructor->instructor->hourly_rate, 2) }}</p>
                                </div>
                            </div>
                        </div>
                        @endif
                        
                        <div class="mb-0">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <span class="badge bg-label-info rounded p-2">
                                        <i class="bx bx-time fs-5"></i>
                                    </span>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-0">Joined</h6>
                                    <p class="mb-0 text-muted">{{ $instructor->created_at->format('d M Y') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Service Areas -->
                @if($instructor->instructor && $instructor->instructor->serviceSuburbs && $instructor->instructor->serviceSuburbs->count() > 0)
                <div class="card mb-4">
                    <div class="card-header d-flex align-items-center">
                        <i class="bx bx-map-alt text-primary me-2"></i>
                        <h5 class="card-title mb-0">Service Areas</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($instructor->instructor->serviceSuburbs as $suburb)
                                <span class="badge bg-label-primary">{{ $suburb->name }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Right Column - Main Content -->
            <div class="col-xl-8 col-lg-7 col-md-12">
                <!-- Instructor Bio -->
                @if($instructor->instructor && $instructor->instructor->bio)
                <div class="card mb-4">
                    <div class="card-header d-flex align-items-center">
                        <i class="bx bx-user-voice text-primary me-2"></i>
                        <h5 class="card-title mb-0">About</h5>
                    </div>
                    <div class="card-body">
                        <p>{{ $instructor->instructor->bio }}</p>
                    </div>
                </div>
                @endif
                
                <!-- Stats Cards -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-start justify-content-between">
                                    <div class="content-left">
                                        <span>Total Bookings</span>
                                        <div class="d-flex align-items-center my-1">
                                            <h4 class="mb-0 me-2">{{ $totalBookings }}</h4>
                                        </div>
                                    </div>
                                    <span class="badge bg-label-primary rounded p-2">
                                        <i class="bx bx-calendar-check fs-4"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-start justify-content-between">
                                    <div class="content-left">
                                        <span>Completed</span>
                                        <div class="d-flex align-items-center my-1">
                                            <h4 class="mb-0 me-2">{{ $completedBookings }}</h4>
                                        </div>
                                    </div>
                                    <span class="badge bg-label-success rounded p-2">
                                        <i class="bx bx-check-circle fs-4"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-start justify-content-between">
                                    <div class="content-left">
                                        <span>Pending</span>
                                        <div class="d-flex align-items-center my-1">
                                            <h4 class="mb-0 me-2">{{ $pendingBookings }}</h4>
                                        </div>
                                    </div>
                                    <span class="badge bg-label-warning rounded p-2">
                                        <i class="bx bx-time fs-4"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Availability Section -->
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="bx bx-time-five text-primary me-2"></i> Availability</h5>
                        <a href="{{ route('admin.instructors.availability', $instructor->id) }}" class="btn btn-sm btn-primary">
                            <i class="bx bx-calendar-edit me-1"></i> Manage Availability
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Current week availability summary -->
                            @php
                                $now = \Carbon\Carbon::now();
                                $startOfWeek = $now->startOfWeek();
                                $endOfWeek = $now->endOfWeek();
                                $daysOfWeek = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                                
                                // Get availability for this week
                                $weekAvailabilities = App\Models\Availability::where('instructor_id', $instructor->instructor->id)
                                    ->whereBetween('date', [$startOfWeek->format('Y-m-d'), $endOfWeek->format('Y-m-d')])
                                    ->orderBy('date')
                                    ->orderBy('start_time')
                                    ->get();
                                    
                                // Group by day of week
                                $groupedAvailabilities = $weekAvailabilities->groupBy(function($item) {
                                    return \Carbon\Carbon::parse($item->date)->format('l');
                                });
                            @endphp
                            
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr class="text-center">
                                                @foreach($daysOfWeek as $day)
                                                    <th>{{ $day }}</th>
                                                @endforeach
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                @foreach($daysOfWeek as $day)
                                                    <td class="text-center p-3">
                                                        @if(isset($groupedAvailabilities[$day]) && $groupedAvailabilities[$day]->count() > 0)
                                                            @foreach($groupedAvailabilities[$day] as $availability)
                                                                <div class="mb-1 bg-light-success p-2 rounded">
                                                                    {{ \Carbon\Carbon::parse($availability->start_time)->format('h:i A') }} - 
                                                                    {{ \Carbon\Carbon::parse($availability->end_time)->format('h:i A') }}
                                                                </div>
                                                            @endforeach
                                                        @else
                                                            <span class="text-muted">Not available</span>
                                                        @endif
                                                    </td>
                                                @endforeach
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                                
                            <div class="col-12 text-center mt-3">
                                <a href="{{ route('admin.instructors.availability', $instructor->id) }}" class="btn btn-outline-primary">
                                    View Full Availability Calendar
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Bookings -->
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="bx bx-calendar-event text-primary me-2"></i> Recent Bookings</h5>
                        <a href="{{ route('admin.instructors.bookings', $instructor->id) }}" class="btn btn-sm btn-primary">
                            View All Bookings
                        </a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Student</th>
                                    <th>Service</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($bookings as $booking)
                                <tr>
                                    <td><strong>#{{ $booking->id }}</strong></td>
                                    <td>{{ $booking->user->name ?? 'N/A' }}</td>
                                    <td>{{ $booking->service->name ?? 'N/A' }}</td>
                                    <td>
                                        <i class="bx bx-calendar text-primary me-1"></i>
                                        {{ $booking->date ? $booking->date->format('d M Y') : 'N/A' }}
                                    </td>
                                    <td>
                                        <span class="badge bg-label-{{ $booking->status == 'confirmed' ? 'success' : ($booking->status == 'pending' ? 'warning' : 'danger') }} me-1">
                                            {{ ucfirst($booking->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.bookings.show', $booking->id) }}" class="btn btn-sm btn-info">
                                            <i class="bx bx-show-alt"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-3">
                                        <div class="d-flex flex-column align-items-center">
                                            <i class="bx bx-calendar-x text-secondary mb-2" style="font-size: 2rem;"></i>
                                            <p class="mb-0 text-muted">No bookings found for this instructor</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Actions Card -->
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <i class="bx bx-cog text-primary me-2"></i>
                        <h5 class="card-title mb-0">Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 mb-3 mb-md-0">
                                <div class="d-grid">
                                    <a href="{{ route('admin.instructors.schedule', $instructor->id) }}" class="btn btn-primary">
                                        <i class="bx bx-calendar-week me-1"></i> View Schedule
                                    </a>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3 mb-md-0">
                                <div class="d-grid">
                                    <a href="{{ route('admin.instructors.availability', $instructor->id) }}" class="btn btn-success">
                                        <i class="bx bx-time-five me-1"></i> Manage Availability
                                    </a>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3 mb-md-0">
                                <div class="d-grid">
                                    <a href="{{ route('admin.instructors.edit', $instructor->id) }}" class="btn btn-warning">
                                        <i class="bx bx-edit me-1"></i> Edit Instructor
                                    </a>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <form action="{{ route('admin.instructors.destroy', $instructor->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this instructor?');">
                                    @csrf
                                    @method('DELETE')
                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-danger">
                                            <i class="bx bx-trash me-1"></i> Delete
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection