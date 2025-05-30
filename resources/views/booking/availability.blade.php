@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <!-- Booking Steps -->
            <div class="booking-steps">
                <div class="step-indicator mb-5">
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

                <div class="booking-card shadow-lg rounded-4 p-0 bg-white">
                    <div class="card-header bg-primary text-white rounded-top-4 px-5 py-4">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                            <div>
                                <h4 class="mb-1 fw-bold">Available Times</h4>
                                <p class="mb-0 opacity-90">Choose your preferred time slot with {{ $instructor->user->name }}</p>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="{{ route('booking.availability', [
                                    'instructor' => $instructor->id,
                                    'week_start' => $startDate->format('Y-m-d'),
                                    'direction' => 'prev'
                                ]) }}" class="btn btn-light btn-sm fw-semibold px-3 py-2">
                                    <i class="fas fa-chevron-left me-1"></i> Previous
                                </a>
                                
                                <a href="{{ route('booking.availability', [
                                    'instructor' => $instructor->id,
                                    'week_start' => $startDate->format('Y-m-d'),
                                    'direction' => 'next'
                                ]) }}" class="btn btn-light btn-sm fw-semibold px-3 py-2">
                                    Next <i class="fas fa-chevron-right ms-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-5">
                        <!-- Service Selection -->
                        <div class="service-selection-card mb-4">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <label for="service-select" class="form-label fw-semibold text-dark mb-2">
                                        <i class="fas fa-clipboard-list text-primary me-2"></i>Select Service
                                    </label>
                                    <select id="service-select" class="form-select form-select-lg border-2">
                                        @foreach($services as $service)
                                            <option value="{{ $service->id }}" data-duration="{{ $service->duration }}">
                                                {{ $service->name }} ({{ $service->duration }} minutes)
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <div class="availability-legend">
                                        <h6 class="fw-semibold mb-2">Legend:</h6>
                                        <div class="d-flex flex-wrap gap-3">
                                            <div class="legend-item">
                                                <span class="legend-dot available"></span>
                                                <small>Available</small>
                                            </div>
                                            <div class="legend-item">
                                                <span class="legend-dot booked"></span>
                                                <small>Booked</small>
                                            </div>
                                            <div class="legend-item">
                                                <span class="legend-dot unavailable"></span>
                                                <small>Unavailable</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Calendar Table -->
                        <div class="table-responsive calendar-container">
                            <table class="table table-bordered align-middle mb-0 calendar-table" id="availability-table">
                                <thead>
                                    <tr>
                                        <th class="time-header">Time</th>
                                        @for($date = clone $startDate; $date <= $endDate; $date->addDay())
                                            <th class="date-header {{ $date->isToday() ? 'today' : '' }}">
                                                <div class="date-info">
                                                    <span class="day-name">{{ $date->format('D') }}</span>
                                                    <span class="date-number">{{ $date->format('j') }}</span>
                                                    <span class="month-name">{{ $date->format('M') }}</span>
                                                </div>
                                            </th>
                                        @endfor
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($timeSlots as $slot)
    <tr>
        <td class="time-cell">
            <div class="time-label">{{ $slot['label'] }}</div>
        </td>
        @for($date = clone $startDate; $date <= $endDate; $date->addDay())
            @php
                $dateStr = $date->format('Y-m-d');
                $cellTime = $slot['start'];
                $cellEndTime = \Carbon\Carbon::createFromFormat('H:i', $cellTime)->addMinutes(15)->format('H:i');
                $cellTimeFull = \Carbon\Carbon::createFromFormat('H:i', $cellTime)->format('H:i:s');
                $cellEndTimeFull = \Carbon\Carbon::createFromFormat('H:i', $cellEndTime)->format('H:i:s');
                
                // FIX: Better availability checking
                $slotAvailable = false;
                if (isset($availabilitySlots[$dateStr])) {
                    $slotAvailable = $availabilitySlots[$dateStr]->contains(function($s) use ($cellTimeFull) {
                        // Compare just the start time since slots are 15 minutes
                        return $s->start_time === $cellTimeFull && $s->is_available;
                    });
                }
                
                // FIX: Better booking conflict checking
                $isBooked = false;
                if (isset($existingBookings[$dateStr])) {
                    $isBooked = $existingBookings[$dateStr]->contains(function($booking) use ($cellTime, $cellEndTime, $dateStr) {
                        try {
                            // Handle different time formats in database
                            $bookingStartStr = $booking->start_time;
                            $bookingEndStr = $booking->end_time;
                            
                            // Extract time part if it's a full datetime
                            if (strlen($bookingStartStr) > 8 || strpos($bookingStartStr, '-') !== false) {
                                $bookingStartTime = \Carbon\Carbon::parse($bookingStartStr)->format('H:i');
                                $bookingEndTime = \Carbon\Carbon::parse($bookingEndStr)->format('H:i');
                            } else {
                                $bookingStartTime = \Carbon\Carbon::parse($bookingStartStr)->format('H:i');
                                $bookingEndTime = \Carbon\Carbon::parse($bookingEndStr)->format('H:i');
                            }
                            
                            // Check if the 15-minute slot overlaps with the booking
                            $slotStart = \Carbon\Carbon::parse($dateStr . ' ' . $cellTime);
                            $slotEnd = \Carbon\Carbon::parse($dateStr . ' ' . $cellEndTime);
                            $bookingStart = \Carbon\Carbon::parse($dateStr . ' ' . $bookingStartTime);
                            $bookingEnd = \Carbon\Carbon::parse($dateStr . ' ' . $bookingEndTime);
                            
                            // Overlap check: slot overlaps if it starts before booking ends and ends after booking starts
                            return $slotStart->lt($bookingEnd) && $slotEnd->gt($bookingStart);
                        } catch (\Exception $e) {
                            \Log::error('Error checking booking conflict: ' . $e->getMessage());
                            return false;
                        }
                    });
                }
                
                // Determine cell class
                $cellClass = '';
                if ($slotAvailable && !$isBooked) {
                    $cellClass = 'bg-success-subtle border-success available';
                } elseif ($isBooked) {
                    $cellClass = 'bg-danger-subtle border-danger booked';
                } else {
                    $cellClass = 'bg-secondary-subtle border-secondary unavailable';
                }
            @endphp
            <td class="slot-cell {{ $cellClass }}"
                data-date="{{ $dateStr }}"
                data-time="{{ $cellTime }}"
                data-available="{{ $slotAvailable && !$isBooked ? '1' : '0' }}">
                <div class="slot-content flex-column align-items-center justify-content-center">
                    @if($slotAvailable && !$isBooked)
                        <i class="fas fa-plus slot-icon text-success mb-1"></i>
                        <span class="small fw-semibold text-success">Available</span>
                    @elseif($isBooked)
                        <i class="fas fa-times slot-icon text-danger mb-1"></i>
                        <span class="small fw-semibold text-danger">Booked</span>
                    @else
                        <span class="slot-dot bg-secondary"></span>
                    @endif
                </div>
            </td>
        @endfor
    </tr>
@endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Booking Confirmation Modal -->
<div class="modal fade" id="bookingModal" tabindex="-1" aria-labelledby="bookingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white border-0">
                <h5 class="modal-title fw-bold" id="bookingModalLabel">
                    <i class="fas fa-calendar-check me-2"></i>Confirm Your Booking
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="booking-summary">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="summary-card">
                                <h6 class="text-primary fw-semibold mb-3">
                                    <i class="fas fa-user-tie me-2"></i>Instructor Details
                                </h6>
                                <div class="instructor-info">
                                    <img src="{{ $instructor->user->profile_photo_url ?? '/images/default-avatar.png' }}" 
                                         alt="{{ $instructor->user->name }}" 
                                         class="instructor-avatar">
                                    <div>
                                        <h6 class="mb-1">{{ $instructor->user->name }}</h6>
                                        <p class="text-muted mb-0 small">Driving Instructor</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="summary-card">
                                <h6 class="text-primary fw-semibold mb-3">
                                    <i class="fas fa-clock me-2"></i>Booking Details
                                </h6>
                                <div class="booking-details">
                                    <div class="detail-row">
                                        <span class="label">Date:</span>
                                        <span class="value" id="selected-date-display"></span>
                                    </div>
                                    <div class="detail-row">
                                        <span class="label">Time:</span>
                                        <span class="value" id="selected-time-display"></span>
                                    </div>
                                    <div class="detail-row">
                                        <span class="label">Service:</span>
                                        <span class="value" id="selected-service-display"></span>
                                    </div>
                                    <div class="detail-row">
                                        <span class="label">Duration:</span>
                                        <span class="value" id="selected-duration-display"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="alert alert-info mt-4">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Please note:</strong> This time slot will be reserved for you once confirmed. 
                    You can cancel or reschedule up to 24 hours before your lesson.
                </div>
            </div>
            <div class="modal-footer border-0 p-4">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-arrow-left me-2"></i>Go Back
                </button>
                <button type="button" class="btn btn-primary btn-lg px-4" id="confirm-booking-btn">
                    <i class="fas fa-check me-2"></i>Confirm Booking
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Hidden form for submission -->
<form id="booking-form" method="POST" action="{{ route('booking.select-time') }}" style="display: none;">
    @csrf
    <input type="hidden" name="date" id="booking-date">
    <input type="hidden" name="start_time" id="booking-start-time">
    <input type="hidden" name="end_time" id="booking-end-time">
    <input type="hidden" name="service_id" id="booking-service-id">
</form>

