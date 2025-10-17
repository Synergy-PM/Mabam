@extends('admin.layouts.shared')
@section('title', 'Ledger Report')
@section('header-title', 'Ledger Report')

@section('content')
<div class="container-fluid">
    <div class="card shadow border-0 rounded-4 overflow-hidden">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-file-invoice me-2"></i> Ledger Report</h5>
            <a href="{{ route('receivable-payments.ledger-report-filter') }}" class="btn btn-light btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Back
            </a>
        </div>

        <div class="card-body">
            {{-- Report Information --}}
            <div class="mb-4 border rounded p-3 bg-light">
                <strong>Dealer:</strong> {{ $selectedDealer->dealer_name ?? 'All' }}<br>
                <strong>Period:</strong>
                {{ $startDate ? \Carbon\Carbon::parse($startDate)->format('d M Y') : 'All' }}
                →
                {{ $endDate ? \Carbon\Carbon::parse($endDate)->format('d M Y') : 'All' }}
            </div>

            @if($transactions->isNotEmpty())
                @php
                    $openingBalance = $openBalance ?? 0; // Controller se opening balance
                    $balance = $openingBalance; // Running balance
                    $totalDebit = 0;
                    $totalCredit = 0;
                @endphp

                {{-- Opening Balance Display --}}
                <p class="d-flex justify-content-between align-items-center mb-3" style="font-size: 16px;">
                    <strong>Opening Balance:</strong> 
                    <span class="fw-bold">{{ number_format($openingBalance, 2) }}</span>
                </p>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped text-center align-middle">
                        <thead class="table-primary">
                            <tr>
                                <th>S.No</th>
                                <th>Dealer</th>
                                <th>Date</th>
                                <th>Credit</th>
                                <th>Debit</th>
                                <th>Payment Mode</th>
                                <th>Type</th>
                                <th>Balance</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transactions as $index => $t)
                                @php
                                    $amount = $t['amount'] ?? 0;
                                    $transactionType = strtolower($t['transaction_type'] ?? '');
                                @endphp
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $t['dealer']->dealer_name ?? 'N/A' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($t['transaction_date'])->format('d M Y') }}</td>
                                    
                                    {{-- Credit Column --}}
                                    <td>
                                        @if($transactionType == 'credit')
                                            {{ number_format($amount, 2) }}
                                            @php 
                                                $totalCredit += $amount; 
                                                $balance += $amount; 
                                            @endphp
                                        @else
                                            -
                                        @endif
                                    </td>
                                    
                                    {{-- Debit Column --}}
                                    <td>
                                        @if($transactionType == 'debit')
                                            {{ number_format($amount, 2) }}
                                            @php 
                                                $totalDebit += $amount; 
                                                $balance -= $amount; 
                                            @endphp
                                        @else
                                            -
                                        @endif
                                    </td>
                                    
                                    <td>{{ ucfirst($t['payment_mode'] ?? '') }}</td>
                                    <td>{{ ucfirst($transactionType) }}</td>
                                    <td>
                                        <span class="fw-bold">{{ number_format($balance, 2) }}</span>
                                    </td>
                                </tr>
                            @endforeach

                            {{-- Totals Row --}}
                            <tr class="table-secondary fw-bold">
                                <td colspan="3">
                                    <strong>Total</strong>
                                </td>
                                <td>{{ number_format($totalCredit, 2) }}</td>
                                <td>{{ number_format($totalDebit, 2) }}</td>
                                <td colspan="2"></td>
                                <td>{{ number_format($balance, 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- Summary --}}
                <div class="mt-3 p-3 bg-light rounded border">
                    <div class="row">
                        <div class="col-md-4">
                            <strong>Total Credit:</strong> {{ number_format($totalCredit, 2) }}
                        </div>
                        <div class="col-md-4">
                            <strong>Total Debit:</strong> {{ number_format($totalDebit, 2) }}
                        </div>
                        <div class="col-md-4">
                            <strong>Closing Balance:</strong> {{ number_format($balance, 2) }}
                        </div>
                    </div>
                </div>

                @if(!$selectedDealer)
                    <div class="mt-5">
                        <h5 class="fw-semibold mb-3">Dealer-wise Closing Balances</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped text-center align-middle">
                                <thead class="table-primary">
                                    <tr>
                                        <th>S.No</th>
                                        <th>Dealer Name</th>
                                        <th>Closing Balance</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $count = 1; @endphp
                                    @foreach($dealerSummaries as $dealer)
                                        <tr>
                                            <td>{{ $count++ }}</td>
                                            <td>{{ $dealer['dealer_name'] ?? 'N/A' }}</td>
                                            <td>
                                                <span class="fw-bold">
                                                    {{ number_format(($dealer['total_credit'] ?? 0) - ($dealer['total_debit'] ?? 0), 2) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

            @else
                <div class="text-center text-muted py-4">
                    <i class="fas fa-folder-open me-2"></i> No transactions found
                </div>
            @endif
        </div>
    </div>
</div>
@endsection