@extends('admin.layouts.shared')
@section('title', 'Create Payable & Receivable')
@section('header-title', 'Create Payable & Receivable')
@section('content')
<div class="row">
    <div class="col-12">
        <!-- PAYABLE FORM -->
        <div class="card mb-4">
            <div class="card-body">
                <h4 class="mb-3">Create Payable</h4>
                <form action="{{ route('payables.store') }}" method="POST" id="payableForm" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label><b>Transaction Date</b><span class="text-danger">*</span></label>
                            <input type="date" name="transaction_date" class="form-control"
                                value="{{ old('transaction_date') }}" required>
                        </div>
                        <div class="col-md-6 mb-3 position-relative">
                            <label><b>Supplier</b><span class="text-danger">*</span></label>
                            <input type="text" id="supplier_search" class="form-control mb-1" placeholder="Type supplier name..." value="{{ old('supplier_name') }}">
                            <ul id="supplier_suggestion_list"
                                class="list-group position-absolute w-100 shadow-sm"
                                style="z-index: 1000; max-height: 200px; overflow-y: auto; display: none;">
                                @foreach ($suppliers as $s)
                                <li class="list-group-item list-group-item-action"
                                    data-id="{{ $s->id }}"
                                    style="cursor: pointer;">
                                    {{ $s->supplier_name }}
                                </li>
                                @endforeach
                                <li class="list-group-item list-group-item-action text-muted not-found" style="display: none;">
                                    Not Found
                                </li>
                            </ul>
                            <input type="hidden" name="supplier_id" id="supplier_id" value="{{ old('supplier_id') }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label><b>Tons</b></label>
                            <input type="text" id="tons" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label><b>No of Bags</b><span class="text-danger">*</span></label>
                            <input type="number" name="no_of_bags" id="no_of_bags" class="form-control"
                                value="{{ old('no_of_bags') }}" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label><b>Amount per Ton</b><span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="amount_per_bag" id="amount_per_bag"
                                class="form-control" value="{{ old('amount_per_bag') }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label><b>Total Amount</b></label>
                            <input type="text" id="total_amount" class="form-control" readonly>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label><b>Bilti No</b></label>
                            <input type="text" name="bilti_no" id="payable_bilti_no" class="form-control"
                                value="{{ old('bilti_no') }}">
                        </div>
                    </div>
                    <!-- RECEIVABLE FORM -->
                    <h4 class="mb-3 mt-5">Create Receivable</h4>
                    <!-- Bilti Input (Readonly) -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label><b>Bilti No</b></label>
                            <input type="text" name="bilti_no" id="bilti_no" class="form-control" readonly>
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
                    <div id="dealersContainer"></div>
                    <div class="mb-3">
                        <button type="button" class="btn btn-success" id="addDealerForm">+ Add Dealer</button>
                    </div>
                    <!-- Summary -->
                    <div class="mb-3">
                        <label><b>Summary</b></label>
                        <div class="row">
                            <div class="col-md-4">
                                <input type="text" id="summaryBags" class="form-control"
                                    placeholder="Total Allocated Bags" readonly>
                            </div>
                            <div class="col-md-4">
                                <input type="text" id="summaryTons" class="form-control" placeholder="Total Tons"
                                    readonly>
                            </div>
                            <div class="col-md-4">
                                <input type="text" id="grandTotal" class="form-control" placeholder="Grand Total"
                                    readonly>
                            </div>
                        </div>
                    </div>
                    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

                   <small id="bagWarning" class="text-danger fw-bold" style="display:none;">
    ⚠ The total dealer bags have exceeded the bilti bags!
