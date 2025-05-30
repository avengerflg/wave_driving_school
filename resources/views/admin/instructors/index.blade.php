@extends('layouts.admin')

@section('title', 'Manage Instructors')

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Header Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h4 class="fw-bold m-0">
                                <i class="bx bx-user-voice text-primary me-2"></i>
                                <span class="text-muted fw-light">Admin /</span> Manage Instructors
                            </h4>
                            <a href="{{ route('admin.instructors.create') }}" class="btn btn-primary">
                                <i class="bx bx-plus me-1"></i> Add New Instructor
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search and Filter Form -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <form method="GET" action="{{ route('admin.instructors.index') }}">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bx bx-search"></i></span>
                                        <input type="text" name="search" class="form-control" placeholder="Search by name, email or phone" value="{{ $search ?? '' }}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <select name="status" class="form-select">
                                        <option value="">All Status</option>
                                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="bx bx-search-alt me-1"></i> Search
                                    </button>
                                </div>
                                <div class="col-md-2">
                                    <a href="{{ route('admin.instructors.index') }}" class="btn btn-secondary w-100">
                                        <i class="bx bx-reset me-1"></i> Reset
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Instructors Table -->
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">All Instructors</h5>
                <span class="badge bg-primary rounded-pill">{{ $instructors->total() }} Total</span>
            </div>
            <div class="table-responsive text-nowrap">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>License</th>
                            <th>Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse ($instructors as $instructor)
                            <tr>
                                <td><strong>#{{ $instructor->id }}</strong></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-2 bg-primary">
                                            @if($instructor->instructor && $instructor->instructor->profile_image)
                                                <img src="{{ Storage::url($instructor->instructor->profile_image) }}" alt="Avatar" class="rounded-circle">
                                            @else
                                                <span class="avatar-initial rounded-circle">{{ substr($instructor->name, 0, 1) }}</span>
                                            @endif
                                        </div>
                                        <span>{{ $instructor->name }}</span>
                                    </div>
                                </td>
                                <td>{{ $instructor->email }}</td>
                                <td>{{ $instructor->phone ?? 'N/A' }}</td>
                                <td>{{ $instructor->instructor->license_number ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge bg-label-{{ $instructor->status == 'active' ? 'success' : 'danger' }} me-1">
                                        {{ ucfirst($instructor->status) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="{{ route('admin.instructors.show', $instructor->id) }}">
                                                <i class="bx bx-show-alt me-1"></i> View
                                            </a>
                                            <a class="dropdown-item" href="{{ route('admin.instructors.edit', $instructor->id) }}">
                                                <i class="bx bx-edit-alt me-1"></i> Edit
                                            </a>
                                            <a class="dropdown-item" href="{{ route('admin.instructors.schedule', $instructor->id) }}">
                                                <i class="bx bx-calendar me-1"></i> Schedule
                                            </a>
                                            <div class="dropdown-divider"></div>
                                            <form action="{{ route('admin.instructors.update-status', $instructor->id) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="status" value="{{ $instructor->status == 'active' ? 'inactive' : 'active' }}">
                                                <button type="submit" class="dropdown-item">
                                                    <i class="bx {{ $instructor->status == 'active' ? 'bx-block' : 'bx-check' }} me-1"></i> 
                                                    {{ $instructor->status == 'active' ? 'Deactivate' : 'Activate' }}
                                                </button>
                                            </form>
                                            <div class="dropdown-divider"></div>
                                            <form action="{{ route('admin.instructors.destroy', $instructor->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this instructor?');">
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
                                        <h6 class="mb-0 text-secondary">No instructors found</h6>
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
        @if($instructors->lastPage() > 1)
        <div class="card mb-4 mt-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-6 mb-2 mb-lg-0">
                        <div class="pagination-info text-center text-lg-start">
                            Showing {{ $instructors->firstItem() ?? 0 }} to {{ $instructors->lastItem() ?? 0 }} of {{ $instructors->total() }} entries
                        </div>
                    </div>
                    <div class="col-lg-6">
                        {{ $instructors->links() }}
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
