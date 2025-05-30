
@extends('layouts.admin')

@section('title', 'Payment Details')

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold m-0">
                <span class="text-muted fw-light">Payments /</span> Details
            </h4>
            <div class="btn-group">
                <a href="{{ route('admin.payments.receipt', $payment->id) }}" class="btn btn-outline-primary">
                    <i class="bx bx-printer me-1"></i> Generate Receipt
                </a>
                <a href="{{ route('admin.payments.index') }}" class="btn btn-outline-secondary">
                    <i class="bx bx-arrow-back me-1"></i> Back to Payments
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Payment Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-8">
                                <h5>Transaction #{{ $payment->id }}</h5>
                                <div class="badge {{ $payment->status === 'completed' ? 'bg-success' : 'bg-warning' }} mb-2">
                                    {{ ucfirst($payment->status) }}
                                </div>
                                <p class="mb-1">
                                    <i class="bx bx-time text-muted me-1"></i> 
                                    {{ $payment->created_at->format('M d, Y h:i A') }}
                                </p>
                            </div>
                            <div class="col-md-4 text-md-end">
                                <h5 class="text-primary mb-0">${{ number_format($payment->amount, 2) }}</h5>
                                <p class="text-muted mb-0">{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</p>
                            </div>
                        </div>

                        <hr>

                        <div class="row mb-3">
                            <div class="col-md-3 fw-bold">Transaction ID</div>
                            <div class="col-md-9">{{ $payment->transaction_id ?? 'N/A' }}</div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-3 fw-bold">Payment Date</div>
                            <div class="col-md-9">{{ $payment->payment_date ? $payment->payment_date->format('M d, Y h:i A') : $payment->created_at->format('M d, Y h:i A') }}</div>
                        </div>

                        @if($payment->invoice)
                            <div class="row mb-3">
                                <div class="col-md-3 fw-bold">Invoice</div>
                                <div class="col-md-9">
                                    <a href="{{ route('admin.payments.invoice.show', $payment->invoice_id) }}" class="text-primary">
                                        {{ $payment->invoice->invoice_number }}
                                    </a>
                                </div>
                            </div>
                        @endif

                        @if($payment->booking)
                            <div class="row mb-3">
                                <div class="col-md-3 fw-bold">Booking</div>
                                <div class="col-md-9">
                                    <a href="{{ route('admin.bookings.show', $payment->booking_id) }}" class="text-primary">
                                        Booking #{{ $payment->booking_id }}
                                    </a>
                                    <div class="text-muted small">
                                        {{ $payment->booking->date->format('M d, Y') }} | 
                                        {{ \Carbon\Carbon::parse($payment->booking->start_time)->format('h:i A') }} - 
                                        {{ \Carbon\Carbon::parse($payment->booking->end_time)->format('h:i A') }}
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-3 fw-bold">Service</div>
                                <div class="col-md-9">
                                    {{ $payment->booking->service->name ?? 'N/A' }}
                                    <div class="text-muted small">
                                        {{ $payment->booking->service->duration ?? '0' }} minutes
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="row mb-3">
                            <div class="col-md-3 fw-bold">Notes</div>
                            <div class="col-md-9">
                                {{ $payment->notes ?? 'No notes available' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Customer Information</h5>
                    </div>
                    <div class="card-body">
                        @if($payment->user)
                            <div class="d-flex align-items-start mb-4">
                                <div class="avatar avatar-md me-2">
                                    <span class="avatar-initial rounded-circle bg-primary">
                                        {{ substr($payment->user->name, 0, 1) }}
                                    </span>
                                </div>
                                <div>
                                    <h5 class="mb-1">{{ $payment->user->name }}</h5>
                                    <p class="text-muted mb-0">{{ $payment->user->email }}</p>
                                </div>
                            </div>
                            
                            <div class="row mb-2">
                                <div class="col-5 fw-semibold">Phone</div>
                                <div class="col-7">{{ $payment->user->phone ?? 'Not provided' }}</div>
                            </div>
                            
                            <div class="row mb-2">
                                <div class="col-5 fw-semibold">Address</div>
                                <div class="col-7">{{ $payment->user->address ?? 'Not provided' }}</div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-5 fw-semibold">Joined</div>
                                <div class="col-7">{{ $payment->user->created_at->format('M d, Y') }}</div>
                            </div>
                            
                            <a href="{{ route('admin.users.show', $payment->user->id) }}" class="btn btn-outline-primary btn-sm d-block">
                                <i class="bx bx-user me-1"></i> View Customer Profile
                            </a>
                        @else
                            <div class="text-center py-4">
                                <div class="avatar avatar-md mx-auto mb-3">
                                    <span class="avatar-initial rounded-circle bg-secondary">
                                        <i class="bx bx-user"></i>
                                    </span>
                                </div>
                                <p class="mb-0">Customer information not available</p>
                            </div>
                        @endif
                    </div>
                </div>

                @if($payment->invoice)
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Invoice Details</h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-2">
                                <div class="col-5 fw-semibold">Invoice #</div>
                                <div class="col-7">{{ $payment->invoice->invoice_number }}</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-5 fw-semibold">Date</div>
                                <div class="col-7">{{ $payment->invoice->created_at->format('M d, Y') }}</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-5 fw-semibold">Amount</div>
                                <div class="col-7">${{ number_format($payment->invoice->amount, 2) }}</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-5 fw-semibold">Status</div>
                                <div class="col-7">
                                    <span class="badge {{ $payment->invoice->status === 'paid' ? 'bg-success' : 'bg-warning' }}">
                                        {{ ucfirst($payment->invoice->status) }}
                                    </span>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-12">
                                    <a href="{{ route('admin.payments.invoice.show', $payment->invoice_id) }}" class="btn btn-primary btn-sm d-block">
                                        <i class="bx bx-file me-1"></i> View Full Invoice
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection