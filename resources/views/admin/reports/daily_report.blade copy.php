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
                    <h5 class="mb-0 text-white">
                        <i class="fas fa-calendar-day me-2"></i> Daily Transaction Report
                    </h5>
                    <button id="exportPDF" class="btn btn-light btn-sm shadow-sm">
                        <i class="fas fa-file-pdf text-danger me-1"></i> Export PDF
                    </button>
                </div>

                <div class="card-body bg-white">
                    {{-- Report Info --}}
                    <div class="border rounded-3 p-3 mb-4 bg-light shadow-sm">
                        <h6 class="fw-bold text-secondary mb-3">
                            <i class="fas fa-info-circle me-2"></i> Report Information
                        </h6>
                        <div class="row">
                            <div class="col-md-4">
                                <p class="mb-1"><strong>Generated On:</strong> {{ \Carbon\Carbon::now()->format('d M, Y') }}</p>
                            </div>
                            <div class="col-md-4">
                                <p class="mb-1"><strong>Total Records:</strong> {{ count($suppliers) + count($dealers) + count($expenses) }}</p>
                            </div>
                            @if ($startDate && $endDate)
                                <div class="col-md-4">
                                    <p class="mb-1"><strong>Date Range:</strong> {{ \Carbon\Carbon::parse($startDate)->format('d M, Y') }} to {{ \Carbon\Carbon::parse($endDate)->format('d M, Y') }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Professional Table --}}
                    <div class="table-responsive shadow-sm rounded-3">
                        <table id="dailyReportTable" class="table table-bordered table-hover align-middle mb-0 text-center">
                            <thead class="table-primary">
                                <tr>
                                    <th>#</th>
                                    <th>Category</th>
                                    <th>Name / Description</th>
                                    <th>Total Amount (PKR)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $serial = 1; @endphp

                                {{-- Supplier Rows --}}
                                @foreach($suppliers as $supplier)
                                    <tr>
                                        <td>{{ $serial++ }}</td>
                                        <td><i class="fas fa-truck text-primary me-1"></i> Supplier</td>
                                        <td class="fw-semibold">{{ $supplier['name'] ?? 'N/A' }}</td>
                                        <td class="text-success fw-bold">{{ number_format($supplier['total_payable'] ?? 0, 2) }}</td>
                                    </tr>
                                @endforeach

                                {{-- Dealer Rows --}}
                                @foreach($dealers as $dealer)
                                    <tr>
                                        <td>{{ $serial++ }}</td>
                                        <td><i class="fas fa-user-tie text-success me-1"></i> Dealer</td>
                                        <td class="fw-semibold">{{ $dealer['name'] ?? 'N/A' }}</td>
                                        <td class="text-info fw-bold">{{ number_format($dealer['total_receivable'] ?? 0, 2) }}</td>
                                    </tr>
                                @endforeach

                                {{-- Expense Rows --}}
                                @foreach($expenses as $expense)
                                    <tr>
                                        <td>{{ $serial++ }}</td>
                                        <td><i class="fas fa-receipt text-warning me-1"></i> Expense</td>
                                        <td class="fw-semibold">{{ $expense['description'] ?? 'N/A' }}</td>
                                        <td class="text-danger fw-bold">{{ number_format($expense['amount'] ?? 0, 2) }}</td>
                                    </tr>
                                @endforeach

                                {{-- Grand Total --}}
                                <tr class="table-secondary fw-bold">
                                    <td colspan="2">Grand Total</td>
                                    <td></td>
                                    <td>
                                        {{ number_format(($totalPayable ?? 0) + ($totalReceivable ?? 0) + ($totalExpense ?? 0), 2) }} PKR
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    {{-- Summary Cards --}}
                    <div class="row mt-4">
                        <div class="col-md-4">
                            <div class="card bg-warning text-dark">
                                <div class="card-body text-center">
                                    <h5>Total Expense: {{ number_format($totalExpense, 2) }} PKR</h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h5>Total Payable: {{ number_format($totalPayable, 2) }} PKR</h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <h5>Total Receivable: {{ number_format($totalReceivable, 2) }} PKR</h5>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- PDF Export --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.13/jspdf.plugin.autotable.min.js"></script>
    <script>
        document.getElementById('exportPDF').addEventListener('click', function () {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();
            const today = "{{ \Carbon\Carbon::now()->format('d M, Y') }}";

            doc.setFontSize(16);
            doc.text("Daily Transaction Report", 14, 20);
            doc.setFontSize(10);
            doc.text(`Generated On: ${today}`, 14, 28);

            doc.autoTable({
                html: "#dailyReportTable",
                startY: 35,
                theme: "grid",
                headStyles: { fillColor: [41, 128, 185], textColor: 255, fontStyle: 'bold' },
                styles: { fontSize: 8, cellPadding: 3 }
            });

            const finalY = doc.lastAutoTable.finalY + 10;
            doc.setFontSize(12);
            doc.setFont('helvetica', 'bold');
            doc.text("Grand Totals:", 14, finalY);
            doc.text("Expense: {{ number_format($totalExpense, 2) }} PKR | Payable: {{ number_format($totalPayable, 2) }} PKR | Receivable: {{ number_format($totalReceivable, 2) }} PKR", 14, finalY + 6);

            doc.save(`Daily_Transaction_Report_{{ \Carbon\Carbon::now()->format('d_M_Y') }}.pdf`);
        });
    </script>
</div>
@endsection
