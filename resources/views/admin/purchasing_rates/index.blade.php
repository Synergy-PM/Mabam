@extends('admin.layouts.shared')
@section('title', 'Purchasing Rates')
@section('header-title', 'Purchasing Rates')

@section('content')
<div class="container-fluid">
  <div class="row">
    <div class="col-12">

      <div class="card shadow-sm border-0">

        <!-- Header -->
        <div class="card-header d-flex justify-content-between align-items-center bg-light">
          <h4 class="card-title mb-0">Purchasing Rates</h4>

          <div class="d-flex gap-2">
            @can('purchasing_rate_create')
              <a href="{{ route('purchasing_rates.create') }}" class="btn btn-sm btn-primary d-flex align-items-center gap-1">
                <i class="mdi mdi-plus"></i> Add
              </a>
            @endcan

            @can('purchasing_rate_trash')
              <a href="{{ route('purchasing_rates.trash') }}" class="btn btn-sm btn-danger d-flex align-items-center gap-2">
                <i class="bi bi-trash-fill"></i> Trash
                <span class="badge bg-light text-dark ms-1">{{ $trashCount ?? 0 }}</span>
              </a>
            @endcan
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
            <table id="purchasingRatesTable" class="table table-hover table-striped table-bordered align-middle">
              <thead class="table-light text-center">
                <tr>
                  <th>#</th>
                  <th>From Date</th>
                  <th>To Date</th>
                  <th>Supplier</th>
                  <th>City</th>
                  <th>Amount / Ton</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                @foreach($rates as $rate)
                  <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($rate->from_date)->format('d M, Y') }}</td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($rate->to_date)->format('d M, Y') }}</td>
                    <td>{{ $rate->supplier->supplier_name ?? '-' }}</td>
                    <td>{{ $rate->city->name ?? '-' }}</td>
                    <td class="text-end">{{ number_format($rate->amount_per_ton, 2) }}</td>
                    <td class="text-center">
                      <div class="d-flex justify-content-center gap-1">
                        @can('purchasing_rate_edit')
                          <a href="{{ route('purchasing_rates.edit', $rate->id) }}" 
                             class="btn btn-sm btn-soft-warning" 
                             title="Edit">
                            <i class="mdi mdi-pencil"></i>
                          </a>
                        @endcan

                        @can('purchasing_rate_trash')
                          <form action="{{ route('purchasing_rates.destroy', $rate->id) }}" 
                                method="POST" 
                                onsubmit="return confirm('Delete this record?');">
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
                @endforeach
              </tbody>
            </table>
          </div>

        </div>
      </div>

    </div>
  </div>
</div>
@endsection
