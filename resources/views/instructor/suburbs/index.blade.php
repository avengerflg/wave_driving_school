@extends('layouts.instructor')

@section('title', 'Service Areas')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="fw-bold m-0">
                        <i class="bx bx-map text-primary me-2"></i>
                        Service Areas
                    </h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('instructor.suburbs.index') }}" class="row g-3">
                <div class="col-md-5">
                    <label class="form-label">Search</label>
                    <input type="text" class="form-control" name="search" 
                           value="{{ request('search') }}" 
                           placeholder="Search by suburb name or postcode">
                </div>
                <div class="col-md-5">
                    <label class="form-label">State</label>
                    <select class="form-select" name="state">
                        <option value="">All States</option>
                        @foreach($states as $state)
                            <option value="{{ $state }}" {{ request('state') == $state ? 'selected' : '' }}>
                                {{ $state }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <div class="d-flex gap-2 w-100">
                        <button type="submit" class="btn btn-primary flex-grow-1">
                            <i class="bx bx-search me-1"></i> Search
                        </button>
                        @if(request()->hasAny(['search', 'state']))
                            <a href="{{ route('instructor.suburbs.index') }}" class="btn btn-outline-secondary">
                                <i class="bx bx-reset"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Suburbs Grid -->
    <div class="row g-4">
        @forelse($suburbs as $suburb)
        <div class="col-md-6 col-lg-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h5 class="card-title mb-1">{{ $suburb->name }}</h5>
                            <span class="text-muted">{{ $suburb->state }} {{ $suburb->postcode }}</span>
                        </div>
                        <span class="badge bg-label-primary">
                            {{ $suburb->bookings_count }} bookings
                        </span>
                    </div>

                    <a href="{{ route('instructor.suburbs.show', $suburb) }}" 
                       class="btn btn-primary w-100">
                        <i class="bx bx-show-alt me-1"></i> View Details
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="bx bx-map-alt text-secondary mb-2" style="font-size: 3rem;"></i>
                    <h6 class="text-secondary mb-1">No suburbs found</h6>
                    <p class="text-muted mb-0">Try adjusting your search criteria</p>
                </div>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($suburbs->hasPages())
    <div class="card mt-4">
        <div class="card-body">
            {{ $suburbs->links() }}
        </div>
    </div>
    @endif
</div>
@endsection