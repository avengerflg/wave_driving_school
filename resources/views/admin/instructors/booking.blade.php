@extends('layouts.admin')

@section('title', 'Instructor Bookings')

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
                                <i class="bx bx-calendar text-primary me-2"></i>
                                <span class="text-muted fw-light">Admin > Instructors > </span> {{ $instructor->name }}'s Bookings
                            </h4>
                            <div>
                                <a href="{{ route('admin.instructors.show', $instructor->id) }}" class="btn btn-secondary">
                                    <i class="bx bx-arrow-back me-1"></i> Back to Instructor
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bookings List -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">All Bookings</h5>
            </div>
            <div class="table-responsive text-nowrap">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Student</th>
                            <th>Service</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bookings as $booking)
                        <tr>
                            <td>#{{ $booking->id }}</td>
                            <td>{{ $booking->date->format('M d, Y') }}</td>
                            <td>{{ date('h:i A', strtotime($booking->start_time)) }} - {{ date('h:i A', strtotime($booking->end_time)) }}</td>
                            <td>
                                @if($booking->user)
                                <a href="{{ route('admin.users.show', $booking->user->id) }}">{{ $booking->user->name }}</a>
                                @else
                                <span class="text-muted">Deleted User</span>
                                @endif
                            </td>
                            <td>{{ $booking->service->name ?? 'N/A' }}</td>
                            <td>
                                <span class="badge bg-label-{{ $booking->status == 'confirmed' ? 'success' : ($booking->status == 'pending' ? 'warning' : 'danger') }}">
                                    {{ ucfirst($booking->status) }}
                                </span>
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn btn-sm dropdown-toggle hide-arrow p-0" data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a href="{{ route('admin.bookings.show', $booking->id) }}" class="dropdown-item">
                                            <i class="bx bx-show me-1"></i> View Details
                                        </a>
                                        @if(!in_array($booking->status, ['completed', 'cancelled']))
                                        <a href="{{ route('instructor.bookings.reschedule.form', $booking) }}" class="dropdown-item">
                                            <i class="bx bx-calendar-edit me-1"></i> Reschedule
                                        </a>
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">No bookings found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                {{ $bookings->links() }}
            </div>
        </div>
    </div>
</div>
@endsection