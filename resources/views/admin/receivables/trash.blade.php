@extends('admin.layouts.shared')
@section('title', 'Receivables Trash')
@section('header-title', 'Receivables Trash')

@section('content')
<div class="container-fluid">
  <div class="row">
    <div class="col-12">
      <div class="card shadow-sm border-0">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
          <h4 class="card-title mb-0">Trash Records</h4>
          <a href="{{ route('receivables.index') }}" class="btn btn-sm btn-secondary">Back</a>
        </div>

        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover table-bordered table-striped align-middle">
              <thead class="table-light text-center">
                <tr>
                  <th>#</th>
                  <th>Date</th>
                  <th>Dealer</th>
                  <th>Bags</th>
                  <th>Rate</th>
                  <th>Total</th>
                  <th>Bilti</th>
                  {{-- <th>Payment Mode</th> --}}
                  <th>Deleted At</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                @forelse($receivables as $r)
                  <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>{{ \Carbon\Carbon::parse($r->transaction_date)->format('d-m-Y') }}</td>
                    <td>{{ $r->dealer->dealer_name ?? '-' }}</td>
                    <td class="text-center">{{ $r->no_of_bags }}</td>
                    <td class="text-end">{{ number_format($r->amount_per_bag,2) }}</td>
                    <td class="text-end">{{ number_format($r->total_amount,2) }}</td>
                    <td>{{ $r->bilti_no ?? '-' }}</td>
                    {{-- <td>{{ ucfirst($r->payment_mode) }}</td> --}}
                    <td>{{ $r->deleted_at->format('d-m-Y H:i') }}</td>
                    <td class="text-center">
                      <div class="d-flex justify-content-center gap-1">
                        <form action="{{ route('receivables.restore', $r->id) }}" method="POST">
                          @csrf
                          <button class="btn btn-sm btn-success" title="Restore"><i class="mdi mdi-restore"></i></button>
                        </form>
                        <form action="{{ route('receivables.forceDelete', $r->id) }}" method="POST" onsubmit="return confirm('Permanently delete this record?');">
                          @csrf
                          @method('DELETE')
                          <button class="btn btn-sm btn-danger" title="Delete Forever"><i class="mdi mdi-delete-forever"></i></button>
                        </form>
                      </div>
                    </td>
                  </tr>
                @empty
                  <tr><td colspan="10" class="text-center text-muted">Trash is empty.</td></tr>
                @endforelse
              </tbody>
            </table>

            <div class="mt-3">{{ $receivables->links() }}</div>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>
@endsection
