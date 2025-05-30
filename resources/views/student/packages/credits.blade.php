@extends('layouts.student')

@section('title', 'My Package Credits')

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Breadcrumb -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold m-0">
                <span class="text-muted fw-light">Student / Packages /</span> My Credits
            </h4>
            <div>
                <a href="{{ route('student.packages.orders') }}" class="btn btn-outline-primary">
                    <i class="bx bx-shopping-bag me-1"></i> My Orders
                </a>
                <a href="{{ route('student.packages.index') }}" class="btn btn-primary ms-2">
                    <i class="bx bx-package me-1"></i> Browse Packages
                </a>
            </div>
        </div>

        @if(session('success'))
        <div class="alert alert-success alert-dismissible mb-4" role="alert">
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger alert-dismissible mb-4" role="alert">
            <div>{{ session('error') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <!-- Credits summary -->
        <div class="row mb-4">
            <div class="col-md-4 mb-4 mb-md-0">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h5 class="card-title mb-1">Available Credits</h5>
                                @php
                                    $activeCredits = $credits->where('status', 'active')
                                        ->where('remaining', '>', 0)
                                        ->where(function($q) {
                                            return !$q->expires_at || $q->expires_at->isFuture();
                                        })->sum('remaining');
                                @endphp
                                <h2 class="mb-0">{{ $activeCredits }}</h2>
                                <small class="text-muted">Active lessons available</small>
                            </div>
                            <div class="avatar avatar-lg bg-label-primary p-2">
                                <i class="bx bx-credit-card fs-3"></i>
                            </div>
                        </div>
                        <div class="mt-3">
                            <a href="{{ route('booking.index') }}" class="btn btn-sm btn-primary d-block">
                                <i class="bx bx-calendar-plus me-1"></i> Book a Lesson
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body p-0">
                        <div class="row g-0 h-100">
                            <div class="col-sm-4 p-4 text-center border-end">
                                @php
                                    $expiredCredits = $credits->where('status', 'expired')
                                        ->sum('remaining');
                                    
                                    $expiringSoonCredits = $credits->where('status', 'active')
                                        ->where('remaining', '>', 0)
                                        ->where('expires_at', '!=', null)
                                        ->where('expires_at', '>', now())
                                        ->where('expires_at', '<', now()->addDays(30))
                                        ->sum('remaining');
                                    
                                    $totalUsed = $credits->sum('total') - $credits->sum('remaining');
                                @endphp
                                <h5 class="mb-1">{{ $expiringSoonCredits }}</h5>
                                <p class="mb-0 text-muted">Expiring Soon</p>
                            </div>
                            <div class="col-sm-4 p-4 text-center border-end">
                                <h5 class="mb-1">{{ $totalUsed }}</h5>
                                <p class="mb-0 text-muted">Used Credits</p>
                            </div>
                            <div class="col-sm-4 p-4 text-center">
                                <h5 class="mb-1">{{ $expiredCredits }}</h5>
                                <p class="mb-0 text-muted">Expired Credits</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Credits list -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">My Package Credits</h5>
            </div>
            
            @if($credits->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Package</th>
                            <th>Date Purchased</th>
                            <th>Total</th>
                            <th>Remaining</th>
                            <th>Status</th>
                            <th>Expires</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @foreach($credits as $credit)
                        <tr>
                            <td>{{ $credit->package->name ?? 'Unknown Package' }}</td>
                            <td>{{ $credit->created_at->format('M d, Y') }}</td>
                            <td>{{ $credit->total }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="progress w-100 me-3" style="height: 8px;">
                                        <div class="progress-bar bg-success" 
                                             style="width: {{ ($credit->remaining / $credit->total) * 100 }}%" 
                                             role="progressbar"></div>
                                    </div>
                                    <span>{{ $credit->remaining }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-{{ $credit->status === 'active' ? 'success' : ($credit->status === 'expired' ? 'warning' : 'secondary') }}">
                                    {{ ucfirst($credit->status) }}
                                </span>
                            </td>
                            <td>
                                @if($credit->expires_at)
                                    {{ $credit->expires_at->format('M d, Y') }}
                                    @if($credit->expires_at->isPast())
                                        <span class="badge bg-danger ms-1">Expired</span>
                                    @elseif($credit->expires_at->diffInDays(now()) < 30)
                                        <span class="badge bg-warning ms-1">Soon</span>
                                    @endif
                                @else
                                    No expiry
                                @endif
                            </td>
                            <td>
                                @if($credit->status === 'active' && $credit->remaining > 0 && (!$credit->expires_at || $credit->expires_at->isFuture()))
                                <a href="{{ route('booking.index') }}?use_credit={{ $credit->id }}" class="btn btn-sm btn-primary">
                                    <i class="bx bx-calendar-plus me-1"></i> Book
                                </a>
                                @else
                                <button class="btn btn-sm btn-secondary" disabled>
                                    <i class="bx bx-calendar-plus me-1"></i> Book
                                </button>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="card-body text-center py-5">
                <img src="{{ asset('assets/img/illustrations/no-credits.svg') }}" alt="No credits" class="mb-3" style="max-width: 180px;">
                <h5>No Package Credits</h5>
                <p class="mb-4">You don't have any package credits yet. Purchase a package to get started.</p>
                <a href="{{ route('student.packages.index') }}" class="btn btn-primary">
                    <i class="bx bx-package me-1"></i> Browse Packages
                </a>
            </div>
            @endif
        </div>

        <!-- Information card -->
        <div class="card mt-4">
            <div class="card-body">
                <h5 class="mb-3">How to Use Your Credits</h5>
                <div class="row">
                    <div class="col-md-4 mb-3 mb-md-0">
                        <div class="d-flex">
                            <div class="avatar avatar-sm bg-label-primary me-3">
                                <span class="avatar-initial rounded-circle bg-primary">1</span>
                            </div>
                            <div>
                                <h6 class="mb-1">Choose a Package</h6>
                                <p class="text-muted mb-0">Purchase a driving lesson package from our available options.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3 mb-md-0">
                        <div class="d-flex">
                            <div class="avatar avatar-sm bg-label-primary me-3">
                                <span class="avatar-initial rounded-circle bg-primary">2</span>
                            </div>
                            <div>
                                <h6 class="mb-1">Book Your Lesson</h6>
                                <p class="text-muted mb-0">Use your available credits to book driving lessons.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex">
                            <div class="avatar avatar-sm bg-label-primary me-3">
                                <span class="avatar-initial rounded-circle bg-primary">3</span>
                            </div>
                            <div>
                                <h6 class="mb-1">Track Usage</h6>
                                <p class="text-muted mb-0">Monitor your remaining credits and book more lessons as needed.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection