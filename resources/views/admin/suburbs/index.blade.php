@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Manage Suburbs</h1>
        <div>
            <button type="button" class="btn btn-outline-primary me-2" data-bs-toggle="modal" data-bs-target="#importModal">
                <i class="fas fa-file-import"></i> Import Suburbs
            </button>
            <a href="{{ route('admin.suburbs.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add Suburb
            </a>
        </div>
    </div>
    
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6">
                    <form action="{{ route('admin.suburbs.index') }}" method="GET" class="d-flex">
                        <input type="text" name="search" class="form-control" placeholder="Search suburbs..." value="{{ request('search') }}">
                        <button type="submit" class="btn btn-outline-primary ms-2">Search</button>
                    </form>
                </div>
                <div class="col-md-6 text-end">
                    <div class="btn-group">
                        <a href="{{ route('admin.suburbs.index') }}" class="btn btn-outline-secondary {{ !request('filter') ? 'active' : '' }}">All</a>
                        <a href="{{ route('admin.suburbs.index', ['filter' => 'active']) }}" class="btn btn-outline-secondary {{ request('filter') === 'active' ? 'active' : '' }}">Active</a>
                        <a href="{{ route('admin.suburbs.index', ['filter' => 'inactive']) }}" class="btn btn-outline-secondary {{ request('filter') === 'inactive' ? 'active' : '' }}">Inactive</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>State</th>
                            <th>Postcode</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($suburbs as $suburb)
                            <tr>
                                <td>{{ $suburb->name }}</td>
                                <td>{{ $suburb->state }}</td>
                                <td>{{ $suburb->postcode }}</td>
                                <td>
                                    @if($suburb->active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('admin.suburbs.edit', $suburb) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $suburb->id }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                    
                                    <!-- Delete Modal -->
                                    <div class="modal fade" id="deleteModal{{ $suburb->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $suburb->id }}" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="deleteModalLabel{{ $suburb->id }}">Confirm Delete</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    Are you sure you want to delete the suburb <strong>{{ $suburb->name }}</strong>?
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <form action="{{ route('admin.suburbs.destroy', $suburb) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger">Delete</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No suburbs found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-center mt-4">
                {{ $suburbs->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importModalLabel">Import Suburbs</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.suburbs.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="file" class="form-label">Select CSV or Excel file</label>
                        <input type="file" class="form-control" id="file" name="file" required>
                        <div class="form-text">
                            File should have columns: name, state, postcode, active (optional)
                        </div>
                    </div>
                    <div class="alert alert-info">
                        <strong>Note:</strong> The first row should be the column headers.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Import</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

