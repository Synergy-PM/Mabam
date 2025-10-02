@extends('admin.layouts.shared')
@section('title', 'Cities')
@section('header-title', 'Cities')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow-sm border-0">
                <div class="card-header d-flex justify-content-between align-items-center bg-light">
                    <h4 class="card-title mb-0">Cities List</h4>
                    <div class="d-flex gap-2">
                        <a href="{{ route('cities.create') }}" class="btn btn-sm btn-primary">
                            <i class="mdi mdi-plus"></i> Add City
                        </a>
                        <a href="{{ route('cities.trash') }}" class="btn btn-sm btn-danger d-flex align-items-center gap-2"
                            title="Deleted Cities">
                            <i class="bi bi-trash-fill"></i>
                            <span>Trash</span>
                            <span class="badge bg-light text-dark">{{ $trashCities ?? 0 }}</span>
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table id="citiesTable"
                            class="table table-hover table-striped table-bordered align-middle text-center">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($cities as $index => $city)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $city->name }}</td>
                                        <td class="text-center">
                                            <a href="{{ route('cities.edit', $city->id) }}"
                                                class="btn btn-sm btn-soft-warning"><i class="mdi mdi-pencil"></i></a>
                                            <form action="{{ route('cities.delete', $city->id) }}" method="POST"
                                                style="display:inline-block;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-soft-danger"><i
                                                        class="mdi mdi-trash-can"></i></button>
                                            </form>
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

@section('scripts')
<script>
$(document).ready(function() {
    $('#citiesTable').DataTable({
        pageLength: 10,
        responsive: true,
        autoWidth: false,
        order: [[1, 'asc']], // Name column
        columnDefs: [
            { orderable: false, targets: [0, 2] } // # and Actions
        ]
    });
});
</script>
@endsection
