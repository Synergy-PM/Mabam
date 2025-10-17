@extends('admin.layouts.shared')
@section('title', 'Bilti Report Results')
@section('header-title', 'Bilti Report Results')
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
                    <div
                        class="card-header d-flex justify-content-between align-items-center bg-gradient bg-primary text-white p-3">
                        <h5 class="mb-0 text-white">
                            <i class="fas fa-file-invoice me-2"></i> Bilti Report (Debit Transactions)
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
                                    <p class="mb-1"><strong>Bilti Number:</strong> {{ request('bilti_no') ?? 'All' }}</p>
                                    <p class="mb-1"><strong>Dealer:</strong> {{ $dealer->dealer_name ?? 'All' }}</p>
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
                            <table id="biltiTable" class="table table-bordered table-hover align-middle text-center mb-0">
                                <thead class="table-primary">
                                    <tr>
                                        <th>S.No</th>
                                        <th>Bilti No</th>
                                        <th>Supplier</th>
                                        <th>Dealer</th>
                                        <th>Date</th>
                                        <th>Bags</th>
                                        <th>Tons</th>
                                        <th>Rate</th>
                                        <th>Total Amount</th>
                                        {{-- <th>Freight</th> --}}
                                        <th>Payment Type</th>
                                        <th>Proof of Payment</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($receivables as $receivable)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $receivable->bilti_no ?? '-' }}</td>
                                            <td>{{ $receivable->payable && $receivable->payable->supplier ? $receivable->payable->supplier->supplier_name : 'N/A' }}</td>
                                            <td>{{ $receivable->dealer->dealer_name ?? 'N/A' }}</td>
                                            <td>
                                                {{ $receivable->payable && $receivable->payable->transaction_date
                                                    ? \Carbon\Carbon::parse($receivable->payable->transaction_date)->format('d M, Y')
                                                    : 'N/A' }}
                                            </td>
                                            <td>{{ $receivable->bags ?? 0 }}</td>
                                            <td>{{ number_format($receivable->bags / 20, 2) }}</td>
                                            <td>{{ number_format($receivable->rate, 2) }}</td>
                                            <td>{{ number_format($receivable->bags * $receivable->rate, 2) }}</td>
                                            {{-- <td>{{ number_format($receivable->freight, 2) }}</td> --}}
                                            <td>{{ ucfirst($receivable->payment_type ?? 'N/A') }}</td>
                                            <td>
                                                @if ($receivable->proof_of_payment)
                                                    <a href="{{ Storage::url($receivable->proof_of_payment) }}"
                                                        target="_blank">View</a>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="12" class="text-center text-muted py-4">
                                                <i class="fas fa-folder-open me-2"></i> No debit transactions found
                                            </td>
                                        </tr>
                                    @endforelse
                                    {{-- Totals Row --}}
                                    @php
                                        $totalBags = $receivables->sum('bags');
                                        $totalTons = $totalBags / 20;
                                        $totalAmount = $receivables->sum(function ($receivable) {
                                            return $receivable->bags * $receivable->rate;
                                        });
                                        $totalFreight = $receivables->sum('freight');
                                    @endphp
                                    <tr class="table-secondary">
                                        <td colspan="5"><strong>Total</strong></td>
                                        <td><strong>{{ $totalBags }}</strong></td>
                                        <td><strong>{{ number_format($totalTons, 2) }}</strong></td>
                                        <td></td>
                                        <td><strong>{{ number_format($totalAmount, 2) }}</strong></td>
                                        <td><strong>{{ number_format($totalFreight, 2) }}</strong></td>
                                        <td colspan="2"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        {{-- Totals Summary --}}
                        <div class="mt-3">
                            <p><strong>Total Bags:</strong> {{ $totalBags }}</p>
                            <p><strong>Total Tons:</strong> {{ number_format($totalTons, 2) }}</p>
                            <p><strong>Total Amount:</strong> {{ number_format($totalAmount, 2) }}</p>
                            <p><strong>Total Freight:</strong> {{ number_format($totalFreight, 2) }}</p>
                        </div>
                    </div> {{-- End Card Body --}}
                </div>
            </div>
        </div>
        {{-- PDF Export Scripts --}}
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
                doc.text("Bilti Report (Debit Transactions)", 14, 20);
                doc.setFontSize(11);
                doc.text(`Bilti Number: {{ request('bilti_no') ?? 'All' }}`, 14, 30);
                doc.text(`Dealer: {{ $selectedDealer->dealer_name ?? 'All' }}`, 14, 36);
                doc.text(
                    `Period: {{ $startDate ? \Carbon\Carbon::parse($startDate)->format('d M, Y') : 'All' }} - {{ $endDate ? \Carbon\Carbon::parse($endDate)->format('d M, Y') : 'All' }}`,
                    14, 42);
                const table = document.querySelector("#biltiTable");
                doc.autoTable({
                    html: table,
                    startY: 50,
                    theme: 'grid',
                    headStyles: {
                        fillColor: [41, 128, 185]
                    },
                    styles: {
                        fontSize: 8
                    },
                    columnStyles: {
                        0: { cellWidth: 10 }, // S.No
                        1: { cellWidth: 20 }, // Bilti No
                        2: { cellWidth: 25 }, // Supplier
                        3: { cellWidth: 25 }, // Dealer
                        4: { cellWidth: 20 }, // Date
                        5: { cellWidth: 15 }, // Bags
                        6: { cellWidth: 15 }, // Tons
                        7: { cellWidth: 15 }, // Rate
                        8: { cellWidth: 20 }, // Total Amount
                        9: { cellWidth: 15 }, // Freight
                        10: { cellWidth: 20 }, // Payment Type
                        11: { cellWidth: 20 } // Proof of Payment
                    }
                });
                const finalY = doc.lastAutoTable.finalY || 50;
                doc.text(`Total Bags: {{ $totalBags }}`, 14, finalY + 10);
                doc.text(`Total Tons: {{ number_format($totalTons, 2) }}`, 14, finalY + 16);
                doc.text(`Total Amount: {{ number_format($totalAmount, 2) }}`, 14, finalY + 22);
                doc.text(`Total Freight: {{ number_format($totalFreight, 2) }}`, 14, finalY + 28);
                doc.save(`Bilti_Report_Debit_{{ \Carbon\Carbon::now()->format('d_M_Y') }}.pdf`);
            });
        </script>
    </div>
@endsection