@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="booking-steps">
                <!-- Steps Indicator -->
                <div class="step-indicator">
                    <div class="step completed">
                        <div class="step-number">
                            <i class="fas fa-check"></i>
                            <span class="step-count">1</span>
                        </div>
                        <div class="step-title">Suburb</div>
                    </div>
                    <div class="step completed">
                        <div class="step-number">
                            <i class="fas fa-check"></i>
                            <span class="step-count">2</span>
                        </div>
                        <div class="step-title">Instructor</div>
                    </div>
                    <div class="step completed">
                        <div class="step-number">
                            <i class="fas fa-check"></i>
                            <span class="step-count">3</span>
                        </div>
                        <div class="step-title">Date & Time</div>
                    </div>
                    <div class="step completed">
                        <div class="step-number">
                            <i class="fas fa-check"></i>
                            <span class="step-count">4</span>
                        </div>
                        <div class="step-title">Service</div>
                    </div>
                    <div class="step active">
                        <div class="step-number">
                            5
                            <span class="step-count">5</span>
                        </div>
                        <div class="step-title">Details</div>
                    </div>
                    <div class="step">
                        <div class="step-number">
                            6
                            <span class="step-count">6</span>
                        </div>
                        <div class="step-title">Payment</div>
                    </div>
                </div>

                <!-- Content -->
                <div class="booking-content">
                    <h1>Booking Details</h1>
                    <p>Please provide the necessary information for your booking</p>

                    <form action="{{ route('booking.details.save') }}" method="POST">
                        @csrf
                        
                        @if(session('booking.booking_for') === 'other')
                            <div class="details-section">
                                <div class="section-header">
                                    <i class="fas fa-user-friends"></i>
                                    <h2>Student Information</h2>
                                </div>
                                
                                <div class="form-group">
                                    <label for="other_name">Full Name</label>
                                    <input type="text" 
                                           class="form-control @error('other_name') is-invalid @enderror" 
                                           id="other_name" 
                                           name="other_name" 
                                           value="{{ old('other_name', session('booking.other_name')) }}" 
                                           required>
                                    @error('other_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="form-group">
                                    <label for="other_email">Email Address</label>
                                    <input type="email" 
                                           class="form-control @error('other_email') is-invalid @enderror" 
                                           id="other_email" 
                                           name="other_email" 
                                           value="{{ old('other_email', session('booking.other_email')) }}" 
                                           required>
                                    @error('other_email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="form-group">
                                    <label for="other_phone">Phone Number</label>
                                    <input type="tel" 
                                           class="form-control @error('other_phone') is-invalid @enderror" 
                                           id="other_phone" 
                                           name="other_phone" 
                                           value="{{ old('other_phone', session('booking.other_phone')) }}" 
                                           required>
                                    @error('other_phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        @endif
                        
                        <div class="details-section">
                            <div class="section-header">
                                <i class="fas fa-map-marker-alt"></i>
                                <h2>Pickup Location</h2>
                            </div>
                            
                            <div class="form-group">
                                <label for="address">Full Address</label>
                                <input type="text" 
                                       class="form-control @error('address') is-invalid @enderror" 
                                       id="address" 
                                       name="address" 
                                       value="{{ old('address', session('booking.address')) }}" 
                                       placeholder="Enter the pickup address" 
                                       required>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    <i class="fas fa-info-circle"></i>
                                    This is where the instructor will meet you for your lesson.
                                </small>
                            </div>
                        </div>

                        <div class="navigation-buttons">
                            <a href="{{ route('booking.services') }}" 
                               class="btn btn-outline-primary btn-lg back-btn">
                                <i class="fas fa-arrow-left me-2"></i>
                                Back
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg continue-btn">
                                Continue
                                <i class="fas fa-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Step Indicator Styles */
.step-indicator {
    display: flex;
    justify-content: space-between;
    margin-bottom: 2rem;
    position: relative;
    padding: 0 1rem;
}

.step-indicator::before {
    content: '';
    position: absolute;
    top: 20px;
    left: 0;
    right: 0;
    height: 2px;
    background: #e2e8f0;
    z-index: 1;
}

.step {
    position: relative;
    z-index: 2;
    text-align: center;
    flex: 1;
}

.step-number {
    width: 40px;
    height: 40px;
    background: white;
    border: 2px solid #e2e8f0;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 0.5rem;
    font-weight: 600;
    color: #64748b;
    position: relative;
}

.step-count {
    position: absolute;
    top: -8px;
    right: -8px;
    background: rgba(13, 110, 253, 0.1);
    color: #0d6efd;
    width: 22px;
    height: 22px;
    border-radius: 50%;
    font-size: 0.8rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    border: 2px solid white;
    z-index: 2;
}

.step.completed .step-number {
    background: #0d6efd;
    border-color: #0d6efd;
    color: white;
}

.step.completed .step-count {
    background: #0d6efd;
    color: white;
}

.step.active .step-number {
    border-color: #0d6efd;
    color: #0d6efd;
}

.step.active .step-count {
    background: #0d6efd;
    color: white;
}

.step-title {
    font-size: 0.875rem;
    color: #64748b;
}

.step.active .step-title,
.step.completed .step-title {
    color: #0d6efd;
    font-weight: 600;
}

/* Existing styles */
.booking-content {
    max-width: 900px;
    margin: 0 auto;
    padding: 20px;
}

.booking-content h1 {
    font-size: 2rem;
    color: #2d3748;
    margin-bottom: 0.5rem;
}

.booking-content p {
    color: #6b7280;
    margin-bottom: 2rem;
}

.details-section {
    background: #ffffff;
    border-radius: 15px;
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
}

.section-header {
    display: flex;
    align-items: center;
    margin-bottom: 1.5rem;
}

.section-header i {
    font-size: 1.5rem;
    color: #0d6efd;
    margin-right: 1rem;
}

.section-header h2 {
    font-size: 1.5rem;
    color: #2d3748;
    margin: 0;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    color: #4a5568;
    font-weight: 500;
}

.form-control {
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 0.75rem 1rem;
    transition: all 0.3s ease;
}

.form-control:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.1);
}

.form-control.is-invalid {
    border-color: #dc3545;
}

.invalid-feedback {
    color: #dc3545;
    font-size: 0.875rem;
    margin-top: 0.25rem;
}

.form-text {
    color: #6b7280;
    font-size: 0.875rem;
    margin-top: 0.5rem;
}

.form-text i {
    margin-right: 0.5rem;
}

.navigation-buttons {
    display: flex;
    justify-content: space-between;
    margin-top: 2rem;
}

.back-btn, .continue-btn {
    padding: 0.75rem 2rem;
    font-weight: 600;
    border-radius: 50px;
    transition: all 0.3s ease;
}

.back-btn:hover, .continue-btn:hover {
    transform: translateY(-2px);
}

@media (max-width: 768px) {
    .details-section {
        padding: 1.5rem;
    }

    .navigation-buttons {
        flex-direction: column;
        gap: 1rem;
    }

    .back-btn, .continue-btn {
        width: 100%;
    }
    
    .step-indicator {
        overflow-x: auto;
        padding-bottom: 1rem;
    }
    
    .step {
        min-width: 120px;
    }
}
</style>
@endsection