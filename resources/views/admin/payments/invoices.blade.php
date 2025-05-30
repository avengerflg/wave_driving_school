
@extends('layouts.admin')

@section('title', 'Invoice Management')

@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endsection

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold m-0">
                <i class="bx bx-file-find me-1 text-primary"></i> Invoice Management
            </h4>
            <div>
                <a href="{{ route('admin.payments.index') }}" class="btn btn-outline-primary me-1">
                    <i class="bx bx-credit-card me-1"></i> View Payments
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
                        <div class="avatar rounded bg-label-danger p-3 me-3">
                            <i class="bx bx-dollar fs-4"></i>
                        </div>
                        <div>
                            <span class="fw-semibold d-block mb-1">Outstanding Amount</span>
                            <h4 class="card-title mb-0">${{ number_format($totalUnpaid, 2) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="card">
                    <div class="card-body d-flex align-items-center">
                        <div class="avatar rounded bg-label-success p-3 me-3">
                            <i class="bx bx-check-circle fs-4"></i>
                        </div>
                        <div>
                            <span class="fw-semibold d-block mb-1">Paid Amount</span>
                            <h4 class="card-title mb-0">${{ number_format($totalPaid, 2) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="card">
                    <div class="card-body d-flex align-items-center">
                        <div class="avatar rounded bg-label-warning p-3 me-3">
                            <i class="bx bx-time fs-4"></i>
                        </div>
                        <div>
                            <span class="fw-semibold d-block mb-1">Overdue Invoices</span>
                            <h4 class="card-title mb-0">{{ $overdue }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">All Invoices</h5>
            </div>
            <div class="card-body">
                <!-- Search and Filter -->
                <div class="row mb-4">
                    <div class="col-12">
                        <form method="GET" action="{{ route('admin.payments.invoices') }}" class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Search</label>
                                <input type="text" class="form-control" name="search" value="{{ $search ?? '' }}" placeholder="Invoice # or customer name">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Status</label>
                                <select class="form-select" name="status">
                                    <option value="">All Statuses</option>
                                    <option value="pending" {{ ($status ?? '') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="paid" {{ ($status ?? '') == 'paid' ? 'selected' : '' }}>Paid</option>
                                    <option value="overdue" {{ ($status ?? '') == 'overdue' ? 'selected' : '' }}>Overdue</option>
                                    <option value="cancelled" {{ ($status ?? '') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
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
                                    <a href="{{ route('admin.payments.invoices') }}" class="btn btn-outline-secondary">
                                        <i class="bx bx-reset"></i>
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Invoices Table -->
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Invoice #</th>
                                <th>Customer</th>
                                <th>Date</th>
                                <th>Due Date</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($invoices as $invoice)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.payments.invoice.show', $invoice->id) }}" class="fw-semibold text-primary">
                                            {{ $invoice->invoice_number }}
                                        </a>
                                    </td>
                                    <td>
                                        @if($invoice->user)
                                            <div>{{ $invoice->user->name }}</div>
                                            <div class="text-muted small">{{ $invoice->user->email }}</div>
                                        @else
                                            <span class="text-muted">Unknown User</span>
                                        @endif
                                    </td>
                                    <td>{{ $invoice->created_at->format('M d, Y') }}</td>
                                    <td>
                                        {{ $invoice->due_date->format('M d, Y') }}
                                        @if($invoice->status === 'pending' && $invoice->due_date < now())
                                            <div class="badge bg-danger mt-1">Overdue</div>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="fw-semibold">${{ number_format($invoice->amount, 2) }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-label-{{ $invoice->status === 'paid' ? 'success' : ($invoice->status === 'cancelled' ? 'secondary' : ($invoice->status === 'pending' && $invoice->due_date < now() ? 'danger' : 'warning')) }}">
                                            {{ ucfirst($invoice->status) }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <div class="dropdown">
                                            <button type="button" class="btn btn-sm px-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                <i class="bx bx-dots-vertical-rounded"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item" href="{{ route('admin.payments.invoice.show', $invoice->id) }}">
                                                    <i class="bx bx-show me-1"></i> View Details
                                                </a>
                                                @if($invoice->status === 'pending')
                                                    <a class="dropdown-item" href="{{ route('admin.payments.invoice.edit', $invoice->id) }}">
                                                        <i class="bx bx-edit me-1"></i> Edit
                                                    </a>
                                                @endif
                                                <a class="dropdown-item" href="{{ route('admin.payments.invoice.pdf', $invoice->id) }}">
                                                    <i class="bx bx-download me-1"></i> Download PDF
                                                </a>
                                                @if($invoice->status === 'pending')
                                                    <a class="dropdown-item" href="{{ route('admin.payments.invoice.send', $invoice->id) }}" 
                                                       onclick="event.preventDefault(); document.getElementById('send-form-{{ $invoice->id }}').submit();">
                                                        <i class="bx bx-envelope me-1"></i> Send to Customer
                                                    </a>
                                                    <form id="send-form-{{ $invoice->id }}" action="{{ route('admin.payments.invoice.send', $invoice->id) }}" method="POST" style="display: none;">
                                                        @csrf
                                                    </form>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-3">No invoices found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-3">
                    {{ $invoices->withQueryString()->links() }}
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
            allowInput: true
        });
    });
</script>
@endsection