@extends('admin.layouts.shared')

@section('main_section')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header"><h4>Edit Payable Payment</h4></div>
            <div class="card-body">
                <form action="{{ route('payable-payments.update',$payment->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf @method('PUT')
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Supplier</label>
                            <select name="supplier_id" class="form-control" required>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" {{ $payment->supplier_id == $supplier->id ? 'selected' : '' }}>
                                        {{ $supplier->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Transaction Date</label>
                            <input type="date" name="transaction_date" class="form-control" value="{{ $payment->transaction_date }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Transaction Type</label>
                            <select name="transaction_type" class="form-control" required>
                                <option value="debit" {{ $payment->transaction_type=='debit'?'selected':'' }}>Debit</option>
                                <option value="credit" {{ $payment->transaction_type=='credit'?'selected':'' }}>Credit</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Amount</label>
                            <input type="number" step="0.01" name="amount" class="form-control" value="{{ $payment->amount }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Payment Mode</label>
                            <select name="payment_mode" class="form-control">
                                <option value="Cash" {{ $payment->payment_mode=='Cash'?'selected':'' }}>Cash</option>
                                <option value="Bank Transfer" {{ $payment->payment_mode=='Bank Transfer'?'selected':'' }}>Bank Transfer</option>
                                <option value="Cheque" {{ $payment->payment_mode=='Cheque'?'selected':'' }}>Cheque</option>
                                <option value="Online" {{ $payment->payment_mode=='Online'?'selected':'' }}>Online</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Proof of Payment</label><br>
                            @if($payment->proof_of_payment)
                                <a href="{{ asset('storage/'.$payment->proof_of_payment) }}" target="_blank">View Current</a>
                            @endif
                            <input type="file" name="proof_of_payment" class="form-control mt-2">
                        </div>
                        <div class="col-12 mb-3">
                            <label>Notes</label>
                            <textarea name="notes" class="form-control">{{ $payment->notes }}</textarea>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-success">Update</button>
                            <a href="{{ route('payable-payments.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
