@extends('layouts.app')

@section('title', 'My Leads')
@section('page_title', 'My Leads')

@section('content')
<x-filter-panel />
<div class="container mt-4">
    <div class="sales_table"></div>
   <div class="table-responsive">
    <table class="table table-bordered table-sm custom-table" id="sales_table">
        <thead class="thead-light">
            <tr>
                <th>Status</th>
                <th>Prospect</th>
                <th>Lead</th>
                <th>Contact Person</th>
                <th>Contact No.</th>
                <th>Next Follow</th>
                <th>State</th>
                <th>City</th>
                <th>Email</th>
                <th>Business</th>
                <th>Source</th>
                <th>Product</th>
                <th>Ticket</th>
                <th>Remark</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>
</div>
<div class="mt-3 d-flex justify-content-center">
  <ul class="pagination" id="paginationLinks"></ul>
</div>
<div class="mt-3 d-flex justify-content-center">
    <ul class="pagination" id="paginationfilterLinks"></ul>
</div>
<div class="mt-3 d-flex justify-content-center">
    <ul class="pagination" id="paginationsearchLinks"></ul>
</div>
<div class="mt-3 d-flex justify-content-center">
    <ul class="pagination" id="paginationdateLinks"></ul>
</div>

@endsection

@push('styles')
<style>
    .custom-table th, .custom-table td {
        font-size: 12px;
        padding: 0.35rem 0.5rem;
        vertical-align: middle;
    }

    .custom-table th {
        background: linear-gradient(to right, #6a11cb, #2575fc);
        font-weight: 600;
        color: #fff;
    }

    .custom-table tr:nth-child(even) {
        background-color: #f2f2f2;
    }

    .custom-table {
        border-collapse: collapse;
        box-shadow: 0 0 5px rgba(0, 0, 0, 0.05);
    }

    .custom-table thead {
        position: sticky;
        top: 0;
        z-index: 1;
    }

    /* for filter box */

    .filterBox{
        display: flex;
        justify-content: space-between;
        background: linear-gradient(to right, #6a11cb, #2575fc);
        padding:20px;
        color: white;
        border-radius:10px;
        flex-wrap: wrap;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
    }
     .filterBox2{
        display: flex;
        justify-content: space-between;
        background: linear-gradient(to right, #6a11cb, #2575fc);
        padding:13px;
        color: white;
        border-radius:10px;
        flex-wrap: wrap;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
    }

      /* pagination */

  .pagination .page-link {
  color: #0d6efd;
}
.pagination .page-item.active .page-link {
  background-color: #0d6efd;
  border-color: #0d6efd;
  color: #fff;
}
      
</style>
@endpush

@push('scripts')

<script>

let currentPage = 1;

function loadMyLeads(page = 1) {
    $.ajax({
        url: '{{ route("myleads.filter") }}?page=' + page,
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            per_page: 10
        },
        success: function (data) {
            let html = '';

            if (data.data.length === 0) {
                html = '<tr><td colspan="14" class="text-center">No records found.</td></tr>';
            } else {
                data.data.forEach(function (record) {
                    let remark = '-';
                    if (record.latest_remark) {
                        remark = `<a href="/remark?sales_record_id=${record.id}" target="_blank">${record.latest_remark.remark}</a>`;
                    }

                    html += `
                        <tr>
                            <td>${record.status?.status_name ?? 'N/A'}</td>
                            <td>${record.prospectus?.prospectus_name ?? 'N/A'}</td>
                            <td>${record.leads_name ?? ''}</td>
                            <td>${record.contact_person ?? ''}</td>
                            <td>${record.contact_number ?? ''}</td>
                            <td>${record.next_follow_up_date ?? 'N/A'}</td>
                            <td>${record.state?.state_name ?? 'N/A'}</td>
                            <td>${record.city?.city_name ?? 'N/A'}</td>
                            <td>${record.email ?? ''}</td>
                            <td>${record.business_type?.business_name ?? 'N/A'}</td>
                            <td>${record.lead_source?.source_name ?? 'N/A'}</td>
                            <td>${record.product?.product_name ?? 'N/A'}</td>
                            <td>${record.ticket_value ?? '0'}</td>
                            <td>${remark}</td>
                        </tr>
                    `;
                });
            }

            $('#sales_table tbody').html(html);
            renderPagination(data);
        },
        error: function (xhr) {
            console.error("Error:", xhr.responseText);
            alert("Something went wrong.");
        }
    });
}

function renderPagination(data) {
    let pagination = $('#paginationLinks');
    pagination.empty();

    const current = data.current_page;
    const last = data.last_page;

    // Prev
    pagination.append(`
        <li class="page-item ${current === 1 ? 'disabled' : ''}">
            <a class="page-link" href="#" data-page="${current - 1}">« Prev</a>
        </li>
    `);

    // Page numbers
    for (let i = 1; i <= last; i++) {
        pagination.append(`
            <li class="page-item ${i === current ? 'active' : ''}">
                <a class="page-link" href="#" data-page="${i}">${i}</a>
            </li>
        `);
    }

    // Next
    pagination.append(`
        <li class="page-item ${current === last ? 'disabled' : ''}">
            <a class="page-link" href="#" data-page="${current + 1}">Next »</a>
        </li>
    `);
}

$(document).on('click', '#paginationLinks .page-link', function (e) {
    e.preventDefault();
    const page = $(this).data('page');
    if (page && page !== currentPage) {
        currentPage = page;
        loadMyLeads(page);
    }
});

$(document).ready(function () {
    loadMyLeads();
});

// search functionality
function searchMyLeads(page = 1) {
    let search = $("#search").val();

    $.ajax({
        url: '{{ route("myleads.filter") }}?page=' + page,
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            search: search,
            per_page: 10
        },
        success: function (response) {
            let data = response.data;
            let html = '';

            if (data.length === 0) {
                html = '<tr><td colspan="14">No records found.</td></tr>';
            } else {
                data.forEach(function (record) {
                    let remark = '-';
                    if (record.latest_remark) {
                        remark = `<a href="/remark?sales_record_id=${record.id}" target="_blank">${record.latest_remark.remark}</a>`;
                    }
                    
                    html += `
                        <tr>
                            <td>${record.status?.status_name ?? 'N/A'}</td>
                            <td>${record.prospectus?.prospectus_name ?? 'N/A'}</td>
                            <td>${record.leads_name ?? 'N/A'}</td>
                            <td>${record.contact_person ?? 'N/A'}</td>
                            <td>${record.contact_number ?? 'N/A'}</td>
                            <td>${record.next_follow_up_date ?? 'N/A'}</td>
                            <td>${record.state?.state_name ?? 'N/A'}</td>
                            <td>${record.city?.city_name ?? 'N/A'}</td>
                            <td>${record.email ?? 'N/A'}</td>
                            <td>${record.business_type?.business_name ?? 'N/A'}</td>
                            <td>${record.lead_source?.source_name ?? 'N/A'}</td>
                            <td>${record.product?.product_name ?? 'N/A'}</td>
                            <td>${record.ticket_value ?? '0'}</td>
                            <td>${remark}</td>
                        </tr>
                    `;
                });
            }

            $('#sales_table tbody').html(html);

            // pagination links
            let links = '';
            response.links.forEach(link => {
                if (link.url !== null) {
                    links += `<li class="page-item ${link.active ? 'active' : ''}">
                        <a class="page-link" href="#" data-page="${link.url.split('page=')[1]}">${link.label}</a>
                    </li>`;
                } else {
                    links += `<li class="page-item disabled"><span class="page-link">${link.label}</span></li>`;
                }
            });

            $('#paginationsearchLinks').html(links);
        },
        error: function (xhr) {
            console.error("Error:", xhr.responseText);
        }
    });
}

// Trigger on keyup
$("#search").on("keyup", function () {
    $('#paginationLinks').hide();
    $('#paginationfilterLinks').hide();
    searchMyLeads(1); 
});

// Handle pagination click
$(document).on('click', '#paginationsearchLinks .page-link', function (e) {
    e.preventDefault();
    let page = $(this).data('page');
    if (page) {
        $('#paginationLinks').hide();
        $('#paginationfilterLinks').hide();
        searchMyLeads(page);
    }
});

// Load filter options
$(document).ready(function() {
    // get business type 
    $.ajax({
        url: "{{ route('getbusiness') }}",
        type: "GET",
        success: function (data) {
            $('#business_type').empty().append('<option value="">Select</option>');
            $.each(data, function (index, type) {
                $('#business_type').append(`<option value="${type.id}">${type.business_name}</option>`);
            });
        },
        error: function () {
            $('#business_type').html('<option value="">Unable to load types</option>');
        }
    });

    // get status
    $.ajax({
        url: "{{ route('getStatuses') }}",
        type: 'GET',
        success: function (data) {
            $('#sales_status').empty().append('<option value="">Select</option>');
            $.each(data, function (key, status) {
                $('#sales_status').append(`<option value="${status.id}">${status.status_name}</option>`);
            });
        },
        error: function () {
            alert('Failed to load sales statuses.');
        }
    });

    // get state
    $.ajax({
        url: "{{ route('state') }}",
        type: "GET",
        dataType: "json",
        success: function (states) {
            let $stateDropdown = $('#state');
            $stateDropdown.empty();
            $stateDropdown.append('<option value="">Select</option>');
            
            $.each(states, function (id, name) {
                $stateDropdown.append(`<option value="${id}">${name}</option>`);
            });
        },
        error: function () {
            alert("Failed to load states.");
        }
    });

    // get sources
    $.ajax({
        url: "{{ route('getsource') }}",
        type: "GET",
        success: function (data) {
            $('#lead_source').empty().append('<option value="">Select</option>');
            $.each(data, function (index, type) {
                $('#lead_source').append(`<option value="${type.id}">${type.source_name}</option>`);
            });
        },
        error: function () {
            $('#lead_source').html('<option value="">Unable to load types</option>');
        }
    });

    // get product
    $.ajax({
        url: "{{ route('getproduct') }}",
        type: "GET",
        success: function (data) {
            $('#product_type').empty().append('<option value="">Select</option>');
            $.each(data, function (index, type) {
                $('#product_type').append(`<option value="${type.id}">${type.product_name}</option>`);
            });
        },
        error: function () {
            $('#product_type').html('<option value="">Unable to load types</option>');
        }
    });

    // get all cities
    $.ajax({
        url: "{{ route('allcity') }}",
        type: "GET",
        success: function (data) {
            $('#city').empty().append('<option value="">Select</option>');
            $.each(data, function (index, type) {
                $('#city').append(`<option value="${type.id}">${type.city_name}</option>`);
            });
        },
        error: function () {
            $('#city').html('<option value="">Unable to load types</option>');
        }
    });

    // State change - load cities for selected state
    $('#state').on('change', function() {
        const stateId = $(this).val();
        if (stateId) {
            $.ajax({
                url: `/myleads/cities/${stateId}`,
                type: 'GET',
                success: function(response) {
                    let cityOptions = '<option value="">Select City</option>';
                    response.forEach(function(city) {
                        cityOptions += `<option value="${city.id}">${city.city_name}</option>`;
                    });
                    $('#city').html(cityOptions);
                },
                error: function() {
                    $('#city').html('<option value="">Unable to load cities</option>');
                }
            });
        } else {
            $('#city').html('<option value="">Select City</option>');
        }
    });
});

// filter functionality
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

function loadFilteredMyLeads(page = 1) {
    $.ajax({
        url: '{{ route("myleads.filter") }}?page=' + page,
        type: 'POST',
        data: {
            status_id: $('#sales_status').val(),
            city_id: $('#city').val(),
            state_id: $('#state').val(),
            business_type_id: $('#business_type').val(),
            lead_source_id: $('#lead_source').val(),
            products_id: $('#product_type').val(),
            per_page: 10
        },
        success: function (response) {
            let data = response.data;
            let html = '';

            if (data.length === 0) {
                html = '<tr><td colspan="14">No records found.</td></tr>';
            } else {
                data.forEach(function (record) {
                    let remark = '-';
                    if (record.latest_remark) {
                        remark = `<a href="/remark?sales_record_id=${record.id}">${record.latest_remark.remark}</a>`;
                    }
                    html += `
                        <tr>
                            <td>${record.status?.status_name ?? 'N/A'}</td>
                            <td>${record.prospectus?.prospectus_name ?? 'N/A'}</td>
                            <td>${record.leads_name ?? ''}</td>
                            <td>${record.contact_person ?? ''}</td>
                            <td>${record.contact_number ?? ''}</td>
                            <td>${record.next_follow_up_date ?? 'N/A'}</td>
                            <td>${record.state?.state_name ?? 'N/A'}</td>
                            <td>${record.city?.city_name ?? 'N/A'}</td>
                            <td>${record.email ?? ''}</td>
                            <td>${record.business_type?.business_name ?? 'N/A'}</td>
                            <td>${record.lead_source?.source_name ?? 'N/A'}</td>
                            <td>${record.product?.product_name ?? 'N/A'}</td>
                            <td>${record.ticket_value ?? '0'}</td>
                            <td>${remark}</td>
                        </tr>
                    `;
                });
            }

            $('#sales_table tbody').html(html);

            // handle pagination links
            let links = '';
            response.links.forEach(link => {
                if (link.url !== null) {
                    links += `<li class="page-item ${link.active ? 'active' : ''}">
                        <a href="#" class="page-link" data-page="${link.url.split('page=')[1]}">${link.label}</a>
                    </li>`;
                } else {
                    links += `<li class="page-item disabled"><span class="page-link">${link.label}</span></li>`;
                }
            });

            $('#paginationfilterLinks').html(links);
        },
        error: function (xhr) {
            console.error("Error:", xhr.responseText);
            alert("Server error occurred. Check the console.");
        }
    });
}

$(document).on('click', '#paginationfilterLinks .page-link', function (e) {
    e.preventDefault();
    let page = $(this).data('page');
    if (page) {
        $('#paginationLinks').hide();
        loadFilteredMyLeads(page);
    }
});

$(document).on('change', '#sales_status, #city, #state, #business_type, #lead_source, #product_type', function () {
    $('#paginationLinks').hide();
    loadFilteredMyLeads(1);
});

// date filter functionality
function loadDateFilteredMyLeads(from_date = '', to_date = '', page = 1) {
    $.ajax({
        url: '{{ route("myleads.filter") }}?page=' + page,
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            date_from: from_date,
            date_to: to_date,
            per_page: 10
        },
        success: function (response) {
            let data = response.data;
            let html = '';

            if (data.length === 0) {
                html = '<tr><td colspan="14">No records found.</td></tr>';
            } else {
                data.forEach(function (record) {
                    let remark = '-';
                    if (record.latest_remark) {
                        remark = `<a href="/remark?sales_record_id=${record.id}">${record.latest_remark.remark}</a>`;
                    }

                    html += `
                        <tr>
                            <td>${record.status?.status_name ?? 'N/A'}</td>
                            <td>${record.prospectus?.prospectus_name ?? 'N/A'}</td>
                            <td>${record.leads_name ?? ''}</td>
                            <td>${record.contact_person ?? ''}</td>
                            <td>${record.contact_number ?? ''}</td>
                            <td>${record.next_follow_up_date ?? 'N/A'}</td>
                            <td>${record.state?.state_name ?? 'N/A'}</td>
                            <td>${record.city?.city_name ?? 'N/A'}</td>
                            <td>${record.email ?? ''}</td>
                            <td>${record.business_type?.business_name ?? 'N/A'}</td>
                            <td>${record.lead_source?.source_name ?? 'N/A'}</td>
                            <td>${record.product?.product_name ?? 'N/A'}</td>
                            <td>${record.ticket_value ?? '0'}</td>
                            <td>${remark}</td>
                        </tr>
                    `;
                });
            }

            $('#sales_table tbody').html(html);

            // Pagination Links
            let links = '';
            response.links.forEach(link => {
                if (link.url !== null) {
                    links += `<li class="page-item ${link.active ? 'active' : ''}">
                        <a href="#" class="page-link" data-page="${link.url.split('page=')[1]}">${link.label}</a>
                    </li>`;
                } else {
                    links += `<li class="page-item disabled"><span class="page-link">${link.label}</span></li>`;
                }
            });
            $('#paginationdateLinks').html(links);
        },
        error: function (xhr) {
            console.error("Error:", xhr.responseText);
            alert("Server error occurred. Check the console.");
        }
    });
}

$(document).on('change', '#from_date, #to_date', function () {
    $('#paginationLinks').hide();
    $('#paginationfilterLinks').hide();
    $('#paginationsearchLinks').hide();
    let from_date = $('#from_date').val();
    let to_date = $('#to_date').val();
    loadDateFilteredMyLeads(from_date, to_date, 1);
});

$(document).on('click', '#paginationdateLinks .page-link', function (e) {
    e.preventDefault();
    let page = $(this).data('page');
    let from_date = $('#from_date').val();
    let to_date = $('#to_date').val();
    if (page) {
        $('#paginationLinks').hide();
        $('#paginationfilterLinks').hide();
        $('#paginationsearchLinks').hide();
        loadDateFilteredMyLeads(from_date, to_date, page);
    }
});

// hide filters
$(document).ready(function () {
    $('#toggleFiltersBtn').on('click', function () {
        let $filterBox = $('.filterScroll');

        if ($filterBox.is(':visible')) {
            $filterBox.slideUp('fast');
            $(this).text('Show Filters ▼');
        } else {
            $filterBox.slideDown('fast');
            $(this).text('Hide Filters ▲');
        }
    });
});

</script>
@endpush
