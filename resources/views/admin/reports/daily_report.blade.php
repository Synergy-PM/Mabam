@extends('admin.layouts.shared')
@section('title', 'Daily Transaction Report')
@section('header-title', 'Daily Transaction Report')

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
                    <div>
                        <h5 class="mb-0 text-white">
                            <i class="fas fa-calendar-day me-2"></i> Daily Transaction Report
                        </h5>
                        <div class="mt-2">
                            @if($selectedSupplier)
                                <span class="badge bg-light text-dark me-1">Supplier: {{ $selectedSupplier->supplier_name ?? 'N/A' }}</span>
                            @endif
                            @if($selectedDealer)
                                <span class="badge bg-light text-dark me-1">Dealer: {{ $selectedDealer->dealer_name ?? 'N/A' }}</span>
                            @endif
                            @if($startDate)
                                <span class="badge bg-light text-dark">Date: {{ \Carbon\Carbon::parse($startDate)->format('d M, Y') }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <button id="printLedger" class="btn btn-light btn-sm shadow-sm me-2">
                            <i class="fas fa-print text-primary me-1"></i> Print
                        </button>
                        <button id="exportPDF" class="btn btn-light btn-sm shadow-sm me-2">
                            <i class="fas fa-file-pdf text-danger me-1"></i> Export PDF
                        </button>
                        <a href="{{ route('daily.report.filter') }}" class="btn btn-outline-light btn-sm">Back to Filter</a>
                    </div>
                </div>

                {{-- Card Body --}}
                <div class="card-body bg-white">
                    {{-- Report Info --}}
                    <div class="border rounded-3 p-3 mb-4 bg-light shadow-sm">
                        <h6 class="fw-bold text-secondary mb-3">
                            <i class="fas fa-info-circle me-2"></i> Report Information
                        </h6>
                        <div class="row">
                            <div class="col-md-3">
                                <p class="mb-1"><strong>Generated On:</strong> {{ \Carbon\Carbon::now()->format('d M, Y') }}</p>
                            </div>
                            <div class="col-md-3">
                                <p class="mb-1"><strong>Total Transactions:</strong> {{ $reports->count() }}</p>
                            </div>
                            <div class="col-md-3">
                                <p class="mb-1"><strong>Total Credit:</strong> {{ number_format($totalCredit, 2) }} PKR</p>
                            </div>
                            <div class="col-md-3">
                                <p class="mb-1"><strong>Total Debit:</strong> {{ number_format($totalDebit, 2) }} PKR</p>
                            </div>
                            @if($startDate)
                                <div class="col-md-6 mt-2">
                                    <p class="mb-1"><strong>Date Range:</strong> {{ \Carbon\Carbon::parse($startDate)->format('d M, Y') }} @if($endDate && $endDate != $startDate) to {{ \Carbon\Carbon::parse($endDate)->format('d M, Y') }} @endif</p>
                                </div>
                            @endif
                            <div class="col-md-6 mt-2">
                                <p class="mb-1"><strong>Net Balance:</strong> <span class="{{ $netBalance >= 0 ? 'text-success' : 'text-danger' }}"> {{ number_format($netBalance, 2) }} PKR </span></p>
                            </div>
                        </div>
                    </div>

                    {{-- Table --}}
                    <div class="table-responsive shadow-sm rounded-3" style="max-height: 65vh; overflow-y: auto;">
                        <table id="dailyReportTable" class="table table-bordered table-hover align-middle text-center mb-0">
                            <thead class="table-primary">
                                <tr>
                                    <th>S.No</th>
                                    <th>Type</th>
                                    <th>Name/Description</th>
                                    <th>Credit (PKR)</th>
                                    <th>Debit (PKR)</th>
                                    <th>Transaction Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $serial = 1; @endphp
                                @forelse($reports as $report)
                                    <tr>
                                        <td>{{ $serial++ }}</td>
                                        <td>{{ $report['detected_type'] ?? 'N/A' }}</td>
                                        <td>
                                            {{ $report['name'] ?? 'N/A' }}<br>
                                            @if(isset($report['description']))
                                                <small class="text-muted">{{ $report['description'] }}</small>
                                            @endif
                                        </td>
                                        <td class="{{ ($report['amount'] ?? 0) > 100000 ? 'text-danger fw-bold' : '' }} text-success">
                                            {{ $report['is_credit'] ? number_format($report['amount'], 2) : '-' }}
                                        </td>
                                        <td class="{{ ($report['amount'] ?? 0) > 100000 ? 'text-danger fw-bold' : '' }} text-danger">
                                            {{ !$report['is_credit'] ? number_format($report['amount'], 2) : '-' }}
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($report['transaction_date'])->format('d M, Y') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">
                                            <i class="fas fa-folder-open me-2"></i> No transactions found for the selected date. Try adjusting the filters.
                                        </td>
                                    </tr>
                                @endforelse

                                {{-- Grand Totals --}}
                                <tr class="table-secondary fw-bold">
                                    <td colspan="3" class="text-end">Grand Total Credit</td>
                                    <td class="text-success">{{ number_format($totalCredit, 2) }}</td>
                                    <td>-</td>
                                    <td></td>
                                </tr>
                                <tr class="table-secondary fw-bold">
                                    <td colspan="3" class="text-end">Grand Total Debit</td>
                                    <td>-</td>
                                    <td class="text-danger">{{ number_format($totalDebit, 2) }}</td>
                                    <td></td>
                                </tr>
                                <tr class="table-dark fw-bold">
                                    <td colspan="3" class="text-end">Net Balance</td>
                                    <td colspan="2" class="{{ $netBalance >= 0 ? 'text-success' : 'text-danger' }} fs-6">
                                        {{ number_format($netBalance, 2) }} PKR
                                    </td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Styles --}}
    <style>
        .table-responsive { max-height: 65vh; overflow-y: auto; }
        @media (max-width: 767.98px) { .card .card-body h5 { font-size: 1rem; } }
    </style>

    {{-- JS: Print & PDF --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.13/jspdf.plugin.autotable.min.js"></script>
    <script>
        // Print Table
        document.getElementById('printLedger').addEventListener('click', function () {
            const table = document.getElementById('dailyReportTable');
            const style = `
                <style>
                    table { width: 100%; border-collapse: collapse; }
                    table, th, td { border: 1px solid #333; }
                    th, td { padding: 6px; text-align: center; }
                    th { background-color: #007bff; color: white; }
                    .text-success { color: green; }
                    .text-danger { color: red; }
                    .fw-bold { font-weight: bold; }
                </style>
            `;
            const printWindow = window.open('', '', 'height=700,width=900');
            printWindow.document.write('<html><head><title>Daily Transaction Report</title>');
            printWindow.document.write(style);
            printWindow.document.write('</head><body>');
            printWindow.document.write(`<h3 style="text-align:center;">HK Group of Companies</h3>`);
            printWindow.document.write(`<h4 style="text-align:center;">Daily Transaction Report</h4>`);

            // Optional: Filter info
            @if($selectedSupplier || $selectedDealer || $startDate)
                let filterHtml = '<p style="text-align:center;">';
                @if($selectedSupplier)
                    filterHtml += 'Supplier: {{ $selectedSupplier->supplier_name ?? "N/A" }} | ';
                @endif
                @if($selectedDealer)
                    filterHtml += 'Dealer: {{ $selectedDealer->dealer_name ?? "N/A" }} | ';
                @endif
                @if($startDate)
                    filterHtml += 'Date: {{ \Carbon\Carbon::parse($startDate)->format("d M, Y") }}';
                @endif
                filterHtml += '</p>';
                printWindow.document.write(filterHtml);
            @endif

            printWindow.document.write(table.outerHTML);
            printWindow.document.write('</body></html>');
            printWindow.document.close();
            printWindow.focus();
            printWindow.print();
        });

        // Export PDF
        document.getElementById('exportPDF').addEventListener('click', function () {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF('p', 'pt', 'a4');
            const companyName = "MK Traders";
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
                filterInfo += `Supplier: {{ $selectedSupplier->supplier_name ?? 'N/A' }} | `;
            @endif
            @if($selectedDealer)
                filterInfo += `Dealer: {{ $selectedDealer->dealer_name ?? 'N/A' }} | `;
            @endif
            @if($startDate)
                filterInfo += `Date: {{ \Carbon\Carbon::parse($startDate)->format('d M, Y') }}`;
            @endif
            doc.text(`Generated On: ${today}`, 40, 78);
            if(filterInfo) doc.text(filterInfo, 40, 90);

            doc.autoTable({
                html: '#dailyReportTable',
                startY: 100 + (filterInfo ? 15 : 0),
                theme: 'grid',
                headStyles: { fillColor: [41, 128, 185], textColor: 255, fontStyle: 'bold' },
                styles: { fontSize: 8, cellPadding: 4, halign: 'center' },
                margin: { left: 40, right: 40 },
                columnStyles: {
                    0: { cellWidth: 30 },
                    1: { cellWidth: 60 },
                    2: { cellWidth: 100 },
                    3: { cellWidth: 60 },
                    4: { cellWidth: 60 },
                    5: { cellWidth: 60 },
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
            doc.text(`Total Credit: {{ number_format($totalCredit, 2) }} PKR`, 40, finalY + 14);
            doc.text(`Total Debit: {{ number_format($totalDebit, 2) }} PKR`, 40, finalY + 24);
            doc.text(`Net Balance: {{ number_format($netBalance, 2) }} PKR`, 40, finalY + 34);
            @if(isset($totalExpense) && $totalExpense > 0)
                doc.text(`Expenses: {{ number_format($totalExpense, 2) }} PKR`, 40, finalY + 44);
            @endif

            const filename = `Daily_Transaction_Report_{{ \Carbon\Carbon::now()->format('d_M_Y') }}.pdf`;
            doc.save(filename);
        });
    </script>
</div>
@endsection
