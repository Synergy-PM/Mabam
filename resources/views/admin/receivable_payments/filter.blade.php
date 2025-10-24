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
                                style="max-height: 250px; overflow-y: auto; border: 1px solid #ced4da; border-radius: 0.25rem;">
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
    const originalTexts = Array.from(items).map(item => item.textContent.trim());

    searchInput.addEventListener("keyup", function () {
        const filter = this.value.toLowerCase();
        let found = false;

        items.forEach((item, index) => {
            const text = originalTexts[index].toLowerCase();
            const originalText = originalTexts[index];

            if (text.includes(filter) && filter !== '') {
                const startIndex = text.indexOf(filter);
                const endIndex = startIndex + filter.length;
                const before = originalText.slice(0, startIndex);
                const match = originalText.slice(startIndex, endIndex);
                const after = originalText.slice(endIndex);
                item.innerHTML = `${before}<span class="highlight">${match}</span>${after}`;
                item.style.display = '';
                found = true;
            } else {
                item.innerHTML = originalText;
                item.style.display = text.includes(filter) ? '' : 'none';
            }
        });

        noResult.style.display = found ? 'none' : 'block';
    });

    items.forEach(item => {
        item.addEventListener("click", function () {
            const name = originalTexts[Array.from(items).indexOf(this)];
            const id = this.getAttribute("data-id");
            dropdownBtn.textContent = name;
            dealerIdInput.value = id;
        });
    });
});
</script>

<style>
    .dropdown-menu {
        background-color: #fff;
        border: 1px solid #ced4da !important;
        border-radius: 0.25rem;
        width: 100%;
        box-sizing: border-box;
    }

    .dropdown-item.dealer-option {
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
        color: #000000; 
        transition: background-color 0.2s ease;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .dropdown-item.dealer-option .highlight {
        color: #4dabf7; 
    }

    .dropdown-item.dealer-option:hover {
        background-color: #f8f9fa;
        color: #000000; 
    }

    .dropdown-item.dealer-option:hover .highlight {
        color: #4dabf7; 
    }

    .dropdown-item.dealer-option.active {
        background-color: #007bff;
        color: white; 
    }

    .dropdown-item.dealer-option.active .highlight {
        color: white; 
    }

    .dropdown-item.no-result {
        font-style: italic;
        color: #6c757d;
    }
</style>
@endsection