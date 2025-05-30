@extends('layouts.instructor')

@section('title', 'Student Package Credits')

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold m-0">
                <span class="text-muted fw-light">Instructor / Packages /</span> Student Credits
            </h4>
            <div>
                <a href="{{ route('instructor.packages.index') }}" class="btn btn-outline-secondary">
                    <i class="bx bx-arrow-back me-1"></i> Back to Packages
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Available Package Credits</h5>
                <div>
                    <small class="text-muted">Credits available for your students</small>
                </div>
            </div>
            <div class="table-responsive text-nowrap">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Package</th>
                            <th>Remaining</th>
                            <th>Used</th>
                            <th>Expiry</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse($credits as $credit)
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
                                            {{ $credit->user->name ?? 'Unknown User' }}
                                        </a>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $credit->package->name ?? 'Unknown Package' }}</td>
                            <td>
                                <span class="badge bg-label-primary">{{ $credit->remaining }} lessons</span>
                            </td>
                            <td>{{ $credit->total - $credit->remaining }}</td>
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
                                <span class="badge bg-{{ $credit->status === 'active' ? 'success' : 'secondary' }}">
                                    {{ ucfirst($credit->status) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('instructor.packages.student.credits', $credit->user_id) }}" class="btn btn-sm btn-primary">
                                    <i class="bx bx-show"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-3">
                                <div class="d-flex flex-column align-items-center py-4">
                                    <i class="bx bx-credit-card text-secondary mb-2" style="font-size: 3rem;"></i>
                                    <h5 class="mb-1">No Credits Found</h5>
                                    <p class="text-muted">None of your students have active package credits</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($credits->count() > 0)
            <div class="card-footer">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        Showing {{ $credits->firstItem() ?? 0 }} to {{ $credits->lastItem() ?? 0 }} of {{ $credits->total() }} credits
                    </div>
                    <div>
                        {{ $credits->links() }}
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection