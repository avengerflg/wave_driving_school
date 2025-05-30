@extends('layouts.instructor')

@section('title', 'Manage Availability')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bx bx-calendar me-2"></i>Manage Availability</h5>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#add-availability-modal">
                    <i class="bx bx-plus me-1"></i> Add Availability
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Calendar Navigation -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ $viewMonth->format('F Y') }}</h5>
                    <div>
                        <a href="{{ route('instructor.calendar.availability', ['month' => $prevMonth]) }}" class="btn btn-outline-primary me-2">
                            <i class="bx bx-chevron-left"></i> Previous
                        </a>
                        <a href="{{ route('instructor.calendar.availability') }}" class="btn btn-outline-primary me-2">Today</a>
                        <a href="{{ route('instructor.calendar.availability', ['month' => $nextMonth]) }}" class="btn btn-outline-primary">
                            Next <i class="bx bx-chevron-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Calendar View -->
<div class="row">
    @foreach($weeks as $days)
        <div class="col-12 mb-4">
            <div class="card shadow-sm">
                <div class="row g-0">
                    @foreach($days as $day)
                        @php
                            $dateStr = $day->format('Y-m-d');
                            $dayAvailabilities = $groupedAvailabilities[$dateStr] ?? collect();
                            $isToday = $day->isToday();
                            $isPast = $day->isPast() && !$isToday;
                            $isCurrentMonth = $day->month === $viewMonth->month;
                        @endphp
                        <div class="col border-end {{ !$isCurrentMonth ? 'bg-light' : '' }}">
                            <div class="p-3">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div>
                                        <span class="d-block {{ $isToday ? 'text-primary fw-bold' : '' }}">
                                            {{ $day->format('D') }}
                                        </span>
                                        <span class="fs-4 {{ $isPast ? 'text-muted' : ($isToday ? 'text-primary fw-bold' : '') }}">
                                            {{ $day->format('d') }}
                                        </span>
                                    </div>
                                    @if(!$isPast)
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-primary rounded-circle"
                                                data-bs-toggle="modal"
                                                data-bs-target="#add-availability-modal"
                                                data-date="{{ $dateStr }}">
                                            <i class="bx bx-plus"></i>
                                        </button>
                                    @endif
                                </div>

                                <div class="availability-slots" style="min-height: 100px; max-height: 300px; overflow-y: auto;">
    @if($dayAvailabilities->count() > 0)
        @php
            // Group continuous slots
            $groupedSlots = [];
            $currentGroup = null;
            
            foreach($dayAvailabilities->sortBy('start_time') as $slot) {
                $startTime = \Carbon\Carbon::parse($slot->start_time);
                $endTime = \Carbon\Carbon::parse($slot->end_time);
                
                if ($currentGroup === null) {
                    $currentGroup = [
                        'start' => $startTime,
                        'end' => $endTime,
                        'slots' => [$slot],
                        'has_bookings' => $slot->isBooked()
                    ];
                } elseif ($currentGroup['end']->eq($startTime) && $currentGroup['has_bookings'] == $slot->isBooked()) {
                    // Continuous slot
                    $currentGroup['end'] = $endTime;
                    $currentGroup['slots'][] = $slot;
                } else {
                    // New group
                    $groupedSlots[] = $currentGroup;
                    $currentGroup = [
                        'start' => $startTime,
                        'end' => $endTime,
                        'slots' => [$slot],
                        'has_bookings' => $slot->isBooked()
                    ];
                }
            }
            if ($currentGroup !== null) {
                $groupedSlots[] = $currentGroup;
            }
        @endphp
        
        @foreach($groupedSlots as $group)
            <div class="card border-0 shadow-sm mb-2 {{ $group['has_bookings'] ? 'border-warning' : '' }}">
                <div class="card-body p-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="small {{ $group['has_bookings'] ? 'text-warning' : '' }}">
                                {{ $group['start']->format('H:i') }} - 
                                {{ $group['end']->format('H:i') }}
                            </span>
                            @if($group['has_bookings'])
                                <span class="badge bg-warning ms-1">Booked</span>
                            @else
                                <span class="badge bg-success ms-1">Available</span>
                            @endif
                        </div>
                        @if(!$group['has_bookings'])
                            <button type="button" 
                                    class="btn btn-sm btn-icon btn-outline-danger"
                                    onclick="deleteTimeRange('{{ $dateStr }}', '{{ $group['start']->format('H:i') }}', '{{ $group['end']->format('H:i') }}')">
                                <i class="bx bx-trash"></i>
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    @else
        @if(!$isPast && $isCurrentMonth)
            <div class="text-center text-muted small py-3">
                <i class="bx bx-calendar-x mb-1"></i>
                <p class="mb-0">No availability</p>
            </div>
        @endif
    @endif
