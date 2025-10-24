@extends('admin.layouts.shared')
@section('title', 'Create Payable & Receivable')
@section('header-title', 'Bilti Entry')
@section('content')
    <div class="row">
        <div class="col-12">
            <!-- PAYABLE FORM -->
            <div class="card mb-4">
                <div class="card-body">
                    <h4 class="mb-3">Create Bilti Entry</h4>
                    <form action="{{ route('payables.store') }}" method="POST" id="payableForm" enctype="multipart/form-data">
                        @csrf
                        <div class="excel-grid">
                            <table class="excel-table">
                                <thead>
                                    <tr>
                                        <th>Transaction Date<span class="text-danger">*</span></th>
                                        <th>Supplier<span class="text-danger">*</span></th>
                                        <th>Tons</th>
                                        <th>No of Bags<span class="text-danger">*</span></th>
                                        <th>Amount per Ton<span class="text-danger">*</span></th>
                                        <th>Total Amount</th>
                                        <th>Bilti No</th>
                                        <th>Truck No</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <input type="date" name="transaction_date" class="form-control excel-input"
                                                value="{{ old('transaction_date') }}" required>
                                        </td>
                                        <td class="position-relative">
                                            <input type="text" id="supplier_search" class="form-control excel-input"
                                                placeholder="Type supplier name..." value="{{ old('supplier_name') }}">
                                            <ul id="supplier_suggestion_list" class="list-group position-absolute w-100 shadow-sm"
                                                style="z-index: 1000; max-height: 200px; overflow-y: auto; display: none;">
                                                @foreach ($suppliers as $s)
                                                    <li class="list-group-item list-group-item-action" data-id="{{ $s->id }}"
                                                        style="cursor: pointer;">
                                                        {{ $s->supplier_name }}
                                                    </li>
                                                @endforeach
                                                <li class="list-group-item list-group-item-action text-muted not-found"
                                                    style="display: none;">
                                                    Not Found
                                                </li>
                                            </ul>
                                            <input type="hidden" name="supplier_id" id="supplier_id" value="{{ old('supplier_id') }}"
                                                required>
                                        </td>
                                        <td>
                                            <input type="text" id="tons" class="form-control excel-input">
                                        </td>
                                        <td>
                                            <input type="number" name="no_of_bags" id="no_of_bags" class="form-control excel-input"
                                                value="{{ old('no_of_bags') }}" readonly>
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" name="amount_per_bag" id="amount_per_bag"
                                                class="form-control excel-input" value="{{ old('amount_per_bag') }}" required>
                                        </td>
                                        <td>
                                            <input type="text" id="total_amount" class="form-control excel-input" readonly>
                                        </td>
                                        <td>
                                            <input type="text" name="bilti_no" id="payable_bilti_no" class="form-control excel-input"
                                                value="{{ old('bilti_no') }}">
                                        </td>
                                         <td>
                                            <input type="text" id="truck_no" class="form-control excel-input">
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <!-- RECEIVABLE FORM -->
                        <h4 class="mb-3 mt-4">Create Receivable</h4>
                        <div class="excel-grid">
                            <table class="excel-table">
                                <thead>
                                    <tr>
                                        <th>Bilti No</th>
                                        <th>Total Bags (From Bilti)</th>
                                        <th>Remaining Bags</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <input type="text" name="bilti_no" id="bilti_no" class="form-control excel-input" readonly>
                                        </td>
                                        <td>
                                            <input type="number" id="total_bags" class="form-control excel-input" readonly>
                                        </td>
                                        <td>
                                            <input type="number" id="remaining_bags" class="form-control excel-input" readonly>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <!-- Dealers Forms -->
                        <div id="dealersContainer" class="excel-grid"></div>
                        <div class="mb-3">
                            <button type="button" class="btn btn-success btn-sm" id="addDealerForm">+ Add Dealer</button>
                        </div>
                        <!-- Summary -->
                        <div class="mb-3">
                            <label><b>Summary</b></label>
                            <div class="excel-grid">
                                <table class="excel-table">
                                    <thead>
                                        <tr>
                                            <th>Total Allocated Bags</th>
                                            <th>Total Tons</th>
                                            <th>Grand Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <input type="text" id="summaryBags" class="form-control excel-input"
                                                    placeholder="Total Allocated Bags" readonly>
                                            </td>
                                            <td>
                                                <input type="text" id="summaryTons" class="form-control excel-input"
                                                    placeholder="Total Tons" readonly>
                                            </td>
                                            <td>
                                                <input type="text" id="grandTotal" class="form-control excel-input"
                                                    placeholder="Grand Total" readonly>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <small id="bagWarning" class="text-danger fw-bold" style="display:none;">
                            ⚠ The total dealer bags have exceeded the bilti bags!
                        </small>
                        <div class="mt-3">
                            <button type="submit" class="btn btn-primary btn-sm" id="saveBtn"><b>Submit</b></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
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
                if (!query || !supplierItems.some(li => li.textContent.toLowerCase().trim() === query)) {
                    supplierIdInput.value = '';
                }
            });

            supplierItems.forEach((li) => {
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

            const payableForm = document.getElementById('payableForm');
            payableForm.addEventListener('submit', function(e) {
                if (!supplierIdInput.value) {
                    e.preventDefault();
                    supplierSearch.classList.add('is-invalid');
                    supplierSearch.insertAdjacentHTML('afterend',
                        '<div class="invalid-feedback">Please select a valid supplier from the list.</div>'
                    );
                } else {
                    supplierSearch.classList.remove('is-invalid');
                    const existingFeedback = supplierSearch.nextElementSibling;
                    if (existingFeedback && existingFeedback.classList.contains('invalid-feedback')) {
                        existingFeedback.remove();
                    }
                }
            });
        });

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
                <table class="excel-table dealer-form" data-index="${dealerIndex}">
                    <thead>
                        <tr>
                            <th>Dealer</th>
                            <th>Bags</th>
                            <th>Tons</th>
                            <th>Rate Per Bag</th>
                            <th>Freight</th>
                            <th>Total</th>
                            <th>Payment Type</th>
                            <th>Proof of Payment</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="position-relative">
                                <input type="hidden" name="supplier_id" value="${document.querySelector('[name="supplier_id"]').value}">
                                <input type="text" class="form-control dealer_search excel-input" placeholder="Type dealer name..." data-index="${dealerIndex}">
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
                            </td>
                            <td>
                                <input type="number" name="bags[${dealerIndex}]" class="form-control bagsInput excel-input" value="">
                            </td>
                            <td>
                                <input type="number" class="form-control tonsInput excel-input" readonly>
                            </td>
                            <td>
                                <input type="number" step="0.01" name="rate[${dealerIndex}]" class="form-control rate excel-input" value="">
                            </td>
                            <td>
                                <input type="number" step="0.01" name="freight[${dealerIndex}]" class="form-control dealerFreight excel-input" value="">
                            </td>
                            <td>
                                <input type="text" class="form-control dealerTotal excel-input" readonly>
                            </td>
                            <td>
                                <select name="payment_type[${dealerIndex}]" class="form-control excel-input">
                                    <option value="">Select</option>
                                    <option value="credit">Credit</option>
                                    <option value="cash">Cash</option>
                                    <option value="online">Online</option>
                                    <option value="cheque">Cheque</option>
                                </select>
                            </td>
                            <td>
                                <input type="file" name="proof_of_payment[${dealerIndex}]" class="form-control excel-input">
                            </td>
                            <td>
                                <button type="button" class="btn btn-danger btn-sm removeDealer">Remove</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            `;
            dealersContainer.insertAdjacentHTML('beforeend', newForm);

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
                if (!query || !dealerItems.some(li => li.textContent.toLowerCase().trim() === query)) {
                    dealerIdInput.value = '';
                }
            });

            dealerItems.forEach((li) => {
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
            bagWarning.style.display = totalDealerBags > totalBags && totalBags > 0 ? 'block' : 'none';
            saveBtn.disabled = totalDealerBags > totalBags && totalBags > 0;
        }

        payableForm.addEventListener('submit', function(e) {
            let isValid = true;
            document.querySelectorAll('.dealer-form').forEach(form => {
                const dealerIdInput = form.querySelector('.dealer_id');
                const dealerSearch = form.querySelector('.dealer_search');
                if (!dealerIdInput.value) {
                    isValid = false;
                    dealerSearch.classList.add('is-invalid');
                    const existingFeedback = dealerSearch.nextElementSibling;
                    if (!existingFeedback || !existingFeedback.classList.contains('invalid-feedback')) {
                        dealerSearch.insertAdjacentHTML('afterend',
                            '<div class="invalid-feedback">Please select a valid dealer from the list.</div>'
                        );
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
        .excel-grid {
            margin-bottom: 1rem;
        }

        .excel-table {
            border-collapse: collapse;
            width: 100%;
            background-color: #ffffff;
            font-family: 'Calibri', 'Arial', sans-serif;
            font-size: 12px;
        }

        .excel-table th,
        .excel-table td {
            border: 1px solid #d3d3d3;
            padding: 4px;
            text-align: center;
            vertical-align: middle;
        }

        .excel-table th {
            background-color: #f2f2f2;
            font-weight: bold;
            color: #333;
            text-transform: uppercase;
            font-size: 11px;
            padding: 6px;
        }

        .excel-table td {
            background-color: #ffffff;
        }

        .excel-input {
            border: none;
            width: 100%;
            padding: 2px 4px;
            background: transparent;
            font-size: 12px;
            line-height: 1.2;
        }

        .excel-input:focus {
            outline: none;
            background-color: #e6f3fa;
        }

        .excel-table .position-relative {
            position: relative;
        }

        .list-group {
            border: 1px solid #d3d3d3;
            border-radius: 0;
            background-color: #ffffff;
        }

        .list-group-item {
            padding: 4px 8px;
            font-size: 12px;
            border: none;
            border-bottom: 1px solid #d3d3d3;
        }

        .list-group-item.active {
            background-color: #0078d4;
            color: #ffffff;
        }

        .list-group-item.not-found {
            cursor: default;
            font-style: italic;
            color: #888;
        }

        .excel-table .form-control[readonly] {
            background-color: #f4f4f4;
            cursor: not-allowed;
        }

        .excel-table select.excel-input {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background: transparent;
            padding-right: 20px;
            background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'><path fill='%23333' d='M6 9l4-4H2z'/></svg>");
            background-repeat: no-repeat;
            background-position: right 4px center;
        }

        .excel-table input[type="file"] {
            padding: 2px;
            font-size: 11px;
        }

        .btn-sm {
            font-size: 11px;
            padding: 4px 8px;
        }

        .invalid-feedback {
            font-size: 10px;
            text-align: left;
        }

        @media (max-width: 768px) {
            .excel-table th,
            .excel-table td {
                font-size: 10px;
                padding: 3px;
            }

            .excel-input {
                font-size: 10px;
                padding: 2px;
            }

            .btn-sm {
                font-size: 10px;
                padding: 3px 6px;
            }
        }
    </style>
@endsection