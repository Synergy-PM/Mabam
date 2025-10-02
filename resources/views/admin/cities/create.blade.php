@extends('admin.layouts.shared')
@section('title', 'Add City')
@section('header-title', 'Add City')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">

                <form action="{{ route('cities.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label><b>City Name</b><span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="Enter City Name" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary"><b>Save City</b></button>
                    <a href="{{ route('cities.index') }}" class="btn btn-secondary">Cancel</a>
                </form>

            </div>
        </div>
    </div>
</div>
@endsection
