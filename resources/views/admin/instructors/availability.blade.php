@extends('layouts.admin')

@section('title', 'Manage Instructor Availability')

@section('styles')
<style>
    /* Calendar Components */
    .calendar-container {
        background: #fff;
        border-radius: 0.375rem;
        box-shadow: 0 0.25rem 1rem rgba(161, 172, 184, 0.12);
        margin-bottom: 1.5rem;
    }
    
    .month-navigator {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1.25rem;
        border-bottom: 1px solid #dbdade;
    }
    
    .month-title {
        font-size: 1.3rem;
        font-weight: 600;
        color: #566a7f;
        margin-bottom: 0;
    }
    
    .nav-controls .date-btn {
        padding: 0.5rem 1rem;
        border-radius: 0.375rem;
        color: #697a8d;
        background-color: #fff;
        border: 1px solid #d9dee3;
        margin: 0 0.25rem;
        transition: all 0.2s ease-in-out;
    }
    
    .nav-controls .date-btn:hover {
        background-color: #f6f7f8;
        border-color: #d9dee3;
    }
    
    .nav-controls .date-btn-today {
        background-color: #e7e7ff;
        color: #696cff;
    }
    
    .nav-controls .date-btn-today:hover {
        background-color: #d6d8ff;
    }
    
    /* Week View */
    .week-container {
        border-radius: 0.375rem;
        overflow: hidden;
        margin-bottom: 1.5rem;
        background: #fff;
    }
    
    .week-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1rem 1.25rem;
        background-color: #f5f5f9;
        border-bottom: 1px solid #dbdade;
    }
    
    .week-header h5 {
        font-weight: 600;
        margin: 0;
        color: #566a7f;
    }
    
    .week-days {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
    }
    
    /* Day Cell */
    .day-cell {
        position: relative;
        min-height: 130px;
        padding: 1rem 0.75rem;
        text-align: center;
        border-right: 1px solid #dbdade;
        transition: background 0.2s;
    }
    
    .day-cell:last-child {
        border-right: none;
    }
    
    .day-cell:hover {
        background: #f8f7fa;
    }
    
    .day-name {
        font-weight: 600;
        color: #696cff;
        margin-bottom: 0.25rem;
    }
    
    .day-number {
        font-size: 1.5rem;
        font-weight: 500;
        margin-bottom: 0.5rem;
        color: #566a7f;
    }
    
    .day-cell.today {
        background-color: #f8f7ff;
        box-shadow: inset 0 0 0 1px #e7e7ff;
    }
    
    .day-cell.today .day-number {
        background: #696cff;
        color: white;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 0.5rem;
    }
    
    .day-cell.past {
        opacity: 0.7;
        background: #f9f9f9;
    }
    
    /* Availability Indicators */
    .availability-indicator {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.375rem;
        margin: 0.625rem 0;
    }
    
    .indicator-dot {
        width: 0.625rem;
        height: 0.625rem;
        border-radius: 50%;
    }
    
    .has-slots .indicator-dot {
        background: #71dd37;
    }
    
    .no-slots .indicator-dot {
        background: #d9dee3;
    }
    
    /* Actionable elements */
    .card-hover {
        transition: transform 0.2s, box-shadow 0.2s;
    }
    
    .card-hover:hover {
        transform: translateY(-3px);
        box-shadow: 0 0.5rem 1.5rem rgba(161, 172, 184, 0.18);
    }
    
    /* Time Slots */
    .time-slots-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 1.25rem;
    }
    
    .time-slot {
        position: relative;
        padding: 0.875rem 1rem;
        background: #f8f7fa;
        border-left: 3px solid #71dd37;
        border-radius: 0.375rem;
        margin-bottom: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        transition: all 0.2s;
    }
    
    .time-slot:hover {
        background: #f2f0f7;
        box-shadow: 0 0.125rem 0.25rem rgba(161, 172, 184, 0.1);
    }

    /* User guidance elements */
    .helper-text {
        color: #a1acb8;
        font-size: 0.85rem;
        margin-top: 0.25rem;
    }
    
    .step-number {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background-color: #696cff;
        color: white;
        font-size: 0.875rem;
        font-weight: bold;
        margin-right: 0.5rem;
    }
    
    .form-section-title {
        display: flex;
        align-items: center;
        margin-bottom: 1rem;
        color: #566a7f;
    }

    /* Form enhancements */
    .days-selector {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-bottom: 1rem;
    }
    
    .days-selector .btn-check + .btn {
        padding: 0.5rem;
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
    }
    
    .quick-action-btn {
        transition: all 0.2s;
    }
    
    .quick-action-btn:hover {
        transform: translateY(-1px);
    }
    
    .action-btn-container {
        display: flex;
        gap: 0.5rem;
    }
    
    /* Empty state */
    .empty-state {
        padding: 2rem;
        text-align: center;
    }
    
    .empty-state-icon {
        font-size: 3rem;
        color: #d9dee3;
        margin-bottom: 1rem;
    }
    
    /* Responsive adjustments */
    @media (max-width: 992px) {
        .week-days {
            grid-template-columns: repeat(4, 1fr);
        }
        .day-cell {
            border-bottom: 1px solid #dbdade;
        }
        .month-navigator {
            flex-direction: column;
            gap: 0.75rem;
            align-items: flex-start;
        }
        .nav-controls {
            display: flex;
            width: 100%;
            justify-content: space-between;
        }
    }
    
    @media (max-width: 768px) {
        .week-days {
            grid-template-columns: repeat(2, 1fr);
        }
        .time-slots-grid {
            grid-template-columns: 1fr;
        }
    }
    
    @media (max-width: 576px) {
        .week-days {
            grid-template-columns: 1fr;
        }
        .day-cell {
            border-right: none;
        }
    }

    /* Accessibility improvements */
    .btn:focus, .form-control:focus, .form-select:focus {
        box-shadow: 0 0 0 0.25rem rgba(105, 108, 255, 0.25);
    }
    
    .btn.btn-icon-only {
        padding: 0.5rem;
        line-height: 1;
    }
    
    .visually-hidden {
        position: absolute;
        width: 1px;
        height: 1px;
        padding: 0;
        margin: -1px;
        overflow: hidden;
        clip: rect(0, 0, 0, 0);
        white-space: nowrap;
        border: 0;
    }
