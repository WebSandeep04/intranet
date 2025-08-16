@extends('layouts.app')

@section('title', 'Tenant Management')
@section('page_title', 'Tenant Management')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Add New Tenant</h5>
                </div>
                <div class="card-body">
                    <form id="tenantForm">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tenant_name">Tenant Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="tenant_name" name="tenant_name" required>
                                    <div class="invalid-feedback" id="tenant_name_error"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <button type="submit" class="btn btn-primary btn-block">
                                        <i class="fas fa-plus"></i> Add Tenant
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Tenant List</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped" id="tenantTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Tenant Name</th>
                                    <th>Tenant Code</th>
                                    <th>Created Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="tenantTableBody">
                                <!-- Data will be loaded here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Tenant Modal -->
<div class="modal fade" id="editTenantModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Tenant</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="editTenantForm">
                @csrf
                <div class="modal-body">
                    <input type="hidden" id="edit_tenant_id" name="tenant_id">
                    <div class="form-group">
                        <label for="edit_tenant_name">Tenant Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_tenant_name" name="tenant_name" required>
                        <div class="invalid-feedback" id="edit_tenant_name_error"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Tenant</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Load tenants on page load
    loadTenants();

    // Handle form submission
    $('#tenantForm').on('submit', function(e) {
        e.preventDefault();
        addTenant();
    });

    // Handle edit form submission
    $('#editTenantForm').on('submit', function(e) {
        e.preventDefault();
        updateTenant();
    });

    function loadTenants() {
        $.ajax({
            url: '{{ route("tenant.fetch") }}',
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    displayTenants(response.data);
                } else {
                    showAlert('Error loading tenants', 'error');
                }
            },
            error: function() {
                showAlert('Error loading tenants', 'error');
            }
        });
    }

    function displayTenants(tenants) {
        let html = '';
        if (tenants.length === 0) {
            html = '<tr><td colspan="5" class="text-center">No tenants found</td></tr>';
        } else {
            tenants.forEach(function(tenant, index) {
                html += `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${tenant.tenant_name}</td>
                        <td>
                            <span class="badge badge-info">${tenant.tenant_code}</span>
                        </td>
                        <td>${new Date(tenant.created_at).toLocaleDateString()}</td>
                        <td>
                            <button class="btn btn-sm btn-primary" onclick="editTenant(${tenant.id}, '${tenant.tenant_name}')">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-warning" onclick="regenerateCode(${tenant.id})">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="deleteTenant(${tenant.id})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
            });
        }
        $('#tenantTableBody').html(html);
    }

    function addTenant() {
        const formData = new FormData($('#tenantForm')[0]);
        
        $.ajax({
            url: '{{ route("tenant.store") }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    showAlert(response.message, 'success');
                    $('#tenantForm')[0].reset();
                    loadTenants();
                } else {
                    showValidationErrors(response.errors);
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    showValidationErrors(xhr.responseJSON.errors);
                } else {
                    showAlert('Error adding tenant', 'error');
                }
            }
        });
    }

    function updateTenant() {
        const tenantId = $('#edit_tenant_id').val();
        const formData = new FormData($('#editTenantForm')[0]);
        
        $.ajax({
            url: `/tenant/${tenantId}`,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-HTTP-Method-Override': 'PUT'
            },
            success: function(response) {
                if (response.success) {
                    showAlert(response.message, 'success');
                    $('#editTenantModal').modal('hide');
                    loadTenants();
                } else {
                    showValidationErrors(response.errors, 'edit_');
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    showValidationErrors(xhr.responseJSON.errors, 'edit_');
                } else {
                    showAlert('Error updating tenant', 'error');
                }
            }
        });
    }

    function deleteTenant(tenantId) {
        if (confirm('Are you sure you want to delete this tenant?')) {
            $.ajax({
                url: `/tenant/${tenantId}`,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        showAlert(response.message, 'success');
                        loadTenants();
                    } else {
                        showAlert('Error deleting tenant', 'error');
                    }
                },
                error: function() {
                    showAlert('Error deleting tenant', 'error');
                }
            });
        }
    }

    function regenerateCode(tenantId) {
        if (confirm('Are you sure you want to regenerate the tenant code?')) {
            $.ajax({
                url: `/tenant/${tenantId}/regenerate-code`,
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        showAlert(response.message, 'success');
                        loadTenants();
                    } else {
                        showAlert('Error regenerating code', 'error');
                    }
                },
                error: function() {
                    showAlert('Error regenerating code', 'error');
                }
            });
        }
    }

    function showValidationErrors(errors, prefix = '') {
        // Clear previous errors
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').text('');

        // Show new errors
        Object.keys(errors).forEach(function(key) {
            const fieldId = prefix + key;
            $(`#${fieldId}`).addClass('is-invalid');
            $(`#${fieldId}_error`).text(errors[key][0]);
        });
    }

    function showAlert(message, type) {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const alertHtml = `
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
        `;
        
        // Remove existing alerts
        $('.alert').remove();
        
        // Add new alert at the top of the container
        $('.container').prepend(alertHtml);
        
        // Auto-hide after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut();
        }, 5000);
    }
});

// Global functions for onclick handlers
function editTenant(tenantId, tenantName) {
    $('#edit_tenant_id').val(tenantId);
    $('#edit_tenant_name').val(tenantName);
    $('#editTenantModal').modal('show');
}

function deleteTenant(tenantId) {
    if (confirm('Are you sure you want to delete this tenant?')) {
        $.ajax({
            url: `/tenant/${tenantId}`,
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    showAlert(response.message, 'success');
                    loadTenants();
                } else {
                    showAlert('Error deleting tenant', 'error');
                }
            },
            error: function() {
                showAlert('Error deleting tenant', 'error');
            }
        });
    }
}

function regenerateCode(tenantId) {
    if (confirm('Are you sure you want to regenerate the tenant code?')) {
        $.ajax({
            url: `/tenant/${tenantId}/regenerate-code`,
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    showAlert(response.message, 'success');
                    loadTenants();
                } else {
                    showAlert('Error regenerating code', 'error');
                }
            },
            error: function() {
                showAlert('Error regenerating code', 'error');
            }
        });
    }
}
</script>
@endpush
