
@extends('layouts.admin')

@section('title', 'Create Service')

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">Admin / Services /</span> Create New Service
        </h4>

        <div class="row">
            <div class="col-md-7">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">New Service Details</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.services.store') }}" method="POST">
    @csrf
    
    <div class="mb-3">
        <label for="name" class="form-label">Service Name</label>
        <input type="text" 
               class="form-control @error('name') is-invalid @enderror" 
               id="name" 
               name="name" 
               value="{{ old('name') }}" 
               required>
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="description" class="form-label">Description</label>
        <textarea class="form-control @error('description') is-invalid @enderror" 
                  id="description" 
                  name="description">{{ old('description') }}</textarea>
        @error('description')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="price" class="form-label">Price</label>
        <input type="number" 
               class="form-control @error('price') is-invalid @enderror" 
               id="price" 
               name="price" 
               value="{{ old('price', '0.00') }}" 
               step="0.01" 
               min="0" 
               required>
        @error('price')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="duration" class="form-label">Duration (minutes)</label>
        <input type="number" 
               class="form-control @error('duration') is-invalid @enderror" 
               id="duration" 
               name="duration" 
               value="{{ old('duration', 60) }}" 
               min="15" 
               step="15" 
               required>
        @error('duration')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <div class="form-check">
            <input type="checkbox" 
                   class="form-check-input" 
                   id="active" 
                   name="active" 
                   {{ old('active', true) ? 'checked' : '' }}>
            <label class="form-check-label" for="active">Active</label>
        </div>
    </div>

    <div class="mb-3">
        <div class="form-check">
            <input type="checkbox" 
                   class="form-check-input" 
                   id="featured" 
                   name="featured" 
                   {{ old('featured') ? 'checked' : '' }}>
            <label class="form-check-label" for="featured">Featured</label>
        </div>
    </div>

    <button type="submit" class="btn btn-primary">Create Service</button>
</form>
                    </div>
                </div>
            </div>
            <div class="col-md-5">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Guidelines</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info mb-3">
                            <h6 class="alert-heading fw-bold mb-1">Service Creation Tips</h6>
                            <ul class="mb-0 ps-3">
                                <li>Use clear and descriptive names for services</li>
                                <li>Set appropriate prices based on market research</li>
                                <li>Choose standard durations (30, 45, 60, 90 minutes)</li>
                                <li>Include detailed descriptions to help customers choose</li>
                                <li>Only select "Featured" for your most popular services</li>
                            </ul>
                        </div>

                        <h6 class="fw-semibold mb-2">Pricing Strategy</h6>
                        <p class="text-muted small">
                            Consider setting different price points for different service durations. 
                            Longer sessions often have better value per minute to encourage longer bookings.
                        </p>
                        
                        <h6 class="fw-semibold mb-2">Common Durations</h6>
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            <button type="button" class="btn btn-outline-primary btn-sm duration-preset" data-value="30">30 min</button>
                            <button type="button" class="btn btn-outline-primary btn-sm duration-preset" data-value="45">45 min</button>
                            <button type="button" class="btn btn-outline-primary btn-sm duration-preset" data-value="60">60 min</button>
                            <button type="button" class="btn btn-outline-primary btn-sm duration-preset" data-value="90">90 min</button>
                            <button type="button" class="btn btn-outline-primary btn-sm duration-preset" data-value="120">120 min</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Set up duration preset buttons
        const durationPresets = document.querySelectorAll('.duration-preset');
        const durationInput = document.getElementById('duration');
        
        durationPresets.forEach(button => {
            button.addEventListener('click', function() {
                durationInput.value = this.getAttribute('data-value');
            });
        });

        // Format price on blur to ensure 2 decimal places
        const priceInput = document.getElementById('price');
        priceInput.addEventListener('blur', function() {
            if (this.value) {
                this.value = parseFloat(this.value).toFixed(2);
            }
        });
    });
</script>
@endsection