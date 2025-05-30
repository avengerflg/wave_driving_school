
@extends('layouts.admin')

@section('title', 'Manage Services')

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">Admin /</span> Services
        </h4>
        
        <!-- Stats widgets -->
        <div class="row mb-4">
            <div class="col-md-3 col-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title mb-1 text-nowrap">Total Services</h6>
                                <h4 class="fw-bold mb-0">{{ $stats['total'] }}</h4>
                            </div>
                            <div class="avatar">
                                <span class="avatar-initial rounded bg-label-primary">
                                    <i class="bx bx-list-ul"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title mb-1 text-nowrap">Active Services</h6>
                                <h4 class="fw-bold text-success mb-0">{{ $stats['active'] }}</h4>
                            </div>
                            <div class="avatar">
                                <span class="avatar-initial rounded bg-label-success">
                                    <i class="bx bx-check-circle"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title mb-1 text-nowrap">Price Range</h6>
                                <h4 class="fw-bold text-primary mb-0">${{ $stats['min_price'] }} - ${{ $stats['max_price'] }}</h4>
                            </div>
                            <div class="avatar">
                                <span class="avatar-initial rounded bg-label-info">
                                    <i class="bx bx-dollar"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title mb-1 text-nowrap">Average Price</h6>
                                <h4 class="fw-bold text-info mb-0">${{ $stats['avg_price'] }}</h4>
                            </div>
                            <div class="avatar">
                                <span class="avatar-initial rounded bg-label-warning">
                                    <i class="bx bx-line-chart"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">All Services</h5>
                <div>
                    <a href="{{ route('admin.services.create') }}" class="btn btn-primary">
                        <i class="bx bx-plus me-1"></i> Add New Service
                    </a>
                </div>
            </div>

            <div class="card-body">
                <!-- Filters -->
                <div class="row mb-4">
                    <div class="col-md-8">
                        <form method="GET" action="{{ route('admin.services.index') }}" class="d-flex gap-3">
                            <div class="input-group">
                                <span class="input-group-text"><i class="bx bx-search"></i></span>
                                <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Search by name or description">
                            </div>
                            <select class="form-select w-px-150" name="status">
                                <option value="">All Status</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            <input type="hidden" name="sort" value="{{ $sortField }}">
                            <input type="hidden" name="direction" value="{{ $sortDirection }}">
                            <button type="submit" class="btn btn-primary">Filter</button>
                            @if(request('search') || request('status'))
                                <a href="{{ route('admin.services.index') }}" class="btn btn-outline-secondary">
                                    <i class="bx bx-reset"></i>
                                </a>
                            @endif
                        </form>
                    </div>
                    <div class="col-md-4 text-end">
                        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#bulkUpdateModal">
                            <i class="bx bx-dollar-circle me-1"></i> Bulk Update Prices
                        </button>
                    </div>
                </div>

                <!-- Services Table -->
                <div class="table-responsive">
                    <table class="table table-hover border-top">
                        <thead>
                            <tr>
                                <th width="30px">
                                    <input type="checkbox" class="form-check-input" id="select-all">
                                </th>
                                <th>
                                    <a href="{{ route('admin.services.index', ['sort' => 'name', 'direction' => $sortField === 'name' && $sortDirection === 'asc' ? 'desc' : 'asc', 'search' => request('search'), 'status' => request('status')]) }}" class="text-body d-flex align-items-center">
                                        Name
                                        @if($sortField === 'name')
                                            <i class="bx {{ $sortDirection === 'asc' ? 'bx-sort-up' : 'bx-sort-down' }} ms-1"></i>
                                        @endif
                                    </a>
                                </th>
                                <th>Description</th>
                                <th>
                                    <a href="{{ route('admin.services.index', ['sort' => 'price', 'direction' => $sortField === 'price' && $sortDirection === 'asc' ? 'desc' : 'asc', 'search' => request('search'), 'status' => request('status')]) }}" class="text-body d-flex align-items-center">
                                        Price
                                        @if($sortField === 'price')
                                            <i class="bx {{ $sortDirection === 'asc' ? 'bx-sort-up' : 'bx-sort-down' }} ms-1"></i>
                                        @endif
                                    </a>
                                </th>
                                <th>
                                    <a href="{{ route('admin.services.index', ['sort' => 'duration', 'direction' => $sortField === 'duration' && $sortDirection === 'asc' ? 'desc' : 'asc', 'search' => request('search'), 'status' => request('status')]) }}" class="text-body d-flex align-items-center">
                                        Duration
                                        @if($sortField === 'duration')
                                            <i class="bx {{ $sortDirection === 'asc' ? 'bx-sort-up' : 'bx-sort-down' }} ms-1"></i>
                                        @endif
                                    </a>
                                </th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($services as $service)
                                <tr>
                                    <td>
                                        <input type="checkbox" class="form-check-input service-checkbox" value="{{ $service->id }}">
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            {{ $service->name }}
                                            @if($service->featured)
                                                <span class="badge rounded-pill bg-warning ms-2">Featured</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-truncate d-inline-block" style="max-width: 250px;">
                                            {{ $service->description ?: 'No description' }}
                                        </span>
                                    </td>
                                    <td>${{ number_format($service->price, 2) }}</td>
                                    <td>{{ $service->formatted_duration }}</td>
                                    <td>
                                        <span class="badge bg-label-{{ $service->active ? 'success' : 'danger' }}">
                                            {{ $service->active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                <i class="bx bx-dots-vertical-rounded"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item" href="{{ route('admin.services.show', $service->id) }}">
                                                    <i class="bx bx-show-alt me-1"></i> View
                                                </a>
                                                <a class="dropdown-item" href="{{ route('admin.services.edit', $service->id) }}">
                                                    <i class="bx bx-edit-alt me-1"></i> Edit
                                                </a>
                                                <a class="dropdown-item" href="javascript:void(0);" onclick="document.getElementById('toggle-status-form-{{ $service->id }}').submit()">
                                                    <i class="bx {{ $service->active ? 'bx-hide' : 'bx-show' }} me-1"></i>
                                                    {{ $service->active ? 'Deactivate' : 'Activate' }}
                                                </a>
                                                <form id="toggle-status-form-{{ $service->id }}" action="{{ route('admin.services.toggle-status', $service->id) }}" method="POST" style="display: none;">
                                                    @csrf
                                                    @method('PATCH')
                                                </form>
                                                <a class="dropdown-item" href="javascript:void(0);" onclick="document.getElementById('toggle-featured-form-{{ $service->id }}').submit()">
                                                    <i class="bx {{ $service->featured ? 'bx-star' : 'bx-star' }} me-1"></i>
                                                    {{ $service->featured ? 'Unfeature' : 'Feature' }}
                                                </a>
                                                <form id="toggle-featured-form-{{ $service->id }}" action="{{ route('admin.services.toggle-featured', $service->id) }}" method="POST" style="display: none;">
                                                    @csrf
                                                    @method('PATCH')
                                                </form>
                                                <div class="dropdown-divider"></div>
                                                <a class="dropdown-item text-danger" href="javascript:void(0);" onclick="confirmDelete({{ $service->id }})">
                                                    <i class="bx bx-trash me-1"></i> Delete
                                                </a>
                                                <form id="delete-form-{{ $service->id }}" action="{{ route('admin.services.destroy', $service->id) }}" method="POST" style="display: none;">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">No services found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-3">
                    {{ $services->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Update Modal -->
<div class="modal fade" id="bulkUpdateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Bulk Update Prices</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.services.bulk-update-prices') }}" method="POST" id="bulkUpdateForm">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <div class="d-flex">
                            <i class="bx bx-info-circle me-2 mt-1"></i>
                            <div>
                                <p class="mb-0">Select services from the list before updating prices.</p>
                            </div>
                        </div>
                    </div>

                    <div id="no-services-selected" class="alert alert-warning d-flex mb-4">
                        <i class="bx bx-error me-2 mt-1"></i>
                        <div>No services selected</div>
                    </div>

                    <div id="services-selected" class="d-none">
                        <div class="mb-3">
                            <label class="form-label">Adjust prices by:</label>
                            <div class="d-flex">
                                <select class="form-select me-3" name="adjustment_type">
                                    <option value="percentage">Percentage (%)</option>
                                    <option value="fixed">Fixed amount ($)</option>
                                </select>
                                <input type="number" class="form-control" name="adjustment_value" placeholder="Value" step="0.01" required>
                            </div>
                            <small class="text-muted mt-1 d-block">
                                Positive values increase prices, negative values decrease prices.
                            </small>
                        </div>
                        
                        <div class="alert alert-warning mt-3">
                            <div class="d-flex">
                                <i class="bx bx-error-circle me-2 mt-1"></i>
                                <div>
                                    <strong>Warning:</strong> This action will update prices for all selected services.
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Hidden input for selected services -->
                    <div id="selected-services-container"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="update-prices-btn" disabled>Update Prices</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Confirm delete
    function confirmDelete(id) {
        if (confirm('Are you sure you want to delete this service? This action cannot be undone.')) {
            document.getElementById('delete-form-' + id).submit();
        }
    }

    // Bulk selection
    document.addEventListener('DOMContentLoaded', function() {
        const selectAllCheckbox = document.getElementById('select-all');
        const serviceCheckboxes = document.querySelectorAll('.service-checkbox');
        const noServicesSelected = document.getElementById('no-services-selected');
        const servicesSelected = document.getElementById('services-selected');
        const updatePricesBtn = document.getElementById('update-prices-btn');
        const selectedServicesContainer = document.getElementById('selected-services-container');
        const bulkUpdateForm = document.getElementById('bulkUpdateForm');

        // Select all checkbox
        selectAllCheckbox.addEventListener('change', function() {
            serviceCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateBulkUI();
        });

        // Individual checkboxes
        serviceCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateBulkUI);
        });

        // Update bulk UI based on selections
        function updateBulkUI() {
            // Count selected services
            const selectedCount = Array.from(serviceCheckboxes).filter(cb => cb.checked).length;
            
            if (selectedCount > 0) {
                noServicesSelected.classList.add('d-none');
                servicesSelected.classList.remove('d-none');
                updatePricesBtn.disabled = false;
                updatePricesBtn.textContent = `Update Prices (${selectedCount} services)`;
            } else {
                noServicesSelected.classList.remove('d-none');
                servicesSelected.classList.add('d-none');
                updatePricesBtn.disabled = true;
                updatePricesBtn.textContent = 'Update Prices';
            }
            
            // Update select all checkbox state
            selectAllCheckbox.checked = selectedCount > 0 && selectedCount === serviceCheckboxes.length;
            selectAllCheckbox.indeterminate = selectedCount > 0 && selectedCount < serviceCheckboxes.length;
        }

        // Update selected services before form submission
        bulkUpdateForm.addEventListener('submit', function(e) {
            // Clear existing hidden inputs
            selectedServicesContainer.innerHTML = '';
            
            // Get selected service IDs
            const selectedServices = Array.from(serviceCheckboxes)
                .filter(cb => cb.checked)
                .map(cb => cb.value);
            
            if (selectedServices.length === 0) {
                e.preventDefault();
                alert('Please select at least one service to update.');
                return;
            }
            
            // Add hidden inputs for each selected service
            selectedServices.forEach(serviceId => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'selected_services[]';
                input.value = serviceId;
                selectedServicesContainer.appendChild(input);
            });
        });
    });
</script>
@endsection