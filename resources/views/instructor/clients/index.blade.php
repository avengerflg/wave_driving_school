@extends('layouts.instructor')

@section('title', 'My Clients')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="fw-bold m-0">
                            <i class="bx bx-user-circle text-primary me-2"></i>
                            My Clients
                        </h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('instructor.clients.index') }}" class="row g-3">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bx bx-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Search clients..." value="{{ $search ?? '' }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Search</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Clients List -->
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Client</th>
                        <th>Contact</th>
                        <th>Total Lessons</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($clients as $client)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-sm me-3">
                                    @if($client->profile_image)
                                        <img src="{{ Storage::url($client->profile_image) }}" alt="Avatar" class="rounded-circle">
                                    @else
                                        <span class="avatar-initial rounded-circle bg-primary">
                                            {{ substr($client->name, 0, 1) }}
                                        </span>
                                    @endif
                                </div>
                                <div>
                                    <h6 class="mb-0">{{ $client->name }}</h6>
                                    <small class="text-muted">Since {{ $client->created_at->format('M Y') }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div>{{ $client->email }}</div>
                            <small class="text-muted">{{ $client->phone ?? 'No phone' }}</small>
                        </td>
                        <td>
                            <span class="badge bg-label-primary">{{ $client->bookings_count }} lessons</span>
                        </td>
                        <td>
                            <span class="badge bg-label-{{ $client->status === 'active' ? 'success' : 'warning' }}">
                                {{ ucfirst($client->status) }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('instructor.clients.show', $client) }}" class="btn btn-sm btn-primary">
                                <i class="bx bx-show-alt me-1"></i> View Details
                            </a>
                            <a href="{{ route('instructor.clients.bookings', $client) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bx bx-calendar me-1"></i> Bookings
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-4">
                            <div class="text-center">
                                <i class="bx bx-user-x text-secondary mb-2" style="font-size: 3rem;"></i>
                                <h6 class="mb-0 text-secondary">No clients found</h6>
                                <p class="text-muted mb-0">You haven't had any lessons with clients yet</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($clients->hasPages())
    <div class="card mt-4">
        <div class="card-body">
            {{ $clients->links() }}
        </div>
    </div>
    @endif
</div>
@endsection