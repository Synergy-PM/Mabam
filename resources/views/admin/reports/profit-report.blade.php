@extends('admin.layouts.shared')
@section('title', 'Profit Report')
@section('header-title', 'Profit Report')

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

                {{-- Header --}}
                <div class="card-header d-flex justify-content-between align-items-center bg-gradient bg-primary text-white p-3">
                    <h5 class="mb-0 text-white">
                        <i class="fas fa-chart-line me-2"></i> Profit Report
                    </h5>
                    <div>
                        <button id="printReport" class="btn btn-light btn-sm shadow-sm me-2">
                            <i class="fas fa-print text-primary me-1"></i> Print
                        </button>
                        <button id="exportPDF" class="btn btn-light btn-sm shadow-sm">
                            <i class="fas fa-file-pdf text-danger me-1"></i> Export PDF
                        </button>
                    </div>
                </div>

                {{-- Body --}}
                <div class="card-body bg-white">
                    {{-- Report Info --}}
                    <div class="border rounded-3 p-3 mb-4 bg-light shadow-sm">
                        <h6 class="fw-bold text-secondary mb-3">
                            <i class="fas fa-info-circle me-2"></i> Report Information
                        </h6>
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Report Type:</strong> {{ ucfirst($reportType) ?? 'All' }}</p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1">
                                    <strong>Generated On:</strong> {{ \Carbon\Carbon::now()->format('d M, Y h:i A') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Table --}}
                    <div class="table-responsive shadow-sm rounded-3">
                        <table id="profitTable" class="table table-bordered table-hover align-middle text-center mb-0">
                            <thead class="table-primary">
                                <tr>
                                    <th>{{ ucfirst($reportType) }}</th>
                                    <th>Total Income</th>
                                    <th>Total Expense</th>
                                    <th>Profit</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($report as $row)
                                    <tr>
                                        <td>{{ $row['label'] }}</td>
                                        <td>{{ number_format($row['income'], 2) }}</td>
                                        <td>{{ number_format($row['expense'], 2) }}</td>
                                        <td class="{{ $row['profit'] >= 0 ? 'text-success fw-bold' : 'text-danger fw-bold' }}">
                                            {{ number_format($row['profit'], 2) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">
                                            <i class="fas fa-folder-open me-2"></i> No data available
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Totals --}}
                    <div class="mt-4 border-top pt-3">
                        <p><strong>Total Income:</strong> {{ number_format($totalIncome, 2) }}</p>
                        <p><strong>Total Expense:</strong> {{ number_format($totalExpense, 2) }}</p>
                        <p>
                            <strong>Net Profit:</strong>
                            <span class="{{ $totalProfit >= 0 ? 'text-success fw-bold' : 'text-danger fw-bold' }}">
                                {{ number_format($totalProfit, 2) }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Print & PDF Scripts --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.13/jspdf.plugin.autotable.min.js"></script>
    <script>
        // Print Report
        document.getElementById('printReport').addEventListener('click', function () {
            const table = document.getElementById('profitTable');
            const style = `
                <style>
                    table { width: 100%; border-collapse: collapse; }
                    table, th, td { border: 1px solid #333; }
                    th, td { padding: 6px; text-align: center; }
                    th { background-color: #007bff; color: white; }
                </style>
            `;
            const printWindow = window.open('', '', 'height=700,width=900');
            printWindow.document.write('<html><head><title>Profit Report</title>');
            printWindow.document.write(style);
            printWindow.document.write('</head><body>');
            printWindow.document.write('<h3 style="text-align:center;">MK Traders</h3>');
            printWindow.document.write('<h4 style="text-align:center;">Profit Report</h4>');
            printWindow.document.write(table.outerHTML);
            printWindow.document.write('</body></html>');
            printWindow.document.close();
            printWindow.focus();
            printWindow.print();
        });

        // Export PDF
        document.getElementById('exportPDF').addEventListener('click', function() {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();
            const printedOnDateTime = "{{ \Carbon\Carbon::now()->format('d M, Y h:i A') }}";
            doc.setFontSize(12);
            doc.text("Profit Report", 14, 20);
            doc.setFontSize(10);
            doc.text(`Report Type: {{ ucfirst($reportType) ?? 'All' }}`, 14, 28);
            doc.text(`Generated On: ${printedOnDateTime}`, 14, 34);

            doc.autoTable({
                html: '#profitTable',
                startY: 45,
                theme: 'grid',
                headStyles: { fillColor: [41, 128, 185] },
                styles: { fontSize: 8 },
            });

            const finalY = doc.lastAutoTable.finalY || 45;
            doc.text(`Total Income: {{ number_format($totalIncome, 2) }}`, 14, finalY + 10);
            doc.text(`Total Expense: {{ number_format($totalExpense, 2) }}`, 14, finalY + 16);
            doc.text(`Net Profit: {{ number_format($totalProfit, 2) }}`, 14, finalY + 22);

            doc.save(`Profit_Report_{{ \Carbon\Carbon::now()->format('d_M_Y') }}.pdf`);
        });
    </script>
</div>
@endsection
