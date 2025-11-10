@extends('admin.layouts.shared')
@section('title', 'Ledger Report')
@section('header-title', 'Ledger Report')

@section('content')
    <div class="container-fluid">

        {{-- Error Messages --}}
        @if ($errors->any())
            @foreach ($errors->all() as $error)
                <div class="alert alert-danger alert-dismissible fade show shadow-sm rounded-3" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i> {{ $error }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endforeach
        @endif

        <div class="row">
            <div class="col-12">
                <div class="card shadow border-0 rounded-4 overflow-hidden">

                    {{-- Card Header --}}
                    <div class="card-header d-flex justify-content-between align-items-center bg-gradient bg-primary text-white p-3">
                        <h5 class="mb-0 text-white">
                            <i class="fas fa-file-invoice-dollar me-2"></i> Receivable Ledger Report
                        </h5>
                        <div>
                            <button id="printLedger" class="btn btn-light btn-sm shadow-sm me-2">
                                <i class="fas fa-print text-primary me-1"></i> Print
                            </button>
                            <button id="exportPDF" class="btn btn-light btn-sm shadow-sm">
                                <i class="fas fa-file-pdf text-danger me-1"></i> Export PDF
                            </button>
                            <a href="{{ route('receivable-payments.ledger-report-filter') }}" class="btn btn-light btn-sm shadow-sm">
                                <i class="fas fa-arrow-left text-primary me-1"></i> Back
                            </a>
                        </div>
                    </div>

                    {{-- Card Body --}}
                    <div class="card-body bg-white" id="ledger-section">

                        {{-- Report Information --}}
                        <div class="border rounded-3 p-3 mb-4 bg-light shadow-sm">
                            <h6 class="fw-bold text-secondary mb-3">
                                <i class="fas fa-info-circle me-2"></i> Report Information
                            </h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Dealer:</strong> {{ $selectedDealer->dealer_name ?? 'All' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1">
                                        <strong>Period:</strong>
                                        {{ $startDate ? \Carbon\Carbon::parse($startDate)->format('d M, Y') : 'All' }} →
                                        {{ $endDate ? \Carbon\Carbon::parse($endDate)->format('d M, Y') : 'All' }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- Results Table --}}
                        <div class="table-responsive shadow-sm rounded-3">
                            <table id="ledgerTable" class="table table-bordered table-hover align-middle text-center mb-0">
                                <thead class="table-primary">
                                    <tr>
                                        <th>S.No</th>
                                        <th>Dealer</th>
                                        <th>Date</th>
                                        <th>Tons</th>
                                        <th>Rate</th>
                                        <th>Credit</th>
                                        <th>Debit</th>
                                        <th>Payment Mode</th>
                                        <th>Type</th>
                                        <th>Balance</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $balance = 0;
                                        $totalCredit = 0;
                                        $totalDebit = 0;
                                    @endphp
                                    @foreach ($transactions as $index => $t)
                                        @php
                                            $amount = $t['amount'] ?? 0;
                                            $transactionType = strtolower($t['transaction_type'] ?? '');
                                            if ($t['is_opening']) {
                                                $balance = $amount * ($transactionType === 'credit' ? 1 : -1);
                                            } else {
                                                $balance += ($transactionType === 'credit' ? $amount : -$amount);
                                            }
                                            if ($transactionType === 'credit') {
                                                $totalCredit += $amount;
                                            } elseif ($transactionType === 'debit') {
                                                $totalDebit += $amount;
                                            }
                                        @endphp
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $t['dealer']->dealer_name ?? 'N/A' }}</td>
                                            <td>{{ \Carbon\Carbon::parse($t['transaction_date'])->format('d M, Y') }}</td>
                                            <td>{{ number_format($t['tons'], 2) }}</td>
                                            <td>{{ number_format($t['rate'], 2) }}</td>
                                            <td>
                                                @if ($transactionType == 'credit')
                                                    {{ number_format($amount, 2) }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                @if ($transactionType == 'debit')
                                                    {{ number_format($amount, 2) }}
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
                                        <td>{{ number_format($transactions->where('is_opening', false)->sum('tons'), 2) }}</td>
                                        <td>{{ number_format($transactions->where('is_opening', false)->sum('rate'), 2) }}</td>
                                        <td>{{ number_format($totalCredit, 2) }}</td>
                                        <td>{{ number_format($totalDebit, 2) }}</td>
                                        <td colspan="2"></td>
                                        <td>{{ number_format($totalCredit - $totalDebit, 2) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        {{-- Totals Summary --}}
                        <div class="mt-3 p-3 bg-light rounded border shadow-sm">
                            <div class="row">
                                <div class="col-md-4">
                                    <p class="mb-1"><strong>Total Credit:</strong> {{ number_format($totalCredit, 2) }}</p>
                                </div>
                                <div class="col-md-4">
                                    <p class="mb-1"><strong>Total Debit:</strong> {{ number_format($totalDebit, 2) }}</p>
                                </div>
                                <div class="col-md-4">
                                    <p class="mb-1"><strong>Closing Balance:</strong>
                                        {{ number_format($totalCredit - $totalDebit, 2) }}</p>
                                </div>
                            </div>
                        </div>

                        @if (!$selectedDealer && count($dealerSummaries))
                            <div class="mt-5">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="fw-semibold mb-0">Dealer-wise Closing Balances</h5>
                                    <div>
                                        <button id="printDealerSummary" class="btn btn-outline-primary btn-sm me-2">
                                            <i class="fas fa-print me-1"></i> Print
                                        </button>
                                        <button id="exportDealerPDF" class="btn btn-outline-danger btn-sm">
                                            <i class="fas fa-file-pdf me-1"></i> PDF
                                        </button>
                                    </div>
                                </div>

                                <div id="dealer-summary" class="table-responsive shadow-sm rounded-3">
                                    <table class="table table-bordered table-striped text-center align-middle">
                                        <thead class="table-primary">
                                            <tr>
                                                <th>S.No</th>
                                                <th>Dealer Name</th>
                                                <th>Tons</th>
                                                <th>Closing Balance</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php $count = 1; @endphp
                                            @foreach ($dealerSummaries as $dealer)
                                                <tr>
                                                    <td>{{ $count++ }}</td>
                                                    <td>{{ $dealer['dealer_name'] }}</td>
                                                    <td>{{ number_format($dealer['tons'], 2) }}</td>
                                                    <td>
                                                        <span class="fw-bold">{{ number_format($dealer['closing_balance'], 2) }}</span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif
                        @if ($transactions->isEmpty())
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-folder-open me-2"></i> No transactions found
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- PDF Export Scripts --}}
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.13/jspdf.plugin.autotable.min.js"></script>

        <script>
            document.getElementById('exportPDF').addEventListener('click', function() {
                const { jsPDF } = window.jspdf;
                const doc = new jsPDF('landscape');
                doc.text("Receivable Ledger Report", 14, 15);
                doc.autoTable({
                    html: '#ledgerTable',
                    startY: 25,
                    theme: 'grid'
                });
                doc.save('Ledger_Report.pdf');
            });

            document.getElementById('printLedger').addEventListener('click', function() {
                const content = document.getElementById('ledger-section').innerHTML;
                const printWin = window.open('', '_blank');
                printWin.document.write(`<html><head><title>Ledger Report</title></head><body>${content}</body></html>`);
                printWin.document.close();
                printWin.print();
            });

            const dealerPDFBtn = document.getElementById('exportDealerPDF');
            if (dealerPDFBtn) {
                dealerPDFBtn.addEventListener('click', function() {
                    const { jsPDF } = window.jspdf;
                    const doc = new jsPDF('landscape');
                    doc.text("Dealer-wise Closing Balances", 14, 15);
                    doc.autoTable({
                        html: '#dealer-summary table',
                        startY: 25,
                        theme: 'grid'
                    });
                    doc.save('Dealer_Summary.pdf');
                });
            }

            const dealerPrintBtn = document.getElementById('printDealerSummary');
            if (dealerPrintBtn) {
                dealerPrintBtn.addEventListener('click', function() {
                    const content = document.getElementById('dealer-summary').innerHTML;
                    const printWin = window.open('', '_blank');
                    printWin.document.write(`<html><head><title>Dealer Summary</title></head><body>${content}</body></html>`);
                    printWin.document.close();
                    printWin.print();
                });
            }
        </script>
    </div>
@endsection