@extends('layouts.admin')

@section('title', 'Calendar Management')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bx bx-calendar me-2"></i>Calendar Management</h5>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.calendar.availability') }}" class="btn btn-primary btn-sm">
                        <i class="bx bx-edit me-1"></i> Manage Availability
                    </a>
                </div>
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
                
                <div class="mt-4">
                    <label class="form-label fw-semibold">Filter by Instructor:</label>
                    <select id="instructor-filter" class="form-select">
                        <option value="">All Instructors</option>
                        @foreach($instructors as $instructor)
                            @if($instructor->instructor)
                                <option value="{{ $instructor->instructor->id }}">{{ $instructor->name }}</option>
                            @endif
                        @endforeach
                    </select>
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
    <div class="modal-dialog modal-lg" role="document">
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
                <div class="dropdown d-inline-block">
                    <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        Actions
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item status-action" data-status="confirmed" href="#"><i class="bx bx-check text-success me-1"></i> Confirm</a></li>
                        <li><a class="dropdown-item status-action" data-status="completed" href="#"><i class="bx bx-check-double text-info me-1"></i> Complete</a></li>
                        <li><a class="dropdown-item status-action" data-status="cancelled" href="#"><i class="bx bx-x text-danger me-1"></i> Cancel</a></li>
                        <li><a class="dropdown-item status-action" data-status="no-show" href="#"><i class="bx bx-error text-warning me-1"></i> No-show</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="#" id="edit-booking-btn"><i class="bx bx-edit text-primary me-1"></i> Edit</a></li>
                    </ul>
                </div>
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
            <form id="availability-form" method="POST" action="{{ route('admin.calendar.availability.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Instructor: <span class="text-danger">*</span></label>
                            <select name="instructor_id" id="availability-instructor" class="form-select" required>
                                <option value="">Select Instructor</option>
                                @foreach($instructors as $instructor)
                                    @if($instructor->instructor)
                                        <option value="{{ $instructor->instructor->id }}">{{ $instructor->name }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Date: <span class="text-danger">*</span></label>
                            <input type="date" name="date" id="availability-date" class="form-control" required>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Start Time: <span class="text-danger">*</span></label>
                            <input type="time" name="start_time" id="availability-time" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Length: <span class="text-danger">*</span></label>
                            <select name="duration" id="availability-duration" class="form-select" required>
                                <option value="15">15 mins</option>
                                <option value="30" selected>30 mins</option>
                                <option value="45">45 mins</option>
                                <option value="60">1 hour</option>
                                <option value="75">1 hour 15 mins</option>
                                <option value="90">1 hour 30 mins</option>
                                <option value="120">2 hours</option>
                                <option value="180">3 hours</option>
                                <option value="240">4 hours</option>
                                <option value="480">8 hours (Full Day)</option>
                            </select>
                        </div>
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

                    <div class="mb-3">
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" id="is-recurring" name="is_recurring">
                            <label class="form-check-label" for="is-recurring">Make this recurring</label>
                        </div>
                        
                        <div id="recurring-options" style="display: none;">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Recur Until:</label>
                                    <input type="date" name="recur_until" id="recur-until" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Days of Week:</label>
                                    <div class="d-flex flex-wrap gap-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="days_of_week[]" value="1" id="monday">
                                            <label class="form-check-label" for="monday">Mon</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="days_of_week[]" value="2" id="tuesday">
                                            <label class="form-check-label" for="tuesday">Tue</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="days_of_week[]" value="3" id="wednesday">
                                            <label class="form-check-label" for="wednesday">Wed</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="days_of_week[]" value="4" id="thursday">
                                            <label class="form-check-label" for="thursday">Thu</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="days_of_week[]" value="5" id="friday">
                                            <label class="form-check-label" for="friday">Fri</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="days_of_week[]" value="6" id="saturday">
                                            <label class="form-check-label" for="saturday">Sat</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="days_of_week[]" value="0" id="sunday">
                                            <label class="form-check-label" for="sunday">Sun</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
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
                            @foreach($suburbs as $suburb)
                                <option value="{{ $suburb->id }}">{{ $suburb->name }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted">Hold Ctrl/Cmd to select multiple suburbs</small>
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
            <form id="booking-form" method="POST" action="{{ route('admin.calendar.bookings.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Customer: <span class="text-danger">*</span></label>
                            <select name="user_id" id="booking-user" class="form-select" required>
                                <option value="">Select Customer</option>
                                <!-- Users will be loaded dynamically -->
                            </select>
                            <small class="text-muted">Or create new customer below</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Instructor: <span class="text-danger">*</span></label>
                            <select name="instructor_id" id="booking-instructor" class="form-select" required>
                                <option value="">Select Instructor</option>
                                @foreach($instructors as $instructor)
                                    @if($instructor->instructor)
                                        <option value="{{ $instructor->instructor->id }}">{{ $instructor->name }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- New Customer Section -->
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="create-new-customer">
                            <label class="form-check-label" for="create-new-customer">
                                Create New Customer
                            </label>
                        </div>
                    </div>

                    <div id="new-customer-fields" style="display: none;">
                        <div class="card border-info mb-3">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0">New Customer Information</h6>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Customer Name:</label>
                                        <input type="text" name="customer_name" id="customer-name" class="form-control" placeholder="Full Name">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Email:</label>
                                        <input type="email" name="customer_email" id="customer-email" class="form-control" placeholder="Email Address">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="form-label">Phone:</label>
                                        <input type="tel" name="customer_phone" id="customer-phone" class="form-control" placeholder="Phone Number">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">License Number (Optional):</label>
                                        <input type="text" name="customer_license" id="customer-license" class="form-control" placeholder="License Number">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Date: <span class="text-danger">*</span></label>
                            <input type="date" name="date" id="booking-date" class="form-control" required readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Start Time: <span class="text-danger">*</span></label>
                            <input type="time" name="start_time" id="booking-time" class="form-control" required readonly>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Select Service: <span class="text-danger">*</span></label>
                            <select name="service_id" id="booking-service" class="form-select" required>
                                <option value="">Choose a service</option>
                                <!-- Services will be loaded dynamically -->
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Suburb: <span class="text-danger">*</span></label>
                            <select name="suburb_id" id="booking-suburb" class="form-select" required>
                                <option value="">Select Suburb</option>
                                <!-- Suburbs will be loaded dynamically -->
                            </select>
                        </div>
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
                        <label class="form-label fw-semibold">Booking Status:</label>
                        <select name="status" id="booking-status" class="form-select">
                            <option value="pending">Pending</option>
                            <option value="confirmed" selected>Confirmed</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Additional Notes:</label>
                        <textarea name="notes" id="booking-notes" class="form-control" rows="3" placeholder="Any special requirements or notes..."></textarea>
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
                                    <small class="text-muted">Customer & Instructor:</small>
                                    <div id="summary-customer-instructor"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success" id="confirm-booking-btn">
                        <i class="bx bx-check me-1"></i> Create Booking
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
    let filteredInstructorId = '';
    let selectedBookingId = null;
    let bookingModal;
    let availabilityModal;
    let createBookingModal;

    // Add these variables for booking form data
    let users = [];
    let services = [];
    let suburbs = [];
    let loadedData = false;

    // Function to get Monday of the week for a given date
    function getMonday(date) {
        const d = new Date(date);
        const day = d.getDay();
        const diff = d.getDate() - day + (day === 0 ? -6 : 1);
        return new Date(d.setDate(diff));
    }

    // Add function to load booking form data
    function loadBookingFormData() {
        if (loadedData) return Promise.resolve();
        
        return fetch('/admin/calendar/booking-form-data')
            .then(response => response.json())
            .then(data => {
                users = data.users || [];
                services = data.services || [];
                suburbs = data.suburbs || [];
                loadedData = true;
                populateFormSelects();
            })
            .catch(error => {
                console.error('Error loading form data:', error);
                alert('Error loading form data. Please try again.');
            });
    }

    function populateFormSelects() {
        // Populate users
        const userSelect = document.getElementById('booking-user');
        userSelect.innerHTML = '<option value="">Select Customer</option>';
        users.forEach(user => {
            const option = document.createElement('option');
            option.value = user.id;
            option.textContent = `${user.name} (${user.email})`;
            userSelect.appendChild(option);
        });

        // Populate services
        const serviceSelect = document.getElementById('booking-service');
        serviceSelect.innerHTML = '<option value="">Choose a service</option>';
        services.forEach(service => {
            const option = document.createElement('option');
            option.value = service.id;
            option.dataset.duration = service.duration;
            option.dataset.price = service.price;
            option.textContent = `${service.name} (${service.duration} mins - $${service.price})`;
            serviceSelect.appendChild(option);
        });

        // Populate suburbs
        const suburbSelect = document.getElementById('booking-suburb');
        suburbSelect.innerHTML = '<option value="">Select Suburb</option>';
        suburbs.forEach(suburb => {
            const option = document.createElement('option');
            option.value = suburb.id;
            option.textContent = suburb.name;
            suburbSelect.appendChild(option);
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Initialize modals
        bookingModal = new bootstrap.Modal(document.getElementById('booking-details-modal'));
        availabilityModal = new bootstrap.Modal(document.getElementById('add-availability-modal'));
        createBookingModal = new bootstrap.Modal(document.getElementById('create-booking-modal'));
        
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

        // Instructor filter handler
        document.getElementById('instructor-filter').addEventListener('change', function() {
            filteredInstructorId = this.value;
            fetchCalendarData();
        });

        // Service selection handler
        document.getElementById('booking-service').addEventListener('change', function() {
            const option = this.options[this.selectedIndex];
            if (option.value) {
                const duration = option.dataset.duration;
                const price = option.dataset.price;
                updateBookingTimeSlot(duration);
                updateBookingSummary();
            } else {
                document.getElementById('time-slot-info').style.display = 'none';
                document.getElementById('booking-summary').style.display = 'none';
            }
        });

        // Suburb and instructor selection handlers
        document.getElementById('booking-suburb').addEventListener('change', updateBookingSummary);
        document.getElementById('booking-instructor').addEventListener('change', updateBookingSummary);
        document.getElementById('booking-date').addEventListener('change', updateBookingSummary);
        document.getElementById('booking-time').addEventListener('change', updateBookingSummary);

        // New customer toggle
        document.getElementById('create-new-customer').addEventListener('change', function() {
            const newCustomerFields = document.getElementById('new-customer-fields');
            const userSelect = document.getElementById('booking-user');
            
            if (this.checked) {
                newCustomerFields.style.display = 'block';
                userSelect.disabled = true;
                userSelect.removeAttribute('required');
                
                // Make new customer fields required
                document.getElementById('customer-name').setAttribute('required', 'required');
                document.getElementById('customer-email').setAttribute('required', 'required');
                document.getElementById('customer-phone').setAttribute('required', 'required');
            } else {
                newCustomerFields.style.display = 'none';
                userSelect.disabled = false;
                userSelect.setAttribute('required', 'required');
                
                // Remove required from new customer fields
                document.getElementById('customer-name').removeAttribute('required');
                document.getElementById('customer-email').removeAttribute('required');
                document.getElementById('customer-phone').removeAttribute('required');
            }
        });

        // Update booking summary when customer is selected
        document.getElementById('booking-user').addEventListener('change', updateBookingSummary);
        document.getElementById('customer-name').addEventListener('input', updateBookingSummary);

        // Recurring availability toggle
        document.getElementById('is-recurring').addEventListener('change', function() {
            document.getElementById('recurring-options').style.display = this.checked ? 'block' : 'none';
        });

        // Form submission handlers
        document.getElementById('availability-form').addEventListener('submit', handleAvailabilitySubmit);
        document.getElementById('booking-form').addEventListener('submit', handleBookingSubmit);

        // Status change handlers
        document.querySelectorAll('.status-action').forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                if (!selectedBookingId) return;
                
                const status = this.dataset.status;
                updateBookingStatus(selectedBookingId, status);
            });
        });
    });

    function updateBookingTimeSlot(duration) {
        const startTime = document.getElementById('booking-time').value;
        if (!startTime) return;
        
        // Calculate end time
        const [hours, minutes] = startTime.split(':').map(Number);
        const totalMinutes = hours * 60 + minutes + parseInt(duration);
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

    // Updated updateBookingSummary function
    function updateBookingSummary() {
        const date = document.getElementById('booking-date').value;
        const time = document.getElementById('booking-time').value;
        const serviceSelect = document.getElementById('booking-service');
        const instructorSelect = document.getElementById('booking-instructor');
        const userSelect = document.getElementById('booking-user');
        const customerName = document.getElementById('customer-name').value;
        const createNewCustomer = document.getElementById('create-new-customer').checked;
        const duration = document.getElementById('booking-duration-display')?.value;
        
        if (date && time && serviceSelect.value && instructorSelect.value && duration) {
            const serviceText = serviceSelect.options[serviceSelect.selectedIndex].text;
            const instructorText = instructorSelect.options[instructorSelect.selectedIndex].text;
            const endTime = document.getElementById('booking-end-time').value;
            
            let customerText = '';
            if (createNewCustomer && customerName) {
                customerText = customerName + ' (New Customer)';
            } else if (userSelect.value) {
                customerText = userSelect.options[userSelect.selectedIndex].text;
            }
            
            document.getElementById('summary-datetime').textContent = `${formatDate(date)} at ${formatTime(time)} - ${formatTime(endTime)}`;
            document.getElementById('summary-service').textContent = serviceText.split(' (')[0];
            document.getElementById('summary-duration').textContent = duration;
            document.getElementById('summary-customer-instructor').textContent = `${customerText} with ${instructorText}`;
            
            if (customerText) {
                document.getElementById('booking-summary').style.display = 'block';
            }
        }
    }

    function fetchCalendarData() {
        let url = '/admin/calendar/data';
        if (filteredInstructorId) {
            url += `?instructor_id=${filteredInstructorId}`;
        }
        
        fetch(url)
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    console.error('Error:', data.error);
                    return;
                }
                availabilities = data.availabilities || [];
                bookings = data.bookings || [];
                generateCalendar(currentMonth, currentYear);
                generateWeeklyCalendar();
            })
            .catch(error => {
                console.error('Error fetching calendar data:', error);
                availabilities = [];
                bookings = [];
                generateCalendar(currentMonth, currentYear);
                generateWeeklyCalendar();
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
                    const hasAvailability = availabilities.some(a => a.date === dateStr && a.is_available);
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

    // Generate time slots (6 AM to 10 PM in 30-minute intervals)
    const timeSlots = [];
    for (let hour = 6; hour <= 22; hour++) {
        for (let minute = 0; minute < 60; minute += 30) {
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

            // Check for bookings first (higher priority)
            const slotBookings = bookings.filter(b => {
                if (b.date !== dateStr) return false;
                
                // Handle both time formats - extract time part if it's a full datetime
                let bookingStartTime = b.start_time;
                let bookingEndTime = b.end_time;
                
                // If the time contains date info, extract just the time part
                if (bookingStartTime.includes('-') || bookingStartTime.length > 8) {
                    const startDate = new Date(bookingStartTime);
                    bookingStartTime = startDate.toTimeString().substring(0, 5);
                } else {
                    bookingStartTime = bookingStartTime.substring(0, 5);
                }
                
                if (bookingEndTime.includes('-') || bookingEndTime.length > 8) {
                    const endDate = new Date(bookingEndTime);
                    bookingEndTime = endDate.toTimeString().substring(0, 5);
                } else {
                    bookingEndTime = bookingEndTime.substring(0, 5);
                }
                
                // Check if current timeSlot overlaps with booking
                const slotTime = timeSlot;
                const slotEndTime = (() => {
                    const [hours, minutes] = timeSlot.split(':').map(Number);
                    const totalMinutes = hours * 60 + minutes + 30; // 30-minute slots
                    const endHours = Math.floor(totalMinutes / 60);
                    const endMinutes = totalMinutes % 60;
                    return `${endHours.toString().padStart(2, '0')}:${endMinutes.toString().padStart(2, '0')}`;
                })();
                
                // Check overlap: slot overlaps with booking if slot starts before booking ends and slot ends after booking starts
                return slotTime < bookingEndTime && slotEndTime > bookingStartTime;
            });

            // Get all availabilities for this slot
            const slotAvailabilities = availabilities.filter(a => {
                if (a.date !== dateStr) return false;
                
                const availabilityStartTime = a.start_time.substring(0, 5);
                const availabilityEndTime = a.end_time.substring(0, 5);
                
                return timeSlot >= availabilityStartTime && timeSlot < availabilityEndTime;
            });

            let cellClass = 'unavailable';
            let content = '<i class="bx bx-plus text-muted add-availability-btn"></i>';

            if (slotBookings.length > 0) {
                // BOOKED SLOT - Show as red with customer name
                cellClass = 'booked';
                const booking = slotBookings[0];
                content = `
                    <div class="slot-content-wrapper">
                        <div class="slot-status">BOOKED</div>
                        <div class="slot-customer">${booking.user ? booking.user.name : 'Unknown'}</div>
                    </div>
                `;
                cell.style.cursor = 'pointer';
                cell.dataset.bookingId = booking.id;
                cell.addEventListener('click', function() {
                    viewBookingDetails(booking.id);
                });
            } else if (slotAvailabilities.length > 0) {
                // AVAILABLE SLOT - Show as green with instructor name
                cellClass = 'available';
                const availability = slotAvailabilities[0];
                content = `
                    <div class="slot-content-wrapper">
                        <div class="slot-status">AVAILABLE</div>
                        <div class="slot-instructor">${availability.instructor_name || 'Instructor'}</div>
                    </div>
                `;
                cell.style.cursor = 'pointer';
                cell.addEventListener('click', function() {
                    openBookingModal(dateStr, timeSlot, availability.instructor_id);
                });
            } else {
                // UNAVAILABLE SLOT - Show add button
                content = '<i class="bx bx-plus add-availability-btn"></i>';
                cell.style.cursor = 'pointer';
                cell.addEventListener('click', function() {
                    openAvailabilityModal(dateStr, timeSlot);
                });
            }

            cell.className += ` ${cellClass}`;
            cell.innerHTML = content;
            
            row.appendChild(cell);
        });

        calendarBody.appendChild(row);
    });
}

    // Updated openBookingModal function
    function openBookingModal(date, time, instructorId = '') {
        const selectedDateTime = new Date(date + 'T' + time);
        const now = new Date();
        
        if (selectedDateTime < now) {
            alert('Cannot create booking for past dates and times.');
            return;
        }
        
        // Load form data first, then show modal
        loadBookingFormData().then(() => {
            document.getElementById('booking-form').reset();
            document.getElementById('booking-date').value = date;
            document.getElementById('booking-time').value = time;
            
            if (instructorId) {
                document.getElementById('booking-instructor').value = instructorId;
            }
            
            document.getElementById('time-slot-info').style.display = 'none';
            document.getElementById('booking-summary').style.display = 'none';
            document.getElementById('new-customer-fields').style.display = 'none';
            document.getElementById('create-new-customer').checked = false;
            
            createBookingModal.show();
        });
    }

    function openAvailabilityModal(date, time) {
        const selectedDateTime = new Date(date + 'T' + time);
        const now = new Date();
        
        if (selectedDateTime < now) {
            alert('Cannot add availability for past dates and times.');
            return;
        }
        
        document.getElementById('availability-form').reset();
        document.getElementById('availability-date').value = date;
        document.getElementById('availability-time').value = time;
        document.getElementById('availability-duration').value = '30';
        document.getElementById('availability-visibility').value = 'public';
        
        const suburbsSelect = document.getElementById('availability-suburbs');
        Array.from(suburbsSelect.options).forEach(option => {
            option.selected = option.value === 'all';
        });
        
        availabilityModal.show();
    }

    function selectDate(dateStr) {
        const date = new Date(dateStr);
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        document.getElementById('selected-date').textContent = date.toLocaleDateString('en-US', options);
        
        currentWeekStart = getMonday(date);
        generateWeeklyCalendar();
        
        document.querySelectorAll('.calendar-day-btn.active').forEach(btn => {
            btn.classList.remove('active', 'btn-primary');
        });
        
        const selectedCell = document.querySelector(`[data-date="${dateStr}"] .calendar-day-btn`);
        if (selectedCell) {
            selectedCell.classList.add('active', 'btn-primary');
        }
    }

    function viewBookingDetails(bookingId) {
        selectedBookingId = bookingId;
        bookingModal.show();
        
        document.getElementById('booking-details-content').innerHTML = `
            <div class="d-flex justify-content-center py-3">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        `;
        
        fetch(`/admin/calendar/booking/${bookingId}`)
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
                            <i class="bx bx-envelope me-2 text-muted"></i>
                            <span>${booking.user.email}</span>
                        </div>
                        <div class="mb-2 d-flex align-items-center">
                            <i class="bx bx-phone me-2 text-muted"></i>
                            <span>${booking.user.phone || 'N/A'}</span>
                        </div>
                        <div class="mb-2 d-flex align-items-center">
                            <i class="bx bx-user-circle me-2 text-muted"></i>
                            <span>Instructor: ${booking.instructor.name}</span>
                        </div>
                        <div class="mb-2 d-flex align-items-center">
                            <i class="bx bx-purchase-tag me-2 text-muted"></i>
                            <span>${booking.service.name} (${booking.service.duration} mins - $${booking.service.price})</span>
                        </div>
                        ${booking.suburb ? `
                        <div class="mb-2 d-flex align-items-center">
                            <i class="bx bx-map me-2 text-muted"></i>
                            <span>${booking.suburb.name}</span>
                        </div>
                        ` : ''}
                        ${booking.pickup_location ? `
                        <div class="mb-2 d-flex align-items-center">
                            <i class="bx bx-map-pin me-2 text-muted"></i>
                            <span>${booking.pickup_location}</span>
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

    function updateBookingStatus(bookingId, status) {
        if (!confirm(`Are you sure you want to change the booking status to "${status}"?`)) {
            return;
        }
        
        fetch(`/admin/calendar/bookings/${bookingId}/status`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ status: status })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Booking status updated successfully!');
                bookingModal.hide();
                fetchCalendarData();
            } else {
                alert('Error updating booking status: ' + (data.error || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while updating the booking status');
        });
    }

    function handleAvailabilitySubmit(e) {
        e.preventDefault();
        
        if (!validateForm(this)) {
            alert('Please fill in all required fields.');
            return;
        }
        
        const formData = new FormData(this);
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
                window.location.reload();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while adding availability');
        })
        .finally(() => {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        });
    }

    function handleBookingSubmit(e) {
        e.preventDefault();
        
        if (!validateForm(this)) {
            alert('Please fill in all required fields.');
            return;
        }
        
        const formData = new FormData(this);
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
                fetchCalendarData();
            } else {
                window.location.reload();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while creating the booking');
        })
        .finally(() => {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        });
    }

    function validateForm(form) {
        const requiredFields = form.querySelectorAll('[required]');
        let isValid = true;
        
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                field.classList.add('is-invalid');
                isValid = false;
            } else {
                field.classList.remove('is-invalid');
            }
        });
        
        return isValid;
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
        return date.toLocaleDateString('en-US', { 
            weekday: 'long', 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric' 
        });
    }

    function getStatusBadgeClass(status) {
        switch(status.toLowerCase()) {
            case 'confirmed':
                return 'bg-success';
            case 'cancelled':
                return 'bg-danger';
            case 'completed':
                return 'bg-info';
            case 'pending':
                return 'bg-warning';
            case 'no-show':
                return 'bg-secondary';
            default:
                return 'bg-secondary';
        }
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

    .time-label {
        font-size: 0.7rem;
        font-weight: 600;
        color: #6c757d;
        line-height: 1;
    }

    .slot-cell {
        width: 80px;
        height: 30px;
        padding: 2px;
        text-align: center;
        vertical-align: middle;
        border: 2px solid #dee2e6;
        position: relative;
        transition: all 0.3s ease;
        background: #f8f9fa;
        cursor: pointer;
    }

    /* Available slots - similar to booking page */
    .slot-cell.available {
        background: #d1edff !important;
        border-color: #198754 !important;
        cursor: pointer;
    }

    .slot-cell.available:hover {
        background: #28a745 !important;
        border-color: #1e7e34 !important;
        transform: scale(1.05);
        box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
    }

    /* Booked slots - red styling like booking page */
    .slot-cell.booked {
        background: #f8d7da !important;
        border-color: #dc3545 !important;
        cursor: pointer;
    }

    .slot-cell.booked:hover {
        background: #dc3545 !important;
        border-color: #c82333 !important;
        transform: scale(1.05);
        box-shadow: 0 4px 12px rgba(220, 53, 69, 0.4);
    }

    /* Unavailable slots */
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
        font-size: 9px;
        font-weight: 600;
        line-height: 1;
    }

    .slot-content-wrapper {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
        line-height: 1;
    }

    .slot-status {
        font-size: 8px;
        font-weight: bold;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 100%;
    }

    .slot-customer, .slot-instructor {
        font-size: 7px;
        font-weight: normal;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 100%;
        margin-top: 1px;
    }

    /* Available text styling */
    .slot-cell.available .slot-status {
        color: #198754 !important;
    }

    .slot-cell.available .slot-instructor {
        color: #198754 !important;
    }

    .slot-cell.available:hover .slot-status,
    .slot-cell.available:hover .slot-instructor {
        color: white !important;
        text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
    }

    /* Booked text styling */
    .slot-cell.booked .slot-status {
        color: #dc3545 !important;
    }

    .slot-cell.booked .slot-customer {
        color: #dc3545 !important;
    }

    .slot-cell.booked:hover .slot-status,
    .slot-cell.booked:hover .slot-customer {
        color: white !important;
        text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
    }

    .add-availability-btn {
        transition: all 0.2s ease;
        font-size: 14px;
        opacity: 0.6;
        color: #6c757d;
    }

    .slot-cell:hover .add-availability-btn {
        opacity: 1;
        color: #495057;
    }

    /* Enhanced borders for better visibility */
    .slot-cell.available::before {
        content: '';
        position: absolute;
        top: 1px;
        right: 1px;
        width: 6px;
        height: 6px;
        background: #28a745;
        border-radius: 50%;
        opacity: 0.8;
    }

    .slot-cell.booked::before {
        content: '';
        position: absolute;
        top: 1px;
        right: 1px;
        width: 6px;
        height: 6px;
        background: #dc3545;
        border-radius: 50%;
    }

    .modal-header {
        background: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
    }

    .modal-footer {
        background: #f8f9fa;
        border-top: 1px solid #dee2e6;
    }

    .modal-content {
        border-radius: 0.5rem;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }

    .modal-title {
        font-weight: 600;
        color: #495057;
    }

    .form-label.fw-semibold {
        color: #495057;
        margin-bottom: 0.5rem;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        transform: translateY(-1px);
    }

    .is-invalid {
        border-color: #dc3545;
    }

    .invalid-feedback {
        display: block;
        color: #dc3545;
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }

    .card.bg-light {
        border: 1px solid #e3f2fd;
        background: linear-gradient(135deg, #f8f9ff 0%, #e3f2fd 100%) !important;
    }

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

    .btn-success {
        background-color: #28a745;
        border-color: #28a745;
    }

    .btn-success:hover {
        background-color: #218838;
        border-color: #1e7e34;
    }

    .text-danger {
        color: #dc3545 !important;
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
        
        .slot-status {
            font-size: 7px;
        }
        
        .slot-customer, .slot-instructor {
            font-size: 6px;
        }
    }

    @media (max-width: 991.98px) {
        .weekly-calendar-table {
            font-size: 0.8rem;
        }
        
        .slot-cell {
            width: 60px;
            height: 25px;
        }
        
        .time-cell {
            width: 65px;
            min-width: 65px;
        }
        
        .slot-status {
            font-size: 6px;
        }
        
        .slot-customer, .slot-instructor {
            font-size: 5px;
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
            height: 22px;
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
        
        .slot-status {
            font-size: 5px;
        }
        
        .slot-customer, .slot-instructor {
            font-size: 4px;
        }
    }

    @media (max-width: 575.98px) {
        .slot-cell {
            width: 40px;
            height: 20px;
        }
        
        .slot-content {
            font-size: 6px;
        }
        
        .slot-status {
            font-size: 4px;
        }
        
        .slot-customer, .slot-instructor {
            font-size: 3px;
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
</style>
@endsection
