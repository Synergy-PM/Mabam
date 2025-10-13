<!-- Updated admin/reports/daily-report-filter.blade.php -->
@extends('admin.layouts.shared')
@section('title', 'Daily Report Filter')
@section('header-title', 'Daily Report Filter')
@section('content')
    <div class="container-fluid">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h4 class="mb-4 fw-semibold">Daily Expense Report Filter</h4>
                {{-- Filter Form --}}
                <form method="GET" action="{{ route('daily.report') }}">
                    <div class="row">
                        {{-- From Date --}}
                        <div class="col-md-2 mb-3">
                            <label class="form-label">From Date</label>
                            <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-control">
                        </div>
                        {{-- To Date --}}
                        <div class="col-md-2 mb-3">
                            <label class="form-label">To Date</label>
                            <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-control">
                        </div>
                        {{-- Button --}}
                        <div class="col-md-4 mb-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search me-2"></i>View Report
                            </button>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-4">
                            <a href="{{ route('daily.report') }}" class="btn btn-outline-secondary w-100">
                                <i class="fas fa-refresh me-2"></i>Reset to Today
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
