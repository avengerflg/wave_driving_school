@extends('layouts.admin')

@section('title', 'Manage Packages')

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">Admin /</span> Packages
        </h4>
        
        <!-- Stats widgets -->
        <div class="row mb-4">
            <div class="col-md-3 col-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title mb-1 text-nowrap">Total Packages</h6>
                                <h4 class="fw-bold mb-0">{{ $stats['total'] }}</h4>
                            </div>
                            <div class="avatar">
                                <span class="avatar-initial rounded bg-label-primary">
                                    <i class="bx bx-package"></i>
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
                                <h6 class="card-title mb-1 text-nowrap">Active Packages</h6>
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
                <h5 class="mb-0">All Packages</h5>
                <div>
                    <a href="{{ route('admin.packages.orders') }}" class="btn btn-outline-primary me-2">
                        <i class="bx bx-shopping-bag me-1"></i> View Orders
                    </a>
                    <a href="{{ route('admin.packages.create') }}" class="btn btn-primary">
                        <i class="bx bx-plus me-1"></i> Add New Package
                    </a>
                </div>
            </div>

            <div class="card-body">
                <!-- Filters -->
                <div class="row mb-4">
                    <div class="col-md-8">
                        <form method="GET" action="{{ route('admin.packages.index') }}" class="d-flex gap-3">
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
                                <a href="{{ route('admin.packages.index') }}" class="btn btn-outline-secondary">
                                    <i class="bx bx-reset"></i>
                                </a>
                            @endif
                        </form>
                    </div>
                </div>

                <!-- Packages Table -->
                <div class="table-responsive">
                    <table class="table table-hover border-top">
                        <thead>
                            <tr>
                                <th width="30px">
                                    <input type="checkbox" class="form-check-input" id="select-all">
                                </th>
                                <th>
                                    <a href="{{ route('admin.packages.index', ['sort' => 'name', 'direction' => $sortField === 'name' && $sortDirection === 'asc' ? 'desc' : 'asc', 'search' => request('search'), 'status' => request('status')]) }}" class="text-body d-flex align-items-center">
                                        Name
                                        @if($sortField === 'name')
                                            <i class="bx {{ $sortDirection === 'asc' ? 'bx-sort-up' : 'bx-sort-down' }} ms-1"></i>
                                        @endif
                                    </a>
                                </th>
                                <th>Description</th>
                                <th>
                                    <a href="{{ route('admin.packages.index', ['sort' => 'price', 'direction' => $sortField === 'price' && $sortDirection === 'asc' ? 'desc' : 'asc', 'search' => request('search'), 'status' => request('status')]) }}" class="text-body d-flex align-items-center">
                                        Price
                                        @if($sortField === 'price')
                                            <i class="bx {{ $sortDirection === 'asc' ? 'bx-sort-up' : 'bx-sort-down' }} ms-1"></i>
                                        @endif
                                    </a>
                                </th>
                                <th>
                                    <a href="{{ route('admin.packages.index', ['sort' => 'lessons', 'direction' => $sortField === 'lessons' && $sortDirection === 'asc' ? 'desc' : 'asc', 'search' => request('search'), 'status' => request('status')]) }}" class="text-body d-flex align-items-center">
                                        Lessons
                                        @if($sortField === 'lessons')
                                            <i class="bx {{ $sortDirection === 'asc' ? 'bx-sort-up' : 'bx-sort-down' }} ms-1"></i>
                                        @endif
                                    </a>
                                </th>
                                <th>
                                    <a href="{{ route('admin.packages.index', ['sort' => 'duration', 'direction' => $sortField === 'duration' && $sortDirection === 'asc' ? 'desc' : 'asc', 'search' => request('search'), 'status' => request('status')]) }}" class="text-body d-flex align-items-center">
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
                            @forelse($packages as $package)
                                <tr>
                                    <td>
                                        <input type="checkbox" class="form-check-input package-checkbox" value="{{ $package->id }}">
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            {{ $package->name }}
                                            @if($package->featured)
                                                <span class="badge rounded-pill bg-warning ms-2">Featured</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-truncate d-inline-block" style="max-width: 250px;">
                                            {{ $package->description ?: 'No description' }}
                                        </span>
                                    </td>
                                    <td>${{ number_format($package->price, 2) }}</td>
                                    <td>{{ $package->lessons }}</td>
                                    <td>{{ $package->duration }} min</td>
                                    <td>
                                        <span class="badge bg-label-{{ $package->active ? 'success' : 'danger' }}">
                                            {{ $package->active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                <i class="bx bx-dots-vertical-rounded"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item" href="{{ route('admin.packages.show', $package->id) }}">
                                                    <i class="bx bx-show-alt me-1"></i> View
                                                </a>
                                                <a class="dropdown-item" href="{{ route('admin.packages.edit', $package->id) }}">
                                                    <i class="bx bx-edit-alt me-1"></i> Edit
                                                </a>
                                                <a class="dropdown-item" href="javascript:void(0);" onclick="document.getElementById('toggle-status-form-{{ $package->id }}').submit()">
                                                    <i class="bx {{ $package->active ? 'bx-hide' : 'bx-show' }} me-1"></i>
                                                    {{ $package->active ? 'Deactivate' : 'Activate' }}
                                                </a>
                                                <form id="toggle-status-form-{{ $package->id }}" action="{{ route('admin.packages.toggle-status', $package->id) }}" method="POST" style="display: none;">
                                                    @csrf
                                                    @method('PATCH')
                                                </form>
                                                <a class="dropdown-item" href="javascript:void(0);" onclick="document.getElementById('toggle-featured-form-{{ $package->id }}').submit()">
                                                    <i class="bx {{ $package->featured ? 'bx-star' : 'bx-star' }} me-1"></i>
                                                    {{ $package->featured ? 'Unfeature' : 'Feature' }}
                                                </a>
                                                <form id="toggle-featured-form-{{ $package->id }}" action="{{ route('admin.packages.toggle-featured', $package->id) }}" method="POST" style="display: none;">
                                                    @csrf
                                                    @method('PATCH')
                                                </form>
                                                <div class="dropdown-divider"></div>
                                                <a class="dropdown-item text-danger" href="javascript:void(0);" onclick="confirmDelete({{ $package->id }})">
                                                    <i class="bx bx-trash me-1"></i> Delete
                                                </a>
                                                <form id="delete-form-{{ $package->id }}" action="{{ route('admin.packages.destroy', $package->id) }}" method="POST" style="display: none;">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">No packages found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-3">
                    {{ $packages->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Confirm delete
    function confirmDelete(id) {
        if (confirm('Are you sure you want to delete this package? This action cannot be undone.')) {
            document.getElementById('delete-form-' + id).submit();
        }
    }

    // Bulk selection
    document.addEventListener('DOMContentLoaded', function() {
        const selectAllCheckbox = document.getElementById('select-all');
        const packageCheckboxes = document.querySelectorAll('.package-checkbox');
        
        // Select all checkbox
        selectAllCheckbox.addEventListener('change', function() {
            packageCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });

        // Individual checkboxes
        packageCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                // Check if all checkboxes are checked
                const allChecked = Array.from(packageCheckboxes).every(cb => cb.checked);
                const someChecked = Array.from(packageCheckboxes).some(cb => cb.checked);
                
                selectAllCheckbox.checked = allChecked;
                selectAllCheckbox.indeterminate = someChecked && !allChecked;
            });
        });
    });
</script>
@endsection