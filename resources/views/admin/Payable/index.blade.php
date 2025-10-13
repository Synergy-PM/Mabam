@extends('admin.layouts.shared')
@section('title', 'Payables')
@section('header-title', 'Payables')

@section('content')
<div class="container-fluid">
  <div class="row">
    <div class="col-12">
      <div class="card shadow-sm border-0">
        <!-- Header -->
        <div class="card-header d-flex justify-content-between align-items-center bg-light">
          <h4 class="card-title mb-0">Payables</h4>
          <div class="d-flex gap-2">
            <a href="{{ route('payables.create') }}" class="btn btn-sm btn-primary">
              <i class="mdi mdi-plus"></i> Add
            </a>
            <a href="{{ route('payables.trash') }}" class="btn btn-sm btn-danger d-flex align-items-center gap-2">
              <i class="bi bi-trash-fill"></i> Trash
              <span class="badge bg-light text-dark ms-1">{{ $trashCount ?? 0 }}</span>
            </a>
          </div>
        </div>

        <!-- Body -->
        <div class="card-body">
          <div class="table-responsive">
            <table id="payablesTable" class="table table-hover table-striped table-bordered align-middle">
              <thead class="table-light text-center">
                <tr>
                  <th>#</th>
                  <th>Supplier</th>
                  <th>Bags</th>
                  <th>Rate</th>
                  <th>Total</th>
                  <th>Bilti</th>
                  <th>Tons</th>
                  <th>Date</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                @forelse($payables as $p)
                  <tr>
                    <!-- Proper numbering with pagination -->
                    <td class="text-center">
                      {{ $loop->iteration + ($payables->currentPage()-1)*$payables->perPage() }}
                    </td>
                    <td>{{ $p->supplier->supplier_name ?? '-' }}</td>
                    <td class="text-center">{{ $p->no_of_bags }}</td>
                    <td class="text-end">{{ number_format($p->amount_per_bag,2) }}</td>
                    <td class="text-end">{{ number_format($p->total_amount,2) }}</td>
                    <td>{{ $p->bilti_no ?? '-' }}</td>
                    <td class="text-end">{{ $p->tons }}</td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($p->transaction_date)->format('d-m-Y') }}</td>
                    <td class="text-center">
                      <div class="d-flex justify-content-center gap-1">
                        <!-- Edit -->
                        <a href="{{ route('payables.edit', $p->id) }}"
                           class="btn btn-sm btn-soft-warning"
                           title="Edit">
                          <i class="mdi mdi-pencil"></i>
                        </a>
                        <!-- Delete -->
                        <form action="{{ route('payables.delete', $p->id) }}" method="POST"
                              onsubmit="return confirm('Move to trash?');">
                          @csrf
                          @method('DELETE')
                          <button class="btn btn-sm btn-soft-danger" title="Delete">
                            <i class="mdi mdi-trash-can"></i>
                          </button>
                        </form>
                      </div>
                    </td>
                  </tr>
                @empty
                  {{-- <tr>
                    <td colspan="9" class="text-center text-muted">No records found.</td>
                  </tr> --}}
                @endforelse
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          {{-- <div class="mt-3">
            {{ $payables->links() }}
          </div> --}}
        </div>

      </div>
    </div>
  </div>
</div>
@endsection
