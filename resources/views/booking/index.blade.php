@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-7">
            <div class="booking-steps">
                <!-- Steps Indicator -->
                <div class="step-indicator">
                    @php
                        $steps = [
                            ['icon' => 'fa-map-marker-alt', 'title' => 'Suburb'],
                            ['icon' => 'fa-user-tie', 'title' => 'Instructor'],
                            ['icon' => 'fa-calendar-alt', 'title' => 'Date & Time'],
                            ['icon' => 'fa-car', 'title' => 'Service'],
                            ['icon' => 'fa-clipboard-list', 'title' => 'Details'],
                            ['icon' => 'fa-credit-card', 'title' => 'Payment']
                        ];
                    @endphp
                    @foreach($steps as $i => $step)
                        <div class="step {{ $i === 0 ? 'active' : '' }}">
                            <div class="step-content">
                                <div class="step-number">
                                    <i class="fas {{ $step['icon'] }}"></i>
                                    <span class="step-count">{{ $i + 1 }}</span>
                                </div>
                                <div class="step-title">{{ $step['title'] }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Content -->
                <div class="booking-content">
                    <div class="content-wrapper">
                        <h1 class="booking-title">Book Your Driving Lesson</h1>
                        <p class="booking-description">
                            Start your journey with <span class="highlight">Wave Driving School</span>. 
                            Professional instructors, flexible scheduling, and competitive rates.
                        </p>
                        <a href="{{ route('booking.suburbs') }}" class="start-booking-btn">
                            <span>Start Booking</span>
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
/* Variables */
:root {
    --primary-color: #0d6efd;
    --primary-dark: #0b5ed7;
    --secondary-color: #6c757d;
    --background-color: #f8fafc;
    --border-color: #e9ecef;
    --white: #ffffff;
    --shadow-sm: 0 2px 4px rgba(0,0,0,0.05);
    --shadow-md: 0 4px 6px -1px rgba(0,0,0,0.1);
    --shadow-lg: 0 10px 15px -3px rgba(0,0,0,0.1);
    --transition: all 0.3s ease;
}

/* Booking Steps Container */
.booking-steps {
    background: var(--white);
    border-radius: 24px;
    padding: 2.5rem;
    box-shadow: var(--shadow-lg);
    position: relative;
    overflow: hidden;
}

/* Step Indicator */
.step-indicator {
    display: -webkit-box;
    display: -ms-flexbox;
    display: flex;
    -webkit-box-pack: justify;
    -ms-flex-pack: justify;
    justify-content: space-between;
    -webkit-box-align: center;
    -ms-flex-align: center;
    align-items: center;
    gap: 1rem;
    margin-bottom: 3rem;
    position: relative;
}

/* Step Styles */
.step {
    -webkit-box-flex: 1;
    -ms-flex: 1;
    flex: 1;
    position: relative;
}

.step-content {
    text-align: center;
    position: relative;
    z-index: 1;
}

.step-number {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: var(--background-color);
    color: var(--secondary-color);
    display: -webkit-box;
    display: -ms-flexbox;
    display: flex;
    -webkit-box-align: center;
    -ms-flex-align: center;
    align-items: center;
    -webkit-box-pack: center;
    -ms-flex-pack: center;
    justify-content: center;
    margin: 0 auto 0.75rem;
    position: relative;
    -webkit-transition: var(--transition);
    transition: var(--transition);
    border: 2px solid var(--border-color);
}

.step-number i {
    font-size: 1.2rem;
}

.step-count {
    position: absolute;
    top: -8px;
    right: -8px;
    background: var(--border-color);
    width: 24px;
    height: 24px;
    border-radius: 50%;
    font-size: 0.8rem;
    display: -webkit-box;
    display: -ms-flexbox;
    display: flex;
    -webkit-box-align: center;
    -ms-flex-align: center;
    align-items: center;
    -webkit-box-pack: center;
    -ms-flex-pack: center;
    justify-content: center;
    font-weight: 600;
    -webkit-transition: var(--transition);
    transition: var(--transition);
}

.step.active .step-number {
    background: var(--primary-color);
    color: var(--white);
    border-color: var(--primary-color);
    -webkit-transform: scale(1.1);
    transform: scale(1.1);
    box-shadow: 0 0 0 0.35rem rgba(13,110,253,.15);
}

.step.active .step-count {
    background: var(--primary-dark);
    color: var(--white);
}

.step-title {
    font-size: 0.95rem;
    font-weight: 600;
    color: var(--secondary-color);
    margin-top: 0.5rem;
    -webkit-transition: var(--transition);
    transition: var(--transition);
}

.step.active .step-title {
    color: var(--primary-color);
}

/* Progress Line */
.step:not(:last-child)::after {
    content: '';
    position: absolute;
    top: 24px;
    left: 50%;
    width: 100%;
    height: 2px;
    background: var(--border-color);
    z-index: 0;
    -webkit-transform: translateX(50%);
    transform: translateX(50%);
}

/* Booking Content */
.booking-content {
    text-align: center;
    padding: 2rem;
    background: linear-gradient(to bottom right, rgba(13,110,253,0.03), rgba(13,110,253,0.08));
    border-radius: 16px;
    margin-top: 1rem;
}

.content-wrapper {
    max-width: 480px;
    margin: 0 auto;
}

.booking-title {
    font-size: 2.5rem;
    font-weight: 800;
    color: var(--primary-color);
    margin-bottom: 1.5rem;
    background: linear-gradient(45deg, var(--primary-color), var(--primary-dark));
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
    line-height: 1.2;
}

.booking-description {
    font-size: 1.1rem;
    color: var(--secondary-color);
    margin-bottom: 2rem;
    line-height: 1.6;
}

.highlight {
    color: var(--primary-color);
    font-weight: 600;
}

/* Start Booking Button */
.start-booking-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
    padding: 1rem 2.5rem;
    font-size: 1.1rem;
    font-weight: 600;
    color: var(--white);
    background: linear-gradient(45deg, var(--primary-color), var(--primary-dark));
    border-radius: 100px;
    text-decoration: none;
    transition: var(--transition);
    box-shadow: 0 4px 16px rgba(13,110,253,0.2);
}

.start-booking-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(13,110,253,0.25);
    color: var(--white);
}

.start-booking-btn i {
    transition: transform 0.3s ease;
}

.start-booking-btn:hover i {
    transform: translateX(4px);
}

/* Responsive Design */
@media (max-width: 768px) {
    .booking-steps {
        padding: 1.5rem;
        border-radius: 16px;
    }

    .step-indicator {
        flex-direction: column;
        gap: 1.5rem;
    }

    .step:not(:last-child)::after {
        height: 100%;
        width: 2px;
        top: 50%;
        left: 24px;
        transform: translateY(50%);
    }

    .step-content {
        display: flex;
        align-items: center;
        text-align: left;
        gap: 1rem;
    }

    .step-number {
        margin: 0;
    }

    .step-title {
        margin-top: 0;
    }

    .booking-title {
        font-size: 2rem;
    }

    .booking-description {
        font-size: 1rem;
    }

    .start-booking-btn {
        width: 100%;
        padding: 0.875rem 1.5rem;
    }
}

/* Dark Mode Support */
@media (prefers-color-scheme: dark) {
    :root {
        --background-color: #1a1a1a;
        --border-color: #2d2d2d;
        --white: #242424;
    }

    .booking-steps {
        background: #242424;
    }

    .booking-content {
        background: linear-gradient(to bottom right, rgba(13,110,253,0.05), rgba(13,110,253,0.1));
    }

    .booking-description {
        color: #a0a0a0;
    }
}
</style>
@endpush
