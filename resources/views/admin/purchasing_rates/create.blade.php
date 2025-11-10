@extends('admin.layouts.shared')
@section('title', 'Add Purchasing Rate')
@section('header-title', 'Add Purchasing Rate')

@section('content')
<div class="container-fluid py-3">
    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('purchasing_rates.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>From Date</label>
                        <input type="date" name="from_date" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>To Date</label>
                        <input type="date" name="to_date" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Supplier</label>
                        <select name="supplier_id" class="form-control" required>
                            <option value="">Select Supplier</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}">{{ $supplier->supplier_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>City</label>
                        <select name="city_id" class="form-control" required>
                            <option value="">Select City</option>
                            @foreach($cities as $city)
                                <option value="{{ $city->id }}">{{ $city->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Amount Per Ton</label>
                        <input type="number" name="amount_per_ton" class="form-control" step="0.01" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Save</button>
                <a href="{{ route('purchasing_rates.index') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>
@endsection
