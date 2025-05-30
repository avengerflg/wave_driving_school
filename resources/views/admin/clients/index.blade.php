<!-- filepath: resources/views/admin/clients/index.blade.php -->

@extends('layouts.admin')

@section('title', 'Manage Clients')

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h4 class="fw-bold m-0">
                                <i class="bx bx-user-circle text-primary me-2"></i>
                                <span class="text-muted fw-light">Admin /</span> Manage Clients
                            </h4>
                            
                            <!-- Button trigger modal -->
                            <button
                                type="button"
                                class="btn btn-primary"
                                data-bs-toggle="modal"
                                data-bs-target="#createClientModal"
                            >
                                <i class="bx bx-plus me-1"></i> Add New Client
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search and Filter Form -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form method="GET" action="{{ route('admin.clients.index') }}">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bx bx-search"></i></span>
                                        <input type="text" name="search" class="form-control" placeholder="Search by name, email or phone" value="{{ $search ?? '' }}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bx bx-filter"></i></span>
                                        <select name="status" class="form-select">
                                            <option value="">All Status</option>
                                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="bx bx-search-alt me-1"></i> Search
                                    </button>
                                </div>
                                <div class="col-md-2">
                                    <a href="{{ route('admin.clients.index') }}" class="btn btn-secondary w-100">
                                        <i class="bx bx-reset me-1"></i> Reset
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Clients Table -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">All Clients</h5>
                <span class="badge bg-primary rounded-pill">{{ $clients->total() }} Total</span>
            </div>
            <div class="table-responsive text-nowrap">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Status</th>
                            <th>Registered</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse ($clients as $client)
                            <tr>
                                <td><strong>#{{ $client->id }}</strong></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-2 bg-primary">
                                            @if($client->profile_image)
                                                <img src="{{ Storage::url($client->profile_image) }}" alt="Avatar" class="rounded-circle">
                                            @else
                                                <span class="avatar-initial rounded-circle">{{ substr($client->name, 0, 1) }}</span>
                                            @endif
                                        </div>
                                        <span>{{ $client->name }}</span>
                                    </div>
                                </td>
                                <td>{{ $client->email }}</td>
                                <td>{{ $client->phone ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge bg-label-{{ $client->status == 'active' ? 'success' : 'danger' }} me-1">
                                        {{ ucfirst($client->status) }}
                                    </span>
                                </td>
                                <td>{{ $client->created_at->format('d M Y') }}</td>
                                <td class="text-center">
                                    <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="{{ route('admin.clients.show', $client->id) }}">
                                                <i class="bx bx-show-alt me-1"></i> View
                                            </a>
                                            <a class="dropdown-item" href="{{ route('admin.clients.edit', $client->id) }}">
                                                <i class="bx bx-edit-alt me-1"></i> Edit
                                            </a>
                                            <a class="dropdown-item" href="{{ route('admin.clients.bookings', $client->id) }}">
                                                <i class="bx bx-calendar me-1"></i> Bookings
                                            </a>
                                            <div class="dropdown-divider"></div>
                                            <form action="{{ route('admin.clients.update-status', $client->id) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="status" value="{{ $client->status == 'active' ? 'inactive' : 'active' }}">
                                                <button type="submit" class="dropdown-item">
                                                    <i class="bx {{ $client->status == 'active' ? 'bx-block' : 'bx-check' }} me-1"></i> 
                                                    {{ $client->status == 'active' ? 'Deactivate' : 'Activate' }}
                                                </button>
                                            </form>
                                            <div class="dropdown-divider"></div>
                                            <form action="{{ route('admin.clients.destroy', $client->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this client?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger">
                                                    <i class="bx bx-trash me-1"></i> Delete
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="bx bx-user-x text-secondary mb-2" style="font-size: 3rem;"></i>
                                        <h6 class="mb-0 text-secondary">No clients found</h6>
                                        <p class="mb-0 text-muted">Try adjusting your search or filter criteria</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Pagination -->
        @if($clients->lastPage() > 1)
        <div class="card mb-4 mt-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-6 mb-2 mb-lg-0">
                        <div class="pagination-info text-center text-lg-start">
                            Showing {{ $clients->firstItem() ?? 0 }} to {{ $clients->lastItem() ?? 0 }} of {{ $clients->total() }} entries
                        </div>
                    </div>
                    <div class="col-lg-6">
                        {{ $clients->links() }}
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Create Client Modal -->
        <div class="modal fade" id="createClientModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <form method="POST" action="{{ route('admin.clients.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">Add New Client</h5>
                            <button
                                type="button"
                                class="btn-close"
                                data-bs-dismiss="modal"
                                aria-label="Close"
                            ></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card mb-4">
                                        <h5 class="card-header">Personal Information</h5>
                                        <div class="card-body">
                                            <div class="mb-3">
                                                <label for="name" class="form-label">Full Name</label>
                                                <input
                                                    type="text"
                                                    id="name"
                                                    name="name"
                                                    class="form-control"
                                                    placeholder="John Doe"
                                                    required
                                                />
                                            </div>
                                            <div class="mb-3">
                                                <label for="email" class="form-label">Email</label>
                                                <input
                                                    type="email"
                                                    id="email"
                                                    name="email"
                                                    class="form-control"
                                                    placeholder="john.doe@example.com"
                                                    required
                                                />
                                            </div>
                                            <div class="mb-3">
                                                <label for="phone" class="form-label">Phone Number</label>
                                                <input
                                                    type="text"
                                                    id="phone"
                                                    name="phone"
                                                    class="form-control"
                                                    placeholder="+1 (123) 456-7890"
                                                />
                                            </div>
                                            <div class="mb-3">
                                                <label for="date_of_birth" class="form-label">Date of Birth</label>
                                                <input
                                                    type="date"
                                                    id="date_of_birth"
                                                    name="date_of_birth"
                                                    class="form-control"
                                                />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="card mb-4">
                                        <h5 class="card-header">Account Details</h5>
                                        <div class="card-body">
                                            <div class="mb-3">
                                                <label for="password" class="form-label">Password</label>
                                                <input
                                                    type="password"
                                                    id="password"
                                                    name="password"
                                                    class="form-control"
                                                    required
                                                />
                                            </div>
                                            <div class="mb-3">
                                                <label for="password_confirmation" class="form-label">Confirm Password</label>
                                                <input
                                                    type="password"
                                                    id="password_confirmation"
                                                    name="password_confirmation"
                                                    class="form-control"
                                                    required
                                                />
                                            </div>
                                            <div class="mb-3">
                                                <label for="address" class="form-label">Address</label>
                                                <textarea
                                                    id="address"
                                                    name="address"
                                                    class="form-control"
                                                    rows="2"
                                                ></textarea>
                                            </div>
                                            <div class="mb-3">
                                                <label for="license_number" class="form-label">License Number</label>
                                                <input
                                                    type="text"
                                                    id="license_number"
                                                    name="license_number"
                                                    class="form-control"
                                                />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-12">
                                    <div class="card mb-4">
                                        <h5 class="card-header">Additional Information</h5>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="profile_image" class="form-label">Profile Image</label>
                                                        <input 
                                                            type="file" 
                                                            id="profile_image" 
                                                            name="profile_image" 
                                                            class="form-control" 
                                                            accept="image/*" 
                                                        />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="status" class="form-label">Status</label>
                                                        <select id="status" name="status" class="form-select" required>
                                                            <option value="active" selected>Active</option>
                                                            <option value="inactive">Inactive</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                Cancel
                            </button>
                            <button type="submit" class="btn btn-primary">Create Client</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection