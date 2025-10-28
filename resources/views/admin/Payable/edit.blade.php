@extends('admin.layouts.shared')
@section('title', 'Edit Bilti Entry')
@section('header-title', 'Edit Bilti Entry')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card mb-4">
            <div class="card-body">
                <h4 class="mb-3">Edit Bilti Entry</h4>

                <form action="{{ route('payables.update', $payable->id) }}" method="POST"
                      id="payableForm" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

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
                                    <input type="date" name="transaction_date"
                                           class="form-control excel-input"
                                           value="{{ old('transaction_date', optional($payable->transaction_date)->format('Y-m-d')) }}"
                                           required>
                                </td>
                                <td class="position-relative">
                                    <input type="text" id="supplier_search"
                                           class="form-control excel-input"
                                           placeholder="Type supplier name..."
                                           value="{{ old('supplier_name', $payable->supplier->supplier_name ?? '') }}">
                                    <ul id="supplier_suggestion_list"
                                        class="list-group position-absolute w-100 shadow-sm"
                                        style="z-index:1000;max-height:200px;overflow-y:auto;display:none;">
                                        @foreach($suppliers as $s)
                                            <li class="list-group-item list-group-item-action"
                                                data-id="{{ $s->id }}" style="cursor:pointer;">
                                                {{ $s->supplier_name }}
                                            </li>
                                        @endforeach
                                        <li class="list-group-item list-group-item-action text-muted not-found"
                                            style="display:none;">Not Found</li>
                                    </ul>
                                    <input type="hidden" name="supplier_id" id="supplier_id"
                                           value="{{ old('supplier_id', $payable->supplier_id) }}" required>
                                </td>
                                <td>
                                    <input type="text" id="tons" class="form-control excel-input"
                                           value="{{ old('tons', $payable->tons ?? '') }}">
                                </td>
                                <td>
                                    <input type="number" name="no_of_bags" id="no_of_bags"
                                           class="form-control excel-input"
                                           value="{{ old('no_of_bags', $payable->no_of_bags) }}" readonly required>
                                </td>
                                <td>
                                    <input type="number" step="0.01" name="amount_per_bag" id="amount_per_bag"
                                           class="form-control excel-input"
                                           value="{{ old('amount_per_bag', $payable->amount_per_bag) }}" required>
                                </td>
                                <td>
                                    <input type="text" id="total_amount" class="form-control excel-input" readonly
                                           value="{{ old('total_amount', $payable->total_amount) }}">
                                </td>
                                <td>
                                    <input type="text" name="bilti_no" id="payable_bilti_no"
                                           class="form-control excel-input"
                                           value="{{ old('bilti_no', $payable->bilti_no) }}">
                                </td>
                                <td>
                                    <input type="text" name="truck_no" id="truck_no"
                                           class="form-control excel-input"
                                           value="{{ old('truck_no', $payable->truck_no) }}">
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                    <h4 class="mb-3 mt-4">Receivable</h4>
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
                                <td><input type="text" id="bilti_no" class="form-control excel-input" readonly></td>
                                <td><input type="number" id="total_bags" class="form-control excel-input" readonly></td>
                                <td><input type="number" id="remaining_bags" class="form-control excel-input" readonly></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                    <div id="dealersContainer" class="excel-grid"></div>

                    <div class="mb-3">
                        <button type="button" class="btn btn-success btn-sm" id="addDealerForm">+ Add Dealer</button>
                    </div>
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
                                    <td><input type="text" id="summaryBags" class="form-control excel-input" readonly></td>
                                    <td><input type="text" id="summaryTons" class="form-control excel-input" readonly></td>
                                    <td><input type="text" id="grandTotal" class="form-control excel-input" readonly></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <small id="bagWarning" class="text-danger fw-bold" style="display:none;">
                        Warning: The total dealer bags have exceeded the bilti bags!
                    </small>

                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary btn-sm" id="saveBtn"><b>Update</b></button>
                        <a href="{{ route('payables.index') }}" class="btn btn-secondary btn-sm ms-2">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const supplierSearch = document.getElementById('supplier_search');
        const supplierSuggestionList = document.getElementById('supplier_suggestion_list');
        const supplierIdInput = document.getElementById('supplier_id');
        const supplierItems = Array.from(supplierSuggestionList.querySelectorAll('li:not(.not-found)'));
        const notFoundItem = supplierSuggestionList.querySelector('.not-found');
        let supplierSelectedIndex = -1;

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
        let dealerIndex = {{ $receivables->count() ?? 0 }};

        supplierSearch.addEventListener('input', function () {
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

        supplierItems.forEach(li => {
            li.addEventListener('click', function () {
                supplierSearch.value = this.textContent.trim();
                supplierIdInput.value = this.getAttribute('data-id');
                supplierSuggestionList.style.display = 'none';
                notFoundItem.style.display = 'none';
            });
        });

        supplierSearch.addEventListener('keydown', function (e) {
            const visible = supplierItems.filter(li => li.style.display !== 'none');
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                supplierSelectedIndex = (supplierSelectedIndex + 1) % visible.length;
                highlightItem(visible, supplierSelectedIndex, supplierItems);
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                supplierSelectedIndex = (supplierSelectedIndex - 1 + visible.length) % visible.length;
                highlightItem(visible, supplierSelectedIndex, supplierItems);
            } else if (e.key === 'Enter') {
                e.preventDefault();
                if (supplierSelectedIndex >= 0 && visible[supplierSelectedIndex]) {
                    supplierSearch.value = visible[supplierSelectedIndex].textContent.trim();
                    supplierIdInput.value = visible[supplierSelectedIndex].getAttribute('data-id');
                    supplierSuggestionList.style.display = 'none';
                    notFoundItem.style.display = 'none';
                }
            }
        });

        document.addEventListener('click', e => {
            if (!e.target.closest('.position-relative')) {
                supplierSuggestionList.style.display = 'none';
                notFoundItem.style.display = 'none';
            }
        });

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
        window.addEventListener('load', calcFromTons);

        function syncReceivableForm() {
            biltiInput.value = payableBiltiInput.value;
            totalBags = parseFloat(noBags.value) || 0;
            totalBagsInput.value = totalBags > 0 ? totalBags : '';
            remainingBagsInput.value = totalBags > 0 ? totalBags : '';
            calculateTotals();
        }

        payableBiltiInput.addEventListener('input', syncReceivableForm);
        tons.addEventListener('input', syncReceivableForm);
        window.addEventListener('load', syncReceivableForm);

        function highlightItem(list, index, allItems) {
            allItems.forEach((li, i) => li.classList.toggle('active', i === index));
        }

        document.getElementById('addDealerForm').addEventListener('click', function () {
            const newRow = `
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
                                <input type="hidden" name="receivable_id[${dealerIndex}]" value="">
                                <input type="hidden" name="supplier_id" value="${supplierIdInput.value}">
                                <input type="text" class="form-control dealer_search excel-input"
                                       placeholder="Type dealer name..." data-index="${dealerIndex}">
                                <ul class="list-group dealer_suggestion_list position-absolute w-100 shadow-sm"
                                    style="z-index:1000;max-height:200px;overflow-y:auto;display:none;"
                                    data-index="${dealerIndex}">
                                    @foreach ($dealers as $dealer)
                                        <li class="list-group-item list-group-item-action"
                                            data-id="{{ $dealer->id }}" style="cursor:pointer;">
                                            {{ $dealer->dealer_name }}
                                        </li>
                                    @endforeach
                                    <li class="list-group-item list-group-item-action text-muted not-found"
                                        style="display:none;">Not Found</li>
                                </ul>
                                <input type="hidden" name="dealer_id[${dealerIndex}]" class="dealer_id" required>
                            </td>
                            <td><input type="number" name="bags[${dealerIndex}]" class="form-control bagsInput excel-input" min="1" required></td>
                            <td><input type="number" class="form-control tonsInput excel-input" readonly></td>
                            <td><input type="number" step="0.01" name="rate[${dealerIndex}]" class="form-control rate excel-input" min="0.01" required></td>
                            <td><input type="number" step="0.01" name="freight[${dealerIndex}]" class="form-control dealerFreight excel-input" value="0"></td>
                            <td><input type="text" class="form-control dealerTotal excel-input" readonly></td>
                            <td>
                                <select name="payment_type[${dealerIndex}]" class="form-control excel-input">
                                    <option value="">Select</option>
                                    <option value="credit">Credit</option>
                                    <option value="cash">Cash</option>
                                    <option value="online">Online</option>
                                    <option value="cheque">Cheque</option>
                                </select>
                            </td>
                            <td><input type="file" name="proof_of_payment[${dealerIndex}]" class="form-control excel-input" accept="image/*,.pdf"></td>
                            <td><button type="button" class="btn btn-danger btn-sm removeDealer">Remove</button></td>
                        </tr>
                    </tbody>
                </table>`;
            dealersContainer.insertAdjacentHTML('beforeend', newRow);
            initDealerAutocomplete(dealerIndex);
            dealerIndex++;
            calculateTotals();
        });

        dealersContainer.addEventListener('click', e => {
            if (e.target.classList.contains('removeDealer')) {
                e.target.closest('.dealer-form').remove();
                calculateTotals();
            }
        });

        dealersContainer.addEventListener('input', e => {
            if (e.target.classList.contains('bagsInput')) {
                const form = e.target.closest('.dealer-form');
                const bags = parseFloat(e.target.value) || 0;
                form.querySelector('.tonsInput').value = bags > 0 ? (bags / 20).toFixed(2) : '';
            }
            calculateTotals();
        });

        dealersContainer.addEventListener('input', e => {
            if (e.target.classList.contains('rate') || e.target.classList.contains('dealerFreight')) {
                calculateTotals();
            }
        });

        /* ---------- GRAND CALCULATION (summary, warning, disable submit) ---------- */
        function calculateTotals() {
            let totalDealerBags = 0, grandTotal = 0, totalTons = 0;
            document.querySelectorAll('.dealer-form').forEach(form => {
                const bags = parseFloat(form.querySelector('.bagsInput').value) || 0;
                const rate = parseFloat(form.querySelector('.rate').value) || 0;
                const freight = parseFloat(form.querySelector('.dealerFreight').value) || 0;
                const tons = bags / 20;
                form.querySelector('.tonsInput').value = bags > 0 ? tons.toFixed(2) : '';
                const dealerTotal = bags * (rate - freight);
                form.querySelector('.dealerTotal').value = dealerTotal > 0 ? dealerTotal.toFixed(2) : '';
                totalDealerBags += bags;
                grandTotal += dealerTotal;
                totalTons += tons;
            });

            const remaining = totalBags - totalDealerBags;
            remainingBagsInput.value = remaining >= 0 ? remaining : '';
            summaryBags.value = totalDealerBags > 0 ? totalDealerBags : '';
            summaryTons.value = totalTons > 0 ? totalTons.toFixed(2) : '';
            grandTotalInput.value = grandTotal > 0 ? grandTotal.toFixed(2) : '';

            const over = totalDealerBags > totalBags && totalBags > 0;
            bagWarning.style.display = over ? 'block' : 'none';
            saveBtn.disabled = over;
        }

        function initExistingDealers() {
            @if($receivables->count())
                @foreach($receivables as $idx => $rec)
                    (function () {
                        const idx = {{ $idx }};
                        const rec = @json($rec);
                        const dealer = @json($rec->dealer);
                        const rowHtml = `
                            <table class="excel-table dealer-form" data-index="${idx}">
                                <thead>
                                    <tr>
                                        <th>Dealer</th><th>Bags</th><th>Tons</th><th>Rate Per Bag</th>
                                        <th>Freight</th><th>Total</th><th>Payment Type</th>
                                        <th>Proof of Payment</th><th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="position-relative">
                                            <input type="hidden" name="receivable_id[${idx}]" value="${rec.id}">
                                            <input type="hidden" name="supplier_id" value="${supplierIdInput.value}">
                                            <input type="text" class="form-control dealer_search excel-input"
                                                   value="${dealer.dealer_name}" data-index="${idx}" readonly>
                                            <ul class="list-group dealer_suggestion_list position-absolute w-100 shadow-sm"
                                                style="z-index:1000;max-height:200px;overflow-y:auto;display:none;"
                                                data-index="${idx}">
                                                @foreach ($dealers as $d)
                                                    <li class="list-group-item list-group-item-action"
                                                        data-id="{{ $d->id }}" style="cursor:pointer;">
                                                        {{ $d->dealer_name }}
                                                    </li>
                                                @endforeach
                                                <li class="list-group-item list-group-item-action text-muted not-found"
                                                    style="display:none;">Not Found</li>
                                            </ul>
                                            <input type="hidden" name="dealer_id[${idx}]" class="dealer_id"
                                                   value="${rec.dealer_id}" required>
                                        </td>
                                        <td><input type="number" name="bags[${idx}]" class="form-control bagsInput excel-input"
                                                   value="${rec.bags}" min="1" required></td>
                                        <td><input type="number" class="form-control tonsInput excel-input"
                                                   value="${(rec.bags/20).toFixed(2)}" readonly></td>
                                        <td><input type="number" step="0.01" name="rate[${idx}]"
                                                   class="form-control rate excel-input" value="${rec.rate}" required></td>
                                        <td><input type="number" step="0.01" name="freight[${idx}]"
                                                   class="form-control dealerFreight excel-input"
                                                   value="${rec.freight ?? 0}"></td>
                                        <td><input type="text" class="form-control dealerTotal excel-input"
                                                   value="${(rec.bags * (rec.rate - (rec.freight ?? 0))).toFixed(2)}" readonly></td>
                                        <td>
                                            <select name="payment_type[${idx}]" class="form-control excel-input">
                                                <option value="">Select</option>
                                                @foreach(['credit','cash','online','cheque'] as $pt)
                                                    <option value="{{ $pt }}" {{ $rec->payment_type == $pt ? 'selected' : '' }}>{{ ucfirst($pt) }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input type="file" name="proof_of_payment[${idx}]"
                                                   class="form-control excel-input" accept="image/*,.pdf">
                                            @if($rec->proof_of_payment)
                                                <small class="text-muted d-block">
                                                    Current: <a href="{{ asset('storage/' . $rec->proof_of_payment) }}" target="_blank">View</a>
                                                </small>
                                            @endif
                                        </td>
                                        <td><button type="button" class="btn btn-danger btn-sm removeDealer">Remove</button></td>
                                    </tr>
                                </tbody>
                            </table>`;
                        dealersContainer.insertAdjacentHTML('beforeend', rowHtml);
                        initDealerAutocomplete(idx);
                    })();
                @endforeach
            @endif
            calculateTotals();
        }

        function initDealerAutocomplete(index) {
            const search = dealersContainer.querySelector(`.dealer_search[data-index="${index}"]`);
            const list = dealersContainer.querySelector(`.dealer_suggestion_list[data-index="${index}"]`);
            const hidden = dealersContainer.querySelector(`.dealer_id[name="dealer_id[${index}]"]`);
            const items = Array.from(list.querySelectorAll('li:not(.not-found)'));
            const notFound = list.querySelector('.not-found');
            let sel = -1;

            search.addEventListener('input', () => {
                const q = search.value.toLowerCase().trim();
                sel = -1;
                list.style.display = q ? 'block' : 'none';
                let match = false;
                items.forEach(li => {
                    const n = li.textContent.toLowerCase();
                    li.style.display = n.includes(q) ? 'block' : 'none';
                    if (n.includes(q)) match = true;
                });
                notFound.style.display = q && !match ? 'block' : 'none';
                if (!items.some(li => li.textContent.toLowerCase().trim() === q)) hidden.value = '';
            });

            items.forEach(li => {
                li.addEventListener('click', () => {
                    search.value = li.textContent.trim();
                    hidden.value = li.getAttribute('data-id');
                    list.style.display = 'none';
                    notFound.style.display = 'none';
                    search.classList.remove('is-invalid');
                });
            });

            search.addEventListener('keydown', e => {
                const visible = items.filter(li => li.style.display !== 'none');
                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    sel = (sel + 1) % visible.length;
                    highlightItem(visible, sel, items);
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    sel = (sel - 1 + visible.length) % visible.length;
                    highlightItem(visible, sel, items);
                } else if (e.key === 'Enter') {
                    e.preventDefault();
                    if (sel >= 0 && visible[sel]) {
                        search.value = visible[sel].textContent.trim();
                        hidden.value = visible[sel].getAttribute('data-id');
                        list.style.display = 'none';
                        notFound.style.display = 'none';
                    }
                }
            });
        }

        document.getElementById('payableForm').addEventListener('submit', e => {
            let ok = true;

            // supplier
            if (!supplierIdInput.value) {
                ok = false;
                supplierSearch.classList.add('is-invalid');
                if (!supplierSearch.nextElementSibling?.classList.contains('invalid-feedback')) {
                    supplierSearch.insertAdjacentHTML('afterend',
                        '<div class="invalid-feedback">Please select a valid supplier.</div>');
                }
            } else {
                supplierSearch.classList.remove('is-invalid');
                const fb = supplierSearch.nextElementSibling;
                if (fb && fb.classList.contains('invalid-feedback')) fb.remove();
            }

            document.querySelectorAll('.dealer-form').forEach(form => {
                const dealerId = form.querySelector('.dealer_id');
                const dealerSearch = form.querySelector('.dealer_search');
                const bags = form.querySelector('.bagsInput');
                const rate = form.querySelector('.rate');
                const payType = form.querySelector('select[name*="payment_type"]');

                if (!dealerId.value) {
                    ok = false;
                    dealerSearch.classList.add('is-invalid');
                    if (!dealerSearch.nextElementSibling?.classList.contains('invalid-feedback')) {
                        dealerSearch.insertAdjacentHTML('afterend',
                            '<div class="invalid-feedback">Please select a valid dealer.</div>');
                    }
                } else {
                    dealerSearch.classList.remove('is-invalid');
                    const fb = dealerSearch.nextElementSibling;
                    if (fb && fb.classList.contains('invalid-feedback')) fb.remove();
                }

                if (!bags.value || parseInt(bags.value) <= 0) { ok = false; bags.classList.add('is-invalid'); }
                else bags.classList.remove('is-invalid');

                if (!rate.value || parseFloat(rate.value) <= 0) { ok = false; rate.classList.add('is-invalid'); }
                else rate.classList.remove('is-invalid');

                if (!payType.value) { ok = false; payType.classList.add('is-invalid'); }
                else payType.classList.remove('is-invalid');
            });

            if (!ok) {
                e.preventDefault();
                Swal.fire({icon:'error',title:'Validation Error',text:'Please fix all errors before submitting.'});
            }
        });

        initExistingDealers();
    });
