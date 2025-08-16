@extends('layouts.app')

@section('title', 'Project Management')
@section('page_title', 'Project Management')

@section('content')
<div class="container mt-4">
    <div id="alertBox"></div>
    
    <button class="btn button" data-bs-toggle="modal" data-bs-target="#createProjectModal">
        <i class="bi bi-plus-lg"></i> Create Project
    </button>
        
    <div class="table-responsive mt-3">
        <table class="table table-hover table-bordered align-middle text-center border shadow-sm rounded" id="projectTable">
            <thead class="table-secondary">
                <tr>
                    <th scope="col">Project Name</th>
                    <th scope="col">Description</th>
                    <th scope="col">Modules Count</th>
                    <th scope="col">Actions</th>
                </tr>
            </thead>
            <tbody>
                <!-- Rows will be loaded via jQuery -->
            </tbody>
        </table>
    </div>
</div>

<!-- Create Project Modal -->
<div class="modal fade" id="createProjectModal" tabindex="-1" aria-labelledby="createProjectModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="createProjectForm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createProjectModalLabel">Create Project</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @csrf
                    <div class="mb-3">
                        <label for="name" class="form-label">Project Name *</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn button w-100">Create Project</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Edit Project Modal -->
<div class="modal fade" id="editProjectModal" tabindex="-1" aria-labelledby="editProjectModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="editProjectForm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editProjectModalLabel">Edit Project</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @csrf
                    <input type="hidden" id="edit_project_id">
                    <div class="mb-3">
                        <label for="edit_name" class="form-label">Project Name *</label>
                        <input type="text" class="form-control" id="edit_name" required>
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
    loadProjects();

    function loadProjects() {
        $.get("{{ route('project.fetch') }}", function (data) {
            let rows = '';
            $.each(data, function (i, project) {
                rows += `<tr>
                    <td>${project.name}</td>
                    <td>${project.description || '-'}</td>
                    <td>${project.modules ? project.modules.length : 0}</td>
                    <td>
                        <button class="btn btn-sm btn-primary editBtn" data-id="${project.id}" 
                                data-name="${project.name}" data-description="${project.description || ''}">
                            <i class="bi bi-pencil-square me-1"></i> Edit
                        </button>
                        <button class="btn btn-sm btn-danger deleteBtn" data-id="${project.id}">
                            <i class="bi bi-trash3-fill me-1"></i> Delete
                        </button>
                    </td>
                </tr>`;
            });
            $('#projectTable tbody').html(rows);
        });
    }

    $('#createProjectForm').submit(function (e) {
        e.preventDefault();
        $.post("{{ route('project.store') }}", {
            name: $('#name').val(),
            description: $('#description').val(),
            _token: '{{ csrf_token() }}'
        }, function (response) {
            if (response.success) {
                $('#createProjectModal').modal('hide');
                $('#createProjectForm')[0].reset();
                loadProjects();
                showAlert('success', 'Project created successfully.');
            }
        }).fail(function (xhr) {
            if (xhr.responseJSON && xhr.responseJSON.errors) {
                alert(Object.values(xhr.responseJSON.errors).join("\n"));
            } else {
                alert('Error creating project.');
            }
        });
    });

    $(document).on('click', '.editBtn', function () {
        $('#edit_project_id').val($(this).data('id'));
        $('#edit_name').val($(this).data('name'));
        $('#edit_description').val($(this).data('description'));
        $('#editProjectModal').modal('show');
    });

    $('#editProjectForm').submit(function (e) {
        e.preventDefault();
        let id = $('#edit_project_id').val();
        $.ajax({
            url: `/project/${id}`,
            type: 'PUT',
            data: {
                name: $('#edit_name').val(),
                description: $('#edit_description').val(),
                _token: '{{ csrf_token() }}'
            },
            success: function (response) {
                if (response.success) {
                    $('#editProjectModal').modal('hide');
                    loadProjects();
                    showAlert('success', 'Project updated successfully.');
                }
            },
            error: function (xhr) {
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    alert(Object.values(xhr.responseJSON.errors).join("\n"));
                } else {
                    alert('Error updating project.');
                }
            }
        });
    });

    $(document).on('click', '.deleteBtn', function () {
        if (confirm('Are you sure you want to delete this project?')) {
            $.ajax({
                url: `/project/${$(this).data('id')}`,
                type: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: function (response) {
                    if (response.success) {
                        loadProjects();
                        showAlert('success', 'Project deleted successfully.');
                    }
                },
                error: function () {
                    alert('Error deleting project.');
                }
            });
        }
    });
});
</script>
@endpush
