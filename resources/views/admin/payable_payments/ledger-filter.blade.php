@extends('admin.layouts.shared')
@section('title', 'Ledger Report Filter')
@section('header-title', 'Ledger Report Filter')

@section('content')
<div class="container-fluid">
  <div class="card shadow-sm border-0">
    <div class="card-body">
      <h4 class="mb-4 fw-semibold">Ledger Report Filter</h4>

      <form method="GET" action="{{ route('payable-payments.ledger-report') }}">
        <div class="row">

          {{-- Supplier Filter --}}
          <div class="col-md-4 mb-3 position-relative">
            <label for="supplier_search" class="form-label">Search Supplier</label>
            <input type="text" id="supplier_search" class="form-control mb-1" placeholder="Type supplier name...">

            <ul id="suggestion_list"
                class="list-group position-absolute w-100 shadow-sm"
                style="z-index: 1000; max-height: 200px; overflow-y: auto; display: none;">
              @foreach ($suppliers as $supplier)
                <li class="list-group-item list-group-item-action" 
                    data-id="{{ $supplier->id }}" 
                    style="cursor:pointer;">
                  {{ $supplier->supplier_name }}
                </li>
              @endforeach
              <li id="no_result" class="list-group-item text-muted" style="display:none;">No supplier found</li>
            </ul>

            <input type="hidden" name="supplier_id" id="supplier_id">
          </div>

          {{-- From Date --}}
          <div class="col-md-4 mb-3">
            <label class="form-label">From Date</label>
            <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
          </div>

          {{-- To Date --}}
          <div class="col-md-4 mb-3">
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
    const searchInput = document.getElementById('supplier_search');
    const suggestionList = document.getElementById('suggestion_list');
    const supplierIdInput = document.getElementById('supplier_id');
    const items = Array.from(suggestionList.querySelectorAll('li[data-id]'));
    const noResult = document.getElementById('no_result');
    let selectedIndex = -1;

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
            suggestionList.style.display = 'none';
            noResult.style.display = 'none';
        }
    });

    items.forEach(li => {
        li.addEventListener('click', function () {
            searchInput.value = this.textContent.trim();
            supplierIdInput.value = this.getAttribute('data-id');
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
                supplierIdInput.value = visibleItems[selectedIndex].getAttribute('data-id');
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

    suggestionList.style.display = 'none';
});
</script>

<style>
  #suggestion_list li.active {
      background-color: #007bff;
      color: white;
  }
</style>
@endsection
