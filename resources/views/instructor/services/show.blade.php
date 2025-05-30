@extends('layouts.instructor')

@section('title', $service->name)

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <a href="{{ route('instructor.services.index') }}" class="btn btn-icon btn-outline-primary me-3">
                            <i class="bx bx-arrow-back"></i>
                        </a>
                        <div>
                            <h4 class="fw-bold mb-0">{{ $service->name }}</h4>
                            @if($service->featured)
                            <span class="badge bg-label-warning">Featured</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Service Details -->
        <div class="col-xl-8 col-lg-7">
            <!-- Service Info -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Service Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">Price:</label>
                            <p class="text-primary h5">{{ $service->formatted_price }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">Duration:</label>
                            <p>{{ $service->formatted_duration }}</p>
                        </div>
                        <div class="col-12">
                            <label class="fw-bold">Description:</label>
                            <p>{{ $service->description ?: 'No description available.' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Bookings -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Recent Bookings</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Client</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($bookings as $booking)
                            <tr>
                                <td>{{ $booking->user->name }}</td>
                                <td>{{ $booking->date->format('d M Y') }}</td>
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
                                <td colspan="4" class="text-center py-3">No bookings found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Stats -->
        <div class="col-xl-4 col-lg-5">
            <div class="row g-4">
                <div class="col-xl-12 col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="mb-1">Total Bookings</h6>
                                    <h4 class="mb-0">{{ $stats['total_bookings'] }}</h4>
                                </div>
                                <div class="avatar">
                                    <span class="avatar-initial rounded bg-label-primary">
                                        <i class="bx bx-calendar"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-12 col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="mb-1">Completed Lessons</h6>
                                    <h4 class="mb-0">{{ $stats['completed_bookings'] }}</h4>
                                </div>
                                <div class="avatar">
                                    <span class="avatar-initial rounded bg-label-success">
                                        <i class="bx bx-check-circle"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-12 col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="mb-1">Total Revenue</h6>
                                    <h4 class="mb-0">${{ number_format($stats['revenue'], 2) }}</h4>
                                </div>
                                <div class="avatar">
                                    <span class="avatar-initial rounded bg-label-info">
                                        <i class="bx bx-dollar"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection