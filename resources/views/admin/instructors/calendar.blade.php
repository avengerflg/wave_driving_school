
@extends('layouts.tailwind-admin')

@section('title', 'Instructor Calendar')

@section('content')
<div class="max-w-7xl mx-auto py-8 px-4">
    <div class="flex flex-col md:flex-row gap-8">
        <!-- Sidebar Calendar -->
        <div class="w-full md:w-80 bg-white rounded-xl shadow p-4">
            <div class="flex items-center justify-between mb-4">
                <button id="prev-month" class="p-2 rounded hover:bg-gray-100"><i class="fas fa-chevron-left"></i></button>
                <h2 id="current-month" class="font-bold text-lg">{{ now()->format('F Y') }}</h2>
                <button id="next-month" class="p-2 rounded hover:bg-gray-100"><i class="fas fa-chevron-right"></i></button>
            </div>
            <table class="w-full text-center">
                <thead>
                    <tr class="text-xs text-gray-500">
                        <th>Sun</th><th>Mon</th><th>Tue</th><th>Wed</th><th>Thu</th><th>Fri</th><th>Sat</th>
                    </tr>
                </thead>
                <tbody id="calendar-body"></tbody>
            </table>
            <div class="mt-4 space-y-2 text-sm">
                <div class="flex items-center gap-2">
                    <div class="w-4 h-4 rounded bg-green-100"></div> Available
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-4 h-4 rounded bg-blue-100"></div> Booked
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-4 h-4 rounded bg-gradient-to-br from-green-100 via-green-100 to-blue-100"></div> Partially Booked
                </div>
            </div>
        </div>
        <!-- Main Content -->
        <div class="flex-1 flex flex-col gap-6">
            <!-- Header -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2">
                <h1 class="text-2xl font-bold flex items-center gap-2 text-blue-700">
                    <i class="fas fa-calendar-alt"></i>
                    {{ $instructor->name }}'s Calendar
                </h1>
                <div class="flex gap-2">
                    <a href="{{ route('admin.instructors.show', $instructor) }}" class="inline-flex items-center px-3 py-1.5 rounded bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm">
                        <i class="fas fa-arrow-left mr-1"></i> Back
                    </a>
                    <a href="{{ route('admin.instructors.availability', $instructor) }}" class="inline-flex items-center px-3 py-1.5 rounded bg-blue-600 hover:bg-blue-700 text-white text-sm">
                        <i class="fas fa-edit mr-1"></i> Manage Availability
                    </a>
                </div>
            </div>
            <!-- Selected Date & Timeline -->
            <div class="bg-white rounded-xl shadow p-6">
                <h2 id="selected-date" class="text-lg font-semibold mb-4">{{ now()->format('l, F j, Y') }}</h2>
                <div id="timeline" class="space-y-3">
                    <div class="text-center text-gray-400 py-8">
                        <i class="fas fa-calendar-day fa-3x mb-2"></i>
                        <p>Select a date to view availability and bookings</p>
                    </div>
                </div>
            </div>
            <!-- Quick Actions -->
            <div class="bg-white rounded-xl shadow p-6 flex flex-col md:flex-row md:items-center gap-3">
                <button id="add-availability-btn" class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700 flex items-center gap-2">
                    <i class="fas fa-plus"></i> Add Availability
                </button>
                <button id="create-booking-btn" disabled class="px-4 py-2 rounded bg-green-600 text-white hover:bg-green-700 flex items-center gap-2 opacity-50 cursor-not-allowed">
                    <i class="fas fa-calendar-plus"></i> Create Booking
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Add Availability Modal -->
<div id="add-availability-modal" class="fixed inset-0 z-50 items-center justify-center bg-black/40 hidden">
    <div class="bg-white w-full max-w-md rounded-xl shadow-lg p-6 m-auto mt-20">
        <h3 class="text-lg font-bold mb-4">Add Availability</h3>
        <form action="{{ route('admin.instructors.availability.store', $instructor) }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="availability-date" class="block text-sm font-medium mb-1">Date</label>
                <input type="date" id="availability-date" name="date" required min="{{ now()->format('Y-m-d') }}" class="w-full border rounded px-3 py-2">
            </div>
            <div class="mb-3">
                <label for="start_time" class="block text-sm font-medium mb-1">Start Time</label>
    <select id="start_time" name="start_time" required class="w-full border rounded px-3 py-2">
        @foreach([
            '06:30','06:45','07:00','07:15','07:30','07:45','08:00','08:15','08:30','08:45',
            '09:00','09:15','09:30','09:45','10:00','10:15','10:30','10:45','11:00','11:15',
            '11:30','11:45','12:00','12:15','12:30','12:45','13:00','13:15','13:30','13:45',
            '14:00','14:15','14:30','14:45','15:00','15:15','15:30','15:45','16:00','16:15',
            '16:30','16:45','17:00','17:15','17:30','17:45'
        ] as $time)
            <option value="{{ $time }}">{{ \Carbon\Carbon::createFromFormat('H:i', $time)->format('g:iA') }}</option>
        @endforeach
    </select>
            </div>
            <div class="mb-3">
                <label for="end_time" class="block text-sm font-medium mb-1">End Time</label>
    <select id="end_time" name="end_time" required class="w-full border rounded px-3 py-2">
        @foreach([
            '06:30','06:45','07:00','07:15','07:30','07:45','08:00','08:15','08:30','08:45',
            '09:00','09:15','09:30','09:45','10:00','10:15','10:30','10:45','11:00','11:15',
            '11:30','11:45','12:00','12:15','12:30','12:45','13:00','13:15','13:30','13:45',
            '14:00','14:15','14:30','14:45','15:00','15:15','15:30','15:45','16:00','16:15',
            '16:30','16:45','17:00','17:15','17:30','17:45'
        ] as $time)
            <option value="{{ $time }}">{{ \Carbon\Carbon::createFromFormat('H:i', $time)->format('g:iA') }}</option>
        @endforeach
    </select>
            </div>
            <div class="mb-3 flex items-center gap-2">
                <input type="checkbox" id="is_recurring" name="is_recurring" value="1" class="rounded">
                <label for="is_recurring" class="text-sm">Make this recurring</label>
            </div>
            <div id="recurring-options" style="display:none;">
                <div class="mb-3">
                    <label for="recur_until" class="block text-sm font-medium mb-1">Recur Until</label>
                    <input type="date" id="recur_until" name="recur_until" class="w-full border rounded px-3 py-2">
                </div>
                <div class="mb-3">
                    <label class="block text-sm font-medium mb-1">Days of Week</label>
                    <div class="flex flex-wrap gap-3">
                        @foreach(['Sun','Mon','Tue','Wed','Thu','Fri','Sat'] as $i => $day)
                        <label class="flex items-center gap-1">
                            <input type="checkbox" id="day-{{ $i }}" name="days_of_week[]" value="{{ $i }}" class="rounded">
                            <span class="text-xs">{{ $day }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
            </div>
            <input type="hidden" name="current_month" id="current_month_input" value="{{ now()->format('Y-m') }}">
            <div class="flex justify-end gap-2 mt-4">
                <button type="button" id="close-availability-modal" class="px-3 py-1.5 rounded bg-gray-200 hover:bg-gray-300">Cancel</button>
                <button type="submit" class="px-3 py-1.5 rounded bg-blue-600 text-white hover:bg-blue-700">Add Availability</button>
            </div>
        </form>
    </div>
</div>

<!-- Create Booking Modal -->
<div id="create-booking-modal" class="fixed inset-0 z-50 items-center justify-center bg-black/40 hidden">
    <div class="bg-white w-full max-w-md rounded-xl shadow-lg p-6 m-auto mt-20">
        <h3 class="text-lg font-bold mb-4">Create Booking</h3>
        <form id="booking-form" action="{{ route('admin.bookings.store') }}" method="POST">
            @csrf
            <input type="hidden" name="instructor_id" value="{{ $instructor->instructor->id }}">
            <input type="hidden" name="date" id="booking-date">
            <input type="hidden" name="start_time" id="booking-start-time">
            <input type="hidden" name="end_time" id="booking-end-time">
            <input type="hidden" name="suburb_id" value="{{ $instructor->suburb_id ?? $instructor->instructor->suburbs[0] ?? 1 }}">
            <input type="hidden" name="status" value="confirmed">
            <div class="mb-3">
                <label for="user_id" class="block text-sm font-medium mb-1">Select Student</label>
                <select id="user_id" name="user_id" required class="w-full border rounded px-3 py-2">
                    <option value="">-- Select Student --</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="service_id" class="block text-sm font-medium mb-1">Select Service</label>
                <select id="service_id" name="service_id" required class="w-full border rounded px-3 py-2">
                    <option value="">-- Select Service --</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="notes" class="block text-sm font-medium mb-1">Notes</label>
                <textarea id="notes" name="notes" rows="3" class="w-full border rounded px-3 py-2"></textarea>
            </div>
            <div class="flex justify-end gap-2 mt-4">
                <button type="button" id="close-booking-modal" class="px-3 py-1.5 rounded bg-gray-200 hover:bg-gray-300">Cancel</button>
                <button type="submit" class="px-3 py-1.5 rounded bg-blue-600 text-white hover:bg-blue-700">Create Booking</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const instructorId = {{ $instructor->instructor->id }};
    const availabilities = {!! json_encode($availabilities->map(function($a) {
        return [
            'id' => $a->id,
            'date' => $a->date->format('Y-m-d'),
            'start_time' => $a->start_time,
            'end_time' => $a->end_time,
        ];
    })->toArray()) !!};
    const bookings = {!! json_encode($bookings->map(function($b) {
        return [
            'id' => $b->id,
            'date' => $b->date->format('Y-m-d'),
            'start_time' => $b->start_time,
            'end_time' => $b->end_time,
            'status' => $b->status,
            'user' => ['id' => $b->user->id, 'name' => $b->user->name],
            'service' => ['id' => $b->service->id, 'name' => $b->service->name]
        ];
    })->toArray()) !!};

    let currentDate = new Date();
    let selectedDate = new Date();
    let currentMonth = currentDate.getMonth();
    let currentYear = currentDate.getFullYear();
    let selectedAvailabilityId = null;

    // Helper function to convert HH:MM time to minutes since midnight for easier comparison
    function timeToMinutes(timeStr) {
        const [hours, minutes] = timeStr.split(':').map(Number);
        return hours * 60 + minutes;
    }

    document.addEventListener('DOMContentLoaded', function() {
        generateCalendar(currentMonth, currentYear);

        // Modal handling without Alpine.js
        const addAvailabilityBtn = document.getElementById('add-availability-btn');
        const addAvailabilityModal = document.getElementById('add-availability-modal');
        const closeAvailabilityModal = document.getElementById('close-availability-modal');
        
        const createBookingBtn = document.getElementById('create-booking-btn');
        const createBookingModal = document.getElementById('create-booking-modal');
        const closeBookingModal = document.getElementById('close-booking-modal');
        
        addAvailabilityBtn.addEventListener('click', function() {
            addAvailabilityModal.classList.remove('hidden');
            addAvailabilityModal.classList.add('flex');
        });
        
        closeAvailabilityModal.addEventListener('click', function() {
            addAvailabilityModal.classList.add('hidden');
            addAvailabilityModal.classList.remove('flex');
        });
        
        createBookingBtn.addEventListener('click', function() {
            if (!selectedAvailabilityId) {
                alert('Please select an availability slot first');
                return;
            }
            
            // Find the selected availability
            const selectedAvailability = availabilities.find(a => a.id === selectedAvailabilityId);
            if (!selectedAvailability) {
                console.error('Could not find availability with ID:', selectedAvailabilityId);
                alert('Error: Selected availability not found');
                return;
            }
            
            // Check again if this slot is already booked (just to be safe)
            const selectedDate = selectedAvailability.date;
            const dayBookings = bookings.filter(b => b.date === selectedDate);
            
            const isBooked = dayBookings.some(booking => {
                const availStart = timeToMinutes(selectedAvailability.start_time);
                const availEnd = timeToMinutes(selectedAvailability.end_time);
                const bookStart = timeToMinutes(booking.start_time);
                const bookEnd = timeToMinutes(booking.end_time);
                
                return (bookStart < availEnd && bookEnd > availStart);
            });
            
            if (isBooked) {
                alert('This time slot is already booked. Please select a different time.');
                return;
            }
            
            // Debug info
            console.log('Creating booking with availability:', selectedAvailability);
            
            // Populate the booking form with the selected availability details
            document.getElementById('booking-date').value = selectedAvailability.date;
            document.getElementById('booking-start-time').value = selectedAvailability.start_time;
            document.getElementById('booking-end-time').value = selectedAvailability.end_time;
            
            // Fetch students and services for the dropdowns
            fetchStudents();
            fetchServices();
            
            // Show the booking modal
            createBookingModal.classList.remove('hidden');
            createBookingModal.classList.add('flex');
        });
        
        closeBookingModal.addEventListener('click', function() {
            createBookingModal.classList.add('hidden');
            createBookingModal.classList.remove('flex');
        });

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

        document.getElementById('is_recurring').addEventListener('change', function() {
            document.getElementById('recurring-options').style.display = this.checked ? 'block' : 'none';
        });
        
        document.getElementById('availability-date').addEventListener('change', function() {
            const date = new Date(this.value);
            const dayOfWeek = date.getDay();
            document.getElementById(`day-${dayOfWeek}`).checked = true;
        });

        // Close modals when clicking outside of modal content
        window.addEventListener('click', function(event) {
            if (event.target === addAvailabilityModal) {
                addAvailabilityModal.classList.add('hidden');
                addAvailabilityModal.classList.remove('flex');
            }
            if (event.target === createBookingModal) {
                createBookingModal.classList.add('hidden');
                createBookingModal.classList.remove('flex');
            }
        });
    });

    function generateCalendar(month, year) {
        const firstDay = new Date(year, month, 1);
        const lastDay = new Date(year, month + 1, 0);
        const daysInMonth = lastDay.getDate();
        const startingDay = firstDay.getDay();
        const monthNames = ["January", "February", "March", "April", "May", "June",
            "July", "August", "September", "October", "November", "December"];
        document.getElementById('current-month').textContent = `${monthNames[month]} ${year}`;
        document.getElementById('current_month_input').value = `${year}-${(month + 1).toString().padStart(2, '0')}`;
        const calendarBody = document.getElementById('calendar-body');
        calendarBody.innerHTML = '';
        let date = 1;
        for (let i = 0; i < 6; i++) {
            const row = document.createElement('tr');
            for (let j = 0; j < 7; j++) {
                const cell = document.createElement('td');
                const cellDiv = document.createElement('div');
                cellDiv.classList.add('rounded', 'w-8', 'h-8', 'flex', 'items-center', 'justify-center', 'mx-auto', 'cursor-pointer', 'transition');
                if (i === 0 && j < startingDay) {
                    const prevMonth = month === 0 ? 11 : month - 1;
                    const prevYear = month === 0 ? year - 1 : year;
                    const prevMonthDays = new Date(prevYear, prevMonth + 1, 0).getDate();
                    const prevDate = prevMonthDays - (startingDay - j - 1);
                    cellDiv.textContent = prevDate;
                    cellDiv.classList.add('text-gray-300');
                    cellDiv.dataset.date = `${prevYear}-${(prevMonth + 1).toString().padStart(2, '0')}-${prevDate.toString().padStart(2, '0')}`;
                } else if (date > daysInMonth) {
                    const nextDate = date - daysInMonth;
                    const nextMonth = month === 11 ? 0 : month + 1;
                    const nextYear = month === 11 ? year + 1 : year;
                    cellDiv.textContent = nextDate;
                    cellDiv.classList.add('text-gray-300');
                    cellDiv.dataset.date = `${nextYear}-${(nextMonth + 1).toString().padStart(2, '0')}-${nextDate.toString().padStart(2, '0')}`;
                    date++;
                } else {
                    cellDiv.textContent = date;
                    const dateStr = `${year}-${(month + 1).toString().padStart(2, '0')}-${date.toString().padStart(2, '0')}`;
                    cellDiv.dataset.date = dateStr;
                    const hasAvailability = availabilities.some(a => a.date === dateStr);
                    const hasBookings = bookings.some(b => b.date === dateStr);
                    if (hasAvailability && hasBookings) cellDiv.classList.add('bg-gradient-to-br', 'from-green-100', 'via-green-100', 'to-blue-100', 'text-blue-700', 'font-bold');
                    else if (hasAvailability) cellDiv.classList.add('bg-green-100', 'text-green-700', 'font-semibold');
                    else if (hasBookings) cellDiv.classList.add('bg-blue-100', 'text-blue-700', 'font-semibold');
                    const today = new Date();
                    if (date === today.getDate() && month === today.getMonth() && year === today.getFullYear()) {
                        cellDiv.classList.add('border-2', 'border-blue-700');
                    }
                    cellDiv.addEventListener('click', function() {
                        document.querySelectorAll('#calendar-body .bg-orange-200').forEach(el => el.classList.remove('bg-orange-200'));
                        this.classList.add('bg-orange-200');
                        const selectedDate = new Date(dateStr);
                        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
                        document.getElementById('selected-date').textContent = selectedDate.toLocaleDateString('en-US', options);
                        loadTimeline(dateStr);
                        selectedAvailabilityId = null;
                        document.getElementById('create-booking-btn').disabled = true;
                        document.getElementById('create-booking-btn').classList.add('opacity-50', 'cursor-not-allowed');
                    });
                    date++;
                }
                cell.appendChild(cellDiv);
                row.appendChild(cell);
            }
            calendarBody.appendChild(row);
            if (date > daysInMonth) break;
        }
    }

    function loadTimeline(dateStr) {
        const timeline = document.getElementById('timeline');
        timeline.innerHTML = '';
        const dayAvailabilities = availabilities.filter(a => a.date === dateStr);
        const dayBookings = bookings.filter(b => b.date === dateStr);
        
        if (dayAvailabilities.length === 0 && dayBookings.length === 0) {
            timeline.innerHTML = `
                <div class="text-center text-gray-400 py-8">
                    <i class="fas fa-calendar-times fa-3x mb-2"></i>
                    <p>No availability or bookings for this date</p>
                    <button type="button" class="mt-4 px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700" onclick="addAvailabilityToDate('${dateStr}')">
                        Add Availability
                    </button>
                </div>
            `;
            document.getElementById('availability-date').value = dateStr;
            return;
        }
        
        const allSlots = [];
        dayAvailabilities.forEach(availability => {
            allSlots.push({ type: 'availability', data: availability, startTime: availability.start_time, endTime: availability.end_time });
        });
        dayBookings.forEach(booking => {
            allSlots.push({ type: 'booking', data: booking, startTime: booking.start_time, endTime: booking.end_time });
        });
        
        allSlots.sort((a, b) => a.startTime.localeCompare(b.startTime));
        allSlots.forEach(slot => {
            const timeSlot = document.createElement('div');
            timeSlot.classList.add('flex', 'items-center', 'justify-between', 'rounded', 'px-4', 'py-3', 'shadow-sm', 'mb-2');
            
            if (slot.type === 'availability') {
                // Add data-availability-id attribute to help with selection
                timeSlot.setAttribute('data-availability-id', slot.data.id);
                timeSlot.classList.add('availability-slot');
                
                // Check if this availability slot overlaps with any booking
                const isBooked = dayBookings.some(booking => {
                    // Convert times to comparable values (minutes since midnight)
                    const availStart = timeToMinutes(slot.startTime);
                    const availEnd = timeToMinutes(slot.endTime);
                    const bookStart = timeToMinutes(booking.start_time);
                    const bookEnd = timeToMinutes(booking.end_time);
                    
                    // Check for any overlap between booking and availability
                    return (bookStart < availEnd && bookEnd > availStart);
                });
                
                if (isBooked) {
                    timeSlot.classList.add('bg-red-50', 'border-l-4', 'border-red-400');
                    timeSlot.innerHTML = `
                        <div>
                            <strong>${formatTime(slot.startTime)} - ${formatTime(slot.endTime)}</strong>
                            <div class="text-xs text-red-600">This slot is already booked</div>
                        </div>
                        <button type="button" class="ml-2 px-2 py-1 rounded bg-red-100 text-red-700 hover:bg-red-200 text-xs" onclick="deleteAvailability(${slot.data.id}, '${dateStr}')">
                            <i class="fas fa-trash"></i>
                        </button>
                    `;
                } else {
                    timeSlot.classList.add('bg-green-50', 'border-l-4', 'border-green-400');
                    timeSlot.innerHTML = `
                        <div>
                            <strong>${formatTime(slot.startTime)} - ${formatTime(slot.endTime)}</strong>
                            <div class="text-xs text-green-700">Available</div>
                        </div>
                        <div class="flex gap-2">
                            <button type="button" class="px-2 py-1 rounded bg-green-100 text-green-700 hover:bg-green-200 text-xs" onclick="selectAvailability(${slot.data.id})">
                                <i class="fas fa-calendar-plus"></i> Book
                            </button>
                            <button type="button" class="px-2 py-1 rounded bg-red-100 text-red-700 hover:bg-red-200 text-xs" onclick="deleteAvailability(${slot.data.id}, '${dateStr}')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    `;
                }
            } else {
                // This is an existing booking - always display in red
                timeSlot.classList.add('bg-red-50', 'border-l-4', 'border-red-400');
                timeSlot.innerHTML = `
                    <div>
                        <strong>${formatTime(slot.startTime)} - ${formatTime(slot.endTime)}</strong>
                        <div class="text-xs text-red-700">Booked: ${slot.data.service.name}</div>
                        <div class="text-xs text-gray-500">Student: ${slot.data.user.name}</div>
                    </div>
                    <a href="/admin/bookings/${slot.data.id}" class="px-2 py-1 rounded bg-red-100 text-red-700 hover:bg-red-200 text-xs">
                        <i class="fas fa-eye"></i> View
                    </a>
                `;
            }
            timeline.appendChild(timeSlot);
        });
        document.getElementById('availability-date').value = dateStr;
    }

    function addAvailabilityToDate(dateStr) {
        document.getElementById('availability-date').value = dateStr;
        const addAvailabilityModal = document.getElementById('add-availability-modal');
        addAvailabilityModal.classList.remove('hidden');
        addAvailabilityModal.classList.add('flex');
    }

    function formatTime(time) {
        const [hours, minutes] = time.split(':');
        const hour = parseInt(hours);
        const ampm = hour >= 12 ? 'PM' : 'AM';
        const hour12 = hour % 12 || 12;
        return `${hour12}:${minutes} ${ampm}`;
    }

    function selectAvailability(availabilityId) {
        // Find the selected availability
        const selectedAvailability = availabilities.find(a => a.id === availabilityId);
        if (!selectedAvailability) {
            console.error('Could not find availability with ID:', availabilityId);
            return;
        }
        
        // Check if this slot is already booked
        const selectedDate = selectedAvailability.date;
        const dayBookings = bookings.filter(b => b.date === selectedDate);
        
        const isBooked = dayBookings.some(booking => {
            const availStart = timeToMinutes(selectedAvailability.start_time);
            const availEnd = timeToMinutes(selectedAvailability.end_time);
            const bookStart = timeToMinutes(booking.start_time);
            const bookEnd = timeToMinutes(booking.end_time);
            
            return (bookStart < availEnd && bookEnd > availStart);
        });
        
        if (isBooked) {
            alert('This time slot is already booked. Please select a different time.');
            return;
        }
        
        // Set the selected availability ID
        selectedAvailabilityId = availabilityId;
        
        // Add visual indication of selected availability
        document.querySelectorAll('.availability-slot').forEach(el => {
            el.classList.remove('ring-2', 'ring-green-500');
        });
        
        const selectedElement = document.querySelector(`[data-availability-id="${availabilityId}"]`);
        if (selectedElement) {
            selectedElement.classList.add('ring-2', 'ring-green-500');
        }
        
        // Enable the booking button
        const createBookingBtn = document.getElementById('create-booking-btn');
        createBookingBtn.disabled = false;
        createBookingBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        
        // Log the selection for debugging
        console.log('Selected availability ID:', availabilityId);
        console.log('Selected availability object:', selectedAvailability);
    }

    function deleteAvailability(availabilityId, dateStr) {
        if (confirm('Are you sure you want to delete this availability slot?')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/instructors/{{ $instructor->id }}/availability/${availabilityId}`;
            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'DELETE';
            form.appendChild(methodInput);
            const tokenInput = document.createElement('input');
            tokenInput.type = 'hidden';
            tokenInput.name = '_token';
            tokenInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            form.appendChild(tokenInput);
            const monthInput = document.createElement('input');
            monthInput.type = 'hidden';
            monthInput.name = 'current_month';
            monthInput.value = document.getElementById('current_month_input').value;
            form.appendChild(monthInput);
            document.body.appendChild(form);
            form.submit();
        }
    }

    function fetchStudents() {
        // Add a loading indicator
        const select = document.getElementById('user_id');
        select.innerHTML = '<option value="">Loading students...</option>';
        select.disabled = true;
        
        fetch('/admin/api/students')
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Received students data:', data); // Debug logging
                
                select.innerHTML = '<option value="">-- Select Student --</option>';
                select.disabled = false;
                
                if (Array.isArray(data) && data.length > 0) {
                    data.forEach(student => {
                        const option = document.createElement('option');
                        option.value = student.id;
                        option.textContent = student.name;
                        select.appendChild(option);
                    });
                } else {
                    select.innerHTML = '<option value="">No students found</option>';
                    console.warn('No student data received or empty array');
                }
            })
            .catch(error => {
                console.error('Error fetching students:', error);
                select.innerHTML = '<option value="">Error loading students</option>';
                select.disabled = true;
            });
    }

    function fetchServices() {
        // Add a loading indicator
        const select = document.getElementById('service_id');
        select.innerHTML = '<option value="">Loading services...</option>';
        select.disabled = true;
        
        fetch('/admin/api/services')
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Received services data:', data); // Debug logging
                
                select.innerHTML = '<option value="">-- Select Service --</option>';
                select.disabled = false;
                
                if (Array.isArray(data) && data.length > 0) {
                    data.forEach(service => {
                        const option = document.createElement('option');
                        option.value = service.id;
                        option.textContent = `${service.name} ($${service.price})`;
                        select.appendChild(option);
                    });
                } else {
                    select.innerHTML = '<option value="">No services found</option>';
                    console.warn('No service data received or empty array');
                }
            })
            .catch(error => {
                console.error('Error fetching services:', error);
                select.innerHTML = '<option value="">Error loading services</option>';
                select.disabled = true;
            });
    }
</script>
@endpush