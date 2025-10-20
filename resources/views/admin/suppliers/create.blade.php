@extends('admin.layouts.shared')
@section('title', 'Create Supplier')
@section('header-title', 'Create Supplier')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body">
                    <form action="{{ route('suppliers.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label><b>Supplier Company Name</b></label>
                                <input type="text" name="supplier_name" class="form-control" 
                                    placeholder="Enter Supplier Name"
                                    value="{{ old('supplier_name') }}">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label><b>Opening Balance</b></label>
                                <input type="number" step="0.01" name="opening_balance" class="form-control" 
                                    placeholder="Enter Opening Balance"
                                    value="{{ old('opening_balance') }}">
                            </div>

                            {{-- Uncomment more fields if needed --}}
                            {{-- 
                            <div class="col-md-6 mb-3">
                                <label><b>Company Name</b></label>
                                <input type="text" name="company_name" class="form-control" 
                                    placeholder="Enter Company Name"
                                    value="{{ old('company_name') }}">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label><b>City</b></label>
                                <select name="city_id" class="form-control">
                                    <option value="">Select City</option>
                                    @foreach($cities as $city)
                                        <option value="{{ $city->id }}" @if(old('city_id') == $city->id) selected @endif>
                                            {{ $city->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            --}}
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary"><b>Create Supplier</b></button>
                            <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
