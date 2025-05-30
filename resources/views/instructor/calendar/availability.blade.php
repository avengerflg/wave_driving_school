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
    @php
        $currentDate = $startDate->copy();
        $weeks = [];
        while ($currentDate->lte($endDate)) {
            $weekStart = $currentDate->copy()->startOfWeek(Carbon\Carbon::SUNDAY);
            $weekKey = $weekStart->format('W');
            if (!isset($weeks[$weekKey])) {
                $weeks[$weekKey] = [];
            }
            $weeks[$weekKey][] = $currentDate->copy();
            $currentDate->addDay();
        }
    @endphp

    @foreach($weeks as $weekNumber => $days)
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

                                <div class="availability-slots" style="min-height: 100px;">
                                    @if($dayAvailabilities->count() > 0)
                                        @foreach($dayAvailabilities as $slot)
                                            <div class="card border-0 shadow-sm mb-2">
                                                <div class="card-body p-2">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <span class="small">
                                                            {{ Carbon\Carbon::parse($slot->start_time)->format('H:i') }} - 
                                                            {{ Carbon\Carbon::parse($slot->end_time)->format('H:i') }}
                                                        </span>
                                                        <form action="{{ route('instructor.calendar.destroyAvailability', $slot->id) }}" 
                                                              method="POST" 
                                                              class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" 
                                                                    class="btn btn-sm btn-icon btn-outline-danger"
                                                                    onclick="return confirm('Are you sure you want to remove this availability?')">
                                                                <i class="bx bx-trash"></i>
                                                            </button>
                                                        </form>
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
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('instructor.calendar.storeAvailability') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add Availability</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Date</label>
                        <input type="date" 
                               name="date" 
                               class="form-control" 
                               required 
                               min="{{ now()->format('Y-m-d') }}"
                               value="{{ now()->format('Y-m-d') }}">
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
                    <div class="form-check form-switch mb-3">
                        <input type="checkbox" class="form-check-input" id="is_recurring" name="is_recurring" value="1">
                        <label class="form-check-label" for="is_recurring">Recurring availability</label>
                    </div>
                    <div id="recurring-options" style="display: none;">
                        <div class="mb-3">
                            <label class="form-label">Recur Until</label>
                            <input type="date" 
                                   name="recur_until" 
                                   class="form-control"
                                   min="{{ now()->addDay()->format('Y-m-d') }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label d-block">Days of Week</label>
                            <div class="btn-group" role="group">
                                @foreach(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $index => $day)
                                    <input type="checkbox" 
                                           class="btn-check" 
                                           name="days_of_week[]" 
                                           id="day_{{ $index }}" 
                                           value="{{ $index }}">
                                    <label class="btn btn-outline-primary" for="day_{{ $index }}">
                                        {{ $day }}
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Availability</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('page-scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const isRecurringCheckbox = document.getElementById('is_recurring');
    const recurringOptions = document.getElementById('recurring-options');
    
    isRecurringCheckbox.addEventListener('change', function() {
        recurringOptions.style.display = this.checked ? 'block' : 'none';
    });

    // Handle date pre-filling when clicking add button on calendar
    const addAvailabilityModal = document.getElementById('add-availability-modal');
    addAvailabilityModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const date = button.getAttribute('data-date');
        if (date) {
            this.querySelector('input[name="date"]').value = date;
        }
    });
});
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
</style>
@endsection
