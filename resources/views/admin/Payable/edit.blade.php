@extends('admin.layouts.shared')
@section('title', 'Edit Payable & Receivable')
@section('header-title', 'Edit Payable & Receivable')

@section('content')
<div class="row">
    <div class="col-12">

        <!-- PAYABLE FORM -->
        <div class="card mb-4">
            <div class="card-body">
                <h4 class="mb-3">Edit Payable</h4>
                <form action="{{ route('payables.update', $payable->id) }}" method="POST" id="payableForm" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label><b>Transaction Date</b><span class="text-danger">*</span></label>
                            <input type="date" name="transaction_date" class="form-control"
                                value="{{ old('transaction_date', $payable->transaction_date) }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label><b>Supplier</b><span class="text-danger">*</span></label>
                            <select name="supplier_id" class="form-control" required>
                                <option value="">Select Supplier</option>
                                @foreach($suppliers as $s)
                                    <option value="{{ $s->id }}" @selected(old('supplier_id', $payable->supplier_id) == $s->id)>
                                        {{ $s->supplier_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label><b>Tons</b></label>
                            <input type="number" id="tons" class="form-control"
                                value="{{ old('tons', $payable->tons) }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label><b>No of Bags</b></label>
                            <input type="number" name="no_of_bags" id="no_of_bags" class="form-control"
                                value="{{ old('no_of_bags', $payable->no_of_bags) }}" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label><b>Amount per Bag</b></label>
                            <input type="number" step="0.01" name="amount_per_bag" id="amount_per_bag" class="form-control"
                                value="{{ old('amount_per_bag', $payable->amount_per_bag) }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label><b>Total Amount</b></label>
                            <input type="text" id="total_amount" class="form-control"
                                value="{{ old('total_amount', $payable->total_amount) }}" readonly>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label><b>Bilti No</b></label>
                            <input type="text" name="bilti_no" id="payable_bilti_no" class="form-control"
                                value="{{ old('bilti_no', $payable->bilti_no) }}">
                        </div>
                    </div>

                    <!-- RECEIVABLE FORM -->
                    <h4 class="mb-3 mt-5">Edit Receivable</h4>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label><b>Bilti No</b></label>
                            <input type="text" id="bilti_no" class="form-control" readonly>
                        </div>
                        <div class="col-md-4">
                            <label><b>Total Bags (From Bilti)</b></label>
                            <input type="number" id="total_bags" class="form-control" readonly>
                        </div>
                        <div class="col-md-4">
                            <label><b>Remaining Bags</b></label>
                            <input type="number" id="remaining_bags" class="form-control" readonly>
                        </div>
                    </div>

                    <!-- Dealers Forms -->
                    <div id="dealersContainer">
                        @foreach($payable->receivables as $index => $rec)
                        <div class="border p-3 mt-3 rounded bg-light dealer-form" data-index="{{ $index }}">
                            <div class="row">

                                <input type="hidden" name="supplier_id" value="{{ $payable->supplier_id }}">

                                <input type="hidden" name="receivable_id[{{ $index }}]" value="{{ $rec->id }}">
                                <div class="col-md-4 mb-3">
                                    <label>Dealer</label>

                                    <select name="dealer_id[{{ $index }}]" class="form-control dealerSelect" required>
                                        <option value="">Select Dealer</option>
                                        @foreach($dealers as $dealer)
                                            <option value="{{ $dealer->id }}" @selected(old('dealer_id.'.$index, $rec->dealer_id) == $dealer->id)>
                                                {{ $dealer->dealer_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label>Tons</label>
                                    <input type="number" class="form-control tonsInput"
                                        value="{{ old('tons.'.$index, $rec->tons) }}">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label>Bags</label>
                                    <input type="number" name="bags[{{ $index }}]" class="form-control bags"
                                        value="{{ old('bags.'.$index, $rec->bags) }}" readonly>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label>Rate</label>
                                    <input type="number" step="0.01" name="rate[{{ $index }}]" class="form-control rate"
                                        value="{{ old('rate.'.$index, $rec->rate) }}">
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label>Total</label>
                                    <input type="text" class="form-control dealerTotal"
                                        value="{{ old('total.'.$index, $rec->total) }}" readonly>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label>Freight</label>
                                    <input type="number" step="0.01" name="freight[{{ $index }}]" class="form-control dealerFreight"
                                        value="{{ old('freight.'.$index, $rec->freight) }}">
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label>Code Number</label>
                                    <input type="text" name="code[{{ $index }}]" class="form-control dealercode"
                                        value="{{ old('code.'.$index, $rec->code) }}">
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label>Payment Type</label>
                                    <select name="payment_type[{{ $index }}]" class="form-control" required>
                                        <option value="">Select</option>
                                        <option value="credit" @selected($rec->payment_type == 'credit')>Credit</option>
                                        <option value="cash" @selected($rec->payment_type == 'cash')>Cash</option>
                                        <option value="online" @selected($rec->payment_type == 'online')>Online</option>
                                        <option value="cheque" @selected($rec->payment_type == 'cheque')>Cheque</option>
                                    </select>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label>Proof of Payment</label>
                                    <input type="file" name="proof_of_payment[{{ $index }}]" class="form-control">
                                    @if($rec->proof_of_payment)
                                        <small class="text-muted">Current: {{ $rec->proof_of_payment }}</small>
                                    @endif
                                </div>

                                <div class="col-md-4 mb-3 d-flex align-items-end">
                                    <button type="button" class="btn btn-danger removeDealer">Remove</button>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

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
                        ⚠ The total dealer bags have exceeded the bilti bags!
                    </small>

                    <div class="col-md-12 mt-4">
                        <button type="submit" class="btn btn-primary" id="saveBtn"><b>Update</b></button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>


<script>
    // ===== PAYABLE CALC =====
    const noBags = document.getElementById('no_of_bags');
    const rate = document.getElementById('amount_per_bag');
    const total = document.getElementById('total_amount');
    const tons = document.getElementById('tons');
    const payableBiltiInput = document.getElementById('payable_bilti_no');

    function calcFromTons() {
        let t = parseFloat(tons.value) || 0;
        let r = parseFloat(rate.value) || 0;
        let b = t * 20;
        noBags.value = b.toFixed(0);
        total.value = (b * r).toFixed(2);
        syncReceivableForm();
    }

    function calcFromRate() {
        let b = parseFloat(noBags.value) || 0;
        let r = parseFloat(rate.value) || 0;
        total.value = (b * r).toFixed(2);
    }

    tons.addEventListener('input', calcFromTons);
    rate.addEventListener('input', calcFromRate);
    window.addEventListener('load', calcFromTons);

    // ===== RECEIVABLE CALC =====
    const biltiInput = document.getElementById('bilti_no');
    const totalBagsInput = document.getElementById('total_bags');
    const remainingBagsInput = document.getElementById('remaining_bags');
    const dealersContainer = document.getElementById('dealersContainer');
    const summaryBags = document.getElementById('summaryBags');
    const summaryTons = document.getElementById('summaryTons');
    const grandTotalInput = document.getElementById('grandTotal');
    const bagWarning = document.getElementById('bagWarning');
    const saveBtn = document.getElementById('saveBtn');

     let totalBags = 0;
    let dealerIndex = 0;


    function syncReceivableForm() {
        biltiInput.value = payableBiltiInput.value;
        totalBags = parseFloat(noBags.value) || 0;
        totalBagsInput.value = totalBags;

        let usedBags = 0;
        document.querySelectorAll('.dealer-form .bags').forEach(b => {
            usedBags += parseFloat(b.value) || 0;
        });

        remainingBagsInput.value = totalBags - usedBags;
        calculateTotals();
    }

    window.addEventListener('load', syncReceivableForm);
    payableBiltiInput.addEventListener('input', syncReceivableForm);
    tons.addEventListener('input', syncReceivableForm);

    document.getElementById('addDealerForm').addEventListener('click', function() {
        let newForm = `
        <div class="border p-3 mt-3 rounded bg-light dealer-form" data-index="${dealerIndex}">
          <div class="row">
            <input type="hidden" name="supplier_id" value="${document.querySelector('[name="supplier_id"]').value}">
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
                <label>Tons</label>
                <input type="number" class="form-control tonsInput">
            </div>
            <div class="col-md-4 mb-3">
                <label>Bags</label>
                <input type="number" name="bags[${dealerIndex}]" class="form-control bags" readonly>
            </div>
            <div class="col-md-4 mb-3">
                <label>Rate</label>
                <input type="number" step="0.01" name="rate[${dealerIndex}]" class="form-control rate">
            </div>
            <div class="col-md-4 mb-3">
                <label>Total</label>
                <input type="text" class="form-control dealerTotal" readonly>
            </div>
            <div class="col-md-4 mb-3">
                <label>Freight</label>
                <input type="number" step="0.01" name="freight[${dealerIndex}]" class="form-control dealerFreight">
            </div>
               <div class="col-md-4 mb-3">
                  <label>Code Number</label>
                  <input type="text" name="code[${dealerIndex}]" class="form-control dealercode" value="">
              {{-- ✅ Validation error show karein --}}
@if($errors->has('code'))
    @foreach($errors->get('code') as $i => $messages)
        @foreach($messages as $message)
            <div class="text-danger small">
                {{ "Row ".($i+1).": ".$message }}
            </div>
        @endforeach
    @endforeach
@endif
                  </div>
            <div class="col-md-4 mb-3">
                <label>Payment Type</label>
                <select name="payment_type[${dealerIndex}]" class="form-control" required>
                  <option value="">Select</option>
                  <option value="credit">Credit</option>
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

    dealersContainer.addEventListener('click', function(e) {
        if (e.target.classList.contains('removeDealer')) {
            e.target.closest('.dealer-form').remove();
            calculateTotals();
        }
    });

    dealersContainer.addEventListener('input', function(e) {
        if (e.target.classList.contains('tonsInput')) {
            let form = e.target.closest('.dealer-form');
            let tonsVal = parseFloat(e.target.value) || 0;
            let bagsField = form.querySelector('.bags');
            bagsField.value = (tonsVal * 20).toFixed(0);
        }
        calculateTotals();
    });

    function calculateTotals() {
        let totalDealerBags = 0;
        let grandTotal = 0;
        let totalTons = 0;

        document.querySelectorAll('.dealer-form').forEach(form => {
            let bags = parseFloat(form.querySelector('.bags').value) || 0;
            let rate = parseFloat(form.querySelector('.rate').value) || 0;
            let freight = parseFloat(form.querySelector('.dealerFreight').value) || 0;
            let code = form.querySelector('.dealercode').value || "";

            let tonsInput = form.querySelector('.tonsInput');
            let tons = bags / 20;
            if (tonsInput.value === "" || parseFloat(tonsInput.value) != tons) {
                tonsInput.value = tons.toFixed(2);
            }

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
