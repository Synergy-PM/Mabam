@extends('admin.layouts.shared')
@section('title', 'Receivables')
@section('header-title', 'Receivables')

@section('content')
<div class="container-fluid">
  <div class="row">
    <div class="col-12">
      <div class="card shadow-sm border-0">

        <!-- Header -->
        <div class="card-header d-flex justify-content-between align-items-center bg-light">
          <h4 class="card-title mb-0">Receivables List</h4>
          <a href="{{ route('payments.create') }}" class="btn btn-primary btn-sm">
            + Add Receivable
          </a>
        </div>

        <!-- Table -->
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
              <thead class="table-light">
                <tr>
                  <th>#</th>
                  <th>Bilti No</th>
                  <th>Dealer</th>
                  <th>Bags</th>
                  <th>Rate</th>
                  <th>Freight</th>
                  <th>Tons</th>
                  <th>Total</th>
                  <th>Payment Type</th>
                  <th>Proof</th>
                  <th>Date</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                @forelse($payments as $payment)
                  <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $payment->bilti_no }}</td>
                    <td>{{ $payment->dealer?->dealer_name }}</td>
                    <td>{{ $payment->bags }}</td>
                    <td>{{ number_format($payment->rate, 2) }}</td>
                    <td>{{ number_format($payment->freight, 2) }}</td>
                    <td>{{ number_format($payment->tons, 2) }}</td>
                    <td>{{ number_format($payment->total, 2) }}</td>
                    <td>{{ ucfirst($payment->payment_type) }}</td>
                    <td>
                      @if($payment->proof_of_payment)
                        <a href="{{ asset('storage/' . $payment->proof_of_payment) }}" target="_blank" class="btn btn-sm btn-info">
                          View
                        </a>
                      @else
                        <span class="badge bg-secondary">No Proof</span>
                      @endif
                    </td>
                    <td>{{ $payment->created_at->format('d-m-Y') }}</td>
                    <td>
                      <a href="{{ route('payments.edit', $payment->id) }}" class="btn btn-sm btn-warning">Edit</a>
                      <form action="{{ route('payments.delete', $payment->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                          Delete
                        </button>
                      </form>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="12" class="text-center text-muted">No Receivables Found</td>
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
