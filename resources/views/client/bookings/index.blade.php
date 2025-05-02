<!-- resources/views/client/bookings/index.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="mb-4">My Bookings</h1>
    
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            @if($bookings->isEmpty())
                <div class="p-4 text-center">
                    <p class="mb-3">You don't have any bookings yet.</p>
                    <a href="{{ route('booking.index') }}" class="btn btn-primary">Book a Lesson</a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Service</th>
                                <th>Instructor</th>
                                <th>Date & Time</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($bookings as $booking)
                                <tr>
                                    <td>{{ $booking->id }}</td>
                                    <td>{{ $booking->service->name }}</td>
                                    <td>{{ $booking->instructor->user->name }}</td>
                                    <td>
                                        {{ \Carbon\Carbon::parse($booking->date)->format('d M Y') }}<br>
                                        <small class="text-muted">
                                            {{ \Carbon\Carbon::parse($booking->start_time)->format('g:i A') }} - 
                                            {{ \Carbon\Carbon::parse($booking->end_time)->format('g:i A') }}
                                        </small>
                                    </td>
                                    <td>
                                        @if($booking->status === 'pending')
                                            <span class="badge bg-warning">Pending</span>
                                        @elseif($booking->status === 'confirmed')
                                            <span class="badge bg-success">Confirmed</span>
                                        @elseif($booking->status === 'completed')
                                            <span class="badge bg-info">Completed</span>
                                        @elseif($booking->status === 'cancelled')
                                            <span class="badge bg-danger">Cancelled</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('client.bookings.show', $booking) }}" class="btn btn-sm btn-outline-primary">View</a>
                                        
                                        @if($booking->status !== 'completed' && $booking->status !== 'cancelled')
                                            <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#cancelModal{{ $booking->id }}">
                                                Cancel
                                            </button>
                                            
                                            <!-- Cancel Modal -->
                                            <div class="modal fade" id="cancelModal{{ $booking->id }}" tabindex="-1" aria-labelledby="cancelModalLabel{{ $booking->id }}" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                        <h5 class="modal-title" id="cancelModalLabel{{ $booking->id }}">Cancel Booking</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p>Are you sure you want to cancel this booking?</p>
                                                            <p><strong>Note:</strong> Bookings can only be cancelled at least 24 hours in advance.</p>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                            <form action="{{ route('client.bookings.cancel', $booking) }}" method="POST">
                                                                @csrf
                                                                @method('PUT')
                                                                <button type="submit" class="btn btn-danger">Cancel Booking</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="d-flex justify-content-center p-3">
                    {{ $bookings->links() }}
                </div>
            @endif
        </div>
    </div>
    
    <div class="mt-4">
        <a href="{{ route('booking.index') }}" class="btn btn-primary">Book Another Lesson</a>
    </div>
</div>
@endsection