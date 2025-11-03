@extends('admin.layouts.shared')
@section('title', 'Cash Book')
@section('header-title', 'Cash Book')

@section('content')
<div class="container-fluid py-3">

    <form method="GET" class="mb-3">
        <div class="row g-2 align-items-end">
            <div class="col-md-2">
                <input type="date" name="from_date" class="form-control form-control-sm" value="{{ request('from_date') }}">
            </div>
            <div class="col-md-2">
                <input type="date" name="to_date" class="form-control form-control-sm" value="{{ request('to_date') }}">
            </div>
            <div class="col-md-2">
                <select name="party_type" class="form-select form-select-sm">
                    <option value="">All Parties</option>
                    <option value="dealer" {{ request('party_type') == 'dealer' ? 'selected' : '' }}>Dealer</option>
                    <option value="supplier" {{ request('party_type') == 'supplier' ? 'selected' : '' }}>Supplier</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary btn-sm me-1">Filter</button>
                <a href="{{ route('cheque.index') }}" class="btn btn-secondary btn-sm">Reset</a>
            </div>
            <div class="col-md-3 text-end">
                <a href="{{ route('cheque.create') }}" class="btn btn-success btn-sm me-1">Add Entry</a>
                <button type="button" id="exportExcelBtn" class="btn btn-outline-success btn-sm">Export</button>
            </div>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-bordered table-sm table-hover" id="cashBookTable">
            <thead>
                <tr>
                    <th style="width:90px;">Date</th>
                    <th style="min-width:180px;">Name</th>
                    <th class="text-end" style="width:100px;">Receive</th>
                    <th class="text-end" style="width:100px;">Pay</th>
                    <th class="text-end" style="width:110px;">Balance</th>
                    <th class="text-end" style="width:100px; background:#f0f8f0;">Acc</th>
                    <th class="text-end" style="width:100px; background:#f0f8f0;">Cash</th>
                    <th class="text-center" style="width:110px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $balance = 0;
                    $cashBalance = 0;
                    $accBalance = 0;
                    $lastDate = null;
                @endphp

                @foreach ($entries as $entry)
                    @php
                        $receive = $entry->credit ?? 0;
                        $pay = $entry->debit ?? 0;
                        $balance += $receive - $pay;

                        if ($entry->payment_type === 'cash') {
                            $cashBalance += $receive - $pay;
                            $cash = $cashBalance;
                            $acc = 0;
                        } elseif (in_array($entry->payment_type, ['online', 'cheque'])) {
                            $accBalance += $receive - $pay;
                            $acc = $accBalance;
                            $cash = 0;
                        } else {
                            $cash = 0;
                            $acc = 0;
                        }

                        $currentDate = \Carbon\Carbon::parse($entry->date)->format('d/m/Y');
                    @endphp

                    <tr>
                        <td class="text-center">
                            @if ($lastDate !== $currentDate)
                                <strong>{{ $currentDate }}</strong>
                                @php $lastDate = $currentDate; @endphp
                            @endif
                        </td>
                        <td>
                            <strong>{{ $entry->party_name ?? ($entry->expense_description ?? '-') }}</strong>
                            @if ($entry->description)
                                <br><small class="text-muted">{{ Str::limit($entry->description, 40) }}</small>
                            @endif
                        </td>
                        <td class="text-end {{ $receive > 0 ? 'text-success fw-bold' : '' }}">{{ $receive > 0 ? number_format($receive, 0) : '' }}</td>
                        <td class="text-end {{ $pay > 0 ? 'text-danger fw-bold' : '' }}">{{ $pay > 0 ? number_format($pay, 0) : '' }}</td>
                        <td class="text-end fw-bold {{ $balance >= 0 ? 'text-success' : 'text-danger' }}">{{ number_format($balance, 0) }}</td>
                        <td class="text-end" style="background:#f8fff8;">{{ $acc != 0 ? number_format($acc, 0) : '' }}</td>
                        <td class="text-end" style="background:#f8fff8;">{{ $cash != 0 ? number_format($cash, 0) : '' }}</td>
                        <td class="text-center">
                            <a href="{{ route('cheque.edit', $entry->id) }}" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('cheque.destroy', $entry->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this entry?')">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="https://cdn.sheetjs.com/xlsx-0.20.3/package/dist/xlsx.full.min.js"></script>
@endsection
