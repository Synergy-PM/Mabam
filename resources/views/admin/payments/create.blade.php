@extends('admin.layouts.shared')
@section('title', 'Create Receivable')
@section('header-title', 'Create Receivable')

@section('content')
<div class="container-fluid">
  <div class="row">
    <div class="col-12">
      <div class="card shadow-sm border-0">
        <div class="card-body">

          <form action="{{ route('payments.store') }}" method="POST" id="receivableForm" enctype="multipart/form-data">
            @csrf

            <!-- Bilti Select -->
            <div class="row">
              <div class="col-md-4 mb-3">
                <label><b>Bilti No</b></label>
                <select name="bilti_no" id="bilti_no" class="form-control">
                  <option value="">Select Bilti</option>
                  @foreach($biltis as $bilti)
                    <option value="{{ $bilti->id }}"
                            data-bags="{{ $bilti->no_of_bags }}"
                            data-rate="{{ $bilti->amount_per_bag }}">
                      {{ $bilti->bilti_no }}
                    </option>
                  @endforeach
                </select>
              </div>

              <div class="col-md-4 mb-3">
                  <label><b>Total Bags (From Bilti)</b></label>
                  <input type="number" id="total_bags" class="form-control" readonly>
              </div>

              <div class="col-md-4 mb-3">
                  <label><b>Remaining Bags</b></label>
                  <input type="number" id="remaining_bags" class="form-control" readonly>
              </div>
            </div>

            <!-- Dealers Forms -->
            <div id="dealersContainer"></div>

            <div class="mb-3">
              <button type="button" class="btn btn-success" id="addDealerForm">+ Add Dealer</button>
            </div>

            <!-- Summary -->
            <div class="mb-3">
              <label><b>Summary</b></label>
              <div class="row">
                <div class="col-md-4">
                  <input type="text" id="summaryBags" class="form-control" placeholder="Total Allocated Bags" readonly>
                </div>
                <div class="col-md-4">
                  <input type="text" id="summaryTons" class="form-control" placeholder="Total Tons" readonly>
                </div>
                <div class="col-md-4">
                  <input type="text" id="grandTotal" class="form-control" placeholder="Grand Total" readonly>
                </div>
              </div>
            </div>

            <small id="bagWarning" class="text-danger fw-bold" style="display:none;">
              ⚠ Dealer bags ka total Bilti bags se zyada ho gaya hai!
            </small>

            <div class="mt-3">
              <button type="submit" class="btn btn-primary" id="saveBtn"><b>Save</b></button>
              <a href="{{ route('payments.index') }}" class="btn btn-secondary">Cancel</a>
            </div>

          </form>

        </div>
      </div>
    </div>
  </div>
</div>

<script>
  const biltiSelect = document.getElementById('bilti_no');
  const totalBagsInput = document.getElementById('total_bags');
  const remainingBagsInput = document.getElementById('remaining_bags');
  const dealersContainer = document.getElementById('dealersContainer');
  const summaryBags = document.getElementById('summaryBags');
  const summaryTons = document.getElementById('summaryTons');
  const grandTotalInput = document.getElementById('grandTotal');
  const bagWarning = document.getElementById('bagWarning');
  const saveBtn = document.getElementById('saveBtn');

  let totalBags = 0;
  let defaultRate = 0;
  let dealerIndex = 0;

  // Bilti change
  biltiSelect.addEventListener('change', function() {
      const selected = this.options[this.selectedIndex];
      totalBags = parseFloat(selected.dataset.bags) || 0;
      defaultRate = parseFloat(selected.dataset.rate) || 0;

      totalBagsInput.value = totalBags;
      remainingBagsInput.value = totalBags;

      // Reset dealers
      dealersContainer.innerHTML = "";
      dealerIndex = 0;
      calculateTotals();
  });

  // Add Dealer Form
  document.getElementById('addDealerForm').addEventListener('click', function() {
      let newForm = `
        <div class="border p-3 mt-3 rounded bg-light dealer-form" data-index="${dealerIndex}">
          <div class="row">
              <div class="col-md-4 mb-3">
                  <label>Dealer</label>
                  <select name="dealer_id[${dealerIndex}]" class="form-control dealerSelect" required>
                    <option value="">Select Dealer</option>
                    @foreach($dealers as $dealer)
                      <option value="{{ $dealer->id }}">{{ $dealer->dealer_name }}</option>
                    @endforeach
                  </select>
              </div>

              <div class="col-md-4 mb-3">
                  <label>Bags</label>
                  <input type="number" name="bags[${dealerIndex}]" class="form-control bags" value="">
              </div>

              <div class="col-md-4 mb-3">
                  <label>Rate</label>
                  <input type="number" step="0.01" name="rate[${dealerIndex}]" class="form-control rate" value="${defaultRate}">
              </div>

              <div class="col-md-4 mb-3">
                  <label>Freight</label>
                  <input type="number" step="0.01" name="freight[${dealerIndex}]" class="form-control dealerFreight" value="">
              </div>

              <div class="col-md-4 mb-3">
                  <label>Tons</label>
                  <input type="text" class="form-control tons" readonly>
              </div>

              <div class="col-md-4 mb-3">
                  <label>Total</label>
                  <input type="text" class="form-control dealerTotal" readonly>
              </div>

              <div class="col-md-4 mb-3">
                  <label>Payment Type</label>
                  <select name="payment_type[${dealerIndex}]" class="form-control" required>
                    <option value="">Select</option>
                    <option value="cash">Cash</option>
                    <option value="online">Online</option>
                    <option value="cheque">Cheque</option>
                  </select>
              </div>

              <div class="col-md-4 mb-3">
                  <label>Proof of Payment</label>
                  <input type="file" name="proof_of_payment[${dealerIndex}]" class="form-control">
              </div>

              <div class="col-md-4 mb-3 d-flex align-items-end">
                  <button type="button" class="btn btn-danger removeDealer">Remove</button>
              </div>
          </div>
        </div>
      `;
      dealersContainer.insertAdjacentHTML('beforeend', newForm);
      dealerIndex++;
  });

  // Remove Dealer
  dealersContainer.addEventListener('click', function(e) {
      if (e.target.classList.contains('removeDealer')) {
          e.target.closest('.dealer-form').remove();
          calculateTotals();
      }
  });

  // Calculate Totals on input change
  dealersContainer.addEventListener('input', calculateTotals);

  function calculateTotals() {
      let totalDealerBags = 0;
      let grandTotal = 0;
      let totalTons = 0;

      document.querySelectorAll('.dealer-form').forEach(form => {
          let bags = parseFloat(form.querySelector('.bags').value) || 0;
          let rate = parseFloat(form.querySelector('.rate').value) || 0;
          let freight = parseFloat(form.querySelector('.dealerFreight').value) || 0;

          let tons = bags / 20;
          form.querySelector('.tons').value = tons.toFixed(2);

          let dealerTotal = (bags * rate) - freight;
          form.querySelector('.dealerTotal').value = dealerTotal.toFixed(2);

          totalDealerBags += bags;
          grandTotal += dealerTotal;
          totalTons += tons;
      });

      let remaining = totalBags - totalDealerBags;
      remainingBagsInput.value = remaining >= 0 ? remaining : 0;

      summaryBags.value = totalDealerBags;
      summaryTons.value = totalTons.toFixed(2);
      grandTotalInput.value = grandTotal.toFixed(2);

      if (totalDealerBags > totalBags) {
          bagWarning.style.display = 'block';
          saveBtn.disabled = true;
      } else {
          bagWarning.style.display = 'none';
          saveBtn.disabled = false;
      }
  }
</script>
@endsection
