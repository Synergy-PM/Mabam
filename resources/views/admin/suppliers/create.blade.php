@extends('admin.layouts.shared')
@section('title', isset($supplier) ? 'Edit Supplier' : 'Add Supplier')
@section('header-title', isset($supplier) ? 'Edit Supplier' : 'Add Supplier')

@section('content')
<div class="container-fluid">
    <form action="{{ isset($supplier) ? route('suppliers.update', $supplier->id) : route('suppliers.store') }}" method="POST">
        @csrf
        @if(isset($supplier))
            @method('PUT')
        @endif

        <div class="row">
            <div class="col-md-6 mb-3">
                <label><b>Supplier Name</b></label>
                <input type="text" name="supplier_name" class="form-control" placeholder="Enter Supplier Name" 
                    value="{{ $supplier->supplier_name ?? old('supplier_name') }}">
            </div>

            <div class="col-md-6 mb-3">
                <label><b>Company Name</b></label>
                <input type="text" name="company_name" class="form-control" placeholder="Enter Company Name"
                    value="{{ $supplier->company_name ?? old('company_name') }}">
            </div>

            <div class="col-md-6 mb-3">
                <label><b>City</b></label>
                <select name="city_id" class="form-control">
                    <option value="">Select City</option>
                    @foreach($cities as $city)
                        <option value="{{ $city->id }}" {{ (isset($supplier) && $supplier->city_id == $city->id) ? 'selected' : '' }}>
                            {{ $city->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6 mb-3">
                <label><b>Email</b></label>
                <input type="email" name="email" class="form-control" placeholder="Enter Email"
                    value="{{ $supplier->email ?? old('email') }}">
            </div>

            <div class="col-md-6 mb-3">
                <label><b>WhatsApp</b></label>
                <input type="text" name="whatsapp" class="form-control" placeholder="Enter WhatsApp Number"
                    value="{{ $supplier->whatsapp ?? old('whatsapp') }}">
            </div>

            <div class="col-md-6 mb-3">
                <label><b>Contact Person</b></label>
                <input type="text" name="contact_person" class="form-control" placeholder="Enter Contact Person Name"
                    value="{{ $supplier->contact_person ?? old('contact_person') }}">
            </div>

            <div class="col-md-6 mb-3">
                <label><b>Contact No</b></label>
                <input type="text" name="contact_no" class="form-control" placeholder="Enter Contact Number"
                    value="{{ $supplier->contact_no ?? old('contact_no') }}">
            </div>

            <div class="col-md-6 mb-3">
                <label><b>Contact Email</b></label>
                <input type="email" name="contact_email" class="form-control" placeholder="Enter Contact Email"
                    value="{{ $supplier->contact_email ?? old('contact_email') }}">
            </div>

            <div class="col-md-12 mb-3">
                <label><b>Address</b></label>
                <textarea name="address" class="form-control" rows="3" placeholder="Enter Address">{{ $supplier->address ?? old('address') }}</textarea>
            </div>
        </div>

        <button type="submit" class="btn btn-success">{{ isset($supplier) ? 'Update Supplier' : 'Save Supplier' }}</button>
        <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
