@extends('layouts.instructor')

@section('title', 'Packages')

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold m-0">
                <span class="text-muted fw-light">Instructor /</span> Packages
            </h4>
        </div>

        <div class="row mb-4">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Available Packages</h5>
                    </div>
                    <div class="table-responsive text-nowrap">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Package</th>
                                    <th>Lessons</th>
                                    <th>Duration</th>
                                    <th>Price</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                                @forelse($packages as $package)
                                <tr>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <strong>{{ $package->name }}</strong>
                                            <small class="text-muted">{{ Str::limit($package->description, 50) }}</small>
                                        </div>
                                    </td>
                                    <td>{{ $package->lessons }}</td>
                                    <td>{{ $package->duration }} minutes</td>
                                    <td>${{ number_format($package->price, 2) }}</td>
                                    <td>
                                        <a href="{{ route('instructor.packages.show', $package->id) }}" class="btn btn-sm btn-primary">
                                            <i class="bx bx-show me-1"></i> Details
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-3">No packages available</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Package Management</h5>
                        <div class="d-grid gap-2">
                            <a href="{{ route('instructor.packages.orders') }}" class="btn btn-outline-primary">
                                <i class="bx bx-shopping-bag me-1"></i> View Student Orders
                            </a>
                            <a href="{{ route('instructor.packages.credits') }}" class="btn btn-outline-primary">
                                <i class="bx bx-credit-card me-1"></i> Student Credits
                            </a>
                            <a href="{{ route('instructor.packages.lessons') }}" class="btn btn-outline-primary">
                                <i class="bx bx-calendar me-1"></i> Package Lessons
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-3">How Packages Work</h5>
                        <p>Packages allow students to purchase multiple lessons at a discounted rate.</p>
                        <p>When a student books a lesson with you, they can choose to use their package credits instead of paying directly.</p>
                        <p>You'll be notified when a student uses package credits for a booking.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection