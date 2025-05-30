
@extends('layouts.admin')

@section('title', 'Edit Suburb')

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">Admin / Suburbs /</span> Edit
        </h4>

        <div class="row">
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Edit Suburb Details</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.suburbs.update', $suburb->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label for="name" class="form-label">Suburb Name</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $suburb->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="state" class="form-label">State</label>
                                <select class="form-select @error('state') is-invalid @enderror" id="state" name="state" required>
                                    <option value="">Select a state</option>
                                    @foreach($states as $code => $name)
                                        <option value="{{ $code }}" {{ old('state', $suburb->state) == $code ? 'selected' : '' }}>{{ $name }}</option>
                                    @endforeach
                                </select>
                                @error('state')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="postcode" class="form-label">Postcode</label>
                                <input type="text" class="form-control @error('postcode') is-invalid @enderror" id="postcode" name="postcode" value="{{ old('postcode', $suburb->postcode) }}" maxlength="4" required>
                                @error('postcode')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="active" name="active" value="1" {{ old('active', $suburb->active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="active">
                                        Active
                                    </label>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end">
                                <a href="{{ route('admin.suburbs.show', $suburb->id) }}" class="btn btn-outline-secondary me-2">Cancel</a>
                                <button type="submit" class="btn btn-primary">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Usage Information</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-3">This suburb is currently:</p>
                        <div class="d-flex mb-3">
                            <div class="me-4">
                                <div class="d-flex align-items-center mb-1">
                                    <div class="badge bg-label-primary me-2">
                                        <i class="bx bx-user-check"></i>
                                    </div>
                                    <span>Used by {{ $suburb->instructors()->count() }} instructor(s)</span>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="badge bg-label-info me-2">
                                        <i class="bx bx-calendar"></i>
                                    </div>
                                    <span>Referenced in {{ $suburb->bookings()->count() }} booking(s)</span>
                                </div>
                            </div>
                        </div>
                        <div class="alert alert-warning mb-0">
                            <div class="d-flex">
                                <i class="bx bx-error-circle me-2 mt-1"></i>
                                <div>
                                    <h6 class="alert-heading fw-bold mb-1">Important Note</h6>
                                    <p class="mb-0">Changing this suburb's information will affect all associated instructors and bookings.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Danger Zone</h5>
                    </div>
                    <div class="card-body">
                        <div class="border-danger border rounded p-3">
                            <h6>Delete this suburb</h6>
                            <p class="mb-3">Once deleted, all associated data will be permanently removed. This action cannot be undone.</p>
                            <button onclick="confirmDelete()" class="btn btn-outline-danger">
                                <i class="bx bx-trash me-1"></i> Delete Suburb
                            </button>
                            <form id="delete-form" action="{{ route('admin.suburbs.destroy', $suburb->id) }}" method="POST" class="d-none">
                                @csrf
                                @method('DELETE')
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function confirmDelete() {
        if (confirm('Are you sure you want to delete this suburb? This action cannot be undone.')) {
            document.getElementById('delete-form').submit();
        }
    }
</script>
@endsection