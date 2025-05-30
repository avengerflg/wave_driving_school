@extends('layouts.instructor')

@section('title', 'Package Lessons')

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold m-0">
                <span class="text-muted fw-light">Instructor / Packages /</span> Lessons
            </h4>
            <div>
                <a href="{{ route('instructor.packages.index') }}" class="btn btn-outline-secondary">
                    <i class="bx bx-arrow-back me-1"></i> Back to Packages
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Package-Based Lessons</h5>
                <div>
                    <small class="text-muted">Lessons booked using package credits</small>
                </div>
            </div>
            <div class="table-responsive text-nowrap">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Booking ID</th>
                            <th>Student</th>
                            <th>Date & Time</th>
                            <th>Service</th>
                            <th>Package</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse($bookings as $booking)
                        <tr>
                            <td><strong>#{{ $booking->id }}</strong></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm me-2">
                                        <span class="avatar-initial rounded-circle bg-label-primary">
                                            {{ substr($booking->user->name ?? 'U', 0, 1) }}
                                        </span>
                                    </div>
                                    <div>
                                        <a href="{{ route('instructor.clients.show', $booking->user_id) }}">
                                            {{ $booking->user->name ?? 'Unknown User' }}
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
                                <span class="badge bg-label-primary">
                                    {{ $booking->packageCredit->package->name ?? 'Unknown Package' }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $booking->status === 'completed' ? 'success' : ($booking->status === 'cancelled' ? 'danger' : ($booking->status === 'confirmed' ? 'info' : 'warning')) }}">
                                    {{ ucfirst($booking->status) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('instructor.bookings.show', $booking->id) }}" class="btn btn-sm btn-primary">
                                    <i class="bx bx-show"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-3">
                                <div class="d-flex flex-column align-items-center py-4">
                                    <i class="bx bx-calendar text-secondary mb-2" style="font-size: 3rem;"></i>
                                    <h5 class="mb-1">No Package Lessons Found</h5>
                                    <p class="text-muted">No lessons have been booked using package credits yet</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($bookings->count() > 0)
            <div class="card-footer">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        Showing {{ $bookings->firstItem() ?? 0 }} to {{ $bookings->lastItem() ?? 0 }} of {{ $bookings->total() }} bookings
                    </div>
                    <div>
                        {{ $bookings->links() }}
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection