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
                            <i class="fas fa-print text-primary me-1"></i> Print
                        </button>
                        <button id="exportPDF" class="btn btn-light btn-sm shadow-sm">
                            <i class="fas fa-file-pdf text-danger me-1"></i> Export PDF
                        </button>
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
                                <p class="mb-1"><strong>Supplier:</strong> {{ $selectedSupplier->supplier_name ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1">
                                    <strong>Period:</strong> 
                                    {{ $startDate ? \Carbon\Carbon::parse($startDate)->format('d M, Y') : '---' }} → 
                                    {{ $endDate ? \Carbon\Carbon::parse($endDate)->format('d M, Y') : '---' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Opening Balance --}}
                    @php
                        $openingBalance = $openBalance ?? 0; 
                        $balance = $openingBalance; 
                        $totalDebit = $openingBalance;
                        $totalCredit = 0;
                    @endphp
                    <p class="d-flex justify-content-between align-items-center mb-3" style="font-size: 16px;">
                        <strong>Opening Balance (Debit):</strong> {{ number_format($openingBalance, 2) }}
                    </p>

                    {{-- Results Table --}}
                    <div class="table-responsive shadow-sm rounded-3">
                        <table id="ledgerTable" class="table table-bordered table-hover align-middle text-center mb-0">
                            <thead class="table-primary">
                                <tr>
                                    <th>S.No</th>
                                    <th>Supplier</th>
                                    <th>Credit</th>
                                    <th>Debit</th>
                                    <th>Payment Mode</th>
                                    <th>Date</th>
                                    <th>Balance</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($payments as $payment)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $payment->supplier->supplier_name ?? 'N/A' }}</td>

                                        {{-- Credit Column --}}
                                        <td>
                                            @if($payment->transaction_type == 'credit')
                                                {{ number_format($payment->amount, 2) }}
                                                @php $totalCredit += $payment->amount; $balance += $payment->amount; @endphp
                                            @else
                                                -
                                            @endif
                                        </td>

                                        {{-- Debit Column --}}
                                        <td>
                                            @if($payment->transaction_type == 'debit')
                                                {{ number_format($payment->amount, 2) }}
                                                @php $totalDebit += $payment->amount; $balance -= $payment->amount; @endphp
                                            @else
                                                -
                                            @endif
                                        </td>

                                        <td>{{ ucfirst($payment->payment_mode) }}</td>
                                        <td>{{ \Carbon\Carbon::parse($payment->transaction_date)->format('d M, Y') }}</td>
                                        <td>{{ number_format($totalCredit - $totalDebit, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center text-muted py-4">
                                            <i class="fas fa-folder-open me-2"></i> No records found
                                        </td>
                                    </tr>
                                @endforelse

                                {{-- Totals Row --}}
                                <tr class="table-secondary">
                                    <td colspan="2"><strong>Total</strong></td>
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

                    {{-- Supplier-wise Closing Balances --}}
                    @if(!$selectedSupplier && count($supplierSummaries))
                        <div class="mt-5">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="fw-semibold mb-0">Supplier-wise Closing Balances</h5>
                                <div>
                                    <button id="printSupplierSummary" class="btn btn-outline-primary btn-sm me-2">
                                        <i class="fas fa-print me-1"></i> Print
                                    </button>
                                    <button id="exportSupplierPDF" class="btn btn-outline-danger btn-sm">
                                        <i class="fas fa-file-pdf me-1"></i> PDF
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
                                        @foreach($supplierSummaries as $index => $supplier)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $supplier['supplier_name'] }}</td>
                                                <td>{{ $supplier['tons'] ?? '-' }}</td>
                                                <td><strong>{{ number_format($supplier['closing_balance'], 2) }}</strong></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                </div> {{-- End Card Body --}}
            </div>
        </div>
    </div>

    {{-- PDF Export Scripts --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.13/jspdf.plugin.autotable.min.js"></script>

    <script>
        document.getElementById('exportPDF').addEventListener('click', function () {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF('landscape');
            doc.text("Payable Ledger Report", 14, 15);
            doc.autoTable({ html: '#ledgerTable', startY: 25, theme: 'grid' });
            doc.save('Ledger_Report.pdf');
        });

        document.getElementById('printLedger').addEventListener('click', function () {
            const content = document.getElementById('ledger-section').innerHTML;
            const printWin = window.open('', '_blank');
            printWin.document.write(`<html><head><title>Ledger Report</title></head><body>${content}</body></html>`);
            printWin.document.close();
            printWin.print();
        });

        const supplierPDFBtn = document.getElementById('exportSupplierPDF');
        if (supplierPDFBtn) {
            supplierPDFBtn.addEventListener('click', function () {
                const { jsPDF } = window.jspdf;
                const doc = new jsPDF('landscape');
                doc.text("Supplier-wise Closing Balances", 14, 15);
                doc.autoTable({ html: '#supplier-summary table', startY: 25, theme: 'grid' });
                doc.save('Supplier_Summary.pdf');
            });
        }

        const supplierPrintBtn = document.getElementById('printSupplierSummary');
        if (supplierPrintBtn) {
            supplierPrintBtn.addEventListener('click', function () {
                const content = document.getElementById('supplier-summary').innerHTML;
                const printWin = window.open('', '_blank');
                printWin.document.write(`<html><head><title>Supplier Summary</title></head><body>${content}</body></html>`);
                printWin.document.close();
                printWin.print();
            });
        }
    </script>
</div>
@endsection