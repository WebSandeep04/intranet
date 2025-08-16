@extends('layouts.app')

@section('title', 'Module Management')
@section('page_title', 'Module Management')

@section('content')
<div class="container mt-4">
    <div id="alertBox"></div>
    
    <button class="btn button" data-bs-toggle="modal" data-bs-target="#createModuleModal">
        <i class="bi bi-plus-lg"></i> Create Module
    </button>
        
    <div class="table-responsive mt-3">
        <table class="table table-hover table-bordered align-middle text-center border shadow-sm rounded" id="moduleTable">
            <thead class="table-secondary">
                <tr>
                    <th scope="col">Module Name</th>
                    <th scope="col">Project</th>
                    <th scope="col">Description</th>
                    <th scope="col">Actions</th>
                </tr>
            </thead>
            <tbody>
                <!-- Rows will be loaded via jQuery -->
            </tbody>
        </table>
    </div>
</div>

<!-- Create Module Modal -->
<div class="modal fade" id="createModuleModal" tabindex="-1" aria-labelledby="createModuleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="createModuleForm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createModuleModalLabel">Create Module</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @csrf
                    <div class="mb-3">
                        <label for="name" class="form-label">Module Name *</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="project_id" class="form-label">Project *</label>
                        <select class="form-control" id="project_id" name="project_id" required>
                            <option value="">Select Project</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn button w-100">Create Module</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Edit Module Modal -->
<div class="modal fade" id="editModuleModal" tabindex="-1" aria-labelledby="editModuleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="editModuleForm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModuleModalLabel">Edit Module</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @csrf
                    <input type="hidden" id="edit_module_id">
                    <div class="mb-3">
                        <label for="edit_name" class="form-label">Module Name *</label>
                        <input type="text" class="form-control" id="edit_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_project_id" class="form-label">Project *</label>
                        <select class="form-control" id="edit_project_id" required>
                            <option value="">Select Project</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_description" class="form-label">Description</label>
                        <textarea class="form-control" id="edit_description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn button w-100">Update Module</button>
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
    loadModules();
    loadProjects();

    function loadModules() {
        $.get("{{ route('module.fetch') }}", function (data) {
            let rows = '';
            $.each(data, function (i, module) {
                rows += `<tr>
                    <td>${module.name}</td>
                    <td>${module.project ? module.project.name : '-'}</td>
                    <td>${module.description || '-'}</td>
                    <td>
                        <button class="btn btn-sm btn-primary editBtn" data-id="${module.id}" 
                                data-name="${module.name}" data-project-id="${module.project_id}" 
                                data-description="${module.description || ''}">
                            <i class="bi bi-pencil-square me-1"></i> Edit
                        </button>
                        <button class="btn btn-sm btn-danger deleteBtn" data-id="${module.id}">
                            <i class="bi bi-trash3-fill me-1"></i> Delete
                        </button>
                    </td>
                </tr>`;
            });
            $('#moduleTable tbody').html(rows);
        });
    }

    function loadProjects() {
        $.get("{{ route('project.fetch') }}", function (data) {
            let options = '<option value="">Select Project</option>';
            $.each(data, function (i, project) {
                options += `<option value="${project.id}">${project.name}</option>`;
            });
            $('#project_id, #edit_project_id').html(options);
        });
    }

    $('#createModuleForm').submit(function (e) {
        e.preventDefault();
        $.post("{{ route('module.store') }}", {
            name: $('#name').val(),
            project_id: $('#project_id').val(),
            description: $('#description').val(),
            _token: '{{ csrf_token() }}'
        }, function (response) {
            if (response.success) {
                $('#createModuleModal').modal('hide');
                $('#createModuleForm')[0].reset();
                loadModules();
                showAlert('success', 'Module created successfully.');
            }
        }).fail(function (xhr) {
            if (xhr.responseJSON && xhr.responseJSON.errors) {
                alert(Object.values(xhr.responseJSON.errors).join("\n"));
            } else {
                alert('Error creating module.');
            }
        });
    });

    $(document).on('click', '.editBtn', function () {
        $('#edit_module_id').val($(this).data('id'));
        $('#edit_name').val($(this).data('name'));
        $('#edit_project_id').val($(this).data('project-id'));
        $('#edit_description').val($(this).data('description'));
        $('#editModuleModal').modal('show');
    });

    $('#editModuleForm').submit(function (e) {
        e.preventDefault();
        let id = $('#edit_module_id').val();
        $.ajax({
            url: `/module/${id}`,
            type: 'PUT',
            data: {
                name: $('#edit_name').val(),
                project_id: $('#edit_project_id').val(),
                description: $('#edit_description').val(),
                _token: '{{ csrf_token() }}'
            },
            success: function (response) {
                if (response.success) {
                    $('#editModuleModal').modal('hide');
                    loadModules();
                    showAlert('success', 'Module updated successfully.');
                }
            },
            error: function (xhr) {
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    alert(Object.values(xhr.responseJSON.errors).join("\n"));
                } else {
                    alert('Error updating module.');
                }
            }
        });
    });

    $(document).on('click', '.deleteBtn', function () {
        if (confirm('Are you sure you want to delete this module?')) {
            $.ajax({
                url: `/module/${$(this).data('id')}`,
                type: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: function (response) {
                    if (response.success) {
                        loadModules();
                        showAlert('success', 'Module deleted successfully.');
                    }
                },
                error: function () {
                    alert('Error deleting module.');
                }
            });
        }
    });
});
</script>
@endpush
