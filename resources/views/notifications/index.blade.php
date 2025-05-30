@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="fw-bold mb-0">
                            <i class="bx bx-bell text-primary me-2"></i>
                            Notifications
                        </h4>
                        <div>
                            @if(auth()->user()->unreadNotifications->count() > 0)
                                <a href="{{ route('notifications.mark-all-read') }}" class="btn btn-outline-primary me-2">
                                    <i class="bx bx-check-double me-1"></i> Mark All as Read
                                </a>
                            @endif
                            <form action="{{ route('notifications.destroy-all') }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger" onclick="return confirm('Are you sure you want to delete all notifications?')">
                                    <i class="bx bx-trash me-1"></i> Clear All
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Notifications List -->
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <h5 class="mb-0">All Notifications</h5>
            <div class="dropdown">
                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="bx bx-filter me-1"></i> Filter
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="{{ route('notifications.index') }}">All Notifications</a></li>
                    <li><a class="dropdown-item" href="{{ route('notifications.index', ['filter' => 'unread']) }}">Unread Only</a></li>
                    <li><a class="dropdown-item" href="{{ route('notifications.index', ['filter' => 'read']) }}">Read Only</a></li>
                </ul>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="list-group list-group-flush">
                @forelse($notifications as $notification)
                    <div class="list-group-item list-group-item-action p-4 {{ $notification->read_at ? '' : 'bg-light' }}">
                        <div class="d-flex align-items-start">
                            <div class="flex-shrink-0 me-3">
                                <div class="avatar">
                                    <span class="avatar-initial rounded-circle bg-label-{{ $notification->data['color'] ?? 'primary' }}">
                                        <i class="bx {{ $notification->data['icon'] ?? 'bx-bell' }}"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="mb-0">{{ $notification->data['title'] }}</h6>
                                    <div>
                                        <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                        <div class="dropdown d-inline ms-3">
                                            <button type="button" class="btn btn-sm dropdown-toggle hide-arrow p-0" data-bs-toggle="dropdown">
                                                <i class="bx bx-dots-vertical-rounded"></i>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                @if(!$notification->read_at)
                                                    <a class="dropdown-item" href="{{ route('notifications.read', $notification->id) }}">
                                                        <i class="bx bx-check me-1"></i> Mark as Read
                                                    </a>
                                                @endif
                                                <form action="{{ route('notifications.destroy', $notification->id) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item">
                                                        <i class="bx bx-trash me-1"></i> Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <p class="mb-2">{{ $notification->data['message'] }}</p>
                                
                                <!-- Additional notification details based on type -->
                                @if(isset($notification->data['date_time']))
                                    <div class="text-muted mb-1">
                                        <i class="bx bx-calendar me-1"></i> {{ $notification->data['date_time'] }}
                                    </div>
                                @endif
                                
                                @if(isset($notification->data['old_date_time']) && isset($notification->data['new_date_time']))
                                    <div class="text-muted mb-1">
                                        <i class="bx bx-revision me-1"></i> Changed from {{ $notification->data['old_date_time'] }} to {{ $notification->data['new_date_time'] }}
                                    </div>
                                @endif
                                
                                @if(isset($notification->data['instructor_name']))
                                    <div class="text-muted mb-1">
                                        <i class="bx bx-user me-1"></i> Instructor: {{ $notification->data['instructor_name'] }}
                                    </div>
                                @endif
                                
                                @if(isset($notification->data['notes']) && $notification->data['notes'])
                                    <div class="alert alert-light-secondary mt-2 mb-0 p-2">
                                        <small><i class="bx bx-notepad me-1"></i> {{ $notification->data['notes'] }}</small>
                                    </div>
                                @endif
                                
                                <div class="mt-3">
                                    <a href="{{ route('notifications.redirect', $notification->id) }}" class="btn btn-sm btn-primary">
                                        <i class="bx bx-right-arrow-alt me-1"></i> View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5">
                        <img src="{{ asset('assets/img/illustrations/empty.png') }}" alt="No Notifications" class="mb-3" style="height: 140px;">
                        <h5 class="fw-semibold mb-1">No Notifications</h5>
                        <p class="text-muted">You don't have any notifications at the moment.</p>
                    </div>
                @endforelse
            </div>
        </div>
        
        @if($notifications->hasPages())
            <div class="card-footer d-flex justify-content-center pt-4">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@section('page-scripts')
<style>
    .avatar-initial {
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        font-weight: 600;
    }
</style>
@endsection