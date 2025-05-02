@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <!-- Booking Steps -->
            <div class="booking-steps">
                <div class="step-indicator">
                    <div class="step completed">
                        <div class="step-number">1</div>
                        <div class="step-title">Suburb</div>
                    </div>
                    <div class="step completed">
                        <div class="step-number">2</div>
                        <div class="step-title">Instructor</div>
                    </div>
                    <div class="step active">
                        <div class="step-number">3</div>
                        <div class="step-title">Date & Time</div>
                    </div>
                    <div class="step">
                        <div class="step-number">4</div>
                        <div class="step-title">Service</div>
                    </div>
                    <div class="step">
                        <div class="step-number">5</div>
                        <div class="step-title">Details</div>
                    </div>
                    <div class="step">
                        <div class="step-number">6</div>
                        <div class="step-title">Payment</div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Available Times with {{ $instructor->user->name }}</h5>
                            <div>
                                <a href="{{ route('booking.availability', [
                                    'instructor' => $instructor->id,
                                    'week_start' => $startDate->format('Y-m-d'),
                                    'direction' => 'prev'
                                ]) }}" class="btn btn-sm btn-light">← Previous Week</a>
                                
                                <a href="{{ route('booking.availability', [
                                    'instructor' => $instructor->id,
                                    'week_start' => $startDate->format('Y-m-d'),
                                    'direction' => 'next'
                                ]) }}" class="btn btn-sm btn-light">Next Week →</a>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Time</th>
                                        @for($date = clone $startDate; $date <= $endDate; $date->addDay())
                                            <th class="text-center {{ $date->isToday() ? 'table-primary' : '' }}">
                                                {{ $date->format('D') }}<br>
                                                {{ $date->format('M d') }}
                                            </th>
                                        @endfor
                                    </tr>
                                </thead>
                                <tbody>
                                    @for($hour = 9; $hour < 17; $hour++)
                                        <tr>
                                            <td class="align-middle">
                                                {{ sprintf('%02d:00', $hour) }} - 
                                                {{ sprintf('%02d:00', $hour + 1) }}
                                            </td>
                                            
                                            @for($date = clone $startDate; $date <= $endDate; $date->addDay())
                                                <td class="text-center">
                                                    @php
                                                        $dateStr = $date->format('Y-m-d');
                                                        $timeSlots = isset($availabilitySlots[$dateStr]) ? $availabilitySlots[$dateStr] : collect();
                                                        $slot = $timeSlots->first(function($slot) use ($hour) {
                                                            return Carbon\Carbon::parse($slot->start_time)->hour === $hour;
                                                        });
                                                        
                                                        $isBooked = isset($existingBookings[$dateStr]) &&
                                                            $existingBookings[$dateStr]->contains(function($booking) use ($hour) {
                                                                return Carbon\Carbon::parse($booking->start_time)->hour === $hour;
                                                            });
                                                    @endphp

                                                    @if($slot && !$isBooked)
                                                        <form action="{{ route('booking.select-time') }}" method="POST">
                                                            @csrf
                                                            <input type="hidden" name="availability_id" value="{{ $slot->id }}">
                                                            <button type="submit" class="btn btn-sm btn-success">
                                                                Available
                                                            </button>
                                                        </form>
                                                    @elseif($isBooked)
                                                        <span class="badge bg-danger">Booked</span>
                                                    @else
                                                        <span class="badge bg-secondary">Unavailable</span>
                                                    @endif
                                                </td>
                                            @endfor
                                        </tr>
                                    @endfor
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Booking Steps Styles */
.booking-steps {
    margin-bottom: 2rem;
}

.step-indicator {
    display: flex;
    justify-content: space-between;
    margin-bottom: 2rem;
    position: relative;
    padding: 0 1rem;
}

.step-indicator::before {
    content: '';
    position: absolute;
    top: 20px;
    left: 0;
    right: 0;
    height: 2px;
    background: #e2e8f0;
    z-index: 1;
}

.step {
    position: relative;
    z-index: 2;
    text-align: center;
    flex: 1;
}

.step-number {
    width: 40px;
    height: 40px;
    background: white;
    border: 2px solid #e2e8f0;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 0.5rem;
    font-weight: 600;
    color: #64748b;
}

.step.completed .step-number {
    background: #0d6efd;
    border-color: #0d6efd;
    color: white;
}

.step.active .step-number {
    border-color: #0d6efd;
    color: #0d6efd;
}

.step-title {
    font-size: 0.875rem;
    color: #64748b;
}

/* Card and Table Styles */
.card {
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    overflow: hidden;
}

.table {
    margin-bottom: 0;
}

.table th {
    background: #f8fafc;
}

.table-primary {
    background-color: #ebf5ff !important;
}

.btn-success {
    background: #10b981;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 6px;
}

.btn-success:hover {
    background: #059669;
}

.badge {
    padding: 0.5rem 1rem;
    border-radius: 6px;
}

/* Responsive Design */
@media (max-width: 768px) {
    .step-indicator {
        overflow-x: auto;
        padding-bottom: 1rem;
    }
    
    .step {
        min-width: 120px;
    }
    
    .card-header .d-flex {
        flex-direction: column;
        gap: 1rem;
    }
}
</style>
@endsection
