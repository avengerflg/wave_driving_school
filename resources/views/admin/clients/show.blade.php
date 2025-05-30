<!-- filepath: resources/views/admin/clients/show.blade.php -->

@extends('layouts.admin')

@section('title', 'Client Details')

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
                                <i class="bx bx-user-circle text-primary me-2"></i>
                                <span class="text-muted fw-light">Admin / Clients /</span> Client Details
                            </h4>
                            <div>
                                <a href="{{ route('admin.clients.index') }}" class="btn btn-secondary me-2">
                                    <i class="bx bx-arrow-back me-1"></i> Back
                                </a>
                                <a href="{{ route('admin.clients.edit', $client->id) }}" class="btn btn-warning">
                                    <i class="bx bx-edit me-1"></i> Edit
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Left Column - Profile Information -->
            <div class="col-xl-4 col-lg-5 col-md-12">
                <!-- Profile Card -->
                <div class="card mb-4">
                    <div class="card-body text-center pt-4 pb-4">
                        <div class="avatar avatar-xl mb-3 mx-auto">
                            @if($client->profile_image)
                                <img src="{{ Storage::url($client->profile_image) }}" alt="Profile" class="rounded-circle">
                            @else
                                <span class="avatar-initial rounded-circle bg-primary">
                                    {{ substr($client->name, 0, 1) }}
                                </span>
                            @endif
                        </div>
                        <h4 class="mb-1">{{ $client->name }}</h4>
                        <span class="badge bg-label-{{ $client->status == 'active' ? 'success' : 'danger' }} mb-3">
                            {{ ucfirst($client->status) }}
                        </span>
                        
                        <div class="d-flex justify-content-center gap-2 mb-3">
                            <form action="{{ route('admin.clients.update-status', $client->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="status" value="{{ $client->status == 'active' ? 'inactive' : 'active' }}">
                                <button type="submit" class="btn btn-sm btn-outline-{{ $client->status == 'active' ? 'danger' : 'success' }}">
                                    <i class="bx {{ $client->status == 'active' ? 'bx-block' : 'bx-check' }} me-1"></i> 
                                    {{ $client->status == 'active' ? 'Deactivate' : 'Activate' }}
                                </button>
                            </form>
                        </div>
                        
                        <div class="divider">
                            <div class="divider-text">Contact Information</div>
                        </div>
                        
                        <div class="info-container">
                            <div class="d-flex justify-content-start align-items-center mb-3">
                                <span class="badge bg-label-primary rounded p-2 me-2">
                                    <i class="bx bx-envelope fs-5"></i>
                                </span>
                                <div class="text-start">
                                    <h6 class="mb-0">Email</h6>
                                    <a href="mailto:{{ $client->email }}">{{ $client->email }}</a>
                                </div>
                            </div>
                            
                            @if($client->phone)
                            <div class="d-flex justify-content-start align-items-center mb-3">
                                <span class="badge bg-label-primary rounded p-2 me-2">
                                    <i class="bx bx-phone fs-5"></i>
                                </span>
                                <div class="text-start">
                                    <h6 class="mb-0">Phone</h6>
                                    <a href="tel:{{ $client->phone }}">{{ $client->phone }}</a>
                                </div>
                            </div>
                            @endif
                            
                            @if($client->address)
                            <div class="d-flex justify-content-start align-items-center mb-3">
                                <span class="badge bg-label-primary rounded p-2 me-2">
                                    <i class="bx bx-map fs-5"></i>
                                </span>
                                <div class="text-start">
                                    <h6 class="mb-0">Address</h6>
                                    <p class="mb-0">{{ $client->address }}</p>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Client Metadata -->
                <div class="card mb-4">
                    <div class="card-header d-flex align-items-center">
                        <i class="bx bx-detail text-primary me-2"></i>
                        <h5 class="card-title mb-0">Client Details</h5>
                    </div>
                    <div class="card-body">
                        @if($client->license_number)
                        <div class="mb-3">
                            <div class="d-flex align-items-center mb-2">
                                <div class="flex-shrink-0">
                                    <span class="badge bg-label-info rounded p-2">
                                        <i class="bx bx-id-card fs-5"></i>
                                    </span>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-0">License Number</h6>
                                    <p class="mb-0 text-muted">{{ $client->license_number }}</p>
                                </div>
                            </div>
                        </div>
                        @endif
                        
                        @if($client->date_of_birth)
                        <div class="mb-3">
                            <div class="d-flex align-items-center mb-2">
                                <div class="flex-shrink-0">
                                    <span class="badge bg-label-info rounded p-2">
                                        <i class="bx bx-calendar fs-5"></i>
                                    </span>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-0">Date of Birth</h6>
                                    <p class="mb-0 text-muted">
                                        {{ \Carbon\Carbon::parse($client->date_of_birth)->format('d M Y') }} 
                                        ({{ \Carbon\Carbon::parse($client->date_of_birth)->age }} years old)
                                    </p>
                                </div>
                            </div>
                        </div>
                        @endif
                        
                        <div class="mb-3">
                            <div class="d-flex align-items-center mb-2">
                                <div class="flex-shrink-0">
                                    <span class="badge bg-label-info rounded p-2">
                                        <i class="bx bx-time fs-5"></i>
                                    </span>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-0">Joined</h6>
                                    <p class="mb-0 text-muted">{{ $client->created_at->format('d M Y') }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <span class="badge bg-label-info rounded p-2">
                                        <i class="bx bx-calendar-check fs-5"></i>
                                    </span>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-0">Last Updated</h6>
                                    <p class="mb-0 text-muted">{{ $client->updated_at->format('d M Y') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Main Content -->
            <div class="col-xl-8 col-lg-7 col-md-12">
                <!-- Recent Bookings -->
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="bx bx-calendar-event text-primary me-2"></i> Recent Bookings</h5>
                        <a href="{{ route('admin.clients.bookings', $client->id) }}" class="btn btn-sm btn-primary">
                            View All Bookings
                        </a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Service</th>
                                    <th>Instructor</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($bookings as $booking)
                                <tr>
                                    <td><strong>#{{ $booking->id }}</strong></td>
                                    <td>{{ $booking->service->name ?? 'N/A' }}</td>
                                    <td>{{ $booking->instructor->user->name ?? 'N/A' }}</td>
                                    <td>
                                        <i class="bx bx-calendar text-primary me-1"></i>
                                        {{ $booking->date ? $booking->date->format('d M Y') : 'N/A' }}
                                    </td>
                                    <td>
                                        <span class="badge bg-label-{{ $booking->status == 'confirmed' ? 'success' : ($booking->status == 'pending' ? 'warning' : 'danger') }} me-1">
                                            {{ ucfirst($booking->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.bookings.show', $booking->id) }}" class="btn btn-sm btn-info">
                                            <i class="bx bx-show-alt"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-3">
                                        <div class="d-flex flex-column align-items-center">
                                            <i class="bx bx-calendar-x text-secondary mb-2" style="font-size: 2rem;"></i>
                                            <p class="mb-0 text-muted">No bookings found for this client</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Actions Card -->
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <i class="bx bx-cog text-primary me-2"></i>
                        <h5 class="card-title mb-0">Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3 mb-md-0">
                                <div class="d-grid">
                                    <a href="{{ route('admin.bookings.create', ['user_id' => $client->id]) }}" class="btn btn-primary">
                                        <i class="bx bx-calendar-plus me-1"></i> New Booking
                                    </a>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3 mb-md-0">
                                <div class="d-grid">
                                    <a href="{{ route('admin.clients.edit', $client->id) }}" class="btn btn-warning">
                                        <i class="bx bx-edit me-1"></i> Edit Client
                                    </a>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <form action="{{ route('admin.clients.destroy', $client->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this client?');">
                                    @csrf
                                    @method('DELETE')
                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-danger">
                                            <i class="bx bx-trash me-1"></i> Delete Client
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection