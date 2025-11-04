@extends('admin.layouts.shared')
@section('title', 'Cash Book')
@section('header-title', 'Cash Book')

@section('content')
<div class="container-fluid py-3">

    <!-- Filter Form -->
    <form method="GET" class="mb-3">
        <div class="row g-2 align-items-end">
            <div class="col-md-2">
                <label class="form-label fw-semibold">From Date</label>
                <input type="date" name="from_date" class="form-control form-control-sm"
                    value="{{ request('from_date') }}">
            </div>

            <div class="col-md-2">
                <label class="form-label fw-semibold">To Date</label>
                <input type="date" name="to_date" class="form-control form-control-sm"
                    value="{{ request('to_date') }}">
            </div>

            <div class="col-md-2">
                <label class="form-label fw-semibold">Party Type</label>
                <select name="party_type" class="form-select form-select-sm">
                    <option value="">All</option>
                    <option value="supplier" {{ request('party_type') == 'supplier' ? 'selected' : '' }}>Supplier</option>
                    <option value="dealer" {{ request('party_type') == 'dealer' ? 'selected' : '' }}>Dealer</option>
                    <option value="expense" {{ request('party_type') == 'expense' ? 'selected' : '' }}>Expense</option>
                </select>
            </div>

            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-primary w-50">Filter</button>
                <a href="{{ route('cheque.create') }}" class="btn btn-success btn-sm w-50">
                    <i class="fas fa-plus"></i> Add Entry
                </a>
            </div>
        </div>
    </form>

    <!-- Table -->
    <div class="card shadow-sm">
        <div class="card-body table-responsive p-0">
            <table id="cashBookTable" class="table table-hover table-striped table-bordered align-middle">
                <thead class="table-light text-center">
                    <tr>
                        <th width="8%">Date</th>
                        <th width="25%">Party / Description</th>
                        <th width="10%">Receive</th>
                        <th width="10%">Pay</th>
                        <th width="10%">Balance</th>
                        <th width="10%">Account</th>
                        <th width="10%">Cash</th>
                        <th width="10%">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $balance = 0;
                        $cashBalance = 0;
                        $accBalance = 0;
                    @endphp

                    @foreach ($groupedEntries as $date => $parties)
                        <!-- Date Heading (Ignored by DataTables using .group-row class) -->
                        {{-- <tr class="table-secondary text-center fw-bold group-row">
                            <td colspan="8">{{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}</td>
                        </tr> --}}

                        @foreach ($parties as $partyName => $entries)
                            @foreach ($entries as $entry)
                                @php
                                    $receive = $entry->debit ?? 0;
                                    $pay = $entry->credit ?? 0;
                                    $balance += ($receive - $pay);

                                    if ($entry->payment_type === 'cash') {
                                        $cashBalance += ($receive - $pay);
                                        $cash = $cashBalance;
                                        $acc = '';
                                    } elseif (in_array($entry->payment_type, ['online', 'cheque'])) {
                                        $accBalance += ($receive - $pay);
                                        $acc = $accBalance;
                                        $cash = '';
                                    } else {
                                        $cash = $acc = '';
                                    }
                                @endphp

                                <tr>
                                    <td class="text-center">{{ \Carbon\Carbon::parse($entry->date)->format('d/m/Y') }}</td>
                                    <td>
                                        <strong>{{ $entry->party_name ?? ($entry->expense_description ?? '-') }}</strong>
                                        <br>
                                        <small class="text-muted">
                                            ({{ ucfirst($entry->party_type ?? 'Expense') }})
                                        </small>
                                        @if ($entry->description)
                                            <br>
                                            <small class="text-muted">{{ Str::limit($entry->description, 40) }}</small>
                                        @endif
                                    </td>
                                    <td class="text-end {{ $receive > 0 ? 'text-success fw-bold' : '' }}">
                                        {{ $receive > 0 ? number_format($receive, 0) : '' }}
                                    </td>
                                    <td class="text-end {{ $pay > 0 ? 'text-danger fw-bold' : '' }}">
                                        {{ $pay > 0 ? number_format($pay, 0) : '' }}
                                    </td>
                                    <td class="text-end fw-bold {{ $balance >= 0 ? 'text-success' : 'text-danger' }}">
                                        {{ number_format($balance, 0) }}
                                    </td>
                                    <td class="text-end" style="background:#f8fff8;">
                                        {{ $acc ? number_format($acc, 0) : '' }}
                                    </td>
                                    <td class="text-end" style="background:#f8fff8;">
                                        {{ $cash ? number_format($cash, 0) : '' }}
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('cheque.edit', $entry->id) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('cheque.destroy', $entry->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger"
                                                onclick="return confirm('Delete this entry?')">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
