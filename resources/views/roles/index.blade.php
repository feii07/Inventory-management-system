@extends('layouts.app')

@section('title', 'Role Management')

@section('content')
<div class="container mt-4">
    <h1 class="mb-4">Role Management</h1>

    <!-- Form tambah role -->
    <div class="card mb-4">
        <div class="card-header">Add Role</div>
        <div class="card-body">
            <form id="addRoleForm">
                <div class="row mb-2">
                    <div class="col">
                        <input type="text" class="form-control" name="name" placeholder="Role Name" required>
                    </div>
                    <div class="col">
                        <input type="text" class="form-control" name="description" placeholder="Description">
                    </div>
                    <div class="col">
                        <button class="btn btn-primary w-100" type="submit">Add</button>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <h6>Menus Access</h6>
                        <div id="addMenuChecklist" class="border p-2" style="max-height:200px;overflow:auto;">
                            <!-- menu checklist load via AJAX -->
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6>Permissions</h6>
                        <div id="addPermissionChecklist" class="border p-2" style="max-height:200px;overflow:auto;">
                            <!-- permission checklist load via AJAX -->
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabel role -->
    <div class="card">
        <div class="card-header">Role List</div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="rolesTable">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th width="150">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data via AJAX -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal edit -->
<div class="modal fade" id="editRoleModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit Role</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="editRoleForm">
          <input type="hidden" name="id">
          <div class="mb-2">
            <input type="text" class="form-control" name="name" placeholder="Role Name" required>
          </div>
          <div class="mb-2">
            <input type="text" class="form-control" name="description" placeholder="Description">
          </div>
          <div class="row">
              <div class="col-md-6">
                  <h6>Menus Access</h6>
                  <div id="editMenuChecklist" class="border p-2" style="max-height:200px;overflow:auto;">
                      <!-- menu checklist load via AJAX -->
                  </div>
              </div>
              <div class="col-md-6">
                  <h6>Permissions</h6>
                  <div id="editPermissionChecklist" class="border p-2" style="max-height:200px;overflow:auto;">
                      <!-- permission checklist load via AJAX -->
                  </div>
              </div>
          </div>
          <button class="btn btn-primary mt-3" type="submit">Save Changes</button>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function(){
    const API_ROLES = '/api/roles';
    const API_MENUS = '/api/menus';
    const API_PERMS = '/api/permissions';

    $.ajaxSetup({
        headers: {
            'Authorization': 'Bearer ' + token,
            'Accept': 'application/json'
        }
    });

    loadMenusAndPermissions('#addMenuChecklist', '#addPermissionChecklist');
    loadRoles();

    // Load role list
    function loadRoles(){
        $.get(API_ROLES, function(res){
            const tbody = $('#rolesTable tbody').empty();
            res.data.forEach(role => {
                tbody.append(`
                    <tr data-id="${role.id}">
                        <td>${role.id}</td>
                        <td>${role.name}</td>
                        <td>${role.description ?? ''}</td>
                        <td>
                            <button class="btn btn-sm btn-warning editRoleBtn">Edit</button>
                        </td>
                    </tr>
                `);
            });
        });
    }

    // Load menu & permission checklist
    function loadMenusAndPermissions(menuSelector, permSelector, selectedMenus = [], selectedPerms = []){
        $.get(API_MENUS, function(res){
            $(menuSelector).html(buildMenuChecklist(res.data, selectedMenus));
        });
        $.get(API_PERMS, function(res){
            $(permSelector).html(buildPermChecklist(res.data, selectedPerms));
        });
    }

    function buildMenuChecklist(menus, selectedIds = []){
        let html = '';
        menus.forEach(menu => {
            const checked = selectedIds.includes(menu.id) ? 'checked' : '';
            html += `
                <div class="form-check">
                    <input type="checkbox" class="form-check-input menu-check" value="${menu.id}" ${checked}>
                    <label class="form-check-label"><i class="${menu.icon}"></i> ${menu.name}</label>
                </div>`;
            if(menu.children && menu.children.length > 0){
                html += `<div class="ms-3">` + buildMenuChecklist(menu.children, selectedIds) + `</div>`;
            }
        });
        return html;
    }

    function buildPermChecklist(perms, selectedIds = []){
        return perms.map(p => `
            <div class="form-check">
                <input type="checkbox" class="form-check-input perm-check" value="${p.id}" ${selectedIds.includes(p.id) ? 'checked' : ''}>
                <label class="form-check-label">${p.name}</label>
            </div>
        `).join('');
    }

    // Tambah role
    $('#addRoleForm').on('submit', function(e){
        e.preventDefault();
        const data = {
            name: $('#addRoleForm [name=name]').val(),
            description: $('#addRoleForm [name=description]').val(),
            menu_ids: $('#addMenuChecklist .menu-check:checked').map((i,el)=>$(el).val()).get(),
            permission_ids: $('#addPermissionChecklist .perm-check:checked').map((i,el)=>$(el).val()).get()
        };
        $.post(API_ROLES, data)
            .done(() => { loadRoles(); $('#addRoleForm')[0].reset(); })
            .fail(err => alert(err.responseJSON?.message ?? 'Error'));
    });

    // Edit modal open
    $(document).on('click', '.editRoleBtn', function(){
        const id = $(this).closest('tr').data('id');
        $.get(API_ROLES + '/' + id, function(res){
            const role = res.data;
            $('#editRoleForm [name=id]').val(role.id);
            $('#editRoleForm [name=name]').val(role.name);
            $('#editRoleForm [name=description]').val(role.description ?? '');
            loadMenusAndPermissions('#editMenuChecklist','#editPermissionChecklist',
                role.menus.map(m=>m.id),
                role.permissions.map(p=>p.id)
            );
            $('#editRoleModal').modal('show');
        });
    });

    // Simpan edit
    $('#editRoleForm').on('submit', function(e){
        e.preventDefault();
        const id = $('#editRoleForm [name=id]').val();
        const data = {
            name: $('#editRoleForm [name=name]').val(),
            description: $('#editRoleForm [name=description]').val(),
            menu_ids: $('#editMenuChecklist .menu-check:checked').map((i,el)=>$(el).val()).get(),
            permission_ids: $('#editPermissionChecklist .perm-check:checked').map((i,el)=>$(el).val()).get()
        };
        $.ajax({
            url: API_ROLES + '/' + id,
            method: 'PUT',
            data: data,
            success: () => { $('#editRoleModal').modal('hide'); loadRoles(); },
            error: err => alert(err.responseJSON?.message ?? 'Error')
        });
    });
});
</script>
@endpush
