@extends('layouts.app')

@section('content')
<div class="container py-4 py-md-5">
    <div class="row justify-content-center mb-4">
        <div class="col-12 col-lg-8 text-center">
            <h1 class="text-primary fw-bold mb-2 mb-md-3">Payment</h1>
            <p class="text-secondary">Complete your purchase securely with your preferred payment method.</p>
        </div>
    </div>

    <div class="row">
        <!-- Main Content - Payment and Order Details -->
        <div class="col-12 col-lg-8 mb-4">
            <!-- Order Summary Card -->
            <div class="card shadow-sm rounded-4 border-0 mb-4">
                <div class="card-header bg-white py-3 border-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-semibold">Order Summary</h5>
                        <span class="badge bg-primary rounded-pill">Step 2 of 2</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <!-- Table for medium screens and up -->
                    <div class="table-responsive d-none d-md-block">
                        <table class="table mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">Package</th>
                                    <th>Quantity</th>
                                    <th class="text-end pe-4">Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cart as $item)
                                <tr>
                                    <td class="ps-4">{{ $item['name'] }}</td>
                                    <td>{{ $item['quantity'] }}</td>
                                    <td class="text-end pe-4">${{ number_format($item['total'], 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-group-divider">
                                <tr>
                                    <td colspan="2" class="text-end pe-4 fw-bold">Total:</td>
                                    <td class="text-end pe-4 fw-bold">${{ number_format($total, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    
                    <!-- Mobile view for small screens -->
                    <div class="d-md-none p-3">
                        <div class="list-group list-group-flush">
                            @foreach($cart as $item)
                            <div class="list-group-item px-0 py-3 border-top-0 border-start-0 border-end-0">
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="fw-semibold">{{ $item['name'] }}</span>
                                    <span>${{ number_format($item['total'], 2) }}</span>
                                </div>
                                <div class="text-secondary small">Quantity: {{ $item['quantity'] }}</div>
                            </div>
                            @endforeach
                            <div class="list-group-item px-0 py-3 border-top-0 border-start-0 border-end-0 bg-light">
                                <div class="d-flex justify-content-between">
                                    <span class="fw-bold">Total:</span>
                                    <span class="fw-bold">${{ number_format($total, 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Payment Details Card -->
            <div class="card shadow-sm rounded-4 border-0 mb-4">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="mb-0 fw-semibold">Payment Details</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('cart.process-payment') }}" method="POST" id="payment-form">
                        @csrf
                        
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Select Payment Method:</label>
                            <div class="row g-3 mb-3">
                                <div class="col-12 col-md-6">
                                    <div class="form-check payment-option border rounded p-3">
                                        <input class="form-check-input" type="radio" name="payment_method" id="credit_card" value="credit_card" checked>
                                        <label class="form-check-label w-100" for="credit_card">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-credit-card text-primary me-3 fa-lg"></i>
                                                <div>
                                                    <span class="d-block fw-semibold">Credit Card</span>
                                                    <small class="text-secondary">Visa, Mastercard, Amex</small>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-check payment-option border rounded p-3">
                                        <input class="form-check-input" type="radio" name="payment_method" id="paypal" value="paypal">
                                        <label class="form-check-label w-100" for="paypal">
                                            <div class="d-flex align-items-center">
                                                <i class="fab fa-paypal text-primary me-3 fa-lg"></i>
                                                <div>
                                                    <span class="d-block fw-semibold">PayPal</span>
                                                    <small class="text-secondary">Pay with your PayPal account</small>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div id="card-element-container">
                            <div class="mb-4">
                                <label for="card-element" class="form-label">Credit Card Information</label>
                                <div id="card-element" class="form-control p-3 bg-light">
                                    <!-- Stripe Element placeholder - for demonstration only -->
                                    <div class="d-flex justify-content-between align-items-center text-secondary">
                                        <span>•••• •••• •••• ••••</span>
                                        <div class="d-flex">
                                            <i class="fab fa-cc-visa mx-1"></i>
                                            <i class="fab fa-cc-mastercard mx-1"></i>
                                            <i class="fab fa-cc-amex mx-1"></i>
                                        </div>
                                    </div>
                                </div>
                                <div id="card-errors" class="text-danger mt-2 small"></div>
                                <div class="form-text">Your card information is encrypted and secure.</div>
                            </div>
                            <input type="hidden" name="stripe_token" id="stripe_token">
                        </div>
                        
                        <div id="paypal-container" style="display: none;" class="mb-4">
                            <div class="alert alert-info">
                                <div class="d-flex">
                                    <div class="me-3">
                                        <i class="fab fa-paypal fa-2x"></i>
                                    </div>
                                    <div>
                                        <h6 class="alert-heading mb-1">PayPal Checkout</h6>
                                        <p class="mb-0">You'll be redirected to PayPal to complete your payment securely.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-between mt-4">
                            <a href="{{ route('cart.details') }}" class="btn btn-outline-secondary rounded-pill px-4">
                                <i class="fas fa-arrow-left me-2"></i> Back to Details
                            </a>
                            <button type="submit" class="btn btn-success rounded-pill px-4 py-2 fw-semibold">
                                <i class="fas fa-lock me-2"></i> Complete Payment
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Sidebar - Booking Summary -->
        <div class="col-12 col-lg-4">
            <div class="card shadow-sm rounded-4 border-0 mb-4 sticky-top" style="top: 20px;">
                <div class="card-header bg-primary text-white py-3 border-0 rounded-top-4">
                    <h5 class="mb-0 fw-semibold">Booking Summary</h5>
                </div>
                <div class="card-body p-4">
                    <ul class="list-group list-group-flush mb-0">
                        <li class="list-group-item px-0 py-2 d-flex justify-content-between border-0">
                            <span class="text-secondary">Total Amount:</span>
                            <span class="fw-semibold">${{ number_format($total, 2) }}</span>
                        </li>
                        <li class="list-group-item px-0 py-2 d-flex justify-content-between border-0">
                            <span class="text-secondary">Booking For:</span>
                            <span class="fw-semibold">{{ Session::get('package_booking.booking_for') == 'self' ? 'Myself' : 'Someone Else' }}</span>
                        </li>
                        
                        @if(Session::get('package_booking.booking_for') == 'other')
                        <li class="list-group-item px-0 py-2 border-0">
                            <span class="text-secondary">Recipient:</span>
                            <div class="mt-1">
                                <div class="fw-semibold">{{ Session::get('package_booking.other_name') }}</div>
                                <div>{{ Session::get('package_booking.other_email') }}</div>
                                <div>{{ Session::get('package_booking.other_phone') }}</div>
                            </div>
                        </li>
                        @endif
                        
                        <li class="list-group-item px-0 py-2 border-0">
                            <span class="text-secondary">Delivery Address:</span>
                            <div class="fw-semibold mt-1">{{ Session::get('package_booking.address') }}</div>
                        </li>
                    </ul>
                </div>
                <div class="card-footer bg-light py-3 border-0 rounded-bottom-4">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-3">
                            <i class="fas fa-shield-alt text-success"></i>
                        </div>
                        <div class="small text-secondary">
                            Your payment information is processed securely. We do not store credit card details.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const creditCardRadio = document.getElementById('credit_card');
        const paypalRadio = document.getElementById('paypal');
        const cardElementContainer = document.getElementById('card-element-container');
        const paypalContainer = document.getElementById('paypal-container');
        
        // Style the selected payment method
        function updatePaymentOptionStyles() {
            document.querySelectorAll('.payment-option').forEach(option => {
                option.classList.remove('border-primary', 'bg-light');
            });
            
            if (creditCardRadio.checked) {
                creditCardRadio.closest('.payment-option').classList.add('border-primary', 'bg-light');
            } else if (paypalRadio.checked) {
                paypalRadio.closest('.payment-option').classList.add('border-primary', 'bg-light');
            }
        }
        
        // Initial style update
        updatePaymentOptionStyles();
        
        creditCardRadio.addEventListener('change', function() {
            cardElementContainer.style.display = 'block';
            paypalContainer.style.display = 'none';
            updatePaymentOptionStyles();
        });
        
        paypalRadio.addEventListener('change', function() {
            cardElementContainer.style.display = 'none';
            paypalContainer.style.display = 'block';
            updatePaymentOptionStyles();
        });
    });
</script>
@endsection