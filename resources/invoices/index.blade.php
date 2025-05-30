@extends('layouts.app')

@section('title', 'My Invoices')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3">My Invoices</h1>
                <a href="{{ route('student.dashboard') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-body">
                    @if($invoices->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Invoice #</th>
                                        <th>Date</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Due Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($invoices as $invoice)
                                        <tr>
                                            <td>
                                                <strong>{{ $invoice->invoice_number }}</strong>
                                            </td>
                                            <td>{{ $invoice->created_at->format('M d, Y') }}</td>
                                            <td>${{ number_format($invoice->amount, 2) }}</td>
                                            <td>
                                                <span class="badge {{ $invoice->status_badge_class }}">
                                                    {{ ucfirst($invoice->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                {{ $invoice->due_date ? $invoice->due_date->format('M d, Y') : 'N/A' }}
                                            </td>
                                            <td>
                                                <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                                <a href="{{ route('invoices.download', $invoice) }}" class="btn btn-sm btn-success">
                                                    <i class="fas fa-download"></i> PDF
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{ $invoices->links() }}
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-file-invoice fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No invoices found</h5>
                            <p class="text-muted">Your invoices will appear here after you make bookings.</p>
                            <a href="{{ route('booking.index') }}" class="btn btn-primary">Book a Lesson</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
