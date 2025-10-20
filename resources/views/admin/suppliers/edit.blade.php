@extends('admin.layouts.shared')
@section('title', isset($supplier) ? 'Edit Supplier' : 'Add Supplier')
@section('header-title', isset($supplier) ? 'Edit Supplier' : 'Add Supplier')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body">
                    <form action="{{ isset($supplier) ? route('suppliers.update', $supplier->id) : route('suppliers.store') }}" method="POST">
                        @csrf
                        @if(isset($supplier))
                            @method('PUT')
                        @endif

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label><b>Supplier Company Name</b></label>
                                <input type="text" name="supplier_name" class="form-control" 
                                    placeholder="Enter Supplier Name" 
                                    value="{{ $supplier->supplier_name ?? old('supplier_name') }}">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label><b>Opening Balance</b></label>
                                <input type="number" step="0.01" name="opening_balance" class="form-control" 
                                    placeholder="Enter Opening Balance"
                                    value="{{ $supplier->opening_balance ?? old('opening_balance') }}">
                            </div>

                            {{-- Uncomment more fields if needed --}}
                            {{-- 
                            <div class="col-md-6 mb-3">
                                <label><b>Company Name</b></label>
                                <input type="text" name="company_name" class="form-control" 
                                    placeholder="Enter Company Name"
                                    value="{{ $supplier->company_name ?? old('company_name') }}">
                            </div>
                            --}}
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <b>{{ isset($supplier) ? 'Update Supplier' : 'Save Supplier' }}</b>
                            </button>
                            <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
