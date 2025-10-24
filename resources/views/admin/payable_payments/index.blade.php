@extends('admin.layouts.shared')
@section('title', 'Payable Payments')
@section('header-title', 'Payable Payments')

@section('content')
<div class="container-fluid">
  <div class="row">
    <div class="col-12">
      <div class="card shadow-sm border-0">
        <!-- Header -->
        <div class="card-header d-flex justify-content-between align-items-center bg-light">
          <h4 class="card-title mb-0">Payable Payments</h4>

          <div class="d-flex gap-2">
            @can('payable_payment_create')
              <a href="{{ route('payable-payments.create') }}" class="btn btn-sm btn-primary">
                <i class="mdi mdi-plus"></i> Add
              </a>
            @endcan

            @can('payable_payment_trash')
              <a href="{{ route('payable-payments.trash') }}" class="btn btn-sm btn-danger d-flex align-items-center gap-2">
                <i class="bi bi-trash-fill"></i> Trash
                <span class="badge bg-light text-dark ms-1">{{ $trashCount ?? 0 }}</span>
              </a>
            @endcan
          </div>
        </div>

        <!-- Body -->
        <div class="card-body">
          <div class="table-responsive">
            <table id="payablePaymentsTable" class="table table-hover table-striped table-bordered align-middle">
              <thead class="table-light text-center">
                <tr>
                  <th>#</th>
                  <th>Supplier</th>
                  <th>Transaction Date</th>
                  <th>Amount</th>
                  <th>Proof</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                @forelse($payments as $payment)
                  <tr>
                    <td class="text-center">
                      {{ $loop->iteration + ($payments->currentPage() - 1) * $payments->perPage() }}
                    </td>
                    <td>{{ $payment->supplier->supplier_name ?? '-' }}</td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($payment->transaction_date)->format('d M, Y') }}</td>
                    <td class="text-end">{{ number_format($payment->amount, 2) }}</td>
                    <td class="text-center">{{ ucfirst($payment->payment_mode) ?? '-' }}</td>
                    <td class="text-center">
                      <div class="d-flex justify-content-center gap-1">
                        @can('payable_payment_edit')
                          <a href="{{ route('payable-payments.edit', $payment->id) }}" 
                             class="btn btn-sm btn-soft-warning" 
                             title="Edit">
                            <i class="mdi mdi-pencil"></i>
                          </a>
                        @endcan

                        @can('payable_payment_trash')
                          <form action="{{ route('payable-payments.delete', $payment->id) }}" method="POST" 
                                onsubmit="return confirm('Move to trash?');">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-soft-danger" title="Delete">
                              <i class="mdi mdi-trash-can"></i>
                            </button>
                          </form>
                        @endcan
                      </div>
                    </td>
                  </tr>
                @empty
                  {{-- <tr>
                    <td colspan="8" class="text-center text-muted">No records found.</td>
                  </tr> --}}
                @endforelse
              </tbody>
            </table>
          </div>

          <div class="mt-3">
            {{ $payments->links() }}
          </div>
        </div>

      </div>
    </div>
  </div>
</div>
@endsection
