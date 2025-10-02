@extends('admin.layouts.shared')
@section('title', 'Create Company')
@section('header-title', 'Create Company')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">

                <form action="{{ route('companies.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label><b>Name</b><span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="Enter Company Name" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label><b>Email</b></label>
                            <input type="email" name="email" class="form-control" placeholder="Enter Email">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label><b>Phone</b></label>
                            <input type="text" name="phone" class="form-control" placeholder="Enter Phone Number">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label><b>Website</b></label>
                            <input type="text" name="website" class="form-control" placeholder="Enter Website URL">
                        </div>

                        <div class="col-md-12 mb-3">
                            <label><b>Address</b></label>
                            <textarea name="address" class="form-control" rows="3" placeholder="Enter Address"></textarea>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary"><b>Save Company</b></button>
                    <a href="{{ route('companies.index') }}" class="btn btn-secondary">Cancel</a>
                </form>

            </div>
        </div>
    </div>
</div>
@endsection
