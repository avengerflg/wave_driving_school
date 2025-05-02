@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Edit Suburb</h1>
        <a href="{{ route('admin.suburbs.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Back to Suburbs
        </a>
    </div>
    
    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.suburbs.update', $suburb) }}" method="POST">
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
                        <option value="">-- Select State --</option>
                        <option value="ACT" {{ old('state', $suburb->state) == 'ACT' ? 'selected' : '' }}>Australian Capital Territory</option>
                        <option value="NSW" {{ old('state', $suburb->state) == 'NSW' ? 'selected' : '' }}>New South Wales</option>
                        <option value="NT" {{ old('state', $suburb->state) == 'NT' ? 'selected' : '' }}>Northern Territory</option>
                        <option value="QLD" {{ old('state', $suburb->state) == 'QLD' ? 'selected' : '' }}>Queensland</option>
                        <option value="SA" {{ old('state', $suburb->state) == 'SA' ? 'selected' : '' }}>South Australia</option>
                        <option value="TAS" {{ old('state', $suburb->state) == 'TAS' ? 'selected' : '' }}>Tasmania</option>
                        <option value="VIC" {{ old('state', $suburb->state) == 'VIC' ? 'selected' : '' }}>Victoria</option>
                        <option value="WA" {{ old('state', $suburb->state) == 'WA' ? 'selected' : '' }}>Western Australia</option>
                    </select>
                    @error('state')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="postcode" class="form-label">Postcode</label>
                    <input type="text" class="form-control @error('postcode') is-invalid @enderror" id="postcode" name="postcode" value="{{ old('postcode', $suburb->postcode) }}" required>
                    @error('postcode')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="active" name="active" value="1" {{ old('active', $suburb->active) == '1' ? 'checked' : '' }}>
                        <label class="form-check-label" for="active">
                            Active
                        </label>
                    </div>
                </div>
                
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">Update Suburb</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
