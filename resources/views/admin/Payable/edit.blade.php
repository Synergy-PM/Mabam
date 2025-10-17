@extends('admin.layouts.shared')
@section('title', 'Edit Payable & Receivable')
@section('header-title', 'Edit Payable & Receivable')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-body">
                    <h4 class="mb-3">Edit Payable</h4>
                    
                    {{-- Global JavaScript Data --}}
                    <script>
                        const allDealers = @json($dealers->map(function($dealer) {
                            return ['id' => $dealer->id, 'dealer_name' => $dealer->dealer_name];
                        }));
                        const allSuppliers = @json($suppliers->map(function($supplier) {
                            return ['id' => $supplier->id, 'supplier_name' => $supplier->supplier_name];
                        }));
                        let dealerIndex = {{ $receivables->count() ?? 0 }};
                    </script>

                    <form action="{{ route('payables.update', $payable->id) }}" method="POST" id="payableForm" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label><b>Transaction Date</b><span class="text-danger">*</span></label>
                                <input type="date" name="transaction_date" class="form-control"
                                    value="{{ old('transaction_date', $payable->transaction_date) }}" required>
                                @error('transaction_date')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3 position-relative">
                                <label><b>Supplier</b><span class="text-danger">*</span></label>
                                <input type="text" id="supplier_search" class="form-control mb-1"
                                    placeholder="Type supplier name..." 
                                    value="{{ old('supplier_name', $payable->supplier->supplier_name ?? '') }}">
                                <ul id="supplier_suggestion_list" class="list-group position-absolute w-100 shadow-sm"
                                    style="z-index: 1000; max-height: 200px; overflow-y: auto; display: none;">
                                </ul>
                                <input type="hidden" name="supplier_id" id="supplier_id" 
                                    value="{{ old('supplier_id', $payable->supplier_id) }}" required>
                                @error('supplier_id')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label><b>Tons</b></label>
                                <input type="text" id="tons" class="form-control"
                                    value="{{ old('tons', $payable->tons ?? '') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label><b>No of Bags</b><span class="text-danger">*</span></label>
                                <input type="number" name="no_of_bags" id="no_of_bags" class="form-control"
                                    value="{{ old('no_of_bags', $payable->no_of_bags) }}" readonly required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label><b>Amount per Ton</b><span class="text-danger">*</span></label>
                                <input type="number" step="0.01" name="amount_per_bag" id="amount_per_bag"
                                    class="form-control" value="{{ old('amount_per_bag', $payable->amount_per_bag) }}" required>
                                @error('amount_per_bag')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label><b>Total Amount</b></label>
                                <input type="text" id="total_amount" class="form-control" readonly
                                    value="{{ old('total_amount', $payable->total_amount) }}">
                            </div>
                            <div class="col-md-12 mb-3">
                                <label><b>Bilti No</b></label>
                                <input type="text" name="bilti_no" id="payable_bilti_no" class="form-control"
                                    value="{{ old('bilti_no', $payable->bilti_no) }}">
                                @error('bilti_no')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <h4 class="mb-3 mt-5">Edit Receivables</h4>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label><b>Bilti No</b></label>
                                <input type="text" id="bilti_no" class="form-control" readonly>
                            </div>
                            <div class="col-md-4">
                                <label><b>Total Bags</b></label>
                                <input type="number" id="total_bags" class="form-control" readonly>
                            </div>
                            <div class="col-md-4">
                                <label><b>Remaining Bags</b></label>
                                <input type="number" id="remaining_bags" class="form-control" readonly>
                            </div>
                        </div>

                        <div id="dealersContainer">
                            @if(isset($receivables) && $receivables->count() > 0)
                                @foreach($receivables as $index => $receivable)
                                <div class="border p-3 mt-3 rounded bg-light dealer-form" data-index="{{ $index }}">
                                    <input type="hidden" name="receivable_id[{{ $index }}]" value="{{ $receivable->id }}">
                                    <div class="row">
                                        <div class="col-md-4 mb-3 position-relative">
                                            <label>Dealer <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control dealer_search mb-1" 
                                                value="{{ $receivable->dealer->dealer_name ?? '' }}" 
                                                data-index="{{ $index }}">
                                            <ul class="list-group dealer_suggestion_list position-absolute w-100 shadow-sm"
                                                style="z-index: 1000; max-height: 200px; overflow-y: auto; display: none;"
                                                data-index="{{ $index }}">
                                            </ul>
                                            <input type="hidden" name="dealer_id[{{ $index }}]" class="dealer_id" 
                                                value="{{ $receivable->dealer_id }}" required>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label>Bags <span class="text-danger">*</span></label>
                                            <input type="number" name="bags[{{ $index }}]" class="form-control bagsInput" 
                                                value="{{ old('bags.' . $index, $receivable->bags) }}" min="1" required>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label>Tons</label>
                                            <input type="number" class="form-control tonsInput" readonly
                                                value="{{ number_format($receivable->bags / 20, 2) }}">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label>Rate Per Bag <span class="text-danger">*</span></label>
                                            <input type="number" step="0.01" name="rate[{{ $index }}]" class="form-control rate" 
                                                value="{{ old('rate.' . $index, $receivable->rate) }}" min="0.01" required>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label>Freight</label>
                                            <input type="number" step="0.01" name="freight[{{ $index }}]" 
                                                class="form-control dealerFreight" 
                                                value="{{ old('freight.' . $index, $receivable->freight ?? 0) }}" min="0">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label>Total</label>
                                            <input type="text" class="form-control dealerTotal" readonly
                                                value="{{ number_format(($receivable->bags * $receivable->rate) - ($receivable->freight ?? 0), 2) }}">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label>Code Number <span class="text-danger">*</span></label>
                                            <input type="text" name="code[{{ $index }}]" class="form-control" 
                                                value="{{ old('code.' . $index, $receivable->code) }}" required>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label>Payment Type <span class="text-danger">*</span></label>
                                            <select name="payment_type[{{ $index }}]" class="form-control" required>
                                                <option value="">Select</option>
                                                <option value="credit" {{ old('payment_type.' . $index, $receivable->payment_type) == 'credit' ? 'selected' : '' }}>Credit</option>
                                                <option value="cash" {{ old('payment_type.' . $index, $receivable->payment_type) == 'cash' ? 'selected' : '' }}>Cash</option>
                                                <option value="online" {{ old('payment_type.' . $index, $receivable->payment_type) == 'online' ? 'selected' : '' }}>Online</option>
                                                <option value="cheque" {{ old('payment_type.' . $index, $receivable->payment_type) == 'cheque' ? 'selected' : '' }}>Cheque</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label>Proof of Payment</label>
                                            <input type="file" name="proof_of_payment[{{ $index }}]" class="form-control" accept="image/*,.pdf">
                                            @if($receivable->proof_of_payment)
                                                <small class="text-muted mt-1 d-block">
                                                    Current: <a href="{{ asset('storage/' . $receivable->proof_of_payment) }}" target="_blank">View</a>
                                                </small>
                                            @endif
                                            <small class="text-muted">JPG, PNG, PDF only (Max 2MB)</small>
                                        </div>
                                        <div class="col-md-2 mb-3 d-flex align-items-end">
                                            <button type="button" class="btn btn-danger removeDealer">Remove</button>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            @endif
                        </div>

                        <div class="mb-3">
                            <button type="button" class="btn btn-success" id="addDealerForm">+ Add Dealer</button>
                        </div>

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
                            <a href="{{ route('payables.index') }}" class="btn btn-secondary ms-2">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            initializeSupplierSearch();
            calcFromTons();
            syncReceivableForm();
            initializeDealerSearches();
            calculateTotals();
        });

        // Elements
        const noBags = document.getElementById('no_of_bags');
        const rate = document.getElementById('amount_per_bag');
        const total = document.getElementById('total_amount');
        const tons = document.getElementById('tons');
        const payableBiltiInput = document.getElementById('payable_bilti_no');
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

        // Calculations
        function calcFromTons() {
            let t = parseFloat(tons.value) || 0;
            let r = parseFloat(rate.value) || 0;
            let b = t * 20;
            noBags.value = b > 0 ? b.toFixed(0) : '';
            total.value = (t * r) > 0 ? (t * r).toFixed(2) : '';
            syncReceivableForm();
        }

        function calcFromRate() {
            let t = parseFloat(tons.value) || 0;
            let r = parseFloat(rate.value) || 0;
            total.value = (t * r) > 0 ? (t * r).toFixed(2) : '';
        }

        tons.addEventListener('input', calcFromTons);
        rate.addEventListener('input', calcFromRate);

        function syncReceivableForm() {
            biltiInput.value = payableBiltiInput.value;
            totalBags = parseFloat(noBags.value) || 0;
            totalBagsInput.value = totalBags > 0 ? totalBags : '';
            calculateTotals();
        }

        payableBiltiInput.addEventListener('input', syncReceivableForm);

        function highlightItem(list, index, allItems) {
            allItems.forEach((li, i) => li.classList.toggle('active', i === index));
        }

        // Supplier Search
        function initializeSupplierSearch() {
            const supplierSearch = document.getElementById('supplier_search');
            const supplierSuggestionList = document.getElementById('supplier_suggestion_list');
            const supplierIdInput = document.getElementById('supplier_id');

            supplierSearch.addEventListener('input', function() {
                const query = this.value.toLowerCase().trim();
                supplierSuggestionList.innerHTML = '';
                
                if (query) {
                    let hasMatches = false;
                    allSuppliers.forEach(supplier => {
                        if (supplier.supplier_name.toLowerCase().includes(query)) {
                            const li = document.createElement('li');
                            li.className = 'list-group-item list-group-item-action';
                            li.setAttribute('data-id', supplier.id);
                            li.style.cursor = 'pointer';
                            li.textContent = supplier.supplier_name;
                            li.addEventListener('click', function() {
                                supplierSearch.value = supplier.supplier_name;
                                supplierIdInput.value = supplier.id;
                                supplierSuggestionList.style.display = 'none';
                            });
                            supplierSuggestionList.appendChild(li);
                            hasMatches = true;
                        }
                    });
                    
                    if (!hasMatches) {
                        const notFound = document.createElement('li');
                        notFound.className = 'list-group-item list-group-item-action text-muted not-found';
                        notFound.textContent = 'Not Found';
                        supplierSuggestionList.appendChild(notFound);
                    }
                    supplierSuggestionList.style.display = 'block';
                } else {
                    supplierSuggestionList.style.display = 'none';
                }
            });
        }

        // Dealer Search Functions
        function initializeNewDealerSearch(index) {
            const dealerSearch = dealersContainer.querySelector(`.dealer_search[data-index="${index}"]`);
            const dealerSuggestionList = dealersContainer.querySelector(`.dealer_suggestion_list[data-index="${index}"]`);
            const dealerIdInput = dealersContainer.querySelector(`.dealer_id[name="dealer_id[${index}]"]`);
            
            if (!dealerSearch) return;

            // Dynamic dealer list
            let dealerListHtml = allDealers.map(dealer => `
                <li class="list-group-item list-group-item-action" data-id="${dealer.id}" style="cursor: pointer;">
                    ${dealer.dealer_name}
                </li>
            `).join('');
            dealerSuggestionList.innerHTML = dealerListHtml + 
                '<li class="list-group-item list-group-item-action text-muted not-found" style="display: none;">Not Found</li>';

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

            dealerItems.forEach(li => {
                li.addEventListener('click', function() {
                    dealerSearch.value = this.textContent.trim();
                    dealerIdInput.value = this.getAttribute('data-id');
                    dealerSuggestionList.style.display = 'none';
                    dealerNotFoundItem.style.display = 'none';
                    dealerSearch.classList.remove('is-invalid');
                });
            });

            dealerSuggestionList.style.display = 'none';
            dealerNotFoundItem.style.display = 'none';
        }

        function initializeDealerSearches() {
            document.querySelectorAll('.dealer_search').forEach((dealerSearch, currentIndex) => {
                const index = dealerSearch.dataset.index;
                initializeNewDealerSearch(index);
            });
        }

        // Add Dealer Form
        document.getElementById('addDealerForm').addEventListener('click', function() {
            let dealerListHtml = allDealers.map(dealer => `
                <li class="list-group-item list-group-item-action" data-id="${dealer.id}" style="cursor: pointer;">
                    ${dealer.dealer_name}
                </li>
            `).join('');
            
            let newForm = `
                <div class="border p-3 mt-3 rounded bg-light dealer-form" data-index="${dealerIndex}">
                    <div class="row">
                        <input type="hidden" name="supplier_id" value="${document.querySelector('[name="supplier_id"]').value || ''}">
                        <div class="col-md-4 mb-3 position-relative">
                            <label>Dealer <span class="text-danger">*</span></label>
                            <input type="text" class="form-control dealer_search mb-1" placeholder="Type dealer name..." data-index="${dealerIndex}">
                            <ul class="list-group dealer_suggestion_list position-absolute w-100 shadow-sm"
                                style="z-index: 1000; max-height: 200px; overflow-y: auto; display: none;" data-index="${dealerIndex}">
                                ${dealerListHtml}
                                <li class="list-group-item list-group-item-action text-muted not-found" style="display: none;">Not Found</li>
                            </ul>
                            <input type="hidden" name="dealer_id[${dealerIndex}]" class="dealer_id" required>
                        </div>
                        <div class="col-md-4 mb-2">
                            <label>Bags <span class="text-danger">*</span></label>
                            <input type="number" name="bags[${dealerIndex}]" class="form-control bagsInput" min="1" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>Tons</label>
                            <input type="number" class="form-control tonsInput" readonly>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>Rate Per Bag <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="rate[${dealerIndex}]" class="form-control rate" min="0.01" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>Freight</label>
                            <input type="number" step="0.01" name="freight[${dealerIndex}]" class="form-control dealerFreight" value="0" min="0">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>Total</label>
                            <input type="text" class="form-control dealerTotal" readonly>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>Code Number <span class="text-danger">*</span></label>
                            <input type="text" name="code[${dealerIndex}]" class="form-control" placeholder="Unique code" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>Payment Type <span class="text-danger">*</span></label>
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
                            <input type="file" name="proof_of_payment[${dealerIndex}]" class="form-control" accept="image/*,.pdf">
                            <small class="text-muted">JPG, PNG, PDF only</small>
                        </div>
                        <div class="col-md-4 mb-3 d-flex align-items-end">
                            <button type="button" class="btn btn-danger removeDealer">Remove</button>
                        </div>
                    </div>
                </div>
            `;
            
            dealersContainer.insertAdjacentHTML('beforeend', newForm);
            initializeNewDealerSearch(dealerIndex);
            dealerIndex++;
            calculateTotals();
        });

        // Remove Dealer
        dealersContainer.addEventListener('click', function(e) {
            if (e.target.classList.contains('removeDealer')) {
                if (confirm('Are you sure you want to remove this dealer?')) {
                    e.target.closest('.dealer-form').remove();
                    calculateTotals();
                }
            }
        });

        // Input Calculations
        dealersContainer.addEventListener('input', function(e) {
            if (e.target.classList.contains('bagsInput')) {
                let form = e.target.closest('.dealer-form');
                let bagsVal = parseFloat(e.target.value) || 0;
                let tonsField = form.querySelector('.tonsInput');
                tonsField.value = bagsVal > 0 ? (bagsVal / 20).toFixed(2) : '';
            }
            if (e.target.classList.contains('rate') || e.target.classList.contains('dealerFreight') || e.target.classList.contains('bagsInput')) {
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
                let dealerTotal = bags * (rate - freight);
                form.querySelector('.dealerTotal').value = dealerTotal > 0 ? dealerTotal.toFixed(2) : '';
                
                totalDealerBags += bags;
                grandTotal += dealerTotal;
                totalTons += tons;
            });
            
            remainingBagsInput.value = totalBags > 0 ? (totalBags - totalDealerBags) : '';
            summaryBags.value = totalDealerBags > 0 ? totalDealerBags : '';
            summaryTons.value = totalTons > 0 ? totalTons.toFixed(2) : '';
            grandTotalInput.value = grandTotal > 0 ? grandTotal.toFixed(2) : '';
            
            if (totalDealerBags > totalBags && totalBags > 0) {
                bagWarning.style.display = 'block';
                saveBtn.disabled = true;
            } else {
                bagWarning.style.display = 'none';
                saveBtn.disabled = false;
            }
        }

        document.getElementById('payableForm').addEventListener('submit', function(e) {
            let isValid = true;
            
            document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            document.querySelectorAll('.invalid-feedback').forEach(el => el.remove());
            
            const supplierIdInput = document.getElementById('supplier_id');
            const supplierSearch = document.getElementById('supplier_search');
            if (!supplierIdInput.value) {
                isValid = false;
                supplierSearch.classList.add('is-invalid');
                supplierSearch.insertAdjacentHTML('afterend', 
                    '<div class="invalid-feedback">Please select a valid supplier.</div>'
                );
            }
            
            document.querySelectorAll('.dealer-form').forEach(form => {
                const dealerIdInput = form.querySelector('.dealer_id');
                const dealerSearch = form.querySelector('.dealer_search');
                const bagsInput = form.querySelector('.bagsInput');
                const rateInput = form.querySelector('.rate');
                const paymentType = form.querySelector('select[name*="payment_type"]');
                const codeInput = form.querySelector('input[name*="code"]');
                
                if (!dealerIdInput.value) {
                    isValid = false;
                    dealerSearch.classList.add('is-invalid');
                    dealerSearch.insertAdjacentHTML('afterend', 
                        '<div class="invalid-feedback">Please select a valid dealer.</div>'
                    );
                }
                
                if (!bagsInput.value || parseInt(bagsInput.value) <= 0) {
                    isValid = false;
                    bagsInput.classList.add('is-invalid');
                }
                
                if (!rateInput.value || parseFloat(rateInput.value) <= 0) {
                    isValid = false;
                    rateInput.classList.add('is-invalid');
                }
                
                if (!paymentType.value) {
                    isValid = false;
                    paymentType.classList.add('is-invalid');
                }
                
                if (!codeInput.value) {
                    isValid = false;
                    codeInput.classList.add('is-invalid');
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error!',
                    text: 'Please fix all errors before submitting.',
                });
            }
        });

        // Hide dropdowns on outside click
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.position-relative')) {
                document.querySelectorAll('.dealer_suggestion_list, #supplier_suggestion_list').forEach(list => {
                    list.style.display = 'none';
                });
            }
        });
    </script>

    <style>
        .list-group-item:hover {
            background-color: #f8f9fa;
        }
        .list-group-item.active {
            background-color: #007bff !important;
            color: white !important;
        }
        .list-group-item.not-found {
            cursor: default;
            font-style: italic;
        }
        .dealer_suggestion_list, #supplier_suggestion_list {
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            max-height: 200px;
            overflow-y: auto;
        }
        .is-invalid {
            border-color: #dc3545 !important;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
        }
        .invalid-feedback {
            display: block !important;
            width: 100%;
            margin-top: 0.25rem;
            font-size: 0.875em;
            color: #dc3545;
        }
    </style>
@endsection