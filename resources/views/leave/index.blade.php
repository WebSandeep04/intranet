@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Leave Management</h4>
                    <button type="button" class="btn btn-primary" onclick="openCreateModal()">
                        <i class="fas fa-plus me-2"></i>Apply Leave
                    </button>
                </div>
                <div class="card-body">
                    <div id="alertContainer"></div>
                    
                    <div class="table-responsive">
                        <table class="table table-striped" id="leavesTable">
                                                         <thead>
                                 <tr>
                                     <th>Date</th>
                                     <th>Leave Type</th>
                                     <th>Reason</th>
                                     <th>Actions</th>
                                 </tr>
                             </thead>
                            <tbody id="leavesTableBody">
                                                                 <tr>
                                     <td colspan="4" class="text-center text-muted">
                                         <div class="spinner-border spinner-border-sm text-primary me-2" role="status">
                                             <span class="visually-hidden">Loading...</span>
                                         </div>
                                         Loading leaves...
                                     </td>
                                 </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create/Edit Modal -->
<div class="modal fade" id="leaveModal" tabindex="-1" aria-labelledby="leaveModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="leaveModalLabel">Apply Leave</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="leaveForm">
                <div class="modal-body">
                    <input type="hidden" id="leaveId" name="id">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="date" class="form-label">Leave Date <span class="text-danger">*</span></label>
                                <input type="date" 
                                       class="form-control" 
                                       id="date" 
                                       name="date" 
                                       required>
                                <div class="invalid-feedback" id="dateError"></div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="leave_type_id" class="form-label">Leave Type <span class="text-danger">*</span></label>
                                <select class="form-control" 
                                        id="leave_type_id" 
                                        name="leave_type_id" 
                                        required>
                                    <option value="">Select Leave Type</option>
                                </select>
                                <div class="invalid-feedback" id="leave_type_idError"></div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="reason" class="form-label">Reason</label>
                        <textarea class="form-control" 
                                  id="reason" 
                                  name="reason" 
                                  rows="3" 
                                  placeholder="Optional reason for leave"></textarea>
                        <div class="invalid-feedback" id="reasonError"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <i class="fas fa-save me-2"></i>Apply Leave
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this leave application?</p>
                <p class="text-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    This action cannot be undone.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                    <i class="fas fa-trash me-2"></i>Delete
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentLeaveId = null;
let deleteLeaveId = null;

$(document).ready(function() {
    console.log('Leave page loaded');
    loadLeaveTypes();
    loadLeaves();
    
    // Handle form submission
    $('#leaveForm').on('submit', function(e) {
        e.preventDefault();
        submitForm();
    });
    
    // Handle delete confirmation
    $('#confirmDeleteBtn').on('click', function() {
        deleteLeave();
    });
    
    // Handle edit and delete button clicks using event delegation
    $(document).on('click', '.edit-btn', function() {
        const id = $(this).data('id');
        const date = $(this).data('date');
        const typeId = $(this).data('type-id');
        const reason = $(this).data('reason');
        openEditModal(id, date, typeId, reason);
    });
    
    $(document).on('click', '.delete-btn', function() {
        const id = $(this).data('id');
        openDeleteModal(id);
    });
    
    // Debug: Check if modals exist
    console.log('Create/Edit Modal exists:', $('#leaveModal').length > 0);
    console.log('Delete Modal exists:', $('#deleteModal').length > 0);
});

function loadLeaveTypes() {
    $.ajax({
        url: '{{ route("leave.types") }}',
        method: 'GET',
        success: function(response) {
            if (response.data) {
                const select = $('#leave_type_id');
                select.find('option:not(:first)').remove();
                
                response.data.forEach(function(type) {
                    select.append(`<option value="${type.id}">${type.name}</option>`);
                });
            }
        },
        error: function(xhr) {
            console.error('Failed to load leave types:', xhr);
            showAlert('error', 'Failed to load leave types');
        }
    });
}

function loadLeaves() {
    $.ajax({
        url: '{{ route("leave.fetch") }}',
        method: 'GET',
        success: function(response) {
            if (response.data) {
                displayLeaves(response.data);
            } else {
                showAlert('error', 'Invalid response format from server');
            }
        },
        error: function(xhr) {
            console.error('AJAX Error:', xhr);
            let errorMessage = 'Failed to load leaves. Please try again.';
            if (xhr.responseJSON && xhr.responseJSON.error) {
                errorMessage = xhr.responseJSON.error;
            }
            showAlert('error', errorMessage);
        }
    });
}

