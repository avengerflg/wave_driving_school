@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-7">
            <div class="booking-card shadow-lg rounded-4 p-4 bg-white">
                <!-- Steps Indicator -->
                <div class="step-indicator mb-4">
                    @php $stepIndex = 1; @endphp
                    <div class="step completed">
                        <div class="step-number">
                            <i class="fas fa-map-marker-alt"></i>
                            <span class="step-count">{{ $stepIndex++ }}</span>
                        </div>
                        <div class="step-title">Suburb</div>
                    </div>
                    <div class="step active">
                        <div class="step-number">
                            <i class="fas fa-user-tie"></i>
                            <span class="step-count">{{ $stepIndex++ }}</span>
                        </div>
                        <div class="step-title">Instructor</div>
                    </div>
                    @foreach(['calendar-alt', 'car', 'clipboard-list', 'credit-card'] as $index => $icon)
                        <div class="step">
                            <div class="step-number">
                                <i class="fas fa-{{ $icon }}"></i>
                                <span class="step-count">{{ $stepIndex++ }}</span>
                            </div>
                            <div class="step-title">{{ ['Date & Time', 'Service', 'Details', 'Payment'][$index] }}</div>
                        </div>
                    @endforeach
                </div>

                <!-- Content -->
                <div class="booking-content text-center py-4">
                    <div class="icon-circle mb-4">
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <h1 class="display-6 fw-bold mb-3 text-primary">Choose Your Instructor</h1>
                    <p class="lead text-secondary mb-4">
                        Select an instructor in <span class="highlight">{{ $suburb->name }}</span> for your driving lesson
                    </p>

                    @if($instructors->isEmpty())
                        <div class="empty-state">
                            <div class="alert alert-info rounded-4 mb-4">
                                <i class="fas fa-info-circle mb-3"></i>
                                <h3>No Instructors Available</h3>
                                <p>Sorry, there are no instructors available in {{ $suburb->name }} at the moment.</p>
                                <p>Please select a different suburb.</p>
                            </div>
                            <a href="{{ route('booking.suburbs') }}" class="btn btn-primary btn-lg continue-btn">
                                <i class="fas fa-arrow-left me-2"></i>
                                Choose Another Suburb
                            </a>
                        </div>
                    @else
                        <form action="{{ route('booking.instructor.select') }}" method="POST">
                            @csrf
                            <div class="row g-4 mb-4">
                                @foreach($instructors as $instructor)
                                    <div class="col-md-6">
                                        <div class="instructor-option h-100">
                                            <input type="radio" 
                                                   name="instructor_id" 
                                                   id="instructor{{ $instructor->id }}" 
                                                   value="{{ $instructor->id }}" 
                                                   class="instructor-radio" 
                                                   required>
                                            <label for="instructor{{ $instructor->id }}" class="instructor-card">
                                                <div class="instructor-header">
                                                    <div class="instructor-avatar">
                                                        <span>{{ substr($instructor->user->name, 0, 2) }}</span>
                                                    </div>
                                                    <div class="instructor-info">
                                                        <h3>{{ $instructor->user->name }}</h3>
                                                        <div class="rating">
                                                            @for($i = 0; $i < 5; $i++)
                                                                <i class="fa{{ $i < $instructor->rating ? 's' : 'r' }} fa-star"></i>
                                                            @endfor
                                                            <span>({{ $instructor->reviews_count ?? 0 }})</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="instructor-bio">
                                                    <p>{{ Str::limit($instructor->bio, 100) }}</p>
                                                </div>
                                                <div class="selection-indicator">
                                                    <i class="fas fa-check-circle"></i>
                                                    Selected
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="d-grid gap-3">
                                <button type="submit" class="btn btn-primary btn-lg continue-btn">
                                    Continue to Select Date & Time
                                    <i class="fas fa-arrow-right ms-2"></i>
                                </button>
                                <a href="{{ route('booking.suburbs') }}" class="btn btn-outline-primary btn-lg">
                                    <i class="fas fa-arrow-left me-2"></i>
                                    Back to Suburb Selection
                                </a>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

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

