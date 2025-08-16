@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Entry Types</h4>
                    <button type="button" class="btn btn-primary" onclick="openCreateModal()">
                        <i class="fas fa-plus me-2"></i>Add New Entry Type
                    </button>
                </div>
                <div class="card-body">
                    <div id="alertContainer"></div>
                    
                    <div class="table-responsive">
                        <table class="table table-striped" id="entryTypesTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Working Hours</th>
                                    <th>Description</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="entryTypesTableBody">
                                <tr>
                                    <td colspan="6" class="text-center text-muted">
                                        <div class="spinner-border spinner-border-sm text-primary me-2" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        Loading entry types...
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
<div class="modal fade" id="entryTypeModal" tabindex="-1" aria-labelledby="entryTypeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="entryTypeModalLabel">Create New Entry Type</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="entryTypeForm">
                <div class="modal-body">
                    <input type="hidden" id="entryTypeId" name="id">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">Entry Type Name <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control" 
                                       id="name" 
                                       name="name" 
                                       placeholder="e.g., Full Day, Half Day, Leave"
                                       required>
                                <div class="invalid-feedback" id="nameError"></div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="working_hours" class="form-label">Working Hours <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" 
                                           class="form-control" 
                                           id="working_hours" 
                                           name="working_hours" 
                                           min="0" 
                                           max="24" 
                                           placeholder="8"
                                           required>
                                    <span class="input-group-text">hours</span>
                                </div>
                                <div class="invalid-feedback" id="working_hoursError"></div>
                                <small class="form-text text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Enter the number of working hours for this entry type (0-24)
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" 
                                  id="description" 
                                  name="description" 
                                  rows="3" 
                                  placeholder="Optional description for this entry type"></textarea>
                        <div class="invalid-feedback" id="descriptionError"></div>
                        <small class="form-text text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            Optional description to help users understand this entry type
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <i class="fas fa-save me-2"></i>Create Entry Type
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
                <p>Are you sure you want to delete the entry type "<strong id="deleteEntryTypeName"></strong>"?</p>
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
let currentEntryTypeId = null;
let deleteEntryTypeId = null;

$(document).ready(function() {
    console.log('Entry Types page loaded');
    loadEntryTypes();
    
    // Handle form submission
    $('#entryTypeForm').on('submit', function(e) {
        e.preventDefault();
        submitForm();
    });
    
    // Handle delete confirmation
    $('#confirmDeleteBtn').on('click', function() {
        deleteEntryType();
    });
    
    // Handle edit and delete button clicks using event delegation
    $(document).on('click', '.edit-btn', function() {
        const id = $(this).data('id');
        const name = $(this).data('name');
        const hours = $(this).data('hours');
        const description = $(this).data('description');
        openEditModal(id, name, hours, description);
    });
    
    $(document).on('click', '.delete-btn', function() {
        const id = $(this).data('id');
        const name = $(this).data('name');
        openDeleteModal(id, name);
    });
    
    // Debug: Check if modals exist
    console.log('Create/Edit Modal exists:', $('#entryTypeModal').length > 0);
    console.log('Delete Modal exists:', $('#deleteModal').length > 0);
});

function loadEntryTypes() {
    $.ajax({
        url: '{{ route("entry-type.fetch") }}',
        method: 'GET',
        success: function(response) {
            if (response.data) {
                displayEntryTypes(response.data);
                
                // Log debug info if available
                if (response.debug) {
                    console.log('Debug info:', response.debug);
                }
                
                // Show warning if user has no tenant_id
                if (response.warning) {
                    showAlert('warning', response.warning);
                }
            } else {
                showAlert('error', 'Invalid response format from server');
            }
        },
        error: function(xhr) {
            console.error('AJAX Error:', xhr);
            let errorMessage = 'Failed to load entry types. Please try again.';
            if (xhr.responseJSON && xhr.responseJSON.error) {
                errorMessage = xhr.responseJSON.error;
            }
            showAlert('error', errorMessage);
        }
    });
}

