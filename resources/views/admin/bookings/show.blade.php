
@extends('layouts.admin')

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
                                <span class="text-muted fw-light">Admin /</span> Booking Details
                            </h4>
                            <div>
                                <a href="{{ route('admin.bookings.index') }}" class="btn btn-secondary me-2">
                                    <i class="bx bx-arrow-back me-1"></i> Back
                                </a>
                                <a href="{{ route('admin.bookings.edit', $booking->id) }}" class="btn btn-warning">
                                    <i class="bx bx-edit me-1"></i> Edit
                                </a>
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
                            
                            <div class="d-flex justify-content-center align-items-center mt-2 gap-2 flex-wrap">
                                @foreach ($statusClasses as $statusKey => $statusInfo)
                                    @if ($statusKey === 'completed' && !in_array($booking->status, ['confirmed'])) {{-- Optionally show complete only if confirmed --}}
                                        {{-- continue; --}}
                                    @endif
                                    <form action="{{ route('admin.bookings.update-status', $booking->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="status" value="{{ $statusKey }}">
                                        <button type="submit" class="btn btn-sm btn-outline-{{ $statusInfo['btn_outline'] }} {{ $booking->status == $statusKey ? 'active' : '' }}">
                                            <i class="bx {{ $statusInfo['icon'] }} me-1"></i> {{ $statusInfo['text'] }}
                                        </button>
                                    </form>
                                @endforeach
                            </div>
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
                                    <p class="mb-0 text-muted">{{ $booking->date ? $booking->date->format('d M Y') : 'N/A' }}</p>
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
                        
                        <div>
                            <div class="d-flex align-items-center mb-2">
                                <div class="flex-shrink-0">
                                    <span class="badge bg-label-primary rounded p-2">
                                        <i class="bx bx-building-house fs-5"></i>
                                    </span>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-0">Location</h6>
                                    <p class="mb-0 text-muted">{{ $booking->suburb->name ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Actions Card (Moved to left column for mobile accessibility) -->
                <div class="card d-block d-xl-none"> <!-- Only show on mobile -->
                    <div class="card-header d-flex align-items-center">
                        <i class="bx bx-cog text-primary me-2"></i>
                        <h5 class="card-title mb-0">Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-2">
                            <div class="col-6">
                                <button type="button" class="btn btn-primary w-100 btn-sm edit-booking-btn" 
                                        data-id="{{ $booking->id }}" data-bs-toggle="modal" data-bs-target="#editBookingModal">
                                    <i class="bx bx-edit me-1"></i> Edit
                                </button>
                            </div>
                            <div class="col-6">
                                <form action="{{ route('admin.bookings.destroy', $booking->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger w-100 btn-sm" onclick="return confirm('Are you sure you want to delete this booking?')">
                                        <i class="bx bx-trash me-1"></i> Delete
                                    </button>
                                </form>
                            </div>
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
                                                <small class="text-muted">Service Type</small>
                                            </div>
                                        </div>
                                        
                                        @if($booking->service && $booking->service->price)
                                        <div class="mt-2 pt-2 border-top">
                                            <div class="d-flex justify-content-between">
                                                <small class="text-muted">Price:</small>
                                                <span class="fw-semibold">${{ number_format($booking->service->price, 2) }}</span>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <h6 class="text-muted fw-semibold mb-2">BOOKING METADATA</h6>
                                <div class="card bg-lighter shadow-sm border-0">
                                    <div class="card-body p-3">
                                        <div class="d-flex mb-2">
                                            <small class="text-muted me-2">Booking ID:</small>
                                            <small class="fw-semibold">{{ $booking->id }}</small>
                                        </div>
                                        <div class="d-flex mb-2">
                                            <small class="text-muted me-2">Created:</small>
                                            <small>{{ $booking->created_at->format('M d, Y h:i A') }}</small>
                                        </div>
                                        <div class="d-flex">
                                            <small class="text-muted me-2">Last Updated:</small>
                                            <small>{{ $booking->updated_at->format('M d, Y h:i A') }}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-4 mb-md-0">
                                <h6 class="text-muted fw-semibold mb-2">USER DETAILS</h6>
                                <div class="card bg-lighter shadow-sm border-0">
                                    <div class="card-body p-3">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="avatar avatar-md me-3 bg-primary rounded-circle">
                                                <span class="avatar-initial">{{ substr($booking->user->name ?? 'NA', 0, 1) }}</span>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $booking->user->name ?? 'N/A' }}</h6>
                                                <small class="text-muted">{{ $booking->user->email ?? 'N/A' }}</small>
                                            </div>
                                        </div>
                                        
                                        @if($booking->user && $booking->user->phone)
                                        <div class="d-flex align-items-center mb-1">
                                            <i class="bx bx-phone text-muted me-2"></i>
                                            <span>{{ $booking->user->phone }}</span>
                                        </div>
                                        @endif
                                        
                                        @if($booking->user)
                                        <a href="{{ route('admin.clients.show', $booking->user->id) }}" class="btn btn-sm btn-outline-primary mt-2">
                                            <i class="bx bx-user-circle me-1"></i> View User Profile
                                        </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <h6 class="text-muted fw-semibold mb-2">INSTRUCTOR DETAILS</h6>
                                <div class="card bg-lighter shadow-sm border-0">
                                    <div class="card-body p-3">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="avatar avatar-md me-3 bg-info rounded-circle">
                                                <span class="avatar-initial">{{ substr($booking->instructor->user->name ?? 'NA', 0, 1) }}</span>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $booking->instructor->user->name ?? 'N/A' }}</h6>
                                                <small class="text-muted">{{ $booking->instructor->user->email ?? 'N/A' }}</small>
                                            </div>
                                        </div>
                                        
                                        @if($booking->instructor && $booking->instructor->user && $booking->instructor->user->phone)
                                        <div class="d-flex align-items-center mb-1">
                                            <i class="bx bx-phone text-muted me-2"></i>
                                            <span>{{ $booking->instructor->user->phone }}</span>
                                        </div>
                                        @endif
                                        
                                        @if($booking->instructor)
                                        <a href="{{ route('admin.instructors.show', $booking->instructor->id) }}" class="btn btn-sm btn-outline-info mt-2">
                                            <i class="bx bx-user-voice me-1"></i> View Instructor Profile
                                        </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Actions Card -->
                <div class="card d-none d-xl-block"> <!-- Only show on desktop -->
                    <div class="card-header d-flex align-items-center">
                        <i class="bx bx-cog text-primary me-2"></i>
                        <h5 class="card-title mb-0">Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <div class="d-grid gap-2">
                                    <a href="{{ route('admin.bookings.edit', $booking->id) }}" class="btn btn-primary">
                                        <i class="bx bx-edit me-1"></i> Edit Booking
                                    </a>
                                    <a href="{{ route('admin.bookings.index') }}" class="btn btn-secondary">
                                        <i class="bx bx-list-ul me-1"></i> All Bookings
                                    </a>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <form action="{{ route('admin.bookings.destroy', $booking->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this booking?')">
                                            <i class="bx bx-trash me-1"></i> Delete Booking
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Optional: If you have specific JS logic for the edit button or modal, it would go here.
    // For example, if you needed to dynamically load data into the modal based on the data-id.
    // const editBookingButtons = document.querySelectorAll('.edit-booking-btn');
    // const editBookingModal = new bootstrap.Modal(document.getElementById('editBookingModal'));
    // const editBookingForm = document.getElementById('editBookingForm');

    // editBookingButtons.forEach(button => {
    //     button.addEventListener('click', function () {
    //         const bookingId = this.dataset.id;
    //         // If your form action isn't already set by Blade, or you need to fetch data:
    //         // editBookingForm.action = '{{ url("admin/bookings") }}/' + bookingId; 
    //         // Fetch booking data via AJAX if needed to populate form fields
    //         // For this example, Blade is populating the form, so this might not be necessary.
    //     });
    // });
});
</script>
@endpush