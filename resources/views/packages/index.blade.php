@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center mb-5">
        <div class="col-lg-8 text-center">
            <h1 class="text-primary fw-bold mb-3">Driving Lesson Packages</h1>
            <p class="text-secondary fs-5 mb-4">
                Choose from our selection of <span class="text-primary fw-semibold">premium driving packages</span> designed to help you become a confident driver. Bundle lessons for better value.
            </p>
        </div>
    </div>
    
    <div class="row justify-content-center">
        @foreach($packages as $package)
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card h-100 shadow rounded-4 border-0">
                <div class="card-header bg-primary text-white text-center py-4 border-0 rounded-top-4">
                    <h3 class="fs-4 fw-bold mb-3">{{ $package->name }}</h3>
                    <div class="d-flex align-items-start justify-content-center">
                        <span class="fs-5 mt-1 me-1">$</span>
                        <span class="display-4 fw-bold lh-1">{{ number_format($package->price, 0) }}</span>
                        <span class="fs-5 mt-2">.{{ substr(number_format($package->price, 2), -2) }}</span>
                    </div>
                </div>
                
                <div class="card-body p-4 d-flex flex-column">
                    <ul class="list-group list-group-flush mb-3">
                        <li class="list-group-item border-0 ps-0 d-flex align-items-center">
                            <i class="fas fa-check-circle text-primary me-3"></i>
                            <span>{{ $package->lessons }} driving lessons</span>
                        </li>
                        <li class="list-group-item border-0 ps-0 d-flex align-items-center">
                            <i class="fas fa-clock text-primary me-3"></i>
                            <span>{{ $package->duration }} minutes per lesson</span>
                        </li>
                        <li class="list-group-item border-0 ps-0 d-flex align-items-center">
                            <i class="fas fa-calendar-check text-primary me-3"></i>
                            <span>Flexible scheduling</span>
                        </li>
                        <li class="list-group-item border-0 ps-0 d-flex align-items-center">
                            <i class="fas fa-user-shield text-primary me-3"></i>
                            <span>Professional instructors</span>
                        </li>
                    </ul>
                    
                    <p class="card-text text-secondary mb-4 flex-grow-1">{{ $package->description }}</p>
                    
                    <form action="{{ route('cart.add') }}" method="POST">
                        @csrf
                        <input type="hidden" name="package_id" value="{{ $package->id }}">
                        
                        <div class="mb-3">
                            <label for="quantity-{{ $package->id }}" class="form-label fw-semibold text-secondary">
                                <i class="fas fa-shopping-basket me-2"></i> Quantity:
                            </label>
                            <div class="input-group">
                                <button type="button" class="btn btn-outline-primary" id="decrease-{{ $package->id }}">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <input type="number" name="quantity" id="quantity-{{ $package->id }}" 
                                       class="form-control text-center" value="1" min="1" max="10">
                                <button type="button" class="btn btn-outline-primary" id="increase-{{ $package->id }}">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100 py-3 rounded-pill fw-semibold">
                            <i class="fas fa-cart-plus me-2"></i>
                            <span>Add to Cart</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Quantity increment/decrement functionality
        @foreach($packages as $package)
        const decrease{{ $package->id }} = document.getElementById('decrease-{{ $package->id }}');
        const increase{{ $package->id }} = document.getElementById('increase-{{ $package->id }}');
        const quantity{{ $package->id }} = document.getElementById('quantity-{{ $package->id }}');
        
        decrease{{ $package->id }}.addEventListener('click', function() {
            const currentValue = parseInt(quantity{{ $package->id }}.value);
            if (currentValue > 1) {
                quantity{{ $package->id }}.value = currentValue - 1;
            }
        });
        
        increase{{ $package->id }}.addEventListener('click', function() {
            const currentValue = parseInt(quantity{{ $package->id }}.value);
            if (currentValue < 10) {
                quantity{{ $package->id }}.value = currentValue + 1;
            }
        });
        @endforeach
    });
</script>
@endpush