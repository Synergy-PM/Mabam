@extends('admin.layouts.shared')
@section('title', 'Dealer Summary Report')
@section('header-title', 'Dealer Summary Report')

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
                    <div>
                        <h5 class="mb-0 text-white">
                            <i class="fas fa-chart-line me-2"></i> Dealer Summary Report
                        </h5>
                        <div class="mt-2">
                            <span class="badge bg-light text-dark">Report Date: {{ \Carbon\Carbon::now()->format('d M, Y') }}</span>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <button id="printSummary" class="btn btn-light btn-sm shadow-sm me-2">
                            <i class="fas fa-print text-primary me-1"></i> Print
                        </button>
                        <button id="exportPDF" class="btn btn-light btn-sm shadow-sm me-2">
                            <i class="fas fa-file-pdf text-danger me-1"></i> Export PDF
                        </button>
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-light btn-sm">
                            Back
                        </a>
                    </div>
                </div>

                {{-- Body --}}
                <div class="card-body bg-white">

                    {{-- Info Box --}}
                    <div class="border rounded-3 p-3 mb-4 bg-light shadow-sm">
                        <h6 class="fw-bold text-secondary mb-3">
                            <i class="fas fa-info-circle me-2"></i> Report Summary
                        </h6>
                        <div class="row">
                            <div class="col-md-4">
                                <p class="mb-1"><strong>Generated On:</strong> {{ \Carbon\Carbon::now()->format('d M, Y') }}</p>
                            </div>
                            <div class="col-md-4">
                                <p class="mb-1"><strong>Total Dealers:</strong> {{ $dealerSummaries->count() }}</p>
                            </div>
                            <div class="col-md-4">
                                @php
                                    $grandClosing = $dealerSummaries->sum('closing_balance');
                                @endphp
                                <p class="mb-1">
                                    <strong>Total Closing Balance:</strong>
                                    <span class="{{ $grandClosing >= 0 ? 'text-success' : 'text-danger' }}">
                                        {{ number_format($grandClosing, 2) }} PKR
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Table --}}
                    <div id="dealer-summary" class="table-responsive shadow-sm rounded-3" style="max-height: 65vh; overflow-y: auto;">
                        <table id="dealerSummaryTable" class="table table-bordered table-hover text-center align-middle mb-0">
                            <thead class="table-primary">
                                <tr>
                                    <th>S.No</th>
                                    <th>Dealer Name</th>
                                    <th>Tons</th>
                                    <th>Closing Balance (PKR)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $count = 1; @endphp
                                @forelse ($dealerSummaries as $dealer)
                                    <tr>
                                        <td>{{ $count++ }}</td>
                                        <td>{{ $dealer['dealer_name'] }}</td>
                                        <td>{{ number_format($dealer['tons'], 2) }}</td>
                                        <td>
                                            <span class="fw-bold {{ $dealer['closing_balance'] >= 0 ? 'text-success' : 'text-danger' }}">
                                                {{ number_format($dealer['closing_balance'], 2) }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-muted py-4">
                                            <i class="fas fa-folder-open me-2"></i> No data available.
                                        </td>
                                    </tr>
                                @endforelse

                                {{-- Grand Total --}}
                                <tr class="table-secondary fw-bold">
                                    <td colspan="2" class="text-end">Grand Total</td>
                                    <td>-</td>
                                    <td class="{{ $grandClosing >= 0 ? 'text-success' : 'text-danger' }}">
                                        {{ number_format($grandClosing, 2) }} PKR
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- JS Section --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.13/jspdf.plugin.autotable.min.js"></script>

    <script>
        // Print
        document.getElementById('printSummary').addEventListener('click', function () {
            const table = document.getElementById('dealerSummaryTable');
            const style = `
                <style>
                    table { width: 100%; border-collapse: collapse; }
                    th, td { border: 1px solid #000; padding: 6px; text-align: center; }
                    th { background: #007bff; color: white; }
                    .fw-bold { font-weight: bold; }
                    .text-success { color: green; }
                    .text-danger { color: red; }
                </style>
            `;
            const win = window.open('', '', 'width=900,height=700');
            win.document.write('<html><head><title>Dealer Summary Report</title>' + style + '</head><body>');
            win.document.write('<h3 style="text-align:center;">HK Group of Companies</h3>');
            win.document.write('<h4 style="text-align:center;">Dealer Summary Report</h4>');
            win.document.write(table.outerHTML);
            win.document.write('</body></html>');
            win.document.close();
            win.focus();
            win.print();
        });

        // Export PDF
        document.getElementById('exportPDF').addEventListener('click', function () {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF('p', 'pt', 'a4');
            const today = "{{ \Carbon\Carbon::now()->format('d M, Y') }}";
            doc.setFontSize(14);
            doc.text('HK Group of Companies', 40, 40);
            doc.setFontSize(12);
            doc.text('Dealer Summary Report', 40, 58);
            doc.setFontSize(10);
            doc.text(`Generated On: ${today}`, 40, 74);

            doc.autoTable({
                html: '#dealerSummaryTable',
                startY: 90,
                theme: 'grid',
                headStyles: { fillColor: [41, 128, 185], textColor: 255 },
                styles: { fontSize: 8, halign: 'center' },
                margin: { left: 40, right: 40 },
            });

            const finalY = doc.lastAutoTable.finalY + 20;
            doc.text(`Grand Total: {{ number_format($grandClosing, 2) }} PKR`, 40, finalY);
            doc.save(`Dealer_Summary_Report_{{ \Carbon\Carbon::now()->format('d_M_Y') }}.pdf`);
        });
    </script>

    {{-- Small Styles --}}
    <style>
        .table-responsive { max-height: 65vh; overflow-y: auto; }
        @media (max-width: 767.98px) { .card .card-body h5 { font-size: 1rem; } }
    </style>

</div>
@endsection
