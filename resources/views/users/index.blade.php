@extends('layouts.app')

@section('title', 'User Management')

@section('content')
<div class="container mt-4">
    <h1 class="mb-4">User Management</h1>

    <!-- Form tambah user -->
    <div class="card mb-4">
        <div class="card-header">Add User</div>
        <div class="card-body">
            <form id="addUserForm">
                <div class="row mb-2">
                    <div class="col">
                        <input type="text" class="form-control" name="name" placeholder="Name" required>
                    </div>
                    <div class="col">
                        <input type="email" class="form-control" name="email" placeholder="Email" required>
                    </div>
                    <div class="col">
                        <select name="role_id" class="form-control" required>
                            <option value="">Loading roles...</option>
                        </select>
                    </div>
                    <div class="col">
                        <input type="password" class="form-control" name="password" placeholder="Password" required>
                    </div>
                    <div class="col">
                        <button class="btn btn-primary w-100" type="submit">Add</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabel users -->
    <div class="card">
        <div class="card-header">User List</div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="usersTable">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th width="150">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data dari AJAX -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal edit -->
<div class="modal fade" id="editUserModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit User</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="editUserForm">
          <input type="hidden" name="id">
          <div class="mb-2">
            <input type="text" class="form-control" name="name" placeholder="Name" required>
          </div>
          <div class="mb-2">
            <input type="email" class="form-control" name="email" placeholder="Email" required>
          </div>
          <div class="mb-2">
            <select name="role_id" class="form-control" required>
                <option value="">Loading roles...</option>
            </select>
          </div>
          <div class="mb-2">
            <input type="password" class="form-control" name="password" placeholder="New Password (optional)">
          </div>
          <button class="btn btn-primary" type="submit">Save Changes</button>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function(){
    const API_USERS = '/api/users';
    const API_ROLES = '/api/roles';

    $.ajaxSetup({
        headers: {
            'Authorization': 'Bearer ' + token,
            'Accept': 'application/json'
        }
    });
    
    loadRoles();
    loadUsers();
});

// Load roles ke select
function loadRoles() {
    $.get(API_ROLES, function(res) {
        const selectAdd = $('#addUserForm [name=role_id]').empty();
        const selectEdit = $('#editUserForm [name=role_id]').empty();
        selectAdd.append('<option value="">Select Role</option>');
        selectEdit.append('<option value="">Select Role</option>');
        res.data.forEach(role => {
            selectAdd.append(`<option value="${role.id}">${role.name}</option>`);
            selectEdit.append(`<option value="${role.id}">${role.name}</option>`);
        });
    });
}

// Load users
function loadUsers() {
    $.get(API_USERS, function(res){
        const tbody = $('#usersTable tbody').empty();
        res.data.forEach(user => {
            tbody.append(`
                <tr data-id="${user.id}" data-role-id="${user.role.id}">
                    <td>${user.id}</td>
                    <td>${user.name}</td>
                    <td>${user.email}</td>
                    <td>${user.role.name}</td>
                    <td>
                        <button class="btn btn-sm btn-warning editUserBtn">Edit</button>
                        <button class="btn btn-sm btn-danger deleteUserBtn">Delete</button>
                    </td>
                </tr>
            `);
        });
    });
}

// Add user
$('#addUserForm').on('submit', function(e){
    e.preventDefault();
    $.post(API_USERS, $(this).serialize())
        .done(() => {
            loadUsers();
            $('#addUserForm')[0].reset();
        })
        .fail(err => alert(err.responseJSON?.message ?? 'Error'));
});

// Edit user - buka modal
$(document).on('click', '.editUserBtn', function(){
    const tr = $(this).closest('tr');
    $('#editUserForm [name=id]').val(tr.data('id'));
    $('#editUserForm [name=name]').val(tr.find('td:eq(1)').text());
    $('#editUserForm [name=email]').val(tr.find('td:eq(2)').text());
    $('#editUserForm [name=role_id]').val(tr.data('role-id'));
    $('#editUserForm [name=password]').val('');
    $('#editUserModal').modal('show');
});

// Simpan edit
$('#editUserForm').on('submit', function(e){
    e.preventDefault();
    const id = $('#editUserForm [name=id]').val();
    const data = {
        name: $('#editUserForm [name=name]').val(),
        email: $('#editUserForm [name=email]').val(),
        role_id: $('#editUserForm [name=role_id]').val(),
    };
    const password = $('#editUserForm [name=password]').val();
    if (password.trim() !== '') {
        data.password = password;
    }
    $.ajax({
        url: API_USERS + '/' + id,
        method: 'PUT',
        data: data,
        success: () => {
            $('#editUserModal').modal('hide');
            loadUsers();
        },
        error: err => alert(err.responseJSON?.message ?? 'Error')
    });
});

// Hapus user
$(document).on('click', '.deleteUserBtn', function(){
    if(!confirm('Are you sure?')) return;
    const id = $(this).closest('tr').data('id');
    $.ajax({
        url: API_USERS + '/' + id,
        method: 'DELETE',
        success: () => loadUsers(),
        error: err => alert(err.responseJSON?.message ?? 'Error')
    });
});
</script>
@endpush
