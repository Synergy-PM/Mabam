@extends('admin.layouts.shared')
@section('title', 'Users')
@section('header-title', 'Users')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow-sm border-0">
                <div class="card-header d-flex justify-content-between align-items-center bg-light">
                    <h4 class="card-title mb-0">Users List</h4>
                    <div class="d-flex gap-2">
                        @can('user_create')
                            <a href="{{ route('user.create') }}" class="btn btn-sm btn-primary">
                                <i class="mdi mdi-plus"></i> Add User
                            </a>
                        @endcan
                        @can('user_trash_view')
                            <a href="#" class="btn btn-sm btn-danger d-flex align-items-center gap-2"
                                title="Deleted Users">
                                <i class="bi bi-trash-fill"></i>
                                <span>Trash</span>
                                <span class="badge bg-light text-dark">{{ $trashuser ?? 0 }}</span>
                            </a>
                        @endcan
                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table id="datatable-buttons"
                            class="table table-hover align-middle table-striped table-bordered mb-0">
                            <thead class="table-light text-center">
                                <tr>
                                    <th style="width: 5%">#</th>
                                    <th style="width: 15%">Name</th>
                                    <th style="width: 20%">Email</th>
                                    <th style="width: 10%">Status</th>
                                    <th style="width: 10%">Dept</th>
                                    <th style="width: 15%">Roles</th>
                                    <th style="width: 15%">Created</th>
                                    <th style="width: 10%">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($users as $index => $user)
                                    <tr>
                                        <td class="text-center">{{ $index + 1 }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-xs me-2">
                                                    <span class="avatar-title rounded-circle bg-primary text-white fw-bold">
                                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                                    </span>
                                                </div>
                                                <span class="fw-semibold">{{ $user->name }}</span>
                                            </div>
                                        </td>
                                        <td>{{ $user->email }}</td>
                                        <td>
                                            <span class="badge bg-{{ $user->status == 'active' ? 'success' : 'danger' }}">
                                                {{ ucfirst($user->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $user->department }}</td>
                                        <td>
                                            @foreach ($user->roles as $role)
                                                <span class="badge bg-info">{{ $role->name }}</span>
                                            @endforeach
                                        </td>
                                        <td>{{ $user->created_at->format('d M Y') }}</td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-1">
                                                @can('user_edit')
                                                    <a href="{{ route('user.edit', $user->id) }}"
                                                        class="btn btn-sm btn-soft-warning" title="Edit">
                                                        <i class="mdi mdi-pencil"></i>
                                                    </a>
                                                @endcan
                                                @can('user_trash')
                                                    <form action="{{ route('user.delete', $user->id) }}" method="POST"
                                                        onsubmit="return confirm('Are you sure you want to delete this user?')">
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
                                        <td colspan="8" class="text-center text-muted">No users found.</td>
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
