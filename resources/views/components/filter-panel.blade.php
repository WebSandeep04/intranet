{{-- resources/views/components/filter-panel.blade.php --}}
<div class="filterScroll">
    <div class="filterBox mb-2 mt-4">
        <div class="mb-2">
            <label for="sales_status" class="form-label">Status</label>
            <select class="form-select" id="sales_status" name="sales_status" required>
                <option value="">Loading...</option>
            </select>
        </div>

        <div class="mb-2">
            <label for="state" class="form-label">State</label>
            <select class="form-select" id="state" name="state" required>
                <option value="">Loading...</option>
            </select>
        </div>

        <div class="mb-2">
            <label for="city" class="form-label">City</label>
            <select class="form-select" id="city" name="city">
                <option value="">Loading...</option>
            </select>
        </div>

        <div class="mb-2">
            <label for="business_type" class="form-label">Business Type</label>
            <select class="form-select" id="business_type" name="business_type" required>
                <option value="">Loading...</option>
            </select>
        </div>

        <div class="mb-2">
            <label for="lead_source" class="form-label">Lead Sources</label>
            <select class="form-select" id="lead_source" name="lead_source" required>
                <option value="">Loading...</option>
            </select>
        </div>

        <div class="mb-2">
            <label for="product_type" class="form-label">Product Type</label>
            <select class="form-select" id="product_type" name="product_type" required>
                <option value="">Loading...</option>
            </select>
        </div>
    </div>

    <div class="filterBox2">
        <div class="mb-2">
            <label for="search" class="form-label">Search</label>
            <input type="text" class="form-control" id="search" placeholder="🔍 Search anything...">
        </div>

        <div class="mb-2">
            <label for="from_date" class="form-label">From Date</label>
            <input type="date" class="form-control" id="from_date" name="from_date">
        </div>

        <div class="mb-2">
            <label for="to_date" class="form-label">To Date</label>
            <input type="date" class="form-control" id="to_date" name="to_date">
        </div>
    </div>
</div>

<button id="toggleFiltersBtn" class="btn btn-sm btn-warning d-block mx-auto mb-2">Hide Filters ▲</button>
