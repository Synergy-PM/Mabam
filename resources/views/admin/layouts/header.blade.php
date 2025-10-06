<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>MK_TRADERS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
    <meta content="Themesdesign" name="author" />

    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.png') }}">

    <!-- plugin css -->
    <link href="{{ asset('assets/libs/jsvectormap/jsvectormap.min.css') }}" rel="stylesheet" type="text/css" />

    <!-- Layout Js -->
    <script src="{{ asset('assets/js/layout.js') }}"></script>
    <!-- Bootstrap Css -->
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" id="bootstrap-style" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="{{ asset('assets/css/app.min.css') }}" id="app-style" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css') }}"
        rel="stylesheet" type="text/css" />
</head>

<body data-sidebar="colored">
    <!-- Begin page -->
    <div id="layout-wrapper">
        <header id="page-topbar">
            <div class="navbar-header">
                <div class="d-flex">
                    <!-- LOGO -->
                    <div class="navbar-brand-box">
                        <a href="{{ route('dashboard') }}" class="logo logo-dark">
                            <span class="logo-sm">
                                <img src="{{ asset('assets/images/logo-dark.png') }}" alt="logo-sm-dark" height="24">
                            </span>
                            <span class="logo-lg">
                                <img src="{{ asset('assets/images/logo-sm-dark.png') }}" alt="logo-dark" height="25">
                            </span>
                        </a>

                        <a href="{{ route('dashboard') }}" class="logo logo-light d-flex align-items-center">
                            <span class="logo-sm">
                                <img src="{{ asset('assets/images/favicon.png') }}" alt="logo-sm-light" height="36">
                            </span>
                            <span class="ms-2">
                                <h2 class="text-white mb-0">MK TRADERS</h2>
                            </span>
                        </a>
                    </div>

                    <button type="button"
                        class="btn btn-sm px-3 font-size-24 header-item waves-effect vertical-menu-btn"
                        id="vertical-menu-btn">
                        <i class="ri-menu-2-line align-middle"></i>
                    </button>

                    <!-- start page title -->
                    <div class="page-title-box align-self-center d-none d-md-block">
                        <h4 class="page-title mb-0">Dashboard</h4>
                    </div>
                    <!-- end page title -->
                </div>

                <div class="dropdown ms-sm-3 header-item topbar-user">
                    <button type="button" class="btn" id="page-header-user-dropdown" data-bs-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                        <span class="d-flex align-items-center">
                            <img class="rounded-circle header-profile-user"
                                src="{{ asset('assets/images/favicon.png') }}" alt="Header Avatar">
                            <span class="text-start ms-xl-2">
                                <span class="d-none d-xl-inline-block ms-1 fw-medium user-name-text">
                                    MK TRADERS
                                </span>
                                <span class="d-none d-xl-block ms-1 fs-12 user-name-sub-text">
                                    Group of Companies
                                </span>
                            </span>
                        </span>
                    </button>

                    <div class="dropdown-menu dropdown-menu-end">

                        {{-- 🔹 Logout Form --}}
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                        <a class="dropdown-item" href="#"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="mdi mdi-logout text-muted fs-16 align-middle me-1"></i>
                            <span class="align-middle" data-key="t-logout">Logout</span>
                        </a>

                        {{-- 🔹 Update Password Link --}}
                        {{-- <a class="dropdown-item" href="{{ route('EditPassword') }}">
                            <i class="mdi mdi-key text-muted fs-16 align-middle me-1"></i>
                            <span class="align-middle">Update Password</span>
                        </a> --}}

                    </div>
                </div>

            </div>
        </header>

        <div class="vertical-menu">
            <!-- LOGO -->
            <div class="navbar-brand-box">
                <a href="{{ route('dashboard') }}" class="logo logo-dark">
                    <span class="logo-sm">
                        <img src="{{ asset('assets/images/logo-sm-dark.png') }}" alt="logo-sm-dark" height="24">
                    </span>
                    <span class="logo-lg">
                        <img src="{{ asset('assets/images/logo-dark.png') }}" alt="logo-dark" height="22">
                    </span>
                </a>

                <a href="{{ route('dashboard') }}" class="logo logo-light d-flex align-items-center">
                    <span class="logo-sm">
                        <img src="{{ asset('assets/images/favicon.png') }}" alt="logo-sm-light" height="45">
                    </span>
                    <span class="ms-2 mt-3">
                        <h2 class="text-white mb-0">MK TRADERS</h2>
                    </span>
                </a>
            </div>

            <button type="button" class="btn btn-sm px-3 font-size-24 header-item waves-effect vertical-menu-btn"
                id="vertical-menu-btn">
                <i class="ri-menu-2-line align-middle"></i>
            </button>

            <div data-simplebar class="vertical-scroll">
                <div id="sidebar-menu">
                    <ul class="metismenu list-unstyled" id="side-menu">
                        <li class="menu-title">Menu</li>
                        <li>
                            <a href="{{ route('dashboard') }}" class="waves-effect">
                                <i class="uim uim-airplay"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>
                        <li>
                            <a href="javascript:void(0);" class="has-arrow waves-effect">
                                <i class="ri-admin-line"></i>
                                <span>User Management</span>
                            </a>
                            <ul class="sub-menu" aria-expanded="false">
                                <li><a href="{{ route('role.index') }}">Roles</a></li>
                                <li><a href="{{ route('user.index') }}">Users</a></li>
                                <li><a href="{{ route('user_activity.index') }}">User Activity</a></li>
                            </ul>
                        </li>
                        <li>
                            <a href="javascript:void(0);" class="has-arrow waves-effect">
                                <i class="ri-building-4-line"></i>
                                <span>Dealer Management</span>
                            </a>
                            <ul class="sub-menu" aria-expanded="false">
                                <li><a href="{{ route('cities.index') }}">Add Cities</a></li>
                                <li><a href="{{ route('suppliers.index') }}">Add Suppliers</a></li>
                                <li><a href="{{ route('dealers.index') }}">Add Dealers</a></li>
                                <li><a href="{{ route('payables.index') }}">Payable</a></li>
                                {{-- <li><a href="{{ route('receivables.index') }}">Receivable</a></li> --}}
                            </ul>
                        </li>
                      <li> 
                        <a href="javascript:void(0);" class="has-arrow waves-effect">
                            <i class="ri-exchange-dollar-line"></i>
                            <span>Transactions</span>
                        </a>
                        <ul class="sub-menu" aria-expanded="false">
                            <li><a href="{{ route('payable-payments.index')}}">Payable Payment</a></li>
                            {{-- <li><a href="#">Receivable Payment</a></li> --}}
                            <li><a href="{{ route('payable-payments.ledger-filter') }}">Ledger Report</a></li> {{-- 🔹 Ledger link --}}

                        </ul>
                     </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="main-content">
     <div class="page-content">
