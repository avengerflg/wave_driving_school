@extends('layouts.app')

@section('content')
<div class="container py-4 py-md-5">
    <div class="row justify-content-center mb-4">
        <div class="col-12 col-lg-8 text-center">
            <h1 class="text-primary fw-bold mb-2 mb-md-3">Booking Details</h1>
            <p class="text-secondary">Please provide the necessary information to complete your booking.</p>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-12 col-md-10 col-lg-8">
            <div class="card shadow-sm rounded-4 border-0 mb-4">
                <div class="card-header bg-white py-3 border-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-semibold">Personal Information</h5>
                        <span class="badge bg-primary rounded-pill">Step 1 of 2</span>
                    </div>
                </div>
                
                <div class="card-body p-4">
                    <form action="{{ route('cart.save-details') }}" method="POST">
                        @csrf
                        
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Is this booking for yourself or someone else?</label>
                            <div class="d-flex flex-column flex-md-row gap-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="booking_for" id="for_self" value="self" checked>
                                    <label class="form-check-label" for="for_self">
                                        <span class="ms-1">This booking is for me</span>
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="booking_for" id="for_other" value="other">
                                    <label class="form-check-label" for="for_other">
                                        <span class="ms-1">This booking is for someone else</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <div id="other_details" style="display: none;">
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <label for="other_name" class="form-label">Recipient's Name</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="fas fa-user text-primary"></i>
                                        </span>
                                        <input type="text" class="form-control" id="other_name" name="other_name" 
                                               value="{{ old('other_name') }}" placeholder="Enter full name">
                                    </div>
                                    @error('other_name')
                                        <div class="text-danger mt-1 small">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="other_email" class="form-label">Recipient's Email</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="fas fa-envelope text-primary"></i>
                                        </span>
                                        <input type="email" class="form-control" id="other_email" name="other_email" 
                                               value="{{ old('other_email') }}" placeholder="Enter email address">
                                    </div>
                                    @error('other_email')
                                        <div class="text-danger mt-1 small">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="other_phone" class="form-label">Recipient's Phone</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="fas fa-phone text-primary"></i>
                                        </span>
                                        <input type="text" class="form-control" id="other_phone" name="other_phone" 
                                               value="{{ old('other_phone') }}" placeholder="Enter phone number">
                                    </div>
                                    @error('other_phone')
                                        <div class="text-danger mt-1 small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <hr class="my-4">
                        </div>
                        
                        <div class="mb-4">
                            <label for="address" class="form-label">Delivery Address</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="fas fa-map-marker-alt text-primary"></i>
                                </span>
                                <input type="text" class="form-control" id="address" name="address" 
                                       value="{{ old('address') }}" required placeholder="Enter complete address" 
                                       aria-describedby="addressHelp">
                            </div>
                            <div id="addressHelp" class="form-text">This is where your package will be delivered.</div>
                            @error('address')
                                <div class="text-danger mt-1 small">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-between mt-4">
                            <a href="{{ route('cart.show') }}" class="btn btn-outline-secondary rounded-pill px-4">
                                <i class="fas fa-arrow-left me-2"></i> Back to Cart
                            </a>
                            <button type="submit" class="btn btn-primary rounded-pill px-4 py-2">
                                Continue to Payment <i class="fas fa-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="card shadow-sm rounded-4 border-0 bg-light">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-3">
                            <i class="fas fa-shield-alt text-primary fa-2x"></i>
                        </div>
                        <div>
                            <h6 class="mb-1 fw-semibold">Secure Checkout</h6>
                            <p class="mb-0 small text-secondary">All information is encrypted and transmitted securely.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Order Summary - Only visible on large screens -->
        <div class="col-lg-4 d-none d-lg-block">
            <div class="card shadow-sm rounded-4 border-0 mb-4 sticky-top" style="top: 20px;">
                <div class="card-header bg-primary text-white py-3 border-0 rounded-top-4">
                    <h5 class="mb-0 fw-semibold">Order Summary</h5>
                </div>
                <div class="card-body p-4">
                    @php
                        $cart = Session::get('cart', []);
                        $total = 0;
                        foreach ($cart as $item) {
                            $total += $item['total'];
                        }
                    @endphp
                    
                    <!-- Cart Items -->
                    @foreach($cart as $item)
                    <div class="d-flex justify-content-between mb-3">
                        <div>
                            <span class="fw-semibold">{{ $item['name'] }}</span>
                            <div class="small text-secondary">Qty: {{ $item['quantity'] }}</div>
                        </div>
                        <span>${{ number_format($item['total'], 2) }}</span>
                    </div>
                    @endforeach
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-secondary">Subtotal</span>
                        <span>${{ number_format($total - ($total/11), 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-secondary">GST (10%)</span>
                        <span>${{ number_format($total/11, 2) }}</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-0">
                        <span class="fw-bold">Total</span>
                        <span class="fw-bold fs-5">${{ number_format($total, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const forSelf = document.getElementById('for_self');
        const forOther = document.getElementById('for_other');
        const otherDetails = document.getElementById('other_details');
        
        forSelf.addEventListener('change', function() {
            otherDetails.style.display = 'none';
        });
        
        forOther.addEventListener('change', function() {
            otherDetails.style.display = 'block';
        });
    });
</script>
@endsection