@extends('layouts.student')

@section('title', 'Available Packages')

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Breadcrumb -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold m-0">
                <span class="text-muted fw-light">Student /</span> Available Packages
            </h4>
            <div>
                <a href="{{ route('student.packages.credits') }}" class="btn btn-outline-primary">
                    <i class="bx bx-credit-card me-1"></i> My Credits
                </a>
                <a href="{{ route('student.packages.orders') }}" class="btn btn-outline-primary ms-2">
                    <i class="bx bx-shopping-bag me-1"></i> My Orders
                </a>
            </div>
        </div>

        @if(session('success'))
        <div class="alert alert-success alert-dismissible mb-4" role="alert">
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger alert-dismissible mb-4" role="alert">
            <div>{{ session('error') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <!-- Packages list -->
        <div class="row">
            @forelse($packages as $package)
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100">
                    @if($package->featured)
                    <div class="badge bg-primary position-absolute end-0 mt-3 me-3">
                        Featured
                    </div>
                    @endif
                    
                    <div class="card-body">
                        <h5 class="card-title">{{ $package->name }}</h5>
                        <h2 class="my-3 text-primary">${{ number_format($package->price, 2) }}</h2>
                        <p class="card-text">{{ $package->description }}</p>
                        
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <span class="badge bg-label-info fs-6">
                                    <i class="bx bx-car me-1"></i> {{ $package->lessons }} Lessons
                                </span>
                            </div>
                            @if($package->expiry_days)
                            <div>
                                <span class="badge bg-label-secondary">
                                    Valid for {{ $package->expiry_days }} days
                                </span>
                            </div>
                            @endif
                        </div>
                        
                        <div class="mb-3">
                            <ul class="list-unstyled">
                                @foreach(explode("\n", $package->features ?? '') as $feature)
                                    @if(trim($feature))
                                    <li class="mb-2">
                                        <i class="bx bx-check text-success me-2"></i> {{ trim($feature) }}
                                    </li>
                                    @endif
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="d-grid gap-2">
                            <a href="{{ route('packages.show', $package->id) }}" class="btn btn-primary">
                                View Details
                            </a>
                            <form action="{{ route('cart.add') }}" method="POST">
                                @csrf
                                <input type="hidden" name="package_id" value="{{ $package->id }}">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" class="btn btn-outline-primary w-100">
                                    <i class="bx bx-cart-add me-1"></i> Add to Cart
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <img src="{{ asset('assets/img/illustrations/no-packages.svg') }}" alt="No packages" class="mb-3" style="max-width: 180px;">
                        <h5>No Packages Available</h5>
                        <p class="mb-0">There are currently no driving lesson packages available. Please check back later.</p>
                    </div>
                </div>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection