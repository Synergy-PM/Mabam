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
          <h4 class="card-title mb-0">Receivables</h4>
          <div class="d-flex gap-2">
            <a href="{{ route('receivables.create') }}" class="btn btn-sm btn-primary">
              <i class="mdi mdi-plus"></i> Add
            </a>
            <a href="{{ route('receivables.trash') }}" 
               class="btn btn-sm btn-danger d-flex align-items-center gap-2">
              <i class="bi bi-trash-fill"></i>
              <span>Trash</span>
              <span class="badge bg-light text-dark">{{ $trashCount ?? 0 }}</span>
            </a>
          </div>
        </div>

        <!-- Body -->
        <div class="card-body">
          <div class="table-responsive">
            <table id="receivablesTable" class="table table-hover table-striped table-bordered align-middle">
              <thead class="table-light text-center">
                <tr>
                  <th style="width:5%">#</th>
                  <th>Bilti</th>
                  <th>Truck No </th>
                  <th>Bags</th>
                  <th>Rate</th>
                  <th>Tons</th>
                  <th>Total</th>
                  <th>Date</th>
                  <th style="width:10%">Actions</th>
                </tr>
              </thead>
              <tbody>
                @forelse($receivables as $r)
                  <tr>
                    <td class="text-center">
                      {{ $loop->iteration + ($receivables->currentPage()-1)*$receivables->perPage() }}
                    </td>
                    <td>{{ $r->bilti_no ?? '-' }}</td>
                    <td>{{ $r->truck_no ?? '-' }}</td>
                    <td class="text-center">{{ $r->no_of_bags }}</td>
                    <td class="text-end">{{ number_format($r->amount_per_bag,2) }}</td>
                    <td>{{ $r->tons ?? '-' }}</td>
                    <td class="text-end">{{ number_format($r->total_amount,2) }}</td>
                    <td>{{ \Carbon\Carbon::parse($r->transaction_date)->format('d-m-Y') }}</td>
                    <td class="text-center">
                      <div class="d-flex justify-content-center gap-1">
                        <!-- Edit -->
                        <a href="{{ route('receivables.edit', $r->id) }}" 
                           class="btn btn-sm btn-soft-warning" title="Edit">
                          <i class="mdi mdi-pencil"></i>
                        </a>
                        <!-- Add Payment -->
                        <a href="{{ route('payments.create', ['payable_id' => $r->id]) }}" 
                           class="btn btn-sm btn-primary" 
                           title="Add Payment">
                          <i class="mdi mdi-plus"></i>
                        </a> 
                        <!-- Delete -->
                        <form action="{{ route('receivables.delete', $r->id) }}" method="POST"
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
                    <td colspan="8" class="text-center text-muted">No records found.</td>
                  </tr> --}}
                @endforelse
              </tbody>
            </table>

            @if(method_exists($receivables, 'links'))
              <div class="mt-3">
                {{ $receivables->links() }}
              </div>
            @endif
          </div>
        </div>

      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    $('#receivablesTable').DataTable({
        pageLength: 10,
        responsive: true,
        autoWidth: false,
        order: [[1, 'desc']], // Date column
        columnDefs: [
            { orderable: false, targets: [0, 7] } // # and Actions
        ]
    });
});
</script>
@endsection
