@extends('layouts.instructor')

@section('title', 'Available Services')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="fw-bold m-0">
                        <i class="bx bx-package text-primary me-2"></i>
                        Available Services
                    </h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Services Grid -->
    <div class="row g-4">
        @forelse($services as $service)
        <div class="col-md-6 col-lg-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3">
                        <h5 class="card-title mb-0">{{ $service->name }}</h5>
                        <span class="badge bg-label-primary">{{ $service->formatted_price }}</span>
                    </div>
                    
                    <p class="card-text text-muted mb-3">{{ Str::limit($service->description, 100) }}</p>
                    
                    <div class="d-flex align-items-center mb-3">
                        <i class="bx bx-time-five me-1"></i>
                        <span class="text-muted">{{ $service->formatted_duration }}</span>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route('instructor.services.show', $service) }}" 
                           class="btn btn-primary">
                            View Details
                        </a>
                        <span class="text-end">
                            @if($service->featured)
                            <span class="badge bg-label-warning">Featured</span>
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="bx bx-store text-secondary mb-2" style="font-size: 3rem;"></i>
                    <h5 class="text-secondary">No Services Available</h5>
                    <p class="text-muted mb-0">There are no active services at the moment.</p>
                </div>
            </div>
        </div>
        @endforelse
    </div>
</div>
@endsection