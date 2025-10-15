@extends('admin.layouts.shared')
@section('title', 'Daily Transaction Report')
@section('header-title', 'Daily Transaction Report')

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
                    <div>
                        <h5 class="mb-0 text-white">
                            <i class="fas fa-calendar-day me-2"></i> Daily Transaction Report
                        </h5>
                        <div class="mt-2">
                            @if($selectedSupplier)
                                <span class="badge bg-light text-dark me-1">Supplier: {{ $selectedSupplier->name }}</span>
                            @endif
                            @if($selectedDealer)
                                <span class="badge bg-light text-dark me-1">Dealer: {{ $selectedDealer->dealer_name }}</span>
                            @endif
                            @if($startDate)
                                <span class="badge bg-light text-dark">Date: {{ \Carbon\Carbon::parse($startDate)->format('d M, Y') }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <button id="exportPDF" class="btn btn-light btn-sm shadow-sm me-2">
                            <i class="fas fa-file-pdf text-danger me-1"></i> Export PDF
                        </button>
                        <a href="{{ route('daily.report.filter') }}" class="btn btn-outline-light btn-sm">Back to Filter</a>
                    </div>
                </div>
                <div class="card-body bg-white">
                    <div class="border rounded-3 p-3 mb-4 bg-light shadow-sm">
                        <h6 class="fw-bold text-secondary mb-3">
                            <i class="fas fa-info-circle me-2"></i> Report Information
                        </h6>
                        <div class="row">
                            <div class="col-md-4">
                                <p class="mb-1"><strong>Generated On:</strong> {{ \Carbon\Carbon::now()->format('d M, Y') }}</p>
                            </div>
                            <div class="col-md-4">
                                <p class="mb-1"><strong>Total Transactions:</strong> {{ $reports->count() }}</p>
                            </div>
                            <div class="col-md-4">
                                <p class="mb-1"><strong>Net Balance:</strong> {{ number_format($netBalance, 2) }} PKR</p>
                            </div>
                            @if($startDate)
                                <div class="col-md-6 mt-2">
                                    <p class="mb-1"><strong>Date:</strong> {{ \Carbon\Carbon::parse($startDate)->format('d M, Y') }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="table-responsive shadow-sm rounded-3">
                        <table id="dailyReportTable" class="table table-bordered table-hover align-middle text-center mb-0">
                            <thead class="table-primary">
                                <tr>
                                    <th>S.No</th>
                                    <th>Type</th>
                                    <th>Name/Description</th>
                                    <th>Credit (PKR)</th>
                                    <th>Debit (PKR)</th>
                                    <th>Transaction Date</th>
                                    <!-- <th>Balance</th> -->
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $serial = 1;
                                    $runningBalance = 0;
                                @endphp
                                @forelse($reports as $report)
                                    <tr>
                                        <td>{{ $serial++ }}</td>
                                       <td>{{ $report['detected_type'] ?? 'N/A' }}</td>
                                        <td>{{ $report['name'] }}</td>
                                        <td class="{{ $report['amount'] > 100000 ? 'text-danger fw-bold' : '' }}">
                                            {{ $report['is_credit'] ? number_format($report['amount'], 2) : '-' }}
                                        </td>
                                        <td class="{{ $report['amount'] > 100000 ? 'text-danger fw-bold' : '' }}">
                                            {{ !$report['is_credit'] ? number_format($report['amount'], 2) : '-' }}
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($report['transaction_date'])->format('d M, Y') }}</td>
                                        <!-- <td>
                                            @php
                                                $runningBalance += $report['is_credit'] ? -$report['amount'] : $report['amount'];
                                            @endphp
                                            {{ number_format($runningBalance, 2) }}
                                        </td> -->
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            <i class="fas fa-folder-open me-2"></i> No transactions found for the selected date. Try adjusting the filters.
                                        </td>
                                    </tr>
                                @endforelse
                                <tr class="table-secondary fw-bold">
                                    <td colspan="3" class="text-end">Grand Total</td>
                                    <td>{{ number_format($totalPayable + $totalReceivablePayment, 2) }}</td>
                                    <td>{{ number_format($totalReceivable, 2) }}</td>
                                    <td></td>
                                    <!-- <td>{{ number_format($runningBalance, 2) }}</td> -->
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <!-- <div class="row mt-4 g-3">
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h6 class="mb-0">Total Payable</h6>
                                    <h5 class="mt-1">{{ number_format($totalPayable, 2) }} PKR</h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <h6 class="mb-0">Total Receivable</h6>
                                    <h5 class="mt-1">{{ number_format($totalReceivable, 2) }} PKR</h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-dark">
                                <div class="card-body text-center">
                                    <h6 class="mb-0">Total Receivable Payment</h6>
                                    <h5 class="mt-1">{{ number_format($totalReceivablePayment, 2) }} PKR</h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-secondary text-white">
                                <div class="card-body text-center">
                                    <h6 class="mb-0">Net Balance</h6>
                                    <h5 class="mt-1">{{ number_format($netBalance, 2) }} PKR</h5>
                                </div>
                            </div>
                        </div>
                    </div> -->
                </div>
            </div>
        </div>
    </div>
    <style>
        .table-responsive {
            max-height: 65vh;
            overflow-y: auto;
        }
        @media (max-width: 767.98px) {
            .card .card-body h5 { font-size: 1rem; }
        }
    </style>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.13/jspdf.plugin.autotable.min.js"></script>
    <script>
        document.getElementById('exportPDF').addEventListener('click', function () {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF('p', 'pt', 'a4');
            const companyName = "HK Group of Companies";
            const title = "Daily Transaction Report";
            const today = "{{ \Carbon\Carbon::now()->format('d M, Y') }}";
            doc.setFontSize(12);
            doc.setFont('helvetica', 'bold');
            doc.text(companyName, 40, 40);
            doc.setFontSize(14);
            doc.text(title, 40, 60);
            doc.setFontSize(10);
            doc.setFont('helvetica', 'normal');
            let filterInfo = '';
            @if($selectedSupplier)
                filterInfo += `Supplier: {{ $selectedSupplier->name }} | `;
            @endif
            @if($selectedDealer)
                filterInfo += `Dealer: {{ $selectedDealer->dealer_name }} | `;
            @endif
            @if($startDate)
                filterInfo += `Date: {{ \Carbon\Carbon::parse($startDate)->format('d M, Y') }}`;
            @endif
            doc.text(`Generated On: ${today}`, 40, 78);
            doc.text(filterInfo, 40, 90);
            doc.autoTable({
                html: '#dailyReportTable',
                startY: 100,
                theme: 'grid',
                headStyles: { fillColor: [41, 128, 185], textColor: 255, fontStyle: 'bold' },
                styles: { fontSize: 8, cellPadding: 4 },
                margin: { left: 40, right: 40 },
                columnStyles: {
                    0: { cellWidth: 30 }, // S.No
                    1: { cellWidth: 60 }, // Type
                    2: { cellWidth: 100 }, // Name/Description
                    3: { cellWidth: 60 }, // Credit
                    4: { cellWidth: 60 }, // Debit
                    5: { cellWidth: 60 }, // Transaction Date
                    6: { cellWidth: 60 }, // Balance
                },
                didDrawPage: function (data) {
                    const pageHeight = doc.internal.pageSize.getHeight();
                    doc.setFontSize(9);
                    const pageNumber = doc.internal.getCurrentPageInfo().pageNumber;
                    doc.text(`Generated on: ${today} | Page ${pageNumber}`, 40, pageHeight - 30);
                }
            });
            const finalY = doc.lastAutoTable ? doc.lastAutoTable.finalY + 20 : 120;
            doc.setFontSize(11);
            doc.setFont('helvetica', 'bold');
            doc.text("Grand Totals:", 40, finalY);
            doc.setFont('helvetica', 'normal');
            doc.setFontSize(10);
            doc.text(`Payable: {{ number_format($totalPayable, 2) }} PKR`, 40, finalY + 14);
            doc.text(`Receivable: {{ number_format($totalReceivable, 2) }} PKR`, 40, finalY + 24);
            doc.text(`Receivable Payment: {{ number_format($totalReceivablePayment, 2) }} PKR`, 40, finalY + 34);
            const filename = `Daily_Transaction_Report_{{ \Carbon\Carbon::now()->format('d_M_Y') }}.pdf`;
            doc.save(filename);
        });
    </script>
</div>
@endsection
