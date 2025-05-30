@extends('layouts.admin')

@section('content')
<div class="container">
    <h1>Instructor Bookings</h1>
    
    @if($bookings->count() > 0)
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Client</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Service</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bookings as $booking)
                    <tr>
                        <td>{{ $booking->id }}</td>
                        <td>{{ $booking->user->name }}</td>
                        <td>{{ $booking->date->format('Y-m-d') }}</td>
                        <td>{{ $booking->start_time->format('H:i') }} - {{ $booking->end_time->format('H:i') }}</td>
                        <td>{{ $booking->service->name }}</td>
                        <td>{{ ucfirst($booking->status) }}</td>
                        <td>
                            <a href="{{ route('admin.bookings.show', $booking) }}" class="btn btn-sm btn-info">View</a>
                            @if(!in_array($booking->status, ['completed', 'cancelled']))
                                <a href="{{ route('instructor.bookings.reschedule.form', $booking) }}" class="btn btn-sm btn-warning">Reschedule</a>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        {{ $bookings->links() }}
    @else
        <div class="alert alert-info">No bookings found for this instructor.</div>
    @endif
</div>
@endsection