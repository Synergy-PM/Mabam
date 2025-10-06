@extends('admin.layouts.shared')
@section('title', 'Ledger Report Filter')
@section('header-title', 'Ledger Report Filter')

@section('content')
<div class="container-fluid">
  <div class="card shadow-sm border-0">
    <div class="card-body">
      <h4 class="mb-4 fw-semibold">Ledger Report Filter</h4>

      <form method="GET" action="{{ route('payable-payments.ledger-report') }}">
        <div class="row">
          {{-- Supplier --}}
          <div class="col-md-4 mb-3">
            <label for="supplier_id" class="form-label">Supplier</label>
            <select name="supplier_id" id="supplier_id" class="form-select">
              <option value="">Select Supplier</option>
              @foreach($suppliers as $supplier)
                <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>
                  {{ $supplier->supplier_name }}
                </option>
              @endforeach
            </select>
          </div>

          {{-- From Date --}}
          <div class="col-md-4 mb-3">
            <label class="form-label">From Date</label>
            <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
          </div>

          {{-- To Date --}}
          <div class="col-md-4 mb-3">
            <label class="form-label">To Date</label>
            <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
          </div>

          {{-- Button --}}
          <div class="col-md-4 mb-3 d-flex align-items-end">
            <button class="btn btn-primary w-100">View Report</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
