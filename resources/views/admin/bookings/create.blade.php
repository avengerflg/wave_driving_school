
@extends('layouts.admin')

@section('title', 'Create Booking')

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Admin /</span> Create Booking</h4>

        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('admin.bookings.store') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="user_id" class="form-label">User</label>
                        <select id="user_id" name="user_id" class="form-control" required>
                            <option value="">Select User</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="instructor_id" class="form-label">Instructor</label>
                        <select id="instructor_id" name="instructor_id" class="form-control" required>
                            <option value="">Select Instructor</option>
                            @foreach ($instructors as $instructor)
                                <option value="{{ $instructor->id }}">{{ $instructor->user->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="service_id" class="form-label">Service</label>
                        <select id="service_id" name="service_id" class="form-control" required>
                            <option value="">Select Service</option>
                            @foreach ($services as $service)
                                <option value="{{ $service->id }}">{{ $service->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="suburb_id" class="form-label">Suburb</label>
                        <select id="suburb_id" name="suburb_id" class="form-control" required>
                            <option value="">Select Suburb</option>
                            @foreach ($suburbs as $suburb)
                                <option value="{{ $suburb->id }}">{{ $suburb->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="date" class="form-label">Date</label>
                        <input type="date" id="date" name="date" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="start_time" class="form-label">Start Time</label>
                        <input type="time" id="start_time" name="start_time" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="end_time" class="form-label">End Time</label>
                        <input type="time" id="end_time" name="end_time" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select id="status" name="status" class="form-control" required>
                            <option value="pending">Pending</option>
                            <option value="confirmed">Confirmed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary">Create Booking</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection