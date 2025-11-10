@extends('admin.layouts.shared')
@section('title', 'Suppliers Trash')
@section('header-title', 'Suppliers Trash')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow-sm border-0">
                <div class="card-header d-flex justify-content-between align-items-center bg-light">
                    <h4 class="card-title mb-0">Deleted Suppliers</h4>
                    <div>
                        <a href="{{ route('suppliers.index') }}" class="btn btn-sm btn-secondary">
                            <i class="mdi mdi-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle table-striped table-bordered mb-0 text-center">
                            <thead class="table-light">
                                <tr>
                                    <th style="width:5%">#</th>
                                    <th>Supplier Company Name</th>
                                    <th>Opening Balance</th>
                                    {{-- <th>City</th>
                                    <th>Email</th>
                                    <th>WhatsApp</th>
                                    <th>Contact Person</th>
                                    <th>Contact No</th>
                                    <th>Contact Email</th>
                                    <th>Address</th> --}}
                                    <th style="width:12%">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($suppliers as $index => $supplier)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $supplier->supplier_name ?? '-' }}</td>
                                        <td>{{ $supplier->opening_balance ?? '-' }}</td>
                                        {{-- <td>{{ $supplier->city->name ?? '-' }}</td>
                                        <td>{{ $supplier->email ?? '-' }}</td>
                                        <td>{{ $supplier->whatsapp ?? '-' }}</td>
                                        <td>{{ $supplier->contact_person ?? '-' }}</td>
                                        <td>{{ $supplier->contact_no ?? '-' }}</td>
                                        <td>{{ $supplier->contact_email ?? '-' }}</td>
                                        <td>{{ $supplier->address ?? '-' }}</td> --}}
                                        <td>
                                            <div class="d-flex justify-content-center gap-1">
                                                <a href="{{ route('suppliers.restore', $supplier->id) }}" 
                                                   class="btn btn-sm btn-success" 
                                                   title="Restore"
                                                   onclick="return confirm('Are you sure you want to restore this supplier?')">
                                                    <i class="mdi mdi-restore"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    {{-- <tr>
                                        <td colspan="11" class="text-center text-muted">No deleted suppliers found.</td>
                                    </tr> --}}
                                @endforelse
                            </tbody>
                        </table>

                        {{-- <div class="mt-3">
                            {{ $suppliers->links() }}
                        </div> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
