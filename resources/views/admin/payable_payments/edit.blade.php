@extends('admin.layouts.shared')
@section('title', 'Edit Payable Payment')
@section('header-title', 'Edit Payable Payment')

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Edit Payment</h4>
            <a href="{{ route('payable-payments.index') }}" class="btn btn-secondary btn-sm">
                <i class="mdi mdi-arrow-left"></i> Back
            </a>
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

            <form action="{{ route('payable-payments.update', $payment->id) }}" method="POST" id="editPaymentForm">
                @csrf
                @method('PUT')
                
                <div class="row g-3">
                    <div class="col-md-6 position-relative">
                        <label class="form-label">Supplier <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control mb-1 supplier-search" 
                               placeholder="Type supplier name..." 
                               value="{{ $payment->supplier->supplier_name ?? '' }}"
                               data-row-index="edit">
                        <ul class="supplier-suggestion-list list-group position-absolute w-100 shadow-sm"
                            style="z-index: 1000; max-height: 200px; overflow-y: auto; display: none;">
                            @foreach ($suppliers as $supplier)
                                <li class="list-group-item list-group-item-action @if($payment->supplier_id == $supplier->id) active @endif"
                                    data-id="{{ $supplier->id }}"
                                    data-row-index="edit"
                                    style="cursor: pointer;">
                                    {{ $supplier->supplier_name ?? 'N/A' }}
                                </li>
                            @endforeach
                            <li class="no-result-item list-group-item text-center text-muted" style="display: none;">
                                No supplier found
                            </li>
                        </ul>
                        <input type="hidden" 
                               name="supplier_id" 
                               class="supplier-id" 
                               value="{{ $payment->supplier_id }}"
                               required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Transaction Date <span class="text-danger">*</span></label>
                        <input type="date" 
                               name="transaction_date" 
                               class="form-control" 
                               value="{{ $payment->transaction_date }}"
                               required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Amount Paid <span class="text-danger">*</span></label>
                        <input type="number" 
                               step="0.01" 
                               name="amount_paid" 
                               class="form-control" 
                               value="{{ $payment->amount }}"
                               required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Payment Mode <span class="text-danger">*</span></label>
                        <select name="payment_mode" class="form-control" required>
                            <option value="" {{ !$payment->payment_mode ? 'selected' : '' }} disabled>Select</option>
                            <option value="cash" {{ $payment->payment_mode == 'cash' ? 'selected' : '' }}>Cash</option>
                            <option value="bank" {{ $payment->payment_mode == 'bank' ? 'selected' : '' }}>Bank</option>
                            <option value="cheque" {{ $payment->payment_mode == 'cheque' ? 'selected' : '' }}>Cheque</option>
                            <option value="online" {{ $payment->payment_mode == 'online' ? 'selected' : '' }}>Online</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Transaction Type <span class="text-danger">*</span></label>
                        <select name="transaction_type" class="form-control" required>
                            <option value="credit" {{ $payment->transaction_type == 'credit' ? 'selected' : '' }}>Credit</option>
                            <option value="debit" {{ $payment->transaction_type == 'debit' ? 'selected' : '' }}>Debit</option>
                        </select>
                    </div>

                    {{-- <div class="col-md-6">
                        <label class="form-label">Payable ID</label>
                        <input type="text" 
                               class="form-control bg-light" 
                               value="{{ $payment->payable_id ? $payment->payable->id : 'N/A' }}" 
                               readonly>
                    </div> --}}
                </div>

                <div class="col-12 mt-4">
                    <button type="submit" class="btn btn-success">
                        <i class="mdi mdi-content-save"></i> Update Payment
                    </button>
                    <a href="{{ route('payable-payments.index') }}" class="btn btn-secondary">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    initializeSupplierSearch('edit');
    
    function initializeSupplierSearch(rowIndex) {
        const supplierSearch = document.querySelector(`.supplier-search[data-row-index="${rowIndex}"]`);
        const supplierSuggestionList = supplierSearch.nextElementSibling;
        const supplierIdInput = supplierSearch.parentElement.querySelector('.supplier-id');
        const supplierItems = Array.from(supplierSuggestionList.querySelectorAll('li[data-id]'));
        const noResultItem = supplierSuggestionList.querySelector('.no-result-item');
        let selectedIndex = -1;

        if (supplierSearch.value) {
            supplierSuggestionList.style.display = 'none';
        }

        supplierSearch.addEventListener('input', function () {
            const query = this.value.toLowerCase().trim();
            selectedIndex = -1;
            let matchCount = 0;

            if (query) {
                supplierSuggestionList.style.display = 'block';
                supplierItems.forEach(li => {
                    const name = li.textContent.toLowerCase();
                    const match = name.includes(query);
                    li.style.display = match ? 'block' : 'none';
                    if (match) matchCount++;
                });
                noResultItem.style.display = matchCount === 0 ? 'block' : 'none';
            } else {
                supplierSuggestionList.style.display = 'none';
            }
        });

        supplierItems.forEach(li => {
            li.addEventListener('click', function () {
                supplierSearch.value = this.textContent.trim();
                supplierIdInput.value = this.getAttribute('data-id');
                supplierSuggestionList.style.display = 'none';
            });
        });

        supplierSearch.addEventListener('keydown', function (e) {
            const visibleItems = supplierItems.filter(li => li.style.display !== 'none');
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
                    supplierSearch.value = visibleItems[selectedIndex].textContent.trim();
                    supplierIdInput.value = visibleItems[selectedIndex].getAttribute('data-id');
                    supplierSuggestionList.style.display = 'none';
                }
            }
        });

        function highlightItem(list, index) {
            list.forEach((li, i) => li.classList.toggle('active', i === index));
        }

        document.addEventListener('click', function (e) {
            if (!e.target.closest(`.supplier-search[data-row-index="${rowIndex}"]`) &&
                !e.target.closest('.supplier-suggestion-list')) {
                supplierSuggestionList.style.display = 'none';
            }
        });
    }
});
</script>

<style>
.supplier-suggestion-list li.active {
    background-color: #007bff;
    color: white;
}
</style>
@endsection