@extends('admin.layouts.shared')
@section('title', 'Supplier-wise Closing Balances')
@section('header-title', 'Supplier-wise Closing Balances')

@section('content')
    <div class="container-fluid">
        <div class="card shadow border-0 rounded-4 overflow-hidden">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-file-invoice-dollar me-2"></i> Supplier-wise Closing Balances</h5>
                <div>
                    <button id="printSupplierSummary" class="btn btn-outline-light btn-sm me-2">
                        <i class="fas fa-print me-1"></i> Print
                    </button>
                    <button id="exportSupplierPDF" class="btn btn-outline-light btn-sm">
                        <i class="fas fa-file-pdf me-1"></i> PDF
                    </button>
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-light btn-sm">
                        Back
                    </a>
                </div>
            </div>

            <div class="card-body bg-white" id="ledger-section">
                <div class="border rounded-3 p-3 mb-4 bg-light shadow-sm">
                    <strong>Supplier:</strong> All Suppliers <br>
                    <strong>Period:</strong>
                    {{ $startDate ? \Carbon\Carbon::parse($startDate)->format('d M, Y') : 'All' }} →
                    {{ $endDate ? \Carbon\Carbon::parse($endDate)->format('d M, Y') : 'All' }}
                </div>

                <div id="supplier-summary" class="table-responsive shadow-sm rounded-3">
                    <table class="table table-bordered table-striped text-center align-middle">
                        <thead class="table-primary">
                            <tr>
                                <th>#</th>
                                <th>Supplier Name</th>
                                <th>Tons</th>
                                {{-- <th>Credit</th> --}}
                                {{-- <th>Debit</th> --}}
                                <th>Closing Balance</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($supplierSummaries as $index => $supplier)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $supplier['supplier_name'] }}</td>
                                    <td>{{ number_format($supplier['tons'], 2) }}</td>
                                    {{-- <td>{{ number_format($supplier['total_credit'], 2) }}</td> --}}
                                    {{-- <td>{{ number_format($supplier['total_debit'], 2) }}</td> --}}
                                    <td><strong>{{ number_format($supplier['closing_balance'], 2) }}</strong></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-muted py-3">
                                        <i class="fas fa-folder-open me-2"></i> No supplier summaries found
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- PDF Export Scripts --}}
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.13/jspdf.plugin.autotable.min.js"></script>

        <script>
            document.getElementById('exportSupplierPDF').addEventListener('click', function() {
                const {
                    jsPDF
                } = window.jspdf;
                const doc = new jsPDF('landscape');
                doc.text("Supplier-wise Closing Balances", 14, 15);
                doc.autoTable({
                    html: '#supplier-summary table',
                    startY: 25,
                    theme: 'grid'
                });
                doc.save('Supplier_Summary.pdf');
            });

            document.getElementById('printSupplierSummary').addEventListener('click', function() {
                const printWin = window.open('', '_blank');
                const supplierSummary = document.getElementById('supplier-summary').outerHTML;
                printWin.document.write(`
                <html>
                    <head><title>Supplier Summary</title></head>
                    <body>${supplierSummary}</body>
                </html>
            `);
                printWin.document.close();
                printWin.print();
            });
        </script>
    </div>
@endsection
