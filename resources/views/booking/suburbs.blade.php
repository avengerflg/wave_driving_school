@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-7">
            <div class="booking-card shadow-lg rounded-4 p-4 bg-white">
                <!-- Steps Indicator -->
                <div class="step-indicator mb-4">
                    @php
                        $steps = [
                            ['Suburb', 'map-marker-alt'],
                            ['Instructor', 'user-tie'],
                            ['Date & Time', 'calendar-alt'],
                            ['Service', 'car'],
                            ['Details', 'clipboard-list'],
                            ['Payment', 'credit-card']
                        ];
                    @endphp
                    @foreach($steps as $i => $step)
                        <div class="step {{ $i === 0 ? 'active' : '' }}">
                            <div class="step-number">
                                <i class="fas fa-{{ $step[1] }}"></i>
                                <span class="step-count">{{ $i + 1 }}</span>
                            </div>
                            <div class="step-title">{{ $step[0] }}</div>
                        </div>
                    @endforeach
                </div>

                <!-- Content -->
                <div class="booking-content text-center py-4">
                    <div class="icon-circle mb-4">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <h1 class="display-6 fw-bold mb-3 text-primary">Choose Your Suburb</h1>
                    <p class="lead text-secondary mb-4">
                        Select a suburb where you would like to take your driving lesson
                    </p>

                    <form action="{{ route('booking.suburb.select') }}" method="POST" class="suburb-form">
                        @csrf
                        <div class="form-group mb-4">
                            <div class="select-wrapper">
                                <select class="form-select form-select-lg @error('suburb_id') is-invalid @enderror" 
                                        id="suburb_id" 
                                        name="suburb_id" 
                                        required>
                                    <option value="">Select your suburb</option>
                                    @foreach($suburbs as $suburb)
                                        <option value="{{ $suburb->id }}">
                                            {{ $suburb->name }}, {{ $suburb->state }} {{ $suburb->postcode }}
                                        </option>
                                    @endforeach
                                </select>
                                <i class="fas fa-map-marker-alt select-icon"></i>
                            </div>
                            @error('suburb_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-lg continue-btn">
                            Continue to Select Instructor
                            <i class="fas fa-arrow-right ms-2"></i>
                        </button>
                    </form>
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
}

.step.active .step-count {
    background: var(--primary);
    color: var(--white);
}
.step-number {
    position: relative;
}

.step.active .step-number {
    background: var(--primary);
    color: var(--white);
    border-color: var(--primary);
    box-shadow: 0 0 0 4px var(--primary-light);
}

.step-title {
    font-size: 0.85rem;
    font-weight: 500;
    color: var(--secondary);
}

.step.active .step-title {
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
    transition: all 0.3s ease;
}

.select-wrapper {
    position: relative;
}

.select-icon {
    position: absolute;
    right: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: var(--secondary);
    pointer-events: none;
}

.form-select {
    height: 60px;
    padding: 0 1rem;
    font-size: 1.1rem;
    border: 2px solid var(--border);
    border-radius: 1rem;
    appearance: none;
    -webkit-appearance: none;
    padding-left: 1rem;
    padding-right: 3rem;
    background: var(--light);
    transition: all 0.3s ease;
}

.form-select:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 4px var(--primary-light);
    background: var(--white);
}

.continue-btn {
    width: 100%;
    padding: 1rem 2rem;
    font-size: 1.1rem;
    font-weight: 600;
    border-radius: 1rem;
    transition: all 0.3s ease;
}

.continue-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(13, 110, 253, 0.2);
}

@media (max-width: 768px) {
    .step-indicator {
        flex-wrap: nowrap;
        overflow-x: auto;
        padding-bottom: 1rem;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: none;
        -ms-overflow-style: none;
    }
    
    .step-indicator::-webkit-scrollbar {
        display: none;
    }
    
    .step {
        flex: 0 0 auto;
        min-width: 100px;
    }
    
    .booking-content {
        padding: 1rem;
    }
    
    .form-select {
        height: 50px;
        font-size: 1rem;
    }

    .icon-circle {
        width: 60px;
        height: 60px;
        font-size: 1.5rem;
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

    .form-select {
        background-color: #1a1a1a;
        color: #ffffff;
    }

    .form-select option {
        background-color: #242424;
        color: #ffffff;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const select = document.querySelector('#suburb_id');
    
    select.addEventListener('change', function() {
        if (this.value) {
            this.style.backgroundColor = 'var(--white)';
        } else {
            this.style.backgroundColor = 'var(--light)';
        }
    });
});
</script>
@endpush