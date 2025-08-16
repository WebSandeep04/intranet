@extends('layouts.app')

@section('title', 'Sales Status')
@section('page_title', 'Sales Status')

@section('content')
<div class="container mt-4">
 
      <button class="btn button" data-bs-toggle="modal" data-bs-target="#createStatusModal">
            <i class="bi bi-plus-lg"></i> Create Status
        </button>
        
<div class="table-responsive">
<table class="table table-hover table-bordered align-middle text-center border shadow-sm rounded" id="salesStatusTable">
  <thead class="table-secondary">
        <tr>
            <th scope="col">Status Name</th>
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

<div class="modal fade" id="createStatusModal" tabindex="-1" aria-labelledby="createStatusModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="createStatusForm">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="createStatusModalLabel">Create Status</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          @csrf
          <div class="mb-3">
            <label for="status_name" class="form-label">Status Name</label>
            <input type="text" class="form-control" id="status_name" name="status_name" required>
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
<div class="modal fade" id="editStatusModal" tabindex="-1" aria-labelledby="editStatusModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="editStatusForm">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editStatusModalLabel">Edit Status</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          @csrf
          <input type="hidden" id="edit_status_id">
          <div class="mb-3">
            <label for="edit_status_name" class="form-label">Status Name</label>
            <input type="text" class="form-control" id="edit_status_name" required>
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
    loadStatus();

function loadStatus(page = 1) {
    $.get(`{{ route('status.fetch') }}?page=${page}`, function (data) {
        let rows = '';
        $.each(data.data, function (i, s) {
            rows += `<tr>
                <td>${s.status_name}</td>
                <td>
                    <button class="btn btn-sm btn-primary editBtn" data-id="${s.id}" data-name="${s.status_name}">
                        <i class="bi bi-pencil-square me-1"></i> Edit
                    </button>
                    <button class="btn btn-sm btn-danger deleteBtn" data-id="${s.id}">
                        <i class="bi bi-trash3-fill me-1"></i> Delete
                    </button>
                </td>
            </tr>`;
        });
        $('#salesStatusTable tbody').html(rows);

        // Generate pagination
        let paginationHTML = `<nav><ul class="pagination">`;

        if (data.prev_page_url) {
            paginationHTML += `<li class="page-item"><a class="page-link" href="#" data-page="${data.current_page - 1}">&laquo;</a></li>`;
        } else {
            paginationHTML += `<li class="page-item disabled"><span class="page-link">&laquo;</span></li>`;
        }

        for (let i = 1; i <= data.last_page; i++) {
            paginationHTML += `
                <li class="page-item ${i === data.current_page ? 'active' : ''}">
                    <a class="page-link" href="#" data-page="${i}">${i}</a>
                </li>`;
        }

        if (data.next_page_url) {
            paginationHTML += `<li class="page-item"><a class="page-link" href="#" data-page="${data.current_page + 1}">&raquo;</a></li>`;
        } else {
            paginationHTML += `<li class="page-item disabled"><span class="page-link">&raquo;</span></li>`;
        }

        paginationHTML += `</ul></nav>`;
        $('#paginationLinks').html(paginationHTML);
    });
}

$(document).on('click', '#paginationLinks .page-link', function (e) {
    e.preventDefault();
    const page = $(this).data('page');
    if (page) {
        loadStatus(page);
    }
});



    $('#createStatusForm').submit(function (e) {
        e.preventDefault();
        $.post("{{ route('status.store') }}", {
            status_name: $('#status_name').val(),
            _token: '{{ csrf_token() }}'
        }, function () {
            $('#createStatusModal').modal('hide');
            $('#createStatusForm')[0].reset();
            loadStatus();
            showAlert('success', 'Status created successfully.');
        }).fail(function (xhr) {
            alert(Object.values(xhr.responseJSON.errors).join("\n"));
        });
    });

    $(document).on('click', '.editBtn', function () {
        $('#edit_status_id').val($(this).data('id'));
        $('#edit_status_name').val($(this).data('name'));
        $('#editStatusModal').modal('show');
    });

    $('#editStatusForm').submit(function (e) {
        e.preventDefault();
        let id = $('#edit_status_id').val();
        $.ajax({
            url: `/status/${id}`,
            type: 'PUT',
            data: {
                status_name: $('#edit_status_name').val(),
                _token: '{{ csrf_token() }}'
            },
            success: function () {
                $('#editStatusModal').modal('hide');
                loadStatus();
                showAlert('success', 'Status updated successfully.');
            }
        });
    });

    $(document).on('click', '.deleteBtn', function () {
        if (confirm('Delete this status?')) {
            $.ajax({
                url: `/status/${$(this).data('id')}`,
                type: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: function () {
                    loadStatus();
                    showAlert('success', 'Status deleted successfully.');
                }
            });
        }
    });
});
</script>
@endpush
