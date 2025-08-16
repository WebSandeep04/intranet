@extends('layouts.app')

@section('title', 'Customer Projects')
@section('page_title', 'Customer Projects')

@section('content')
<div class="container mt-4">
    <div id="alertBox"></div>
    
    <button class="btn button" data-bs-toggle="modal" data-bs-target="#createCustomerProjectModal">
        <i class="bi bi-plus-lg"></i> Assign Project to Customer
    </button>
        
    <div class="table-responsive mt-3">
        <table class="table table-hover table-bordered align-middle text-center border shadow-sm rounded" id="customerProjectTable">
            <thead class="table-secondary">
                <tr>
                    <th scope="col">Customer</th>
                    <th scope="col">Project</th>
                    <th scope="col">Status</th>
                    <th scope="col">Start Date</th>
                    <th scope="col">End Date</th>
                    <th scope="col">Modules</th>
                    <th scope="col">Actions</th>
                </tr>
            </thead>
            <tbody>
                <!-- Rows will be loaded via jQuery -->
            </tbody>
        </table>
    </div>
</div>

<!-- Create Customer Project Modal -->
<div class="modal fade" id="createCustomerProjectModal" tabindex="-1" aria-labelledby="createCustomerProjectModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form id="createCustomerProjectForm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createCustomerProjectModalLabel">Assign Project to Customer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="customer_id" class="form-label">Customer *</label>
                                <select class="form-control" id="customer_id" name="customer_id" required>
                                    <option value="">Select Customer</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="project_id" class="form-label">Project *</label>
                                <select class="form-control" id="project_id" name="project_id" required>
                                    <option value="">Select Project</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="start_date" class="form-label">Start Date</label>
                                <input type="date" class="form-control" id="start_date" name="start_date">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="end_date" class="form-label">End Date</label>
                                <input type="date" class="form-control" id="end_date" name="end_date">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status *</label>
                        <select class="form-control" id="status" name="status" required>
                            <option value="pending">Pending</option>
                            <option value="in_progress">In Progress</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Select Modules *</label>
                        <div id="modules_container">
                            <p class="text-muted">Select a project first to see available modules</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn button w-100">Assign Project</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Edit Customer Project Modal -->
<div class="modal fade" id="editCustomerProjectModal" tabindex="-1" aria-labelledby="editCustomerProjectModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form id="editCustomerProjectForm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editCustomerProjectModalLabel">Edit Customer Project</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @csrf
                    <input type="hidden" id="edit_customer_project_id">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_customer_id" class="form-label">Customer *</label>
                                <select class="form-control" id="edit_customer_id" required>
                                    <option value="">Select Customer</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_project_id" class="form-label">Project *</label>
                                <select class="form-control" id="edit_project_id" required>
                                    <option value="">Select Project</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_start_date" class="form-label">Start Date</label>
                                <input type="date" class="form-control" id="edit_start_date">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_end_date" class="form-label">End Date</label>
                                <input type="date" class="form-control" id="edit_end_date">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="edit_status" class="form-label">Status *</label>
                        <select class="form-control" id="edit_status" required>
                            <option value="pending">Pending</option>
                            <option value="in_progress">In Progress</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_description" class="form-label">Description</label>
                        <textarea class="form-control" id="edit_description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn button w-100">Update Project</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Module Status Modal -->
