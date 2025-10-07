@extends('admin.layouts.shared')
@section('title', 'Add Payable Payment')
@section('header-title', 'Add Payable Payment')

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-light">
            <h4 class="mb-0">Add Payable Payment</h4>
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

            <form action="{{ route('payable-payments.store') }}" method="POST" enctype="multipart/form-data" id="paymentForm">
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
                            <div class="col-md-6">
                                <label class="form-label">Supplier</label>
                                <select name="payments[0][supplier_id]" class="form-control payable-select" required>
                                    <option value="">-- Select Supplier --</option>
                                    @foreach ($supplier as $suppliers)
                                        <option value="{{ $suppliers->id }}">{{ $suppliers->supplier_name ?? 'N/A' }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Transaction Date</label>
                                <input type="date" name="payments[0][transaction_date]" class="form-control" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Amount Paid</label>
                                <input type="number" step="0.01" name="payments[0][amount_paid]" class="form-control" required>
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

                            <input type="hidden" name="payments[0][transaction_type]" value="credit">
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
                    <a href="{{ route('payable-payments.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let rowIndex = 1;

    // Add More Button
    document.getElementById('addMoreBtn').addEventListener('click', function() {
        const container = document.getElementById('paymentRows');
        const firstRow = document.querySelector('.payment-row');
        const newRow = firstRow.cloneNode(true);

        newRow.querySelectorAll('input, select, textarea').forEach(function(element) {
            const name = element.getAttribute('name');
            if (name) {
                element.setAttribute('name', name.replace(/\[\d+\]/, '[' + rowIndex + ']'));
                if (element.type !== 'hidden') {
                    element.value = '';
                }
            }
            if (element.tagName === 'SELECT') {
                element.selectedIndex = 0;
            }
        });

        newRow.querySelector('h5').textContent = 'Payment Entry ' + (rowIndex + 1);
        newRow.querySelector('.removeRowBtn').style.display = 'inline-block'; // show remove button
        container.appendChild(newRow);
        rowIndex++;
    });

    document.addEventListener('click', function(e) {
        if (e.target.closest('.removeRowBtn')) {
            e.target.closest('.payment-row').remove();
        }
    });
});
</script>
@endsection
