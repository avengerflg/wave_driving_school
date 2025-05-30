@extends('layouts.admin')

@section('title', 'Edit Booking')

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Header Card -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="fw-bold m-0">
                                <i class="bx bx-edit-alt text-primary me-2"></i>
                                <span class="text-muted fw-light">Admin /</span> Edit Booking #{{ $booking->id }}
                            </h4>
                            <div>
                                <a href="{{ route('admin.bookings.index') }}" class="btn btn-secondary me-2">
                                    <i class="bx bx-arrow-back me-1"></i> Back to List
                                </a>
                                <a href="{{ route('admin.bookings.show', $booking->id) }}" class="btn btn-info">
                                    <i class="bx bx-show me-1"></i> View Details
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Form Card -->
        <div class="card shadow-sm">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <i class="bx bx-calendar-edit text-primary fs-4 me-2"></i>
                    <h5 class="card-title mb-0">Booking Information</h5>
                </div>
                <div class="badge bg-label-{{ $booking->status == 'confirmed' ? 'success' : ($booking->status == 'pending' ? 'warning' : ($booking->status == 'completed' ? 'info' : 'danger')) }} ms-auto">
                    {{ ucfirst($booking->status) }}
                </div>
            </div>
            
            <div class="card-body">
                <form method="POST" action="{{ route('admin.bookings.update', $booking->id) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <!-- Customer & Instructor Section -->
                        <div class="col-md-6">
                            <div class="card shadow-none border mb-4">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="bx bx-user-circle me-1"></i> Customer & Instructor</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="user_id" class="form-label">Customer</label>
                                        <select id="user_id" name="user_id" class="form-select" required>
                                            @foreach ($users as $user)
                                                @if($user->role === 'student' || $user->hasRole('student'))
                                                    <option value="{{ $user->id }}" {{ $booking->user_id == $user->id ? 'selected' : '' }}>
                                                        {{ $user->name }} ({{ $user->email }})
                                                    </option>
                                                @endif
                                            @endforeach
                                        </select>
                                        <div class="form-text">Select the customer who is booking.</div>
                                    </div>

                                    <div class="mb-0">
                                        <label for="instructor_id" class="form-label">Instructor</label>
                                        <select id="instructor_id" name="instructor_id" class="form-select" required>
                                            @foreach ($instructors as $instructor)
                                                <option value="{{ $instructor->id }}" {{ $booking->instructor_id == $instructor->id ? 'selected' : '' }}>
                                                    {{ $instructor->user->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Service & Location Section -->
                        <div class="col-md-6">
                            <div class="card shadow-none border mb-4">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="bx bx-package me-1"></i> Service & Location</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="service_id" class="form-label">Service Type</label>
                                        <select id="service_id" name="service_id" class="form-select" required>
                                            @foreach ($services as $service)
                                                <option value="{{ $service->id }}" {{ $booking->service_id == $service->id ? 'selected' : '' }}>
                                                    {{ $service->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="mb-0">
                                        <label for="suburb_id" class="form-label">Suburb</label>
                                        <select id="suburb_id" name="suburb_id" class="form-select" required>
                                            @foreach ($suburbs as $suburb)
                                                <option value="{{ $suburb->id }}" {{ $booking->suburb_id == $suburb->id ? 'selected' : '' }}>
                                                    {{ $suburb->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Date & Time Section -->
                        <div class="col-md-6">
                            <div class="card shadow-none border mb-4">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="bx bx-calendar me-1"></i> Date & Time</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="date" class="form-label">Date</label>
                                        <input 
                                            type="date" 
                                            id="date" 
                                            name="date" 
                                            class="form-control @error('date') is-invalid @enderror" 
                                            value="{{ old('date', $booking->date ? $booking->date->format('Y-m-d') : '') }}" 
                                            required
                                        >
                                        @error('date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="row">
                                        <div class="col-6">
                                            <label for="start_time" class="form-label">Start Time</label>
                                            <input 
                                                type="time" 
                                                id="start_time" 
                                                name="start_time" 
                                                class="form-control @error('start_time') is-invalid @enderror" 
                                                value="{{ old('start_time', $booking->start_time) }}" 
                                                required
                                            >
                                            @error('start_time')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-6">
                                            <label for="end_time" class="form-label">End Time</label>
                                            <input 
                                                type="time" 
                                                id="end_time" 
                                                name="end_time" 
                                                class="form-control @error('end_time') is-invalid @enderror" 
                                                value="{{ old('end_time', $booking->end_time) }}" 
                                                required
                                            >
                                            @error('end_time')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Status Section -->
                        <div class="col-md-6">
                            <div class="card shadow-none border mb-4">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="bx bx-info-circle me-1"></i> Booking Status</h6>
                                </div>
                                <div class="card-body">
                                    <div>
                                        <label for="status" class="form-label">Status</label>
                                        <div class="d-flex gap-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="status" id="status-pending" value="pending" {{ $booking->status == 'pending' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="status-pending">
                                                    <span class="badge bg-label-warning me-1">Pending</span>
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="status" id="status-confirmed" value="confirmed" {{ $booking->status == 'confirmed' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="status-confirmed">
                                                    <span class="badge bg-label-success me-1">Confirmed</span>
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="status" id="status-completed" value="completed" {{ $booking->status == 'completed' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="status-completed">
                                                    <span class="badge bg-label-info me-1">Completed</span>
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="status" id="status-cancelled" value="cancelled" {{ $booking->status == 'cancelled' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="status-cancelled">
                                                    <span class="badge bg-label-danger me-1">Cancelled</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Metadata Information -->
                            <div class="card bg-lighter shadow-none mb-4">
                                <div class="card-body">
                                    <small class="text-muted d-block mb-2">Booking #{{ $booking->id }}</small>
                                    <small class="text-muted d-block mb-1">Created: {{ $booking->created_at->format('M d, Y \a\t H:i') }}</small>
                                    <small class="text-muted d-block">Last updated: {{ $booking->updated_at->format('M d, Y \a\t H:i') }}</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="mt-2 d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.bookings.show', $booking->id) }}" class="btn btn-outline-secondary">
                            <i class="bx bx-x me-1"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-save me-1"></i> Update Booking
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