</style>
@endsection

@section('content')
<!-- Quick Help Guide -->
<div class="alert alert-primary alert-dismissible fade show mb-4" role="alert" id="helpGuide">
    <div class="d-flex">
        <i class="bx bx-info-circle me-2 mt-1 fs-5"></i>
        <div>
            <h6 class="alert-heading mb-1">Managing Instructor Availability</h6>
            <p class="mb-0">You can add single slots, generate bulk schedules, or modify existing availability. Click on days with slots to view and manage them.</p>
            <div class="mt-2">
                <button type="button" class="btn btn-sm btn-outline-primary me-2" data-bs-toggle="modal" data-bs-target="#helpModal">
                    <i class="bx bx-help-circle me-1"></i> View Help Guide
                </button>
                <button type="button" class="alert-close-persistent" aria-label="Close" onclick="document.getElementById('helpGuide').style.display='none';">
                    <span>Dismiss</span>
                </button>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
</div>

<!-- Page Header -->
<h4 class="fw-bold py-3 mb-4">
    <span class="text-muted fw-light">Admin / Instructors /</span> Manage Availability
</h4>

<div class="row">
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div class="instructor-info d-flex align-items-center">
                        <div class="avatar avatar-lg me-3">
                            @if($instructor->instructor && $instructor->instructor->profile_image)
                                <img src="{{ Storage::url($instructor->instructor->profile_image) }}" alt="{{ $instructor->name }}" class="rounded-circle">
                            @else
                                <span class="avatar-initial rounded-circle bg-primary">
                                    {{ substr($instructor->name, 0, 1) }}
                                </span>
                            @endif
                        </div>
                        <div>
                            <h5 class="mb-0">{{ $instructor->name }}</h5>
                            <div class="d-flex align-items-center mt-1">
                                <span class="badge bg-label-{{ $instructor->status == 'active' ? 'success' : 'danger' }} me-2">
                                    {{ ucfirst($instructor->status) }}
                                </span>
                                <span class="text-muted small">
                                    <i class="bx bx-envelope me-1"></i>{{ $instructor->email }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('admin.instructors.show', $instructor->id) }}" class="btn btn-outline-primary">
                            <i class="bx bx-arrow-back me-1"></i> Back to Instructor
                        </a>
                        <a href="{{ route('admin.instructors.schedule', $instructor->id) }}" class="btn btn-primary">
                            <i class="bx bx-calendar me-1"></i> View Schedule
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="row">
    <!-- Left Column - Actions and Forms -->
    <div class="col-lg-4 mb-4">
        <!-- Add Single Availability -->
        <div class="card mb-4 card-hover">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <span class="step-number">1</span>
                    <h5 class="mb-0">Add Single Availability</h5>
                </div>
            </div>
            <div class="card-body">
                <p class="helper-text mb-3">Add a time slot when the instructor is available for lessons</p>
                <form action="{{ route('admin.instructors.availability.store', $instructor->id) }}" method="POST" id="singleAvailabilityForm">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label" for="date">Date <i class="bx bx-calendar text-primary"></i></label>
                        <input type="date" class="form-control @error('date') is-invalid @enderror" id="date" name="date" value="{{ old('date', now()->format('Y-m-d')) }}" min="{{ now()->format('Y-m-d') }}" required>
                        <div class="helper-text">Select the day for availability</div>
                        @error('date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="start_time">Start Time <i class="bx bx-time text-primary"></i></label>
                            <input type="time" class="form-control @error('start_time') is-invalid @enderror" id="start_time" name="start_time" value="{{ old('start_time', '09:00') }}" required>
                            @error('start_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="end_time">End Time <i class="bx bx-time-five text-primary"></i></label>
                            <input type="time" class="form-control @error('end_time') is-invalid @enderror" id="end_time" name="end_time" value="{{ old('end_time', '17:00') }}" required>
                            @error('end_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="is_recurring" name="is_recurring" {{ old('is_recurring') ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_recurring">Make this recurring <span class="badge bg-label-primary">Weekly pattern</span></label>
                    </div>
                    
                    <div class="recurring-options d-none">
                        <hr>
                        <div class="mb-3">
                            <label class="form-label" for="recur_until">Recur Until <i class="bx bx-calendar-check text-primary"></i></label>
                            <input type="date" class="form-control @error('recur_until') is-invalid @enderror" id="recur_until" name="recur_until" value="{{ old('recur_until', now()->addWeeks(4)->format('Y-m-d')) }}" min="{{ now()->addDay()->format('Y-m-d') }}">
                            <div class="helper-text">Choose end date for recurring pattern</div>
                            @error('recur_until')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <label class="form-label d-block">Days of Week <i class="bx bx-calendar-week text-primary"></i></label>
                        <div class="days-selector mb-3">
                            @foreach(['S', 'M', 'T', 'W', 'T', 'F', 'S'] as $index => $day)
                                <input type="checkbox" class="btn-check" id="day_{{ $index }}" name="days_of_week[]" value="{{ $index }}" {{ in_array($index, old('days_of_week', [])) ? 'checked' : '' }}>
                                <label class="btn btn-outline-primary" for="day_{{ $index }}">{{ $day }}</label>
                            @endforeach
                        </div>
                        <div class="helper-text mb-3">Select days to repeat this time slot</div>
                        @error('days_of_week')
                            <div class="text-danger mb-3">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <button type="submit" class="btn btn-primary d-grid w-100">
                        <i class="bx bx-plus me-1"></i> Add Availability
                    </button>
                </form>
            </div>
        </div>

        <!-- Generate Bulk Availabilities -->
        <div class="card card-hover">
            <div class="card-header d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <span class="step-number">2</span>
                    <h5 class="mb-0">Generate Schedule</h5>
                </div>
                <button type="button" class="btn btn-sm btn-icon btn-outline-primary" data-bs-toggle="collapse" data-bs-target="#generateScheduleForm" aria-expanded="false" aria-controls="generateScheduleForm" title="Expand/Collapse">
                    <i class="bx bx-chevron-down"></i>
                    <span class="visually-hidden">Toggle schedule generator</span>
                </button>
            </div>
            <div class="collapse" id="generateScheduleForm">
                <div class="card-body">
                    <p class="helper-text mb-3">Create multiple availability slots over a range of dates</p>
                    <form action="{{ route('admin.instructors.availability.generate', $instructor->id) }}" method="POST">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label" for="start_date">Start Date <i class="bx bx-calendar text-primary"></i></label>
                                <input type="date" class="form-control" id="start_date" name="start_date" value="{{ now()->format('Y-m-d') }}" min="{{ now()->format('Y-m-d') }}" required>
                                <div class="helper-text">First day</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="end_date">End Date <i class="bx bx-calendar-x text-primary"></i></label>
                                <input type="date" class="form-control" id="end_date" name="end_date" value="{{ now()->addWeeks(2)->format('Y-m-d') }}" min="{{ now()->format('Y-m-d') }}" required>
                                <div class="helper-text">Last day</div>
                            </div>
                        </div>

                        <div class="form-section-title">
                            <i class="bx bx-calendar-week text-primary me-1"></i>
                            <span>Select days to include</span>
                        </div>
                        <div class="days-selector mb-3">
                            @foreach(['S', 'M', 'T', 'W', 'T', 'F', 'S'] as $index => $day)
                                <input type="checkbox" class="btn-check" id="gen_day_{{ $index }}" name="days[]" value="{{ $index }}" {{ $index > 0 && $index < 6 ? 'checked' : '' }}>
                                <label class="btn btn-outline-primary" for="gen_day_{{ $index }}" title="{{ ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'][$index] }}">{{ $day }}</label>
                            @endforeach
                        </div>
                        <div class="helper-text mb-3">Weekdays are selected by default</div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label" for="gen_start_time">Daily Start <i class="bx bx-time text-primary"></i></label>
                                <input type="time" class="form-control" id="gen_start_time" name="start_time" value="09:00" required>
                                <div class="helper-text">First slot time</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="gen_end_time">Daily End <i class="bx bx-time-five text-primary"></i></label>
                                <input type="time" class="form-control" id="gen_end_time" name="end_time" value="17:00" required>
                                <div class="helper-text">Last slot time</div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="slot_duration">Slot Duration <i class="bx bx-timer text-primary"></i></label>
                            <select class="form-select" id="slot_duration" name="slot_duration" required>
                                <option value="60">1 hour</option>
                                <option value="45">45 minutes</option>
                                <option value="30">30 minutes</option>
                                <option value="90">1.5 hours</option>
                                <option value="120">2 hours</option>
                            </select>
                            <div class="helper-text">Length of each individual lesson slot</div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary d-grid w-100">
                            <i class="bx bx-calendar-edit me-1"></i> Generate Availability
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Quick Actions Card -->
        <div class="card mt-4">
            <div class="card-body">
                <h6 class="card-subtitle mb-3 text-muted">Common Actions</h6>
                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-outline-primary quick-action-btn" data-bs-toggle="modal" data-bs-target="#todayScheduleModal">
                        <i class="bx bx-calendar-exclamation me-1"></i> Set Today's Availability
                    </button>
                    <button type="button" class="btn btn-outline-primary quick-action-btn" data-bs-toggle="modal" data-bs-target="#copyScheduleModal">
                        <i class="bx bx-copy me-1"></i> Copy Schedule From Another Week
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column - Calendar View -->
    <div class="col-lg-8">
        <!-- Month Navigator -->
        <div class="card mb-4">
            <div class="month-navigator">
                <h5 class="month-title">
                    {{ $viewMonth->format('F Y') }}
                </h5>
                <div class="nav-controls">
                    <a href="{{ route('admin.instructors.availability', ['instructor' => $instructor->id, 'month' => $prevMonth]) }}" class="btn date-btn" title="Previous Month">
                        <i class="bx bx-chevron-left"></i>
                        <span class="d-none d-sm-inline ms-1">Previous</span>
                    </a>
                    <a href="{{ route('admin.instructors.availability', ['instructor' => $instructor->id]) }}" class="btn date-btn date-btn-today">
                        <i class="bx bx-calendar-check me-1"></i> Today
                    </a>
                    <a href="{{ route('admin.instructors.availability', ['instructor' => $instructor->id, 'month' => $nextMonth]) }}" class="btn date-btn" title="Next Month">
                        <span class="d-none d-sm-inline me-1">Next</span>
                        <i class="bx bx-chevron-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Calendar Legend -->
        <div class="card mb-4">
            <div class="card-body py-3">
                <div class="d-flex flex-wrap gap-3 justify-content-center">
                    <div class="d-flex align-items-center">
                        <span class="indicator-dot me-2" style="background:#71dd37"></span>
                        <span class="text-muted">Has availability</span>
                    </div>
                    <div class="d-flex align-items-center">
                        <span class="indicator-dot me-2" style="background:#d9dee3"></span>
                        <span class="text-muted">No availability</span>
                    </div>
                    <div class="d-flex align-items-center">
                        <span class="badge bg-label-primary me-2">Today</span>
                        <span class="text-muted">Current day</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Calendar Weeks -->
        <div class="calendar-container">
            @php
                $currentDate = $startDate->copy();
                $days = [];
                while ($currentDate->lte($endDate)) {
                    $weekStart = $currentDate->copy()->startOfWeek();
                    $weekNumber = $weekStart->weekOfYear;
                    $year = $weekStart->year;
                    $weekKey = "{$year}-W{$weekNumber}";
                    if (!isset($days[$weekKey])) {
                        $days[$weekKey] = [];
                    }
                    $days[$weekKey][] = $currentDate->copy();
                    $currentDate->addDay();
                }
            @endphp

            @foreach($days as $weekKey => $weekDays)
                <div class="card week-container mb-4">
                    <div class="week-header">
                        <h5>Week {{ substr($weekKey, -2) }}</h5>
                        <span class="text-muted">{{ $weekDays[0]->format('M d') }} - {{ end($weekDays)->format('M d') }}</span>
                    </div>
                    <div class="week-days">
                        @foreach($weekDays as $day)
                            @php
                                $dateStr = $day->format('Y-m-d');
                                $dayAvailabilities = $groupedAvailabilities[$dateStr] ?? collect();
                                $hasAvailability = $dayAvailabilities->count() > 0;
                                $isToday = $day->isToday();
                                $isPast = $day->isPast() && !$isToday;
                            @endphp
                            <div class="day-cell {{ $isToday ? 'today' : '' }} {{ $isPast ? 'past' : '' }}">
                                <div class="day-name">{{ $day->format('D') }}</div>
                                <div class="day-number">{{ $day->format('d') }}</div>
                                
                                <div class="availability-indicator {{ $hasAvailability ? 'has-slots' : 'no-slots' }}">
                                    <span class="indicator-dot"></span>
                                    <span>{{ $dayAvailabilities->count() }} {{ $dayAvailabilities->count() == 1 ? 'slot' : 'slots' }}</span>
                                </div>
                                
                                <div class="mt-2">
                                    @if($hasAvailability)
                                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="collapse" data-bs-target="#day-{{ $dateStr }}" aria-controls="day-{{ $dateStr }}" aria-expanded="false">
                                            <i class="bx bx-calendar-event me-1"></i> View
                                        </button>
                                        
                                        <!-- Add quick-add button when there are already slots -->
                                        @if(!$isPast)
                                            <div class="mt-2">
                                                <button type="button" class="btn btn-sm btn-outline-secondary quick-add-btn" data-date="{{ $dateStr }}">
                                                    <i class="bx bx-plus-circle me-1"></i> Add More
                                                </button>
                                            </div>
                                        @endif
                                    @elseif(!$isPast)
                                        <form action="{{ route('admin.instructors.availability.store', $instructor->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="date" value="{{ $dateStr }}">
                                            <input type="hidden" name="start_time" value="09:00">
                                            <input type="hidden" name="end_time" value="17:00">
                                            <button type="submit" class="btn btn-sm btn-outline-secondary">
                                                <i class="bx bx-plus me-1"></i> Add Full Day
                                            </button>
                                        </form>
                                        
                                        <!-- Add quick-add button even when no slots -->
                                        <div class="mt-2">
                                            <button type="button" class="btn btn-sm btn-outline-primary quick-add-btn" data-date="{{ $dateStr }}">
                                                <i class="bx bx-time me-1"></i> Add Time Slot
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Detailed Time Slots -->
        <div class="time-slots-grid">
            @if(count($groupedAvailabilities) > 0)
                @foreach($groupedAvailabilities as $date => $availabilities)
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="bx bx-calendar-event me-1"></i>
                                {{ \Carbon\Carbon::parse($date)->format('D, M d, Y') }}
                            </h5>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-icon btn-text-secondary p-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bx bx-dots-vertical-rounded"></i>
                                    <span class="visually-hidden">Day actions</span>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <button type="button" class="dropdown-item d-flex align-items-center quick-add-btn" data-date="{{ $date }}">
                                            <i class="bx bx-plus-circle me-2 text-primary"></i> Add More Slots
                                        </button>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="{{ route('admin.instructors.availability.bulk-delete', $instructor->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete all availability for this day?')">
                                            @csrf
                                            <input type="hidden" name="date" value="{{ $date }}">
                                            <button type="submit" class="dropdown-item d-flex align-items-center">
                                                <i class="bx bx-trash me-2 text-danger"></i> Delete All Slots
                                            </button>
                                        </form>
                                    </li>
                                    <li>
                                        <form action="{{ route('admin.instructors.availability.bulk-delete', $instructor->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete all future availability from this day?')">
                                            @csrf
                                            <input type="hidden" name="date" value="{{ $date }}">
                                            <input type="hidden" name="delete_all_future" value="1">
                                            <button type="submit" class="dropdown-item d-flex align-items-center">
                                                <i class="bx bx-trash-alt me-2 text-danger"></i> Delete All Future
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="card-body collapse show" id="day-{{ $date }}">
                            @foreach($availabilities as $availability)
                                <div class="time-slot">
                                    <div class="d-flex align-items-center">
                                        <i class="bx bx-time me-2 text-primary"></i>
                                        <span class="fw-semibold">{{ \Carbon\Carbon::parse($availability->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($availability->end_time)->format('h:i A') }}</span>
                                    </div>
                                    <div class="action-btn-container">
                                        <button type="button" class="btn btn-sm btn-icon btn-outline-secondary edit-slot-btn" 
                                                data-id="{{ $availability->id }}"
                                                data-start="{{ \Carbon\Carbon::parse($availability->start_time)->format('H:i') }}"
                                                data-end="{{ \Carbon\Carbon::parse($availability->end_time)->format('H:i') }}">
                                            <i class="bx bx-edit-alt"></i>
                                            <span class="visually-hidden">Edit slot</span>
                                        </button>
                                        <form action="{{ route('admin.instructors.availability.destroy', [$instructor->id, $availability->id]) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-icon btn-outline-danger" onclick="return confirm('Are you sure you want to delete this slot?')">
                                                <i class="bx bx-trash"></i>
                                                <span class="visually-hidden">Delete slot</span>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            @else
                <div class="card mb-4">
                    <div class="card-body empty-state">
                        <i class="bx bx-calendar-x empty-state-icon"></i>
                        <h5>No Availability Set</h5>
                        <p class="text-muted mb-3">This instructor doesn't have any availability set up yet.</p>
                        <button type="button" class="btn btn-primary" onclick="document.getElementById('singleAvailabilityForm').scrollIntoView({behavior: 'smooth'});">
                            <i class="bx bx-plus me-1"></i> Add Your First Slot
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Quick-Add Time Slot Modal -->
<div class="modal fade" id="quickAddModal" tabindex="-1" aria-labelledby="quickAddModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="quickAddModalLabel">Add Time Slot</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.instructors.availability.store', $instructor->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="date" id="quick_add_date">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="quick_start_time">Start Time</label>
                            <input type="time" class="form-control" id="quick_start_time" name="start_time" value="09:00" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="quick_end_time">End Time</label>
                            <input type="time" class="form-control" id="quick_end_time" name="end_time" value="10:00" required>
                        </div>
                    </div>
                    <div class="alert alert-info d-flex align-items-center">
                        <i class="bx bx-info-circle me-2"></i>
                        <div>Adding availability for <span id="display_date" class="fw-bold"></span></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Time Slot</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Time Slot Modal -->
<div class="modal fade" id="editSlotModal" tabindex="-1" aria-labelledby="editSlotModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editSlotModalLabel">Edit Time Slot</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editSlotForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="edit_start_time">Start Time</label>
                            <input type="time" class="form-control" id="edit_start_time" name="start_time" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="edit_end_time">End Time</label>
                            <input type="time" class="form-control" id="edit_end_time" name="end_time" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Slot</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Helper Modal -->
<div class="modal fade" id="helpModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">How to Manage Instructor Availability</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-4">
                    <h6 class="fw-bold"><i class="bx bx-help-circle me-1"></i> What is instructor availability?</h6>
                    <p>Availability defines when instructors can teach lessons. Students can only book lessons during these time slots.</p>
                </div>
                
                <div class="mb-4">
                    <h6 class="fw-bold"><i class="bx bx-calendar-plus me-1"></i> Adding availability</h6>
                    <p>You can add availability in several ways:</p>
                    <ul>
                        <li><strong>Single slots</strong> - Add individual time slots on specific days</li>
                        <li><strong>Recurring slots</strong> - Create weekly patterns that repeat on selected days</li>
                        <li><strong>Bulk generation</strong> - Create multiple slots over a date range</li>
                        <li><strong>Quick add</strong> - Click "Add Time Slot" buttons on any day in the calendar</li>
                    </ul>
                </div>
                
                <div class="mb-4">
                    <h6 class="fw-bold"><i class="bx bx-edit me-1"></i> Managing existing slots</h6>
                    <p>For days with availability:</p>
                    <ul>
                        <li>Click "View" to see all time slots for that day</li>
                        <li>Use the edit icon to modify a slot's times</li>
                        <li>Use the trash icon to remove individual slots</li>
                        <li>Use the day's menu (three dots) for bulk actions</li>
                    </ul>
                </div>
                
                <div class="alert alert-warning d-flex mb-0">
                    <i class="bx bx-error-circle me-2 mt-1"></i>
                    <div>
                        <strong>Important:</strong> Students may have already booked lessons during these availability slots. Removing availability with existing bookings can cause conflicts.
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Got It</button>
            </div>
        </div>
    </div>
</div>

<!-- Today's Schedule Quick Modal -->
<div class="modal fade" id="todayScheduleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Set Today's Availability</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.instructors.availability.store', $instructor->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="date" value="{{ now()->format('Y-m-d') }}">
                    
                    <div class="alert alert-info">
                        <div class="d-flex align-items-center">
                            <i class="bx bx-calendar-check me-2 fs-5"></i>
                            <div>Setting availability for <strong>{{ now()->format('D, M d, Y') }}</strong></div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="today_start_time">Start Time</label>
                            <input type="time" class="form-control" id="today_start_time" name="start_time" value="{{ now()->addHour()->startOfHour()->format('H:i') }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="today_end_time">End Time</label>
                            <input type="time" class="form-control" id="today_end_time" name="end_time" value="{{ now()->addHours(5)->startOfHour()->format('H:i') }}" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Today's Availability</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Copy Schedule Modal -->
<div class="modal fade" id="copyScheduleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Copy Schedule</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.instructors.availability.copy', $instructor->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label" for="source_week">Copy From Week</label>
                        <input type="date" class="form-control" id="source_week" name="source_week" value="{{ now()->startOfWeek()->format('Y-m-d') }}" required>
                        <div class="helper-text">Select any date in the source week</div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label" for="target_week">Copy To Week</label>
                        <input type="date" class="form-control" id="target_week" name="target_week" value="{{ now()->addWeek()->startOfWeek()->format('Y-m-d') }}" required>
                        <div class="helper-text">Select any date in the target week</div>
                    </div>
                    
                    <div class="alert alert-warning">
                        <div class="d-flex">
                            <i class="bx bx-error me-2 fs-5"></i>
                            <div>This will copy all availability slots from one week to another. Any existing slots in the target week will be preserved.</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Copy Schedule</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle recurring options visibility
        const isRecurringCheckbox = document.getElementById('is_recurring');
        const recurringOptions = document.querySelector('.recurring-options');
        
        function toggleRecurringOptions() {
            if (isRecurringCheckbox.checked) {
                recurringOptions.classList.remove('d-none');
            } else {
                recurringOptions.classList.add('d-none');
            }
        }
        
        isRecurringCheckbox.addEventListener('change', toggleRecurringOptions);
        toggleRecurringOptions();
        
        // Toggle button text for day views
        const dayButtons = document.querySelectorAll('[data-bs-toggle="collapse"]');
        dayButtons.forEach(button => {
            button.addEventListener('click', function() {
                const targetId = this.getAttribute('data-bs-target');
                const isVisible = !document.querySelector(targetId).classList.contains('show');
                
                if (isVisible) {
                    this.innerHTML = '<i class="bx bx-chevron-up me-1"></i> Hide';
                } else {
                    this.innerHTML = '<i class="bx bx-calendar-event me-1"></i> View';
                }
            });
        });
        
        // Quick-add modal functionality
        const quickAddModal = new bootstrap.Modal(document.getElementById('quickAddModal'));
        const quickAddBtns = document.querySelectorAll('.quick-add-btn');
        
        quickAddBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const date = this.dataset.date;
                document.getElementById('quick_add_date').value = date;
                
                // Format the date for display
                const displayDate = new Date(date);
                const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
                document.getElementById('display_date').textContent = displayDate.toLocaleDateString('en-US', options);
                
                quickAddModal.show();
            });
        });
        
        // Edit slot modal functionality
        const editSlotModal = new bootstrap.Modal(document.getElementById('editSlotModal'));
        const editSlotBtns = document.querySelectorAll('.edit-slot-btn');
        
        editSlotBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                const start = this.dataset.start;
                const end = this.dataset.end;
                
                document.getElementById('edit_start_time').value = start;
                document.getElementById('edit_end_time').value = end;
                
                const form = document.getElementById('editSlotForm');
                form.action = "{{ route('admin.instructors.availability', $instructor->id) }}/" + id;
                
                editSlotModal.show();
            });
        });
        
        // Form validation for time inputs
        const timeInputs = document.querySelectorAll('input[type="time"]');
        timeInputs.forEach(input => {
            input.addEventListener('change', function() {
                const formGroup = this.closest('.mb-3');
                const endInput = formGroup.parentElement.querySelector('[name="end_time"]');
                const startInput = formGroup.parentElement.querySelector('[name="start_time"]');
                
                if (endInput && startInput && endInput.value <= startInput.value) {
                    if (!formGroup.querySelector('.invalid-feedback')) {
                        const feedback = document.createElement('div');
                        feedback.classList.add('invalid-feedback', 'd-block');
                        feedback.textContent = 'End time must be after start time';
                        formGroup.appendChild(feedback);
                    }
                } else {
                    const feedback = formGroup.querySelector('.invalid-feedback');
                    if (feedback) {
                        feedback.remove();
                    }
                }
            });
        });
    });
</script>
@endsection
