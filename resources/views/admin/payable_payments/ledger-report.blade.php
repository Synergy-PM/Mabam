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
                    <button id="exportPDF" class="btn btn-light btn-sm shadow-sm">
                        <i class="fas fa-file-pdf text-danger me-1"></i> Export PDF
                    </button>
                </div>

                {{-- Card Body --}}
                <div class="card-body bg-white">
                    
                    {{-- Report Information --}}
                    <div class="border rounded-3 p-3 mb-4 bg-light shadow-sm">
                        <h6 class="fw-bold text-secondary mb-3">
                            <i class="fas fa-info-circle me-2"></i> Report Information
                        </h6>
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Supplier:</strong> {{ $supplier->supplier_name ?? 'N/A' }}</p>
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
                        $openingBalance = $openBalance ?? 0; // Original opening balance
                        $balance = $openingBalance; // Running balance
                        $totalDebit = 0;
                        $totalCredit = 0;
                    @endphp
                    <p class="d-flex justify-content-between align-items-center mb-3" style="font-size: 16px;">
                        <strong>Opening Balance:</strong> {{ number_format($openingBalance, 2) }}
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
                                    <th>Proof of Payment</th>
                                    <th>Date</th>
                                    <th>Notes</th>
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
                                        <td>
                                            @if ($payment->proof_of_payment)
                                                <a href="{{ Storage::url($payment->proof_of_payment) }}" target="_blank">View</a>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($payment->transaction_date)->format('d M, Y') }}</td>
                                        <td>{{ $payment->notes ?? '-' }}</td>
                                        <td>{{ number_format($balance, 2) }}</td>
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
                                    <td colspan="5"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    {{-- Totals Summary --}}
                    <div class="mt-3">
                        <p><strong>Total Debit:</strong> {{ number_format($totalDebit, 2) }}</p>
                        <p><strong>Total Credit:</strong> {{ number_format($totalCredit, 2) }}</p>
                        <p><strong>Closing Balance:</strong> {{ number_format($balance, 2) }}</p>
                    </div>

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
            const doc = new jsPDF();

            const printedOnDateTime = "{{ \Carbon\Carbon::now()->format('d M, Y h:i A') }}";
            const pageWidth = doc.internal.pageSize.width;
            const textWidth = doc.getTextWidth(`PRINTED ON: ${printedOnDateTime}`);
            doc.setFontSize(10);
            doc.setTextColor(128, 128, 128);
            doc.text(`PRINTED ON: ${printedOnDateTime}`, pageWidth - textWidth - 14, 10);

            doc.setTextColor(0, 0, 0);
            doc.setFontSize(16);
            doc.text("Payable Ledger Report", 14, 20);

            doc.setFontSize(11);
            doc.text(`Supplier: {{ $selectedPayable->supplier->supplier_name ?? 'N/A' }}`, 14, 30);
            doc.text(`Period: {{ $startDate ? \Carbon\Carbon::parse($startDate)->format('d M, Y') : '---' }} - {{ $endDate ? \Carbon\Carbon::parse($endDate)->format('d M, Y') : '---' }}`, 14, 36);
            doc.text(`Opening Balance: {{ number_format($openingBalance, 2) }}`, 14, 42);

            const table = document.querySelector("#ledgerTable");
            doc.autoTable({ 
                html: table, 
                startY: 50, 
                theme: 'grid', 
                headStyles: { fillColor: [41, 128, 185] }, 
                styles: { fontSize: 8 },
                columnStyles: {
                    5: { cellWidth: 30 }, // Proof of Payment
                    7: { cellWidth: 40 }  // Notes
                }
            });

            const finalY = doc.lastAutoTable.finalY || 50;
            doc.text(`Total Debit: {{ number_format($totalDebit, 2) }}`, 14, finalY + 10);
            doc.text(`Total Credit: {{ number_format($totalCredit, 2) }}`, 14, finalY + 16);
            doc.text(`Closing Balance: {{ number_format($balance, 2) }}`, 14, finalY + 22);

            doc.save(`Ledger_Report_{{ \Carbon\Carbon::now()->format('d_M_Y') }}.pdf`);
        });
    </script>
</div>
@endsection
