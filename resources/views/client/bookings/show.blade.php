<!-- resources/views/client/bookings/show.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Booking Details</h1>
        <a href="{{ route('client.bookings.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Back to My Bookings
        </a>
    </div>
    
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    
    <div class="row">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between mb-3">
                        <h4>Booking #{{ $booking->id }}</h4>
                        <span class="
                            @if($booking->status === 'pending') badge bg-warning
                            @elseif($booking->status === 'confirmed') badge bg-success
                            @elseif($booking->status === 'completed') badge bg-info
                            @elseif($booking->status === 'cancelled') badge bg-danger
                            @endif
                        ">
                            {{ ucfirst($booking->status) }}
                        </span>
                    </div>
                    
                    <div class="row mb-2">
                        <div class="col-md-4 fw-bold">Service:</div>
                        <div class="col-md-8">{{ $booking->service->name }}</div>
                    </div>
                    
                    <div class="row mb-2">
                        <div class="col-md-4 fw-bold">Date:</div>
                        <div class="col-md-8">{{ \Carbon\Carbon::parse($booking->date)->format('l, F j, Y') }}</div>
                    </div>
                    
                    <div class="row mb-2">
                        <div class="col-md-4 fw-bold">Time:</div>
                        <div class="col-md-8">{{ \Carbon\Carbon::parse($booking->start_time)->format('g:i A') }} - {{ \Carbon\Carbon::parse($booking->end_time)->format('g:i A') }}</div>
                    </div>
                    
                    <div class="row mb-2">
                        <div class="col-md-4 fw-bold">Duration:</div>
                        <div class="col-md-8">{{ $booking->service->duration }} minutes</div>
                    </div>
                    
                    <div class="row mb-2">
                        <div class="col-md-4 fw-bold">Location:</div>
                        <div class="col-md-8">{{ $booking->suburb->name }}, {{ $booking->suburb->state }}</div>
                    </div>
                    
                    <div class="row mb-2">
                        <div class="col-md-4 fw-bold">Pickup Address:</div>
                        <div class="col-md-8">{{ $booking->address }}</div>
                    </div>
                    
                    @if($booking->booking_for === 'other')
                        <div class="row mb-2">
                            <div class="col-md-4 fw-bold">Student Name:</div>
                            <div class="col-md-8">{{ $booking->other_name }}</div>
                        </div>
                        
                        <div class="row mb-2">
                            <div class="col-md-4 fw-bold">Student Email:</div>
                            <div class="col-md-8">{{ $booking->other_email }}</div>
                        </div>
                        
                        <div class="row mb-2">
                            <div class="col-md-4 fw-bold">Student Phone:</div>
                            <div class="col-md-8">{{ $booking->other_phone }}</div>
                        </div>
                    @endif
                    
                    <div class="row mb-2">
                        <div class="col-md-4 fw-bold">Booked On:</div>
                        <div class="col-md-8">{{ $booking->created_at->format('F j, Y, g:i A') }}</div>
                    </div>
                    
                    @if($booking->notes)
                        <div class="row mb-2">
                            <div class="col-md-4 fw-bold">Notes:</div>
                            <div class="col-md-8">{{ $booking->notes }}</div>
                        </div>
                    @endif
                </div>
            </div>
            
            @if($booking->payment)
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h4 class="mb-3">Payment Information</h4>
                        
                        <div class="row mb-2">
                            <div class="col-md-4 fw-bold">Amount:</div>
                            <div class="col-md-8">${{ number_format($booking->payment->amount, 2) }}</div>
                        </div>
                        
                        <div class="row mb-2">
                            <div class="col-md-4 fw-bold">Payment Method:</div>
                            <div class="col-md-8">
                                @if($booking->payment->payment_method === 'credit_card')
                                    Credit Card
                                @elseif($booking->payment->payment_method === 'paypal')
                                    PayPal
                                @else
                                    {{ ucfirst($booking->payment->payment_method) }}
                                @endif
                            </div>
                        </div>
                        
                        <div class="row mb-2">
                            <div class="col-md-4 fw-bold">Transaction ID:</div>
                            <div class="col-md-8">{{ $booking->payment->transaction_id }}</div>
                        </div>
                        
                        <div class="row mb-2">
                            <div class="col-md-4 fw-bold">Status:</div>
                            <div class="col-md-8">
                                @if($booking->payment->status === 'completed')
                                    <span class="badge bg-success">Completed</span>
                                @elseif($booking->payment->status === 'pending')
                                    <span class="badge bg-warning">Pending</span>
                                @elseif($booking->payment->status === 'failed')
                                    <span class="badge bg-danger">Failed</span>
                                @elseif($booking->payment->status === 'refunded')
                                    <span class="badge bg-info">Refunded</span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="row mb-2">
                            <div class="col-md-4 fw-bold">Payment Date:</div>
                            <div class="col-md-8">{{ $booking->payment->created_at->format('F j, Y, g:i A') }}</div>
                        </div>
                    </div>
                </div>
            @endif
            
            <div class="d-flex justify-content-between">
                <a href="{{ route('client.bookings.index') }}" class="btn btn-outline-secondary">Back to My Bookings</a>
                
                @if($booking->status !== 'completed' && $booking->status !== 'cancelled')
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#cancelModal">
                        Cancel Booking
                    </button>
                    
                    <!-- Cancel Modal -->
                    <div class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="cancelModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="cancelModalLabel">Cancel Booking</h5>
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
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h4 class="mb-3">Instructor</h4>
                    
                    <div class="text-center mb-3">
                        <img src="{{ $booking->instructor->profile_image ? asset('storage/' . $booking->instructor->profile_image) : 'https://via.placeholder.com/150x150?text=Instructor' }}" class="rounded-circle" style="width: 100px; height: 100px; object-fit: cover;" alt="{{ $booking->instructor->user->name }}">
                        <h5 class="mt-2 mb-0">{{ $booking->instructor->user->name }}</h5>
                        <div class="text-warning">
                            @for($i = 0; $i < 5; $i++)
                                @if($i < $booking->instructor->rating)
                                    <i class="fas fa-star"></i>
                                @else
                                    <i class="far fa-star"></i>
                                @endif
                            @endfor
                        </div>
                    </div>
                    
                    <p>{{ Str::limit($booking->instructor->bio, 150) }}</p>
                    
                    <div class="d-grid">
                        <a href="tel:{{ $booking->instructor->user->phone }}" class="btn btn-outline-primary">
                            <i class="fas fa-phone me-2"></i> Contact Instructor
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h4 class="mb-3">Need Help?</h4>
                    <p>If you have any questions about your booking, please contact our customer support team.</p>
                    <div class="d-grid">
                        <a href="{{ route('contact') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-envelope me-2"></i> Contact Support
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
