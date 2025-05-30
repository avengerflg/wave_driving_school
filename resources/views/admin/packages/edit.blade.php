@extends('layouts.admin')

@section('title', 'Edit Package')

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">Admin / Packages /</span> Edit Package
        </h4>

        <div class="row">
            <div class="col-md-7">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Edit Package Details</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.packages.update', $package->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label for="name" class="form-label">Package Name</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $package->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4">{{ old('description', $package->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="price" class="form-label">Price ($)</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" class="form-control @error('price') is-invalid @enderror" id="price" name="price" value="{{ old('price', number_format($package->price, 2)) }}" min="0" step="0.01" required>
                                        @error('price')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="lessons" class="form-label">Number of Lessons</label>
                                    <input type="number" class="form-control @error('lessons') is-invalid @enderror" id="lessons" name="lessons" value="{{ old('lessons', $package->lessons) }}" min="1" step="1" required>
                                    @error('lessons')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label for="duration" class="form-label">Lesson Duration (min)</label>
                                    <input type="number" class="form-control @error('duration') is-invalid @enderror" id="duration" name="duration" value="{{ old('duration', $package->duration) }}" min="15" step="15" required>
                                    @error('duration')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-4">
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" id="active" name="active" {{ old('active', $package->active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="active">
                                        Active
                                    </label>
                                    <small class="text-muted d-block">If checked, this package will be available for purchase.</small>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="featured" name="featured" {{ old('featured', $package->featured) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="featured">
                                        Featured
                                    </label>
                                    <small class="text-muted d-block">If checked, this package will be highlighted on the website.</small>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end mt-4">
                                <a href="{{ route('admin.packages.show', $package->id) }}" class="btn btn-outline-secondary me-2">Cancel</a>
                                <button type="submit" class="btn btn-primary">Update Package</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-5">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Current Usage</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar avatar-lg bg-label-primary me-3">
                                <span class="avatar-initial"><i class="bx bx-package"></i></span>
                            </div>
                            <div>
                                <h6 class="mb-0">{{ $package->orders()->count() }} Orders</h6>
                                <small class="text-muted">Purchased this package</small>
                            </div>
                        </div>

                        <div class="alert alert-warning mb-0">
                            <div class="d-flex">
                                <i class="bx bx-info-circle me-2 mt-1"></i>
                                <div>
                                    <h6 class="alert-heading fw-bold mb-1">Important Note</h6>
                                    <p class="mb-0">Changing this package will not affect existing orders.</p>
                                    <p class="mb-0">Price changes will only apply to new purchases.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Helpful Tips</h5>
                    </div>
                    <div class="card-body">
                        <h6 class="fw-semibold mb-2">Common Lesson Combinations</h6>
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            <button type="button" class="btn btn-outline-primary btn-sm lesson-preset" data-value="5">5 lessons</button>
                            <button type="button" class="btn btn-outline-primary btn-sm lesson-preset" data-value="10">10 lessons</button>
                            <button type="button" class="btn btn-outline-primary btn-sm lesson-preset" data-value="15">15 lessons</button>
                            <button type="button" class="btn btn-outline-primary btn-sm lesson-preset" data-value="20">20 lessons</button>
                        </div>
                        
                        <h6 class="fw-semibold mb-2">Common Durations</h6>
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            <button type="button" class="btn btn-outline-primary btn-sm duration-preset" data-value="30">30 min</button>
                            <button type="button" class="btn btn-outline-primary btn-sm duration-preset" data-value="45">45 min</button>
                            <button type="button" class="btn btn-outline-primary btn-sm duration-preset" data-value="60">60 min</button>
                            <button type="button" class="btn btn-outline-primary btn-sm duration-preset" data-value="90">90 min</button>
                        </div>

                        <div class="card bg-light border-0 mt-3 mb-0">
                            <div class="card-body p-3">
                                <h6 class="fw-bold mb-1">Pricing Strategy</h6>
                                <p class="text-muted mb-0 small">
                                    Consider offering discounts for larger packages. For example, a 10-lesson package should cost less per lesson than a 5-lesson package.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Danger Zone</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-danger mb-3">
                            <h6 class="alert-heading fw-bold mb-1">Delete this Package</h6>
                            <p class="mb-2">Once deleted, you cannot recover this package.</p>
                            <button onclick="confirmDelete()" class="btn btn-danger btn-sm">
                                <i class="bx bx-trash me-1"></i> Delete Package
                            </button>
                        </div>
                        
                        <div class="alert alert-warning mb-0">
                            <h6 class="alert-heading fw-bold mb-1">Alternatively, Deactivate</h6>
                            <p class="mb-2">Instead of deleting, you can deactivate the package to hide it from users.</p>
                            <button onclick="document.getElementById('toggle-status-form').submit();" class="btn btn-warning btn-sm">
                                <i class="bx bx-hide me-1"></i> Deactivate Package
                            </button>
                        </div>
                        <form id="delete-form" action="{{ route('admin.packages.destroy', $package->id) }}" method="POST" style="display: none;">
                            @csrf
                            @method('DELETE')
                        </form>
                        <form id="toggle-status-form" action="{{ route('admin.packages.toggle-status', $package->id) }}" method="POST" style="display: none;">
                            @csrf
                            @method('PATCH')
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function confirmDelete() {
        if (confirm('Are you sure you want to delete this package? This action cannot be undone.')) {
            document.getElementById('delete-form').submit();
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Set up lesson preset buttons
        const lessonPresets = document.querySelectorAll('.lesson-preset');
        const lessonsInput = document.getElementById('lessons');
        
        lessonPresets.forEach(button => {
            button.addEventListener('click', function() {
                lessonsInput.value = this.getAttribute('data-value');
            });
        });
        
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