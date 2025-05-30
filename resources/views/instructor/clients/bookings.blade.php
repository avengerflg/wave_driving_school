@extends('layouts.instructor')

@section('title', "{$client->name}'s Bookings")

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <a href="{{ route('instructor.clients.show', $client) }}" class="btn btn-icon btn-outline-primary me-3">
                                <i class="bx bx-arrow-back"></i>
                            </a>
                            <div>
                                <h4 class="fw-bold m-0">{{ $client->name }}'s Bookings</h4>
                                <small class="text-muted">Showing all lessons history</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bookings List -->
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Booking ID</th>
                        <th>Date & Time</th>
                        <th>Service</th>
                        <th>Location</th>
                        <th>Status</th>
                        <th>Payment</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse($bookings as $booking)
                    <tr>
                        <td>
                            <strong>#{{ $booking->id }}</strong>
                        </td>
                        <td>
                            <div class="fw-semibold">{{ $booking->date->format('d M Y') }}</div>
                            <small class="text-muted">
                                {{ Carbon\Carbon::parse($booking->start_time)->format('h:i A') }} - 
                                {{ Carbon\Carbon::parse($booking->end_time)->format('h:i A') }}
                            </small>
                        </td>
                        <td>
                            <div class="fw-semibold">{{ $booking->service->name }}</div>
                            <small class="text-muted">{{ $booking->duration }} minutes</small>
                        </td>
                        <td>
                            @if($booking->suburb)
                                {{ $booking->suburb->name }}
                            @else
                                <span class="text-muted">Not specified</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-label-{{ $booking->status_color }}">
                                {{ ucfirst($booking->status) }}
                            </span>
                        </td>
                        <td>
                            @if($booking->payment)
                                <span class="badge bg-label-success">Paid</span>
                            @else
                                <span class="badge bg-label-warning">Pending</span>
                            @endif
                        </td>
                        <td>
                            <div class="dropdown">
                                <button type="button" class="btn btn-sm dropdown-toggle hide-arrow p-0" data-bs-toggle="dropdown">
                                    <i class="bx bx-dots-vertical-rounded"></i>
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="{{ route('instructor.bookings.show', $booking) }}">
                                        <i class="bx bx-show-alt me-1"></i> View Details
                                    </a>
                                    @if($booking->status === 'scheduled')
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item text-success" href="{{ route('instructor.bookings.complete', $booking) }}">
                                        <i class="bx bx-check-circle me-1"></i> Mark Complete
                                    </a>
                                    <a class="dropdown-item text-danger" href="{{ route('instructor.bookings.cancel', $booking) }}">
                                        <i class="bx bx-x-circle me-1"></i> Cancel Booking
                                    </a>
                                    @endif
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            <div class="text-center">
                                <i class="bx bx-calendar-x text-secondary mb-2" style="font-size: 3rem;"></i>
                                <h6 class="mb-0 text-secondary">No bookings found</h6>
                                <p class="text-muted mb-0">This client hasn't booked any lessons yet</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($bookings->hasPages())
        <div class="card-footer">
            <div class="d-flex justify-content-between align-items-center">
                <div class="pagination-info">
                    Showing {{ $bookings->firstItem() }} to {{ $bookings->lastItem() }} of {{ $bookings->total() }} bookings
                </div>
                {{ $bookings->links() }}
            </div>
        </div>
        @endif
    </div>
</div>
@endsection