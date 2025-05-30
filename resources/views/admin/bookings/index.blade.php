@extends('layouts.admin')

@section('title', 'Manage Bookings')

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h4 class="fw-bold m-0">
                                <i class="bx bx-calendar-check text-primary me-2"></i>
                                <span class="text-muted fw-light">Admin /</span> Manage Bookings
                            </h4>
                            
                            <!-- Button trigger modal -->
                            <button
                                type="button"
                                class="btn btn-primary"
                                data-bs-toggle="modal"
                                data-bs-target="#createBookingModal"
                            >
                                <i class="bx bx-plus me-1"></i> Create Booking
                            </button>
                            
                            <!-- Create Booking Modal -->
                            <div class="modal fade" id="createBookingModal" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                                    <div class="modal-content">
                                        <form method="POST" action="{{ route('admin.bookings.store') }}">
                                            @csrf
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="createBookingModalTitle">Create New Booking</h5>
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
                                                            <h5 class="card-header">User & Instructor</h5>
                                                            <div class="card-body">
                                                                <div class="mb-3">
                                                                    <label for="user_id" class="form-label">User</label>
                                                                    <select id="user_id" name="user_id" class="form-select" required>
                                                                        <option value="">Select User</option>
                                                                        @foreach ($users as $user)
                                                                            @if($user->role == 'student')
                                                                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                                                                            @endif
                                                                        @endforeach
                                                                    </select>
                                                                    <div class="form-text">
                                                                        Select the user who is booking.
                                                                    </div>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="instructor_id" class="form-label">Instructor</label>
                                                                    <select id="instructor_id" name="instructor_id" class="form-select" required>
                                                                        <option value="">Select Instructor</option>
                                                                        @foreach ($instructors as $instructor)
                                                                            <option value="{{ $instructor->id }}">{{ $instructor->user->name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="card mb-4">
                                                            <h5 class="card-header">Service & Location</h5>
                                                            <div class="card-body">
                                                                <div class="mb-3">
                                                                    <label for="service_id" class="form-label">Service</label>
                                                                    <select id="service_id" name="service_id" class="form-select" required>
                                                                        <option value="">Select Service</option>
                                                                        @foreach ($services as $service)
                                                                            <option value="{{ $service->id }}">{{ $service->name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="suburb_id" class="form-label">Suburb</label>
                                                                    <select id="suburb_id" name="suburb_id" class="form-select" required>
                                                                        <option value="">Select Suburb</option>
                                                                        @foreach ($suburbs as $suburb)
                                                                            <option value="{{ $suburb->id }}">{{ $suburb->name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="card mb-4">
                                                            <h5 class="card-header">Date & Time</h5>
                                                            <div class="card-body">
                                                                <div class="mb-3">
                                                                    <label for="date" class="form-label">Date</label>
                                                                    <input
                                                                        type="date"
                                                                        id="date"
                                                                        name="date"
                                                                        class="form-control"
                                                                        required
                                                                    />
                                                                </div>
                                                                <div class="row g-2">
                                                                    <div class="col mb-0">
                                                                        <label for="start_time" class="form-label">Start Time</label>
                                                                        <input
                                                                            type="time"
                                                                            id="start_time"
                                                                            name="start_time"
                                                                            class="form-control"
                                                                            required
                                                                        />
                                                                    </div>
                                                                    <div class="col mb-0">
                                                                        <label for="end_time" class="form-label">End Time</label>
                                                                        <input
                                                                            type="time"
                                                                            id="end_time"
                                                                            name="end_time"
                                                                            class="form-control"
                                                                            required
                                                                        />
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="card mb-4">
                                                            <h5 class="card-header">Additional Details</h5>
                                                            <div class="card-body">
                                                                <div class="mb-3">
                                                                    <label for="status" class="form-label">Status</label>
                                                                    <select id="status" name="status" class="form-select" required>
                                                                        <option value="pending">Pending</option>
                                                                        <option value="confirmed">Confirmed</option>
                                                                        <option value="completed">Completed</option>
                                                                        <option value="cancelled">Cancelled</option>
                                                                    </select>
                                                                </div>
                                                                <div>
                                                                    <label for="notes" class="form-label">Notes</label>
                                                                    <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
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
                                                <button type="submit" class="btn btn-primary">Create Booking</button>
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

        <!-- Search and Filter Form -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form method="GET" action="{{ route('admin.bookings.index') }}">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bx bx-search"></i></span>
                                        <input type="text" name="search" class="form-control" placeholder="Search by User, Instructor, or Service" value="{{ $search ?? '' }}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bx bx-filter"></i></span>
                                        <select name="status" class="form-select">
                                            <option value="">All Statuses</option>
                                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="bx bx-search-alt me-1"></i> Search
                                    </button>
                                </div>
                                <div class="col-md-2">
                                    <a href="{{ route('admin.bookings.index') }}" class="btn btn-secondary w-100">
                                        <i class="bx bx-reset me-1"></i> Reset
                                    </a>
                                </div>
                            </div>
                        </form>
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
                            <th>#</th>
                            <th>User</th>
                            <th>Instructor</th>
                            <th>Service</th>
                            <th>Suburb</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse ($bookings as $booking)
                            <tr>
                                <td><strong>#{{ $booking->id }}</strong></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-2 bg-primary">
                                            <span class="avatar-initial rounded-circle">{{ substr($booking->user->name ?? 'NA', 0, 1) }}</span>
                                        </div>
                                        <span>{{ $booking->user->name ?? 'N/A' }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-2 bg-info">
                                            <span class="avatar-initial rounded-circle">{{ substr($booking->instructor->user->name ?? 'NA', 0, 1) }}</span>
                                        </div>
                                        <span>{{ $booking->instructor->user->name ?? 'N/A' }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-label-dark">{{ $booking->service->name ?? 'N/A' }}</span>
                                </td>
                                <td>{{ $booking->suburb->name ?? 'N/A' }}</td>
                                <td>
                                    <i class="bx bx-calendar text-primary me-1"></i>
                                    {{ $booking->date ? $booking->date->format('d M Y') : 'N/A' }}
                                </td>
                                <td>
                                    <span class="badge bg-label-{{ $booking->status == 'confirmed' ? 'success' : ($booking->status == 'pending' ? 'warning' : ($booking->status == 'completed' ? 'info' : 'danger')) }} me-1">
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
                                    <div class="btn-group dropup d-inline-block">
                                        <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                            Status
                                        </button>
                                        <ul class="dropdown-menu">
                                            <form action="{{ route('admin.bookings.update-status', $booking->id) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="status" value="pending">
                                                <li><button type="submit" class="dropdown-item {{ $booking->status == 'pending' ? 'active' : '' }}">Pending</button></li>
                                            </form>
                                            <form action="{{ route('admin.bookings.update-status', $booking->id) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="status" value="confirmed">
                                                <li><button type="submit" class="dropdown-item {{ $booking->status == 'confirmed' ? 'active' : '' }}">Confirmed</button></li>
                                            </form>
                                            <form action="{{ route('admin.bookings.update-status', $booking->id) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="status" value="completed">
                                                <li><button type="submit" class="dropdown-item {{ $booking->status == 'completed' ? 'active' : '' }}">Completed</button></li>
                                            </form>
                                            <form action="{{ route('admin.bookings.update-status', $booking->id) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="status" value="cancelled">
                                                <li><button type="submit" class="dropdown-item {{ $booking->status == 'cancelled' ? 'active' : '' }}">Cancelled</button></li>
                                            </form>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="bx bx-calendar-x text-secondary mb-2" style="font-size: 3rem;"></i>
                                        <h6 class="mb-0 text-secondary">No bookings found</h6>
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
        @if($bookings->lastPage() > 1)
        <div class="card mb-4 mt-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-6 mb-2 mb-lg-0">
                        <div class="pagination-info text-center text-lg-start">
                            Showing {{ $bookings->firstItem() ?? 0 }} to {{ $bookings->lastItem() ?? 0                            }} of {{ $bookings->total() }} entries
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <nav aria-label="Page navigation" class="d-flex justify-content-center justify-content-lg-end">
                            <ul class="pagination mb-0">
                                <li class="page-item {{ $bookings->onFirstPage() ? 'disabled' : '' }}">
                                    <a class="page-link" href="{{ $bookings->url(1) }}" aria-label="First">
                                        <i class="tf-icon bx bx-chevrons-left"></i>
                                    </a>
                                </li>
                                <li class="page-item {{ $bookings->previousPageUrl() ? '' : 'disabled' }}">
                                    <a class="page-link" href="{{ $bookings->previousPageUrl() }}" aria-label="Previous">
                                        <i class="tf-icon bx bx-chevron-left"></i>
                                    </a>
                                </li>
                                
                                @php
                                    $currentPage = $bookings->currentPage();
                                    $lastPage = $bookings->lastPage();
                                    $start = max($currentPage - 2, 1);
                                    $end = min($start + 4, $lastPage);
                                @endphp
                                
                                @if($start > 1)
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $bookings->url(1) }}">1</a>
                                    </li>
                                    @if($start > 2)
                                        <li class="page-item disabled">
                                            <a class="page-link" href="javascript:void(0);">...</a>
                                        </li>
                                    @endif
                                @endif
                                
                                @for ($i = $start; $i <= $end; $i++)
                                    <li class="page-item {{ $currentPage == $i ? 'active' : '' }}">
                                        <a class="page-link" href="{{ $bookings->url($i) }}">{{ $i }}</a>
                                    </li>
                                @endfor
                                
                                @if($end < $lastPage)
                                    @if($end < $lastPage - 1)
                                        <li class="page-item disabled">
                                            <a class="page-link" href="javascript:void(0);">...</a>
                                        </li>
                                    @endif
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $bookings->url($lastPage) }}">{{ $lastPage }}</a>
                                    </li>
                                @endif
                                
                                <li class="page-item {{ $bookings->nextPageUrl() ? '' : 'disabled' }}">
                                    <a class="page-link" href="{{ $bookings->nextPageUrl() }}" aria-label="Next">
                                        <i class="tf-icon bx bx-chevron-right"></i>
                                    </a>
                                </li>
                                <li class="page-item {{ $currentPage == $lastPage ? 'disabled' : '' }}">
                                    <a class="page-link" href="{{ $bookings->url($lastPage) }}" aria-label="Last">
                                        <i class="tf-icon bx bx-chevrons-right"></i>
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

