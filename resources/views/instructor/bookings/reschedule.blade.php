@extends('layouts.admin')

@section('title', 'Reschedule Booking')

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="fw-bold m-0">
                                <i class="bx bx-calendar-edit text-primary me-2"></i>
                                <span>Reschedule Booking #{{ $booking->id }}</span>
                            </h4>
                            <div>
                                <a href="{{ route('instructor.bookings.show', $booking) }}" class="btn btn-secondary">
                                    <i class="bx bx-arrow-back me-1"></i> Back to Booking
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reschedule Form -->
        <div class="row">
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Booking Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Student</label>
                            <input type="text" class="form-control" value="{{ $booking->user->name }}" disabled>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Service</label>
                            <input type="text" class="form-control" value="{{ $booking->service->name }}" disabled>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Current Date & Time</label>
                            <input type="text" class="form-control" value="{{ $booking->date->format('M d, Y') }}, {{ date('h:i A', strtotime($booking->start_time)) }} - {{ date('h:i A', strtotime($booking->end_time)) }}" disabled>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Location</label>
                            <input type="text" class="form-control" value="{{ $booking->suburb->name ?? 'N/A' }}" disabled>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Reschedule Details</h5>
                    </div>
                    <div class="card-body">
                        @if(session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif

                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('instructor.bookings.reschedule', $booking) }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <div class="mb-3">
                                <label class="form-label" for="date">New Date</label>
                                <input type="date" class="form-control" id="date" name="date" value="{{ old('date', $booking->date->format('Y-m-d')) }}" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label" for="start_time">Start Time</label>
                                <input type="time" class="form-control" id="start_time" name="start_time" value="{{ old('start_time', $booking->start_time->format('H:i')) }}" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label" for="end_time">End Time</label>
                                <input type="time" class="form-control" id="end_time" name="end_time" value="{{ old('end_time', $booking->end_time->format('H:i')) }}" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label" for="notes">Notes (Optional)</label>
                                <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Add a note about why this booking is being rescheduled">{{ old('notes') }}</textarea>
                            </div>
                            
                            <div class="d-flex justify-content-end mt-4">
                                <a href="{{ route('instructor.bookings.show', $booking) }}" class="btn btn-secondary me-2">Cancel</a>
                                <button type="submit" class="btn btn-primary">Reschedule Booking</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection