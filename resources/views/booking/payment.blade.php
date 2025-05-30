@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="booking-steps">
                <!-- Steps Indicator -->
                <div class="step-indicator">
                    <div class="step completed">
                        <div class="step-number">
                            <i class="fas fa-check"></i>
                            <span class="step-count">1</span>
                        </div>
                        <div class="step-title">Suburb</div>
                    </div>
                    <div class="step completed">
                        <div class="step-number">
                            <i class="fas fa-check"></i>
                            <span class="step-count">2</span>
                        </div>
                        <div class="step-title">Instructor</div>
                    </div>
                    <div class="step completed">
                        <div class="step-number">
                            <i class="fas fa-check"></i>
                            <span class="step-count">3</span>
                        </div>
                        <div class="step-title">Date & Time</div>
                    </div>
                    <div class="step completed">
                        <div class="step-number">
                            <i class="fas fa-check"></i>
                            <span class="step-count">4</span>
                        </div>
                        <div class="step-title">Service</div>
                    </div>
                    <div class="step completed">
                        <div class="step-number">
                            <i class="fas fa-check"></i>
                            <span class="step-count">5</span>
                        </div>
                        <div class="step-title">Details</div>
                    </div>
                    <div class="step active">
                        <div class="step-number">
                            6
                            <span class="step-count">6</span>
                        </div>
                        <div class="step-title">Payment</div>
                    </div>
                </div>
                
                <div class="booking-content">
                    <h1>Complete Payment</h1>
                    <p>Finalize your booking by making a payment</p>
                    
                    <div class="details-section">
                        <div class="section-header">
                            <i class="fas fa-clipboard-list"></i>
                            <h2>Booking Summary</h2>
                        </div>
                        
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
                        
                        <div class="row mb-2 total-price">
                            <div class="col-md-4 fw-bold">Total Price:</div>
                            <div class="col-md-8 fw-bold">${{ number_format($bookingData['service']->price, 2) }}</div>
                        </div>
                    </div>
                    
                    <form action="{{ route('booking.payment.process') }}" method="POST" id="payment-form">
                        @csrf
                        
                        <div class="details-section">
                            <div class="section-header">
                                <i class="fas fa-credit-card"></i>
                                <h2>Payment Method</h2>
                            </div>
                            
                            <div class="payment-options">
                                <div class="payment-option">
                                    <input class="payment-radio" type="radio" name="payment_method" id="creditCard" value="credit_card" checked>
                                    <label class="payment-label" for="creditCard">
                                        <div class="payment-icon">
                                            <i class="fab fa-cc-visa"></i>
                                            <i class="fab fa-cc-mastercard"></i>
                                            <i class="fab fa-cc-amex"></i>
                                        </div>
                                        <div class="payment-title">Credit Card</div>
                                    </label>
                                </div>
                                
                                <div class="payment-option">
                                    <input class="payment-radio" type="radio" name="payment_method" id="paypal" value="paypal">
                                    <label class="payment-label" for="paypal">
                                        <div class="payment-icon">
                                            <i class="fab fa-paypal"></i>
                                        </div>
                                        <div class="payment-title">PayPal</div>
                                    </label>
                                </div>
                            </div>
                            
                            <div id="credit-card-fields" class="mt-4">
                                <div class="form-group">
                                    <label for="card_number">Card Number</label>
                                    <input type="text" class="form-control" id="card_number" placeholder="1234 5678 9012 3456">
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="expiry_date">Expiry Date</label>
                                            <input type="text" class="form-control" id="expiry_date" placeholder="MM/YY">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="cvv">CVV</label>
                                            <input type="text" class="form-control" id="cvv" placeholder="123">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="card_name">Name on Card</label>
                                    <input type="text" class="form-control" id="card_name" placeholder="John Doe">
                                </div>
                            </div>
                            
                            <div class="alert alert-info mt-4">
                                <i class="fas fa-info-circle me-2"></i>
                                <span>This is a demo application. No actual payment will be processed.</span>
                            </div>
                        </div>
                        
                        <div class="navigation-buttons">
                            <a href="{{ route('booking.details') }}" 
                               class="btn btn-outline-primary btn-lg back-btn">
                                <i class="fas fa-arrow-left me-2"></i>
                                Back
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg continue-btn">
                                Complete Booking
                                <i class="fas fa-check ms-2"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Step Indicator Styles */
