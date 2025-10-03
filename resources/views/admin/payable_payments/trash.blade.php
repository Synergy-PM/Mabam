@extends('admin.layouts.shared')

@section('main_section')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4>Trashed Payable Payments</h4>
                <a href="{{ route('payable-payments.index') }}" class="btn btn-secondary btn-sm">Back</a>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Supplier</th>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Amount</th>
                            <th>Payment Mode</th>
                            <th>Restore</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $key => $payment)
                        <tr>
                            <td>{{ $key+1 }}</td>
                            <td>{{ $payment->supplier->name ?? 'N/A' }}</td>
                            <td>{{ $payment->transaction_date }}</td>
                            <td>{{ ucfirst($payment->transaction_type) }}</td>
                            <td>{{ number_format($payment->amount, 2) }}</td>
                            <td>{{ $payment->payment_mode ?? '-' }}</td>
                            <td>
                                <form action="{{ route('payable-payments.restore',$payment->id) }}" method="POST">
                                    @csrf @method('PUT')
                                    <button type="submit" class="btn btn-success btn-sm">Restore</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">No Trashed Payments Found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
