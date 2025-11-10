@extends('admin.layouts.shared')
@section('title', 'Trashed Purchasing Rates')
@section('header-title', 'Trashed Purchasing Rates')

@section('content')
<div class="container-fluid">
  <div class="row">
    <div class="col-12">

      <div class="card shadow-sm border-0">

        <!-- Header -->
        <div class="card-header d-flex justify-content-between align-items-center bg-light">
          <h4 class="card-title mb-0">Trashed Purchasing Rates</h4>

          <div class="d-flex gap-2">
            <a href="{{ route('purchasing_rates.index') }}" class="btn btn-sm btn-secondary d-flex align-items-center gap-1">
              <i class="mdi mdi-arrow-left"></i> Back
            </a>
          </div>
        </div>

        <!-- Body -->
        <div class="card-body">
          @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
              {{ session('success') }}
              <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
          @endif

          <div class="table-responsive">
            <table id="trashPurchasingRatesTable" class="table table-hover table-striped table-bordered align-middle">
              <thead class="table-light text-center">
                <tr>
                  <th>#</th>
                  <th>From Date</th>
                  <th>To Date</th>
                  <th>Supplier</th>
                  <th>City</th>
                  <th>Amount / Ton</th>
                  <th>Deleted At</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                @forelse($trashedRates as $rate)
                  <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($rate->from_date)->format('d M, Y') }}</td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($rate->to_date)->format('d M, Y') }}</td>
                    <td>{{ $rate->supplier->supplier_name ?? '-' }}</td>
                    <td>{{ $rate->city->name ?? '-' }}</td>
                    <td class="text-end">{{ number_format($rate->amount_per_ton, 2) }}</td>
                    <td class="text-center">{{ $rate->deleted_at ? $rate->deleted_at->format('d M, Y h:i A') : '-' }}</td>
                    <td class="text-center">
                      <div class="d-flex justify-content-center gap-1">

                        {{-- Restore --}}
                        <form action="{{ route('purchasing_rates.restore', $rate->id) }}" method="POST" 
                              onsubmit="return confirm('Restore this record?');">
                          @csrf
                          <button class="btn btn-sm btn-soft-success" title="Restore">
                            <i class="mdi mdi-backup-restore"></i>
                          </button>
                        </form>

                        {{-- Permanent Delete --}}
                        <form action="{{ route('purchasing_rates.forceDelete', $rate->id) }}" method="POST" 
                              onsubmit="return confirm('Permanently delete this record? This action cannot be undone!');">
                          @csrf
                          @method('DELETE')
                          <button class="btn btn-sm btn-soft-danger" title="Permanent Delete">
                            <i class="mdi mdi-delete-forever"></i>
                          </button>
                        </form>

                      </div>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="8" class="text-center text-muted">No trashed purchasing rates found.</td>
                  </tr>
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
