@extends('layouts.app')

@section('title', 'Sales Business')
@section('page_title', 'Sales Business')

@section('content')
<div class="container mt-4">
 
      <button class="btn button" data-bs-toggle="modal" data-bs-target="#createBusinessModal">
            <i class="bi bi-plus-lg"></i> Create Business
        </button>
        
<div class="table-responsive">
<table class="table table-hover table-bordered align-middle text-center border shadow-sm rounded" id="salesBusinessTable">
  <thead class="table-secondary">
        <tr>
            <th scope="col">Business Name</th>
            <th scope="col">Actions</th>
        </tr>
    </thead>
    <tbody>
        <!-- Rows will be loaded via jQuery -->
    </tbody>
</table>
</div>
<div id="paginationLinks" class=""></div>

<!-- create modal -->

<div class="modal fade" id="createBusinessModal" tabindex="-1" aria-labelledby="createBusinessModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="createBusinessForm">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="createBusinessModalLabel">Create Business</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          @csrf
          <div class="mb-3">
            <label for="business_name" class="form-label">Business Name</label>
            <input type="text" class="form-control" id="business_name" name="business_name" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn button w-100">Submit</button>
        </div>
      </div>
    </form>
  </div>
</div>


<!--  -->

    <!-- Edit Modal -->
<div class="modal fade" id="editBusinessModal" tabindex="-1" aria-labelledby="editBusinessModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="editBusinessForm">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editBusinessModalLabel">Edit Business</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          @csrf
          <input type="hidden" id="edit_business_id">
          <div class="mb-3">
            <label for="edit_business_name" class="form-label">Business Name</label>
            <input type="text" class="form-control" id="edit_business_name" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn button w-100">Update</button>
        </div>
      </div>
    </form>
  </div>
</div>
<!--  -->

</div>
@endsection

@push('scripts')
<script>
function showAlert(type, message) {
    let colorClass = 'custom-alert-' + type;
    $('#alertBox').html(`
        <div class="custom-alert ${colorClass}">
            ${message}
            <button class="custom-alert-close" onclick="this.parentElement.remove()">×</button>
        </div>
    `);
    setTimeout(() => $('.custom-alert').fadeOut(500, function() { $(this).remove(); }), 2000);
}

$(function () {
    loadBusiness();

function loadBusiness(page = 1) {
    $.get(`{{ route('business.fetch') }}?page=${page}`, function (data) {
        let rows = '';
        $.each(data.data, function (i, s) {
            rows += `<tr>
                <td>${s.business_name}</td>
                <td>
                    <button class="btn btn-sm btn-primary editBtn" data-id="${s.id}" data-name="${s.business_name}">
                        <i class="bi bi-pencil-square me-1"></i> Edit
                    </button>
                    <button class="btn btn-sm btn-danger deleteBtn" data-id="${s.id}">
                        <i class="bi bi-trash3-fill me-1"></i> Delete
                    </button>
                </td>
            </tr>`;
        });
        $('#salesBusinessTable tbody').html(rows);

        // Generate pagination
      let paginationHTML = `<nav aria-label="Page navigation"><ul class="pagination justify-content-center">`;

// Previous Button
if (data.prev_page_url) {
    paginationHTML += `
        <li class="page-item">
            <a class="page-link" href="#" data-page="${data.current_page - 1}">&laquo; Prev</a>
        </li>`;
} else {
    paginationHTML += `
        <li class="page-item disabled">
            <span class="page-link">&laquo; Prev</span>
        </li>`;
}

// Show current, one before, and one after (e.g., 2 3 4)
let start = Math.max(1, data.current_page - 1);
let end = Math.min(data.last_page, start + 2);

if (end - start < 2) {
    start = Math.max(1, end - 2); // Always show 3 pages if possible
}

for (let i = start; i <= end; i++) {
    paginationHTML += `
        <li class="page-item ${i === data.current_page ? 'active' : ''}">
            <a class="page-link" href="#" data-page="${i}">${i}</a>
        </li>`;
}

// Next Button
if (data.next_page_url) {
    paginationHTML += `
        <li class="page-item">
            <a class="page-link" href="#" data-page="${data.current_page + 1}">Next &raquo;</a>
        </li>`;
} else {
    paginationHTML += `
        <li class="page-item disabled">
            <span class="page-link">Next &raquo;</span>
        </li>`;
}

paginationHTML += `</ul></nav>`;
$('#paginationLinks').html(paginationHTML);

    });
}

$(document).on('click', '#paginationLinks .page-link', function (e) {
    e.preventDefault();
    const page = $(this).data('page');
    if (page) {
        loadBusiness(page);
    }
});



    $('#createBusinessForm').submit(function (e) {
        e.preventDefault();
        $.post("{{ route('business.store') }}", {
            business_name: $('#business_name').val(),
            _token: '{{ csrf_token() }}'
        }, function () {
            $('#createBusinessModal').modal('hide');
            $('#createBusinessForm')[0].reset();
            loadBusiness();
            showAlert('success', 'Business created successfully.');
        }).fail(function (xhr) {
            alert(Object.values(xhr.responseJSON.errors).join("\n"));
        });
    });

    $(document).on('click', '.editBtn', function () {
        $('#edit_business_id').val($(this).data('id'));
        $('#edit_business_name').val($(this).data('name'));
        $('#editBusinessModal').modal('show');
    });

    $('#editBusinessForm').submit(function (e) {
        e.preventDefault();
        let id = $('#edit_business_id').val();
        $.ajax({
            url: `/business/${id}`,
            type: 'PUT',
            data: {
                business_name: $('#edit_business_name').val(),
                _token: '{{ csrf_token() }}'
            },
            success: function () {
                $('#editBusinessModal').modal('hide');
                loadBusiness();
                showAlert('success', 'Business updated successfully.');
            }
        });
    });

    $(document).on('click', '.deleteBtn', function () {
        if (confirm('Delete this business?')) {
            $.ajax({
                url: `/business/${$(this).data('id')}`,
                type: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: function () {
                    loadBusiness();
                    showAlert('success', 'Business deleted successfully.');
                }
            });
        }
    });
});
</script>
@endpush
