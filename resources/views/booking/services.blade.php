@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="booking-steps">
                <!-- Steps Indicator -->
                <div class="step-indicator">
                    <div class="step completed">
                        <div class="step-number"><i class="fas fa-check"></i></div>
                        <div class="step-title">Suburb</div>
                    </div>
                    <div class="step completed">
                        <div class="step-number"><i class="fas fa-check"></i></div>
                        <div class="step-title">Instructor</div>
                    </div>
                    <div class="step completed">
                        <div class="step-number"><i class="fas fa-check"></i></div>
                        <div class="step-title">Date & Time</div>
                    </div>
                    <div class="step active">
                        <div class="step-number">4</div>
                        <div class="step-title">Service</div>
                    </div>
                    <div class="step">
                        <div class="step-number">5</div>
                        <div class="step-title">Details</div>
                    </div>
                    <div class="step">
                        <div class="step-number">6</div>
                        <div class="step-title">Payment</div>
                    </div>
                </div>

                <!-- Content -->
                <div class="booking-content">
                    <h1>Select Your Service</h1>
                    <p>Choose the type of driving lesson that best suits your needs</p>

                    <form action="{{ route('booking.service.select') }}" method="POST">
                        @csrf
                        
                        <!-- Services Grid -->
                        <div class="services-grid">
                            @foreach($services as $service)
                                <div class="service-card">
                                    <input type="radio" 
                                           name="service_id" 
                                           id="service{{ $service->id }}" 
                                           value="{{ $service->id }}" 
                                           class="service-radio"
                                           {{ old('service_id') == $service->id ? 'checked' : '' }}
                                           required>
                                    <label for="service{{ $service->id }}" class="service-label">
                                        <div class="service-content">
                                            <div class="service-header">
                                                <h3>{{ $service->name }}</h3>
                                                <span class="price-badge">${{ number_format($service->price, 2) }}</span>
                                            </div>
                                            <p class="service-description">{{ $service->description }}</p>
                                            <div class="service-footer">
                                                <span class="duration">
                                                    <i class="fas fa-clock"></i>
                                                    {{ $service->duration }} minutes
                                                </span>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            @endforeach
                        </div>

                        <!-- Booking For Section -->
                        <div class="booking-for-section">
                            <h2>Who is this booking for?</h2>
                            <div class="booking-options">
                                <div class="booking-option">
                                    <input type="radio" 
                                           name="booking_for" 
                                           id="bookingForSelf" 
                                           value="self" 
                                           {{ old('booking_for', 'self') == 'self' ? 'checked' : '' }}>
                                    <label for="bookingForSelf">
                                        <i class="fas fa-user"></i>
                                        <span>Myself</span>
                                    </label>
                                </div>
                                <div class="booking-option">
                                    <input type="radio" 
                                           name="booking_for" 
                                           id="bookingForOther" 
                                           value="other"
                                           {{ old('booking_for') == 'other' ? 'checked' : '' }}>
                                    <label for="bookingForOther">
                                        <i class="fas fa-users"></i>
                                        <span>Someone Else</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Navigation Buttons -->
                        <div class="navigation-buttons">
                            <a href="{{ route('booking.availability', session('booking.instructor_id')) }}" 
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

                    <!-- Error Messages -->
                    @if ($errors->any())
                        <div class="alert alert-danger mt-3">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
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

.services-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.service-card {
    position: relative;
}

.service-radio {
    position: absolute;
    opacity: 0;
    cursor: pointer;
}

.service-label {
    display: block;
    cursor: pointer;
    border-radius: 15px;
    overflow: hidden;
    transition: all 0.3s ease;
    background: #ffffff;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
}

.service-radio:checked + .service-label {
    box-shadow: 0 8px 15px rgba(13, 110, 253, 0.15);
    border: 2px solid #0d6efd;
    transform: translateY(-2px);
}

.service-content {
    padding: 1.5rem;
}

.service-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.service-header h3 {
    margin: 0;
    font-size: 1.25rem;
    font-weight: 600;
    color: #2d3748;
}

.price-badge {
    background: #0d6efd;
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 50px;
    font-weight: 600;
}

.service-description {
    color: #6b7280;
    font-size: 0.95rem;
    line-height: 1.5;
    margin-bottom: 1rem;
}

.service-footer {
    display: flex;
    align-items: center;
}

.duration {
    color: #6b7280;
    font-size: 0.9rem;
}

.duration i {
    margin-right: 0.5rem;
    color: #0d6efd;
}

.booking-for-section {
    background: #f8fafc;
    border-radius: 15px;
    padding: 2rem;
    margin-bottom: 2rem;
}

.booking-for-section h2 {
    font-size: 1.5rem;
    color: #2d3748;
    margin-bottom: 1rem;
}

.booking-options {
    display: flex;
    gap: 2rem;
}

.booking-option {
    flex: 1;
}

.booking-option input[type="radio"] {
    display: none;
}

.booking-option label {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 1.5rem;
    background: white;
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.booking-option input[type="radio"]:checked + label {
    background: #0d6efd;
    color: white;
}

.booking-option i {
    font-size: 1.5rem;
    margin-bottom: 0.5rem;
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
    .services-grid {
        grid-template-columns: 1fr;
    }

    .booking-options {
        flex-direction: column;
        gap: 1rem;
    }

    .navigation-buttons {
        flex-direction: column;
        gap: 1rem;
    }

    .back-btn, .continue-btn {
        width: 100%;
    }
}
</style>
@endsection