@extends('admin.layouts.shared')
@section('title', 'Trashed Cities')
@section('header-title', 'Trashed Cities')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">

                <a href="{{ route('cities.index') }}" class="btn btn-secondary mb-3"><b>Back to Cities</b></a>

                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th width="60">#</th>
                            <th>Name</th>
                            <th width="220">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($cities as $city)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $city->name }}</td>
                                <td>
                                    {{-- Restore Button --}}
                                    <a href="{{ route('cities.restore', $city->id) }}" class="btn btn-sm btn-success">
                                        Restore
                                    </a>

                                    {{-- Permanent Delete --}}
                                    <form action="{{ route('cities.delete', $city->id) }}" method="POST" style="display:inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger" onclick="return confirm('Delete permanently?')">
                                            Delete Permanently
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center">No trashed cities found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</div>
@endsection
