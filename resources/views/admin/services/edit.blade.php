
@extends('layouts.admin')

@section('title', 'Edit Service')

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">Admin / Services /</span> Edit Service
        </h4>

        <div class="row">
            <div class="col-md-7">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Edit Service Details</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.services.update', $service->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label for="name" class="form-label">Service Name</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $service->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4">{{ old('description', $service->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="price" class="form-label">Price ($)</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" class="form-control @error('price') is-invalid @enderror" id="price" name="price" value="{{ old('price', number_format($service->price, 2)) }}" min="0" step="0.01" required>
                                        @error('price')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="duration" class="form-label">Duration (minutes)</label>
                                    <input type="number" class="form-control @error('duration') is-invalid @enderror" id="duration" name="duration" value="{{ old('duration', $service->duration) }}" min="15" step="15" required>
                                    @error('duration')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">In minutes (e.g., 60 for 1 hour)</small>
                                </div>
                            </div>

                            <div class="mb-4">
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" id="active" name="active" {{ old('active', $service->active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="active">
                                        Active
                                    </label>
                                    <small class="text-muted d-block">If checked, this service will be available for booking.</small>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="featured" name="featured" {{ old('featured', $service->featured) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="featured">
                                        Featured
                                    </label>
                                    <small class="text-muted d-block">If checked, this service will be highlighted on the website.</small>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end mt-4">
                                <a href="{{ route('admin.services.show', $service->id) }}" class="btn btn-outline-secondary me-2">Cancel</a>
                                <button type="submit" class="btn btn-primary">Update Service</button>
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
                                <span class="avatar-initial"><i class="bx bx-calendar"></i></span>
                            </div>
                            <div>
                                <h6 class="mb-0">{{ $service->bookings()->count() }} Bookings</h6>
                                <small class="text-muted">Used this service</small>
                            </div>
                        </div>

                        <div class="alert alert-warning mb-0">
                            <div class="d-flex">
                                <i class="bx bx-info-circle me-2 mt-1"></i>
                                <div>
                                    <h6 class="alert-heading fw-bold mb-1">Important Note</h6>
                                    <p class="mb-0">Changing this service will not affect existing bookings.</p>
                                    <p class="mb-0">Price changes will only apply to new bookings.</p>
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
                        <h6 class="fw-semibold mb-2">Common Durations</h6>
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            <button type="button" class="btn btn-outline-primary btn-sm duration-preset" data-value="30">30 min</button>
                            <button type="button" class="btn btn-outline-primary btn-sm duration-preset" data-value="45">45 min</button>
                            <button type="button" class="btn btn-outline-primary btn-sm duration-preset" data-value="60">60 min</button>
                            <button type="button" class="btn btn-outline-primary btn-sm duration-preset" data-value="90">90 min</button>
                            <button type="button" class="btn btn-outline-primary btn-sm duration-preset" data-value="120">120 min</button>
                        </div>

                        <div class="card bg-light border-0 mt-3 mb-0">
                            <div class="card-body p-3">
                                <h6 class="fw-bold mb-1">Pricing Strategy</h6>
                                <p class="text-muted mb-0 small">
                                    Consider your pricing strategy carefully. Price too high, and you might lose customers. 
                                    Price too low, and you might undervalue your services.
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
                            <h6 class="alert-heading fw-bold mb-1">Delete this Service</h6>
                            <p class="mb-2">Once deleted, you cannot recover this service.</p>
                            <button onclick="confirmDelete()" class="btn btn-danger btn-sm">
                                <i class="bx bx-trash me-1"></i> Delete Service
                            </button>
                        </div>
                        
                        <div class="alert alert-warning mb-0">
                            <h6 class="alert-heading fw-bold mb-1">Alternatively, Deactivate</h6>
                            <p class="mb-2">Instead of deleting, you can deactivate the service to hide it from users.</p>
                            <button onclick="document.getElementById('toggle-status-form').submit();" class="btn btn-warning btn-sm">
                                <i class="bx bx-hide me-1"></i> Deactivate Service
                            </button>
                        </div>
                        <form id="delete-form" action="{{ route('admin.services.destroy', $service->id) }}" method="POST" style="display: none;">
                            @csrf
                            @method('DELETE')
                        </form>
                        <form id="toggle-status-form" action="{{ route('admin.services.toggle-status', $service->id) }}" method="POST" style="display: none;">
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
        if (confirm('Are you sure you want to delete this service? This action cannot be undone.')) {
            document.getElementById('delete-form').submit();
        }
    }

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