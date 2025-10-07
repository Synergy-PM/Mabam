@extends('admin.layouts.shared')
@section('title', 'Expenses')
@section('header-title', 'Expenses')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow-sm border-0">
                <div class="card-header d-flex justify-content-between align-items-center bg-light">
                    <h4 class="card-title mb-0">Expenses List</h4>
                    <div class="d-flex gap-2">
                        <a href="{{ route('expenses.create') }}" class="btn btn-sm btn-primary">
                            <i class="mdi mdi-plus"></i> Add Expense
                        </a>
                        <a href="{{ route('expenses.trash') }}" class="btn btn-sm btn-danger d-flex align-items-center gap-2"
                            title="Deleted Expenses">
                            <i class="bi bi-trash-fill"></i>
                            <span>Trash</span>
                            <span class="badge bg-light text-dark">{{ $trashExpenses ?? 0 }}</span>
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table id="expensesTable" class="table table-hover table-striped table-bordered align-middle text-center">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Date</th>
                                    <th>Description</th>
                                    <th>Amount</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($expenses as $index => $expense)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ \Carbon\Carbon::parse($expense->expense_date)->format('d M Y') }}</td>
                                        <td>{{ $expense->expense_description }}</td>
                                        <td>{{ number_format($expense->amount, 2) }}</td>
                                        <td class="text-center">
                                            <a href="{{ route('expenses.edit', $expense->id) }}"
                                                class="btn btn-sm btn-soft-warning"><i class="mdi mdi-pencil"></i></a>
                                            <form action="{{ route('expenses.delete', $expense->id) }}" method="POST" style="display:inline-block;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-soft-danger">
                                                    <i class="mdi mdi-trash-can"></i>
                                                </button>
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
    $('#expensesTable').DataTable({
        pageLength: 10,
        responsive: true,
        autoWidth: false,
        order: [[1, 'desc']], // Sort by Date
        columnDefs: [
            { orderable: false, targets: [0, 4] } // # and Actions
        ]
    });
});
</script>
@endsection
