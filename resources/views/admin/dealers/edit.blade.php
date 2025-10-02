@extends('admin.layouts.shared')
@section('title', 'Edit Dealer')
@section('header-title', 'Edit Dealer')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">

               <form action="{{ route('dealers.update', $dealer->id) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="row">
        <div class="col-md-6 mb-3">
            <label><b>Dealer Name</b></label>
            <input type="text" name="dealer_name" class="form-control" 
                placeholder="Enter Dealer Name"
                value="{{ old('dealer_name', $dealer->dealer_name) }}">
        </div>

        <div class="col-md-6 mb-3">
            <label><b>Company Name</b></label>
            <input type="text" name="company_name" class="form-control" 
                placeholder="Enter Company Name"
                value="{{ old('company_name', $dealer->company_name) }}">
        </div>

        <div class="col-md-6 mb-3">
            <label><b>City</b></label>
            <select name="city_id" class="form-control">
                <option value="">Select City</option>
                @foreach($cities as $city)
                    <option value="{{ $city->id }}" @if($dealer->city_id == $city->id) selected @endif>
                        {{ $city->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-6 mb-3">
            <label><b>Email</b></label>
            <input type="email" name="email" class="form-control" 
                value="{{ old('email', $dealer->email) }}">
        </div>

        <div class="col-md-6 mb-3">
            <label><b>WhatsApp</b></label>
            <input type="text" name="whatsapp" class="form-control" 
                value="{{ old('whatsapp', $dealer->whatsapp) }}">
        </div>

        <div class="col-md-6 mb-3">
            <label><b>Contact Person</b></label>
            <input type="text" name="contact_person" class="form-control" 
                value="{{ old('contact_person', $dealer->contact_person) }}">
        </div>

        <div class="col-md-3 mb-3">
            <label><b>Contact No</b></label>
            <input type="text" name="contact_no" class="form-control" 
                value="{{ old('contact_no', $dealer->contact_no) }}">
        </div>

        <div class="col-md-3 mb-3">
            <label><b>Contact Email</b></label>
            <input type="email" name="contact_email" class="form-control" 
                value="{{ old('contact_email', $dealer->contact_email) }}">
        </div>

        <div class="col-md-12 mb-3">
            <label><b>Address</b></label>
            <textarea name="address" class="form-control" rows="3">{{ old('address', $dealer->address) }}</textarea>
        </div>
    </div>

    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary"><b>Update Dealer</b></button>
        <a href="{{ route('dealers.index') }}" class="btn btn-secondary">Cancel</a>
    </div>
</form>


            </div>
        </div>
    </div>
</div>
@endsection
