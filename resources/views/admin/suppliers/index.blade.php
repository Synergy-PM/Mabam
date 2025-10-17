@extends('admin.layouts.shared')
@section('title', 'Suppliers')
@section('header-title', 'Suppliers')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow-sm border-0">
                <div class="card-header d-flex justify-content-between align-items-center bg-light">
                    <h4 class="card-title mb-0">Suppliers List</h4>
                    <div class="d-flex gap-2">
                        <a href="{{ route('suppliers.create') }}" class="btn btn-sm btn-primary">
                            <i class="mdi mdi-plus"></i> Add Supplier
                        </a>
                        <a href="{{ route('suppliers.trash') }}" class="btn btn-sm btn-danger d-flex align-items-center gap-2"
                           title="Deleted Suppliers">
                            <i class="bi bi-trash-fill"></i>
                            <span>Trash</span>
                            <span class="badge bg-light text-dark">{{ $trashSuppliers ?? 0 }}</span>
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table id="payablesTable" class="table table-hover table-striped table-bordered align-middle">
                          <thead class="table-light text-center">
                                <tr>
                                    <th style="width:5%">#</th>
                                    <th>Supplier Company Name</th>
                                    {{-- <th>Company</th>
                                    <th>City</th>
                                    <th>Email</th>
                                    <th>WhatsApp</th>
                                    <th>Contact Person</th>
                                    <th>Contact No</th>
                                    <th>Contact Email</th>
                                    <th>Address</th> --}}
                                    <th style="width:10%">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($suppliers as $index => $supplier)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $supplier->supplier_name ?? '-' }}</td>
                                        {{-- <td>{{ $supplier->company_name ?? '-' }}</td>
                                        <td>{{ $supplier->city->name ?? '-' }}</td>
                                        <td>{{ $supplier->email ?? '-' }}</td>
                                        <td>{{ $supplier->whatsapp ?? '-' }}</td>
                                        <td>{{ $supplier->contact_person ?? '-' }}</td>
                                        <td>{{ $supplier->contact_no ?? '-' }}</td>
                                        <td>{{ $supplier->contact_email ?? '-' }}</td>
                                        <td>{{ $supplier->address ?? '-' }}</td> --}}
                                        <td>
                                            <div class="d-flex justify-content-center gap-1">
                                                <a href="{{ route('suppliers.edit', $supplier->id) }}" class="btn btn-sm btn-soft-warning" title="Edit">
                                                    <i class="mdi mdi-pencil"></i>
                                                </a>
                                                <form action="{{ route('suppliers.delete', $supplier->id) }}" method="POST"
                                                      onsubmit="return confirm('Are you sure you want to delete this supplier?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-soft-danger" title="Delete">
                                                        <i class="mdi mdi-trash-can"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    {{-- <tr>
                                        <td colspan="11" class="text-center text-muted">No suppliers found.</td>
                                    </tr> --}}
                                @endforelse
                            </tbody>
                        </table>

                        <div class="mt-3">
                            {{ $suppliers->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