@push('styles')
<style>
    .slot-cell {
        position: relative;
        width: 60px;
        height: 60px;
        cursor: pointer;
        transition: all 0.3s ease;
        border-width: 2px !important;
        background: white;
    }
    .slot-cell.selected {
        background-color: var(--bs-primary) !important;
        border-color: var(--bs-primary) !important;
        color: #fff !important;
        transform: scale(1.12);
        z-index: 20;
    }
    .slot-cell.selected .slot-icon {
        color: #fff !important;
        transform: rotate(45deg);
    }
    .slot-icon {
        font-size: 16px;
        transition: all 0.3s ease;
    }
    .slot-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        display: inline-block;
    }
    .legend-dot.available {
        background: #198754 !important;
    }
    .legend-dot.booked {
        background: #dc3545 !important;
    }
    .legend-dot.unavailable {
        background: #6c757d !important;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const serviceSelect = document.getElementById('service-select');
    const table = document.getElementById('availability-table');
    const bookingModal = new bootstrap.Modal(document.getElementById('bookingModal'));
    const confirmBookingBtn = document.getElementById('confirm-booking-btn');
    const bookingForm = document.getElementById('booking-form');
    
    let selectedDate = null;
    let selectedStartTime = null;
    let selectedEndTime = null;

    table.addEventListener('click', function(e) {
        const cell = e.target.closest('.slot-cell');
        if (!cell || cell.getAttribute('data-available') !== '1') return;

        const date = cell.getAttribute('data-date');
        const time = cell.getAttribute('data-time');
        const duration = parseInt(serviceSelect.options[serviceSelect.selectedIndex].getAttribute('data-duration'), 10);
        const slotsNeeded = duration / 15;

        clearSelection();

        const columnCells = Array.from(table.querySelectorAll(`td[data-date="${date}"]`));
        const startIndex = columnCells.findIndex(c => c === cell);

        let canBook = true;
        for (let i = 0; i < slotsNeeded; i++) {
            const targetCell = columnCells[startIndex + i];
            if (!targetCell || targetCell.getAttribute('data-available') !== '1') {
                canBook = false;
                break;
            }
        }

        if (canBook) {
            for (let i = 0; i < slotsNeeded; i++) {
                columnCells[startIndex + i].classList.add('selected');
            }
            const endTime = calculateEndTime(time, duration);
            selectedDate = date;
            selectedStartTime = time;
            selectedEndTime = endTime;
            updateModalContent(date, time, endTime);
            bookingModal.show();
        } else {
            showAlert('Not enough consecutive time slots available for this service. Please select a different time.', 'warning');
        }
    });

    confirmBookingBtn.addEventListener('click', function() {
        if (!selectedDate || !selectedStartTime || !selectedEndTime) {
            showAlert('Please select a time slot first.', 'warning');
            return;
        }
        document.getElementById('booking-date').value = selectedDate;
        document.getElementById('booking-start-time').value = selectedStartTime;
        document.getElementById('booking-end-time').value = selectedEndTime;
        document.getElementById('booking-service-id').value = serviceSelect.value;
        confirmBookingBtn.disabled = true;
        confirmBookingBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';
        bookingForm.submit();
    });

    document.getElementById('bookingModal').addEventListener('hidden.bs.modal', function() {
        clearSelection();
        confirmBookingBtn.disabled = false;
        confirmBookingBtn.innerHTML = '<i class="fas fa-check me-2"></i>Confirm Booking';
    });

    function updateModalContent(date, startTime, endTime) {
        const dateObj = new Date(date);
        const formattedDate = dateObj.toLocaleDateString('en-US', { 
            weekday: 'long', 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric' 
        });
        const serviceOption = serviceSelect.options[serviceSelect.selectedIndex];
        const serviceName = serviceOption.text;
        const duration = serviceOption.getAttribute('data-duration');
        document.getElementById('selected-date-display').textContent = formattedDate;
        document.getElementById('selected-time-display').textContent = `${startTime} - ${endTime}`;
        document.getElementById('selected-service-display').textContent = serviceName.split(' (')[0];
        document.getElementById('selected-duration-display').textContent = `${duration} minutes`;
    }

    function clearSelection() {
        document.querySelectorAll('.slot-cell.selected').forEach(cell => {
            cell.classList.remove('selected');
        });
        selectedDate = null;
        selectedStartTime = null;
        selectedEndTime = null;
    }

    function calculateEndTime(startTime, duration) {
        const [hours, minutes] = startTime.split(':').map(Number);
        const totalMinutes = hours * 60 + minutes + duration;
        const endHours = Math.floor(totalMinutes / 60);
        const endMinutes = totalMinutes % 60;
        return `${String(endHours).padStart(2, '0')}:${String(endMinutes).padStart(2, '0')}`;
    }

    function showAlert(message, type = 'info') {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
        alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.body.appendChild(alertDiv);
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 5000);
    }

    serviceSelect.addEventListener('change', clearSelection);
});
</script>
@endpush
@endsection