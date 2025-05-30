@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="login-wrapper">
                <div class="booking-card shadow-xl">
                    <!-- Content -->
                    <div class="booking-content">
                        <div class="text-center mb-4">
                            <div class="icon-wrapper mb-4">
                                <div class="icon-circle">
                                    <i class="fas fa-user"></i>
                                </div>
                            </div>
                            <h1 class="fw-bold mb-2 text-primary">{{ __('Welcome Back') }}</h1>
                            <p class="text-secondary mb-4">
                                {{ __('Sign in to your account to continue') }}
                            </p>
                        </div>

                        @if (session('status'))
                            <div class="alert alert-success rounded-4 mb-4" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('login') }}" class="text-start">
                            @csrf

                            <!-- Email Address -->
                            <div class="form-group mb-4">
                                <label for="email" class="form-label">{{ __('Email') }}</label>
                                <div class="input-group input-group-custom">
                                    <span class="input-icon">
                                        <i class="far fa-envelope"></i>
                                    </span>
                                    <input id="email" 
                                        type="email" 
                                        class="form-control @error('email') is-invalid @enderror" 
                                        name="email" 
                                        value="{{ old('email') }}" 
                                        required 
                                        autofocus 
                                        autocomplete="username">
                                </div>
                                @error('email')
                                    <div class="error-message">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Password -->
                            <div class="form-group mb-4">
                                <label for="password" class="form-label">{{ __('Password') }}</label>
                                <div class="input-group input-group-custom">
                                    <span class="input-icon">
                                        <i class="fas fa-lock"></i>
                                    </span>
                                    <input id="password" 
                                        type="password" 
                                        class="form-control @error('password') is-invalid @enderror" 
                                        name="password" 
                                        required 
                                        autocomplete="current-password">
                                </div>
                                @error('password')
                                    <div class="error-message">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Remember Me and Forgot Password -->
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div class="form-check custom-checkbox">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember_me">
                                    <label class="form-check-label" for="remember_me">
                                        {{ __('Remember me') }}
                                    </label>
                                </div>
                                @if (Route::has('password.request'))
                                    <a href="{{ route('password.request') }}" 
                                    class="text-primary fw-semibold text-decoration-none link-hover">
                                        {{ __('Forgot password?') }}
                                    </a>
                                @endif
                            </div>

                            <!-- Login Button -->
                            <div class="mb-4">
                                <button type="submit" class="btn btn-primary continue-btn">
                                    <span>{{ __('Sign in') }}</span>
                                    <i class="fas fa-arrow-right"></i>
                                </button>
                            </div>

                            <div class="divider-text mb-4">
                                <span>{{ __('or') }}</span>
                            </div>

                            <!-- Social Login -->
                            <div class="social-login mb-4 text-center">
                                <a href="#" class="btn-social-circle">
                                    <i class="fab fa-google"></i>
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Register Link Card -->
                <div class="register-card shadow-sm mt-4">
                    <p class="mb-0">
                        {{ __("Don't have an account?") }}
                        <a href="{{ route('register') }}" class="text-primary fw-semibold text-decoration-none ms-1 link-hover">
                            {{ __('Create an account') }}
                        </a>
                    </p>
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
    --primary-dark: #0b5ed7;
    --secondary: #6c757d;
    --success: #198754;
    --light: #f8fafc;
    --dark: #212529;
    --white: #ffffff;
    --border: #e2e8f0;
    --shadow-sm: 0 2px 4px rgba(0,0,0,0.05);
    --shadow-md: 0 4px 6px rgba(0,0,0,0.07);
    --shadow-lg: 0 10px 15px -3px rgba(0,0,0,0.1);
    --shadow-xl: 0 20px 25px -5px rgba(0,0,0,0.1), 0 10px 10px -5px rgba(0,0,0,0.04);
    --google: #DB4437;
    --input-bg: #f9fafb;
    --font-primary: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
}

body {
    font-family: var(--font-primary);
}

/* Login Wrapper */
.login-wrapper {
    max-width: 500px;
    margin: 0 auto;
}

/* Card Styles */
.booking-card {
    background: var(--white);
    border-radius: 16px;
    border: none;
    overflow: hidden;
    box-shadow: var(--shadow-xl);
}

.booking-content {
    padding: 3rem 2.5rem;
}

/* Icon Styling */
.icon-wrapper {
    position: relative;
    width: 100px;
    height: 100px;
    margin: 0 auto;
}

.icon-circle {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background-image: linear-gradient(135deg, #c3d7ff, var(--primary-light));
    border: 1px solid rgba(13, 110, 253, 0.1);
    color: var(--primary);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.8rem;
    position: relative;
    box-shadow: 0 12px 20px -10px rgba(13, 110, 253, 0.25);
    z-index: 1;
}

.icon-wrapper::after {
    content: '';
    position: absolute;
    width: 100px;
    height: 100px;
    border-radius: 50%;
    background-color: var(--primary-light);
    top: -10px;
    left: -10px;
    z-index: 0;
}

/* Typography */
h1 {
    font-size: 2rem;
    letter-spacing: -0.5px;
}

/* Form Styling */
.form-group {
    margin-bottom: 1.5rem;
}

.form-label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 600;
    font-size: 0.95rem;
    color: var(--dark);
}

