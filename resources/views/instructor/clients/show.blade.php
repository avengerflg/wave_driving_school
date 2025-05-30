@extends('layouts.instructor')

@section('title', $client->name)

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Back Button and Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <a href="{{ route('instructor.clients.index') }}" class="btn btn-icon btn-outline-primary me-3">
                                <i class="bx bx-arrow-back"></i>
                            </a>
                            <h4 class="fw-bold m-0">Client Details</h4>
                        </div>
                        <a href="{{ route('instructor.clients.bookings', $client) }}" class="btn btn-primary">
                            <i class="bx bx-calendar me-1"></i> View All Bookings
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Client Profile -->
        <div class="col-xl-4 col-lg-5">
            <div class="card mb-4">
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="avatar avatar-xl mb-3">
                            @if($client->profile_image)
                                <img src="{{ Storage::url($client->profile_image) }}" alt="Avatar" class="rounded-circle">
                            @else
                                <span class="avatar-initial rounded-circle bg-primary">
                                    {{ substr($client->name, 0, 1) }}
                                </span>
                            @endif
                        </div>
                        <h5 class="mb-1">{{ $client->name }}</h5>
                        <span class="badge bg-label-{{ $client->status === 'active' ? 'success' : 'warning' }}">
                            {{ ucfirst($client->status) }}
                        </span>
                    </div>
                    
                    <div class="info-container">
                        <ul class="list-unstyled">
                            <li class="mb-3">
                                <span class="fw-bold me-2"><i class="bx bx-envelope me-2"></i>Email:</span>
                                <span>{{ $client->email }}</span>
                            </li>
                            <li class="mb-3">
                                <span class="fw-bold me-2"><i class="bx bx-phone me-2"></i>Phone:</span>
                                <span>{{ $client->phone ?? 'Not provided' }}</span>
                            </li>
                            <li class="mb-3">
                                <span class="fw-bold me-2"><i class="bx bx-map me-2"></i>Address:</span>
                                <span>{{ $client->address ?? 'Not provided' }}</span>
                            </li>
                            @if($client->suburb)
                            <li class="mb-3">
                                <span class="fw-bold me-2"><i class="bx bx-building-house me-2"></i>Suburb:</span>
                                <span>{{ $client->suburb->name }}</span>
                            </li>
                            @endif
                            <li class="mb-3">
                                <span class="fw-bold me-2"><i class="bx bx-calendar me-2"></i>Joined:</span>
                                <span>{{ $client->created_at->format('d M Y') }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics and Recent Bookings -->
        <div class="col-xl-8 col-lg-7">
            <!-- Statistics -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Lesson Statistics</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-sm me-2 bg-label-primary">
                                    <i class="bx bx-book"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0">{{ $stats['total_lessons'] }}</h6>
                                    <small>Total Lessons</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-sm me-2 bg-label-success">
                                    <i class="bx bx-check-circle"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0">{{ $stats['completed_lessons'] }}</h6>
                                    <small>Completed</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-sm me-2 bg-label-warning">
                                    <i class="bx bx-time"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0">{{ $stats['upcoming_lessons'] }}</h6>
                                    <small>Upcoming</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-sm me-2 bg-label-danger">
                                    <i class="bx bx-x-circle"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0">{{ $stats['cancelled_lessons'] }}</h6>
                                    <small>Cancelled</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Bookings -->
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h5 class="card-title mb-0">Recent Bookings</h5>
                    <a href="{{ route('instructor.clients.bookings', $client) }}" class="btn btn-sm btn-primary">
                        View All
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Date & Time</th>
                                <th>Service</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($bookings->take(5) as $booking)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $booking->date->format('d M Y') }}</div>
                                    <small class="text-muted">{{ $booking->formatted_time }}</small>
                                </td>
                                <td>
                                    <span class="fw-semibold">{{ $booking->service->name }}</span>
                                    <br>
                                    <small class="text-muted">{{ $booking->duration }} mins</small>
                                </td>
                                <td>
                                    <span class="badge bg-label-{{ $booking->status_color }}">
                                        {{ ucfirst($booking->status) }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('instructor.bookings.show', $booking) }}" 
                                       class="btn btn-sm btn-icon btn-outline-primary">
                                        <i class="bx bx-show"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-3">
                                    <p class="text-muted mb-0">No bookings found</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection