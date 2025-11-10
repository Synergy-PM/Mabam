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
                            @can('supplier_create')
                                <a href="{{ route('suppliers.create') }}" class="btn btn-sm btn-primary">
                                    <i class="mdi mdi-plus"></i> Add Supplier
                                </a>
                            @endcan
                            @can('supplier_trash_view')
                                <a href="{{ route('suppliers.trash') }}"
                                    class="btn btn-sm btn-danger d-flex align-items-center gap-2" title="Deleted Suppliers">
                                    <i class="bi bi-trash-fill"></i>
                                    <span>Trash</span>
                                    <span class="badge bg-light text-dark">{{ $trashSuppliers ?? 0 }}</span>
                                </a>
                            @endcan
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="suppilerTable" class="table table-hover table-striped table-bordered align-middle">
                                <thead class="table-light text-center">
                                    <tr>
                                        <th style="width:5%">#</th>
                                        <th>Supplier Company Name</th>
                                        <th>Opening balance</th>
                                        <th>Transaction Type</th>
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
                                    @forelse ($suppliers as $supplier)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $supplier->supplier_name ?? '-' }}</td>
                                            <td>{{ $supplier->opening_balance ?? '-' }}</td>
                                            <td>{{ $supplier->transaction_type ?? '-' }}</td>
                                            <td>
                                                @can('supplier_edit')
                                                    <div class="d-flex justify-content-center gap-1">
                                                        <a href="{{ route('suppliers.edit', $supplier->id) }}"
                                                            class="btn btn-sm btn-soft-warning" title="Edit">
                                                            <i class="mdi mdi-pencil"></i>
                                                        </a>
                                                    @endcan
                                                    @can('supplier_trash')
                                                        <form action="{{ route('suppliers.delete', $supplier->id) }}"
                                                            method="POST"
                                                            onsubmit="return confirm('Are you sure you want to delete this supplier?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-soft-danger"
                                                                title="Delete">
                                                                <i class="mdi mdi-trash-can"></i>
                                                            </button>
                                                        </form>
                                                    @endcan
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        {{-- <tr>
                                            <td colspan="4" class="text-center text-muted">No suppliers found.</td>
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
