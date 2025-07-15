@extends('layouts.app')

@section('title', 'Items Management')

@section('content')
<div class="container mt-4">
    <h1 class="mb-4">Items</h1>

    <!-- Form tambah item -->
    <div class="card mb-4">
        <div class="card-header">Add Item</div>
        <div class="card-body">
            <form id="addItemForm">
                <div class="row g-2 mb-2">
                    <div class="col-md-3">
                        <input type="text" class="form-control" name="name" placeholder="Name" required>
                    </div>
                    <div class="col-md-3">
                        <input type="text" class="form-control" name="sku" placeholder="SKU" required>
                    </div>
                    <div class="col-md-3">
                        <input type="number" class="form-control" name="price" placeholder="Price" required>
                    </div>
                    <div class="col-md-3">
                        <input type="number" step="0.01" class="form-control" name="stock" placeholder="Stock" required>
                    </div>
                    <div class="col-md-3">
                        <input type="number" class="form-control" name="min_stock" placeholder="Min" value="0">
                    </div>
                    <div class="col-md-3">
                        <input type="text" class="form-control" name="category" placeholder="Category">
                    </div>
                    <div class="col-md-3">
                        <input type="text" class="form-control" name="unit" placeholder="Unit" value="pcs">
                    </div>
                </div>
                <div class="mb-2">
                    <textarea class="form-control" name="description" placeholder="Description (optional)"></textarea>
                </div>
                <button class="btn btn-primary w-100" type="submit">Add</button>
            </form>
        </div>
    </div>

    <!-- Tabel items -->
    <div class="card">
        <div class="card-header">Item List</div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle" id="itemsTable">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>SKU</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Min</th>
                            <th>Category</th>
                            <th>Unit</th>
                            <th>Description</th>
                            <th width="150">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data akan dimuat via AJAX -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal edit -->
<div class="modal fade" id="editModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit Item</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="editItemForm">
          <input type="hidden" name="id">
          <div class="row g-2 mb-2">
              <div class="col-md-3">
                  <input type="text" class="form-control" name="name" placeholder="Name" required>
              </div>
              <div class="col-md-3">
                  <input type="text" class="form-control" name="sku" placeholder="SKU" required>
              </div>
              <div class="col-md-3">
                  <input type="number" class="form-control" name="price" placeholder="Price" required>
              </div>
              <div class="col-md-3">
                  <input type="number" step="0.01" class="form-control" name="stock" placeholder="Stock" required>
              </div>
              <div class="col-md-3">
                  <input type="number" class="form-control" name="min_stock" placeholder="Min">
              </div>
              <div class="col-md-3">
                  <input type="text" class="form-control" name="category" placeholder="Category">
              </div>
              <div class="col-md-3">
                  <input type="text" class="form-control" name="unit" placeholder="Unit">
              </div>
          </div>
          <div class="mb-2">
              <textarea class="form-control" name="description" placeholder="Description (optional)"></textarea>
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
const API_URL = '/api/items';
    
$(document).ready(function(){
    $.ajaxSetup({
        headers: {
            'Authorization': 'Bearer ' + token,
            'Accept': 'application/json'
        }
    });
    
    loadItems();
});

function loadItems(){
    $.get(API_URL, function(res){
        const tbody = $('#itemsTable tbody').empty();
        res.data.data.forEach(item => {
            tbody.append(`
                <tr data-id="${item.id}"
                    data-name="${item.name}"
                    data-sku="${item.sku}"
                    data-price="${item.price}"
                    data-stock="${item.stock}"
                    data-min_stock="${item.min_stock}"
                    data-category="${item.category ?? ''}"
                    data-unit="${item.unit}"
                    data-description="${item.description ?? ''}">
                    <td>${item.id}</td>
                    <td>${item.name}</td>
                    <td>${item.sku}</td>
                    <td>${item.price}</td>
                    <td>${item.stock}</td>
                    <td>${item.min_stock}</td>
                    <td>${item.category ?? '-'}</td>
                    <td>${item.unit}</td>
                    <td>${item.description ?? '-'}</td>
                    <td>
                        <button class="btn btn-sm btn-warning editBtn">Edit</button>
                        <button class="btn btn-sm btn-danger deleteBtn">Delete</button>
                    </td>
                </tr>
            `);
        });
    });
}

// tambah item
$('#addItemForm').on('submit', function(e){
    e.preventDefault();
    const data = $(this).serialize();
    $.post(API_URL, data)
        .done(function(){
            loadItems();
            $('#addItemForm')[0].reset();
        })
        .fail(function(err){
            alert('Error: ' + err.responseJSON.message);
        });
});

// buka modal edit
$(document).on('click', '.editBtn', function() {
    const tr = $(this).closest('tr');

    $('#editItemForm [name=id]').val(tr.data('id'));
    $('#editItemForm [name=name]').val(tr.data('name'));
    $('#editItemForm [name=sku]').val(tr.data('sku'));
    $('#editItemForm [name=price]').val(tr.data('price'));
    $('#editItemForm [name=stock]').val(tr.data('stock'));
    $('#editItemForm [name=min_stock]').val(tr.data('min_stock'));
    $('#editItemForm [name=category]').val(tr.data('category'));
    $('#editItemForm [name=unit]').val(tr.data('unit'));
    $('#editItemForm [name=description]').val(tr.data('description'));

    $('#editModal').modal('show');
});

// simpan edit
$('#editItemForm').on('submit', function(e){
    e.preventDefault();
    const id = $('#editItemForm [name=id]').val();
    const data = {
        name: $('#editItemForm [name=name]').val(),
        sku: $('#editItemForm [name=sku]').val(),
        price: $('#editItemForm [name=price]').val(),
        stock: $('#editItemForm [name=stock]').val(),
        min_stock: $('#editItemForm [name=min_stock]').val(),
        category: $('#editItemForm [name=category]').val(),
        unit: $('#editItemForm [name=unit]').val(),
        description: $('#editItemForm [name=description]').val(),
    };
    $.ajax({
        url: API_URL + '/' + id,
        method: 'PUT',
        data: data,
        success: function(){
            $('#editModal').modal('hide');
            loadItems();
        },
        error: function(err){
            alert('Error: ' + err.responseJSON.message);
        }
    });
});

// delete item
$(document).on('click', '.deleteBtn', function(){
    if(!confirm('Are you sure?')) return;
    const id = $(this).closest('tr').data('id');
    $.ajax({
        url: API_URL + '/' + id,
        method: 'DELETE',
        success: function(){
            loadItems();
        },
        error: function(err){
            alert('Error: ' + err.responseJSON.message);
        }
    });
});
</script>
@endpush
