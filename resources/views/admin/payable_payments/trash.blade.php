@extends('admin.layouts.shared')
@section('title', 'Trashed Payable Payments')
@section('header-title', 'Trashed Payable Payments')

@section('content')
<div class="container-fluid">
  <div class="row">
    <div class="col-12">
      <div class="card shadow-sm border-0">
        
        <!-- Header -->
        <div class="card-header d-flex justify-content-between align-items-center bg-light">
          <h4 class="card-title mb-0">Trashed Payable Payments</h4>
          <a href="{{ route('payable-payments.index') }}" class="btn btn-sm btn-secondary">
            <i class="bi bi-arrow-left"></i> Back
          </a>
        </div>

        <!-- Body -->
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover table-striped table-bordered align-middle">
              <thead class="table-light text-center">
                <tr>
                  <th>#</th>
                  <th>Supplier</th>
                  <th>Transaction Date</th>
                  <th>Amount</th>
                  <th>Payment Mode</th>
                  <th>Transaction Type</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                @forelse($payments as $payment)
                  <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>{{ $payment->supplier->supplier_name ?? 'N/A' }}</td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($payment->transaction_date)->format('d M, Y') }}</td>
                    <td class="text-end">{{ number_format($payment->amount, 2) }}</td>
                    <td class="text-center">{{ ucfirst($payment->payment_mode) ?? '-' }}</td>
                    <td class="text-center">{{ ucfirst($payment->transaction_type) ?? '-' }}</td>
                    <td class="text-center">
                      <div class="d-flex justify-content-center gap-1">
                        <!-- Restore -->
                        <form action="{{ route('payable-payments.restore', $payment->id) }}" method="POST">
                          @csrf
                          @method('PUT')
                          <button type="submit" class="btn btn-sm btn-soft-success" title="Restore">
                            <i class="mdi mdi-restore"></i>
                          </button>
                        </form>

                        <!-- Permanent Delete -->
                        {{-- <form action="{{ route('payable-payments.forceDelete', $payment->id) }}" method="POST"
                              onsubmit="return confirm('Are you sure you want to permanently delete this payment?');">
                          @csrf
                          @method('DELETE')
                          <button type="submit" class="btn btn-sm btn-soft-danger" title="Delete Permanently">
                            <i class="mdi mdi-delete-forever"></i>
                          </button>
                        </form> --}}
                      </div>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="7" class="text-center text-muted">No trashed payments found.</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          <div class="mt-3">
            {{ $payments->links() }}
          </div>
        </div>

      </div>
    </div>
  </div>
</div>
@endsection
