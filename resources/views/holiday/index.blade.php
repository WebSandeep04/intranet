@extends('layouts.app')

@section('title', 'Holiday Management')
@section('page_title', 'Holiday Management')

@section('content')
<div class="container mt-4">
    <div id="alertBox"></div>
    
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Holidays</h5>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addHolidayModal">
                        <i class="bi bi-plus-circle"></i> Add Holiday
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered align-middle text-center border shadow-sm rounded">
                            <thead class="table-secondary">
                                <tr>
                                    <th scope="col">Holiday Name</th>
                                    <th scope="col">Date</th>
                                    <th scope="col">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="holidayTableBody">
                                <!-- Rows will be loaded via jQuery -->
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- No Data Message -->
                    <div id="noDataMessage" class="text-center py-4" style="display: none;">
                        <i class="bi bi-calendar-x fs-1 text-muted"></i>
                        <h5 class="text-muted mt-3">No holidays found</h5>
                        <p class="text-muted">Add holidays to manage worklog date validation.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Holiday Modal -->
<div class="modal fade" id="addHolidayModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Holiday</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addHolidayForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="holiday_name" class="form-label">Holiday Name</label>
                        <input type="text" class="form-control" id="holiday_name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="holiday_date" class="form-label">Holiday Date</label>
                        <input type="date" class="form-control" id="holiday_date" name="holiday_date" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Holiday</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Holiday Modal -->
<div class="modal fade" id="editHolidayModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Holiday</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editHolidayForm">
                <input type="hidden" id="edit_holiday_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_holiday_name" class="form-label">Holiday Name</label>
                        <input type="text" class="form-control" id="edit_holiday_name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_holiday_date" class="form-label">Holiday Date</label>
                        <input type="date" class="form-control" id="edit_holiday_date" name="holiday_date" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Holiday</button>
                </div>
            </form>
        </div>
    </div>
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
    setTimeout(() => $('.custom-alert').fadeOut(500, function() { $(this).remove(); }), 3000);
}

$(function () {
    loadHolidays();
});

function loadHolidays() {
    $.get("{{ route('holiday.fetch') }}", function (data) {
        if (data.length === 0) {
            $('#holidayTableBody').hide();
            $('#noDataMessage').show();
        } else {
            $('#holidayTableBody').show();
            $('#noDataMessage').hide();
            
            let rows = '';
            $.each(data, function (i, holiday) {
                rows += `<tr>
                    <td>${holiday.name}</td>
                    <td>${holiday.holiday_date}</td>
                    <td>
                        <button class="btn btn-sm btn-primary editBtn" data-id="${holiday.id}" data-name="${holiday.name}" data-date="${holiday.holiday_date}" title="Edit Holiday">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-sm btn-danger deleteBtn" data-id="${holiday.id}" title="Delete Holiday">
                            <i class="bi bi-trash3-fill"></i>
                        </button>
                    </td>
                </tr>`;
            });
            $('#holidayTableBody').html(rows);
        }
    }).fail(function () {
        showAlert('error', 'Error loading holidays.');
    });
}

// Add Holiday
$('#addHolidayForm').submit(function (e) {
    e.preventDefault();
    
    $.post("{{ route('holiday.store') }}", {
        name: $('#holiday_name').val(),
        holiday_date: $('#holiday_date').val(),
        _token: '{{ csrf_token() }}'
    }, function (response) {
        if (response.success) {
            $('#addHolidayModal').modal('hide');
            $('#addHolidayForm')[0].reset();
            loadHolidays();
            showAlert('success', response.message);
        }
    }).fail(function (xhr) {
        if (xhr.responseJSON && xhr.responseJSON.errors) {
            let errorMessage = '';
            $.each(xhr.responseJSON.errors, function (key, value) {
                errorMessage += value[0] + '\n';
            });
            showAlert('error', errorMessage);
        } else {
            showAlert('error', 'Error adding holiday.');
        }
    });
});

// Edit Holiday
$(document).on('click', '.editBtn', function () {
    const id = $(this).data('id');
    const name = $(this).data('name');
    const date = $(this).data('date');
    
    $('#edit_holiday_id').val(id);
    $('#edit_holiday_name').val(name);
    $('#edit_holiday_date').val(date);
    $('#editHolidayModal').modal('show');
});

$('#editHolidayForm').submit(function (e) {
    e.preventDefault();
    
    const id = $('#edit_holiday_id').val();
    
    $.ajax({
        url: `/holiday/${id}`,
        type: 'PUT',
        data: {
            name: $('#edit_holiday_name').val(),
            holiday_date: $('#edit_holiday_date').val(),
            _token: '{{ csrf_token() }}'
        },
        success: function (response) {
            if (response.success) {
                $('#editHolidayModal').modal('hide');
                loadHolidays();
                showAlert('success', response.message);
            }
        },
        error: function (xhr) {
            if (xhr.responseJSON && xhr.responseJSON.errors) {
                let errorMessage = '';
                $.each(xhr.responseJSON.errors, function (key, value) {
                    errorMessage += value[0] + '\n';
                });
                showAlert('error', errorMessage);
            } else {
                showAlert('error', 'Error updating holiday.');
            }
        }
    });
});

// Delete Holiday
$(document).on('click', '.deleteBtn', function () {
    const holidayId = $(this).data('id');
    
    if (confirm('Are you sure you want to delete this holiday?')) {
        $.ajax({
            url: `/holiday/${holidayId}`,
            type: 'DELETE',
            data: { _token: '{{ csrf_token() }}' },
            success: function (response) {
                if (response.success) {
                    loadHolidays();
                    showAlert('success', response.message);
                }
            },
            error: function () {
                showAlert('error', 'Error deleting holiday.');
            }
        });
    }
});
</script>
@endpush
