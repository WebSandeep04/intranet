@extends('layouts.app')

@section('title', 'Sales City')
@section('page_title', 'Sales City')

@section('content')
<div class="container mt-4">
 
      <button class="btn button" data-bs-toggle="modal" data-bs-target="#createCityModal">
            <i class="bi bi-plus-lg"></i> Create City
        </button>
        
<div class="table-responsive">
<table class="table table-hover table-bordered align-middle text-center border shadow-sm rounded" id="salesCityTable">
  <thead class="table-secondary">
        <tr>
            <th scope="col">City Name</th>
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

<div class="modal fade" id="createCityModal" tabindex="-1" aria-labelledby="createCityModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="createCityForm">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="createCityModalLabel">Create City</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          @csrf
          <div class="mb-3">
            <label for="state_id" class="form-label">State</label>
            <select class="form-control" id="state_id" name="state_id" required>
              <option value="">Select State</option>
              <!-- States will be loaded dynamically via AJAX -->
            </select>
          </div>
          <div class="mb-3">
            <label for="city_name" class="form-label">City Name</label>
            <input type="text" class="form-control" id="city_name" name="city_name" required>
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
<div class="modal fade" id="editCityModal" tabindex="-1" aria-labelledby="editCityModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="editCityForm">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editCityModalLabel">Edit City</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          @csrf
          <input type="hidden" id="edit_city_id">
          <div class="mb-3">
            <label for="edit_city_name" class="form-label">City Name</label>
            <input type="text" class="form-control" id="edit_city_name" required>
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
    loadCity();

function loadCity(page = 1) {
    $.get(`{{ route('city.fetch') }}?page=${page}`, function (data) {
        let rows = '';
        $.each(data.data, function (i, s) {
            rows += `<tr>
                <td>${s.city_name}</td>
                <td>
                    <button class="btn btn-sm btn-primary editBtn" data-id="${s.id}" data-name="${s.city_name}">
                        <i class="bi bi-pencil-square me-1"></i> Edit
                    </button>
                    <button class="btn btn-sm btn-danger deleteBtn" data-id="${s.id}">
                        <i class="bi bi-trash3-fill me-1"></i> Delete
                    </button>
                </td>
            </tr>`;
        });
        $('#salesCityTable tbody').html(rows);

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
        loadCity(page);
    }
});



    $('#createCityForm').submit(function (e) {
        e.preventDefault();
        $.post("{{ route('city.store') }}", {
            state_id: $('#state_id').val(),
            city_name: $('#city_name').val(),
            _token: '{{ csrf_token() }}'
        }, function () {
            $('#createCityModal').modal('hide');
            $('#createCityForm')[0].reset();
            loadCity();
            showAlert('success', 'City created successfully.');
        }).fail(function (xhr) {
            alert(Object.values(xhr.responseJSON.errors).join("\n"));
        });
    });



    

    $(document).on('click', '.editBtn', function () {
        $('#edit_city_id').val($(this).data('id'));
        $('#edit_city_name').val($(this).data('name'));
        $('#editCityModal').modal('show');
    });

    $('#editCityForm').submit(function (e) {
        e.preventDefault();
        let id = $('#edit_city_id').val();
        $.ajax({
            url: `/city/${id}`,
            type: 'PUT',
            data: {
                city_name: $('#edit_city_name').val(),
                _token: '{{ csrf_token() }}'
            },
            success: function () {
                $('#editCityModal').modal('hide');
                loadCity();
                showAlert('success', 'City updated successfully.');
            }
        });
    });

    $(document).on('click', '.deleteBtn', function () {
        if (confirm('Delete this city?')) {
            $.ajax({
                url: `/city/${$(this).data('id')}`,
                type: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: function () {
                    loadCity();
                    showAlert('success', 'City deleted successfully.');
                }
            });
        }
    });
});

$(document).ready(function(){
      $('#createCityModal').on('show.bs.modal', function() {
    $.get('/state', function(data) {
      // Assuming your state view returns JSON when requested via AJAX
      // Or you might need to modify your controller to return JSON
      $('#state_id').empty();
      $('#state_id').append('<option value="">Select State</option>');
      $.each(data, function(key, value) {
        $('#state_id').append('<option value="'+key+'">'+value+'</option>');
      });
    });
  });

});
</script>
@endpush
