@extends('admin.layouts.shared')
@section('title', 'Create User')
@section('header-title', 'Create User')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">

                <form action="{{ route('user.store') }}" method="POST">
                    @csrf

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label><b>Name</b><span class="text-danger">*</span></label>
                            <input type="text" name="name" value="{{ old('name') }}" class="form-control" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label><b>Email</b><span class="text-danger">*</span></label>
                            <input type="email" name="email" value="{{ old('email') }}" class="form-control" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label><b>Password</b><span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control" placeholder="Password" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label><b>Status</b><span class="text-danger">*</span></label>
                            <select name="status" class="form-control" required>
                                <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label><b>Department</b><span class="text-danger">*</span></label>
                            <input type="text" name="department" value="{{ old('department') }}" class="form-control" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label><b>Roles</b><span class="text-danger">*</span></label>
                            <select name="roles[]" class="form-control" multiple required>
                                @foreach($roles as $role)
                                    <option value="{{ $role->name }}" {{ collect(old('roles'))->contains($role->name) ? 'selected' : '' }}>
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary"><b>Create User</b></button>
                </form>

            </div>
        </div>
    </div>
</div>
@endsection
