<!-- resources/views/booking/confirmation.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-5 text-center">
                    <div class="mb-4">
                        <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center mx-auto" style="width: 80px; height: 80px;">
                            <i class="fas fa-check fa-3x"></i>
                        </div>
                    </div>
                    
                    <h2 class="mb-3">Booking Confirmed!</h2>
                    <p class="lead mb-4">Your booking has been successfully processed.</p>
                    
                    <div class="alert alert-info mb-4">
                        <p class="mb-0">A confirmation email has been sent to your email address.</p>
                    </div>
                </div>
            </div>
            
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-body p-4">
                    <h4 class="mb-3">Booking Details</h4>
                    
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
                                Credit Card
                            @elseif($booking->payment->payment_method === 'paypal')
                                PayPal
                            @else
                                {{ ucfirst($booking->payment->payment_method) }}
                            @endif
                        </div>
                    </div>
                    
                    <div class="row mb-2">
                        <div class="col-md-4 fw-bold">Amount Paid:</div>
                        <div class="col-md-8">${{ number_format($booking->payment->amount, 2) }}</div>
                    </div>
                    
                    <div class="row mb-2">
                        <div class="col-md-4 fw-bold">Transaction ID:</div>
                        <div class="col-md-8">{{ $booking->payment->transaction_id }}</div>
                    </div>
                </div>
            </div>
            
            <div class="mt-4 text-center">
                <a href="{{ route('home') }}" class="btn btn-primary">Back to Home</a>
                <a href="{{ route('client.bookings.index') }}" class="btn btn-outline-primary ms-2">View My Bookings</a>
            </div>
        </div>
    </div>
</div>
@endsection