/* Step Indicator Styles */
.step-indicator {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem;
    background: var(--light);
    border-radius: 1rem;
}
.step {
    flex: 1;
    text-align: center;
    position: relative;
    padding: 0.5rem 0;
}
.step-number {
    width: 35px;
    height: 35px;
    border-radius: 50%;
    background: #e9ecef;
    color: var(--secondary);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 0.5rem;
    font-size: 0.9rem;
    border: 2px solid transparent;
    transition: all 0.3s ease;
    position: relative;
}
.step-count {
    position: absolute;
    top: -8px;
    right: -8px;
    background: var(--primary-light);
    color: var(--primary);
    width: 22px;
    height: 22px;
    border-radius: 50%;
    font-size: 0.8rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    border: 2px solid var(--white);
    z-index: 2;
}
.step.active .step-number,
.step.completed .step-number {
    background: var(--primary);
    color: var(--white);
    border-color: var(--primary);
    box-shadow: 0 0 0 4px var(--primary-light);
}
.step.active .step-count,
.step.completed .step-count {
    background: var(--primary);
    color: var(--white);
}
.step-title {
    font-size: 0.85rem;
    font-weight: 500;
    color: var(--secondary);
}
.step.active .step-title,
.step.completed .step-title {
    color: var(--primary);
    font-weight: 600;
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

/* Instructor Card Styles */
.instructor-option {
    position: relative;
}

.instructor-radio {
    position: absolute;
    opacity: 0;
}

.instructor-card {
    display: block;
    height: 100%;
    padding: 1.5rem;
    border: 2px solid var(--border);
    border-radius: 1rem;
    background: var(--light);
    cursor: pointer;
    transition: border-color 0.3s, box-shadow 0.3s, background 0.3s;
    position: relative;
    box-shadow: 0 2px 8px rgba(13,110,253,0.04);
}

/* Visible custom radio button */
.instructor-card::before {
    content: '';
    position: absolute;
    top: 1.5rem;
    right: 1.5rem;
    width: 22px;
    height: 22px;
    border-radius: 50%;
    border: 2px solid var(--border);
    background: var(--white);
    transition: all 0.3s ease;
    z-index: 1;
}

.instructor-radio:checked + .instructor-card::before {
    border-color: var(--primary);
    background: var(--primary);
    box-shadow: 0 0 0 2px var(--primary-light);
}

.instructor-radio:checked + .instructor-card::after {
    content: '';
    position: absolute;
    top: 2.05rem;
    right: 2.05rem;
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: var(--white);
    z-index: 2;
}

.instructor-radio:checked + .instructor-card {
    border-color: var(--primary);
    border-width: 2px;
    box-shadow: 0 0 0 4px var(--primary-light), 0 4px 16px rgba(13,110,253,0.15);
    background: var(--white);
    z-index: 2;
}

.instructor-card:hover {
    border-color: var(--primary);
    box-shadow: 0 0 0 2px var(--primary-light);
}

.instructor-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1rem;
}

.instructor-avatar {
    width: 60px;
    height: 60px;
    flex-shrink: 0;
    border-radius: 50%;
    background: var(--primary);
    color: var(--white);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    font-weight: 600;
    transition: all 0.3s ease;
    box-shadow: 0 4px 8px rgba(13,110,253,0.2);
}

.instructor-radio:checked + .instructor-card .instructor-avatar {
    background: linear-gradient(135deg, var(--primary), #0a58ca);
    transform: scale(1.05);
    box-shadow: 0 6px 12px rgba(13,110,253,0.25);
}

.instructor-info {
    flex-grow: 1;
    min-width: 0;
}

.instructor-info h3 {
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
    color: var(--gray-900, #212529);
}

.rating {
    color: #fbbf24;
    font-size: 0.9rem;
}

.rating span {
    color: var(--secondary);
    margin-left: 0.5rem;
}

.instructor-bio {
    font-size: 0.9rem;
    color: var(--secondary);
    line-height: 1.5;
    min-height: 4rem; /* Fixed height for bio section */
}

.instructor-bio p {
    margin: 0;
}

.selection-indicator {
    position: absolute;
    top: 1rem;
    right: 1rem;
    color: var(--primary);
    font-size: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    background-color: var(--primary-light);
    padding: 0.35rem 0.75rem;
    border-radius: 2rem;
    opacity: 0;
    transform: scale(0.8);
    transition: all 0.3s ease;
}

.selection-indicator i {
    color: var(--primary);
}

.instructor-radio:checked + .instructor-card .selection-indicator {
    opacity: 1;
    transform: scale(1);
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
.alert-info {
    background: var(--primary-light);
    border: none;
    padding: 2rem;
    text-align: center;
}

.alert-info i {
    font-size: 3rem;
    color: var(--primary);
    display: block;
}

.highlight {
    color: var(--primary);
    font-weight: 600;
    position: relative;
}

.highlight::after {
    content: '';
    position: absolute;
    left: 0;
    bottom: -2px;
    width: 100%;
    height: 2px;
    background-color: var(--primary);
    opacity: 0.3;
}

/* Responsive Design */
@media (max-width: 768px) {
    .booking-card {
        padding: 1.5rem;
    }
    
    .instructor-card {
        padding: 1rem;
    }
    
    .instructor-avatar {
        width: 50px;
        height: 50px;
        font-size: 1.25rem;
    }
    
    .instructor-card::before {
        top: 1rem;
        right: 1rem;
        width: 20px;
        height: 20px;
    }
    
    .instructor-radio:checked + .instructor-card::after {
        top: 1.5rem;
        right: 1.5rem;
        width: 7px;
        height: 7px;
    }
    
    .selection-indicator {
        padding: 0.25rem 0.5rem;
        font-size: 0.85rem;
    }
    
    .continue-btn,
    .btn-outline-primary {
        padding: 0.75rem 1.5rem;
        font-size: 1rem;
    }
    
    .instructor-bio {
        min-height: 3rem;
    }
}

/* Dark Mode Support */
@media (prefers-color-scheme: dark) {
    :root {
        --light: #1a1a1a;
        --white: #242424;
        --border: #404040;
        --secondary: #a0aec0;
        --gray-900: #ffffff;
        --primary-light: rgba(13, 110, 253, 0.2);
    }
    
    .booking-card {
        background: var(--white);
    }
    
    .instructor-card {
        background: var(--light);
    }
    
    .instructor-radio:checked + .instructor-card {
        background: var(--white);
    }
    
    .instructor-card::before {
        background: #333;
        border-color: #555;
    }
    
    .instructor-radio:checked + .instructor-card::before {
        background: var(--primary);
        border-color: var(--primary);
    }
    
    .selection-indicator {
        background-color: rgba(13, 110, 253, 0.25);
    }
}
</style>
@endpush