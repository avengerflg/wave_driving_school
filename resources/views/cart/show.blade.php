@extends('layouts.app')

@section('content')
<div class="container py-4 py-md-5">
    <div class="row justify-content-center mb-4">
        <div class="col-lg-8 text-center">
            <h1 class="text-primary fw-bold mb-2 mb-md-3">Your Shopping Cart</h1>
            <p class="text-secondary">Review your selected packages before proceeding to checkout.</p>
        </div>
    </div>
    
    @if(count($cart) > 0)
    <div class="row">
        <!-- Cart Items - Full width on mobile, 8 cols on larger screens -->
        <div class="col-12 col-lg-8 mb-4">
            <div class="card shadow-sm rounded-4 border-0 mb-4">
                <div class="card-header bg-white py-3 rounded-top-4 border-0">
                    <h5 class="mb-0 fw-semibold">Package Items</h5>
                </div>
                <div class="card-body p-0">
                    <form action="{{ route('cart.update') }}" method="POST">
                        @csrf
                        <!-- Table for medium screens and up -->
                        <div class="table-responsive d-none d-md-block">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-4">Product</th>
                                        <th>Quantity</th>
                                        <th>Unit Price</th>
                                        <th>GST</th>
                                        <th>Total</th>
                                        <th class="text-end pe-4">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($cart as $index => $item)
                                    <tr>
                                        <td class="ps-4">
                                            <div class="fw-semibold">{{ $item['name'] }}</div>
                                        </td>
                                        <td>
                                            <div class="input-group input-group-sm" style="width: 120px;">
                                                <button type="button" class="btn btn-outline-primary" id="decrease-md-{{ $index }}">
                                                    <i class="fas fa-minus"></i>
                                                </button>
                                                <input type="number" name="quantities[{{ $index }}]" id="quantity-md-{{ $index }}" 
                                                       value="{{ $item['quantity'] }}" min="1" max="10"
                                                       class="form-control text-center">
                                                <button type="button" class="btn btn-outline-primary" id="increase-md-{{ $index }}">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </div>
                                        </td>
                                        <td>${{ number_format($item['price'], 2) }}</td>
                                        <td>${{ number_format($item['gst'] * $item['quantity'], 2) }}</td>
                                        <td class="fw-semibold">${{ number_format($item['total'], 2) }}</td>
                                        <td class="text-end pe-4">
                                            <a href="{{ route('cart.remove', $index) }}" class="btn btn-sm btn-outline-danger rounded-pill">
                                                <i class="fas fa-trash-alt me-1"></i> Remove
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Mobile view for small screens -->
                        <div class="d-md-none">
                            @foreach($cart as $index => $item)
                            <div class="p-3 border-bottom">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="fw-semibold mb-0">{{ $item['name'] }}</h6>
                                    <a href="{{ route('cart.remove', $index) }}" class="btn btn-sm btn-outline-danger">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-6">
                                        <small class="text-secondary">Unit Price:</small>
                                        <div>${{ number_format($item['price'], 2) }}</div>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-secondary">GST:</small>
                                        <div>${{ number_format($item['gst'] * $item['quantity'], 2) }}</div>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <small class="text-secondary">Quantity:</small>
                                        <div class="input-group input-group-sm mt-1" style="width: 110px;">
                                            <button type="button" class="btn btn-outline-primary" id="decrease-sm-{{ $index }}">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                            <input type="number" name="quantities[{{ $index }}]" id="quantity-sm-{{ $index }}" 
                                                   value="{{ $item['quantity'] }}" min="1" max="10"
                                                   class="form-control text-center">
                                            <button type="button" class="btn btn-outline-primary" id="increase-sm-{{ $index }}">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div>
                                        <small class="text-secondary">Total:</small>
                                        <div class="fw-semibold">${{ number_format($item['total'], 2) }}</div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        
                        <div class="card-footer bg-white py-3 px-4 rounded-bottom-4 border-0">
                            <div class="d-flex flex-column flex-md-row justify-content-between align-items-stretch gap-2">
                                <a href="{{ route('packages.index') }}" class="btn btn-outline-primary rounded-pill w-100 w-md-auto mb-2 mb-md-0 me-md-2">
                                    <i class="fas fa-arrow-left me-2"></i> Continue Shopping
                                </a>
                                <button type="submit" class="btn btn-primary rounded-pill w-100 w-md-auto">
                                    <i class="fas fa-sync-alt me-2"></i> Update Cart
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Order Summary - Full width on mobile, 4 cols on larger screens -->
        <div class="col-12 col-lg-4">
            <div class="card shadow-sm rounded-4 border-0 mb-4">
                <div class="card-header bg-primary text-white py-3 border-0 rounded-top-4">
                    <h5 class="mb-0 fw-semibold">Order Summary</h5>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-secondary">Subtotal</span>
                        <span>${{ number_format($total - ($total/11), 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-secondary">GST (10%)</span>
                        <span>${{ number_format($total/11, 2) }}</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-4">
                        <span class="fw-bold">Total</span>
                        <span class="fw-bold fs-5">${{ number_format($total, 2) }}</span>
                    </div>
                    
                    <a href="{{ route('cart.checkout') }}" class="btn btn-success w-100 py-3 rounded-pill fw-semibold">
                        <i class="fas fa-lock me-2"></i> Proceed to Checkout
                    </a>
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="row justify-content-center">
        <div class="col-12 col-md-10 col-lg-8">
            <div class="card shadow-sm rounded-4 border-0 p-4 p-md-5 text-center">
                <div class="py-3 py-md-4">
                    <i class="fas fa-shopping-cart text-secondary fa-3x fa-md-4x mb-3 mb-md-4"></i>
                    <h3>Your Cart is Empty</h3>
                    <p class="text-secondary mb-4">Looks like you haven't added any packages to your cart yet.</p>
                    <a href="{{ route('packages.index') }}" class="btn btn-primary rounded-pill px-4 py-2">
                        <i class="fas fa-shopping-bag me-2"></i> Browse Packages
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Quantity functionality for medium screens
        @foreach($cart as $index => $item)
        const decreaseMd{{ $index }} = document.getElementById('decrease-md-{{ $index }}');
        const increaseMd{{ $index }} = document.getElementById('increase-md-{{ $index }}');
        const quantityMd{{ $index }} = document.getElementById('quantity-md-{{ $index }}');
        
        if (decreaseMd{{ $index }}) {
            decreaseMd{{ $index }}.addEventListener('click', function() {
                const currentValue = parseInt(quantityMd{{ $index }}.value);
                if (currentValue > 1) {
                    quantityMd{{ $index }}.value = currentValue - 1;
                }
            });
        }
        
        if (increaseMd{{ $index }}) {
            increaseMd{{ $index }}.addEventListener('click', function() {
                const currentValue = parseInt(quantityMd{{ $index }}.value);
                if (currentValue < 10) {
                    quantityMd{{ $index }}.value = currentValue + 1;
                }
            });
        }
        
        // Quantity functionality for small screens
        const decreaseSm{{ $index }} = document.getElementById('decrease-sm-{{ $index }}');
        const increaseSm{{ $index }} = document.getElementById('increase-sm-{{ $index }}');
        const quantitySm{{ $index }} = document.getElementById('quantity-sm-{{ $index }}');
        
        if (decreaseSm{{ $index }}) {
            decreaseSm{{ $index }}.addEventListener('click', function() {
                const currentValue = parseInt(quantitySm{{ $index }}.value);
                if (currentValue > 1) {
                    quantitySm{{ $index }}.value = currentValue - 1;
                }
            });
        }
        
        if (increaseSm{{ $index }}) {
            increaseSm{{ $index }}.addEventListener('click', function() {
                const currentValue = parseInt(quantitySm{{ $index }}.value);
                if (currentValue < 10) {
                    quantitySm{{ $index }}.value = currentValue + 1;
                }
            });
        }
        @endforeach
    });
</script>
@endpush