@extends('admin.layouts.shared')
@section('title', 'Ledger Report Results')
@section('header-title', 'Ledger Report Results')

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
                            <i class="fas fa-file-invoice-dollar me-2"></i> Payable Ledger Report
                        </h5>
                        <div>
                            <button id="printLedger" class="btn btn-light btn-sm shadow-sm me-2">
                                <i class="fas fa-print text-primary me-1"></i> Print Ledger
                            </button>
                            <button id="exportPDF" class="btn btn-light btn-sm shadow-sm">
                                <i class="fas fa-file-pdf text-danger me-1"></i> Export PDF
                            </button>
                            <a href="{{ route('payable-payments.ledger-filter') }}" class="btn btn-light btn-sm shadow-sm">
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
                                    <p class="mb-1"><strong>Supplier:</strong>
                                        {{ $selectedSupplier->supplier_name ?? 'All' }}</p>
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
                                        <th>Supplier</th>
                                        <th>Tons</th>
                                        <th>Rate</th>
                                        <th>Credit</th>
                                        <th>Debit</th>
                                        <th>Payment Mode</th>
                                        <th>Date</th>
                                        <th>Balance</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $balance = $openBalance ?? 0;
                                        $totalDebit = 0;
                                        $totalCredit = 0;
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
                                            <td>{{ $t['supplier']->supplier_name ?? 'N/A' }}</td>
                                            <td>{{ $t['is_opening'] || $transactionType === 'credit' ? '-' : number_format($t['tons'], 2) }}</td>
                                            <td>{{ $t['is_opening'] || $transactionType === 'credit' ? '-' : number_format($t['rate'], 2) }}</td>
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
                                            <td>{{ \Carbon\Carbon::parse($t['transaction_date'])->format('d M, Y') }}</td>
                                            <td>{{ number_format($balance, 2) }}</td>
                                        </tr>
                                    @endforeach

                                    <tr class="table-secondary">
                                        <td colspan="2"><strong>Total</strong></td>
                                        <td><strong>{{ number_format($transactions->where('is_opening', false)->where('transaction_type', 'debit')->sum('tons'), 2) }}</strong></td>
                                        <td><strong>{{ number_format($transactions->where('is_opening', false)->where('transaction_type', 'debit')->sum('rate'), 2) }}</strong></td>
                                        <td><strong>{{ number_format($totalCredit, 2) }}</strong></td>
                                        <td><strong>{{ number_format($totalDebit, 2) }}</strong></td>
                                        <td colspan="3"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        {{-- Totals Summary --}}
                        <div class="mt-3">
                            <p><strong>Total Debit:</strong> {{ number_format($totalDebit, 2) }}</p>
                            <p><strong>Total Credit:</strong> {{ number_format($totalCredit, 2) }}</p>
                            <p><strong>Closing Balance:</strong> {{ number_format($totalCredit - $totalDebit, 2) }}</p>
                        </div>

                        @if (!$selectedSupplier && count($supplierSummaries))
                            <div class="mt-5">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="fw-semibold mb-0">Supplier-wise Closing Balances</h5>
                                    <div>
                                        <button id="printSupplierSummary" class="btn btn-outline-primary btn-sm me-2">
                                            <i class="fas fa-print me-1"></i> Print Summary
                                        </button>
                                        <button id="exportSupplierPDF" class="btn btn-outline-danger btn-sm">
                                            <i class="fas fa-file-pdf me-1"></i> Export PDF
                                        </button>
                                    </div>
                                </div>

                                <div id="supplier-summary" class="table-responsive">
                                    <table class="table table-bordered table-striped text-center align-middle">
                                        <thead class="table-primary">
                                            <tr>
                                                <th>S.No</th>
                                                <th>Supplier Name</th>
                                                <th>Tons</th>
                                                <th>Closing Balance</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($supplierSummaries as $index => $supplier)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $supplier['supplier_name'] }}</td>
                                                    <td>{{ number_format($supplier['tons'], 2) }}</td>
                                                    <td><strong>{{ number_format($supplier['closing_balance'], 2) }}</strong></td>
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
                doc.text("Payable Ledger Report", 14, 15);
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
                const ledgerTable = document.getElementById('ledgerTable').outerHTML;
                const reportInfo = document.querySelector('.border.rounded-3.p-3.mb-4.bg-light.shadow-sm').outerHTML;
                const totalsSummary = document.querySelector('.mt-3').outerHTML;
                printWin.document.write(`
                    <html><head><title>Ledger Report</title>
                    <style>
                        body { font-family: Arial, sans-serif; }
                        table { width: 100%; border-collapse: collapse; }
                        th, td { border: 1px solid #ddd; padding: 8px; text-align: center; }
                        th { background-color: #007bff; color: white; }
                        .table-secondary { background-color: #e9ecef; font-weight: bold; }
                    </style></head>
                    <body>${reportInfo}${ledgerTable}${totalsSummary}</body></html>`);
                printWin.document.close();
                printWin.print();
            });

            const supplierPDFBtn = document.getElementById('exportSupplierPDF');
            if (supplierPDFBtn) {
                supplierPDFBtn.addEventListener('click', function() {
                    const { jsPDF } = window.jspdf;
                    const doc = new jsPDF('landscape');
                    doc.text("Supplier-wise Closing Balances", 14, 15);
                    doc.autoTable({
                        html: '#supplier-summary table',
                        startY: 25,
                        theme: 'grid'
                    });
                    doc.save('Supplier_Summary.pdf');
                });
            }

            const supplierPrintBtn = document.getElementById('printSupplierSummary');
            if (supplierPrintBtn) {
                supplierPrintBtn.addEventListener('click', function() {
                    const content = document.getElementById('ledger-section').innerHTML;
                    const printWin = window.open('', '_blank');
                    const supplierSummary = document.getElementById('supplier-summary').outerHTML;
                    const reportInfo = document.querySelector('.border.rounded-3.p-3.mb-4.bg-light.shadow-sm').outerHTML;
                    printWin.document.write(`
                        <html><head><title>Supplier Summary</title>
                        <style>
                            body { font-family: Arial, sans-serif; }
                            table { width: 100%; border-collapse: collapse; }
                            th, td { border: 1px solid #ddd; padding: 8px; text-align: center; }
                            th { background-color: #007bff; color: white; }
                            .table-primary { background-color: #007bff; color: white; }
                        </style></head>
                        <body>${reportInfo}${supplierSummary}</body></html>`);
                    printWin.document.close();
                    printWin.print();
                });
            }
        </script>
    </div>
@endsection