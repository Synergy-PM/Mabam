@extends('admin.layouts.shared')
@section('title', 'Add Payable Payment')
@section('header-title', 'Add Payable Payment')

@section('content')
<div class="container-fluid">
  <div class="row">
    <div class="col-12">
      <div class="card shadow-sm border-0">
        <!-- Header -->
        <div class="card-header bg-light">
          <h4 class="card-title mb-0">Add Payable Payment</h4>
        </div>

        <!-- Body -->
        <div class="card-body">
          <form action="{{ route('payable-payments.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row g-3">
              
              <!-- Supplier -->
              <div class="col-md-6">
                <label class="form-label">Supplier <span class="text-danger">*</span></label>
                <select name="supplier_id" class="form-control" required>
                  <option value="">-- Select Supplier --</option>
                  @foreach($suppliers as $supplier)
                    <option value="{{ $supplier->id }}">{{ $supplier->supplier_name }}</option>
                  @endforeach
                </select>
              </div>

              <!-- Transaction Date -->
              <div class="col-md-6">
                <label class="form-label">Transaction Date <span class="text-danger">*</span></label>
                <input type="date" name="transaction_date" class="form-control" required>
              </div>

              <!-- Transaction Type -->
              <div class="col-md-6">
                <label class="form-label">Transaction Type <span class="text-danger">*</span></label>
                <select name="transaction_type" class="form-control" required>
                  <option value="debit">Debit</option>
                  <option value="credit">Credit</option>
                </select>
              </div>

              <!-- Amount -->
              <div class="col-md-6">
                <label class="form-label">Amount <span class="text-danger">*</span></label>
                <input type="number" step="0.01" name="amount" class="form-control" required>
              </div>

              <!-- Payment Mode -->
              <div class="col-md-6">
                <label class="form-label">Payment Mode</label>
                <select name="payment_mode" class="form-control">
                  <option value="">-- Select Mode --</option>
                  <option value="Cash">Cash</option>
                  <option value="Cheque">Cheque</option>
                  <option value="Online">Online</option>
                </select>
              </div>

              <!-- Proof of Payment -->
              <div class="col-md-6">
                <label class="form-label">Proof of Payment</label>
                <input type="file" name="proof_of_payment" class="form-control">
              </div>

              <!-- Notes -->
              <div class="col-12">
                <label class="form-label">Notes</label>
                <textarea name="notes" class="form-control" rows="3"></textarea>
              </div>

              <!-- Actions -->
              <div class="col-12 d-flex gap-2">
                <button type="submit" class="btn btn-success">
                  <i class="mdi mdi-content-save"></i> Save
                </button>
                <a href="{{ route('payable-payments.index') }}" class="btn btn-secondary">
                  <i class="mdi mdi-close"></i> Cancel
                </a>
              </div>

            </div>
          </form>
        </div>

      </div>
    </div>
  </div>
</div>
@endsection
