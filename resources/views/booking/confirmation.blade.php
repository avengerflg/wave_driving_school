@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="booking-content text-center">
                <div class="details-section confirmation-header">
                    <div class="success-icon mb-4">
                        <i class="fas fa-check"></i>
                    </div>
                    
                    <h1 class="display-6 fw-bold mb-3 text-primary">Booking Confirmed!</h1>
                    <p class="lead text-secondary mb-4">Your booking has been successfully processed.</p>
                    
                    <div class="alert alert-info rounded-4 mb-0">
                        <i class="fas fa-envelope me-2"></i>
                        <span>A confirmation email has been sent to your email address.</span>
                    </div>
                </div>
                
                <div class="details-section mt-4">
                    <div class="section-header">
                        <i class="fas fa-clipboard-list"></i>
                        <h2>Booking Details</h2>
                    </div>
                    
                    <div class="row mb-2">
                        <div class="col-md-4 fw-bold">Booking ID:</div>
                        <div class="col-md-8">{{ $booking->id }}</div>
                    </div>
                    
                    <div class="row mb-2">
                        <div class="col-md-4 fw-bold">Service:</div>
                        <div class="col-md-8">{{ $booking->service->name }}</div>
                    </div>
                    
                    <div class="row mb-2">
                        <div class="col-md-4 fw-bold">Instructor:</div>
                        <div class="col-md-8">{{ $booking->instructor->user->name }}</div>
                    </div>
                    
                    <div class="row mb-2">
                        <div class="col-md-4 fw-bold">Date:</div>
                        <div class="col-md-8">{{ \Carbon\Carbon::parse($booking->date)->format('l, F j, Y') }}</div>
                    </div>
                    
                    <div class="row mb-2">
                        <div class="col-md-4 fw-bold">Time:</div>
                        <div class="col-md-8">{{ \Carbon\Carbon::parse($booking->start_time)->format('g:i A') }} - {{ \Carbon\Carbon::parse($booking->end_time)->format('g:i A') }}</div>
                    </div>
                    
                    <div class="row mb-2">
                        <div class="col-md-4 fw-bold">Location:</div>
                        <div class="col-md-8">{{ $booking->suburb->name }}, {{ $booking->suburb->state }}</div>
                    </div>
                    
                    <div class="row mb-2">
                        <div class="col-md-4 fw-bold">Pickup Address:</div>
                        <div class="col-md-8">{{ $booking->address }}</div>
                    </div>
                    
                    @if($booking->booking_for === 'other')
                        <div class="row mb-2">
                            <div class="col-md-4 fw-bold">Student Name:</div>
                            <div class="col-md-8">{{ $booking->other_name }}</div>
                        </div>
                    @endif
                    
                    <hr>
                    
                    <div class="row mb-2">
                        <div class="col-md-4 fw-bold">Payment Method:</div>
                        <div class="col-md-8">
                            @if($booking->payment->payment_method === 'credit_card')
                                <i class="fas fa-credit-card me-2 text-primary"></i> Credit Card
                            @elseif($booking->payment->payment_method === 'paypal')
                                <i class="fab fa-paypal me-2 text-primary"></i> PayPal
                            @else
                                <i class="fas fa-money-bill-wave me-2 text-primary"></i> {{ ucfirst($booking->payment->payment_method) }}
                            @endif
                        </div>
                    </div>
                    
                    <div class="row mb-2">
                        <div class="col-md-4 fw-bold">Amount Paid:</div>
                        <div class="col-md-8 text-success fw-semibold">${{ number_format($booking->payment->amount, 2) }}</div>
                    </div>
                    
                    <div class="row mb-2">
                        <div class="col-md-4 fw-bold">Transaction ID:</div>
                        <div class="col-md-8"><code>{{ $booking->payment->transaction_id }}</code></div>
                    </div>
                </div>
                
                <div class="navigation-buttons mt-4">
                    <a href="{{ route('client.dashboard') }}" class="btn btn-primary btn-lg dashboard-btn">
                        <i class="fas fa-tachometer-alt me-2"></i>
                        Go to Dashboard
                    </a>
                    <a href="{{ route('client.bookings.index') }}" class="btn btn-outline-primary btn-lg bookings-btn">
                        <i class="fas fa-list me-2"></i>
                        My Bookings
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Booking Content Styles */
.booking-content {
    max-width: 900px;
    margin: 0 auto;
    padding: 20px;
}

.details-section {
    background: #ffffff;
    border-radius: 15px;
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
}

.confirmation-header {
    padding-top: 3rem;
    padding-bottom: 3rem;
}

.section-header {
    display: flex;
    align-items: center;
    margin-bottom: 1.5rem;
}

.section-header i {
    font-size: 1.5rem;
    color: #0d6efd;
    margin-right: 1rem;
}

.section-header h2 {
    font-size: 1.5rem;
    color: #2d3748;
    margin: 0;
}

/* Success Icon Styles */
.success-icon {
    width: 100px;
    height: 100px;
    background: #10B981;
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
    font-size: 3rem;
    box-shadow: 0 10px 15px -3px rgba(16, 185, 129, 0.2);
}

/* Alert Styles */
.alert-info {
    background-color: rgba(13, 110, 253, 0.1);
    border: none;
    color: #0d6efd;
    border-radius: 10px;
    padding: 1.25rem;
    display: flex;
    align-items: center;
    justify-content: center;
}

.alert-info i {
    font-size: 1.2rem;
    margin-right: 0.5rem;
}

/* Navigation Buttons */
.navigation-buttons {
    display: flex;
    justify-content: center;
    gap: 1.5rem;
    margin-top: 2rem;
}

.navigation-buttons .btn {
    padding: 0.875rem 2.5rem;
    font-weight: 600;
    border-radius: 50px;
    transition: all 0.3s ease;
    min-width: 180px;
    font-size: 1rem;
}

.navigation-buttons .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(13, 110, 253, 0.25);
}

.dashboard-btn {
    background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
    border: none;
}

.dashboard-btn:hover {
    background: linear-gradient(135deg, #0b5ed7 0%, #0a58ca 100%);
    box-shadow: 0 8px 25px rgba(13, 110, 253, 0.4);
}

.bookings-btn {
    border: 2px solid #0d6efd;
    color: #0d6efd;
    background: transparent;
}

.bookings-btn:hover {
    background: #0d6efd;
    color: white;
    border-color: #0d6efd;
}

/* Responsive Styles */
@media (max-width: 768px) {
    .details-section {
        padding: 1.5rem;
    }
    
    .confirmation-header {
        padding-top: 2rem;
        padding-bottom: 2rem;
    }
    
    .success-icon {
        width: 80px;
        height: 80px;
        font-size: 2.5rem;
    }
    
    .navigation-buttons {
        flex-direction: column;
        gap: 1rem;
        align-items: center;
    }
    
    .navigation-buttons .btn {
        width: 100%;
        max-width: 300px;
        min-width: auto;
    }
}

/* Dark Mode Support */
@media (prefers-color-scheme: dark) {
    .details-section {
        background: #242424;
    }
    
    .section-header h2 {
        color: #e2e8f0;
    }
    
    .alert-info {
        background-color: rgba(13, 110, 253, 0.15);
    }
}
</style>
@endsection