function displayEntryTypes(entryTypes) {
    const tbody = $('#entryTypesTableBody');
    
    if (entryTypes.length === 0) {
        tbody.html(`
            <tr>
                <td colspan="6" class="text-center text-muted">
                    <i class="fas fa-info-circle me-2"></i>
                    No entry types found. Create your first entry type to get started.
                </td>
            </tr>
        `);
        return;
    }
    
    let html = '';
    entryTypes.forEach(function(entryType) {
        html += `
            <tr>
                <td>${entryType.id}</td>
                <td>${entryType.name}</td>
                <td>${entryType.working_hours} hours</td>
                <td>${entryType.description || 'No description'}</td>
                <td>${formatDate(entryType.created_at)}</td>
                <td>
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-sm btn-warning edit-btn" data-id="${entryType.id}" data-name="${entryType.name}" data-hours="${entryType.working_hours}" data-description="${entryType.description || ''}">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-danger delete-btn" data-id="${entryType.id}" data-name="${entryType.name}">
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
    
    currentEntryTypeId = null;
    $('#entryTypeModalLabel').text('Create New Entry Type');
    $('#submitBtn').html('<i class="fas fa-save me-2"></i>Create Entry Type');
    $('#entryTypeForm')[0].reset();
    clearErrors();
    
    console.log('Modal element:', document.getElementById('entryTypeModal'));
    console.log('Bootstrap available:', typeof bootstrap !== 'undefined');
    
    try {
        const modal = new bootstrap.Modal(document.getElementById('entryTypeModal'));
        console.log('Modal instance created:', modal);
        modal.show();
        console.log('Modal show() called');
    } catch (error) {
        console.error('Error opening create modal:', error);
        // Fallback to jQuery method
        console.log('Trying jQuery fallback...');
        $('#entryTypeModal').modal('show');
    }
}

function openEditModal(id, name, workingHours, description) {
    console.log('openEditModal called with:', { id, name, workingHours, description });
    
    currentEntryTypeId = id;
    $('#entryTypeModalLabel').text('Edit Entry Type');
    $('#submitBtn').html('<i class="fas fa-save me-2"></i>Update Entry Type');
    
    $('#entryTypeId').val(id);
    $('#name').val(name);
    $('#working_hours').val(workingHours);
    $('#description').val(description);
    clearErrors();
    
    console.log('Modal element:', document.getElementById('entryTypeModal'));
    console.log('Bootstrap available:', typeof bootstrap !== 'undefined');
    
    try {
        const modal = new bootstrap.Modal(document.getElementById('entryTypeModal'));
        console.log('Modal instance created:', modal);
        modal.show();
        console.log('Modal show() called');
    } catch (error) {
        console.error('Error opening edit modal:', error);
        // Fallback to jQuery method
        console.log('Trying jQuery fallback...');
        $('#entryTypeModal').modal('show');
    }
}

function openDeleteModal(id, name) {
    deleteEntryTypeId = id;
    $('#deleteEntryTypeName').text(name);
    
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
        name: $('#name').val(),
        working_hours: $('#working_hours').val(),
        description: $('#description').val(),
        _token: '{{ csrf_token() }}'
    };
    
    const url = currentEntryTypeId 
        ? `/entry-type/${currentEntryTypeId}`
        : '{{ route("entry-type.store") }}';
    
    const method = currentEntryTypeId ? 'PUT' : 'POST';
    
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
                    const modal = bootstrap.Modal.getInstance(document.getElementById('entryTypeModal'));
                    if (modal) {
                        modal.hide();
                    } else {
                        $('#entryTypeModal').modal('hide');
                    }
                } catch (error) {
                    $('#entryTypeModal').modal('hide');
                }
                loadEntryTypes();
            }
        },
        error: function(xhr) {
            if (xhr.status === 422) {
                displayErrors(xhr.responseJSON.errors);
            } else {
                showAlert('error', 'An error occurred. Please try again.');
            }
        }
    });
}

function deleteEntryType() {
    $.ajax({
        url: `/entry-type/${deleteEntryTypeId}`,
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
                loadEntryTypes();
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
        month: 'short',
        day: 'numeric',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

// Auto-format working hours input
$('#working_hours').on('input', function() {
    let value = parseInt($(this).val());
    if (value < 0) $(this).val(0);
    if (value > 24) $(this).val(24);
});
</script>
@endpush
