@extends('admin.layouts.shared')
@section('title', 'Receivable Payments')
@section('header-title', 'Receivable Payments')

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm border-0">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Receivable Payments</h4>
            <a href="{{ route('receivable-payments.create') }}" class="btn btn-primary">
                <i class="mdi mdi-plus"></i> Add New
            </a>
        </div>

        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Dealer Name</th>
                            <th>Transaction Date</th>
                            <th>Amount</th>
                            <th>Transaction type</th>

                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($payments as $key => $payment)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $payment->dealer->dealer_name ?? 'N/A' }}</td>
                                <td>{{ $payment->transaction_date }}</td>
                                <td>{{ number_format($payment->amount_received, 2) }}</td>
                                <td>{{ ucfirst($payment->payment_mode) }}</td>
                                <td>
                                    <form action="{{ route('receivable-payments.delete', $payment->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger">
                                            <i class="mdi mdi-delete"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">No payments found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