</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endforeach
</div>

<!-- Add Availability Modal -->
<div class="modal fade" id="add-availability-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Availability</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Navigation Tabs -->
                <ul class="nav nav-tabs" id="availabilityTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="single-tab" data-bs-toggle="tab" data-bs-target="#single" type="button" role="tab">
                            <i class="bx bx-calendar me-1"></i>Single Date
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="routine-tab" data-bs-toggle="tab" data-bs-target="#routine" type="button" role="tab">
                            <i class="bx bx-calendar-week me-1"></i>Routine Schedule
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="recurring-tab" data-bs-toggle="tab" data-bs-target="#recurring" type="button" role="tab">
                            <i class="bx bx-repeat me-1"></i>Custom Recurring
                        </button>
                    </li>
                </ul>

                <div class="tab-content mt-4" id="availabilityTabContent">
                    <!-- Single Date Tab -->
                    <div class="tab-pane fade show active" id="single" role="tabpanel">
                        <form action="{{ route('instructor.availability.store') }}" method="POST" id="singleForm">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Date</label>
                                <input type="date" name="date" class="form-control" required min="{{ now()->format('Y-m-d') }}" value="{{ now()->format('Y-m-d') }}">
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Start Time</label>
                                    <input type="time" name="start_time" class="form-control" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">End Time</label>
                                    <input type="time" name="end_time" class="form-control" required>
                                </div>
                            </div>
                            <div class="modal-footer border-0 px-0">
                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Add Availability</button>
                            </div>
                        </form>
                    </div>

                    <!-- Routine Schedule Tab -->
                    <div class="tab-pane fade" id="routine" role="tabpanel">
                        <form action="{{ route('instructor.availability.store') }}" method="POST" id="routineForm">
                            @csrf
                            <input type="hidden" name="routine_schedule" value="true">
                            
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label class="form-label">Start Date</label>
                                    <input type="date" name="routine_start_date" class="form-control" required min="{{ now()->format('Y-m-d') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">End Date</label>
                                    <input type="date" name="routine_end_date" class="form-control" required>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label">Schedule Type</label>
                                <div class="card">
                                    <div class="card-body p-3">
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="radio" name="routine_schedule_type" id="standard_schedule" value="standard" checked>
                                            <label class="form-check-label" for="standard_schedule">
                                                <strong>Standard Schedule</strong>
                                                <div class="text-muted small mt-1">
                                                    <div>Monday - Friday: 7:00 AM - 4:00 PM</div>
                                                    <div>Saturday: 7:00 AM - 5:00 PM</div>
                                                    <div>Sunday: Off</div>
                                                </div>
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="routine_schedule_type" id="custom_schedule" value="custom">
                                            <label class="form-check-label" for="custom_schedule">
                                                <strong>Custom Weekly Schedule</strong>
                                                <div class="text-muted small mt-1">Set different times for each day of the week</div>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Custom Schedule Fields -->
                            <div id="custom-schedule-fields" class="mb-4" style="display: none;">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0">Custom Weekly Schedule</h6>
                                    </div>
                                    <div class="card-body">
                                        @php
                                            $days = [
                                                'monday' => 'Monday',
                                                'tuesday' => 'Tuesday', 
                                                'wednesday' => 'Wednesday',
                                                'thursday' => 'Thursday',
                                                'friday' => 'Friday',
                                                'saturday' => 'Saturday',
                                                'sunday' => 'Sunday'
                                            ];
                                        @endphp
                                        @foreach($days as $key => $day)
                                            <div class="row mb-3">
                                                <div class="col-md-3">
                                                    <div class="form-check mt-2">
                                                        <input class="form-check-input day-checkbox" type="checkbox" id="{{ $key }}_enabled" data-day="{{ $key }}">
                                                        <label class="form-check-label fw-semibold" for="{{ $key }}_enabled">
                                                            {{ $day }}
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <input type="time" name="{{ $key }}_start" class="form-control day-time" placeholder="Start time" disabled>
                                                </div>
                                                <div class="col-md-4">
                                                    <input type="time" name="{{ $key }}_end" class="form-control day-time" placeholder="End time" disabled>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <div class="modal-footer border-0 px-0">
                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bx bx-calendar-plus me-1"></i>Create Routine Schedule
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Custom Recurring Tab -->
                    <div class="tab-pane fade" id="recurring" role="tabpanel">
                        <form action="{{ route('instructor.availability.store') }}" method="POST" id="recurringForm">
                            @csrf
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Start Date</label>
                                    <input type="date" name="date" class="form-control" required min="{{ now()->format('Y-m-d') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Repeat Until</label>
                                    <input type="date" name="recur_until" class="form-control" required>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Start Time</label>
                                    <input type="time" name="start_time" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">End Time</label>
                                    <input type="time" name="end_time" class="form-control" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <input type="hidden" name="is_recurring" value="1">
                                <label class="form-label">Repeat on Days</label>
                                <div class="row">
                                    @php
                                        $weekDays = [
                                            0 => 'Sunday',
                                            1 => 'Monday', 
                                            2 => 'Tuesday',
                                            3 => 'Wednesday',
                                            4 => 'Thursday',
                                            5 => 'Friday',
                                            6 => 'Saturday'
                                        ];
                                    @endphp
                                    @foreach($weekDays as $value => $dayName)
                                        <div class="col-md-6 mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="days_of_week[]" value="{{ $value }}" id="day_{{ $value }}">
                                                <label class="form-check-label" for="day_{{ $value }}">
                                                    {{ $dayName }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="modal-footer border-0 px-0">
                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Create Recurring Availability</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page-scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle schedule type changes
    const standardSchedule = document.getElementById('standard_schedule');
    const customSchedule = document.getElementById('custom_schedule');
    const customFields = document.getElementById('custom-schedule-fields');

    function toggleCustomFields() {
        if (customSchedule && customSchedule.checked) {
            customFields.style.display = 'block';
        } else {
            customFields.style.display = 'none';
        }
    }

    if (standardSchedule) standardSchedule.addEventListener('change', toggleCustomFields);
    if (customSchedule) customSchedule.addEventListener('change', toggleCustomFields);

    // Handle day checkbox changes
    document.querySelectorAll('.day-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const day = this.dataset.day;
            const timeInputs = document.querySelectorAll(`input[name="${day}_start"], input[name="${day}_end"]`);
            
            timeInputs.forEach(input => {
                input.disabled = !this.checked;
                if (!this.checked) {
                    input.value = '';
                }
            });
        });
    });

    // Auto-fill dates when modal opens
    const modal = document.getElementById('add-availability-modal');
    modal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const date = button ? button.getAttribute('data-date') : null;
        
        if (date) {
            // Set date for all forms
            const dateInputs = modal.querySelectorAll('input[type="date"]');
            dateInputs.forEach(input => {
                if (input.name === 'date' || input.name === 'routine_start_date') {
                    input.value = date;
                }
            });
        }
    });

    // Form validation for routine schedule
    document.getElementById('routineForm').addEventListener('submit', function(e) {
        const scheduleType = document.querySelector('input[name="routine_schedule_type"]:checked').value;
        
        if (scheduleType === 'custom') {
            const anyDaySelected = Array.from(document.querySelectorAll('.day-checkbox')).some(cb => cb.checked);
            if (!anyDaySelected) {
                e.preventDefault();
                alert('Please select at least one day for your custom schedule.');
                return false;
            }

            // Validate that selected days have both start and end times
            let hasErrors = false;
            document.querySelectorAll('.day-checkbox:checked').forEach(checkbox => {
                const day = checkbox.dataset.day;
                const startTime = document.querySelector(`input[name="${day}_start"]`).value;
                const endTime = document.querySelector(`input[name="${day}_end"]`).value;
                
                if (!startTime || !endTime) {
                    hasErrors = true;
                }
            });

            if (hasErrors) {
                e.preventDefault();
                alert('Please provide both start and end times for all selected days.');
                return false;
            }
        }
    });
});

function deleteTimeRange(date, startTime, endTime) {
    if (confirm('Are you sure you want to delete this availability slot?')) {
        // Create a form to submit the delete request
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("instructor.availability.bulk-delete") }}';
        
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = '{{ csrf_token() }}';
        form.appendChild(csrfInput);
        
        const dateInput = document.createElement('input');
        dateInput.type = 'hidden';
        dateInput.name = 'date';
        dateInput.value = date;
        form.appendChild(dateInput);
        
        document.body.appendChild(form);
        form.submit();
    }
}
</script>

<style>
.availability-slots {
    max-height: 200px;
    overflow-y: auto;
}

.btn-check:checked + .btn-outline-primary {
    background-color: #696cff;
    color: #fff;
}

.nav-tabs .nav-link {
    border: none;
    color: #6c757d;
}

.nav-tabs .nav-link.active {
    background-color: #696cff;
    color: white;
    border-radius: 6px;
}

.nav-tabs {
    border-bottom: none;
    background-color: #f8f9fa;
    padding: 8px;
    border-radius: 8px;
}

.day-checkbox:disabled + label {
    opacity: 0.5;
}

.day-time:disabled {
    background-color: #f8f9fa;
}
</style>
@endsection