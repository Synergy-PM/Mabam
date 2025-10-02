@extends('admin.layouts.shared')

@section('content')
<style>
    #passportChart, #ticketChart, #transportChart, #hotelChart {
        max-height: 300px !important;
    }
    .chart-container {
        height: 350px;
    }
    .card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .card:hover {
        transform: translateY(-10px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
    }
    .icon {
        transition: color 0.3s ease;
    }
    .card:hover .icon {
        color: #ff5722;
    }
    .dark-mode {
        background-color: #121212;
        color: white;
    }
    .dark-mode .card {
        background-color: #1e1e1e;
        color: white;
    }
    .avatar-md {
        width: 60px;
        height: 60px;
        object-fit: cover;
    }
</style>

<div class="container-fluid">
    <!-- Page Title -->
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

    <!-- Metrics Cards -->
    <div class="row">
        <!-- Users Card -->
        <div class="col-xl-3 col-md-6">
            <a href="{{ route('user.index') }}" style="text-decoration: none;">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <!-- Profile Image -->
                            <div class="flex-shrink-0">
                                <img src="{{ asset('assets/images/users/avatar-3.jpg') }}" alt="profile"
                                     class="rounded-circle avatar-md border shadow">
                            </div>
                            <div class="flex-grow-1 overflow-hidden ms-4">
                                <p class="text-muted text-truncate font-size-15 mb-2">Total Users</p>
                                <h3 class="fs-4 flex-grow-1 mb-3">{{ $totalUsers }}</h3>
                            </div>
                            <div class="flex-shrink-0 align-self-start">
                                <div class="dropdown">
                                    <a class="dropdown-toggle btn-icon border rounded-circle" href="#" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="ri-more-2-fill text-muted font-size-16"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- City Card -->
        <div class="col-xl-3 col-md-6">
            <a href="{{ route('cities.index') }}" style="text-decoration: none;">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <!-- Profile Image -->
                            <div class="flex-shrink-0">
                                <img src="{{ asset('assets/images/city1.jpg') }}" alt="city"
                                     class="rounded-circle avatar-md border shadow">
                            </div>
                            <div class="flex-grow-1 overflow-hidden ms-4">
                                <p class="text-muted text-truncate font-size-15 mb-2">Total City</p>
                                <h3 class="fs-4 flex-grow-1 mb-3">{{ $totalCity }}</h3>
                            </div>
                            <div class="flex-shrink-0 align-self-start">
                                <div class="dropdown">
                                    <a class="dropdown-toggle btn-icon border rounded-circle" href="#" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="ri-more-2-fill text-muted font-size-16"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Supplier Card -->
        <div class="col-xl-3 col-md-6">
            <a href="{{ route('suppliers.index') }}" style="text-decoration: none;">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <!-- Profile Image -->
                            <div class="flex-shrink-0">
                                <img src="{{ asset('assets/images/users/avatar-8.jpg') }}" alt="supplier"
                                     class="rounded-circle avatar-md border shadow">
                            </div>
                            <div class="flex-grow-1 overflow-hidden ms-4">
                                <p class="text-muted text-truncate font-size-15 mb-2">Total Supplier</p>
                                <h3 class="fs-4 flex-grow-1 mb-3">{{ $totalSupplier }}</h3>
                            </div>
                            <div class="flex-shrink-0 align-self-start">
                                <div class="dropdown">
                                    <a class="dropdown-toggle btn-icon border rounded-circle" href="#" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="ri-more-2-fill text-muted font-size-16"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Dealer Card -->
        <div class="col-xl-3 col-md-6">
            <a href="{{route('dealers.index')}}" style="text-decoration: none;">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <!-- Profile Image -->
                            <div class="flex-shrink-0">
                                <img src="{{ asset('assets/images/users/avatar-7.jpg') }}" alt="dealer"
                                     class="rounded-circle avatar-md border shadow">
                            </div>
                            <div class="flex-grow-1 overflow-hidden ms-4">
                                <p class="text-muted text-truncate font-size-15 mb-2">Total Dealer</p>
                                <h3 class="fs-4 flex-grow-1 mb-3">{{$totalDealer}}</h3>
                            </div>
                            <div class="flex-shrink-0 align-self-start">
                                <div class="dropdown">
                                    <a class="dropdown-toggle btn-icon border rounded-circle" href="#" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="ri-more-2-fill text-muted font-size-16"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
    <!-- END ROW -->

    <!-- Charts Section -->
    <div class="row">
        <div class="col-xl-8">
            <div class="card">
                <div class="card-header border-0 align-items-center d-flex pb-0">
                    <h4 class="card-title mb-0 flex-grow-1">MK TRADERS Metrics</h4>
                </div>
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-xl-8 audiences-border">
                            <div id="column-chart" class="apex-charts chart-container"></div>
                        </div>
                        <div class="col-xl-4">
                            <div id="donut-chart" class="apex-charts chart-container"></div>
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
