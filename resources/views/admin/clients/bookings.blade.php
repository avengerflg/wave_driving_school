<!-- filepath: resources/views/admin/clients/bookings.blade.php -->

@extends('layouts.admin')

@section('title', 'Client Bookings')

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
                                <i class="bx bx-calendar-check text-primary me-2"></i>
                                <span class="text-muted fw-light">Admin / Clients /</span> {{ $client->name }}'s Bookings
                            </h4>
                            <div>
                                <a href="{{ route('admin.clients.show', $client->id) }}" class="btn btn-secondary me-2">
                                    <i class="bx bx-arrow-back me-1"></i> Back to Client
                                </a>
                                <a href="{{ route('admin.bookings.create', ['user_id' => $client->id]) }}" class="btn btn-primary">
                                    <i class="bx bx-plus me-1"></i> New Booking
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Client Summary Card -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-md me-3">
                                @if($client->profile_image)
                                    <img src="{{ Storage::url($client->profile_image) }}" alt="Profile" class="rounded-circle">
                                @else
                                    <span class="avatar-initial rounded-circle bg-primary">{{ substr($client->name, 0, 1) }}</span>
                                @endif
                            </div>
                            <div>
                                <h5 class="mb-0">{{ $client->name }}</h5>
                                <small class="text-muted">{{ $client->email }} | {{ $client->phone ?? 'No phone' }}</small>
                            </div>
                            <span class="badge bg-label-{{ $client->status == 'active' ? 'success' : 'danger' }} ms-auto">
                                {{ ucfirst($client->status) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bookings Table -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">All Bookings</h5>
                <span class="badge bg-primary rounded-pill">{{ $bookings->total() }} Total</span>
            </div>
            <div class="table-responsive text-nowrap">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Service</th>
                            <th>Instructor</th>
                            <th>Date & Time</th>
                            <th>Suburb</th>
                            <th>Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse ($bookings as $booking)
                            <tr>
                                <td><strong>#{{ $booking->id }}</strong></td>
                                <td>
                                    <span class="badge bg-label-dark">{{ $booking->service->name ?? 'N/A' }}</span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-xs me-2 bg-info">
                                            <span class="avatar-initial rounded-circle">{{ substr($booking->instructor->user->name ?? 'NA', 0, 1) }}</span>
                                        </div>
                                        <span>{{ $booking->instructor->user->name ?? 'N/A' }}</span>
                                    </div>
                                </td>
                                <td>
                                    <i class="bx bx-calendar text-primary me-1"></i>
                                    {{ $booking->date ? $booking->date->format('d M Y') : 'N/A' }}
                                    <br>
                                    <small class="text-muted">
                                        <i class="bx bx-time me-1"></i>
                                        {{ $booking->start_time }} - {{ $booking->end_time }}
                                    </small>
                                </td>
                                <td>{{ $booking->suburb->name ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge bg-label-{{ $booking->status == 'confirmed' ? 'success' : ($booking->status == 'pending' ? 'warning' : 'danger') }} me-1">
                                        {{ ucfirst($booking->status) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('admin.bookings.show', $booking->id) }}" class="btn btn-sm btn-info me-1">
                                        <i class="bx bx-show-alt"></i>
                                    </a>
                                    <a href="{{ route('admin.bookings.edit', $booking->id) }}" class="btn btn-sm btn-warning me-1">
                                        <i class="bx bx-edit-alt"></i>
                                    </a>
                                    <form action="{{ route('admin.bookings.destroy', $booking->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger me-1" onclick="return confirm('Are you sure you want to delete this booking?')">
                                            <i class="bx bx-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="bx bx-calendar-x text-secondary mb-2" style="font-size: 3rem;"></i>
                                        <h6 class="mb-0 text-secondary">No bookings found for this client</h6>
                                        <p class="mb-0 text-muted">Create a new booking by clicking the "New Booking" button</p>
                                        <a href="{{ route('admin.bookings.create', ['user_id' => $client->id]) }}" class="btn btn-primary mt-3">
                                            <i class="bx bx-calendar-plus me-1"></i> Create New Booking
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Pagination -->
        @if($bookings->lastPage() > 1)
        <div class="card mb-4 mt-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-6 mb-2 mb-lg-0">
                        <div class="pagination-info text-center text-lg-start">
                            Showing {{ $bookings->firstItem() ?? 0 }} to {{ $bookings->lastItem() ?? 0 }} of {{ $bookings->total() }} entries
                        </div>
                    </div>
                    <div class="col-lg-6">
                        {{ $bookings->links() }}
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection