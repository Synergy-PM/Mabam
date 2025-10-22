@extends('admin.layouts.shared')
@section('title', 'Deleted Expenses')
@section('header-title', 'Deleted Expenses')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow-sm border-0">
                <div class="card-header d-flex justify-content-between align-items-center bg-light">
                    <h4 class="card-title mb-0">Deleted Expenses</h4>
                    <a href="{{ route('expenses.index') }}" class="btn btn-sm btn-secondary">
                        <i class="mdi mdi-arrow-left"></i> Back to List
                    </a>
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
                                    <th>Deleted At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($expenses as $index => $expense)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ \Carbon\Carbon::parse($expense->expense_date)->format('d M Y') }}</td>
                                        <td>{{ $expense->expense_description }}</td>
                                        <td>{{ number_format($expense->amount, 2) }}</td>
                                        <td>{{ \Carbon\Carbon::parse($expense->deleted_at)->format('d M Y h:i A') }}</td>
                                        <td class="text-center">
                                            <form action="{{ route('expenses.restore', $expense->id) }}" method="POST" style="display:inline-block;">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success" title="Restore">
                                                    <i class="mdi mdi-restore"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('expenses.forceDelete', $expense->id) }}" method="POST" style="display:inline-block;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Delete Permanently"
                                                    onclick="return confirm('Are you sure you want to permanently delete this expense?')">
                                                    <i class="mdi mdi-delete-forever"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">No deleted expenses found.</td>
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
