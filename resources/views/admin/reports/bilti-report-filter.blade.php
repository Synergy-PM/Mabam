@extends('admin.layouts.shared')
@section('title', 'Bilti Report Filter')
@section('header-title', 'Bilti Report Filter')

@section('content')
<div class="container-fluid">
  <div class="card shadow-sm border-0">
    <div class="card-body">
      <h4 class="mb-4 fw-semibold">Bilti Report Filter</h4>

      {{-- Filter Form --}}
      <form method="GET" action="{{ route('bilti.report') }}">
        <div class="row">

          {{-- Dealer Search --}}
          <div class="col-md-4 mb-3 position-relative">
            <label for="dealer_search" class="form-label">Search Dealer</label>
            <input type="text" id="dealer_search" class="form-control mb-1" placeholder="Type dealer name..." value="{{ $selectedDealerName ?? '' }}">
            <ul id="dealer_suggestion_list"
                class="list-group position-absolute w-100 shadow-sm"
                style="z-index: 1000; max-height: 200px; overflow-y: auto; display: none; border: 1px solid #ced4da; border-radius: 0.25rem;">
              @foreach($dealers as $dealer)
                <li class="list-group-item list-group-item-action dealer-option" 
                    data-id="{{ $dealer->id }}" 
                    style="cursor: pointer;">
                  {{ $dealer->dealer_name }}
                </li>
              @endforeach
              {{-- No found message placeholder --}}
              <li id="no_result_item" class="list-group-item text-center text-muted" style="display: none;">
                No dealer found
              </li>
            </ul>
            <input type="hidden" name="dealer_id" id="dealer_id" value="{{ request('dealer_id') }}">
          </div>

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
            <button class="btn btn-primary w-100">View Report</button>
          </div>

        </div>
      </form>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const dealerSearch = document.getElementById('dealer_search');
    const dealerSuggestionList = document.getElementById('dealer_suggestion_list');
    const dealerIdInput = document.getElementById('dealer_id');
    const dealerItems = Array.from(dealerSuggestionList.querySelectorAll('li[data-id]'));
    const noResultItem = document.getElementById('no_result_item');
    const originalTexts = Array.from(dealerItems).map(item => item.textContent.trim());
    let selectedIndex = -1;

    dealerSearch.addEventListener('input', function () {
        const query = this.value.toLowerCase().trim();
        selectedIndex = -1;
        let matchCount = 0;

        if (query) {
            dealerSuggestionList.style.display = 'block';
            dealerItems.forEach((item, index) => {
                const text = originalTexts[index].toLowerCase();
                const originalText = originalTexts[index];

                if (text.includes(query)) {
                    const startIndex = text.indexOf(query);
                    const endIndex = startIndex + query.length;
                    const before = originalText.slice(0, startIndex);
                    const match = originalText.slice(startIndex, endIndex);
                    const after = originalText.slice(endIndex);
                    item.innerHTML = `${before}<span class="highlight">${match}</span>${after}`;
                    item.style.display = 'block';
                    matchCount++;
                } else {
                    item.innerHTML = originalText;
                    item.style.display = 'none';
                }
            });

            noResultItem.style.display = matchCount === 0 ? 'block' : 'none';
        } else {
            dealerSuggestionList.style.display = 'none';
            dealerItems.forEach((item, index) => {
                item.innerHTML = originalTexts[index];
                item.style.display = 'block';
            });
            noResultItem.style.display = 'none';
        }
    });

    dealerItems.forEach(item => {
        item.addEventListener('click', function () {
            const index = Array.from(dealerItems).indexOf(this);
            dealerSearch.value = originalTexts[index];
            dealerIdInput.value = this.getAttribute('data-id');
            dealerSuggestionList.style.display = 'none';
        });
    });

    dealerSearch.addEventListener('keydown', function (e) {
        const visibleItems = dealerItems.filter(li => li.style.display !== 'none');
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
                const index = Array.from(dealerItems).indexOf(visibleItems[selectedIndex]);
                dealerSearch.value = originalTexts[index];
                dealerIdInput.value = visibleItems[selectedIndex].getAttribute('data-id');
                dealerSuggestionList.style.display = 'none';
            }
        }
    });

    function highlightItem(list, index) {
        list.forEach((li, i) => li.classList.toggle('active', i === index));
    }

    document.addEventListener('click', function (e) {
        if (!e.target.closest('.position-relative')) {
            dealerSuggestionList.style.display = 'none';
        }
    });

    dealerSuggestionList.style.display = 'none';
});
</script>

<style>
    #dealer_suggestion_list {
        background-color: #fff;
        border: 1px solid #ced4da !important;
        border-radius: 0.25rem;
    }

    #dealer_suggestion_list li.dealer-option {
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
        color: #000000; 
        transition: background-color 0.2s ease;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    #dealer_suggestion_list li.dealer-option .highlight {
        color: #4dabf7; 
    }

    #dealer_suggestion_list li.dealer-option:hover {
        background-color: #f8f9fa;
        color: #000000; 
    }

    #dealer_suggestion_list li.dealer-option:hover .highlight {
        color: #4dabf7; 
    }

    #dealer_suggestion_list li.active {
        background-color: #007bff;
        color: white; 
    }

    #dealer_suggestion_list li.active .highlight {
        color: white; 
    }

    #dealer_suggestion_list li.text-muted {
        font-style: italic;
        color: #6c757d;
    }
</style>
@endsection