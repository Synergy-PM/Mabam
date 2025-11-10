@extends('admin.layouts.shared')
@section('title', 'Add Expense')
@section('header-title', 'Add Expense')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">

                <form action="{{ route('expenses.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label><b>Expense Date</b><span class="text-danger">*</span></label>
                            <input type="date" name="expense_date" class="form-control" required>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label><b>Expense Description</b><span class="text-danger">*</span></label>
                            <input type="text" name="expense_description" class="form-control" placeholder="Enter expense details" required>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label><b>Amount</b><span class="text-danger">*</span></label>
                            <input type="number" name="amount" step="0.01" class="form-control" placeholder="Enter amount" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary"><b>Save Expense</b></button>
                    <a href="{{ route('expenses.index') }}" class="btn btn-secondary">Cancel</a>
                </form>

            </div>
        </div>
    </div>
</div>
@endsection