</script>

<style>
    .excel-grid{margin-bottom:1rem;}
    .excel-table{border-collapse:collapse;width:100%;background:#fff;font-family:'Calibri','Arial',sans-serif;font-size:12px;}
    .excel-table th,.excel-table td{border:1px solid #d3d3d3;padding:4px;text-align:center;vertical-align:middle;}
    .excel-table th{background:#f2f2f2;font-weight:bold;color:#333;text-transform:uppercase;font-size:11px;padding:6px;}
    .excel-input{border:none;width:100%;padding:2px 4px;background:transparent;font-size:12px;line-height:1.2;}
    .excel-input:focus{outline:none;background:#e6f3fa;}
    .position-relative{position:relative;}
    .list-group{border:1px solid #d3d3d3;border-radius:0;background:#fff;}
    .list-group-item{padding:4px 8px;font-size:12px;border:none;border-bottom:1px solid #d3d3d3;}
    .list-group-item.active{background:#0078d4;color:#fff;}
    .list-group-item.not-found{cursor:default;font-style:italic;color:#888;}
    .excel-table .form-control[readonly]{background:#f4f4f4;cursor:not-allowed;}
    .excel-table select.excel-input{
        appearance:none;-webkit-appearance:none;-moz-appearance:none;
        background:transparent;padding-right:20px;
        background-image:url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'><path fill='%23333' d='M6 9l4-4H2z'/></svg>");
        background-repeat:no-repeat;background-position:right 4px center;
    }
    .excel-table input[type=file]{padding:2px;font-size:11px;}
    .btn-sm{font-size:11px;padding:4px 8px;}
    .invalid-feedback{font-size:10px;text-align:left;}
    @media(max-width:768px){
        .excel-table th,.excel-table td{font-size:10px;padding:3px;}
        .excel-input{font-size:10px;padding:2px;}
        .btn-sm{font-size:10px;padding:3px 6px;}
    }
</style>
@endsection