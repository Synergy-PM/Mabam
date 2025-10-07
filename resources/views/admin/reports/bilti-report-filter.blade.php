@extends('admin.layouts.shared')
@section('title', 'Bilti Report Filter')
@section('header-title', 'Bilti Report Filter')

@section('content')
<div class="container-fluid">
  <div class="card shadow-sm border-0">
    <div class="card-body">
      <h4 class="mb-4 fw-semibold">Bilti Report Filter</h4>

      {{-- Filter Form --}}
      <form method="GET" action="{{ route('bilti.report') }}">
        <div class="row">
          {{-- Bilti Number --}}
          <div class="col-md-4 mb-3">
            <label for="bilti_no" class="form-label">Bilti Number</label>
            <select name="bilti_no" id="bilti_no" class="form-select">
              <option value="">Select Bilti No</option>
              @foreach($biltiList as $bilti)
                <option value="{{ $bilti->bilti_no }}">{{ $bilti->bilti_no }}</option>
              @endforeach
            </select>
          </div>

          {{-- Dealer Dropdown --}}
          <div class="col-md-4 mb-3">
            <label for="dealer_id" class="form-label">Dealer</label>
            <select id="dealer_id" name="dealer_id" class="form-select">
              <option value="">Select Dealer</option>
            </select>
          </div>

          {{-- From Date --}}
          <div class="col-md-2 mb-3">
            <label class="form-label">From Date</label>
            <input type="date" name="start_date" class="form-control">
          </div>

          {{-- To Date --}}
          <div class="col-md-2 mb-3">
            <label class="form-label">To Date</label>
            <input type="date" name="end_date" class="form-control">
          </div>

          {{-- Button --}}
          <div class="col-md-12 mb-3 d-flex align-items-end">
            <button class="btn btn-primary w-100">View Report</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- ===================== AJAX SCRIPT ===================== --}}
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const biltiSelect = document.getElementById('bilti_no');
    const dealerSelect = document.getElementById('dealer_id');

    biltiSelect.addEventListener('change', function () {
      const biltiNo = this.value;
      dealerSelect.innerHTML = '<option value="">Select Dealer</option>';

      if (!biltiNo) return;

      fetch(`{{ route('bilti.get-dealer', ':biltiNo') }}`.replace(':biltiNo', biltiNo))
        .then(res => {
          console.log("Response status:", res.status);
          if (!res.ok) throw new Error(`HTTP error! Status: ${res.status}`);
          return res.json();
        })
        .then(data => {
          console.log("Dealer response:", data);

          if (data.success && Array.isArray(data.dealers)) {
            data.dealers.forEach(dealer => {
              const option = document.createElement('option');
              option.value = dealer.id;
              option.textContent = dealer.dealer_name; 
              dealerSelect.appendChild(option);
            });
          } else {
            const option = document.createElement('option');
            option.value = "";
            option.textContent = data.message || "No dealers found";
            dealerSelect.appendChild(option);
          }
        })
        .catch(err => {
          console.error('Error fetching dealers:', err);
          const option = document.createElement('option');
          option.value = "";
          option.textContent = "Error loading dealers";
          dealerSelect.appendChild(option);
        });
    });
  });
</script>
@endsection
