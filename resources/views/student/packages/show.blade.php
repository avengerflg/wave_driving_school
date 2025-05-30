@extends('layouts.student')

@section('title', $package->name)

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Breadcrumb -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold m-0">
                <span class="text-muted fw-light">Student / Packages /</span> {{ $package->name }}
            </h4>
            <div>
                <a href="{{ route('student.packages.index') }}" class="btn btn-outline-secondary">
                    <i class="bx bx-arrow-back me-1"></i> Back to Packages
                </a>
            </div>
        </div>

        <div class="row">
            <!-- Package details -->
            <div class="col-md-8 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="card-title mb-0">{{ $package->name }}</h4>
                            @if($package->featured)
                            <span class="badge bg-primary">Featured</span>
                            @endif
                        </div>
                        
                        <div class="mb-4">
                            <p>{{ $package->description }}</p>
                        </div>
                        
                        <div class="d-flex flex-wrap gap-3 mb-4">
                            <div class="badge bg-label-primary p-2">
                                <i class="bx bx-car me-1"></i> {{ $package->lessons }} Lessons
                            </div>
                            
                            @if($package->expiry_days)
                            <div class="badge bg-label-secondary p-2">
                                <i class="bx bx-time me-1"></i> Valid for {{ $package->expiry_days }} days
                            </div>
                            @endif
                            
                            @if($package->transferable)
                            <div class="badge bg-label-success p-2">
                                <i class="bx bx-transfer me-1"></i> Transferable
                            </div>
                            @endif
                        </div>
                        
                        <div class="mb-4">
                            <h5>Package Features</h5>
                            <ul class="list-unstyled mt-3">
                                @foreach(explode("\n", $package->features ?? '') as $feature)
                                    @if(trim($feature))
                                    <li class="d-flex align-items-center mb-2">
                                        <i class="bx bx-check-circle text-success me-2"></i>
                                        <span>{{ trim($feature) }}</span>
                                    </li>
                                    @endif
                                @endforeach
                            </ul>
                        </div>
                        
                        @if($package->terms)
                        <div class="mb-0">
                            <h5>Terms & Conditions</h5>
                            <div class="p-3 bg-lighter rounded mt-3">
                                <p class="mb-0">{{ $package->terms }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Purchase card -->
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-3">Purchase Package</h4>
                        
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="fs-4 fw-semibold text-primary">${{ number_format($package->price, 2) }}</span>
                            @if($package->original_price && $package->original_price > $package->price)
                            <span class="text-decoration-line-through text-muted">
                                ${{ number_format($package->original_price, 2) }}
                            </span>
                            @endif
                        </div>
                        
                        <form action="{{ route('cart.add') }}" method="POST">
                            @csrf
                            <input type="hidden" name="package_id" value="{{ $package->id }}">
                            
                            <div class="mb-3">
                                <label for="quantity" class="form-label">Quantity</label>
                                <input type="number" id="quantity" name="quantity" class="form-control" value="1" min="1" max="10">
                                <small class="text-muted">Choose how many packages you want to purchase</small>
                            </div>
                            
                            <div class="d-grid gap-2 mb-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bx bx-cart-add me-1"></i> Add to Cart
                                </button>
                                <a href="{{ route('booking.index') }}?package={{ $package->id }}" class="btn btn-outline-primary">
                                    <i class="bx bx-calendar-plus me-1"></i> Book Now
                                </a>
                            </div>
                        </form>
                        
                        <div class="mt-4">
                            <h6>Payment & Booking</h6>
                            <ul class="list-unstyled mt-2">
                                <li class="d-flex align-items-center mb-2">
                                    <i class="bx bx-credit-card text-primary me-2"></i>
                                    <small>Secure payment via credit card</small>
                                </li>
                                <li class="d-flex align-items-center mb-2">
                                    <i class="bx bx-check-shield text-primary me-2"></i>
                                    <small>Book with instructors in your area</small>
                                </li>
                                <li class="d-flex align-items-center mb-0">
                                    <i class="bx bx-calendar-check text-primary me-2"></i>
                                    <small>Flexible scheduling options</small>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection