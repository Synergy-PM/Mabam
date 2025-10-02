@extends('admin.layouts.shared')
@section('title', 'Edit Receivable')
@section('header-title', 'Edit Receivable')

@section('content')
<div class="container-fluid">
  <div class="row">
    <div class="col-12">
      <div class="card shadow-sm border-0">
        <div class="card-body">

          <form action="{{ route('receivables.update', $receivable->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row">

              <!-- Transaction Date -->
              <div class="col-md-6 mb-3">
                <label><b>Transaction Date</b> <span class="text-danger">*</span></label>
                <input type="date" name="transaction_date" class="form-control" value="{{ old('transaction_date', $receivable->transaction_date) }}" required>
              </div>

              <!-- Dealer -->
              <div class="col-md-6 mb-3">
                <label><b>Dealer</b> <span class="text-danger">*</span></label>
                <select name="dealer_id" class="form-control" required>
                  <option value="">Select Dealer</option>
                  @foreach($dealers as $dealer)
                    <option value="{{ $dealer->id }}" {{ old('dealer_id', $receivable->dealer_id) == $dealer->id ? 'selected' : '' }}>
                      {{ $dealer->dealer_name }}
                    </option>
                  @endforeach
                </select>
              </div>

              <!-- No of Bags -->
              <div class="col-md-6 mb-3">
                <label><b>No of Bags</b> <span class="text-danger">*</span></label>
                <input type="number" name="no_of_bags" id="no_of_bags" class="form-control" value="{{ old('no_of_bags', $receivable->no_of_bags) }}" required>
              </div>

              <!-- Amount per Bag -->
              <div class="col-md-6 mb-3">
                <label><b>Amount per Bag</b> <span class="text-danger">*</span></label>
                <input type="number" step="0.01" name="amount_per_bag" id="amount_per_bag" class="form-control" value="{{ old('amount_per_bag', $receivable->amount_per_bag) }}" required>
              </div>

              <!-- Total Amount -->
              <div class="col-md-6 mb-3">
                <label><b>Total Amount</b></label>
                <input type="text" id="total_amount" class="form-control" value="{{ $receivable->total_amount }}" readonly>
              </div>

              <!-- Bilti No -->
              <div class="col-md-6 mb-3">
                <label><b>Bilti No</b></label>
                <input type="text" name="bilti_no" class="form-control" value="{{ old('bilti_no', $receivable->bilti_no) }}">
              </div>

              <!-- Payment Mode -->
              {{-- <div class="col-md-6 mb-3">
                <label><b>Payment Mode</b> <span class="text-danger">*</span></label>
                <select name="payment_mode" class="form-control" required>
                  <option value="cash" {{ old('payment_mode', $receivable->payment_mode) == 'cash' ? 'selected' : '' }}>Cash</option>
                  <option value="cheque" {{ old('payment_mode', $receivable->payment_mode) == 'cheque' ? 'selected' : '' }}>Cheque</option>
                  <option value="online" {{ old('payment_mode', $receivable->payment_mode) == 'online' ? 'selected' : '' }}>Online</option>
                </select>
              </div> --}}

            </div>

            <button type="submit" class="btn btn-primary"><b>Update</b></button>
            <a href="{{ route('receivables.index') }}" class="btn btn-secondary">Cancel</a>
          </form>

        </div>
      </div>
    </div>
  </div>
</div>

<script>
  document.getElementById('no_of_bags').addEventListener('input', calculateTotal);
  document.getElementById('amount_per_bag').addEventListener('input', calculateTotal);

  function calculateTotal() {
      let bags = parseFloat(document.getElementById('no_of_bags').value) || 0;
      let rate = parseFloat(document.getElementById('amount_per_bag').value) || 0;
      let total = bags * rate;
      document.getElementById('total_amount').value = total.toFixed(2);
  }
</script>
@endsection
