@extends('layouts.instructor')

@section('title', $package->name)

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Breadcrumb and actions header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold m-0">
                <span class="text-muted fw-light">Instructor / Packages /</span> Package Details
            </h4>
            <div>
                <a href="{{ route('instructor.packages.index') }}" class="btn btn-outline-secondary">
                    <i class="bx bx-arrow-back me-1"></i> Back to Packages
                </a>
            </div>
        </div>

        @if(session('success'))
        <div class="alert alert-success alert-dismissible mb-4" role="alert">
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <!-- Package overview card -->
        <div class="row">
            <div class="col-xl-8 col-lg-7">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between">
                        <h5 class="mb-0">Package Information</h5>
                        <div>
                            @if($package->active)
                            <span class="badge bg-success">Active</span>
                            @else
                            <span class="badge bg-secondary">Inactive</span>
                            @endif
                            @if($package->featured)
                            <span class="badge bg-primary ms-1">Featured</span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <h2 class="card-title mb-2">{{ $package->name }}</h2>
                                <p class="card-text text-muted mb-4">{{ $package->description }}</p>
                                
                                <div class="mb-3">
                                    <h6 class="fw-semibold">Package Details:</h6>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="d-flex align-items-center mb-2">
                                                <i class="bx bx-check-circle text-primary me-2"></i>
                                                <div>
                                                    <small class="text-muted d-block">Number of Lessons</small>
                                                    <span class="fw-semibold">{{ $package->lessons }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="d-flex align-items-center mb-2">
                                                <i class="bx bx-time text-primary me-2"></i>
                                                <div>
                                                    <small class="text-muted d-block">Lesson Duration</small>
                                                    <span class="fw-semibold">{{ $package->duration ?? 60 }} minutes</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="d-flex align-items-center mb-2">
                                                <i class="bx bx-calendar text-primary me-2"></i>
                                                <div>
                                                    <small class="text-muted d-block">Validity Period</small>
                                                    <span class="fw-semibold">{{ $package->validity ?? 'No expiry' }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="d-flex align-items-center mb-2">
                                                <i class="bx bx-dollar-circle text-primary me-2"></i>
                                                <div>
                                                    <small class="text-muted d-block">Price per Lesson</small>
                                                    <span class="fw-semibold">${{ number_format($package->price / $package->lessons, 2) }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                @if($package->features)
                                <div class="mb-3">
                                    <h6 class="fw-semibold">Features & Benefits:</h6>
                                    <ul class="ps-3 mb-0">
                                        @foreach(explode("\n", $package->features) as $feature)
                                            @if(trim($feature))
                                            <li class="mb-1">{{ trim($feature) }}</li>
                                            @endif
                                        @endforeach
                                    </ul>
                                </div>
                                @endif
                            </div>
                            
                            <div class="col-md-4">
                                <div class="price-card bg-light rounded p-3 text-center mb-3">
                                    <small class="text-muted">Package Price</small>
                                    <h3 class="fw-bold text-primary">${{ number_format($package->price, 2) }}</h3>
                                    <p class="mb-0">
                                        <span class="badge bg-label-success">Save {{ number_format(100 - (($package->price / $package->lessons) / ($package->original_price ?? $package->price) * 100), 0) }}%</span>
                                        <small class="text-muted">compared to single lessons</small>
                                    </p>
                                </div>
                                
                                <div class="divider mb-3">
                                    <div class="divider-text">Instructor Information</div>
                                </div>
                                
                                <div class="mb-3">
                                    <p class="text-muted mb-0">As an instructor, you should:</p>
                                    <ul class="ps-3 mt-2 mb-0">
                                        <li class="mb-1">Promote this package to your students</li>
                                        <li class="mb-1">Offer consistent lesson quality</li>
                                        <li class="mb-1">Keep track of package lessons</li>
                                        <li class="mb-1">Help students maximize value</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Package analytics if available -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Package Analytics</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-sm-6 col-md-3">
                                <div class="card shadow-none bg-label-primary h-100">
                                    <div class="card-body">
                                        <h5 class="card-title mb-0">{{ $packageStats['total_purchases'] ?? 0 }}</h5>
                                        <small>Total Purchases</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-3">
                                <div class="card shadow-none bg-label-success h-100">
                                    <div class="card-body">
                                        <h5 class="card-title mb-0">{{ $packageStats['your_students'] ?? 0 }}</h5>
                                        <small>Your Students</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-3">
                                <div class="card shadow-none bg-label-info h-100">
                                    <div class="card-body">
                                        <h5 class="card-title mb-0">{{ $packageStats['active_credits'] ?? 0 }}</h5>
                                        <small>Active Credits</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-3">
                                <div class="card shadow-none bg-label-warning h-100">
                                    <div class="card-body">
                                        <h5 class="card-title mb-0">{{ $packageStats['usage_rate'] ?? '0%' }}</h5>
                                        <small>Usage Rate</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-4 col-lg-5">
                <!-- Your students with this package -->
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between">
                        <h5 class="mb-0">Your Students with This Package</h5>
                        <div>
                            <small class="text-muted">Active credits only</small>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Student</th>
                                        <th>Remaining</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                    @forelse($studentCredits ?? [] as $credit)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-sm me-2">
                                                    <span class="avatar-initial rounded-circle bg-label-primary">
                                                        {{ substr($credit->user->name ?? 'U', 0, 1) }}
                                                    </span>
                                                </div>
                                                <div>
                                                    <a href="{{ route('instructor.clients.show', $credit->user_id) }}">
                                                        {{ $credit->user->name ?? 'Unknown Student' }}
                                                    </a>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="progress w-100 me-3" style="height: 8px;">
                                                    <div class="progress-bar bg-success" 
                                                         style="width: {{ ($credit->remaining / $credit->total) * 100 }}%" 
                                                         role="progressbar"></div>
                                                </div>
                                                <span>{{ $credit->remaining }}/{{ $credit->total }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <a href="{{ route('instructor.clients.show', $credit->user_id) }}" class="btn btn-sm btn-icon btn-primary">
                                                <i class="bx bx-show"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-3">
                                            <div class="p-4">
                                                <img src="{{ asset('assets/img/illustrations/empty.svg') }}" 
                                                     alt="No students" 
                                                     class="img-fluid mb-2" 
                                                     style="max-height: 120px;">
                                                <p class="mb-0">No students have active credits for this package</p>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    @if(!empty($studentCredits) && count($studentCredits) > 5)
                    <div class="card-footer text-center">
                        <a href="{{ route('instructor.packages.credits') }}?package={{ $package->id }}" class="btn btn-sm btn-outline-primary">
                            View All Student Credits
                        </a>
                    </div>
                    @endif
                </div>
                
                <!-- Upcoming package lessons -->
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between">
                        <h5 class="mb-0">Upcoming Package Lessons</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            @forelse($upcomingLessons ?? [] as $booking)
                            <div class="list-group-item">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm me-3">
                                        <span class="avatar-initial rounded-circle bg-label-primary">
                                            {{ substr($booking->user->name ?? 'U', 0, 1) }}
                                        </span>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-0">{{ $booking->user->name ?? 'Unknown Student' }}</h6>
                                        <small class="text-muted">
                                            {{ $booking->date->format('D, M d') }} · 
                                            {{ $booking->start_time->format('g:i A') }} - 
                                            {{ $booking->end_time->format('g:i A') }}
                                        </small>
                                    </div>
                                    <a href="{{ route('instructor.bookings.show', $booking->id) }}" class="btn btn-sm btn-icon btn-primary">
                                        <i class="bx bx-show"></i>
                                    </a>
                                </div>
                            </div>
                            @empty
                            <div class="text-center py-5">
                                <i class="bx bx-calendar-x text-secondary mb-2" style="font-size: 3rem;"></i>
                                <p class="mb-0">No upcoming lessons booked with this package</p>
                            </div>
                            @endforelse
                        </div>
                    </div>
                    
                    @if(!empty($upcomingLessons) && count($upcomingLessons) > 5)
                    <div class="card-footer text-center">
                        <a href="{{ route('instructor.packages.lessons') }}?package={{ $package->id }}" class="btn btn-sm btn-outline-primary">
                            View All Package Lessons
                        </a>
                    </div>
                    @endif
                </div>
                
                <!-- Marketing tips -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Promoting This Package</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar avatar-lg bg-label-primary me-3">
                                <i class="bx bx-bulb"></i>
                            </div>
                            <div>
                                <h6 class="mb-0">Tips for Success</h6>
                                <small class="text-muted">How to promote this package</small>
                            </div>
                        </div>
                        
                        <ul class="ps-3 mb-0">
                            <li class="mb-2">Mention the package during lessons with interested students</li>
                            <li class="mb-2">Highlight the cost savings compared to individual lessons</li>
                            <li class="mb-2">Emphasize the consistency and progress possible with multiple lessons</li>
                            <li class="mb-2">Share the package link directly with your students</li>
                        </ul>
                        
                        <div class="mt-3">
                            <div class="input-group">
                                <input type="text" class="form-control" id="packageLink" 
                                       value="{{ route('packages.show', $package->id) }}" readonly>
                                <button class="btn btn-outline-primary" type="button" id="copyLink" 
                                        onclick="copyLinkToClipboard()">
                                    <i class="bx bx-copy"></i>
                                </button>
                            </div>
                            <small class="text-muted">Share this link with your students</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function copyLinkToClipboard() {
        var linkInput = document.getElementById('packageLink');
        linkInput.select();
        document.execCommand('copy');
        
        var copyBtn = document.getElementById('copyLink');
        copyBtn.innerHTML = '<i class="bx bx-check"></i>';
        copyBtn.classList.remove('btn-outline-primary');
        copyBtn.classList.add('btn-success');
        
        setTimeout(function() {
            copyBtn.innerHTML = '<i class="bx bx-copy"></i>';
            copyBtn.classList.remove('btn-success');
            copyBtn.classList.add('btn-outline-primary');
        }, 2000);
    }
</script>
@endsection