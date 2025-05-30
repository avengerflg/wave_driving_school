@extends('layouts.student')

@section('title', 'Booking Details')

@section('content')
@php
    $statusClasses = [
        'pending' => ['label' => 'warning', 'icon' => 'bx-time', 'text' => 'Pending', 'btn_outline' => 'warning'],
        'confirmed' => ['label' => 'info', 'icon' => 'bx-calendar-check', 'text' => 'Confirmed', 'btn_outline' => 'info'],
        'completed' => ['label' => 'success', 'icon' => 'bx-check-double', 'text' => 'Completed', 'btn_outline' => 'success'],
        'cancelled' => ['label' => 'danger', 'icon' => 'bx-x-circle', 'text' => 'Cancelled', 'btn_outline' => 'danger'],
    ];
    $currentBookingStatusInfo = $statusClasses[$booking->status] ?? $statusClasses['pending'];
@endphp

<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Header Card with Page Title -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="fw-bold m-0">
                                <i class="bx bx-detail text-primary me-2"></i>
                                <span class="text-muted fw-light">My Bookings /</span> Booking Details
                            </h4>
                            <div>
                                <a href="{{ route('student.bookings.index') }}" class="btn btn-secondary me-2">
                                    <i class="bx bx-arrow-back me-1"></i> Back to My Bookings
                                </a>
                                @if(in_array($booking->status, ['pending', 'confirmed']))
                                <a href="/" class="btn btn-warning">
                                    <i class="bx bx-edit me-1"></i> Reschedule
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Left Column - Status and Time/Location -->
            <div class="col-xl-4 col-lg-5 col-md-12">
                <!-- Booking status card -->
                <div class="card mb-4">
                    <div class="card-header bg-transparent">
                        <h5 class="card-title mb-0">Booking Status</h5>
                    </div>
                    <div class="card-body text-center">
                        <div class="d-flex align-items-center flex-column pt-2">
                            <div class="avatar avatar-xl mb-3">
                                <span class="avatar-initial rounded-circle bg-label-{{ $currentBookingStatusInfo['label'] }}">
                                    <i class="bx {{ $currentBookingStatusInfo['icon'] }} fs-3"></i>
                                </span>
                            </div>
                            <h3 class="fw-bold text-center mb-1">
                                #{{ $booking->id }}
                            </h3>
                            <span class="badge bg-label-{{ $currentBookingStatusInfo['label'] }} mb-3 px-3 py-2">
                                <span class="fs-6">{{ ucfirst($currentBookingStatusInfo['text']) }}</span>
                            </span>
                            
                            @if($booking->status === 'pending')
                                <div class="alert alert-warning text-center mt-2">
                                    <i class="bx bx-info-circle me-1"></i>
                                    Your booking is pending confirmation. You'll receive an email once it's confirmed.
                                </div>
                            @elseif($booking->status === 'confirmed')
                                <div class="alert alert-info text-center mt-2">
                                    <i class="bx bx-check-circle me-1"></i>
                                    Your booking is confirmed! See you on {{ $booking->date->format('M d, Y') }}.
                                </div>
                            @elseif($booking->status === 'completed')
                                <div class="alert alert-success text-center mt-2">
                                    <i class="bx bx-trophy me-1"></i>
                                    Lesson completed! How did it go?
                                </div>
                            @elseif($booking->status === 'cancelled')
                                <div class="alert alert-danger text-center mt-2">
                                    <i class="bx bx-x-circle me-1"></i>
                                    This booking has been cancelled.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Time and location card -->
                <div class="card mb-4">
                    <div class="card-header d-flex align-items-center">
                        <i class="bx bx-map-pin text-primary me-2"></i>
                        <h5 class="card-title mb-0">Time & Location</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="d-flex align-items-center mb-2">
                                <div class="flex-shrink-0">
                                    <span class="badge bg-label-primary rounded p-2">
                                        <i class="bx bx-calendar fs-5"></i>
                                    </span>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-0">Date</h6>
                                    <p class="mb-0 text-muted">{{ $booking->date ? $booking->date->format('l, d M Y') : 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="d-flex align-items-center mb-2">
                                <div class="flex-shrink-0">
                                    <span class="badge bg-label-primary rounded p-2">
                                        <i class="bx bx-time fs-5"></i>
                                    </span>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-0">Time</h6>
                                    <p class="mb-0 text-muted">{{ $booking->start_time ? \Carbon\Carbon::parse($booking->start_time)->format('h:i A') : 'N/A' }} - {{ $booking->end_time ? \Carbon\Carbon::parse($booking->end_time)->format('h:i A') : 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="d-flex align-items-center mb-2">
                                <div class="flex-shrink-0">
                                    <span class="badge bg-label-primary rounded p-2">
                                        <i class="bx bx-building-house fs-5"></i>
                                    </span>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-0">Location</h6>
                                    <p class="mb-0 text-muted">{{ $booking->suburb->name ?? 'N/A' }}</p>
                                    @if($booking->address)
                                        <small class="text-muted">{{ $booking->address }}</small>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Quick Actions Card (Mobile) -->
                <div class="card d-block d-xl-none">
                    <div class="card-header d-flex align-items-center">
                        <i class="bx bx-cog text-primary me-2"></i>
                        <h5 class="card-title mb-0">Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-2">
                            @if(in_array($booking->status, ['pending', 'confirmed']))
                            <div class="col-6">
                                <a href="/" class="btn btn-warning w-100 btn-sm">
                                    <i class="bx bx-edit me-1"></i> Reschedule
                                </a>
                            </div>
                            <div class="col-6">
                                <form action="{{ route('student.bookings.cancel', $booking->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="btn btn-danger w-100 btn-sm" onclick="return confirm('Are you sure you want to cancel this booking?')">
                                        <i class="bx bx-x me-1"></i> Cancel
                                    </button>
                                </form>
                            </div>
                            @endif
                            @if($booking->status === 'completed')
                            <div class="col-12">
                                <button type="button" class="btn btn-success w-100 btn-sm" data-bs-toggle="modal" data-bs-target="#feedbackModal">
                                    <i class="bx bx-star me-1"></i> Leave Feedback
                                </button>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Main Content -->
            <div class="col-xl-8 col-lg-7 col-md-12">
                <!-- Booking Information Card -->
                <div class="card mb-4">
                    <div class="card-header d-flex align-items-center">
                        <i class="bx bx-info-circle text-primary me-2"></i>
                        <h5 class="card-title mb-0">Booking Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h6 class="text-muted fw-semibold mb-2">SERVICE DETAILS</h6>
                                <div class="card bg-lighter shadow-sm border-0 mb-3">
                                    <div class="card-body p-3">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm me-3 bg-label-dark rounded">
                                                <i class="bx bx-car fs-5"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $booking->service->name ?? 'N/A' }}</h6>
                                                <small class="text-muted">Driving Lesson</small>
                                            </div>
                                        </div>
                                        
                                        @if($booking->service && $booking->service->price)
                                        <div class="mt-2 pt-2 border-top">
                                            <div class="d-flex justify-content-between">
                                                <small class="text-muted">Price:</small>
                                                <span class="fw-semibold">${{ number_format($booking->service->price, 2) }}</span>
                                            </div>
                                            @if($booking->service->duration)
                                            <div class="d-flex justify-content-between">
                                                <small class="text-muted">Duration:</small>
                                                <span class="fw-semibold">{{ $booking->service->duration }} minutes</span>
                                            </div>
                                            @endif
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <h6 class="text-muted fw-semibold mb-2">BOOKING DETAILS</h6>
                                <div class="card bg-lighter shadow-sm border-0">
                                    <div class="card-body p-3">
                                        <div class="d-flex mb-2">
                                            <small class="text-muted me-2">Booking ID:</small>
                                            <small class="fw-semibold">#{{ $booking->id }}</small>
                                        </div>
                                        <div class="d-flex mb-2">
                                            <small class="text-muted me-2">Booked on:</small>
                                            <small>{{ $booking->created_at->format('M d, Y') }}</small>
                                        </div>
                                        <div class="d-flex mb-2">
                                            <small class="text-muted me-2">Booking for:</small>
                                            <small class="fw-semibold">{{ ucfirst($booking->booking_for ?? 'self') }}</small>
                                        </div>
                                        @if($booking->booking_for === 'other' && $booking->other_name)
                                        <div class="d-flex">
                                            <small class="text-muted me-2">Student:</small>
                                            <small class="fw-semibold">{{ $booking->other_name }}</small>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-4 mb-md-0">
                                <h6 class="text-muted fw-semibold mb-2">YOUR INSTRUCTOR</h6>
                                <div class="card bg-lighter shadow-sm border-0">
                                    <div class="card-body p-3">
                                        @if($booking->instructor)
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="avatar avatar-md me-3">
                                                @if($booking->instructor->avatar)
                                                    <img src="{{ $booking->instructor->avatar }}" alt="{{ $booking->instructor->name }}" class="rounded-circle">
                                                @else
                                                    <span class="avatar-initial rounded-circle bg-info">
                                                        {{ $booking->instructor->user->name[0] ?? 'I' }}</span>
                                                    </span>
                                                @endif
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{  $booking->instructor->user->name }}</h6>
                                                <small class="text-muted">Licensed Instructor</small>
                                            </div>
                                        </div>
                                        
                                        @if($booking->instructor->phone)
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="bx bx-phone text-muted me-2"></i>
                                            <span>{{ $booking->instructor->phone }}</span>
                                        </div>
                                        @endif
                                        
                                        @if($booking->instructor->email)
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="bx bx-envelope text-muted me-2"></i>
                                            <span>{{ $booking->instructor->email }}</span>
                                        </div>
                                        @endif
                                        
                                        @if($booking->status === 'confirmed')
                                        <div class="mt-3">
                                            <a href="tel:{{ $booking->instructor->phone }}" class="btn btn-sm btn-outline-primary me-2">
                                                <i class="bx bx-phone me-1"></i> Call
                                            </a>
                                            <a href="mailto:{{ $booking->instructor->email }}" class="btn btn-sm btn-outline-secondary">
                                                <i class="bx bx-envelope me-1"></i> Email
                                            </a>
                                        </div>
                                        @endif
                                        @else
                                        <div class="text-center py-3">
                                            <i class="bx bx-user-plus text-muted fs-2 mb-2"></i>
                                            <p class="text-muted mb-0">Instructor will be assigned soon</p>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <h6 class="text-muted fw-semibold mb-2">ADDITIONAL INFORMATION</h6>
                                <div class="card bg-lighter shadow-sm border-0">
                                    <div class="card-body p-3">
                                        @if($booking->notes)
                                        <div class="mb-3">
                                            <h6 class="mb-1">Notes</h6>
                                            <p class="text-muted small mb-0">{{ $booking->notes }}</p>
                                        </div>
                                        @endif
                                        
                                        <div class="mb-2">
                                            <small class="text-muted">Confirmation sent:</small>
                                            <span class="badge bg-label-{{ $booking->confirmation_sent ? 'success' : 'secondary' }} ms-1">
                                                {{ $booking->confirmation_sent ? 'Yes' : 'No' }}
                                            </span>
                                        </div>
                                        
                                        @if($booking->status === 'confirmed')
                                        <div class="mt-3 p-2 bg-info-subtle rounded">
                                            <small class="text-info">
                                                <i class="bx bx-info-circle me-1"></i>
                                                You'll receive reminder notifications before your lesson.
                                            </small>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Actions Card (Desktop) -->
                <div class="card d-none d-xl-block">
                    <div class="card-header d-flex align-items-center">
                        <i class="bx bx-cog text-primary me-2"></i>
                        <h5 class="card-title mb-0">Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @if(in_array($booking->status, ['pending', 'confirmed']))
                            <div class="col-md-6 mb-3 mb-md-0">
                                <div class="d-grid gap-2">
                                    <a href="/" class="btn btn-warning">
                                        <i class="bx bx-edit me-1"></i> Reschedule Booking
                                    </a>
                                    <a href="{{ route('student.bookings.index') }}" class="btn btn-secondary">
                                        <i class="bx bx-list-ul me-1"></i> My Bookings
                                    </a>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <form action="{{ route('student.bookings.cancel', $booking->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to cancel this booking?')">
                                            <i class="bx bx-x me-1"></i> Cancel Booking
                                        </button>
                                    </div>
                                </form>
                            </div>
                            @elseif($booking->status === 'completed')
                            <div class="col-md-6 mb-3 mb-md-0">
                                <div class="d-grid gap-2">
                                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#feedbackModal">
                                        <i class="bx bx-star me-1"></i> Leave Feedback
                                    </button>
                                    <a href="{{ route('student.bookings.create') }}" class="btn btn-primary">
                                        <i class="bx bx-plus me-1"></i> Book Another Lesson
                                    </a>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-grid">
                                    <a href="{{ route('student.bookings.index') }}" class="btn btn-secondary">
                                        <i class="bx bx-list-ul me-1"></i> My Bookings
                                    </a>
                                </div>
                            </div>
                            @else
                            <div class="col-12">
                                <div class="d-grid">
                                    <a href="{{ route('student.bookings.index') }}" class="btn btn-secondary">
                                        <i class="bx bx-list-ul me-1"></i> Back to My Bookings
                                    </a>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Feedback Modal -->
@if($booking->status === 'completed')
<div class="modal fade" id="feedbackModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Leave Feedback</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('student.bookings.feedback', $booking->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Rating</label>
                        <div class="rating">
                            @for($i = 5; $i >= 1; $i--)
                            <input type="radio" name="rating" value="{{ $i }}" id="star{{ $i }}">
                            <label for="star{{ $i }}">★</label>
                            @endfor
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="feedback" class="form-label">Your Feedback</label>
                        <textarea class="form-control" id="feedback" name="feedback" rows="4" placeholder="How was your lesson?"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Submit Feedback</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@endsection

@push('styles')
<style>
.rating {
    display: flex;
    flex-direction: row-reverse;
    justify-content: center;
}

.rating input {
    display: none;
}

.rating label {
    font-size: 2rem;
    color: #ddd;
    cursor: pointer;
    transition: color 0.2s;
}

.rating label:hover,
.rating label:hover ~ label,
.rating input:checked ~ label {
    color: #ffc107;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Auto-hide alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        }, 5000);
    });
});
</script>
@endpush