function displayLeaves(leaves) {
    const tbody = $('#leavesTableBody');
    
         if (leaves.length === 0) {
         tbody.html(`
             <tr>
                 <td colspan="4" class="text-center text-muted">
                     <i class="fas fa-info-circle me-2"></i>
                     No leaves found. Apply your first leave to get started.
                 </td>
             </tr>
         `);
         return;
     }
    
    let html = '';
    leaves.forEach(function(leave) {
        html += `
            <tr>
                <td>${formatDate(leave.date)}</td>
                <td>${leave.leave_type ? leave.leave_type.name : 'Unknown'}</td>
                <td>${leave.reason || 'No reason provided'}</td>
                <td>
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-sm btn-warning edit-btn" 
                                data-id="${leave.id}" data-date="${leave.date}" 
                                data-type-id="${leave.leave_type_id}" data-reason="${leave.reason || ''}">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-danger delete-btn" data-id="${leave.id}">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    });
    
    tbody.html(html);
}

function openCreateModal() {
    console.log('openCreateModal called');
    
    currentLeaveId = null;
    $('#leaveModalLabel').text('Apply Leave');
    $('#submitBtn').html('<i class="fas fa-save me-2"></i>Apply Leave');
    $('#leaveForm')[0].reset();
    clearErrors();
    
    // Set default date to today
    $('#date').val(new Date().toISOString().split('T')[0]);
    
    console.log('Modal element:', document.getElementById('leaveModal'));
    console.log('Bootstrap available:', typeof bootstrap !== 'undefined');
    
    try {
        const modal = new bootstrap.Modal(document.getElementById('leaveModal'));
        console.log('Modal instance created:', modal);
        modal.show();
        console.log('Modal show() called');
    } catch (error) {
        console.error('Error opening create modal:', error);
        // Fallback to jQuery method
        console.log('Trying jQuery fallback...');
        $('#leaveModal').modal('show');
    }
}

function openEditModal(id, date, typeId, reason) {
    console.log('openEditModal called with:', { id, date, typeId, reason });
    
    currentLeaveId = id;
    $('#leaveModalLabel').text('Edit Leave');
    $('#submitBtn').html('<i class="fas fa-save me-2"></i>Update Leave');
    
    $('#leaveId').val(id);
    $('#date').val(date);
    $('#leave_type_id').val(typeId);
    $('#reason').val(reason);
    clearErrors();
    
    console.log('Modal element:', document.getElementById('leaveModal'));
    console.log('Bootstrap available:', typeof bootstrap !== 'undefined');
    
    try {
        const modal = new bootstrap.Modal(document.getElementById('leaveModal'));
        console.log('Modal instance created:', modal);
        modal.show();
        console.log('Modal show() called');
    } catch (error) {
        console.error('Error opening edit modal:', error);
        // Fallback to jQuery method
        console.log('Trying jQuery fallback...');
        $('#leaveModal').modal('show');
    }
}

function openDeleteModal(id) {
    deleteLeaveId = id;
    
    try {
        const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
        modal.show();
    } catch (error) {
        console.error('Error opening delete modal:', error);
        // Fallback to jQuery method
        $('#deleteModal').modal('show');
    }
}

function submitForm() {
    const formData = {
        date: $('#date').val(),
        leave_type_id: $('#leave_type_id').val(),
        reason: $('#reason').val(),
        _token: '{{ csrf_token() }}'
    };
    
    const url = currentLeaveId 
        ? `/leave/${currentLeaveId}`
        : '{{ route("leave.store") }}';
    
    const method = currentLeaveId ? 'PUT' : 'POST';
    
    if (method === 'PUT') {
        formData._method = 'PUT';
    }
    
    $.ajax({
        url: url,
        method: method,
        data: formData,
        success: function(response) {
            if (response.success) {
                showAlert('success', response.message);
                try {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('leaveModal'));
                    if (modal) {
                        modal.hide();
                    } else {
                        $('#leaveModal').modal('hide');
                    }
                } catch (error) {
                    $('#leaveModal').modal('hide');
                }
                loadLeaves();
            }
        },
        error: function(xhr) {
            if (xhr.status === 422) {
                displayErrors(xhr.responseJSON.errors);
            } else {
                showAlert('error', xhr.responseJSON?.message || 'An error occurred. Please try again.');
            }
        }
    });
}

function deleteLeave() {
    $.ajax({
        url: `/leave/${deleteLeaveId}`,
        method: 'DELETE',
        data: {
            _token: '{{ csrf_token() }}',
            _method: 'DELETE'
        },
        success: function(response) {
            if (response.success) {
                showAlert('success', response.message);
                try {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('deleteModal'));
                    if (modal) {
                        modal.hide();
                    } else {
                        $('#deleteModal').modal('hide');
                    }
                } catch (error) {
                    $('#deleteModal').modal('hide');
                }
                loadLeaves();
            }
        },
        error: function(xhr) {
            if (xhr.status === 422) {
                showAlert('error', xhr.responseJSON.message);
            } else {
                showAlert('error', 'An error occurred. Please try again.');
            }
        }
    });
}

function displayErrors(errors) {
    clearErrors();
    
    Object.keys(errors).forEach(function(field) {
        const errorElement = $(`#${field}Error`);
        const inputElement = $(`#${field}`);
        
        if (errorElement.length && inputElement.length) {
            errorElement.text(errors[field][0]);
            inputElement.addClass('is-invalid');
        }
    });
}

function clearErrors() {
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').text('');
}

function showAlert(type, message) {
    let alertClass = 'alert-info';
    
    if (type === 'success') {
        alertClass = 'alert-success';
    } else if (type === 'error') {
        alertClass = 'alert-danger';
    } else if (type === 'warning') {
        alertClass = 'alert-warning';
    }
    
    const alertHtml = `
        <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    $('#alertContainer').html(alertHtml);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        $('#alertContainer .alert').fadeOut();
    }, 5000);
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        weekday: 'short',
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
}
</script>
@endpush
