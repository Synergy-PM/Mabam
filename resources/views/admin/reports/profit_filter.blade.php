@extends('admin.layouts.shared')
@section('title', 'Profit Report Filter')
@section('header-title', 'Profit Report Filter')

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <h4 class="mb-4 fw-semibold">Profit Report Filter</h4>

            <form method="GET" action="{{ route('profit.report') }}">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Report Type</label>
                        <select name="report_type" class="form-control" id="reportType">
                            <option value="daily">Daily</option>
                            <option value="monthly">Monthly</option>
                        </select>
                    </div>

                    <div class="col-md-4 daily-field">
                        <label class="form-label">From Date</label>
                        <input type="date" name="start_date" class="form-control">
                    </div>

                    <div class="col-md-4 daily-field">
                        <label class="form-label">To Date</label>
                        <input type="date" name="end_date" class="form-control">
                    </div>

                    <div class="col-md-4 monthly-field d-none">
                        <label class="form-label">Select Year</label>
                        <input type="number" name="year" class="form-control" value="{{ date('Y') }}">
                    </div>

                    <div class="col-md-4">
                        <button class="btn btn-primary w-100 mt-4">View Report</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const reportType = document.getElementById('reportType');
    const dailyFields = document.querySelectorAll('.daily-field');
    const monthlyField = document.querySelector('.monthly-field');

    reportType.addEventListener('change', function() {
        if (this.value === 'monthly') {
            dailyFields.forEach(el => el.classList.add('d-none'));
            monthlyField.classList.remove('d-none');
        } else {
            dailyFields.forEach(el => el.classList.remove('d-none'));
            monthlyField.classList.add('d-none');
        }
    });
});
</script>
@endsection
