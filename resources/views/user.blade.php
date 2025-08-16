@extends('layouts.app')

@section('title', 'Sales Product')
@section('page_title', 'Sales Product')

@section('content')
<div class="container mt-4">
    <button class="btn button" data-bs-toggle="modal" data-bs-target="#createUserModal">
            <i class="bi bi-plus-lg"></i> Create User
        </button>

  <div class="table-responsive">
    <table class="table table-hover table-bordered align-middle text-center border shadow-sm rounded" id="usersTable">
      <thead class="table-secondary">
        <tr>
          <th scope="col">ID</th>
          <th scope="col">Name</th>
          <th scope="col">Role</th>
          <th scope="col">Email</th>
          <th scope="col">Manager</th>
          <th scope="col">Worklog</th>
          <th scope="col">Actions</th>
        </tr>
      </thead>
      <tbody>
        <!-- Rows will be loaded via jQuery -->
      </tbody>
    </table>
  </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="editUserForm">
        <div class="modal-header">
          <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <input type="hidden" id="edit_user_id">
            <div class="mb-3">
                <label for="edit_name" class="form-label">Name</label>
                <input type="text" class="form-control" id="edit_name" name="name" required>
            </div>
            <div class="mb-3">
                <label for="edit_email" class="form-label">Email</label>
                <input type="email" class="form-control" id="edit_email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="edit_role" class="form-label">Role</label>
                <select class="form-select" id="edit_role" name="role_id" required>
                    <!-- Load roles via JS -->
                </select>
            </div>
            <div class="mb-3">
                <label for="edit_manager" class="form-label">Manager</label>
                <select class="form-select" id="edit_manager" name="is_manager">
                    <option value="">Select Manager (Optional)</option>
                    <!-- Load users via JS -->
                </select>
            </div>
            <div class="mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="edit_is_worklog" name="is_worklog">
                    <label class="form-check-label" for="edit_is_worklog">
                        Enable Worklog Access
                    </label>
                </div>
            </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Update</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Create User Modal -->
<div class="modal fade" id="createUserModal" tabindex="-1" aria-labelledby="createUserModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="createUserForm">
        <div class="modal-header">
          <h5 class="modal-title" id="createUserModalLabel">Create User</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="mb-3">
                <label for="create_name" class="form-label">Name</label>
                <input type="text" class="form-control" id="create_name" name="name" required>
            </div>
            <div class="mb-3">
                <label for="create_email" class="form-label">Email</label>
                <input type="email" class="form-control" id="create_email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="create_password" class="form-label">Password</label>
                <input type="password" class="form-control" id="create_password" name="password" required>
            </div>
            <div class="mb-3">
                <label for="create_role" class="form-label">Role</label>
                <select class="form-select" id="create_role" name="role_id" required>
                    <!-- Roles will be loaded via JS -->
                </select>
            </div>
            <div class="mb-3">
                <label for="create_manager" class="form-label">Manager</label>
                <select class="form-select" id="create_manager" name="is_manager">
                    <option value="">Select Manager (Optional)</option>
                    <!-- Load users via JS -->
                </select>
            </div>
            <div class="mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="create_is_worklog" name="is_worklog">
                    <label class="form-check-label" for="create_is_worklog">
                        Enable Worklog Access
                    </label>
                </div>
            </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Create</button>
        </div>
      </form>
    </div>
  </div>
</div>


@endsection

@push('scripts')
<script>
$(document).ready(function () {
  $.ajax({
    url: '{{ route("fetchuser") }}',
    method: 'GET',
    success: function (data) {
      let tbody = $('#usersTable tbody');
      tbody.empty();

      if (data.length === 0) {
        tbody.append(`<tr><td colspan="7" class="text-center">No users found</td></tr>`);
      } else {
              data.forEach(user => {
        const managerName = user.manager ? user.manager.name : 'None';
        const worklogStatus = user.is_worklog ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-secondary">No</span>';
        
        tbody.append(`
          <tr>
            <td>${user.id}</td>
            <td>${user.name}</td>
            <td>${user.role.role_name}</td>
            <td>${user.email}</td>
            <td>${managerName}</td>
            <td>${worklogStatus}</td>
            <td>
             <a href="#" class="btn btn-sm btn-primary me-1" onclick='openEditModal(${JSON.stringify(user)})'>Edit</a>
             <button class="btn btn-sm btn-danger" onclick="deleteUser(${user.id})">Delete</button>
            </td>
          </tr>
        `);
      });
      }
    },
    error: function () {
      alert('Failed to fetch users.');
    }
  });
});

