@extends('admin.layouts.shared')
@section('title', 'Edit Cash Book')
@section('header-title', 'Edit Cash Book Entry')

@section('content')
    <div class="container-fluid">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Edit Cash Book Entry</h5>
                <a href="{{ route('cheque.print') }}" target="_blank" class="btn btn-light btn-sm">Print</a>
            </div>
            <div class="card-body p-0">
                <div class="cheque-book">
                    <div class="cheque-page">
                        <form action="{{ route('cheque.update', $entry->id) }}" method="POST" id="cashForm">
                            @csrf
                            @method('PUT')
                            <table class="cheque-table">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Party</th>
                                        <th>Credit</th>
                                        <th>Debit</th>
                                        <th>Pay</th>
                                        <th>Bal</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody id="cashEntries">
                                    @php
                                        $balance = 0;
                                    @endphp
                                    @foreach ($entries as $index => $entry)
                                        @php
                                            $balance += ($entry->credit ?? 0) - ($entry->debit ?? 0);
                                            $partyName = $entry->party_type === 'expense' ? ($entry->expense_description ?? '') : ($entry->party ? $entry->party->name : '');
                                            $partyId = $entry->party_type === 'expense' ? '' : ($entry->party_type === 'dealer' ? 'D' . $entry->party_id : $entry->party_id);
                                        @endphp
                                        <tr class="main-entry">
                                            <td>
                                                <input type="date" name="entries[{{$index}}][date]" value="{{ $entry->date }}" class="date">
                                            </td>
                                            <td class="position-relative">
                                                @if ($entry->party_type === 'expense')
                                                    <div class="party-cell">
                                                        <div class="sd-group">
                                                            <button type="button" class="btn btn-outline-primary sd-btn" data-type="supplier">S</button>
                                                            <button type="button" class="btn btn-outline-info sd-btn" data-type="dealer">D</button>
                                                            <button type="button" class="btn btn-warning btn-sm exp-btn ms-1">E</button>
                                                        </div>
                                                        <div class="d-flex align-items-center mt-1">
                                                            <input type="text" name="entries[{{$index}}][expense_description]" placeholder="Expense Description" class="form-control form-control-sm expense-desc-input flex-fill" value="{{ $entry->expense_description ?? '' }}">
                                                        </div>
                                                        <input type="hidden" name="entries[{{$index}}][party_type]" value="expense">
                                                        <input type="hidden" name="entries[{{$index}}][party_id]" value="">
                                                    </div>
                                                @else
                                                    <div class="party-cell">
                                                        <div class="sd-group">
                                                            <button type="button" class="btn btn-outline-primary sd-btn {{ $entry->party_type === 'supplier' ? 'active' : '' }}" data-type="supplier">S</button>
                                                            <button type="button" class="btn btn-outline-info sd-btn {{ $entry->party_type === 'dealer' ? 'active' : '' }}" data-type="dealer">D</button>
                                                            <button type="button" class="btn btn-outline-warning btn-sm exp-btn ms-1">E</button>
                                                        </div>
                                                        <div class="d-flex align-items-center mt-1 party-search-wrapper">
                                                            <input type="text" class="form-control form-control-sm party-input flex-fill" placeholder="Search {{ ucfirst($entry->party_type) }}..." autocomplete="off" value="{{ $partyName }}">
                                                        </div>
                                                        <input type="hidden" name="entries[{{$index}}][party_id]" class="party-id" value="{{ $partyId }}">
                                                        <input type="hidden" name="entries[{{$index}}][party_type]" class="party-type" value="{{ $entry->party_type }}">
                                                    </div>
                                                    <ul class="list-group suggestion-list position-absolute w-100 mt-1 z-3" style="display:none;"></ul>
                                                @endif
                                            </td>
                                            <td>
                                                <input type="number" step="0.01" name="entries[{{$index}}][credit]" class="amount-input credit-input" placeholder="0.00" value="{{ $entry->credit ?? '' }}">
                                            </td>
                                            <td>
                                                <input type="number" step="0.01" name="entries[{{$index}}][debit]" class="amount-input debit-input" placeholder="0.00" value="{{ $entry->debit ?? '' }}">
                                            </td>
                                            <td>
                                                <select name="entries[{{$index}}][payment_type]" class="form-control payment-type">
                                                    <option value="">Select</option>
                                                    @foreach ($paymentTypes as $key => $label)
                                                        <option value="{{ $key }}" {{ $entry->payment_type === $key ? 'selected' : '' }}>{{ $label }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <input type="text" class="balance" readonly value="{{ number_format($balance, 2) }}">
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-danger btn-sm remove-btn">X</button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="summary-row">
                                        <td colspan="5"><strong>Total</strong></td>
                                        <td id="runningBalance">{{ number_format($balance, 2) }}</td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>

                            <div class="p-3 text-end">
                                <button type="button" class="btn btn-success btn-sm" id="addEntry">
                                    + Add Entry
                                </button>
                                <button type="submit" class="btn btn-primary btn-sm">Update</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .cheque-book {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }

        .cheque-page {
            background: white;
            border: 3px double #333;
            padding: 15px;
            border-radius: 10px;
            position: relative;
        }

        .cheque-page::before {
            content: "CASH BOOK";
            position: absolute;
            top: -12px;
            left: 20px;
            background: #fff;
            padding: 0 10px;
            font-weight: bold;
            color: #333;
            font-size: 14px;
        }

        .cheque-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            table-layout: fixed;
        }

        .cheque-table th,
        .cheque-table td {
            border: 1px dashed #ddd;
            padding: 4px 6px;
            vertical-align: middle;
            height: 50px;
            position: relative;
        }

        .cheque-table th {
            background: #e9ecef;
            text-align: center;
            font-weight: bold;
            font-size: 11px;
            text-transform: uppercase;
            height: 40px;
        }

        .cheque-table input,
        .cheque-table select {
            border: none;
            width: 100%;
            padding: 4px;
            font-size: 12px;
            background: transparent;
            height: 32px;
        }

        .cheque-table input:focus,
        .cheque-table select:focus {
            background: #fff8e6;
            outline: 1px solid #007bff;
        }

        .summary-row td {
            font-weight: bold;
            background: #f1f3f5 !important;
            font-size: 13px;
            height: 40px;
        }

        .remove-btn {
            background: #dc3545;
            color: white;
            border: none;
            padding: 4px 8px;
            font-size: 10px;
            cursor: pointer;
            border-radius: 3px;
            width: 28px;
            height: 28px;
            line-height: 1;
        }

        .exp-btn {
            font-size: 10px;
            padding: 2px 6px;
            min-width: 28px;
            min-height: 28px;
        }

        .cheque-table th:nth-child(1),
        .cheque-table td:nth-child(1) {
            width: 10%;
        }

        .cheque-table th:nth-child(2),
        .cheque-table td:nth-child(2) {
            width: 28%;
        }

        .cheque-table th:nth-child(3),
        .cheque-table td:nth-child(3) {
            width: 12%;
        }

        .cheque-table th:nth-child(4),
        .cheque-table td:nth-child(4) {
            width: 12%;
        }

        .cheque-table th:nth-child(5),
        .cheque-table td:nth-child(5) {
            width: 12%;
        }

        .cheque-table th:nth-child(6),
        .cheque-table td:nth-child(6) {
            width: 6%;
        }

        .cheque-table th:nth-child(7),
        .cheque-table td:nth-child(7) {
            width: 5%;
            text-align: center;
        }

        .party-cell {
            display: flex;
            flex-direction: column;
            height: 100%;
            gap: 2px;
        }

        .sd-group {
            display: flex;
            gap: 2px;
        }

        .sd-btn {
            flex: 1;
            font-size: 10px !important;
            padding: 2px 4px !important;
            min-width: 0 !important;
            height: 24px;
        }

        .sd-btn.active {
            font-weight: bold !important;
            background-color: #007bff !important;
            color: white !important;
            border-color: #007bff !important;
        }

        .sd-btn[data-type="dealer"].active {
            background-color: #17a2b8 !important;
            border-color: #17a2b8 !important;
        }

        .party-input {
            flex: 1;
            font-size: 12px !important;
            height: 28px !important;
        }

        .expense-desc-input {
            background: #fff9e6 !important;
            font-style: italic;
            border: none;
            padding: 4px;
            font-size: 12px;
            height: 32px;
            flex: 1;
        }

        .suggestion-list {
            position: absolute;
            left: 0;
            right: 0;
            top: 100%;
            max-height: 150px;
            overflow-y: auto;
            background: white;
            border: 1px solid #ced4da;
            border-top: none;
            border-radius: 0 0 0.25rem 0.25rem;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            display: none;
            margin: 0;
            padding: 0;
        }

        .suggestion-list li {
            padding: 6px 10px;
            font-size: 12px;
            cursor: pointer;
            list-style: none;
            border-bottom: 1px solid #eee;
        }

        .suggestion-list li:last-child {
            border-bottom: none;
        }

        .suggestion-list li:hover {
            background: #007bff;
            color: white;
        }

        .balance {
            background: transparent;
            border: none;
            text-align: right;
            font-weight: 500;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let entryIndex = {{ count($entries) }};
            const entriesBody = document.getElementById('cashEntries');
            const parties = @json($parties ?? []);
            const paymentTypes = @json($paymentTypes ?? []);

            function addEntryRow() {
                const row = document.createElement('tr');
                row.className = 'main-entry';
                row.innerHTML = `
                    <td><input type="date" name="entries[${entryIndex}][date]" value="{{ date('Y-m-d') }}" class="date"></td>
                    <td class="position-relative">
                        <div class="party-cell">
                            <div class="sd-group">
                                <button type="button" class="btn btn-outline-primary sd-btn active" data-type="supplier">S</button>
                                <button type="button" class="btn btn-outline-info sd-btn" data-type="dealer">D</button>
                                <button type="button" class="btn btn-outline-warning btn-sm exp-btn ms-1">E</button>
                            </div>
                            <div class="d-flex align-items-center mt-1 party-search-wrapper">
                                <input type="text" class="form-control form-control-sm party-input flex-fill" placeholder="Search Supplier..." autocomplete="off">
                            </div>
                            <input type="hidden" name="entries[${entryIndex}][party_id]" class="party-id">
                            <input type="hidden" name="entries[${entryIndex}][party_type]" class="party-type" value="supplier">
                        </div>
                        <ul class="list-group suggestion-list position-absolute w-100 mt-1 z-3" style="display:none;"></ul>
                    </td>
                    <td><input type="number" step="0.01" name="entries[${entryIndex}][credit]" class="amount-input credit-input" placeholder="0.00"></td>
                    <td><input type="number" step="0.01" name="entries[${entryIndex}][debit]" class="amount-input debit-input" placeholder="0.00"></td>
                    <td>
                        <select name="entries[${entryIndex}][payment_type]" class="form-control payment-type">
                            <option value="">Select</option>
                            ${Object.entries(paymentTypes).map(([k, l]) => `<option value="${k}">${l}</option>`).join('')}
                        </select>
                    </td>
                    <td><input type="text" class="balance" readonly value="0.00"></td>
                    <td class="text-center"><button type="button" class="btn btn-danger btn-sm remove-btn">X</button></td>
                `;

                entriesBody.appendChild(row);

                setupPartySearch(row);
                setupAmountEvents(row);
                setupExpenseToggle(row, entryIndex);
                setupRemoveButton(row);

                entryIndex++;
                updateTotals();
            }

            function setupPartySearch(row) {
                const sdButtons = row.querySelectorAll('.sd-btn');
                const partyInput = row.querySelector('.party-input');
                const partyIdInput = row.querySelector('.party-id');
                const partyTypeInput = row.querySelector('.party-type');
                const suggestionList = row.querySelector('.suggestion-list');

                let currentType = partyTypeInput.value || 'supplier';

                sdButtons.forEach(btn => {
                    btn.addEventListener('click', function() {
                        const expBtn = row.querySelector('.exp-btn');
                        if (expBtn && expBtn.classList.contains('btn-warning')) return;

                        sdButtons.forEach(b => b.classList.remove('active', 'btn-primary', 'btn-info'));
                        this.classList.add('active', this.dataset.type === 'supplier' ? 'btn-primary' : 'btn-info');

                        currentType = this.dataset.type;
                        partyTypeInput.value = currentType;
                        partyInput.value = '';
                        partyIdInput.value = '';
                        suggestionList.style.display = 'none';
                        partyInput.placeholder = `Search ${currentType.charAt(0).toUpperCase() + currentType.slice(1)}...`;
                        partyInput.focus();
                    });
                });

                partyInput.addEventListener('input', function() {
                    const query = this.value.trim().toLowerCase();
                    suggestionList.innerHTML = '';

                    if (!query) {
                        suggestionList.style.display = 'none';
                        return;
                    }

                    const filtered = Object.entries(parties).filter(([id, data]) => {
                        return data.type === currentType && data.name.toLowerCase().includes(query);
                    });

                    if (filtered.length === 0) {
                        suggestionList.innerHTML = '<li class="list-group-item text-muted py-1 px-3">No results</li>';
                        suggestionList.style.display = 'block';
                        return;
                    }

                    filtered.forEach(([id, data]) => {
                        const li = document.createElement('li');
                        li.classList.add('list-group-item', 'list-group-item-action');
                        li.textContent = data.name;
                        li.dataset.id = id;

                        li.addEventListener('click', () => {
                            const selectedId = li.dataset.id;
                            partyInput.value = data.name;
                            partyIdInput.value = selectedId;
                            partyTypeInput.value = currentType;
                            suggestionList.innerHTML = '';
                            suggestionList.style.display = 'none';

                            console.log("✅ Selected:", data.name, "| ID:", selectedId, "| Type:", currentType);
                        });

                        suggestionList.appendChild(li);
                    });

                    suggestionList.style.display = 'block';
                });

                document.addEventListener('click', function(e) {
                    if (!row.contains(e.target)) suggestionList.style.display = 'none';
                });

                partyInput.addEventListener('focus', function() {
                    if (this.value) this.dispatchEvent(new Event('input'));
                });
            }

            function setupAmountEvents(row) {
                row.querySelectorAll('.amount-input').forEach(input => {
                    input.addEventListener('input', updateTotals);
                });
            }

            function setupExpenseToggle(row, expIndex) {
                const expBtn = row.querySelector('.exp-btn');
                const partyCell = row.querySelector('.party-cell');
                let isExpenseMode = expBtn.classList.contains('btn-warning');

                expBtn.addEventListener('click', function() {
                    if (!isExpenseMode) {
                        isExpenseMode = true;
                        this.classList.remove('btn-outline-warning');
                        this.classList.add('btn-warning');

                        partyCell.innerHTML = `
                            <div class="sd-group">
                                <button type="button" class="btn btn-outline-primary sd-btn" data-type="supplier">S</button>
                                <button type="button" class="btn btn-outline-info sd-btn" data-type="dealer">D</button>
                                <button type="button" class="btn btn-warning btn-sm exp-btn ms-1">E</button>
                            </div>
                            <div class="d-flex align-items-center mt-1">
                                <input type="text" name="entries[${expIndex}][expense_description]" placeholder="Expense Description" class="form-control form-control-sm expense-desc-input flex-fill">
                            </div>
                            <input type="hidden" name="entries[${expIndex}][party_type]" value="expense">
                            <input type="hidden" name="entries[${expIndex}][party_id]" value="">
                        `;
                        setTimeout(() => partyCell.querySelector('input').focus(), 50);
                        const newExpBtn = partyCell.querySelector('.exp-btn');
                        newExpBtn.addEventListener('click', () => expBtn.click());
                    } else {
                        isExpenseMode = false;
                        this.classList.remove('btn-warning');
                        this.classList.add('btn-outline-warning');
                        partyCell.innerHTML = `
                            <div class="sd-group">
                                <button type="button" class="btn btn-outline-primary sd-btn active" data-type="supplier">S</button>
                                <button type="button" class="btn btn-outline-info sd-btn" data-type="dealer">D</button>
                                <button type="button" class="btn btn-outline-warning btn-sm exp-btn ms-1">E</button>
                            </div>
                            <div class="d-flex align-items-center mt-1 party-search-wrapper">
                                <input type="text" class="form-control form-control-sm party-input flex-fill" placeholder="Search Supplier..." autocomplete="off">
                            </div>
                            <input type="hidden" name="entries[${expIndex}][party_id]" class="party-id">
                            <input type="hidden" name="entries[${expIndex}][party_type]" class="party-type" value="supplier">
                        `;
                        setupPartySearch(row);
                        const newExpBtn = partyCell.querySelector('.exp-btn');
                        newExpBtn.addEventListener('click', () => expBtn.click());
                    }
                    updateTotals();
                });
            }

            function setupRemoveButton(row) {
                row.querySelector('.remove-btn').addEventListener('click', () => {
                    row.remove();
                    updateTotals();
                });
            }

            function updateTotals() {
                let balance = 0;
                document.querySelectorAll('#cashEntries tr.main-entry').forEach(row => {
                    const credit = parseFloat(row.querySelector('.credit-input').value) || 0;
                    const debit = parseFloat(row.querySelector('.debit-input').value) || 0;
                    balance += credit - debit;
                    const balanceCell = row.querySelector('.balance');
                    if (balanceCell) balanceCell.value = balance.toFixed(2);
                });
                document.getElementById('runningBalance').textContent = balance.toFixed(2);
            }

            document.getElementById('addEntry').addEventListener('click', addEntryRow);

            document.querySelectorAll('#cashEntries tr.main-entry').forEach(row => {
                setupPartySearch(row);
                setupAmountEvents(row);
                setupExpenseToggle(row, row.querySelector('.party-type').name.match(/\d+/)[0]);
                setupRemoveButton(row);
            });
        });
    </script>
@endsection