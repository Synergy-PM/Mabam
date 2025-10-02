@extends('admin.layouts.shared')
@section('title', 'Deleted Dealers')
@section('header-title', 'Deleted Dealers')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow-sm border-0">
                <div class="card-header d-flex justify-content-between align-items-center bg-light">
                    <h4 class="card-title mb-0">Deleted Dealers</h4>
                    <a href="{{ route('dealers.index') }}" class="btn btn-sm btn-secondary">
                        <i class="mdi mdi-arrow-left"></i> Back
                    </a>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle table-striped table-bordered mb-0">
                            <thead class="table-light text-center">
                                <tr>
                                    <th style="width:5%">#</th>
                                    <th>Company</th>
                                    <th>City</th>
                                    <th>Email</th>
                                    <th>WhatsApp</th>
                                    <th>Address</th>
                                    <th style="width:15%">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($dealers as $index => $dealer)
                                    <tr>
                                        <td class="text-center">{{ $index + 1 }}</td>
                                        <td>{{ $dealer->company->name ?? '-' }}</td>
                                        <td>{{ $dealer->city->name ?? '-' }}</td>
                                        <td>{{ $dealer->email ?? '-' }}</td>
                                        <td>{{ $dealer->whatsapp ?? '-' }}</td>
                                        <td>{{ $dealer->address ?? '-' }}</td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-1">
                                                <a href="{{ route('dealers.restore', $dealer->id) }}"
                                                   class="btn btn-sm btn-soft-success" title="Restore">
                                                    <i class="mdi mdi-backup-restore"></i>
                                                </a>
                                                {{-- <form action="{{ route('dealers.delete', $dealer->id) }}" method="POST"
                                                      onsubmit="return confirm('Permanently delete this dealer?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-soft-danger" title="Delete">
                                                        <i class="mdi mdi-trash-can-outline"></i>
                                                    </button>
                                                </form> --}}
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted">No deleted dealers found.</td>
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
