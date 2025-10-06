@extends('admin.layouts.shared')
@section('title', 'Add Payable Payment')
@section('header-title', 'Add Payable Payment')

@section('content')
<div class="container-fluid">
  <div class="card shadow-sm border-0">
    <div class="card-header bg-light">
      <h4 class="mb-0">Add Payable Payment</h4>
    </div>
    <div class="card-body">
      <form action="{{ route('payable-payments.store') }}" method="POST" enctype="multipart/form-data" id="paymentForm">
        @csrf
        <div class="row g-3" id="paymentRows">
          {{-- Initial Payment Row --}}
          <div class="payment-row">
            <h5>Payment Entry 1</h5>
            <div class="row g-3">
              {{-- Supplier Dropdown --}}
              <div class="col-md-6">
                <label class="form-label">Supplier</label>
                <select name="payments[0][payable_id]" class="form-control payable-select" required>
                  <option value="">-- Select Supplier --</option>
                  @foreach($supplier as $suppliers)
                    <option value="{{ $suppliers->id }}">
                      {{ $suppliers->supplier_name ?? 'N/A' }}
                    </option>
                  @endforeach
                </select>
              </div>

              <div class="col-md-6">
                <label class="form-label">Transaction Date</label>
                <input type="date" name="payments[0][transaction_date]" class="form-control" required>
              </div>

              <div class="col-md-6">
                <label class="form-label">Amount Paid</label>
                <input type="number" step="0.01" name="payments[0][amount_paid]" class="form-control" required>
              </div>

              <div class="col-md-6">
                <label class="form-label">Payment Mode</label>
                <select name="payments[0][payment_mode]" class="form-control" required>
                  <option value="">-- Select --</option>
                  <option value="cash">Cash</option>
                  <option value="bank">Bank</option>
                  <option value="cheque">Cheque</option>
                  <option value="online">Online</option>
                </select>
              </div>

              {{-- <div class="col-md-6">
                <label class="form-label">Proof of Payment</label>
                <input type="file" name="payments[0][proof_of_payment]" class="form-control">
              </div> --}}

              {{-- <div class="col-12">
                <label class="form-label">Notes</label>
                <textarea name="payments[0][notes]" class="form-control"></textarea>
              </div> --}}

              {{-- Hidden Transaction Type --}}
              <input type="hidden" name="payments[0][transaction_type]" value="credit">
            </div>
            <hr>
          </div>
        </div>

        {{-- Add More Button --}}
        <div class="col-12 mb-3">
          <button type="button" id="addMoreBtn" class="btn btn-primary">Add More Payment</button>
        </div>

        <div class="col-12">
          <button type="submit" class="btn btn-success">Save All Payments</button>
          <a href="{{ route('payable-payments.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let rowIndex = 1; 

    document.getElementById('addMoreBtn').addEventListener('click', function() {
        const container = document.getElementById('paymentRows');
        const newRow = document.querySelector('.payment-row').cloneNode(true);

        // Update the index for all inputs in the new row
        newRow.querySelectorAll('input, select, textarea').forEach(function(element) {
            const name = element.getAttribute('name');
            if (name) {
                element.setAttribute('name', name.replace(/\[\d+\]/, '[' + rowIndex + ']'));
                if (element.type !== 'hidden') {
                    element.value = ''; // Clear values for non-hidden inputs
                }
            }
            if (element.tagName === 'SELECT') {
                element.selectedIndex = 0; // Reset dropdowns
            }
        });

        newRow.querySelector('h5').textContent = 'Payment Entry ' + (rowIndex + 1);

        container.appendChild(newRow);

        rowIndex++;
    });
});
</script>
@endsection