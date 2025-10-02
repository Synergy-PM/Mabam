@extends('admin.layouts.shared')
@section('title', 'Roles')
@section('header-title', 'Roles')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow-sm border-0">
                <div class="card-header d-flex justify-content-between align-items-center bg-light">
                    <h4 class="card-title mb-0">Roles List</h4>
                    <div class="d-flex gap-2">
                        @can('role_create')
                            <a href="{{ route('role.create') }}" class="btn btn-sm btn-primary">
                                <i class="mdi mdi-plus"></i> Create Role
                            </a>
                        @endcan
                        @can('role_trash_view')
                            <a href="{{ route('role.trash') }}" class="btn btn-sm btn-danger d-flex align-items-center gap-2"
                                title="Deleted Roles">
                                <i class="bi bi-trash-fill"></i>
                                <span>Trash</span>
                                <span class="badge bg-light text-dark">{{ $trashrole ?? 0 }}</span>
                            </a>
                        @endcan
                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table id="datatable-roles"
                            class="table table-hover align-middle table-striped table-bordered mb-0">
                            <thead class="table-light text-center">
                                <tr>
                                    <th style="width: 5%">#</th>
                                    <th style="width: 20%">Name</th>
                                    <th style="width: 20%">Created At</th>
                                    <th style="width: 15%">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($roles as $index => $role)
                                    <tr>
                                        <td class="text-center">{{ $index + 1 }}</td>
                                        <td class="fw-semibold">{{ $role->name }}</td>
                                        <td>{{ $role->created_at->format('d M Y') }}</td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-1">
                                                @can('role_edit')
                                                    <a href="{{ route('role.edit', $role->id) }}"
                                                        class="btn btn-sm btn-soft-warning" title="Edit">
                                                        <i class="mdi mdi-pencil"></i>
                                                    </a>
                                                @endcan
                                                @can('role_trash')
                                                    <form action="{{ route('role.delete', $role->id) }}" method="POST"
                                                        onsubmit="return confirm('Are you sure you want to delete this role?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-soft-danger" title="Delete">
                                                            <i class="mdi mdi-trash-can"></i>
                                                        </button>
                                                    </form>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">No roles found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        $('#datatable-roles').DataTable({
            "order": [[ 0, "asc" ]], // Default sort by first column (#)
            "pageLength": 10,
            "lengthMenu": [10, 25, 50, 100],
            "columnDefs": [
                { "orderable": false, "targets": 3 } // Disable sorting on Actions column
            ]
        });
    });
</script>
@endpush
