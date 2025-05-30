<!-- filepath: resources/views/admin/clients/edit.blade.php -->

@extends('layouts.admin')

@section('title', 'Edit Client')

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
                                <i class="bx bx-edit-alt text-primary me-2"></i>
                                <span class="text-muted fw-light">Admin / Clients /</span> Edit Client
                            </h4>
                            <div>
                                <a href="{{ route('admin.clients.show', $client->id) }}" class="btn btn-secondary me-2">
                                    <i class="bx bx-arrow-back me-1"></i> Back
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Form Card -->
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <i class="bx bx-user-circle text-primary fs-4 me-2"></i>
                    <h5 class="card-title mb-0">Client Information</h5>
                </div>
                <div class="badge bg-label-{{ $client->status == 'active' ? 'success' : 'danger' }} ms-auto">
                    {{ ucfirst($client->status) }}
                </div>
            </div>
            
            <div class="card-body">
                <form method="POST" action="{{ route('admin.clients.update', $client->id) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <!-- Personal Information -->
                        <div class="col-md-6">
                            <div class="card shadow-none border mb-4">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="bx bx-user me-1"></i> Personal Information</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Full Name</label>
                                        <input
                                            type="text"
                                            id="name"
                                            name="name"
                                            class="form-control @error('name') is-invalid @enderror"
                                            value="{{ old('name', $client->name) }}"
                                            required
                                        />
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input
                                            type="email"
                                            id="email"
                                            name="email"
                                            class="form-control @error('email') is-invalid @enderror"
                                            value="{{ old('email', $client->email) }}"
                                            required
                                        />
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="phone" class="form-label">Phone Number</label>
                                        <input
                                            type="text"
                                            id="phone"
                                            name="phone"
                                            class="form-control @error('phone') is-invalid @enderror"
                                            value="{{ old('phone', $client->phone) }}"
                                        />
                                        @error('phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="mb-0">
                                        <label for="date_of_birth" class="form-label">Date of Birth</label>
                                        <input
                                            type="date"
                                            id="date_of_birth"
                                            name="date_of_birth"
                                            class="form-control @error('date_of_birth') is-invalid @enderror"
                                            value="{{ old('date_of_birth', $client->date_of_birth ? $client->date_of_birth->format('Y-m-d') : null) }}"
                                        />
                                        @error('date_of_birth')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Account Details -->
                        <div class="col-md-6">
                            <div class="card shadow-none border mb-4">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="bx bx-lock me-1"></i> Account Details</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="password" class="form-label">New Password <small class="text-muted">(leave blank to keep current password)</small></label>
                                        <input
                                            type="password"
                                            id="password"
                                            name="password"
                                            class="form-control @error('password') is-invalid @enderror"
                                        />
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="password_confirmation" class="form-label">Confirm New Password</label>
                                        <input
                                            type="password"
                                            id="password_confirmation"
                                            name="password_confirmation"
                                            class="form-control"
                                        />
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="address" class="form-label">Address</label>
                                        <textarea
                                            id="address"
                                            name="address"
                                            class="form-control @error('address') is-invalid @enderror"
                                            rows="2"
                                        >{{ old('address', $client->address) }}</textarea>
                                        @error('address')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="mb-0">
                                        <label for="license_number" class="form-label">License Number</label>
                                        <input
                                            type="text"
                                            id="license_number"
                                            name="license_number"
                                            class="form-control @error('license_number') is-invalid @enderror"
                                            value="{{ old('license_number', $client->license_number) }}"
                                        />
                                        @error('license_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Additional Information -->
                        <div class="col-12">
                            <div class="card shadow-none border mb-4">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="bx bx-info-circle me-1"></i> Additional Information</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="profile_image" class="form-label">Profile Image</label>
                                                <input 
                                                    type="file" 
                                                    id="profile_image" 
                                                    name="profile_image" 
                                                    class="form-control @error('profile_image') is-invalid @enderror" 
                                                    accept="image/*" 
                                                />
                                                @error('profile_image')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                
                                                @if($client->profile_image)
                                                <div class="mt-2">
                                                    <img src="{{ Storage::url($client->profile_image) }}" alt="Current Profile" width="100" class="rounded border">
                                                    <p class="text-muted mt-1 mb-0 small">Current profile image</p>
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="status" class="form-label">Status</label>
                                                <select id="status" name="status" class="form-select @error('status') is-invalid @enderror" required>
                                                    <option value="active" {{ old('status', $client->status) == 'active' ? 'selected' : '' }}>Active</option>
                                                    <option value="inactive" {{ old('status', $client->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                                </select>
                                                @error('status')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="mt-2 d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.clients.show', $client->id) }}" class="btn btn-outline-secondary">
                            <i class="bx bx-x me-1"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-save me-1"></i> Update Client
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection