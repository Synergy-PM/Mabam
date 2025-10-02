@extends('admin.layouts.shared')
@section('title', 'Roles Trash')
@section('header-title', 'Roles Trash')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-body">

                <h4 class="mb-3"><b>Deleted Roles</b></h4>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Deleted At</th>
                                <th width="20%">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($roles as $index => $role)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $role->name }}</td>
                                    <td>{{ $role->deleted_at->format('d M Y h:i A') }}</td>
                                    <td>
                                        <form action="{{ route('role.restore', $role->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="btn btn-success btn-sm">
                                                <i class="bi bi-arrow-counterclockwise"></i> Restore
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">No deleted roles found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    <a href="{{ route('role.index') }}" class="btn btn-secondary"><b>Back to Roles</b></a>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
