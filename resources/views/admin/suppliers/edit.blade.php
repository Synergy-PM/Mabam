@extends('admin.layouts.shared')
@section('title', 'Edit Supplier')
@section('header-title', 'Edit Supplier')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="card-header bg-light">
                    <h4 class="card-title mb-0">Edit Supplier</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('suppliers.update', $supplier->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label><b>Supplier Name</b></label>
                                <input type="text" name="supplier_name" class="form-control" placeholder="Enter Supplier Name" 
                                    value="{{ old('supplier_name', $supplier->supplier_name) }}">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label><b>Company Name</b></label>
                                <input type="text" name="company_name" class="form-control" placeholder="Enter Company Name"
                                    value="{{ old('company_name', $supplier->company_name) }}">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label><b>City</b></label>
                                <select name="city_id" class="form-control">
                                    <option value="">Select City</option>
                                    @foreach($cities as $city)
                                        <option value="{{ $city->id }}" {{ old('city_id', $supplier->city_id) == $city->id ? 'selected' : '' }}>
                                            {{ $city->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label><b>Email</b></label>
                                <input type="email" name="email" class="form-control" placeholder="Enter Email"
                                    value="{{ old('email', $supplier->email) }}">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label><b>WhatsApp</b></label>
                                <input type="text" name="whatsapp" class="form-control" placeholder="Enter WhatsApp Number"
                                    value="{{ old('whatsapp', $supplier->whatsapp) }}">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label><b>Contact Person</b></label>
                                <input type="text" name="contact_person" class="form-control" placeholder="Enter Contact Person Name"
                                    value="{{ old('contact_person', $supplier->contact_person) }}">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label><b>Contact No</b></label>
                                <input type="text" name="contact_no" class="form-control" placeholder="Enter Contact Number"
                                    value="{{ old('contact_no', $supplier->contact_no) }}">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label><b>Contact Email</b></label>
                                <input type="email" name="contact_email" class="form-control" placeholder="Enter Contact Email"
                                    value="{{ old('contact_email', $supplier->contact_email) }}">
                            </div>

                            <div class="col-md-12 mb-3">
                                <label><b>Address</b></label>
                                <textarea name="address" class="form-control" rows="3" placeholder="Enter Address">{{ old('address', $supplier->address) }}</textarea>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-success"><b>Update Supplier</b></button>
                        <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
