@extends('admin.layouts.shared')
@section('title', 'Ledger Report')
@section('header-title', 'Ledger Report')

@section('content')
<div class="container-fluid">
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
                <div class="card-header d-flex justify-content-between align-items-center bg-gradient bg-primary text-white p-3">
                    <h5 class="mb-0 text-white">
                        <i class="fas fa-file-invoice me-2"></i> Ledger Report
                    </h5>
                    <div>
                        <a href="{{ route('receivable-payments.ledger-report-filter') }}" class="btn btn-light btn-sm shadow-sm me-2">
                            <i class="fas fa-filter me-1"></i> Back to Filter
                        </a>
                        <button id="exportPDF" class="btn btn-light btn-sm shadow-sm">
                            <i class="fas fa-file-pdf text-danger me-1"></i> Export PDF
                        </button>
                    </div>
                </div>
                <div class="card-body bg-white">
                    <div class="border rounded-3 p-3 mb-4 bg-light shadow-sm">
                        <h6 class="fw-bold text-secondary mb-3">
                            <i class="fas fa-info-circle me-2"></i> Report Information
                        </h6>
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Dealer:</strong> {{ $selectedDealer->dealer_name ?? 'N/A' }}</p>
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

                    @if($transactions->isNotEmpty())
                        <div class="table-responsive shadow-sm rounded-3 mb-4">
                            <table id="ledgerTable" class="table table-bordered table-hover align-middle text-center mb-0">
                                <thead class="table-primary">
                                    <tr>
                                        <th>S.No</th>
                                        <th>Dealer</th>
                                        <th>Date</th>
                                        <th>Credit</th>
                                        <th>Debit</th>
                                        <th>Payment Mode</th>
                                        <th>Freight</th>
                                        <th>Balance</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $runningBalance = 0;
                                    @endphp
                                    @foreach($transactions as $index => $transaction)
                                        @php
                                            $runningBalance += $transaction['is_receivable'] ? $transaction['amount'] : -$transaction['amount'];
                                        @endphp
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $transaction['dealer']->dealer_name ?? 'N/A' }}</td>
                                            <td>
                                                {{ $transaction['transaction_date']
                                                    ? \Carbon\Carbon::parse($transaction['transaction_date'])->format('d M, Y')
                                                    : 'N/A' }}
                                            </td>
                                            <td>{{ $transaction['is_receivable'] ? '-' : number_format($transaction['amount'], 2) }}</td>
                                            <td>{{ $transaction['is_receivable'] ? number_format($transaction['amount'], 2) : '-' }}</td>
                                            <td>{{ ucfirst($transaction['payment_type']) }}</td>
                                            <td>{{ number_format($transaction['freight'], 2) }}</td>
                                            <td>{{ number_format($runningBalance, 2) }}</td>
                                        </tr>
                                    @endforeach
                                    <tr class="table-secondary">
                                        <td colspan="3"><strong>Total</strong></td>
                                        <td><strong>{{ number_format($totalCreditAmount, 2) }}</strong></td>
                                        <td><strong>{{ number_format($totalDebitAmount, 2) }}</strong></td>
                                        <td></td>
                                        <td><strong>{{ number_format($totalFreight, 2) }}</strong></td>
                                        <td><strong>{{ number_format($runningBalance, 2) }}</strong></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            <p><strong>Total Credit Amount:</strong> {{ number_format($totalCreditAmount, 2) }}</p>
                            <p><strong>Total Debit Amount:</strong> {{ number_format($totalDebitAmount, 2) }}</p>
                            <p><strong>Total Freight:</strong> {{ number_format($totalFreight, 2) }}</p>
                            <p><strong>Final Balance:</strong> {{ number_format($runningBalance, 2) }}</p>
                        </div>
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-folder-open me-2"></i> No transactions found
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if($transactions->isNotEmpty())
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.13/jspdf.plugin.autotable.min.js"></script>
        <script>
            document.getElementById('exportPDF').addEventListener('click', function() {
                const { jsPDF } = window.jspdf;
                const doc = new jsPDF();
                const printedOnDateTime = "{{ \Carbon\Carbon::now()->format('d M, Y h:i A') }}";
                const pageWidth = doc.internal.pageSize.width;
                const textWidth = doc.getTextWidth(`PRINTED ON: ${printedOnDateTime}`);
                doc.setFontSize(10);
                doc.setTextColor(128, 128, 128);
                doc.text(`PRINTED ON: ${printedOnDateTime}`, pageWidth - textWidth - 14, 10);
                doc.setTextColor(0, 0, 0);
                doc.setFontSize(16);
                doc.text("Ledger Report", 14, 20);
                doc.setFontSize(11);
                doc.text(`Dealer: {{ $selectedDealer->dealer_name ?? 'N/A' }}`, 14, 30);
                doc.text(
                    `Period: {{ $startDate ? \Carbon\Carbon::parse($startDate)->format('d M, Y') : 'All' }} - {{ $endDate ? \Carbon\Carbon::parse($endDate)->format('d M, Y') : 'All' }}`,
                    14, 36);

                const ledgerTable = document.querySelector("#ledgerTable");
                doc.autoTable({
                    html: ledgerTable,
                    startY: 44,
                    theme: 'grid',
                    headStyles: { fillColor: [41, 128, 185] },
                    styles: { fontSize: 8 },
                    columnStyles: {
                        0: { cellWidth: 10 }, // S.No
                        1: { cellWidth: 30 }, // Dealer
                        2: { cellWidth: 25 }, // Date
                        3: { cellWidth: 25 }, // Credit
                        4: { cellWidth: 25 }, // Debit
                        5: { cellWidth: 25 }, // Payment Mode
                        6: { cellWidth: 20 }, // Freight
                        7: { cellWidth: 25 }  // Balance
                    }
                });

                let finalY = doc.lastAutoTable.finalY || 44;
                doc.setFontSize(10);
                doc.text(`Total Credit Amount: {{ number_format($totalCreditAmount, 2) }}`, 14, finalY + 10);
                doc.text(`Total Debit Amount: {{ number_format($totalDebitAmount, 2) }}`, 14, finalY + 16);
                doc.text(`Total Freight: {{ number_format($totalFreight, 2) }}`, 14, finalY + 22);
                doc.text(`Final Balance: {{ number_format($runningBalance, 2) }}`, 14, finalY + 28);

                doc.save(`Ledger_Report_{{ \Carbon\Carbon::now()->format('d_M_Y') }}.pdf`);
            });
        </script>
    @endif
</div>
@endsection