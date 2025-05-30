@extends('layouts.instructor')

@section('title', 'Booking Details')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="fw-bold m-0">
                            <i class="bx bx-detail text-primary me-2"></i>
                            Booking Details
                        </h4>
                        <a href="{{ route('instructor.bookings.index') }}" class="btn btn-secondary">
                            <i class="bx bx-arrow-back me-1"></i> Back to Bookings
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Left Column - Status and Time/Location -->
        <div class="col-xl-4 col-lg-5 col-md-12">
            <!-- Booking Status Card -->
            <div class="card mb-4">
                <div class="card-header bg-transparent">
                    <h5 class="card-title mb-0">Booking Status</h5>
                </div>
                <div class="card-body text-center">
                    <div class="d-flex align-items-center flex-column pt-2">
                        @php
                            $statusColor = [
                                'pending' => 'warning',
                                'confirmed' => 'success',
                                'completed' => 'info',
                                'cancelled' => 'danger'
                            ][$booking->status] ?? 'secondary';
                            
                            $statusIcon = [
                                'pending' => 'bx-time',
                                'confirmed' => 'bx-check',
                                'completed' => 'bx-check-double',
                                'cancelled' => 'bx-x'
                            ][$booking->status] ?? 'bx-question-mark';
                        @endphp
                        
                        <div class="avatar avatar-xl mb-3">
                            <span class="avatar-initial rounded-circle bg-label-{{ $statusColor }}">
                                <i class="bx {{ $statusIcon }} fs-3"></i>
                            </span>
                        </div>
                        <h3 class="fw-bold text-center mb-1">
                            #{{ $booking->id }}
                        </h3>
                        <span class="badge bg-label-{{ $statusColor }} mb-3 px-3 py-2">
                            <span class="fs-6">{{ ucfirst($booking->status) }}</span>
                        </span>

                        @if($booking->status == 'pending')
                        <div class="d-grid gap-2 w-100">
                            <form action="{{ route('instructor.bookings.update-status', $booking) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="status" value="confirmed">
                                <button type="submit" class="btn btn-success w-100">
                                    <i class="bx bx-check me-1"></i> Confirm Booking
                                </button>
                            </form>
                        </div>
                        @endif

                        @if($booking->status == 'confirmed')
                        <div class="d-grid gap-2 w-100">
                            <form action="{{ route('instructor.bookings.update-status', $booking) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="status" value="completed">
                                <button type="submit" class="btn btn-info w-100">
                                    <i class="bx bx-check-double me-1"></i> Mark as Completed
                                </button>
                            </form>
                        </div>
                        @endif
                        
                        @if($booking->status != 'cancelled' && $booking->status != 'completed')
                        <div class="d-grid gap-2 w-100 mt-2">
                            <form action="{{ route('instructor.bookings.update-status', $booking) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="status" value="cancelled">
                                <button type="submit" class="btn btn-outline-danger w-100" 
                                        onclick="return confirm('Are you sure you want to cancel this booking?')">
                                    <i class="bx bx-x me-1"></i> Cancel Booking
                                </button>
                            </form>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Time and Location Card -->
            <div class="card mb-4">
                <div class="card-header d-flex align-items-center">
                    <i class="bx bx-map-pin text-primary me-2"></i>
                    <h5 class="card-title mb-0">Time & Location</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex align-items-center mb-2">
                            <span class="badge bg-label-primary rounded p-2">
                                <i class="bx bx-calendar fs-5"></i>
                            </span>
                            <div class="ms-3">
                                <h6 class="mb-0">Date</h6>
                                <p class="mb-0 text-muted">{{ $booking->date->format('d M Y') }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex align-items-center mb-2">
                            <span class="badge bg-label-primary rounded p-2">
                                <i class="bx bx-time fs-5"></i>
                            </span>
                            <div class="ms-3">
                                <h6 class="mb-0">Time</h6>
                                <p class="mb-0 text-muted">
                                    {{ $booking->start_time->format('H:i') }} - {{ $booking->end_time->format('H:i') }}
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-0">
                        <div class="d-flex align-items-center">
                            <span class="badge bg-label-primary rounded p-2">
                                <i class="bx bx-building-house fs-5"></i>
                            </span>
                            <div class="ms-3">
                                <h6 class="mb-0">Location</h6>
                                <p class="mb-0 text-muted">{{ $booking->suburb->name }}</p>
                                @if($booking->address)
                                    <small class="text-muted">{{ $booking->address }}</small>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="col-xl-8 col-lg-7 col-md-12">
            <!-- Student Details -->
            <div class="card mb-4">
                <div class="card-header d-flex align-items-center">
                    <i class="bx bx-user text-primary me-2"></i>
                    <h5 class="card-title mb-0">Student Details</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="avatar avatar-md me-3">
                            <span class="avatar-initial rounded-circle bg-primary">
                                {{ substr($booking->user->name, 0, 1) }}
                            </span>
                        </div>
                        <div>
                            <h6 class="mb-0">{{ $booking->user->name }}</h6>
                            <small class="text-muted">{{ $booking->user->email }}</small>
                        </div>
                    </div>

                    @if($booking->booking_for === 'other')
                    <div class="alert alert-info d-flex align-items-start">
                        <i class="bx bx-info-circle me-2 mt-1 fs-5"></i>
                        <div>
                            <h6 class="alert-heading mb-1">Booking for Someone Else</h6>
                            <p class="mb-0"><strong>Name:</strong> {{ $booking->other_name }}</p>
                            <p class="mb-0"><strong>Email:</strong> {{ $booking->other_email }}</p>
                            <p class="mb-0"><strong>Phone:</strong> {{ $booking->other_phone }}</p>
                        </div>
                    </div>
                    @endif

                    @if($booking->notes)
                    <div class="mt-3">
                        <h6 class="fw-semibold">Additional Notes:</h6>
                        <div class="p-3 bg-light rounded">
                            <p class="mb-0">{{ $booking->notes }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Service Details -->
            <div class="card mb-4">
                <div class="card-header d-flex align-items-center">
                    <i class="bx bx-car text-primary me-2"></i>
                    <h5 class="card-title mb-0">Service Details</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="avatar avatar-md me-3 bg-label-primary">
                            <i class="bx bx-car fs-5"></i>
                        </div>
                        <div>
                            <h6 class="mb-0">{{ $booking->service->name }}</h6>
                            <span class="badge bg-label-secondary">Duration: {{ $booking->service->duration }} minutes</span>
                        </div>
                    </div>
                    
                    @if($booking->service->description)
                    <div class="mt-3">
                        <h6 class="fw-semibold">Service Description:</h6>
                        <p class="text-muted mb-0">{{ $booking->service->description }}</p>
                    </div>
                    @endif
                    
                    <div class="mt-4">
                        <h6 class="fw-semibold">Payment Details:</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-borderless mb-0">
                                <tbody>
                                    <tr>
                                        <td class="ps-0"><strong>Service Fee:</strong></td>
                                        <td class="text-end pe-0">${{ number_format($booking->service->price, 2) }}</td>
                                    </tr>
                                    @if($booking->discount_amount > 0)
                                    <tr>
                                        <td class="ps-0"><strong>Discount:</strong></td>
                                        <td class="text-end text-success pe-0">-${{ number_format($booking->discount_amount, 2) }}</td>
                                    </tr>
                                    @endif
                                    <tr class="border-top">
                                        <td class="ps-0"><strong>Total Amount:</strong></td>
                                        <td class="text-end fw-bold pe-0">${{ number_format($booking->total_amount, 2) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            
@endsection

@section('page-scripts')
<style>
    .badge.bg-label-info {
        background-color: rgba(3, 195, 236, 0.16) !important;
        color: #03c3ec !important;
    }
    
    .avatar-initial {
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        font-weight: 600;
    }
    
    .avatar-md .avatar-initial {
        font-size: 0.9rem;
    }
    
    .alert-info {
        background-color: rgba(3, 195, 236, 0.16);
        border-color: rgba(3, 195, 236, 0.2);
        color: #03c3ec;
    }
    
    .bg-light {
        background-color: #f6f7f9 !important;
    }
</style>
@endsection
