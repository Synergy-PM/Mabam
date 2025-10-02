@extends('admin.layouts.shared')
@section('title', 'Edit City')
@section('header-title', 'Edit City')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">

                <form action="{{ route('cities.update', $city->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label><b>City Name</b><span class="text-danger">*</span></label>
                            <input type="text" name="name" value="{{ old('name', $city->name) }}" class="form-control" placeholder="Enter City Name" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary"><b>Update City</b></button>
                    <a href="{{ route('cities.index') }}" class="btn btn-secondary">Cancel</a>
                </form>

            </div>
        </div>
    </div>
</div>
@endsection
