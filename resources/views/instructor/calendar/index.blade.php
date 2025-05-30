@extends('layouts.instructor')

@section('title', 'My Calendar')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bx bx-calendar me-2"></i>My Calendar</h5>
                <a href="{{ route('instructor.availability.index') }}" class="btn btn-primary btn-sm">
                    <i class="bx bx-edit me-1"></i> Manage Availability
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Sidebar Calendar -->
    <div class="col-md-4 col-lg-3 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <button id="prev-month" class="btn btn-sm btn-outline-secondary"><i class="bx bx-chevron-left"></i></button>
                    <h6 id="current-month" class="mb-0 fw-bold">{{ now()->format('F Y') }}</h6>
                    <button id="next-month" class="btn btn-sm btn-outline-secondary"><i class="bx bx-chevron-right"></i></button>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm text-center calendar-table mb-0">
                        <thead>
                            <tr class="text-muted small">
                                <th>M</th><th>T</th><th>W</th><th>T</th><th>F</th><th>S</th><th>S</th>
                            </tr>
                        </thead>
                        <tbody id="calendar-body"></tbody>
                    </table>
                </div>
                <div class="mt-3">
                    <div class="d-flex align-items-center mb-1">
                        <span class="legend-dot available me-2"></span> Available
                    </div>
                    <div class="d-flex align-items-center mb-1">
                        <span class="legend-dot booked me-2"></span> Booked
                    </div>
                    <div class="d-flex align-items-center">
                        <span class="legend-dot partially-booked me-2"></span> Partially Booked
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="col-md-8 col-lg-9">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 id="selected-date" class="card-title mb-0">{{ $today->format('l, F j, Y') }}</h5>
                    <div class="d-flex gap-2">
                        <button id="prev-week" class="btn btn-outline-primary btn-sm">
                            <i class="bx bx-chevron-left"></i> Previous Week
                        </button>
                        <button id="next-week" class="btn btn-outline-primary btn-sm">
                            Next Week <i class="bx bx-chevron-right"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Weekly Calendar View -->
                <div class="table-responsive">
                    <table class="table table-bordered align-middle mb-0 weekly-calendar-table" id="weekly-calendar">
                        <thead>
                            <tr>
                                <th class="time-header" style="width: 100px;">Time</th>
                                <th class="date-header monday">Mon</th>
                                <th class="date-header tuesday">Tue</th>
                                <th class="date-header wednesday">Wed</th>
                                <th class="date-header thursday">Thu</th>
                                <th class="date-header friday">Fri</th>
                                <th class="date-header saturday">Sat</th>
                                <th class="date-header sunday">Sun</th>
                            </tr>
                            <tr id="date-row">
                                <th></th>
                                <th class="date-number" data-day="monday"></th>
                                <th class="date-number" data-day="tuesday"></th>
                                <th class="date-number" data-day="wednesday"></th>
                                <th class="date-number" data-day="thursday"></th>
                                <th class="date-number" data-day="friday"></th>
                                <th class="date-number" data-day="saturday"></th>
                                <th class="date-number" data-day="sunday"></th>
                            </tr>
                        </thead>
                        <tbody id="weekly-calendar-body">
                            <!-- Time slots will be generated here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Booking Details Modal -->
<div class="modal fade" id="booking-details-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Booking Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="booking-details-content">
                <div class="d-flex justify-content-center py-3">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Add Availability Modal -->
<div class="modal fade" id="add-availability-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Availability</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="availability-form" method="POST" action="{{ route('instructor.calendar.store-availability') }}">
                @csrf
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Date:</label>
                            <input type="date" name="date" id="availability-date" class="form-control" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Time:</label>
                            <input type="time" name="start_time" id="availability-time" class="form-control" readonly>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Staff:</label>
                            <input type="text" value="{{ auth()->user()->name }}" class="form-control" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Location:</label>
                            <input type="text" value="North Brisbane" class="form-control" readonly>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Length:</label>
                        <select name="duration" id="availability-duration" class="form-select" required>
                            <option value="15">15 mins</option>
                            <option value="30" selected>30 mins</option>
                            <option value="45">45 mins</option>
                            <option value="60">1 hour</option>
                            <option value="75">1 hour 15 mins</option>
                            <option value="90">1 hour 30 mins</option>
                            <option value="120">2 hours</option>
                            <option value="180">3 hours</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Visibility:</label>
                        <select name="visibility" id="availability-visibility" class="form-select" required>
                            <option value="public" selected>Publicly Available to book</option>
                            <option value="private">Privately Available, shown as booked</option>
                            <option value="hidden">Hidden note or booking. Hidden from clients</option>
                            <option value="note">Public note only</option>
                        </select>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Private Note:</label>
                            <textarea name="private_note" id="availability-private-note" class="form-control" rows="3" placeholder="Internal notes..."></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Public Note:</label>
                            <textarea name="public_note" id="availability-public-note" class="form-control" rows="3" placeholder="Notes visible to clients..."></textarea>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Suburbs:</label>
                        <select name="suburbs[]" id="availability-suburbs" class="form-select" multiple>
                            <option value="all" selected>Available in all Suburbs</option>
                            <option value="brisbane-city">Brisbane City</option>
                            <option value="south-brisbane">South Brisbane</option>
                            <option value="north-brisbane">North Brisbane</option>
                            <option value="west-brisbane">West Brisbane</option>
                            <option value="east-brisbane">East Brisbane</option>
                        </select>
                        <small class="text-muted">Hold Ctrl/Cmd to select multiple suburbs</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Bulk Action:</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bulk_action" id="bulk-action">
                            <label class="form-check-label" for="bulk-action">
                                Select Time Frame - Reuse these selected settings and skip this screen.
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bx bx-check me-1"></i> Add Availability
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Create Booking Modal -->
<div class="modal fade" id="create-booking-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create Booking</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="booking-form" method="POST" action="{{ route('instructor.bookings.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Date:</label>
                            <input type="date" name="date" id="booking-date" class="form-control" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Time:</label>
                            <input type="time" name="start_time" id="booking-time" class="form-control" readonly>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Instructor:</label>
                            <input type="text" value="{{ auth()->user()->name }}" class="form-control" readonly>
                            <input type="hidden" name="instructor_id" value="{{ auth()->user()->instructor->id }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Choose Your Suburb: <span class="text-danger">*</span></label>
                            <select name="suburb_id" id="booking-suburb" class="form-select" required>
                                <option value="">Select Suburb</option>
                                <option value="brisbane-city">Brisbane City</option>
                                <option value="south-brisbane">South Brisbane</option>
                                <option value="north-brisbane">North Brisbane</option>
                                <option value="west-brisbane">West Brisbane</option>
                                <option value="east-brisbane">East Brisbane</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Select Service: <span class="text-danger">*</span></label>
                        <select name="service_id" id="booking-service" class="form-select" required>
                            <option value="">Choose a service</option>
                            <!-- Services will be loaded dynamically -->
                        </select>
                        <small class="text-muted">Service selection will determine the time slot duration</small>
                    </div>

                    <div class="row mb-3" id="time-slot-info" style="display: none;">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Duration:</label>
                            <input type="text" id="booking-duration-display" class="form-control" readonly>
                            <input type="hidden" name="duration" id="booking-duration">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">End Time:</label>
                            <input type="time" name="end_time" id="booking-end-time" class="form-control" readonly>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Pickup Location:</label>
                        <textarea name="pickup_location" id="booking-pickup-location" class="form-control" rows="2" placeholder="Enter pickup address or location details..."></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Customer Information:</label>
                        <div class="row">
                            <div class="col-md-6">
                                <input type="text" name="customer_name" class="form-control mb-2" placeholder="Customer Name" required>
                            </div>
                            <div class="col-md-6">
                                <input type="email" name="customer_email" class="form-control mb-2" placeholder="Customer Email" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <input type="tel" name="customer_phone" class="form-control" placeholder="Customer Phone" required>
                            </div>
                            <div class="col-md-6">
                                <select name="customer_type" class="form-select">
                                    <option value="new">New Customer</option>
                                    <option value="returning">Returning Customer</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Additional Notes:</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="Any special requirements or notes..."></textarea>
                    </div>

                    <!-- Booking Summary -->
                    <div class="card bg-light" id="booking-summary" style="display: none;">
                        <div class="card-body">
                            <h6 class="card-title">Booking Summary</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <small class="text-muted">Date & Time:</small>
                                    <div id="summary-datetime"></div>
                                </div>
                                <div class="col-md-6">
                                    <small class="text-muted">Service:</small>
                                    <div id="summary-service"></div>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-6">
                                    <small class="text-muted">Duration:</small>
                                    <div id="summary-duration"></div>
                                </div>
                                <div class="col-md-6">
                                    <small class="text-muted">Suburb:</small>
                                    <div id="summary-suburb"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success" id="confirm-booking-btn">
                        <i class="bx bx-check me-1"></i> Confirm Booking
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('page-scripts')
<script>
    let currentDate = new Date();
    let selectedDate = new Date();
    let currentMonth = currentDate.getMonth();
    let currentYear = currentDate.getFullYear();
    let currentWeekStart = getMonday(new Date());
    let availabilities = [];
    let bookings = [];
    let services = [];
    let bookingModal;
    let availabilityModal;
    let createBookingModal;

    // Sample services data (replace with actual API call)
    const servicesData = [
        { id: 1, name: 'Basic Driving Lesson', duration: 60, price: 80 },
        { id: 2, name: 'Highway Driving', duration: 90, price: 120 },
        { id: 3, name: 'Parallel Parking', duration: 45, price: 60 },
        { id: 4, name: 'City Driving', duration: 75, price: 100 },
        { id: 5, name: 'Test Preparation', duration: 120, price: 150 }
    ];

    // Function to get Monday of the week for a given date
    function getMonday(date) {
        const d = new Date(date);
        const day = d.getDay();
        const diff = d.getDate() - day + (day === 0 ? -6 : 1);
        return new Date(d.setDate(diff));
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Initialize modals
        bookingModal = new bootstrap.Modal(document.getElementById('booking-details-modal'));
        availabilityModal = new bootstrap.Modal(document.getElementById('add-availability-modal'));
        createBookingModal = new bootstrap.Modal(document.getElementById('create-booking-modal'));
        
        // Load services into select
        loadServices();
        
        // Initial calendar data
        fetchCalendarData();

        // Month navigation
        document.getElementById('prev-month').addEventListener('click', function() {
            currentMonth--;
            if (currentMonth < 0) { currentMonth = 11; currentYear--; }
            generateCalendar(currentMonth, currentYear);
        });
        
        document.getElementById('next-month').addEventListener('click', function() {
            currentMonth++;
            if (currentMonth > 11) { currentMonth = 0; currentYear++; }
            generateCalendar(currentMonth, currentYear);
        });

        // Week navigation
        document.getElementById('prev-week').addEventListener('click', function() {
            currentWeekStart.setDate(currentWeekStart.getDate() - 7);
            generateWeeklyCalendar();
        });

        document.getElementById('next-week').addEventListener('click', function() {
            currentWeekStart.setDate(currentWeekStart.getDate() + 7);
            generateWeeklyCalendar();
        });

        // Service selection handler
        document.getElementById('booking-service').addEventListener('change', function() {
            const serviceId = this.value;
            if (serviceId) {
                const service = servicesData.find(s => s.id == serviceId);
                if (service) {
                    updateBookingTimeSlot(service);
                    updateBookingSummary();
                }
            } else {
                document.getElementById('time-slot-info').style.display = 'none';
                document.getElementById('booking-summary').style.display = 'none';
            }
        });

        // Suburb selection handler
        document.getElementById('booking-suburb').addEventListener('change', function() {
            updateBookingSummary();
        });

        // Form submission handlers
        document.getElementById('availability-form').addEventListener('submit', handleAvailabilitySubmit);
        document.getElementById('booking-form').addEventListener('submit', handleBookingSubmit);
    });

    function loadServices() {
        const serviceSelect = document.getElementById('booking-service');
        serviceSelect.innerHTML = '<option value="">Choose a service</option>';
        
        servicesData.forEach(service => {
            const option = document.createElement('option');
            option.value = service.id;
            option.textContent = `${service.name} (${service.duration} mins - $${service.price})`;
            option.dataset.duration = service.duration;
            option.dataset.price = service.price;
            serviceSelect.appendChild(option);
        });
    }

    function updateBookingTimeSlot(service) {
        const startTime = document.getElementById('booking-time').value;
        const duration = service.duration;
        
        // Calculate end time
        const [hours, minutes] = startTime.split(':').map(Number);
        const totalMinutes = hours * 60 + minutes + duration;
        const endHours = Math.floor(totalMinutes / 60);
        const endMinutes = totalMinutes % 60;
        const endTime = `${endHours.toString().padStart(2, '0')}:${endMinutes.toString().padStart(2, '0')}`;
        
        // Update fields
        document.getElementById('booking-duration').value = duration;
        document.getElementById('booking-duration-display').value = `${duration} minutes`;
        document.getElementById('booking-end-time').value = endTime;
        
        // Show time slot info
        document.getElementById('time-slot-info').style.display = 'block';
    }

    function updateBookingSummary() {
        const date = document.getElementById('booking-date').value;
        const time = document.getElementById('booking-time').value;
        const serviceSelect = document.getElementById('booking-service');
        const suburbSelect = document.getElementById('booking-suburb');
        const duration = document.getElementById('booking-duration-display').value;
        
        if (date && time && serviceSelect.value && suburbSelect.value) {
            const serviceText = serviceSelect.options[serviceSelect.selectedIndex].text;
            const suburbText = suburbSelect.options[suburbSelect.selectedIndex].text;
            const endTime = document.getElementById('booking-end-time').value;
            
            document.getElementById('summary-datetime').textContent = `${formatDate(date)} at ${formatTime(time)} - ${formatTime(endTime)}`;
            document.getElementById('summary-service').textContent = serviceText;
            document.getElementById('summary-duration').textContent = duration;
            document.getElementById('summary-suburb').textContent = suburbText;
            
            document.getElementById('booking-summary').style.display = 'block';
        }
    }

    function fetchCalendarData() {
        fetch('/instructor/calendar/data')
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    console.error('Error:', data.error);
                    return;
                }
                availabilities = data.availabilities;
                bookings = data.bookings;
                generateCalendar(currentMonth, currentYear);
                generateWeeklyCalendar();
            })
            .catch(error => {
                console.error('Error fetching calendar data:', error);
            });
    }

    function generateCalendar(month, year) {
        const firstDay = new Date(year, month, 1);
        const lastDay = new Date(year, month + 1, 0);
        const daysInMonth = lastDay.getDate();
        const startingDay = (firstDay.getDay() + 6) % 7;
        const monthNames = ["January", "February", "March", "April", "May", "June",
            "July", "August", "September", "October", "November", "December"];
        
        document.getElementById('current-month').textContent = `${monthNames[month]} ${year}`;
        
        const calendarBody = document.getElementById('calendar-body');
        calendarBody.innerHTML = '';
        let date = 1;
        
        for (let i = 0; i < 6; i++) {
            const row = document.createElement('tr');
            for (let j = 0; j < 7; j++) {
                const cell = document.createElement('td');
                if (i === 0 && j < startingDay) {
                    const prevMonth = month === 0 ? 11 : month - 1;
                    const prevYear = month === 0 ? year - 1 : year;
                    const prevMonthDays = new Date(prevYear, prevMonth + 1, 0).getDate();
                    const prevDate = prevMonthDays - (startingDay - j - 1);
                    cell.innerHTML = `<span class="text-muted small">${prevDate}</span>`;
                    cell.dataset.date = `${prevYear}-${(prevMonth + 1).toString().padStart(2, '0')}-${prevDate.toString().padStart(2, '0')}`;
                } else if (date > daysInMonth) {
                    const nextDate = date - daysInMonth;
                    const nextMonth = month === 11 ? 0 : month + 1;
                    const nextYear = month === 11 ? year + 1 : year;
                    cell.innerHTML = `<span class="text-muted small">${nextDate}</span>`;
                    cell.dataset.date = `${nextYear}-${(nextMonth + 1).toString().padStart(2, '0')}-${nextDate.toString().padStart(2, '0')}`;
                    date++;
                } else {
                    const dateStr = `${year}-${(month + 1).toString().padStart(2, '0')}-${date.toString().padStart(2, '0')}`;
                    const hasAvailability = availabilities.some(a => a.date === dateStr);
                    const hasBookings = bookings.some(b => b.date === dateStr);
                    
                    let badgeClass = '';
                    
                    if (hasAvailability && hasBookings) {
                        badgeClass = 'partially-booked';
                    } else if (hasAvailability) {
                        badgeClass = 'available';
                    } else if (hasBookings) {
                        badgeClass = 'booked';
                    }
                    
                    const today = new Date();
                    const isToday = date === today.getDate() && month === today.getMonth() && year === today.getFullYear();
                    
                    cell.innerHTML = `
                        <button class="btn btn-sm calendar-day-btn ${isToday ? 'border-primary' : ''} ${badgeClass}">
                            ${date}
                        </button>
                    `;
                    cell.dataset.date = dateStr;
                    cell.addEventListener('click', function() {
                        selectDate(dateStr);
                    });
                    date++;
                }
                row.appendChild(cell);
            }
            calendarBody.appendChild(row);
            if (date > daysInMonth) break;
        }
    }

    function generateWeeklyCalendar() {
        const weekEnd = new Date(currentWeekStart);
        weekEnd.setDate(weekEnd.getDate() + 6);
        
        // Update date headers
        const days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        days.forEach((day, index) => {
            const date = new Date(currentWeekStart);
            date.setDate(date.getDate() + index);
            const dateHeader = document.querySelector(`[data-day="${day}"]`);
            if (dateHeader) {
                dateHeader.textContent = date.getDate();
                dateHeader.dataset.date = date.toISOString().split('T')[0];
                
                const today = new Date();
                if (date.toDateString() === today.toDateString()) {
                    dateHeader.classList.add('today');
                } else {
                    dateHeader.classList.remove('today');
                }
            }
        });

        // Generate time slots (6 AM to 10 PM in 15-minute intervals)
        const timeSlots = [];
        for (let hour = 6; hour <= 22; hour++) {
            for (let minute = 0; minute < 60; minute += 15) {
                const timeStr = `${hour.toString().padStart(2, '0')}:${minute.toString().padStart(2, '0')}`;
                timeSlots.push(timeStr);
            }
        }

        const calendarBody = document.getElementById('weekly-calendar-body');
        calendarBody.innerHTML = '';

        timeSlots.forEach(timeSlot => {
            const row = document.createElement('tr');
            
                        // Time cell
            const timeCell = document.createElement('td');
            timeCell.className = 'time-cell';
            timeCell.innerHTML = `<div class="time-label">${formatTime(timeSlot)}</div>`;
            row.appendChild(timeCell);

            // Day cells
            days.forEach((day, index) => {
                const date = new Date(currentWeekStart);
                date.setDate(date.getDate() + index);
                const dateStr = date.toISOString().split('T')[0];
                
                const cell = document.createElement('td');
                cell.className = 'slot-cell';
                cell.dataset.date = dateStr;
                cell.dataset.time = timeSlot;

                // Check availability and bookings
                const hasAvailability = availabilities.some(a => 
                    a.date === dateStr && 
                    timeSlot >= a.start_time.substring(0, 5) && 
                    timeSlot < a.end_time.substring(0, 5)
                );

                const hasBooking = bookings.some(b => 
                    b.date === dateStr && 
                    timeSlot >= b.start_time.substring(0, 5) && 
                    timeSlot < b.end_time.substring(0, 5)
                );

                let cellClass = 'unavailable';
                let content = '<i class="bx bx-plus text-muted add-availability-btn"></i>';

                if (hasBooking) {
                    cellClass = 'booked';
                    content = '<i class="bx bx-calendar-check text-white"></i>';
                    cell.style.cursor = 'pointer';
                    cell.addEventListener('click', function() {
                        const booking = bookings.find(b => 
                            b.date === dateStr && 
                            timeSlot >= b.start_time.substring(0, 5) && 
                            timeSlot < b.end_time.substring(0, 5)
                        );
                        if (booking) {
                            viewBookingDetails(booking.id);
                        }
                    });
                } else if (hasAvailability) {
                    cellClass = 'available';
                    content = '<span class="available-text">Available</span>';
                    cell.style.cursor = 'pointer';
                    cell.addEventListener('click', function() {
                        openBookingModal(dateStr, timeSlot);
                    });
                } else {
                    // Add click handler for unavailable slots
                    cell.style.cursor = 'pointer';
                    cell.addEventListener('click', function() {
                        openAvailabilityModal(dateStr, timeSlot);
                    });
                }

                cell.className += ` ${cellClass}`;
                cell.innerHTML = `<div class="slot-content">${content}</div>`;
                
                row.appendChild(cell);
            });

            calendarBody.appendChild(row);
        });
    }

    function openBookingModal(date, time) {
        // Check if the selected date is in the past
        const selectedDateTime = new Date(date + 'T' + time);
        const now = new Date();
        
        if (selectedDateTime < now) {
            alert('Cannot create booking for past dates and times.');
            return;
        }
        
        // Set the date and time in the modal
        document.getElementById('booking-date').value = date;
        document.getElementById('booking-time').value = time;
        
        // Reset form to defaults
        document.getElementById('booking-form').reset();
        document.getElementById('booking-date').value = date;
        document.getElementById('booking-time').value = time;
        document.getElementById('booking-suburb').value = '';
        document.getElementById('booking-service').value = '';
        
        // Hide conditional sections
        document.getElementById('time-slot-info').style.display = 'none';
        document.getElementById('booking-summary').style.display = 'none';
        
        // Show the modal
        createBookingModal.show();
    }

    function openAvailabilityModal(date, time) {
        // Check if the selected date is in the past
        const selectedDateTime = new Date(date + 'T' + time);
        const now = new Date();
        
        if (selectedDateTime < now) {
            alert('Cannot add availability for past dates and times.');
            return;
        }
        
        // Set the date and time in the modal
        document.getElementById('availability-date').value = date;
        document.getElementById('availability-time').value = time;
        
        // Reset form to defaults
        document.getElementById('availability-duration').value = '30';
        document.getElementById('availability-visibility').value = 'public';
        document.getElementById('availability-private-note').value = '';
        document.getElementById('availability-public-note').value = '';
        
        // Reset suburbs selection
        const suburbsSelect = document.getElementById('availability-suburbs');
        Array.from(suburbsSelect.options).forEach(option => {
            option.selected = option.value === 'all';
        });
        
        // Reset bulk action checkbox
        document.getElementById('bulk-action').checked = false;
        
        // Show the modal
        availabilityModal.show();
    }

    function selectDate(dateStr) {
        // Update the selected date display
        const date = new Date(dateStr);
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        document.getElementById('selected-date').textContent = date.toLocaleDateString('en-US', options);
        
        // Set the week to show this date
        currentWeekStart = getMonday(date);
        generateWeeklyCalendar();
        
        // Highlight the selected date in the calendar
        document.querySelectorAll('.calendar-day-btn.active').forEach(btn => {
            btn.classList.remove('active', 'btn-primary');
        });
        
        const selectedCell = document.querySelector(`[data-date="${dateStr}"] .calendar-day-btn`);
        if (selectedCell) {
            selectedCell.classList.add('active', 'btn-primary');
        }
    }

    function handleAvailabilitySubmit(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        // Show loading state
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="bx bx-loader-alt bx-spin me-1"></i> Adding...';
        submitBtn.disabled = true;
        
        fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (response.redirected) {
                window.location.href = response.url;
                return;
            }
            return response.json();
        })
        .then(data => {
            if (data && data.error) {
                alert('Error: ' + data.error);
            } else {
                // Success - reload the page to show the new availability
                window.location.reload();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while adding availability');
        })
        .finally(() => {
            // Reset button state
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        });
    }

    function handleBookingSubmit(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        // Show loading state
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="bx bx-loader-alt bx-spin me-1"></i> Creating Booking...';
        submitBtn.disabled = true;
        
        fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (response.redirected) {
                window.location.href = response.url;
                return;
            }
            return response.json();
        })
        .then(data => {
            if (data && data.error) {
                alert('Error: ' + data.error);
            } else if (data && data.success) {
                alert('Booking created successfully!');
                createBookingModal.hide();
                // Reload calendar data to show new booking
                fetchCalendarData();
            } else {
                // Success - reload the page to show the new booking
                window.location.reload();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while creating the booking');
        })
        .finally(() => {
            // Reset button state
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        });
    }

    function formatTime(time) {
        const [hours, minutes] = time.split(':');
        const hour = parseInt(hours);
        const ampm = hour >= 12 ? 'PM' : 'AM';
        const hour12 = hour % 12 || 12;
        return `${hour12}:${minutes} ${ampm}`;
    }

    function formatDate(dateStr) {
        const date = new Date(dateStr);
        return date.toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
    }

    function getStatusBadgeClass(status) {
        switch(status.toLowerCase()) {
            case 'confirmed':
                return 'bg-success';
            case 'cancelled':
                return 'bg-danger';
            case 'completed':
                return 'bg-info';
            default:
                return 'bg-secondary';
        }
    }

    function viewBookingDetails(bookingId) {
        // Show the modal
        bookingModal.show();
        
        // Reset content to loading state
        document.getElementById('booking-details-content').innerHTML = `
            <div class="d-flex justify-content-center py-3">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        `;
        
        // Fetch booking details
        fetch(`/instructor/calendar/booking/${bookingId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Booking not found');
                }
                return response.json();
            })
            .then(booking => {
                document.getElementById('booking-details-content').innerHTML = `
                    <div class="mb-3 d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Booking #${booking.id}</h6>
                        <span class="badge ${getStatusBadgeClass(booking.status)}">${booking.status}</span>
                    </div>
                    
                    <div class="mb-3">
                        <div class="mb-2 d-flex align-items-center">
                            <i class="bx bx-calendar me-2 text-muted"></i>
                            <span>${formatDate(booking.date)}</span>
                        </div>
                        <div class="mb-2 d-flex align-items-center">
                            <i class="bx bx-time me-2 text-muted"></i>
                            <span>${formatTime(booking.start_time.substring(0, 5))} - ${formatTime(booking.end_time.substring(0, 5))}</span>
                        </div>
                        <div class="mb-2 d-flex align-items-center">
                            <i class="bx bx-user me-2 text-muted"></i>
                            <span>${booking.user.name}</span>
                        </div>
                        <div class="mb-2 d-flex align-items-center">
                            <i class="bx bx-purchase-tag me-2 text-muted"></i>
                            <span>${booking.service.name}</span>
                        </div>
                        ${booking.suburb ? `
                        <div class="mb-2 d-flex align-items-center">
                            <i class="bx bx-map me-2 text-muted"></i>
                            <span>${booking.suburb.name}</span>
                        </div>
                        ` : ''}
                    </div>
                    
                    ${booking.notes ? `
                    <div class="mt-3">
                        <h6 class="mb-2">Notes:</h6>
                        <div class="p-3 bg-light rounded">${booking.notes}</div>
                    </div>
                    ` : ''}
                `;
            })
            .catch(error => {
                document.getElementById('booking-details-content').innerHTML = `
                    <div class="text-center text-danger py-3">
                        <i class="bx bx-error-circle fs-1 mb-2"></i>
                        <p>Error loading booking details</p>
                    </div>
                `;
                console.error('Error fetching booking details:', error);
            });
    }
</script>

<style>
    .calendar-table {
        table-layout: fixed;
    }
    
    .calendar-table td {
        padding: 2px;
        text-align: center;
        height: 2.5rem;
        width: 14.28%;
    }
    
    .calendar-day-btn {
        width: 28px !important;
        height: 28px !important;
        padding: 0 !important;
        border-radius: 50% !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        font-size: 0.75rem !important;
        border: 1px solid #dee2e6 !important;
        background: white !important;
        color: #495057 !important;
        transition: all 0.2s ease;
    }

    .calendar-day-btn:hover {
        transform: scale(1.1);
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .calendar-day-btn.available {
        background: #d1edff !important;
        border-color: #0d6efd !important;
        color: #0d6efd !important;
    }

    .calendar-day-btn.booked {
        background: #f8d7da !important;
        border-color: #dc3545 !important;
        color: #dc3545 !important;
    }

    .calendar-day-btn.partially-booked {
        background: #fff3cd !important;
        border-color: #ffc107 !important;
        color: #856404 !important;
    }

    .calendar-day-btn.active {
        background: #0d6efd !important;
        border-color: #0d6efd !important;
        color: white !important;
    }

    .legend-dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        display: inline-block;
    }

    .legend-dot.available {
        background: #28a745;
    }

    .legend-dot.booked {
        background: #dc3545;
    }

    .legend-dot.partially-booked {
        background: #ffc107;
    }

    /* Weekly Calendar Styles */
    .weekly-calendar-table {
        font-size: 0.875rem;
        border-collapse: separate;
        border-spacing: 1px;
    }

    .time-header {
        background: #f8f9fa;
        font-weight: 600;
        vertical-align: middle;
        border: 1px solid #dee2e6;
    }

    .date-header {
        background: #f8f9fa;
        font-weight: 600;
        text-align: center;
        padding: 8px 4px;
        border: 1px solid #dee2e6;
        position: relative;
    }

    .date-header.today {
        background: #e3f2fd;
        color: #1976d2;
        font-weight: bold;
    }

    .date-number {
        font-size: 1.1rem;
        font-weight: bold;
        padding: 8px 4px;
        border: 1px solid #dee2e6;
    }

    .date-number.today {
        background: #e3f2fd;
        color: #1976d2;
    }

    .time-cell {
        background: #f8f9fa;
        font-size: 0.75rem;
        font-weight: 500;
        text-align: center;
        vertical-align: middle;
        padding: 4px;
        border: 1px solid #dee2e6;
        width: 80px;
        min-width: 80px;
    }

    .slot-cell {
        width: 80px;
        height: 25px;
        padding: 2px;
        text-align: center;
        vertical-align: middle;
        border: 1px solid #dee2e6;
        position: relative;
        transition: all 0.2s ease;
        background: #f8f9fa;
    }

    .slot-cell.available {
        background: #28a745;
        border-color: #28a745;
        cursor: pointer;
    }

    .slot-cell.available:hover {
        background: #218838;
        transform: scale(1.05);
        box-shadow: 0 2px 8px rgba(40, 167, 69, 0.3);
    }

    .slot-cell.booked {
        background: #dc3545;
        border-color: #dc3545;
        cursor: pointer;
    }

    .slot-cell.booked:hover {
        background: #c82333;
        transform: scale(1.05);
    }

    .slot-cell.unavailable {
        background: #f8f9fa;
        border-color: #dee2e6;
        cursor: pointer;
    }

    .slot-cell.unavailable:hover {
        background: #e9ecef;
        border-color: #6c757d;
        transform: scale(1.05);
    }

    .slot-cell.unavailable:hover .add-availability-btn {
        color: #495057 !important;
        transform: scale(1.3);
    }

    .slot-content {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 100%;
        font-size: 10px;
        font-weight: 600;
    }

    .available-text {
        color: white;
        font-weight: bold;
        font-size: 9px;
        text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
    }

    .add-availability-btn {
        transition: all 0.2s ease;
        font-size: 14px;
        opacity: 0.6;
    }

    .slot-cell:hover .add-availability-btn {
        opacity: 1;
    }

    /* Modal Enhancements */
    .modal-header {
        background: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
    }

    .modal-footer {
        background: #f8f9fa;
        border-top: 1px solid #dee2e6;
    }

    /* Form Enhancements */
    .form-label.fw-semibold {
        color: #495057;
        margin-bottom: 0.5rem;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
    }

    /* Booking Summary Card */
    .card.bg-light {
        border: 1px solid #e3f2fd;
        background: linear-gradient(135deg, #f8f9ff 0%, #e3f2fd 100%) !important;
    }

    /* Loading States */
    .btn:disabled {
        opacity: 0.65;
        cursor: not-allowed;
    }

    .bx-spin {
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    /* Responsive Design */
    @media (max-width: 1199.98px) {
        .slot-cell {
            width: 70px;
        }
        
        .time-cell {
            width: 70px;
            min-width: 70px;
            font-size: 0.7rem;
        }
        
        .available-text {
            font-size: 8px;
        }
    }

    @media (max-width: 991.98px) {
        .weekly-calendar-table {
            font-size: 0.8rem;
        }
        
        .slot-cell {
            width: 60px;
            height: 22px;
        }
        
        .time-cell {
            width: 65px;
            min-width: 65px;
        }
        
        .available-text {
            font-size: 7px;
        }
    }

    @media (max-width: 767.98px) {
        .calendar-day-btn {
            width: 24px !important;
            height: 24px !important;
            font-size: 0.7rem !important;
        }
        
        .calendar-table th {
            font-size: 0.7rem;
        }

        .weekly-calendar-table {
            font-size: 0.75rem;
        }

        .slot-cell {
            width: 50px;
            height: 20px;
        }
        
        .time-cell {
            width: 55px;
            min-width: 55px;
            font-size: 0.65rem;
        }
        
        .date-header,
        .date-number {
            padding: 4px 2px;
            font-size: 0.8rem;
        }
        
        .available-text {
            font-size: 6px;
        }
    }

    @media (max-width: 575.98px) {
        .slot-cell {
            width: 40px;
            height: 18px;
        }
        
        .slot-content {
            font-size: 8px;
        }
        
        .available-text {
            font-size: 5px;
        }
        
        .add-availability-btn {
            font-size: 10px;
        }
        
        .time-cell {
            width: 50px;
            min-width: 50px;
            font-size: 0.6rem;
        }
    }

    /* Enhanced visual feedback */
    .slot-cell.available::before {
        content: '';
        position: absolute;
        top: 2px;
        right: 2px;
        width: 4px;
        height: 4px;
        background: #fff;
        border-radius: 50%;
        opacity: 0.8;
    }

    .slot-cell.booked::before {
        content: '';
        position: absolute;
        top: 2px;
        right: 2px;
        width: 4px;
        height: 4px;
        background: #fff;
        border-radius: 50%;
    }

    /* Required field indicator */
    .text-danger {
        color: #dc3545 !important;
    }

    /* Success button styling */
    .btn-success {
        background-color: #28a745;
        border-color: #28a745;
    }

    .btn-success:hover {
        background-color: #218838;
        border-color: #1e7e34;
    }
</style>
@endsection
