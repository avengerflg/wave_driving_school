<!-- resources/views/home.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container">
    <!-- Hero Section -->
    <div class="row align-items-center py-5">
        <div class="col-md-6">
            <h1 class="display-4 fw-bold mb-4">Learn to Drive with Confidence</h1>
            <p class="lead mb-4">Professional driving lessons across Australia with experienced instructors. Book your lesson today and take the first step towards getting your license.</p>
            <div class="d-grid gap-2 d-md-flex justify-content-md-start">
                <a href="{{ route('booking.index') }}" class="btn btn-primary btn-lg px-4 me-md-2">Book Now</a>
                <a href="{{ route('about') }}" class="btn btn-outline-secondary btn-lg px-4">Learn More</a>
            </div>
        </div>
        <div class="col-md-6">
            <img src="https://via.placeholder.com/600x400?text=Driving+Lessons" alt="Driving Lessons" class="img-fluid rounded shadow-lg">
        </div>
    </div>
    
    <!-- How It Works Section -->
    <div class="py-5 bg-light mt-5 rounded">
        <div class="text-center mb-5">
            <h2 class="fw-bold">How It Works</h2>
            <p class="lead">Book your driving lesson in 3 simple steps</p>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto mb-4" style="width: 80px; height: 80px;">
                            <i class="fas fa-map-marker-alt fa-2x"></i>
                        </div>
                        <h4>1. Choose Your Location</h4>
                        <p class="text-muted">Select your suburb and we'll show you available instructors in your area.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto mb-4" style="width: 80px; height: 80px;">
                            <i class="fas fa-calendar-alt fa-2x"></i>
                        </div>
                        <h4>2. Pick a Time</h4>
                        <p class="text-muted">Choose from available time slots that work with your schedule.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto mb-4" style="width: 80px; height: 80px;">
                            <i class="fas fa-car fa-2x"></i>
                        </div>
                        <h4>3. Start Learning</h4>
                        <p class="text-muted">Your instructor will meet you at the agreed location and time.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="text-center mt-4">
            <a href="{{ route('booking.index') }}" class="btn btn-primary btn-lg">Book Your Lesson</a>
        </div>
    </div>
    
    <!-- Services Section -->
    <div class="py-5 mt-5">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Our Services</h2>
            <p class="lead">We offer a range of driving lessons to suit your needs</p>
        </div>
        <div class="row g-4">
            @foreach($services as $service)
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h4>{{ $service->name }}</h4>
                        <p class="text-muted">{{ $service->description }}</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold text-primary">${{ number_format($service->price, 2) }}</span>
                            <span class="text-muted">{{ $service->duration }} minutes</span>
                        </div>
                    </div>
                    <div class="card-footer bg-white border-0 p-4 pt-0">
                        <div class="d-grid">
                            <a href="{{ route('booking.index') }}" class="btn btn-outline-primary">Book Now</a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <div class="text-center mt-4">
            <a href="{{ route('services') }}" class="btn btn-outline-primary">View All Services</a>
        </div>
    </div>
    
    <!-- Instructors Section -->
    <div class="py-5 bg-light mt-5 rounded">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Meet Our Instructors</h2>
            <p class="lead">Professional and experienced driving instructors</p>
        </div>
        <div class="row g-4">
            @foreach($instructors as $instructor)
            <div class="col-md-3">
                <div class="card h-100 border-0 shadow-sm instructor-card">
                    <img src="{{ $instructor->profile_image ? asset('storage/' . $instructor->profile_image) : 'https://via.placeholder.com/300x300?text=Instructor' }}" class="card-img-top" alt="{{ $instructor->user->name }}">
                    <div class="card-body text-center">
                        <h5 class="card-title">{{ $instructor->user->name }}</h5>
                        <p class="card-text text-muted">{{ Str::limit($instructor->bio, 100) }}</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <div class="text-center mt-4">
            <a href="{{ route('about') }}" class="btn btn-outline-primary">Meet All Instructors</a>
        </div>
    </div>
    
    <!-- Testimonials Section -->
    <div class="py-5 mt-5">
        <div class="text-center mb-5">
            <h2 class="fw-bold">What Our Students Say</h2>
            <p class="lead">Read testimonials from our happy students</p>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="d-flex mb-3">
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                        </div>
                        <p class="card-text">"My instructor was patient and knowledgeable. I passed my test on the first try thanks to their excellent teaching methods."</p>
                        <div class="d-flex align-items-center mt-3">
                            <img src="https://via.placeholder.com/50x50" class="rounded-circle me-3" alt="Student">
                            <div>
                                <h6 class="mb-0">Sarah Johnson</h6>
                                <small class="text-muted">Sydney, NSW</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="d-flex mb-3">
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                        </div>
                        <p class="card-text">"I was very nervous about learning to drive, but my instructor made me feel comfortable and confident. Highly recommend!"</p>
                        <div class="d-flex align-items-center mt-3">
                            <img src="https://via.placeholder.com/50x50" class="rounded-circle me-3" alt="Student">
                            <div>
                                <h6 class="mb-0">Michael Chen</h6>
                                <small class="text-muted">Melbourne, VIC</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="d-flex mb-3">
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                        </div>
                        <p class="card-text">"The booking process was so easy, and the instructor was punctual and professional. Great experience overall!"</p>
                        <div class="d-flex align-items-center mt-3">
                            <img src="https://via.placeholder.com/50x50" class="rounded-circle me-3" alt="Student">
                            <div>
                                <h6 class="mb-0">Emma Wilson</h6>
                                <small class="text-muted">Brisbane, QLD</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Call to Action Section -->
    <div class="py-5 mt-5 bg-primary text-white rounded">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h2 class="fw-bold mb-3">Ready to Start Your Driving Journey?</h2>
                    <p class="lead mb-0">Book your first lesson today and take the first step towards getting your license.</p>
                </div>
                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    <a href="{{ route('booking.index') }}" class="btn btn-light btn-lg">Book Now</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
