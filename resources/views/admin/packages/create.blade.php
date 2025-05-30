@extends('layouts.admin')

@section('title', 'Create Package')

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">Admin / Packages /</span> Create New Package
        </h4>

        <div class="row">
            <div class="col-md-7">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">New Package Details</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.packages.store') }}" method="POST">
                            @csrf
                            
                            <div class="mb-3">
                                <label for="name" class="form-label">Package Name</label>
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
                                          name="description" 
                                          rows="4">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="price" class="form-label">Price ($)</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" 
                                               class="form-control @error('price') is-invalid @enderror" 
                                               id="price" 
                                               name="price" 
                                               value="{{ old('price', '0.00') }}" 
                                               min="0" 
                                               step="0.01" 
                                               required>
                                        @error('price')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="lessons" class="form-label">Number of Lessons</label>
                                    <input type="number" 
                                           class="form-control @error('lessons') is-invalid @enderror" 
                                           id="lessons" 
                                           name="lessons" 
                                           value="{{ old('lessons', 1) }}" 
                                           min="1" 
                                           required>
                                    @error('lessons')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
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
                            </div>

                            <div class="mb-4">
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" id="active" name="active" {{ old('active', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="active">
                                        Active
                                    </label>
                                    <small class="text-muted d-block">If checked, this package will be available for purchase.</small>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="featured" name="featured" {{ old('featured') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="featured">
                                        Featured
                                    </label>
                                    <small class="text-muted d-block">If checked, this package will be highlighted on the website.</small>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end mt-4">
                                <a href="{{ route('admin.packages.index') }}" class="btn btn-outline-secondary me-2">Cancel</a>
                                <button type="submit" class="btn btn-primary">Create Package</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-5">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Package Creation Tips</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info mb-3">
                            <h6 class="alert-heading fw-bold mb-1">Best Practices</h6>
                            <ul class="mb-0 ps-3">
                                <li>Create packages with different lesson counts to offer variety</li>
                                <li>Offer better value per lesson in larger packages</li>
                                <li>Use clear and descriptive names for packages</li>
                                <li>Include detailed descriptions highlighting the benefits</li>
                                <li>Only select "Featured" for your best value packages</li>
                            </ul>
                        </div>

                        <h6 class="fw-semibold mb-2">Common Lesson Packages</h6>
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            <button type="button" class="btn btn-outline-primary btn-sm package-preset" data-lessons="1" data-price="130.00">1 Lesson</button>
                            <button type="button" class="btn btn-outline-primary btn-sm package-preset" data-lessons="5" data-price="600.00">5 Lessons</button>
                            <button type="button" class="btn btn-outline-primary btn-sm package-preset" data-lessons="10" data-price="1150.00">10 Lessons</button>
                            <button type="button" class="btn btn-outline-primary btn-sm package-preset" data-lessons="15" data-price="1650.00">15 Lessons</button>
                        </div>
                        
                        <h6 class="fw-semibold mb-2">Common Durations</h6>
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            <button type="button" class="btn btn-outline-primary btn-sm duration-preset" data-value="45">45 min</button>
                            <button type="button" class="btn btn-outline-primary btn-sm duration-preset" data-value="60">60 min</button>
                            <button type="button" class="btn btn-outline-primary btn-sm duration-preset" data-value="90">90 min</button>
                            <button type="button" class="btn btn-outline-primary btn-sm duration-preset" data-value="120">120 min</button>
                        </div>
                        
                        <div class="card bg-light border-0 mt-3 mb-0">
                            <div class="card-body p-3">
                                <h6 class="fw-bold mb-1">Pricing Strategy</h6>
                                <p class="text-muted mb-0 small">
                                    Consider offering discounts for larger packages to encourage customers to commit to more lessons.
                                    For example, if a single lesson is $130, a 10-lesson package could be $1150 (saving $150).
                                </p>
                            </div>
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

        // Set up package preset buttons
        const packagePresets = document.querySelectorAll('.package-preset');
        const lessonsInput = document.getElementById('lessons');
        const priceInput = document.getElementById('price');
        
        packagePresets.forEach(button => {
            button.addEventListener('click', function() {
                lessonsInput.value = this.getAttribute('data-lessons');
                priceInput.value = this.getAttribute('data-price');
            });
        });

        // Format price on blur to ensure 2 decimal places
        priceInput.addEventListener('blur', function() {
            if (this.value) {
                this.value = parseFloat(this.value).toFixed(2);
            }
        });
    });
</script>
@endsection