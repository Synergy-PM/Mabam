@extends('admin.layouts.shared')
@section('title', 'Add Receivable Payment')
@section('header-title', 'Add Receivable Payment')

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-light">
            <h4 class="mb-0">Add Receivable Payment</h4>
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('receivable-payments.store') }}" method="POST" enctype="multipart/form-data" id="paymentForm">
                @csrf
                <div class="row g-3" id="paymentRows">
                    <div class="payment-row border rounded p-3 mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Payment Entry 1</h5>
                            <button type="button" class="btn btn-sm btn-danger removeRowBtn" style="display:none;">
                                <i class="mdi mdi-delete"></i> Remove
                            </button>
                        </div>
                        <hr>
                        <div class="row g-3">
                            <div class="col-md-6 position-relative">
                                <label class="form-label">Dealer</label>
                                <input type="text" class="form-control mb-1 dealer-search" placeholder="Type dealer name..." data-row-index="0">
                                <ul class="dealer-suggestion-list list-group position-absolute w-100 shadow-sm"
                                    style="z-index: 1000; max-height: 200px; overflow-y: auto; display: none;">
                                    @foreach($dealers as $dealer)
                                        <li class="list-group-item list-group-item-action"
                                            data-id="{{ $dealer->id }}"
                                            data-row-index="0"
                                            style="cursor: pointer;">
                                            {{ $dealer->dealer_name ?? 'N/A' }}
                                        </li>
                                    @endforeach
                                    <li class="no-result-item list-group-item text-center text-muted" style="display: none;">
                                        No dealer found
                                    </li>
                                </ul>
                                <input type="hidden" name="payments[0][dealer_id]" class="dealer-id" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Transaction Date</label>
                                <input type="date" name="payments[0][transaction_date]" class="form-control" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Amount Received</label>
                                <input type="number" step="0.01" name="payments[0][amount_received]" class="form-control" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Payment Mode</label>
                                <select name="payments[0][payment_mode]" class="form-control" required>
                                    <option value="" selected disabled>Select</option>
                                    <option value="cash">Cash</option>
                                    <option value="bank">Bank</option>
                                    <option value="cheque">Cheque</option>
                                    <option value="online">Online</option>
                                </select>
                            </div>

                            <input type="hidden" name="payments[0][transaction_type]" value="debit">
                        </div>
                    </div>
                </div>

                <div class="col-12 mb-3">
                    <button type="button" id="addMoreBtn" class="btn btn-primary">
                        <i class="mdi mdi-plus"></i> Add More Payment
                    </button>
                </div>

                <div class="col-12">
                    <button type="submit" class="btn btn-success">Save All Payments</button>
                    <a href="{{ route('receivable-payments.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    let rowIndex = 1;

    initializeDealerSearch(0);

    document.getElementById('addMoreBtn').addEventListener('click', function () {
        const container = document.getElementById('paymentRows');
        const firstRow = document.querySelector('.payment-row');
        const newRow = firstRow.cloneNode(true);

        const firstTransactionDate = document.querySelector('input[name="payments[0][transaction_date]"]').value;

        newRow.querySelectorAll('input, select, textarea').forEach(function (element) {
            const name = element.getAttribute('name');
            if (name) {
                element.setAttribute('name', name.replace(/\[\d+\]/, '[' + rowIndex + ']'));
                if (element.type !== 'hidden') {
                    if (element.name.includes('transaction_date')) {
                        element.value = firstTransactionDate;
                    } else {
                        element.value = '';
                    }
                }
            }
            if (element.tagName === 'SELECT') {
                element.selectedIndex = 0;
            }
        });

        newRow.querySelector('.dealer-search').setAttribute('data-row-index', rowIndex);
        newRow.querySelector('.dealer-id').value = '';
        newRow.querySelectorAll('.dealer-suggestion-list li[data-id]').forEach(function (li) {
            li.setAttribute('data-row-index', rowIndex);
        });
        newRow.querySelector('.no-result-item').style.display = 'none';
        newRow.querySelector('.dealer-suggestion-list').style.display = 'none';

        newRow.querySelector('h5').textContent = 'Payment Entry ' + (rowIndex + 1);
        newRow.querySelector('.removeRowBtn').style.display = 'inline-block';
        container.appendChild(newRow);

        initializeDealerSearch(rowIndex);
        rowIndex++;
    });

    document.addEventListener('click', function (e) {
        if (e.target.closest('.removeRowBtn')) {
            e.target.closest('.payment-row').remove();
        }
    });

    function initializeDealerSearch(rowIndex) {
        const dealerSearch = document.querySelector(`.dealer-search[data-row-index="${rowIndex}"]`);
        const dealerSuggestionList = dealerSearch.nextElementSibling;
        const dealerIdInput = dealerSearch.parentElement.querySelector('.dealer-id');
        const dealerItems = Array.from(dealerSuggestionList.querySelectorAll('li[data-id]'));
        const noResultItem = dealerSuggestionList.querySelector('.no-result-item');
        let selectedIndex = -1;

        dealerSearch.addEventListener('input', function () {
            const query = this.value.toLowerCase().trim();
            selectedIndex = -1;
            let matchCount = 0;

            if (query) {
                dealerSuggestionList.style.display = 'block';
                dealerItems.forEach(li => {
                    const name = li.textContent.toLowerCase();
                    const match = name.includes(query);
                    li.style.display = match ? 'block' : 'none';
                    if (match) matchCount++;
                });
                noResultItem.style.display = matchCount === 0 ? 'block' : 'none';
            } else {
                dealerSuggestionList.style.display = 'none';
            }
        });

        dealerItems.forEach(li => {
            li.addEventListener('click', function () {
                dealerSearch.value = this.textContent.trim();
                dealerIdInput.value = this.getAttribute('data-id');
                dealerSuggestionList.style.display = 'none';
            });
        });

        dealerSearch.addEventListener('keydown', function (e) {
            const visibleItems = dealerItems.filter(li => li.style.display !== 'none');
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                selectedIndex = (selectedIndex + 1) % visibleItems.length;
                highlightItem(visibleItems, selectedIndex);
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                selectedIndex = (selectedIndex - 1 + visibleItems.length) % visibleItems.length;
                highlightItem(visibleItems, selectedIndex);
            } else if (e.key === 'Enter') {
                e.preventDefault();
                if (selectedIndex >= 0 && visibleItems[selectedIndex]) {
                    dealerSearch.value = visibleItems[selectedIndex].textContent.trim();
                    dealerIdInput.value = visibleItems[selectedIndex].getAttribute('data-id');
                    dealerSuggestionList.style.display = 'none';
                }
            }
        });

        function highlightItem(list, index) {
            list.forEach((li, i) => li.classList.toggle('active', i === index));
        }

        document.addEventListener('click', function (e) {
            if (!e.target.closest(`.dealer-search[data-row-index="${rowIndex}"]`) &&
                !e.target.closest(`.dealer-suggestion-list`)) {
                dealerSuggestionList.style.display = 'none';
            }
        });
    }
});
</script>

<style>
.dealer-suggestion-list li.active {
    background-color: #007bff;
    color: white;
}
</style>
@endsection