<div class="modal fade" id="moduleStatusModal" tabindex="-1" aria-labelledby="moduleStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="moduleStatusForm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="moduleStatusModalLabel">Update Module Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @csrf
                    <input type="hidden" id="module_customer_project_id">
                    <input type="hidden" id="module_module_id">
                    <div class="mb-3">
                        <label for="module_status" class="form-label">Status *</label>
                        <select class="form-control" id="module_status" required>
                            <option value="pending">Pending</option>
                            <option value="in_progress">In Progress</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="module_start_date" class="form-label">Start Date</label>
                                <input type="date" class="form-control" id="module_start_date">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="module_end_date" class="form-label">End Date</label>
                                <input type="date" class="form-control" id="module_end_date">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="module_description" class="form-label">Description</label>
                        <textarea class="form-control" id="module_description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn button w-100">Update Status</button>
                </div>
            </div>
        </form>
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
    loadCustomerProjects();
    loadCustomers();
    loadProjects();

    function loadCustomerProjects() {
        $.get("{{ route('customer-project.fetch') }}", function (data) {
            let rows = '';
            $.each(data, function (i, cp) {
                let modulesHtml = '';
                if (cp.customer_project_modules && cp.customer_project_modules.length > 0) {
                    cp.customer_project_modules.forEach(function(cpm) {
                        let statusClass = '';
                        switch(cpm.status) {
                            case 'completed': statusClass = 'badge bg-success'; break;
                            case 'in_progress': statusClass = 'badge bg-warning'; break;
                            case 'cancelled': statusClass = 'badge bg-danger'; break;
                            default: statusClass = 'badge bg-secondary';
                        }
                        modulesHtml += `<span class="${statusClass} me-1 mb-1">${cpm.module.name}</span>`;
                    });
                } else {
                    modulesHtml = '<span class="text-muted">No modules assigned</span>';
                }

                let statusClass = '';
                switch(cp.status) {
                    case 'completed': statusClass = 'badge bg-success'; break;
                    case 'in_progress': statusClass = 'badge bg-warning'; break;
                    case 'cancelled': statusClass = 'badge bg-danger'; break;
                    default: statusClass = 'badge bg-secondary';
                }

                rows += `<tr>
                    <td>${cp.customer.name}</td>
                    <td>${cp.project.name}</td>
                    <td><span class="${statusClass}">${cp.status.replace('_', ' ')}</span></td>
                    <td>${cp.start_date || '-'}</td>
                    <td>${cp.end_date || '-'}</td>
                    <td>${modulesHtml}</td>
                    <td>
                        <button class="btn btn-sm btn-primary editBtn" data-id="${cp.id}" 
                                data-customer-id="${cp.customer_id}" data-project-id="${cp.project_id}"
                                data-start-date="${cp.start_date || ''}" data-end-date="${cp.end_date || ''}"
                                data-status="${cp.status}" data-description="${cp.description || ''}">
                            <i class="bi bi-pencil-square me-1"></i> Edit
                        </button>
                        <button class="btn btn-sm btn-danger deleteBtn" data-id="${cp.id}">
                            <i class="bi bi-trash3-fill me-1"></i> Delete
                        </button>
                    </td>
                </tr>`;
            });
            $('#customerProjectTable tbody').html(rows);
        });
    }

    function loadCustomers() {
        $.get("{{ route('customer-project.customers') }}", function (data) {
            let options = '<option value="">Select Customer</option>';
            $.each(data, function (i, customer) {
                options += `<option value="${customer.id}">${customer.name}</option>`;
            });
            $('#customer_id, #edit_customer_id').html(options);
        });
    }

    function loadProjects() {
        $.get("{{ route('customer-project.projects') }}", function (data) {
            let options = '<option value="">Select Project</option>';
            $.each(data, function (i, project) {
                options += `<option value="${project.id}">${project.name}</option>`;
            });
            $('#project_id, #edit_project_id').html(options);
        });
    }

    // Load modules when project is selected
    $('#project_id').change(function() {
        let projectId = $(this).val();
        if (projectId) {
            $.get(`/module/project/${projectId}`, function (data) {
                let modulesHtml = '';
                $.each(data, function (i, module) {
                    modulesHtml += `<div class="form-check">
                        <input class="form-check-input" type="checkbox" name="module_ids[]" value="${module.id}" id="module_${module.id}">
                        <label class="form-check-label" for="module_${module.id}">
                            ${module.name}
                        </label>
                    </div>`;
                });
                $('#modules_container').html(modulesHtml);
            });
        } else {
            $('#modules_container').html('<p class="text-muted">Select a project first to see available modules</p>');
        }
    });

    $('#createCustomerProjectForm').submit(function (e) {
        e.preventDefault();
        
        // Get selected modules
        let selectedModules = [];
        $('input[name="module_ids[]"]:checked').each(function() {
            selectedModules.push($(this).val());
        });

        if (selectedModules.length === 0) {
            alert('Please select at least one module.');
            return;
        }

        $.post("{{ route('customer-project.store') }}", {
            customer_id: $('#customer_id').val(),
            project_id: $('#project_id').val(),
            start_date: $('#start_date').val(),
            end_date: $('#end_date').val(),
            status: $('#status').val(),
            description: $('#description').val(),
            module_ids: selectedModules,
            _token: '{{ csrf_token() }}'
        }, function (response) {
            if (response.success) {
                $('#createCustomerProjectModal').modal('hide');
                $('#createCustomerProjectForm')[0].reset();
                $('#modules_container').html('<p class="text-muted">Select a project first to see available modules</p>');
                loadCustomerProjects();
                showAlert('success', 'Project assigned to customer successfully.');
            }
        }).fail(function (xhr) {
            if (xhr.responseJSON && xhr.responseJSON.errors) {
                alert(Object.values(xhr.responseJSON.errors).join("\n"));
            } else {
                alert('Error assigning project to customer.');
            }
        });
    });

    $(document).on('click', '.editBtn', function () {
        $('#edit_customer_project_id').val($(this).data('id'));
        $('#edit_customer_id').val($(this).data('customer-id'));
        $('#edit_project_id').val($(this).data('project-id'));
        $('#edit_start_date').val($(this).data('start-date'));
        $('#edit_end_date').val($(this).data('end-date'));
        $('#edit_status').val($(this).data('status'));
        $('#edit_description').val($(this).data('description'));
        $('#editCustomerProjectModal').modal('show');
    });

    $('#editCustomerProjectForm').submit(function (e) {
        e.preventDefault();
        let id = $('#edit_customer_project_id').val();
        $.ajax({
            url: `/customer-project/${id}`,
            type: 'PUT',
            data: {
                customer_id: $('#edit_customer_id').val(),
                project_id: $('#edit_project_id').val(),
                start_date: $('#edit_start_date').val(),
                end_date: $('#edit_end_date').val(),
                status: $('#edit_status').val(),
                description: $('#edit_description').val(),
                _token: '{{ csrf_token() }}'
            },
            success: function (response) {
                if (response.success) {
                    $('#editCustomerProjectModal').modal('hide');
                    loadCustomerProjects();
                    showAlert('success', 'Customer project updated successfully.');
                }
            },
            error: function (xhr) {
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    alert(Object.values(xhr.responseJSON.errors).join("\n"));
                } else {
                    alert('Error updating customer project.');
                }
            }
        });
    });

    $(document).on('click', '.deleteBtn', function () {
        if (confirm('Are you sure you want to delete this customer project?')) {
            $.ajax({
                url: `/customer-project/${$(this).data('id')}`,
                type: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: function (response) {
                    if (response.success) {
                        loadCustomerProjects();
                        showAlert('success', 'Customer project deleted successfully.');
                    }
                },
                error: function () {
                    alert('Error deleting customer project.');
                }
            });
        }
    });
});
</script>
@endpush
