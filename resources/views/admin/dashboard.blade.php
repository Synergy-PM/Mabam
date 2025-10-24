@extends('admin.layouts.shared')

@section('content')
    <style>
        /* Smooth Card Animation */
        .card {
            transition: all 0.3s ease;
            border: none;
            border-radius: 12px;
        }

        .card:hover {
            transform: translateY(-6px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }

        .icon-box {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 26px;
            margin: 0 auto 10px auto;
        }

        .card p {
            margin-bottom: 5px;
        }

        /* Gradient Backgrounds */
        .bg-gradient-blue {
            background: linear-gradient(135deg, #007bff, #00c6ff);
        }

        .bg-gradient-green {
            background: linear-gradient(135deg, #28a745, #85e085);
        }

        .bg-gradient-orange {
            background: linear-gradient(135deg, #ff7f50, #ffb347);
        }

        .bg-gradient-purple {
            background: linear-gradient(135deg, #6f42c1, #b084f9);
        }

        .bg-gradient-red {
            background: linear-gradient(135deg, #dc3545, #ff6b6b);
        }
    </style>

    <div class="container-fluid">
        <div class="page-title-box">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h6 class="page-title">Dashboard</h6>
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item active">Welcome to MK TRADERS Dashboard</li>
                    </ol>
                </div>
            </div>
        </div>

        {{-- Row 1 - 3 Cards --}}
        <div class="row g-3">
            <div class="col-xl-4 col-md-6">
                <a href="{{ route('user.index') }}" class="text-decoration-none text-dark">
                    <div class="card shadow-sm text-center">
                        <div class="card-body">
                            <div class="icon-box bg-gradient-blue shadow-sm mb-2">
                                <i class="fas fa-users"></i>
                            </div>
                            <p class="text-muted font-size-15 mb-1">Total Users</p>
                            <h3 class="fw-bold mb-0">{{ $totalUsers }}</h3>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-xl-4 col-md-6">
                <a href="{{ route('suppliers.index') }}" class="text-decoration-none text-dark">
                    <div class="card shadow-sm text-center">
                        <div class="card-body">
                            <div class="icon-box bg-gradient-green shadow-sm mb-2">
                                <i class="fas fa-truck"></i>
                            </div>
                            <p class="text-muted font-size-15 mb-1">Total Suppliers</p>
                            <h3 class="fw-bold mb-0">{{ $totalSupplier }}</h3>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-xl-4 col-md-6">
                <a href="{{ route('dealers.index') }}" class="text-decoration-none text-dark">
                    <div class="card shadow-sm text-center">
                        <div class="card-body">
                            <div class="icon-box bg-gradient-orange shadow-sm mb-2">
                                <i class="fas fa-handshake"></i>
                            </div>
                            <p class="text-muted font-size-15 mb-1">Total Dealers</p>
                            <h3 class="fw-bold mb-0">{{ $totalDealer }}</h3>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        {{-- Row 2 - 2 Cards --}}
        <div class="row g-3 mt-1">
            <div class="col-xl-6 col-md-6">
                <a href="{{ route('receivable-payments.supplier-summary')}}" class="text-decoration-none text-dark">
                    <div class="card shadow-sm text-center">
                        <div class="card-body">
                            <div class="icon-box bg-gradient-purple shadow-sm mb-2">
                                <i class="fas fa-arrow-down"></i>
                            </div>
                            <p class="text-muted font-size-15 mb-1">Total Receivable</p>
                            <h3 class="fw-bold mb-0">{{ number_format($totalReceivables, 2) }}</h3>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-xl-6 col-md-6">
                <a href="{{route('payable-payments.supplier-summary')}}" class="text-decoration-none text-dark">
                    <div class="card shadow-sm text-center">
                        <div class="card-body">
                            <div class="icon-box bg-gradient-red shadow-sm mb-2">
                                <i class="fas fa-arrow-up"></i>
                            </div>
                            <p class="text-muted font-size-15 mb-1">Total Payable</p>
                            <h3 class="fw-bold mb-0">
                                {{ number_format($totalPayables ?? 0, 2) }}
                            </h3>
                        </div>
                    </div>
                </a>
            </div>

        </div>

        {{-- Charts Section --}}
        <div class="row mt-4">
            <div class="col-xl-8">
                <div class="card">
                    <div class="card-header border-0 align-items-center d-flex pb-0">
                        <h4 class="card-title mb-0 flex-grow-1">MK TRADERS Metrics</h4>
                    </div>
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-xl-8 audiences-border">
                                <div id="column-chart" class="apex-charts" style="height: 350px;"></div>
                            </div>
                            <div class="col-xl-4">
                                <div id="donut-chart" class="apex-charts" style="height: 350px;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="card">
                    <div class="card-header border-0 align-items-center d-flex pb-1">
                        <h4 class="card-title mb-0 flex-grow-1">Users by Region</h4>
                        <div>
                            <button type="button" class="btn btn-soft-primary btn-sm">Export Report</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="world-map-markers" style="height: 346px;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
