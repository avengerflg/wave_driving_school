@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="booking-card shadow-lg rounded-4 p-0 bg-white">
                <!-- Content -->
                <div class="booking-content text-center py-4">
                    <div class="icon-circle mb-4">
                        <i class="fas fa-user"></i>
                    </div>
                    <h1 class="display-6 fw-bold mb-3 text-primary">{{ __('Login') }}</h1>
                    <p class="lead text-secondary mb-4">
                        {{ __('Welcome back! Please enter your details') }}
                    </p>

                    @if (session('status'))
                        <div class="alert alert-success rounded-4 mb-4" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}" class="text-start">
                        @csrf

                        <!-- Email Address -->
                        <div class="mb-4">
                            <label for="email" class="form-label">{{ __('Email') }}</label>
                            <input id="email" 
                                   type="email" 
                                   class="form-control form-control-lg @error('email') is-invalid @enderror" 
                                   name="email" 
                                   value="{{ old('email') }}" 
                                   required 
                                   autofocus 
                                   autocomplete="username">
                            @error('email')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div class="mb-4">
                            <label for="password" class="form-label">{{ __('Password') }}</label>
                            <input id="password" 
                                   type="password" 
                                   class="form-control form-control-lg @error('password') is-invalid @enderror" 
                                   name="password" 
                                   required 
                                   autocomplete="current-password">
                            @error('password')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Remember Me -->
                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember_me">
                                <label class="form-check-label" for="remember_me">
                                    {{ __('Remember me') }}
                                </label>
                            </div>
                        </div>

                        <!-- Buttons and Links -->
                        <div class="d-grid gap-3">
                            <button type="submit" class="btn btn-primary btn-lg continue-btn">
                                {{ __('Log in') }}
                                <i class="fas fa-arrow-right ms-2"></i>
                            </button>

                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" 
                                   class="btn btn-outline-primary btn-lg">
                                    {{ __('Forgot your password?') }}
                                </a>
                            @endif
                        </div>

                        <div class="text-center mt-4">
                            <p class="text-secondary mb-0">
                                {{ __("Don't have an account?") }}
                                <a href="{{ route('register') }}" class="text-primary text-decoration-none ms-1">
                                    {{ __('Register now') }}
                                </a>
                            </p>
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

.form-control {
    padding: 1rem;
    border-radius: 1rem;
    border: 2px solid var(--border);
    transition: all 0.3s ease;
    font-size: 1rem;
}

.form-control:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 0.25rem var(--primary-light);
}

.form-control.is-invalid {
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

.btn-outline-primary {
    width: 100%;
    padding: 1rem 2rem;
    font-size: 1.1rem;
    font-weight: 600;
    border-radius: 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    background: transparent;
    border: 2px solid var(--primary);
    color: var(--primary);
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(13, 110, 253, 0.2);
}

/* Alert Styles */
.alert {
    padding: 1rem;
    border: none;
}

/* Responsive Design */
@media (max-width: 768px) {
    .booking-content {
        padding: 1.5rem;
    }
    
    .continue-btn,
    .btn-outline-primary {
        padding: 0.75rem 1.5rem;
        font-size: 1rem;
    }
    
    .form-control {
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
    
    .form-control {
        background-color: var(--light);
        color: var(--white);
    }
    
    .form-control:focus {
        background-color: var(--light);
        color: var(--white);
    }
}
</style>
@endpush
@endsection
