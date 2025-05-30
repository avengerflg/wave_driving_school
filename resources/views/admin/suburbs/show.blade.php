
@extends('layouts.admin')

@section('title', 'Suburb Details')

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">Admin / Suburbs /</span> View
        </h4>

        <div class="row">
            <div class="col-md-7">
                <!-- Suburb Information -->
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Suburb Information</h5>
                        <div>
                            <a href="{{ route('admin.suburbs.edit', $suburb->id) }}" class="btn btn-primary btn-sm">
                                <i class="bx bx-edit-alt me-1"></i> Edit
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <h4 class="mb-1">{{ $suburb->name }}</h4>
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-label-{{ $suburb->active ? 'success' : 'danger' }} me-2">
                                        {{ $suburb->active ? 'Active' : 'Inactive' }}
                                    </span>
                                    <span class="text-muted">Created {{ $suburb->created_at->format('M d, Y') }}</span>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <dl class="row mb-0">
                            <dt class="col-sm-3">ID</dt>
                            <dd class="col-sm-9">{{ $suburb->id }}</dd>
                            
                            <dt class="col-sm-3">State</dt>
                            <dd class="col-sm-9">{{ $suburb->state }}</dd>
                            
                            <dt class="col-sm-3">Postcode</dt>
                            <dd class="col-sm-9">{{ $suburb->postcode }}</dd>
                            
                            <dt class="col-sm-3">Last Updated</dt>
                            <dd class="col-sm-9">{{ $suburb->updated_at->format('M d, Y h:i A') }}</dd>
                        </dl>
                    </div>
                </div>
                
                <!-- Related Instructors -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Associated Instructors</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($suburb->instructors->take(5) as $instructor)
                                    <tr>
                                        <td>{{ $instructor->user->name }}</td>
                                        <td>{{ $instructor->user->email }}</td>
                                        <td>
                                            <span class="badge bg-label-{{ $instructor->user->status == 'active' ? 'success' : 'danger' }}">
                                                {{ ucfirst($instructor->user->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.instructors.show', $instructor->user->id) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="bx bx-show"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-3">No instructors associated with this suburb</td>
                                    </tr>
                                @endforelse
                                @if($suburb->instructors->count() > 5)
                                    <tr>
                                        <td colspan="4" class="text-center py-2">
                                            <a href="#" class="text-primary">View all {{ $suburb->instructors->count() }} instructors</a>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Recent Bookings -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Recent Bookings</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Student</th>
                                    <th>Instructor</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($suburb->bookings->take(5) as $booking)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($booking->date)->format('M d, Y') }}</td>
                                        <td>{{ $booking->user->name }}</td>
                                        <td>{{ $booking->instructor->user->name }}</td>
                                        <td>
                                            <span class="badge bg-label-{{ $booking->status == 'completed' ? 'success' : ($booking->status == 'cancelled' ? 'danger' : 'warning') }}">
                                                {{ ucfirst($booking->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-3">No bookings associated with this suburb</td>
                                    </tr>
                                @endforelse
                                @if($suburb->bookings->count() > 5)
                                    <tr>
                                        <td colspan="4" class="text-center py-2">
                                            <a href="#" class="text-primary">View all {{ $suburb->bookings->count() }} bookings</a>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <div class="col-md-5">
                <!-- Statistics -->
                <div class="row">
                    <div class="col-6 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div class="card-info">
                                        <p class="card-text mb-1">Instructors</p>
                                        <div class="d-flex align-items-end">
                                            <h4 class="card-title mb-0 me-2">{{ $instructorCount }}</h4>
                                        </div>
                                    </div>
                                    <div class="card-icon">
                                        <span class="badge bg-label-primary rounded p-2">
                                            <i class="bx bx-user-check bx-sm"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div class="card-info">
                                        <p class="card-text mb-1">Bookings</p>
                                        <div class="d-flex align-items-end">
                                            <h4 class="card-title mb-0 me-2">{{ $bookingCount }}</h4>
                                        </div>
                                    </div>
                                    <div class="card-icon">
                                        <span class="badge bg-label-info rounded p-2">
                                            <i class="bx bx-calendar bx-sm"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Map View -->
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between">
                        <h5 class="mb-0">Location</h5>
                    </div>
                    <div class="card-body p-0">
                        <div id="suburb-map" style="height: 300px;">
                            <!-- Map will be loaded here -->
                            <div class="d-flex justify-content-center align-items-center h-100 bg-light">
                                <div class="text-center p-4">
                                    <i class="bx bx-map text-primary" style="font-size: 3.5rem;"></i>
                                    <h6 class="mt-2">{{ $suburb->name }}, {{ $suburb->state }} {{ $suburb->postcode }}</h6>
                                    <p class="text-muted mb-0">Map view would be displayed here</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Actions Card -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('admin.suburbs.edit', $suburb->id) }}" class="btn btn-primary">
                                <i class="bx bx-edit me-1"></i> Edit Suburb
                            </a>
                            <form action="{{ route('admin.suburbs.toggle-status', $suburb->id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-outline-{{ $suburb->active ? 'warning' : 'success' }} w-100">
                                    <i class="bx {{ $suburb->active ? 'bx-hide' : 'bx-show' }} me-1"></i>
                                    {{ $suburb->active ? 'Deactivate Suburb' : 'Activate Suburb' }}
                                </button>
                            </form>
                            <button onclick="confirmDelete()" class="btn btn-outline-danger">
                                <i class="bx bx-trash me-1"></i> Delete Suburb
                            </button>
                            <form id="delete-form" action="{{ route('admin.suburbs.destroy', $suburb->id) }}" method="POST" class="d-none">
                                @csrf
                                @method('DELETE')
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- Suburb Info Card -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">About This Suburb</h5>
                    </div>
                    <div class="card-body">
                        <dl class="row mb-0">
                            <dt class="col-sm-4">Created</dt>
                            <dd class="col-sm-8">{{ $suburb->created_at->format('M d, Y') }}</dd>
                            
                            <dt class="col-sm-4">Last Updated</dt>
                            <dd class="col-sm-8">{{ $suburb->updated_at->format('M d, Y') }}</dd>
                            
                            <dt class="col-sm-4">Status</dt>
                            <dd class="col-sm-8">
                                <span class="badge bg-label-{{ $suburb->active ? 'success' : 'danger' }}">
                                    {{ $suburb->active ? 'Active' : 'Inactive' }}
                                </span>
                            </dd>
                        </dl>
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
        if (confirm('Are you sure you want to delete this suburb? This action cannot be undone.')) {
            document.getElementById('delete-form').submit();
        }
    }
</script>
@endsection