.step-indicator {
    display: flex;
    justify-content: space-between;
    margin-bottom: 2rem;
    position: relative;
    padding: 0 1rem;
}

.step-indicator::before {
    content: '';
    position: absolute;
    top: 20px;
    left: 0;
    right: 0;
    height: 2px;
    background: #e2e8f0;
    z-index: 1;
}

.step {
    position: relative;
    z-index: 2;
    text-align: center;
    flex: 1;
}

.step-number {
    width: 40px;
    height: 40px;
    background: white;
    border: 2px solid #e2e8f0;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 0.5rem;
    font-weight: 600;
    color: #64748b;
    position: relative;
}

.step-count {
    position: absolute;
    top: -8px;
    right: -8px;
    background: rgba(13, 110, 253, 0.1);
    color: #0d6efd;
    width: 22px;
    height: 22px;
    border-radius: 50%;
    font-size: 0.8rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    border: 2px solid white;
    z-index: 2;
}

.step.completed .step-number {
    background: #0d6efd;
    border-color: #0d6efd;
    color: white;
}

.step.completed .step-count {
    background: #0d6efd;
    color: white;
}

.step.active .step-number {
    border-color: #0d6efd;
    color: #0d6efd;
}

.step.active .step-count {
    background: #0d6efd;
    color: white;
}

.step-title {
    font-size: 0.875rem;
    color: #64748b;
}

.step.active .step-title,
.step.completed .step-title {
    color: #0d6efd;
    font-weight: 600;
}

/* Booking Content Styles */
.booking-content {
    max-width: 900px;
    margin: 0 auto;
    padding: 20px;
}

.booking-content h1 {
    font-size: 2rem;
    color: #2d3748;
    margin-bottom: 0.5rem;
}

.booking-content p {
    color: #6b7280;
    margin-bottom: 2rem;
}

/* Details Section Styles */
.details-section {
    background: #ffffff;
    border-radius: 15px;
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
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

.total-price {
    font-size: 1.1rem;
    color: #2d3748;
}

/* Payment Options Styles */
.payment-options {
    display: flex;
    gap: 1.5rem;
    margin-bottom: 1.5rem;
}

.payment-option {
    flex: 1;
}

.payment-radio {
    display: none;
}

.payment-label {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 1.5rem;
    background: #f8fafc;
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.3s ease;
    border: 2px solid #e2e8f0;
}

.payment-radio:checked + .payment-label {
    background: white;
    border-color: #0d6efd;
    box-shadow: 0 4px 12px rgba(13, 110, 253, 0.1);
}

.payment-icon {
    font-size: 1.8rem;
    color: #0d6efd;
    margin-bottom: 1rem;
}

.payment-icon i {
    margin: 0 0.25rem;
}

.payment-title {
    font-weight: 600;
    color: #2d3748;
}

/* Form Styles */
.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    color: #4a5568;
    font-weight: 500;
}

.form-control {
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 0.75rem 1rem;
    transition: all 0.3s ease;
}

.form-control:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.1);
}

/* Alert Styles */
.alert-info {
    background-color: rgba(13, 110, 253, 0.1);
    border: none;
    color: #0d6efd;
    border-radius: 10px;
    display: flex;
    align-items: center;
}

.alert-info i {
    font-size: 1.2rem;
}

/* Navigation Buttons */
.navigation-buttons {
    display: flex;
    justify-content: space-between;
    margin-top: 2rem;
}

.back-btn, .continue-btn {
    padding: 0.75rem 2rem;
    font-weight: 600;
    border-radius: 50px;
    transition: all 0.3s ease;
}

.back-btn:hover, .continue-btn:hover {
    transform: translateY(-2px);
}

/* Responsive Styles */
@media (max-width: 768px) {
    .payment-options {
        flex-direction: column;
        gap: 1rem;
    }
    
    .navigation-buttons {
        flex-direction: column;
        gap: 1rem;
    }
    
    .back-btn, .continue-btn {
        width: 100%;
    }
    
    .step-indicator {
        overflow-x: auto;
        padding-bottom: 1rem;
    }
    
    .step {
        min-width: 120px;
    }
    
    .details-section {
        padding: 1.5rem;
    }
}
</style>

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
@endsection