@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="booking-card shadow-lg rounded-4 p-0 bg-white">
                <!-- Content -->
                <div class="booking-content text-center py-4">
                    <div class="icon-circle mb-4">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <h1 class="display-6 fw-bold mb-3 text-primary">{{ __('Register as Student') }}</h1>
                    <p class="lead text-secondary mb-4">
                        {{ __('Create your account to start booking driving lessons') }}
                    </p>

                    <form method="POST" action="{{ route('register') }}" class="text-start">
                        @csrf

                        <div class="row g-4">
                            <!-- Name -->
                            <div class="col-md-6">
                                <label for="name" class="form-label">{{ __('Name') }}</label>
                                <input id="name" type="text" 
                                       class="form-control form-control-lg @error('name') is-invalid @enderror" 
                                       name="name" value="{{ old('name') }}" 
                                       required autofocus>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div class="col-md-6">
                                <label for="email" class="form-label">{{ __('Email') }}</label>
                                <input id="email" type="email" 
                                       class="form-control form-control-lg @error('email') is-invalid @enderror" 
                                       name="email" value="{{ old('email') }}" 
                                       required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Phone -->
                            <div class="col-md-6">
                                <label for="phone" class="form-label">{{ __('Phone') }}</label>
                                <input id="phone" type="text" 
                                       class="form-control form-control-lg @error('phone') is-invalid @enderror" 
                                       name="phone" value="{{ old('phone') }}" 
                                       required>
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Address -->
                            <div class="col-md-6">
                                <label for="address" class="form-label">{{ __('Address') }}</label>
                                <input id="address" type="text" 
                                       class="form-control form-control-lg @error('address') is-invalid @enderror" 
                                       name="address" value="{{ old('address') }}" 
                                       required>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Suburb Dropdown -->
                            <div class="col-12">
                                <label for="suburb_id" class="form-label">{{ __('Suburb') }}</label>
                                <select id="suburb_id" name="suburb_id" 
                                        class="form-select form-select-lg @error('suburb_id') is-invalid @enderror" 
                                        required>
                                    <option value="">Select a suburb</option>
                                    @foreach($suburbs as $suburb)
                                        <option value="{{ $suburb->id }}" 
                                                {{ old('suburb_id') == $suburb->id ? 'selected' : '' }}>
                                            {{ $suburb->name }}, {{ $suburb->state }} {{ $suburb->postcode }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('suburb_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Password -->
                            <div class="col-md-6">
                                <label for="password" class="form-label">{{ __('Password') }}</label>
                                <input id="password" type="password" 
                                       class="form-control form-control-lg @error('password') is-invalid @enderror" 
                                       name="password" required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Confirm Password -->
                            <div class="col-md-6">
                                <label for="password_confirmation" class="form-label">{{ __('Confirm Password') }}</label>
                                <input id="password_confirmation" type="password" 
                                       class="form-control form-control-lg" 
                                       name="password_confirmation" required>
                            </div>

                            <!-- Terms and Conditions -->
                            <div class="col-12">
                                <div class="form-check">
                                    <input type="checkbox" 
                                           class="form-check-input @error('terms') is-invalid @enderror" 
                                           id="terms" name="terms" required>
                                    <label class="form-check-label" for="terms">
                                        {{ __('I agree to the') }} 
                                        <a href="/" class="text-primary text-decoration-none">{{ __('Terms of Service') }}</a>
                                    </label>
                                    @error('terms')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Buttons and Links -->
                        <div class="d-grid gap-3 mt-4">
                            <button type="submit" class="btn btn-primary btn-lg continue-btn">
                                {{ __('Register') }}
                                <i class="fas fa-arrow-right ms-2"></i>
                            </button>

                            <div class="text-center">
                                <p class="text-secondary mb-2">
                                    {{ __('Already have an account?') }}
                                    <a href="{{ route('login') }}" class="text-primary text-decoration-none ms-1">
                                        {{ __('Login here') }}
                                    </a>
                                </p>
                                <p class="text-secondary mb-0">
                                    {{ __('Want to teach?') }}
                                    <a href="/" class="text-primary text-decoration-none ms-1">
                                        {{ __('Register as Instructor') }}
                                    </a>
                                </p>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
:root {
    --primary: #0d6efd;
    --primary-light: rgba(13, 110, 253, 0.1);
    --secondary: #6c757d;
    --success: #198754;
    --light: #f8fafc;
    --white: #ffffff;
    --border: #e2e8f0;
}

.booking-card {
    background: var(--white);
    border-radius: 1.5rem;
    transition: all 0.3s ease;
}

.booking-content {
    padding: 2rem;
}

.icon-circle {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: var(--primary-light);
    color: var(--primary);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
    font-size: 2rem;
}

/* Form Styles */
.form-label {
    font-weight: 500;
    color: var(--secondary);
    margin-bottom: 0.5rem;
}

.form-control, .form-select {
    padding: 1rem;
    border-radius: 1rem;
    border: 2px solid var(--border);
    transition: all 0.3s ease;
    font-size: 1rem;
}

.form-control:focus, .form-select:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 0.25rem var(--primary-light);
}

.form-control.is-invalid, .form-select.is-invalid {
    border-color: #dc3545;
    background-image: none;
}

.form-check-input {
    width: 1.2em;
    height: 1.2em;
    margin-top: 0.2em;
    border-radius: 0.25em;
    border: 2px solid var(--border);
}

.form-check-input:checked {
    background-color: var(--primary);
    border-color: var(--primary);
}

.form-check-label {
    color: var(--secondary);
    margin-left: 0.5rem;
}

/* Button Styles */
.continue-btn {
    width: 100%;
    padding: 1rem 2rem;
    font-size: 1.1rem;
    font-weight: 600;
    border-radius: 1rem;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(13, 110, 253, 0.2);
}

/* Responsive Design */
@media (max-width: 768px) {
    .booking-content {
        padding: 1.5rem;
    }
    
    .continue-btn {
        padding: 0.75rem 1.5rem;
        font-size: 1rem;
    }
    
    .form-control, .form-select {
        padding: 0.75rem;
    }
}

/* Dark Mode Support */
@media (prefers-color-scheme: dark) {
    :root {
        --light: #1a1a1a;
        --white: #242424;
        --border: #404040;
        --secondary: #a0aec0;
    }
    
    .booking-card {
        background: var(--white);
    }
    
    .form-control, .form-select {
        background-color: var(--light);
        color: var(--white);
    }
    
    .form-control:focus, .form-select:focus {
        background-color: var(--light);
        color: var(--white);
    }
}
</style>
@endpush
@endsection
