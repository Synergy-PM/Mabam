@extends('admin.layouts.shared')
@section('title','Payables Trash')
@section('header-title','Payables Trash')

@section('content')
<div class="container-fluid">
  <div class="card">
    <div class="card-header d-flex justify-content-between bg-light">
      <h4 class="mb-0">Trashed Payables</h4>
      <a href="{{ route('payables.index') }}" class="btn btn-sm btn-secondary">Back</a>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle text-center">
          <thead>
            <tr>
              <th>#</th>
              <th>Date</th>
              <th>Supplier</th>
              <th>Total</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($payables as $p)
              <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $p->transaction_date->format('d-m-Y') }}</td>
                <td>{{ $p->supplier->supplier_name ?? '-' }}</td>
                <td class="text-end">{{ number_format($p->total_amount,2) }}</td>
                <td>
                  <a href="{{ route('payables.restore', $p->id) }}" class="btn btn-sm btn-success">Restore</a>
                  {{-- <a href="{{ route('payables.forceDelete', $p->id) }}" class="btn btn-sm btn-danger" onclick="return confirm('Permanently delete?')">Delete</a> --}}
                </td>
              </tr>
            @empty
              <tr><td colspan="5" class="text-muted">No trashed records.</td></tr>
            @endforelse
          </tbody>
        </table>
        <div class="mt-3">{{ $payables->links() }}</div>
      </div>
    </div>
  </div>
</div>
@endsection
