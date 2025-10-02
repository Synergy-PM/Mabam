@extends('admin.layouts.shared')
@section('title', 'Edit User')
@section('header-title', 'Edit User')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">

                <form action="{{ route('user.update', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label><b>Name</b><span class="text-danger">*</span></label>
                            <input type="text" name="name" value="{{ $user->name }}" class="form-control" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label><b>Email</b><span class="text-danger">*</span></label>
                            <input type="email" name="email" value="{{ $user->email }}" class="form-control" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label><b>Password</b> (leave blank if not changing)</label>
                            <input type="password" name="password" class="form-control" placeholder="New Password">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label><b>Status</b><span class="text-danger">*</span></label>
                            <select name="status" class="form-control" required>
                                <option value="active" {{ $user->status == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ $user->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label><b>Department</b><span class="text-danger">*</span></label>
                            <input type="text" name="department" value="{{ $user->department }}" class="form-control" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label><b>Roles</b><span class="text-danger">*</span></label>
                            <select name="roles[]" class="form-control" multiple required>
                                @foreach($roles as $role)
                                    <option value="{{ $role->name }}" {{ $user->roles->contains('name', $role->name) ? 'selected' : '' }}>
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary"><b>Update User</b></button>
                </form>

            </div>
        </div>
    </div>
</div>
@endsection
