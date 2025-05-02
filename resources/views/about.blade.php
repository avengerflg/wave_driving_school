<!-- resources/views/about.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container py-5">
    <!-- About Us Section -->
    <div class="row align-items-center mb-5">
        <div class="col-md-6">
            <h1 class="display-4 fw-bold mb-4">About Wave Driving School</h1>
            <p class="lead mb-4">We are dedicated to providing high-quality driving instruction across Australia.</p>
            <p>At Wave Driving School, we believe that learning to drive should be a positive and empowering experience. Our mission is to create safe, confident, and responsible drivers through personalized instruction and a supportive learning environment.</p>
            <p>Established in 2010, we have helped thousands of students successfully obtain their driver's license and build the skills they need for a lifetime of safe driving.</p>
        </div>
        <div class="col-md-6">
            <img src="https://via.placeholder.com/600x400?text=About+Us" alt="About Wave Driving School" class="img-fluid rounded shadow-lg">
        </div>
    </div>
    
    <!-- Why Choose Us Section -->
    <div class="py-5 bg-light rounded">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Why Choose Wave Driving School?</h2>
            <p class="lead">We offer more than just driving lessons</p>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto mb-4" style="width: 80px; height: 80px;">
                            <i class="fas fa-user-tie fa-2x"></i>
                        </div>
                        <h4>Experienced Instructors</h4>
                        <p class="text-muted">All our instructors are fully licensed, accredited, and have years of teaching experience.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto mb-4" style="width: 80px; height: 80px;">
                            <i class="fas fa-car fa-2x"></i>
                        </div>
                        <h4>Modern Vehicles</h4>
                        <p class="text-muted">Learn in dual-control, late-model vehicles that are regularly serviced and maintained.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto mb-4" style="width: 80px; height: 80px;">
                            <i class="fas fa-clipboard-check fa-2x"></i>
                        </div>
                        <h4>Personalized Lessons</h4>
                        <p class="text-muted">We tailor our teaching approach to match your learning style and needs.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto mb-4" style="width: 80px; height: 80px;">
                            <i class="fas fa-map-marked-alt fa-2x"></i>
                        </div>
                        <h4>Convenient Locations</h4>
                        <p class="text-muted">We offer lessons in suburbs across Australia, with pick-up and drop-off service.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto mb-4" style="width: 80px; height: 80px;">
                            <i class="fas fa-clock fa-2x"></i>
                        </div>
                        <h4>Flexible Scheduling</h4>
                        <p class="text-muted">Book lessons at times that suit your schedule, including evenings and weekends.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto mb-4" style="width: 80px; height: 80px;">
                            <i class="fas fa-graduation-cap fa-2x"></i>
                        </div>
                        <h4>High Pass Rate</h4>
                        <p class="text-muted">We're proud of our high first-time test pass rate and positive student feedback.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Our Instructors Section -->
    <div class="py-5 mt-5">
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
                        <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#instructorModal{{ $instructor->id }}">
                            Read More
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Instructor Modal -->
            <div class="modal fade" id="instructorModal{{ $instructor->id }}" tabindex="-1" aria-labelledby="instructorModalLabel{{ $instructor->id }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="instructorModalLabel{{ $instructor->id }}">{{ $instructor->user->name }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="text-center mb-3">
                                <img src="{{ $instructor->profile_image ? asset('storage/' . $instructor->profile_image) : 'https://via.placeholder.com/300x300?text=Instructor' }}" class="img-fluid rounded" style="max-height: 200px;" alt="{{ $instructor->user->name }}">
                            </div>
                            <p>{{ $instructor->bio }}</p>
                            <p><strong>License Number:</strong> {{ $instructor->license_number }}</p>
                            <p><strong>Areas Covered:</strong>
                                @php
                                    $suburbIds = $instructor->suburbs ?? [];
                                    $suburbNames = \App\Models\Suburb::whereIn('id', $suburbIds)->pluck('name')->toArray();
                                    echo implode(', ', $suburbNames);
                                @endphp
                            </p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <a href="{{ route('booking.index') }}" class="btn btn-primary">Book with {{ $instructor->user->name }}</a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
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
