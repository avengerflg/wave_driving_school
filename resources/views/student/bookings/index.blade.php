@extends('layouts.student')

@section('title', 'My Lessons')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="fw-bold mb-0">
                            <i class="bx bx-calendar-check text-primary me-2"></i>
                            My Lessons
                        </h4>
                        <a href="{{ route('booking.index') }}" class="btn btn-primary">
                            <i class="bx bx-plus me-1"></i> Book New Lesson
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Filters -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('student.bookings.index') }}">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bx bx-search"></i></span>
                                    <input type="text" name="search" class="form-control" placeholder="Search lessons..." value="{{ request('search') }}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <select name="status" class="form-select">
                                    <option value="all">All Status</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    <option value="rescheduled" {{ request('status') == 'rescheduled' ? 'selected' : '' }}>Rescheduled</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <input type="date" name="date_from" class="form-control" placeholder="From Date" value="{{ request('date_from') }}">
                            </div>
                            <div class="col-md-2">
                                <input type="date" name="date_to" class="form-control" placeholder="To Date" value="{{ request('date_to') }}">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bx bx-filter-alt me-1"></i> Filter
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Lessons Table -->
    <div class="card">
        <h5 class="card-header">Lesson List</h5>
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Instructor</th>
                        <th>Service</th>
                        <th>Date & Time</th>
                        <th>Location</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse($bookings as $booking)
                    <tr>
                        <td>
                            @if($booking->instructor)
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm me-2">
                                        @if($booking->instructor->avatar)
                                            <img src="{{ $booking->instructor->avatar }}" alt="{{ $booking->instructor->name }}" class="rounded-circle">
                                        @else
                                            <span class="avatar-initial rounded-circle bg-primary">
                                                {{ $booking->instructor->user->name[0] ?? 'I' }}
                                            </span>
                                        @endif
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ $booking->instructor->user->name }}</h6>
                                        <small class="text-muted">{{ $booking->instructor->user->email }}</small>
                                    </div>
                                </div>
                            @else
                                <span class="text-muted">Not assigned</span>
                            @endif
                        </td>
                        <td>
                            @if($booking->service)
                                <div>
                                    <span class="fw-medium">{{ $booking->service->name }}</span>
                                    @if($booking->service->price)
                                        <br><small class="text-success fw-medium">${{ number_format($booking->service->price, 2) }}</small>
                                    @endif
                                </div>
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex flex-column">
                                <span><i class="bx bx-calendar text-primary me-1"></i>{{ \Carbon\Carbon::parse($booking->date)->format('d M Y') }}</span>
                                <small class="text-muted">
                                    <i class="bx bx-time-five me-1"></i>
                                    @if($booking->time)
                                        {{ \Carbon\Carbon::parse($booking->time)->format('H:i') }}
                                        @if($booking->duration)
                                            - {{ \Carbon\Carbon::parse($booking->time)->addHours($booking->duration)->format('H:i') }}
                                        @endif
                                    @elseif($booking->start_time && $booking->end_time)
                                        {{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}
                                    @else
                                        Time not set
                                    @endif
                                </small>
                            </div>
                        </td>
                        <td>
                            @php
                                // Handle different location data formats
                                $location = 'Not specified';
                                
                                if ($booking->suburb) {
                                    // If suburb is a relationship
                                    if (is_object($booking->suburb) && isset($booking->suburb->name)) {
                                        $location = $booking->suburb->name;
                                    }
                                    // If suburb is JSON string
                                    elseif (is_string($booking->suburb)) {
                                        $suburbData = json_decode($booking->suburb, true);
                                        if (json_last_error() === JSON_ERROR_NONE && isset($suburbData['name'])) {
                                            $location = $suburbData['name'];
                                        } else {
                                            $location = $booking->suburb;
                                        }
                                    }
                                    // If suburb is an array
                                    elseif (is_array($booking->suburb) && isset($booking->suburb['name'])) {
                                        $location = $booking->suburb['name'];
                                    }
                                }
                                // Fallback to location field
                                elseif ($booking->location) {
                                    $location = $booking->location;
                                }
                            @endphp
                            {{ $location }}
                        </td>
                        <td>
                            @php
                                $statusColor = [
                                    'pending' => 'warning',
                                    'confirmed' => 'success',
                                    'completed' => 'info',
                                    'cancelled' => 'danger',
                                    'rescheduled' => 'secondary'
                                ][$booking->status] ?? 'secondary';
                            @endphp
                            <span class="badge bg-label-{{ $statusColor }} me-1">{{ ucfirst($booking->status) }}</span>
                        </td>
                        <td>
                            <div class="dropdown">
                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                    <i class="bx bx-dots-vertical-rounded"></i>
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="{{ route('student.bookings.show', $booking) }}">
                                        <i class="bx bx-show-alt me-1"></i> View Details
                                    </a>
                                    
                                    @if($booking->instructor && $booking->instructor->phone && in_array($booking->status, ['confirmed', 'pending']))
                                        <a class="dropdown-item" href="tel:{{ $booking->instructor->phone }}">
                                            <i class="bx bx-phone me-1 text-primary"></i> Call Instructor
                                        </a>
                                    @endif
                                    
                                    @if(in_array($booking->status, ['pending', 'confirmed']) && \Carbon\Carbon::parse($booking->date)->gt(now()))
                                        <button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#cancelModal{{ $booking->id }}">
                                            <i class="bx bx-x me-1 text-danger"></i> Cancel Lesson
                                        </button>
                                    @endif
                                    
                                    @if($booking->status === 'completed' && !$booking->rating)
                                        <button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#reviewModal{{ $booking->id }}">
                                            <i class="bx bx-star me-1 text-warning"></i> Leave Review
                                        </button>
                                    @endif
                                    
                                    @if($booking->status === 'completed' && $booking->rating)
                                        <a class="dropdown-item" href="{{ route('student.bookings.show', $booking) }}#review">
                                            <i class="bx bx-show me-1 text-info"></i> View Review
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </td>
                    </tr>

                    <!-- Cancel Modal for each booking -->
                    <div class="modal fade" id="cancelModal{{ $booking->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Cancel Lesson</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <p>Are you sure you want to cancel this lesson?</p>
                                    <div class="alert alert-warning">
                                        <small><strong>Note:</strong> Lessons can only be cancelled at least 24 hours in advance. Cancellation policies may apply.</small>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Keep Lesson</button>
                                    <form action="{{ route('student.bookings.cancel', $booking) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="btn btn-danger">Yes, Cancel Lesson</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Review Modal for each completed booking -->
                    @if($booking->status === 'completed' && !$booking->rating)
                    <div class="modal fade" id="reviewModal{{ $booking->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Leave a Review</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form action="{{ route('student.bookings.review', $booking) }}" method="POST">
                                    @csrf
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label">Rating *</label>
                                            <div class="rating-input d-flex gap-1" data-booking="{{ $booking->id }}">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <input type="radio" name="rating" value="{{ $i }}" id="star{{ $booking->id }}_{{ $i }}" required style="display: none;">
                                                    <label for="star{{ $booking->id }}_{{ $i }}" class="star-label" style="font-size: 1.5rem; color: #ddd; cursor: pointer;">
                                                        ★
                                                    </label>
                                                @endfor
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="review{{ $booking->id }}" class="form-label">Review (Optional)</label>
                                            <textarea class="form-control" id="review{{ $booking->id }}" name="review" rows="4" 
                                                      placeholder="Share your experience with this lesson..."></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-primary">Submit Review</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endif

                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <div class="d-flex flex-column align-items-center">
                                <img src="{{ asset('assets/img/illustrations/empty.png') }}" alt="No Lessons" class="mb-3" style="height: 140px;">
                                <h5 class="fw-semibold mb-1">No Lessons Found</h5>
                                <p class="text-muted mb-3">You haven't booked any lessons yet or no lessons match your current filters.</p>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('booking.index') }}" class="btn btn-sm btn-primary">
                                        <i class="bx bx-plus me-1"></i> Book Your First Lesson
                                    </a>
                                    @if(request()->hasAny(['search', 'status', 'date_from', 'date_to']))
                                        <a href="{{ route('student.bookings.index') }}" class="btn btn-sm btn-outline-secondary">
                                            <i class="bx bx-reset me-1"></i> Clear Filters
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($bookings->hasPages())
        <div class="card-footer d-flex justify-content-center pt-4">
            {{ $bookings->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

@section('page-scripts')
<style>
    .badge.bg-label-info {
        background-color: rgba(3, 195, 236, 0.16) !important;
        color: #03c3ec !important;
    }
    
    .badge.bg-label-warning {
        background-color: rgba(255, 171, 0, 0.16) !important;
        color: #ffab00 !important;
    }
    
    .badge.bg-label-success {
        background-color: rgba(113, 221, 55, 0.16) !important;
        color: #71dd37 !important;
    }
    
    .badge.bg-label-danger {
        background-color: rgba(255, 62, 29, 0.16) !important;
        color: #ff3e1d !important;
    }
    
    .badge.bg-label-secondary {
        background-color: rgba(133, 146, 163, 0.16) !important;
        color: #8592a3 !important;
    }
    
    .table-responsive {
        min-height: 300px;
    }
    
    .dropdown-item:active {
        color: inherit;
        background-color: rgba(67, 89, 113, 0.04);
    }
    
    .avatar-initial {
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.8rem;
        font-weight: 600;
    }
    
    .star-label:hover,
    .star-label.active {
        color: #ffc107 !important;
    }
    
    .text-success {
        color: #71dd37 !important;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle star rating interactions for all review modals
    document.querySelectorAll('.rating-input').forEach(function(ratingContainer) {
        const stars = ratingContainer.querySelectorAll('.star-label');
        const inputs = ratingContainer.querySelectorAll('input[type="radio"]');
        
        stars.forEach(function(star, index) {
            star.addEventListener('mouseenter', function() {
                highlightStars(stars, index);
            });
            
            star.addEventListener('click', function() {
                inputs[index].checked = true;
                highlightStars(stars, index);
            });
        });
        
        ratingContainer.addEventListener('mouseleave', function() {
            const checkedIndex = Array.from(inputs).findIndex(input => input.checked);
            if (checkedIndex >= 0) {
                highlightStars(stars, checkedIndex);
            } else {
                resetStars(stars);
            }
        });
    });
    
    function highlightStars(stars, index) {
        stars.forEach(function(star, i) {
            if (i <= index) {
                star.style.color = '#ffc107';
            } else {
                star.style.color = '#ddd';
            }
        });
    }
    
    function resetStars(stars) {
        stars.forEach(function(star) {
            star.style.color = '#ddd';
        });
    }
});
</script>
@endsection
