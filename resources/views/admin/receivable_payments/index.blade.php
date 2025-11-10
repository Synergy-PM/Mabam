@extends('admin.layouts.shared')
@section('title', 'Receivable Payments')
@section('header-title', 'Receivable Payments')

@section('content')
<div class="container-fluid">
  <div class="row">
    <div class="col-12">
      <div class="card shadow-sm border-0">
        <!-- Header -->
        <div class="card-header d-flex justify-content-between align-items-center bg-light">
          <h4 class="card-title mb-0">Receivable Payments</h4>
          <div class="d-flex gap-2">
            @can('receivable_payment_create')
              <a href="{{ route('receivable-payments.create') }}" class="btn btn-sm btn-primary">
                <i class="mdi mdi-plus"></i> Add
              </a>
            @endcan

            @can('receivable_payment_trash')
              <a href="{{ route('receivable-payments.trash') }}" class="btn btn-sm btn-danger d-flex align-items-center gap-2">
                <i class="bi bi-trash-fill"></i> Trash
                <span class="badge bg-light text-dark ms-1">{{ $trashCount ?? 0 }}</span>
              </a>
            @endcan
          </div>
        </div>

        <!-- Body -->
        <div class="card-body">
          @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
              <i class="mdi mdi-check-circle-outline me-2"></i> {{ session('success') }}
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
          @endif

          @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
              <i class="mdi mdi-alert-circle-outline me-2"></i> {{ session('error') }}
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
          @endif

          <div class="table-responsive">
            <table id="receivablePaymentsTable" class="table table-hover table-striped table-bordered align-middle">
              <thead class="table-light text-center">
                <tr>
                  <th>#</th>
                  <th>Dealer</th>
                  <th>Transaction Date</th>
                  <th>Amount</th>
                  <th>Transaction Type</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                @forelse($payments as $payment)
                  <tr>
                    <td class="text-center">
                      {{ $loop->iteration + ($payments->currentPage() - 1) * $payments->perPage() }}
                    </td>
                    <td>{{ $payment->dealer->dealer_name ?? '-' }}</td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($payment->transaction_date)->format('d M, Y') }}</td>
                    <td class="text-end">{{ number_format($payment->amount_received, 2) }}</td>
                    <td class="text-center">{{ ucfirst($payment->payment_mode) ?? '-' }}</td>
                    <td class="text-center">
                      <div class="d-flex justify-content-center gap-1">
                        @can('receivable_payment_edit')
                          <a href="{{ route('receivable-payments.edit', $payment->id) }}" 
                             class="btn btn-sm btn-soft-warning" 
                             title="Edit">
                            <i class="mdi mdi-pencil"></i>
                          </a>
                        @endcan

                        @can('receivable_payment_trash')
                          <form action="{{ route('receivable-payments.delete', $payment->id) }}" method="POST" 
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
                    <td colspan="6" class="text-center text-muted">No records found.</td>
                  </tr> --}}
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
