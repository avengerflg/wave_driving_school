@extends('layouts.instructor')

@section('title', 'My Bookings')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="fw-bold mb-0">
                            <i class="bx bx-calendar-check text-primary me-2"></i>
                            My Bookings
                        </h4>
                        <a href="{{ route('instructor.calendar') }}" class="btn btn-primary">
                            <i class="bx bx-calendar me-1"></i> View Calendar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('instructor.bookings.index') }}">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bx bx-search"></i></span>
                                    <input type="text" name="search" class="form-control" placeholder="Search bookings..." value="{{ request('search') }}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <select name="status" class="form-select">
                                    <option value="all">All Status</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <input type="date" name="date_from" class="form-control" placeholder="From Date" value="{{ request('date_from') }}">
                            </div>
                            <div class="col-md-2">
                                <input type="date" name="date_to" class="form-control" placeholder="To Date" value="{{ request('date_to') }}">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bx bx-filter-alt me-1"></i> Filter
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bookings Table -->
    <div class="card">
        <h5 class="card-header">Booking List</h5>
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Service</th>
                        <th>Date & Time</th>
                        <th>Location</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse($bookings as $booking)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-sm me-2">
                                    <span class="avatar-initial rounded-circle bg-primary">
                                        {{ substr($booking->user->name, 0, 1) }}
                                    </span>
                                </div>
                                <div>
                                    <h6 class="mb-0">{{ $booking->user->name }}</h6>
                                    <small class="text-muted">{{ $booking->user->email }}</small>
                                </div>
                            </div>
                        </td>
                        <td>{{ $booking->service->name }}</td>
                        <td>
                            <div class="d-flex flex-column">
                                <span><i class="bx bx-calendar text-primary me-1"></i>{{ $booking->date->format('d M Y') }}</span>
                                <small class="text-muted">
                                    <i class="bx bx-time-five me-1"></i>
                                    {{ $booking->start_time->format('H:i') }} - {{ $booking->end_time->format('H:i') }}
                                </small>
                            </div>
                        </td>
                        <td>{{ $booking->suburb->name }}</td>
                        <td>
                            @php
                                $statusColor = [
                                    'pending' => 'warning',
                                    'confirmed' => 'success',
                                    'completed' => 'info',
                                    'cancelled' => 'danger'
                                ][$booking->status] ?? 'secondary';
                            @endphp
                            <span class="badge bg-label-{{ $statusColor }} me-1">{{ ucfirst($booking->status) }}</span>
                        </td>
                        <td>
                            <div class="dropdown">
                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                    <i class="bx bx-dots-vertical-rounded"></i>
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="{{ route('instructor.bookings.show', $booking) }}">
                                        <i class="bx bx-show-alt me-1"></i> View Details
                                    </a>
                                    @if($booking->status != 'cancelled' && $booking->status != 'completed')
                                    <a class="dropdown-item" href="{{ route('instructor.bookings.reschedule.form', $booking) }}">
                                        <i class="bx bx-calendar-edit me-1 text-warning"></i> Reschedule
                                    </a>
                                    @endif
                                    @if($booking->status == 'pending')
                                    <form action="{{ route('instructor.bookings.update-status', $booking) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="status" value="confirmed">
                                        <button type="submit" class="dropdown-item">
                                            <i class="bx bx-check me-1 text-success"></i> Confirm Booking
                                        </button>
                                    </form>
                                    @endif
                                    @if($booking->status == 'confirmed')
                                    <form action="{{ route('instructor.bookings.update-status', $booking) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="status" value="completed">
                                        <button type="submit" class="dropdown-item">
                                            <i class="bx bx-check-double me-1 text-info"></i> Mark as Completed
                                        </button>
                                    </form>
                                    @endif
                                    @if($booking->status != 'cancelled' && $booking->status != 'completed')
                                    <form action="{{ route('instructor.bookings.update-status', $booking) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="status" value="cancelled">
                                        <button type="submit" class="dropdown-item" 
                                                onclick="return confirm('Are you sure you want to cancel this booking?')">
                                            <i class="bx bx-x me-1 text-danger"></i> Cancel Booking
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <div class="d-flex flex-column align-items-center">
                                <img src="{{ asset('assets/img/illustrations/empty.png') }}" alt="No Bookings" class="mb-3" style="height: 140px;">
                                <h5 class="fw-semibold mb-1">No Bookings Found</h5>
                                <p class="text-muted mb-3">No bookings match your current filters.</p>
                                <a href="{{ route('instructor.bookings.index') }}" class="btn btn-sm btn-primary">
                                    <i class="bx bx-reset me-1"></i> Clear Filters
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($bookings->hasPages())
        <div class="card-footer d-flex justify-content-center pt-4">
            {{ $bookings->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

@section('page-scripts')
<style>
    .badge.bg-label-info {
        background-color: rgba(3, 195, 236, 0.16) !important;
        color: #03c3ec !important;
    }
    
    .table-responsive {
        min-height: 300px;
    }
    
    .dropdown-item:active {
        color: inherit;
        background-color: rgba(67, 89, 113, 0.04);
    }
    
    .avatar-initial {
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.8rem;
        font-weight: 600;
    }
</style>
@endsection