</small>

                    <div class="col-md-12 mt-4">
                        <button type="submit" class="btn btn-primary" id="saveBtn"><b>Submit</b></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    // Supplier Search Functionality
    document.addEventListener('DOMContentLoaded', function() {
        const supplierSearch = document.getElementById('supplier_search');
        const supplierSuggestionList = document.getElementById('supplier_suggestion_list');
        const supplierIdInput = document.getElementById('supplier_id');
        const supplierItems = Array.from(supplierSuggestionList.querySelectorAll('li:not(.not-found)'));
        const notFoundItem = supplierSuggestionList.querySelector('.not-found');
        let supplierSelectedIndex = -1;

        supplierSearch.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            supplierSelectedIndex = -1;
            supplierSuggestionList.style.display = query ? 'block' : 'none';
            let hasMatches = false;
            supplierItems.forEach(li => {
                const name = li.textContent.toLowerCase();
                li.style.display = name.includes(query) ? 'block' : 'none';
                if (name.includes(query)) hasMatches = true;
            });
            notFoundItem.style.display = query && !hasMatches ? 'block' : 'none';
            if (!supplierItems.some(li => li.textContent.toLowerCase().trim() === query)) {
                supplierIdInput.value = '';
            }
        });

        supplierItems.forEach((li, index) => {
            li.addEventListener('click', function() {
                supplierSearch.value = this.textContent.trim();
                supplierIdInput.value = this.getAttribute('data-id');
                supplierSuggestionList.style.display = 'none';
                notFoundItem.style.display = 'none';
            });
        });

        supplierSearch.addEventListener('keydown', function(e) {
            const visibleItems = supplierItems.filter(li => li.style.display !== 'none');
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                supplierSelectedIndex = (supplierSelectedIndex + 1) % visibleItems.length;
                highlightItem(visibleItems, supplierSelectedIndex, supplierItems);
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                supplierSelectedIndex = (supplierSelectedIndex - 1 + visibleItems.length) % visibleItems.length;
                highlightItem(visibleItems, supplierSelectedIndex, supplierItems);
            } else if (e.key === 'Enter') {
                e.preventDefault();
                if (supplierSelectedIndex >= 0 && visibleItems[supplierSelectedIndex]) {
                    supplierSearch.value = visibleItems[supplierSelectedIndex].textContent.trim();
                    supplierIdInput.value = visibleItems[supplierSelectedIndex].getAttribute('data-id');
                    supplierSuggestionList.style.display = 'none';
                    notFoundItem.style.display = 'none';
                }
            }
        });

        document.addEventListener('click', function(e) {
            if (!e.target.closest('.position-relative')) {
                supplierSuggestionList.style.display = 'none';
                notFoundItem.style.display = 'none';
            }
        });

        supplierSuggestionList.style.display = 'none';
        notFoundItem.style.display = 'none';

        // Form validation
        const payableForm = document.getElementById('payableForm');
        payableForm.addEventListener('submit', function(e) {
            if (!supplierIdInput.value) {
                e.preventDefault();
                supplierSearch.classList.add('is-invalid');
                supplierSearch.insertAdjacentHTML('afterend', '<div class="invalid-feedback">Please select a valid supplier from the list.</div>');
            } else {
                supplierSearch.classList.remove('is-invalid');
                const existingFeedback = supplierSearch.nextElementSibling;
                if (existingFeedback && existingFeedback.classList.contains('invalid-feedback')) {
                    existingFeedback.remove();
                }
            }
        });
    });

    // Existing calculations
    const noBags = document.getElementById('no_of_bags');
    const rate = document.getElementById('amount_per_bag');
    const total = document.getElementById('total_amount');
    const tons = document.getElementById('tons');
    const payableBiltiInput = document.getElementById('payable_bilti_no');

    function calcFromTons() {
        let t = parseFloat(tons.value) || 0;
        let r = parseFloat(rate.value) || 0;
        let b = t * 20; // 1 ton = 20 bags
        noBags.value = b > 0 ? b.toFixed(0) : '';
        total.value = (t * r) > 0 ? (t * r).toFixed(2) : ''; // ✅ changed this line
        syncReceivableForm();
    }

    function calcFromRate() {
        let t = parseFloat(tons.value) || 0;
        let r = parseFloat(rate.value) || 0;
        total.value = (t * r) > 0 ? (t * r).toFixed(2) : ''; // ✅ changed this line
    }


    tons.addEventListener('input', calcFromTons);
    rate.addEventListener('input', calcFromRate);
    window.addEventListener('load', calcFromTons);

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
        totalBagsInput.value = totalBags > 0 ? totalBags : '';
        remainingBagsInput.value = totalBags > 0 ? totalBags : '';
        dealersContainer.innerHTML = "";
        dealerIndex = 0;
        calculateTotals();
    }

    window.addEventListener('load', syncReceivableForm);
    payableBiltiInput.addEventListener('input', syncReceivableForm);
    tons.addEventListener('input', syncReceivableForm);

    function highlightItem(list, index, allItems) {
        allItems.forEach((li, i) => li.classList.toggle('active', i === index));
    }

    document.getElementById('addDealerForm').addEventListener('click', function() {
        let newForm = `
        <div class="border p-3 mt-3 rounded bg-light dealer-form" data-index="${dealerIndex}">
          <div class="row">
              <input type="hidden" name="supplier_id" value="${document.querySelector('[name="supplier_id"]').value}">
              <div class="col-md-4 mb-3 position-relative">
                  <label>Dealer</label>
                  <input type="text" class="form-control dealer_search mb-1" placeholder="Type dealer name..." data-index="${dealerIndex}">
                  <ul class="list-group dealer_suggestion_list position-absolute w-100 shadow-sm"
                      style="z-index: 1000; max-height: 200px; overflow-y: auto; display: none;"
                      data-index="${dealerIndex}">
                      @foreach ($dealers as $dealer)
                          <li class="list-group-item list-group-item-action"
                              data-id="{{ $dealer->id }}"
                              style="cursor: pointer;">
                              {{ $dealer->dealer_name }}
                          </li>
                      @endforeach
                      <li class="list-group-item list-group-item-action text-muted not-found" style="display: none;">
                          Not Found
                      </li>
                  </ul>
                  <input type="hidden" name="dealer_id[${dealerIndex}]" class="dealer_id" required>
              </div>
              <div class="col-md-4 mb-3">
                  <label>Bags</label>
                  <input type="number" name="bags[${dealerIndex}]" class="form-control bagsInput" value="">
              </div>
              <div class="col-md-4 mb-3">
                  <label>Tons</label>
                  <input type="number" class="form-control tonsInput" readonly>
              </div>
              <div class="col-md-4 mb-3">
                  <label>Rate Per Bag</label>
                  <input type="number" step="0.01" name="rate[${dealerIndex}]" class="form-control rate" value="">
              </div>
               <div class="col-md-4 mb-3">
                  <label>Freight</label>
                  <input type="number" step="0.01" name="freight[${dealerIndex}]" class="form-control dealerFreight" value="">
              </div>
              <div class="col-md-4 mb-3">
                  <label>Total</label>
                  <input type="text" class="form-control dealerTotal" readonly>
              </div>

              <div class="col-md-4 mb-3">
                  <label>Code Number</label>
                  <input type="text" name="code[${dealerIndex}]" class="form-control" value="">
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

        // Initialize dealer search for the new form
        const dealerSearch = dealersContainer.querySelector(`.dealer_search[data-index="${dealerIndex}"]`);
        const dealerSuggestionList = dealersContainer.querySelector(`.dealer_suggestion_list[data-index="${dealerIndex}"]`);
        const dealerIdInput = dealersContainer.querySelector(`.dealer_id[name="dealer_id[${dealerIndex}]"]`);
        const dealerItems = Array.from(dealerSuggestionList.querySelectorAll('li:not(.not-found)'));
        const dealerNotFoundItem = dealerSuggestionList.querySelector('.not-found');
        let dealerSelectedIndex = -1;

        dealerSearch.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            dealerSelectedIndex = -1;
            dealerSuggestionList.style.display = query ? 'block' : 'none';
            let hasMatches = false;
            dealerItems.forEach(li => {
                const name = li.textContent.toLowerCase();
                li.style.display = name.includes(query) ? 'block' : 'none';
                if (name.includes(query)) hasMatches = true;
            });
            dealerNotFoundItem.style.display = query && !hasMatches ? 'block' : 'none';
            if (!dealerItems.some(li => li.textContent.toLowerCase().trim() === query)) {
                dealerIdInput.value = '';
            }
        });

        dealerItems.forEach((li, index) => {
            li.addEventListener('click', function() {
                dealerSearch.value = this.textContent.trim();
                dealerIdInput.value = this.getAttribute('data-id');
                dealerSuggestionList.style.display = 'none';
                dealerNotFoundItem.style.display = 'none';
                dealerSearch.classList.remove('is-invalid');
                const existingFeedback = dealerSearch.nextElementSibling;
                if (existingFeedback && existingFeedback.classList.contains('invalid-feedback')) {
                    existingFeedback.remove();
                }
            });
        });

        dealerSearch.addEventListener('keydown', function(e) {
            const visibleItems = dealerItems.filter(li => li.style.display !== 'none');
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                dealerSelectedIndex = (dealerSelectedIndex + 1) % visibleItems.length;
                highlightItem(visibleItems, dealerSelectedIndex, dealerItems);
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                dealerSelectedIndex = (dealerSelectedIndex - 1 + visibleItems.length) % visibleItems.length;
                highlightItem(visibleItems, dealerSelectedIndex, dealerItems);
            } else if (e.key === 'Enter') {
                e.preventDefault();
                if (dealerSelectedIndex >= 0 && visibleItems[dealerSelectedIndex]) {
                    dealerSearch.value = visibleItems[dealerSelectedIndex].textContent.trim();
                    dealerIdInput.value = visibleItems[dealerSelectedIndex].getAttribute('data-id');
                    dealerSuggestionList.style.display = 'none';
                    dealerNotFoundItem.style.display = 'none';
                    dealerSearch.classList.remove('is-invalid');
                    const existingFeedback = dealerSearch.nextElementSibling;
                    if (existingFeedback && existingFeedback.classList.contains('invalid-feedback')) {
                        existingFeedback.remove();
                    }
                }
            }
        });

        dealerSuggestionList.style.display = 'none';
        dealerNotFoundItem.style.display = 'none';
        dealerIndex++;
        calculateTotals();
    });

    dealersContainer.addEventListener('click', function(e) {
        if (e.target.classList.contains('removeDealer')) {
            e.target.closest('.dealer-form').remove();
            calculateTotals();
        }
    });

    dealersContainer.addEventListener('input', function(e) {
        if (e.target.classList.contains('bagsInput')) {
            let form = e.target.closest('.dealer-form');
            let bagsVal = parseFloat(e.target.value) || 0;
            let tonsField = form.querySelector('.tonsInput');
            tonsField.value = bagsVal > 0 ? (bagsVal / 20).toFixed(2) : '';
        }
        calculateTotals();
    });

    dealersContainer.addEventListener('input', function(e) {
        if (e.target.classList.contains('rate') || e.target.classList.contains('dealerFreight')) {
            calculateTotals();
        }
    });

    function calculateTotals() {
    let totalDealerBags = 0;
    let grandTotal = 0;
    let totalTons = 0;

    document.querySelectorAll('.dealer-form').forEach(form => {
        let bags = parseFloat(form.querySelector('.bagsInput').value) || 0;
        let rate = parseFloat(form.querySelector('.rate').value) || 0;
        let freight = parseFloat(form.querySelector('.dealerFreight').value) || 0;
        let tons = bags / 20;

        form.querySelector('.tonsInput').value = bags > 0 ? tons.toFixed(2) : '';

        // ✅ Updated formula: Freight will be subtracted from Rate per Bag, not from total
        let dealerTotal = bags * (rate - freight);

        form.querySelector('.dealerTotal').value = dealerTotal > 0 ? dealerTotal.toFixed(2) : '';
        totalDealerBags += bags;
        grandTotal += dealerTotal;
        totalTons += tons;
    });

    let remaining = totalBags - totalDealerBags;
    remainingBagsInput.value = remaining >= 0 ? remaining : '';
    summaryBags.value = totalDealerBags > 0 ? totalDealerBags : '';
    summaryTons.value = totalTons > 0 ? totalTons.toFixed(2) : '';
    grandTotalInput.value = grandTotal > 0 ? grandTotal.toFixed(2) : '';

    // if (totalDealerBags > totalBags && totalBags > 0) {
    //     bagWarning.style.display = 'block';
    //     saveBtn.disabled = true;
    // } else {
    //     bagWarning.style.display = 'none';
    //     saveBtn.disabled = false;
    // }
    if (totalDealerBags > totalBags && totalBags > 0) {
    saveBtn.disabled = true;
    Swal.fire({
        icon: 'warning',
        title: '⚠ Limit Exceeded!',
        text: 'The total dealer bags have exceeded the bilti bags!',
        confirmButtonColor: '#d33',
        confirmButtonText: 'OK'
    });
} else {
    saveBtn.disabled = false;
}

}


    // Dealer validation on form submit
    document.getElementById('payableForm').addEventListener('submit', function(e) {
        let isValid = true;
        document.querySelectorAll('.dealer-form').forEach(form => {
            const dealerIdInput = form.querySelector('.dealer_id');
            const dealerSearch = form.querySelector('.dealer_search');
            if (!dealerIdInput.value) {
                isValid = false;
                dealerSearch.classList.add('is-invalid');
                const existingFeedback = dealerSearch.nextElementSibling;
                if (!existingFeedback || !existingFeedback.classList.contains('invalid-feedback')) {
                    dealerSearch.insertAdjacentHTML('afterend', '<div class="invalid-feedback">Please select a valid dealer from the list.</div>');
                }
            } else {
                dealerSearch.classList.remove('is-invalid');
                const existingFeedback = dealerSearch.nextElementSibling;
                if (existingFeedback && existingFeedback.classList.contains('invalid-feedback')) {
                    existingFeedback.remove();
                }
            }
        });
        if (!isValid) {
            e.preventDefault();
        }
    });
</script>
<style>
    .list-group-item.active {
        background-color: #007bff;
        color: white;
    }

    .list-group-item.not-found {
        cursor: default;
        font-style: italic;
    }
</style>
@endsection
