@extends('layouts.instructor')

@section('title', $suburb->name)

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <a href="{{ route('instructor.suburbs.index') }}" class="btn btn-icon btn-outline-primary me-3">
                            <i class="bx bx-arrow-back"></i>
                        </a>
                        <div>
                            <h4 class="fw-bold mb-0">{{ $suburb->name }}</h4>
                            <p class="text-muted mb-0">{{ $suburb->state }} {{ $suburb->postcode }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Statistics -->
        <div class="col-xl-4 col-lg-5 mb-4">
            <div class="row g-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-3">
                                <h5 class="card-title mb-0">Statistics</h5>
                            </div>
                            <div class="d-flex mb-3">
                                <div class="avatar avatar-sm me-3 bg-label-primary">
                                    <i class="bx bx-calendar"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0">Total Bookings</h6>
                                    <h4 class="mb-0">{{ $stats['total_bookings'] }}</h4>
                                </div>
                            </div>
                            <div class="d-flex mb-3">
                                <div class="avatar avatar-sm me-3 bg-label-success">
                                    <i class="bx bx-check-circle"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0">Completed Lessons</h6>
                                    <h4 class="mb-0">{{ $stats['completed_bookings'] }}</h4>
                                </div>
                            </div>
                            <div class="d-flex">
                                <div class="avatar avatar-sm me-3 bg-label-info">
                                    <i class="bx bx-dollar"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0">Total Revenue</h6>
                                    <h4 class="mb-0">${{ number_format($stats['revenue'], 2) }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Bookings -->
        <div class="col-xl-8 col-lg-7">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Recent Bookings in {{ $suburb->name }}</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
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
                                <td>{{ $booking->user->name }}</td>
                                <td>{{ $booking->service->name }}</td>
                                <td>{{ $booking->date->format('M d, Y') }}</td>
                                <td>
                                    <span class="badge bg-label-{{ $booking->status_color }}">
                                        {{ ucfirst($booking->status) }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('instructor.bookings.show', $booking) }}" 
                                       class="btn btn-sm btn-primary">
                                        View
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-3">
                                    <span class="text-muted">No bookings found</span>
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