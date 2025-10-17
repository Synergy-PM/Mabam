@extends('admin.layouts.shared')
@section('title', 'Dealers')
@section('header-title', 'Dealers')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow-sm border-0">
                <div class="card-header d-flex justify-content-between align-items-center bg-light">
                    <h4 class="card-title mb-0">Dealers List</h4>
                    <div class="d-flex gap-2">
                        <a href="{{ route('dealers.create') }}" class="btn btn-sm btn-primary">
                            <i class="mdi mdi-plus"></i> Add Dealer
                        </a>
                        <a href="{{ route('dealers.trash') }}" class="btn btn-sm btn-danger d-flex align-items-center gap-2"
                           title="Deleted Dealers">
                            <i class="bi bi-trash-fill"></i>
                            <span>Trash</span>
                            <span class="badge bg-light text-dark">{{ $trashDealers ?? 0 }}</span>
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table id="DealerTable" class="table table-hover table-striped table-bordered align-middle">
                           <thead class="table-light text-center">
                                <tr>
                                    <th style="width:5%">#</th>
                                    <th>Dealer Name</th>
                                    <th>Company</th>
                                    <th>Contact No</th>
                                    <th>City</th>
                                    {{-- <th>Email</th>
                                    <th>WhatsApp</th>
                                    <th>Contact Person</th> --}}
                                    {{-- <th>Contact Email</th> --}}
                                    {{-- <th>Address</th> --}}
                                    <th style="width:10%">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($dealers as $index => $dealer)
                                    <tr>
                                        <td class="text-center">{{ $index + 1 }}</td>
                                        <td>{{$dealer->dealer_name ?? '-'}}
                                        <td>{{ $dealer->company_name ?? '-' }}</td>
                                        <td>{{ $dealer->contact_no ?? '-' }}</td>
                                        <td>{{ $dealer->city->name ?? '-' }}</td>
                                        {{-- <td>{{ $dealer->email ?? '-' }}</td>
                                        <td>{{ $dealer->whatsapp ?? '-' }}</td>
                                        <td>{{ $dealer->contact_person ?? '-' }}</td> --}}
                                        {{-- <td>{{ $dealer->contact_email ?? '-' }}</td> --}}
                                        {{-- <td>{{ $dealer->address ?? '-' }}</td> --}}
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-1">
                                                <a href="{{ route('dealers.edit', $dealer->id) }}" class="btn btn-sm btn-soft-warning" title="Edit">
                                                    <i class="mdi mdi-pencil"></i>
                                                </a>
                                                <form action="{{ route('dealers.delete', $dealer->id) }}" method="POST"
                                                      onsubmit="return confirm('Are you sure you want to delete this dealer?')">
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