.input-group-custom {
    position: relative;
    display: flex;
    align-items: center;
}

.input-icon {
    position: absolute;
    left: 16px;
    color: var(--secondary);
    z-index: 10;
}

.form-control {
    height: 56px;
    padding-left: 48px;
    border-radius: 12px;
    border: 2px solid var(--border);
    font-size: 1rem;
    font-weight: 500;
    transition: all 0.25s ease;
    background-color: var(--input-bg);
    width: 100%;
    box-shadow: none;
}

.form-control:focus {
    border-color: var(--primary);
    background-color: var(--white);
    box-shadow: 0 0 0 4px var(--primary-light);
}

.error-message {
    color: #dc3545;
    font-size: 0.875rem;
    margin-top: 0.5rem;
}

/* Checkbox Styling */
.custom-checkbox .form-check-input {
    width: 20px;
    height: 20px;
    margin-top: 0.15rem;
    border: 2px solid var(--border);
    border-radius: 4px;
    cursor: pointer;
}

.custom-checkbox .form-check-label {
    padding-left: 0.5rem;
    color: var(--secondary);
    cursor: pointer;
}

.form-check-input:checked {
    background-color: var(--primary);
    border-color: var(--primary);
}

/* Divider */
.divider-text {
    display: flex;
    align-items: center;
    color: var(--secondary);
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 1px;
    font-weight: 600;
}

.divider-text span {
    padding: 0 1rem;
}

.divider-text::before,
.divider-text::after {
    content: '';
    flex: 1;
    border-bottom: 1px solid var(--border);
}

/* Social Button */
.btn-social-circle {
    display: inline-flex;
    justify-content: center;
    align-items: center;
    width: 56px;
    height: 56px;
    border-radius: 50%;
    font-size: 1.5rem;
    background-color: var(--white);
    color: var(--google);
    border: 2px solid var(--border);
    transition: all 0.3s ease;
    box-shadow: var(--shadow-sm);
    text-decoration: none;
}

.btn-social-circle:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 16px rgba(219, 68, 55, 0.25);
    border-color: var(--google);
    background-color: rgba(219, 68, 55, 0.05);
}

/* Button Styles */
.continue-btn {
    width: 100%;
    padding: 1rem;
    font-size: 1rem;
    font-weight: 600;
    border-radius: 12px;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
    background-image: linear-gradient(135deg, var(--primary), var(--primary-dark));
    border: none;
    box-shadow: 0 4px 12px rgba(13, 110, 253, 0.25);
    letter-spacing: 0.5px;
}

.continue-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(13, 110, 253, 0.3);
}

.continue-btn i {
    transition: transform 0.3s ease;
}

.continue-btn:hover i {
    transform: translateX(4px);
}

/* Register Card */
.register-card {
    background: var(--white);
    border-radius: 12px;
    padding: 1.2rem;
    text-align: center;
    font-size: 1rem;
}

.link-hover {
    position: relative;
    transition: all 0.3s ease;
}

.link-hover:after {
    content: '';
    position: absolute;
    width: 0;
    height: 2px;
    background: var(--primary);
    left: 0;
    bottom: -2px;
    transition: width 0.3s ease;
}

.link-hover:hover:after {
    width: 100%;
}

/* Responsive Design */
@media (max-width: 768px) {
    .booking-content {
        padding: 2rem 1.5rem;
    }
    
    .form-control {
        height: 52px;
        font-size: 1rem;
    }
    
    .continue-btn {
        padding: 0.85rem;
    }
    
    .icon-circle {
        width: 70px;
        height: 70px;
    }
    
    .icon-wrapper::after {
        width: 90px;
        height: 90px;
    }
    
    .btn-social-circle {
        width: 50px;
        height: 50px;
        font-size: 1.3rem;
    }
}

/* Dark Mode Support */
@media (prefers-color-scheme: dark) {
    :root {
        --light: #1a1a1a;
        --white: #242424;
        --border: #404040;
        --secondary: #a0aec0;
        --dark: #e0e0e0;
        --input-bg: #1a1a1a;
    }
    
    .booking-card, .register-card {
        background: var(--white);
    }
    
    .icon-circle {
        background-image: linear-gradient(135deg, rgba(13, 110, 253, 0.15), rgba(13, 110, 253, 0.25));
        border: 1px solid rgba(13, 110, 253, 0.3);
    }
    
    .form-control {
        background-color: var(--input-bg);
        color: #e0e0e0;
        border-color: #404040;
    }
    
    .form-control:focus {
        background-color: #252525;
    }
}
</style>
@endpush
@endsection