@extends('layouts.app')

@section('title', 'Sales Product')
@section('page_title', 'Sales Product')

@section('content')
<div class="container mt-4">
  <h5 class="mb-3">Today's Pending</h5>
  <div class="d-flex justify-content-start mb-3">
  <input type="text" id="followupSearch" class="form-control w-50 shadow-sm" placeholder="🔍 Search follow-ups...">
</div>
   <div class="table-responsive-custom">
    <table class="custom-table" id="followupsTable">
      <thead>
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
@endsection

@push('styles')
<style>
  #followupSearch {
    border-radius: 0.375rem;
    border: 1px solid #ced4da;
  }

  #followupSearch:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
  }

  .table-responsive-custom {
    max-width: 85%;
    overflow-x: auto;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 8px;
    background-color: #fff;
  }

  .custom-table {
    min-width: 1200px;
    width: 100%;
    font-size: 14px;
    border-collapse: collapse;
  }

  .custom-table thead tr {
    background: linear-gradient(to right, #6a11cb, #2575fc);
    color: #fff;
  }

  .custom-table thead th {
    padding: 10px;
    font-weight: 600;
    border: 1px solid #dee2e6;
    text-align: center;
    white-space: nowrap;
  }

  .custom-table tbody td {
    padding: 8px;
    border: 1px solid #dee2e6;
    text-align: center;
    vertical-align: middle;
  }

  .custom-table tbody tr:nth-child(even) {
    background-color: #f9f9f9;
  }

  .custom-table tbody tr:hover {
    background-color: #e2e6ea;
  }

  h5.mb-3 {
    font-weight: 600;
    color: #0d6efd;
    border-left: 4px solid #ffc107;
    padding-left: 10px;
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

function loadFollowups(page = 1) {
  $.ajax({
    url: `/todaypendingfollowupstabledata?page=${page}`,
    method: 'GET',
    success: function (response) {
      const tbody = $('#followupsTable tbody');
      tbody.empty();

      if (response.data.length === 0) {
        tbody.append(`<tr><td colspan="14" class="text-center">No records found</td></tr>`);
        $('#paginationLinks').empty();
        return;
      }

      response.data.forEach(item => {
         const remark = item.latest_remark
          ? `<a href="/remark?sales_record_id=${item.id}" class="text-decoration-underline text-primary">${item.latest_remark}</a>`
          : '-';
        tbody.append(`
          <tr>
            <td>${item.status_name ?? '-'}</td>
            <td>${item.prospectus_name ?? '-'}</td>
            <td>${item.leads_name ?? '-'}</td>
            <td>${item.contact_person ?? '-'}</td>
            <td>${item.contact_number ?? '-'}</td>
            <td>${item.next_follow_up_date ?? '-'}</td>
            <td>${item.state_name ?? '-'}</td>
            <td>${item.city_name ?? '-'}</td>
            <td>${item.email ?? '-'}</td>
            <td>${item.business_name ?? '-'}</td>
            <td>${item.source_name ?? '-'}</td>
            <td>${item.product_name ?? '-'}</td>
            <td>${item.ticket_value ?? '-'}</td>
            <td>${remark}</td>
          </tr>
        `);
      });

      renderPagination(response);
    }
  });
}

function renderPagination(data) {
  let pagination = $('#paginationLinks');
  pagination.empty();

  const current = data.current_page;
  const last = data.last_page;

  pagination.append(`
    <li class="page-item ${current === 1 ? 'disabled' : ''}">
      <a class="page-link" href="#" data-page="${current - 1}">« Prev</a>
    </li>
  `);

  for (let i = 1; i <= last; i++) {
    pagination.append(`
      <li class="page-item ${i === current ? 'active' : ''}">
        <a class="page-link" href="#" data-page="${i}">${i}</a>
      </li>
    `);
  }

  pagination.append(`
    <li class="page-item ${current === last ? 'disabled' : ''}">
      <a class="page-link" href="#" data-page="${current + 1}">Next »</a>
    </li>
  `);
}

// Handle pagination clicks
$(document).on('click', '.pagination .page-link', function (e) {
  e.preventDefault();
  const page = $(this).data('page');
  if (page) {
    currentPage = page;
    loadFollowups(page);
  }
});

// Initial load
$(document).ready(function () {
  loadFollowups();
});

$('#followupSearch').on('keyup', function () {
  let search = $(this).val();

  $.ajax({
    url: '/searchpendingFollowups',
    method: 'GET',
    data: { search: search },
    success: function (data) {
      let tbody = $('#followupsTable tbody');
      tbody.empty();

      if (data.length === 0) {
        tbody.append('<tr><td colspan="14" class="text-center">No records found</td></tr>');
      } else {
        data.forEach((item) => {
          tbody.append(`
            <tr>
              <td>${item.status_name ?? '-'}</td>
              <td>${item.prospectus_name ?? '-'}</td>
              <td>${item.leads_name ?? '-'}</td>
              <td>${item.contact_person ?? '-'}</td>
              <td>${item.contact_number ?? '-'}</td>
              <td>${item.next_follow_up_date ?? '-'}</td>
              <td>${item.state_name ?? '-'}</td>
              <td>${item.city_name ?? '-'}</td>
              <td>${item.email ?? '-'}</td>
              <td>${item.business_name ?? '-'}</td>
              <td>${item.source_name ?? '-'}</td>
              <td>${item.product_name ?? '-'}</td>
              <td>${item.ticket_value ?? '-'}</td>
              <td>${item.latest_remark ?? '-'}</td>
            </tr>
          `);
        });
      }
    },
    error: function () {
      alert('Search failed.');
    }
  });
});

</script>
@endpush

