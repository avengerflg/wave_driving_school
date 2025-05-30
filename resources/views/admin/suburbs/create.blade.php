
@extends('layouts.admin')

@section('title', 'Add New Suburb')

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">Admin / Suburbs /</span> Add New
        </h4>

        <div class="row">
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">New Suburb Details</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.suburbs.store') }}" method="POST">
                            @csrf

                            <div class="mb-3">
                                <label for="name" class="form-label">Suburb Name</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="state" class="form-label">State</label>
                                <select class="form-select @error('state') is-invalid @enderror" id="state" name="state" required>
                                    <option value="">Select a state</option>
                                    @foreach($states as $code => $name)
                                        <option value="{{ $code }}" {{ old('state') == $code ? 'selected' : '' }}>{{ $name }}</option>
                                    @endforeach
                                </select>
                                @error('state')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="postcode" class="form-label">Postcode</label>
                                <input type="text" class="form-control @error('postcode') is-invalid @enderror" id="postcode" name="postcode" value="{{ old('postcode') }}" maxlength="4" required>
                                @error('postcode')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="active" name="active" value="1" {{ old('active', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="active">
                                        Active
                                    </label>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end">
                                <a href="{{ route('admin.suburbs.index') }}" class="btn btn-outline-secondary me-2">Cancel</a>
                                <button type="submit" class="btn btn-primary">Save Suburb</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info mb-0">
                            <h6 class="alert-heading fw-bold mb-1">About Suburbs</h6>
                            <p class="mb-0">Suburbs are used throughout the system for:</p>
                            <ul class="mb-0 ps-3 mt-2">
                                <li>Defining service areas for driving instructors</li>
                                <li>Location options for lesson bookings</li>
                                <li>Address information for users</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection