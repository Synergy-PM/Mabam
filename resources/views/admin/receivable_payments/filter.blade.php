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
                    <div class="col-md-6 position-relative">
                        <label class="form-label">Dealer (Optional)</label>
                        <input type="text" class="form-control mb-1 dealer-search" id="dealer_search" placeholder="Type dealer name or leave blank for all dealers..." data-row-index="0">
                        <ul class="dealer-suggestion-list list-group position-absolute w-100 shadow-sm" id="suggestion_list"
                            style="z-index: 1000; max-height: 200px; overflow-y: auto; display: none;">
                            <li class="list-group-item list-group-item-action"
                                data-id=""
                                data-row-index="0"
                                style="cursor: pointer;">
                                All Dealers
                            </li>
                            @foreach($dealers as $dealer)
                                <li class="list-group-item list-group-item-action"
                                    data-id="{{ $dealer->id }}"
                                    data-row-index="0"
                                    style="cursor: pointer;">
                                    {{ $dealer->dealer_name ?? 'N/A' }}
                                </li>
                            @endforeach
                            <li class="no-result-item list-group-item text-center text-muted" id="no_result" style="display: none;">
                                No dealer found
                            </li>
                        </ul>
                        <input type="hidden" name="dealer_id" id="dealer_id" class="dealer-id">
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
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('dealer_search');
    const suggestionList = document.getElementById('suggestion_list');
    const dealerIdInput = document.getElementById('dealer_id');
    const items = Array.from(suggestionList.querySelectorAll('li[data-id]'));
    const noResult = document.getElementById('no_result');
    let selectedIndex = -1;

    if (!dealerIdInput.value) {
        searchInput.value = 'All Dealers';
    }

    searchInput.addEventListener('input', function () {
        const query = this.value.toLowerCase().trim();
        selectedIndex = -1;
        let visibleCount = 0;

        if (query) {
            suggestionList.style.display = 'block';
            items.forEach(li => {
                const name = li.textContent.toLowerCase();
                const match = name.includes(query);
                li.style.display = match ? 'block' : 'none';
                if (match) visibleCount++;
            });
            noResult.style.display = visibleCount === 0 ? 'block' : 'none';
        } else {
            suggestionList.style.display = 'block';
            items.forEach(li => li.style.display = 'block');
            noResult.style.display = 'none';
        }
    });

    items.forEach(li => {
        li.addEventListener('click', function () {
            searchInput.value = this.textContent.trim();
            dealerIdInput.value = this.getAttribute('data-id');
            suggestionList.style.display = 'none';
        });
    });

    searchInput.addEventListener('keydown', function(e) {
        const visibleItems = items.filter(li => li.style.display !== 'none');
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            selectedIndex = (selectedIndex + 1) % visibleItems.length;
            highlightItem(visibleItems, selectedIndex);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            selectedIndex = (selectedIndex - 1 + visibleItems.length) % visibleItems.length;
            highlightItem(visibleItems, selectedIndex);
        } else if (e.key === 'Enter') {
            e.preventDefault();
            if (selectedIndex >= 0 && visibleItems[selectedIndex]) {
                searchInput.value = visibleItems[selectedIndex].textContent.trim();
                dealerIdInput.value = visibleItems[selectedIndex].getAttribute('data-id');
                suggestionList.style.display = 'none';
            }
        }
    });

    function highlightItem(list, index) {
        list.forEach((li, i) => li.classList.toggle('active', i === index));
    }

    document.addEventListener('click', function(e) {
        if (!e.target.closest('.position-relative')) {
            suggestionList.style.display = 'none';
        }
    });

    searchInput.addEventListener('click', function() {
        suggestionList.style.display = 'block';
        items.forEach(li => li.style.display = 'block');
        noResult.style.display = 'none';
    });
});
</script>

<style>
    #suggestion_list li.active {
        background-color: #007bff;
        color: white;
    }
</style>
@endsection