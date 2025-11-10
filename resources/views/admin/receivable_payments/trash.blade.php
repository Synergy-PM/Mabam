@extends('admin.layouts.shared')
@section('title', 'Receivable Payments Trash')
@section('header-title', 'Receivable Payments Trash')

@section('content')
<div class="container-fluid">
  <div class="row">
    <div class="col-12">
      <div class="card shadow-sm border-0">

        <!-- Header -->
        <div class="card-header d-flex justify-content-between align-items-center bg-light">
          <h4 class="card-title mb-0">Receivable Payments - Trash</h4>
          <a href="{{ route('receivable-payments.index') }}" class="btn btn-sm btn-secondary">
            <i class="mdi mdi-arrow-left"></i> Back
          </a>
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
            <table id="trashTable" class="table table-hover table-striped table-bordered align-middle">
              <thead class="table-light text-center">
                <tr>
                  <th>#</th>
                  <th>Dealer</th>
                  <th>Transaction Date</th>
                  <th>Amount</th>
                  <th>Transaction Type</th>
                  <th>Deleted At</th>
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
                    <td class="text-center">{{ $payment->deleted_at ? $payment->deleted_at->format('d M, Y H:i') : '-' }}</td>
                    <td class="text-center">
                      <div class="d-flex justify-content-center gap-1">
                        <!-- Restore -->
                        <form action="{{ route('receivable-payments.restore', $payment->id) }}" method="POST" 
                              onsubmit="return confirm('Restore this payment?');">
                          @csrf
                          <button class="btn btn-sm btn-soft-success" title="Restore">
                            <i class="mdi mdi-restore"></i>
                          </button>
                        </form>
                        <!-- Permanent Delete -->
                        {{-- <form action="{{ route('receivable-payments.forceDelete', $payment->id) }}" method="POST" 
                              onsubmit="return confirm('Permanently delete this payment? This action cannot be undone.');">
                          @csrf
                          @method('DELETE')
                          <button class="btn btn-sm btn-soft-danger" title="Delete Permanently">
                            <i class="mdi mdi-delete-forever"></i>
                          </button>
                        </form> --}}
                      </div>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="7" class="text-center text-muted">No trashed records found.</td>
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