function deleteUser(id) {
  if (confirm("Are you sure you want to delete this user?")) {
    $.ajax({
      url: `/user/delete/${id}`,
      method: 'DELETE',
      headers: {
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
      },
      success: function () {
        alert('User deleted successfully.');
        location.reload();
      },
      error: function () {
        alert('Failed to delete user.');
      }
    });
  }
}

// edit modal

function openEditModal(user) {
  $('#edit_user_id').val(user.id);
  $('#edit_name').val(user.name);
  $('#edit_email').val(user.email);
  $('#edit_is_worklog').prop('checked', user.is_worklog == 1);

  // Load roles dropdown
  $.ajax({
    url: '{{ route("fetchrole") }}',
    method: 'GET',
    success: function (roles) {
      let roleSelect = $('#edit_role');
      roleSelect.empty();

      roles.forEach(role => {
        roleSelect.append(`<option value="${role.id}" ${user.role && user.role.id === role.id ? 'selected' : ''}>${role.role_name}</option>`);
      });

      // Load users for manager dropdown
      $.ajax({
        url: '{{ route("fetchUsersForManager") }}',
        method: 'GET',
        success: function (users) {
          let managerSelect = $('#edit_manager');
          managerSelect.empty();
          managerSelect.append('<option value="">Select Manager (Optional)</option>');

          users.forEach(managerUser => {
            if (managerUser.id != user.id) { // Don't allow self as manager
              managerSelect.append(`<option value="${managerUser.id}" ${user.is_manager == managerUser.id ? 'selected' : ''}>${managerUser.name}</option>`);
            }
          });

          $('#editUserModal').modal('show');
        }
      });
    }
  });
}


$('#editUserForm').submit(function (e) {
  e.preventDefault();

  const userId = $('#edit_user_id').val();

  $.ajax({
    url: `/user/update/${userId}`,
    method: 'PUT',
    headers: {
      'X-CSRF-TOKEN': '{{ csrf_token() }}'
    },
    data: {
      name: $('#edit_name').val(),
      email: $('#edit_email').val(),
      role_id: $('#edit_role').val(),
      is_manager: $('#edit_manager').val() || null,
      is_worklog: $('#edit_is_worklog').is(':checked')
    },
    success: function () {
      $('#editUserModal').modal('hide');
      alert('User updated successfully.');
      location.reload();
    },
    error: function (xhr) {
      if (xhr.responseJSON && xhr.responseJSON.message) {
        alert('Error: ' + xhr.responseJSON.message);
      } else {
        alert('Failed to update user.');
      }
      console.error(xhr.responseJSON);
    }
  });
});


$('#createUserModal').on('show.bs.modal', function () {
  // Load roles dropdown
  $.ajax({
    url: '{{ route("fetchrole") }}',
    method: 'GET',
    success: function (roles) {
      let roleSelect = $('#create_role');
      roleSelect.empty();

      roles.forEach(role => {
        roleSelect.append(`<option value="${role.id}">${role.role_name}</option>`);
      });
    }
  });

  // Load users for manager dropdown
  $.ajax({
    url: '{{ route("fetchUsersForManager") }}',
    method: 'GET',
    success: function (users) {
      let managerSelect = $('#create_manager');
      managerSelect.empty();
      managerSelect.append('<option value="">Select Manager (Optional)</option>');

      users.forEach(user => {
        managerSelect.append(`<option value="${user.id}">${user.name}</option>`);
      });
    }
  });
});

// Handle Create User Form submission
$('#createUserForm').submit(function (e) {
  e.preventDefault();

  $.ajax({
    url: '{{ route("user.store") }}',
    method: 'POST',
    headers: {
      'X-CSRF-TOKEN': '{{ csrf_token() }}'
    },
    data: {
      name: $('#create_name').val(),
      email: $('#create_email').val(),
      password: $('#create_password').val(),
      role_id: $('#create_role').val(),
      is_manager: $('#create_manager').val() || null,
      is_worklog: $('#create_is_worklog').is(':checked')
    },
    success: function () {
      $('#createUserModal').modal('hide');
      alert('User created successfully.');
      location.reload();
    },
    error: function (xhr) {
      if (xhr.responseJSON && xhr.responseJSON.message) {
        alert('Error: ' + xhr.responseJSON.message);
      } else {
        alert('Failed to create user.');
      }
      console.error(xhr.responseJSON);
    }
  });
});

function deleteUser(id) {
  if (confirm("Are you sure you want to delete this user?")) {
    $.ajax({
      url: `/user/delete/${id}`,
      method: 'DELETE',
      headers: {
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
      },
      success: function () {
        alert('User deleted successfully.');
        location.reload();
      },
      error: function () {
        alert('Failed to delete user.');
      }
    });
  }
}


</script>
@endpush
