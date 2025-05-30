<!-- filepath: resources/views/admin/instructors/create.blade.php -->

@extends('layouts.admin')

@section('title', 'Add New Instructor')

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Header Card -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="fw-bold m-0">
                                <i class="bx bx-user-plus text-primary me-2"></i>
                                <span class="text-muted fw-light">Admin / Instructors /</span> Add New Instructor
                            </h4>
                            <div>
                                <a href="{{ route('admin.instructors.index') }}" class="btn btn-secondary">
                                    <i class="bx bx-arrow-back me-1"></i> Back to Instructors
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Instructor Form -->
        <form method="POST" action="{{ route('admin.instructors.store') }}" enctype="multipart/form-data">
            @csrf
            
            <div class="row">
                <!-- Left Column -->
                <div class="col-md-6">
                    <div class="card mb-4">
                        <h5 class="card-header">Account Information</h5>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input 
                                    type="text" 
                                    class="form-control @error('name') is-invalid @enderror" 
                                    id="name" 
                                    name="name" 
                                    value="{{ old('name') }}" 
                                    required
                                >
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input 
                                    type="email" 
                                    class="form-control @error('email') is-invalid @enderror" 
                                    id="email" 
                                    name="email" 
                                    value="{{ old('email') }}" 
                                    required
                                >
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input 
                                    type="text" 
                                    class="form-control @error('phone') is-invalid @enderror" 
                                    id="phone" 
                                    name="phone" 
                                    value="{{ old('phone') }}"
                                >
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                                <input 
                                    type="password" 
                                    class="form-control @error('password') is-invalid @enderror" 
                                    id="password" 
                                    name="password" 
                                    required
                                >
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                                <input 
                                    type="password" 
                                    class="form-control" 
                                    id="password_confirmation" 
                                    name="password_confirmation" 
                                    required
                                >
                            </div>
                            
                            <div class="mb-0">
                                <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                <select 
                                    class="form-select @error('status') is-invalid @enderror" 
                                    id="status" 
                                    name="status" 
                                    required
                                >
                                    <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="card mb-4">
                        <h5 class="card-header">Address Details</h5>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="address" class="form-label">Address</label>
                                <textarea 
                                    class="form-control @error('address') is-invalid @enderror" 
                                    id="address" 
                                    name="address" 
                                    rows="2"
                                >{{ old('address') }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-0">
                                <label for="suburb_id" class="form-label">Home Suburb</label>
                                <select 
                                    class="form-select @error('suburb_id') is-invalid @enderror" 
                                    id="suburb_id" 
                                    name="suburb_id"
                                >
                                    <option value="">Select Suburb</option>
                                    @foreach($suburbs as $suburb)
                                        <option value="{{ $suburb->id }}" {{ old('suburb_id') == $suburb->id ? 'selected' : '' }}>
                                            {{ $suburb->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('suburb_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Right Column -->
                <div class="col-md-6">
                    <div class="card mb-4">
                        <h5 class="card-header">Instructor Details</h5>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="license_number" class="form-label">License Number <span class="text-danger">*</span></label>
                                <input 
                                    type="text" 
                                    class="form-control @error('license_number') is-invalid @enderror" 
                                    id="license_number" 
                                    name="license_number" 
                                    value="{{ old('license_number') }}"
                                    required
                                >
                                @error('license_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label for="profile_image" class="form-label">Profile Image</label>
                                <input 
                                    type="file" 
                                    class="form-control @error('profile_image') is-invalid @enderror" 
                                    id="profile_image" 
                                    name="profile_image"
                                    accept="image/*"
                                >
                                @error('profile_image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-0">
                                <label for="bio" class="form-label">Biography</label>
                                <textarea 
                                    class="form-control @error('bio') is-invalid @enderror" 
                                    id="bio" 
                                    name="bio" 
                                    rows="4"
                                >{{ old('bio') }}</textarea>
                                @error('bio')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="card mb-4">
                        <h5 class="card-header">Service Areas</h5>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label d-block">Select Service Suburbs <span class="text-danger">*</span></label>
                                <div class="p-2 border rounded mb-2" style="max-height: 200px; overflow-y: auto;">
                                    @foreach($suburbs->chunk(3) as $chunk)
                                        <div class="row">
                                            @foreach($chunk as $suburb)
                                                <div class="col-md-4 col-6 mb-1">
                                                    <div class="form-check">
                                                        <input 
                                                            class="form-check-input" 
                                                            type="checkbox" 
                                                            name="service_suburbs[]" 
                                                            value="{{ $suburb->id }}" 
                                                            id="suburb{{ $suburb->id }}"
                                                            {{ in_array($suburb->id, old('service_suburbs', [])) ? 'checked' : '' }}
                                                        >
                                                        <label class="form-check-label" for="suburb{{ $suburb->id }}">
                                                            {{ $suburb->name }}
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endforeach
                                </div>
                                @error('service_suburbs')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-12 text-center mb-4">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="bx bx-save me-1"></i> Create Instructor
                    </button>
                    <a href="{{ route('admin.instructors.index') }}" class="btn btn-secondary">
                        <i class="bx bx-x me-1"></i> Cancel
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection