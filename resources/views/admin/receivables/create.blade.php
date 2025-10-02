@extends('admin.layouts.shared')
@section('title', 'Create Receivable')
@section('header-title', 'Create Receivable')

@section('content')
<div class="container-fluid">
  <div class="row">
    <div class="col-12">
      <div class="card shadow-sm border-0">
        <div class="card-body">

          <form action="{{ route('receivables.store') }}" method="POST">
            @csrf
            <div class="row">

              <!-- Transaction Date -->
              <div class="col-md-6 mb-3">
                <label><b>Transaction Date</b> <span class="text-danger">*</span></label>
                <input type="date" name="transaction_date" class="form-control" value="{{ old('transaction_date') }}" required>
              </div>

              <!-- Bilti No -->
              <div class="col-md-6 mb-3">
                <label><b>Bilti No</b> <span class="text-danger">*</span></label>
                <input type="text" name="bilti_no" class="form-control" value="{{ old('bilti_no') }}" required>
              </div>

              <!-- Truck No -->
              <div class="col-md-6 mb-3">
                <label><b>Truck No</b> <span class="text-danger">*</span></label>
                <input type="text" name="truck_no" class="form-control" value="{{ old('truck_no') }}" required>
              </div>

              <!-- No of Bags -->
              <div class="col-md-6 mb-3">
                <label><b>No of Bags</b> <span class="text-danger">*</span></label>
                <input type="number" name="no_of_bags" class="form-control" value="{{ old('no_of_bags') }}" required>
              </div>

              <!-- Amount per Bag -->
              <div class="col-md-6 mb-3">
                <label><b>Amount per Bag</b> <span class="text-danger">*</span></label>
                <input type="number" step="0.01" name="amount_per_bag" class="form-control" value="{{ old('amount_per_bag') }}" required>
              </div>

              <!-- Total Amount -->
              <div class="col-md-6 mb-3">
                <label><b>Total Amount</b></label>
                <input type="text" id="total_amount" class="form-control" readonly>
              </div>

              <!-- Tons -->
              <div class="col-md-6 mb-3">
                <label><b>Tons</b></label>
                <input type="text" id="tons" class="form-control" readonly>
              </div>

            </div>

            <button type="submit" class="btn btn-primary"><b>Save</b></button>
            <a href="{{ route('receivables.index') }}" class="btn btn-secondary">Cancel</a>
          </form>

        </div>
      </div>
    </div>
  </div>
</div>

<script>
const bagsInput = document.querySelector('input[name="no_of_bags"]');
const rateInput = document.querySelector('input[name="amount_per_bag"]');
const totalInput = document.getElementById('total_amount');
const tonsInput = document.getElementById('tons');

function calculateTotal() {
    let bags = parseFloat(bagsInput.value) || 0;
    let rate = parseFloat(rateInput.value) || 0;

    let total = bags * rate;
    totalInput.value = total.toFixed(2);
    tonsInput.value = (bags / 20).toFixed(2);
}

bagsInput.addEventListener('input', calculateTotal);
rateInput.addEventListener('input', calculateTotal);
</script>
@endsection
