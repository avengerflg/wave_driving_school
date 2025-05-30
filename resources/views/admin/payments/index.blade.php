
@extends('layouts.admin')

@section('title', 'Payment Management')

@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endsection

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold m-0">
                <i class="bx bx-credit-card me-1 text-primary"></i> Payment Management
            </h4>
            <div>
                <a href="{{ route('admin.payments.invoices') }}" class="btn btn-outline-primary me-1">
                    <i class="bx bx-file me-1"></i> View Invoices
                </a>
                <a href="{{ route('admin.payments.invoice.create') }}" class="btn btn-primary">
                    <i class="bx bx-plus me-1"></i> Create New Invoice
                </a>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row mb-4">
            <div class="col-sm-4">
                <div class="card">
                    <div class="card-body d-flex align-items-center">
                        <div class="avatar rounded bg-label-primary p-3 me-3">
                            <i class="bx bx-dollar fs-4"></i>
                        </div>
                        <div>
                            <span class="fw-semibold d-block mb-1">Total Revenue</span>
                            <h4 class="card-title mb-0">${{ number_format($totalPayments, 2) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="card">
                    <div class="card-body d-flex align-items-center">
                        <div class="avatar rounded bg-label-success p-3 me-3">
                            <i class="bx bx-calendar fs-4"></i>
                        </div>
                        <div>
                            <span class="fw-semibold d-block mb-1">Today's Revenue</span>
                            <h4 class="card-title mb-0">${{ number_format($todayPayments, 2) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="card">
                    <div class="card-body d-flex align-items-center">
                        <div class="avatar rounded bg-label-info p-3 me-3">
                            <i class="bx bx-bar-chart fs-4"></i>
                        </div>
                        <div>
                            <span class="fw-semibold d-block mb-1">Monthly Revenue</span>
                            <h4 class="card-title mb-0">${{ number_format($monthlyPayments, 2) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Payment Records</h5>
            </div>
            <div class="card-body">
                <!-- Search and Filter -->
                <div class="row mb-4">
                    <div class="col-12">
                        <form method="GET" action="{{ route('admin.payments.index') }}" class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Search</label>
                                <input type="text" class="form-control" name="search" value="{{ $search ?? '' }}" placeholder="Transaction ID or customer name">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Status</label>
                                <select class="form-select" name="status">
                                    <option value="">All Statuses</option>
                                    <option value="completed" {{ ($status ?? '') == 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="pending" {{ ($status ?? '') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="failed" {{ ($status ?? '') == 'failed' ? 'selected' : '' }}>Failed</option>
                                    <option value="refunded" {{ ($status ?? '') == 'refunded' ? 'selected' : '' }}>Refunded</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Payment Method</label>
                                <select class="form-select" name="method">
                                    <option value="">All Methods</option>
                                    @foreach($paymentMethods as $method)
                                        <option value="{{ $method }}" {{ ($method ?? '') == $method ? 'selected' : '' }}>
                                            {{ ucfirst(str_replace('_', ' ', $method)) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Date Range</label>
                                <input type="text" class="form-control date-range-picker" name="date_range" value="{{ $dateRange ?? '' }}" placeholder="Select dates">
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <div class="d-flex">
                                    <button type="submit" class="btn btn-primary me-2">
                                        <i class="bx bx-search"></i>
                                    </button>
                                    <a href="{{ route('admin.payments.index') }}" class="btn btn-outline-secondary">
                                        <i class="bx bx-reset"></i>
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Payments Table -->
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Customer</th>
                                <th>Amount</th>
                                <th>Method</th>
                                <th>Status</th>
                                <th>Transaction ID</th>
                                <th>Invoice/Booking</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($payments as $payment)
                                <tr>
                                    <td>
                                        {{ $payment->created_at->format('M d, Y') }}
                                        <div class="text-muted small">{{ $payment->created_at->format('h:i A') }}</div>
                                    </td>
                                    <td>
                                        @if($payment->user)
                                            <a href="{{ route('admin.users.show', $payment->user->id) }}" class="fw-semibold text-body">
                                                {{ $payment->user->name }}
                                            </a>
                                            <div class="text-muted small">{{ $payment->user->email }}</div>
                                        @else
                                            <span class="text-muted">Unknown User</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="fw-semibold">${{ number_format($payment->amount, 2) }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-label-secondary">
                                            {{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $payment->status === 'completed' ? 'bg-success' : ($payment->status === 'failed' ? 'bg-danger' : 'bg-warning') }}">
                                            {{ ucfirst($payment->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="small">
                                            {{ $payment->transaction_id ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($payment->invoice)
                                            <a href="{{ route('admin.payments.invoice.show', $payment->invoice->id) }}" class="text-primary small">
                                                {{ $payment->invoice->invoice_number }}
                                            </a>
                                        @endif
                                        @if($payment->booking)
                                            <div class="mt-1">
                                                <a href="{{ route('admin.bookings.show', $payment->booking->id) }}" class="text-primary small">
                                                    Booking #{{ $payment->booking->id }}
                                                </a>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <div class="dropdown">
                                            <button type="button" class="btn btn-sm p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                <i class="bx bx-dots-vertical-rounded"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item" href="{{ route('admin.payments.show', $payment->id) }}">
                                                    <i class="bx bx-show me-1"></i> View Details
                                                </a>
                                                <a class="dropdown-item" href="{{ route('admin.payments.receipt', $payment->id) }}">
                                                    <i class="bx bx-printer me-1"></i> Generate Receipt
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-3">No payments found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-3">
                    {{ $payments->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize date range picker
        flatpickr('.date-range-picker', {
            mode: 'range',
            dateFormat: 'Y-m-d',
            altFormat: 'd M Y',
            conjunction: ' to ',
            allowInput: true
        });
    });
</script>
@endsection