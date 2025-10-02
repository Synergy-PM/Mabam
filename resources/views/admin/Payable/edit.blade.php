@extends('admin.layouts.shared')
@section('title','Edit Payable')
@section('header-title','Edit Payable')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        <form action="{{ route('payables.update', $payable->id) }}" method="POST">
          @csrf
          @method('PUT')

          <div class="row">
            <div class="col-md-6 mb-3">
              <label><b>Transaction Date</b><span class="text-danger">*</span></label>
              <input type="date"name="transaction_date"class="form-control"value="{{ old('transaction_date', $payable->transaction_date?->format('Y-m-d')) }}"required>
            </div>
            <div class="col-md-6 mb-3">
              <label><b>Supplier</b><span class="text-danger">*</span></label>
              <select name="supplier_id" class="form-control" required>
                <option value="">Select Supplier</option>
                @foreach($suppliers as $s)
                  <option value="{{ $s->id }}" @selected(old('supplier_id', $payable->supplier_id) == $s->id)>{{ $s->supplier_name }}</option>
                @endforeach
              </select>
            </div>

            <div class="col-md-6 mb-3">
              <label><b>No of Bags</b><span class="text-danger">*</span></label>
              <input type="number" name="no_of_bags" id="no_of_bags" class="form-control" value="{{ old('no_of_bags', $payable->no_of_bags) }}" required>
            </div>

            <div class="col-md-6 mb-3">
              <label><b>Amount per Bag</b><span class="text-danger">*</span></label>
              <input type="number" step="0.01" name="amount_per_bag" id="amount_per_bag" class="form-control" value="{{ old('amount_per_bag', $payable->amount_per_bag) }}" required>
            </div>

            <div class="col-md-6 mb-3">
              <label><b>Total Amount</b></label>
              <input type="text" id="total_amount" class="form-control" value="{{ number_format($payable->total_amount,2) }}" readonly>
            </div>

            <div class="col-md-6 mb-3">
              <label><b>Bilti No</b></label>
              <input type="text" name="bilti_no" class="form-control" value="{{ old('bilti_no', $payable->bilti_no) }}">
            </div>

            {{-- <div class="col-md-6 mb-3">
              <label><b>Payment Mode</b></label>
              <select name="payment_mode" class="form-control">
                <option value="cash" @selected(old('payment_mode', $payable->payment_mode)=='cash')>Cash</option>
                <option value="cheque" @selected(old('payment_mode', $payable->payment_mode)=='cheque')>Cheque</option>
                <option value="online" @selected(old('payment_mode', $payable->payment_mode)=='online')>Online</option>
              </select>
            </div> --}}

          </div>

          <button class="btn btn-primary">Update</button>
          <a href="{{ route('payables.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
  const noBags = document.getElementById('no_of_bags');
  const rate = document.getElementById('amount_per_bag');
  const total = document.getElementById('total_amount');

  function calc(){
    let b = parseFloat(noBags.value)||0;
    let r = parseFloat(rate.value)||0;
    total.value = (b*r).toFixed(2);
  }

  noBags.addEventListener('input', calc);
  rate.addEventListener('input', calc);
  window.addEventListener('load', calc);
</script>
@endsection
