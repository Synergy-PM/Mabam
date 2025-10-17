@extends('admin.layouts.shared')
@section('title', 'Ledger Report Filter')
@section('header-title', 'Ledger Report Filter')

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <h4 class="mb-4 fw-semibold">Ledger Report Filter</h4>
            <form method="GET" action="{{ route('receivable-payments.ledger-report') }}">
                <div class="row">
                    {{-- Dealer Filter --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Dealer (Optional)</label>

                        <div class="dropdown w-100">
                            <button class="btn btn-light border dropdown-toggle w-100 text-start" type="button"
                                id="dealerDropdownBtn" data-bs-toggle="dropdown" aria-expanded="false">
                                {{ old('dealer_name', 'All Dealers') }}
                            </button>

                            <div class="dropdown-menu p-2 w-100 shadow" aria-labelledby="dealerDropdownBtn"
                                style="max-height: 250px; overflow-y: auto;">

                                <!-- Search Box -->
                                <input type="text" class="form-control mb-2" id="dealerSearchInput" placeholder="Search dealer...">

                                <!-- Dealer List -->
                                <a class="dropdown-item dealer-option" data-id="">All Dealers</a>
                                @foreach($dealers as $dealer)
                                    <a class="dropdown-item dealer-option" data-id="{{ $dealer->id }}">
                                        {{ $dealer->dealer_name ?? 'N/A' }}
                                    </a>
                                @endforeach

                                <!-- No result -->
                                <div class="dropdown-item text-muted text-center no-result" style="display:none;">
                                    No dealer found
                                </div>
                            </div>
                        </div>

                        <input type="hidden" name="dealer_id" id="dealer_id">
                    </div>

                    {{-- From Date --}}
                    <div class="col-md-3 mb-3">
                        <label class="form-label">From Date</label>
                        <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                    </div>

                    {{-- To Date --}}
                    <div class="col-md-3 mb-3">
                        <label class="form-label">To Date</label>
                        <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                    </div>

                    {{-- Button --}}
                    <div class="col-md-4 mb-3 d-flex align-items-end">
                        <button class="btn btn-primary w-100">View Report</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById("dealerSearchInput");
    const items = document.querySelectorAll(".dealer-option");
    const dropdownBtn = document.getElementById("dealerDropdownBtn");
    const dealerIdInput = document.getElementById("dealer_id");
    const noResult = document.querySelector(".no-result");

    searchInput.addEventListener("keyup", function () {
        const filter = this.value.toLowerCase();
        let found = false;
        items.forEach(item => {
            const text = item.textContent.toLowerCase();
            if (text.includes(filter)) {
                item.style.display = "";
                found = true;
            } else {
                item.style.display = "none";
            }
        });
        noResult.style.display = found ? "none" : "block";
    });

    items.forEach(item => {
        item.addEventListener("click", function () {
            const name = this.textContent.trim();
            const id = this.getAttribute("data-id");
            dropdownBtn.textContent = name;
            dealerIdInput.value = id;
        });
    });
});
</script>
@endsection
