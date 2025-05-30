@extends('layouts.admin')

@section('title', 'Manage Suburbs')

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">Admin /</span> Suburbs
        </h4>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">All Suburbs</h5>
                <div>
                    <a href="{{ route('admin.suburbs.create') }}" class="btn btn-primary">
                        <i class="bx bx-plus me-1"></i> Add New Suburb
                    </a>
                </div>
            </div>

            <div class="card-body">
                <!-- Filters -->
                <div class="row mb-4">
                    <div class="col-12">
                        <form method="GET" action="{{ route('admin.suburbs.index') }}" class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Search</label>
                                <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Search by name or postcode">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">State</label>
                                <select class="form-select" name="state">
                                    <option value="">All States</option>
                                    @foreach($states as $stateOption)
                                        <option value="{{ $stateOption }}" {{ request('state') == $stateOption ? 'selected' : '' }}>{{ $stateOption }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Status</label>
                                <select class="form-select" name="status">
                                    <option value="">All Status</option>
                                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="bx bx-search me-1"></i> Filter
                                </button>
                                <a href="{{ route('admin.suburbs.index') }}" class="btn btn-outline-secondary">
                                    <i class="bx bx-reset"></i>
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Import/Export Tools -->
                <div class="row mb-3">
                    <div class="col-12">
                        <div class="card bg-light">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">Data Tools</h6>
                                    <div>
                                        <a href="{{ route('admin.suburbs.export') }}" class="btn btn-outline-primary btn-sm">
                                            <i class="bx bx-export me-1"></i> Export CSV
                                        </a>
                                        <button type="button" class="btn btn-outline-secondary btn-sm ms-2" data-bs-toggle="modal" data-bs-target="#importModal">
                                            <i class="bx bx-import me-1"></i> Import CSV
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Suburbs Table -->
                <div class="table-responsive">
                    <table class="table table-striped table-hover border-top">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Suburb</th>
                                <th>State</th>
                                <th>Postcode</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($suburbs as $suburb)
                                <tr>
                                    <td>{{ $suburb->id }}</td>
                                    <td>{{ $suburb->name }}</td>
                                    <td>{{ $suburb->state }}</td>
                                    <td>{{ $suburb->postcode }}</td>
                                    <td>
                                        <span class="badge bg-label-{{ $suburb->active ? 'success' : 'danger' }}">
                                            {{ $suburb->active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                <i class="bx bx-dots-vertical-rounded"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item" href="{{ route('admin.suburbs.show', $suburb->id) }}">
                                                    <i class="bx bx-show-alt me-1"></i> View
                                                </a>
                                                <a class="dropdown-item" href="{{ route('admin.suburbs.edit', $suburb->id) }}">
                                                    <i class="bx bx-edit-alt me-1"></i> Edit
                                                </a>
                                                <a class="dropdown-item" href="javascript:void(0);" onclick="document.getElementById('toggle-form-{{ $suburb->id }}').submit()">
                                                    <i class="bx {{ $suburb->active ? 'bx-hide' : 'bx-show' }} me-1"></i>
                                                    {{ $suburb->active ? 'Deactivate' : 'Activate' }}
                                                </a>
                                                <form id="toggle-form-{{ $suburb->id }}" action="{{ route('admin.suburbs.toggle-status', $suburb->id) }}" method="POST" style="display: none;">
                                                    @csrf
                                                    @method('PATCH')
                                                </form>
                                                <div class="dropdown-divider"></div>
                                                <a class="dropdown-item text-danger" href="javascript:void(0);" onclick="confirmDelete({{ $suburb->id }})">
                                                    <i class="bx bx-trash me-1"></i> Delete
                                                </a>
                                                <form id="delete-form-{{ $suburb->id }}" action="{{ route('admin.suburbs.destroy', $suburb->id) }}" method="POST" style="display: none;">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">No suburbs found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-3">
                    {{ $suburbs->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Import Suburbs</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.suburbs.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">CSV File</label>
                        <input type="file" name="csv_file" class="form-control" required accept=".csv">
                        <small class="text-muted">
                            File should contain columns: Name, State, Postcode (in that order)
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Import</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function confirmDelete(id) {
        if (confirm('Are you sure you want to delete this suburb? This action cannot be undone.')) {
            document.getElementById('delete-form-' + id).submit();
        }
    }
</script>
@endsection