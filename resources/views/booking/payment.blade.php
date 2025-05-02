<!-- resources/views/booking/payment.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="booking-steps">
                <div class="step-indicator">
                    <div class="step completed">
                        <div class="step-number">1</div>
                        <div class="step-title">Suburb</div>
                    </div>
                    <div class="step completed">
                        <div class="step-number">2</div>
                        <div class="step-title">Instructor</div>
                    </div>
                    <div class="step completed">
                        <div class="step-number">3</div>
                        <div class="step-title">Date & Time</div>
                    </div>
                    <div class="step completed">
                        <div class="step-number">4</div>
                        <div class="step-title">Service</div>
                    </div>
                    <div class="step completed">
                        <div class="step-number">5</div>
                        <div class="step-title">Details</div>
                    </div>
                    <div class="step active">
                        <div class="step-number">6</div>
                        <div class="step-title">Payment</div>
                    </div>
                </div>
                
                <h2 class="text-center mb-4">Payment</h2>
                <p class="text-center mb-4">Complete your booking by making a payment</p>
                
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h5 class="mb-3">Booking Summary</h5>
                        
                        <div class="row mb-2">
                            <div class="col-md-4 fw-bold">Service:</div>
                            <div class="col-md-8">{{ $bookingData['service']->name }}</div>
                        </div>
                        
                        <div class="row mb-2">
                            <div class="col-md-4 fw-bold">Instructor:</div>
                            <div class="col-md-8">{{ $bookingData['instructor']->user->name }}</div>
                        </div>
                        
                        <div class="row mb-2">
                            <div class="col-md-4 fw-bold">Date:</div>
                            <div class="col-md-8">{{ \Carbon\Carbon::parse($bookingData['date'])->format('l, F j, Y') }}</div>
                        </div>
                        
                        <div class="row mb-2">
                            <div class="col-md-4 fw-bold">Time:</div>
                            <div class="col-md-8">{{ \Carbon\Carbon::parse($bookingData['start_time'])->format('g:i A') }} - {{ \Carbon\Carbon::parse($bookingData['end_time'])->format('g:i A') }}</div>
                        </div>
                        
                        <div class="row mb-2">
                            <div class="col-md-4 fw-bold">Location:</div>
                            <div class="col-md-8">{{ $bookingData['suburb']->name }}, {{ $bookingData['suburb']->state }}</div>
                        </div>
                        
                        <div class="row mb-2">
                            <div class="col-md-4 fw-bold">Pickup Address:</div>
                            <div class="col-md-8">{{ $bookingData['address'] }}</div>
                        </div>
                        
                        @if($bookingData['booking_for'] === 'other')
                            <div class="row mb-2">
                                <div class="col-md-4 fw-bold">Student Name:</div>
                                <div class="col-md-8">{{ $bookingData['other_name'] }}</div>
                            </div>
                            
                            <div class="row mb-2">
                                <div class="col-md-4 fw-bold">Student Email:</div>
                                <div class="col-md-8">{{ $bookingData['other_email'] }}</div>
                            </div>
                            
                            <div class="row mb-2">
                                <div class="col-md-4 fw-bold">Student Phone:</div>
                                <div class="col-md-8">{{ $bookingData['other_phone'] }}</div>
                            </div>
                        @endif
                        
                        <hr>
                        
                        <div class="row mb-2">
                            <div class="col-md-4 fw-bold">Price:</div>
                            <div class="col-md-8">${{ number_format($bookingData['service']->price, 2) }}</div>
                        </div>
                    </div>
                </div>
                
                <form action="{{ route('booking.payment.process') }}" method="POST" id="payment-form">
                    @csrf
                    
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body p-4">
                            <h5 class="mb-3">Payment Method</h5>
                            
                            <div class="mb-3">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="payment_method" id="creditCard" value="credit_card" checked>
                                    <label class="form-check-label" for="creditCard">
                                        <i class="fab fa-cc-visa me-2"></i>
                                        <i class="fab fa-cc-mastercard me-2"></i>
                                        <i class="fab fa-cc-amex me-2"></i>
                                        Credit Card
                                    </label>
                                </div>
                                
                                <div id="credit-card-fields" class="mt-3">
                                    <div class="mb-3">
                                        <label for="card_number" class="form-label">Card Number</label>
                                        <input type="text" class="form-control" id="card_number" placeholder="1234 5678 9012 3456">
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="expiry_date" class="form-label">Expiry Date</label>
                                            <input type="text" class="form-control" id="expiry_date" placeholder="MM/YY">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="cvv" class="form-label">CVV</label>
                                            <input type="text" class="form-control" id="cvv" placeholder="123">
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="card_name" class="form-label">Name on Card</label>
                                        <input type="text" class="form-control" id="card_name" placeholder="John Doe">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" id="paypal" value="paypal">
                                <label class="form-check-label" for="paypal">
                                    <i class="fab fa-paypal me-2"></i>
                                    PayPal
                                </label>
                            </div>
                            
                            <div class="alert alert-info mt-3">
                                <small><i class="fas fa-info-circle me-2"></i> This is a demo application. No actual payment will be processed.</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('booking.details') }}" class="btn btn-outline-secondary">Back</a>
                        <button type="submit" class="btn btn-primary">Complete Booking</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const creditCardRadio = document.getElementById('creditCard');
        const paypalRadio = document.getElementById('paypal');
        const creditCardFields = document.getElementById('credit-card-fields');
        
        creditCardRadio.addEventListener('change', function() {
            if (this.checked) {
                creditCardFields.style.display = 'block';
            }
        });
        
        paypalRadio.addEventListener('change', function() {
            if (this.checked) {
                creditCardFields.style.display = 'none';
            }
        });
    });
</script>
@endpush
