@extends('layouts.app')

@section('title', 'Dashboard - IMS')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">Dashboard</h1>
            <div class="text-muted">
                <i class="fas fa-clock"></i> <span id="currentTime"></span>
            </div>
        </div>
    </div>
</div>

<div id="dashboardContent" class="d-none">
    <!-- contoh cards -->
    <div class="row mb-4" id="statsContainer"></div>
    <div class="row mb-4" id="lowStockContainer"></div>
    <div class="row mb-4" id="adminContainer"></div>
    <div class="row mb-4" id="recentLogsContainer"></div>
</div>

<div id="loadingState" class="text-center my-5">
    <div class="spinner-border text-primary" role="status"></div>
    <p class="mt-2">Loading dashboard...</p>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('authUserReady', function(e) {
    const user = e.detail.user;
    const menu = e.detail.menu;
    loadDashboard(user, menu);
});

function loadDashboard(user, menu){
    $.ajax({
        url: '/api/dashboard-stats',
        method: 'GET',
        headers: { 'Authorization': 'Bearer ' + token },
        success: function(res){
            if(res.success){
                renderDashboard(user, res.data, menu);
            } else {
                alert('Gagal memuat data dashboard.');
            }
        },
        error: function(){
            alert('Terjadi kesalahan memuat dashboard.');
        }
    });
}

function renderDashboard(user, data, menu) {
    $('#loadingState').hide();
    $('#dashboardContent').removeClass('d-none');

    // cek apakah user punya menu Items
    const hasItemsMenu = menu.some(m => m.route === 'items' || (m.children && m.children.some(c => c.route === 'items')));
    const hasUserManagementMenu = menu.some(m => m.name.toLowerCase().includes('user management'));
    const hasLogsMenu = menu.some(m => m.route === 'logs');

    let htmlStats = '';

    // kalau dia punya menu Items berarti tampilkan statistik item
    if (hasItemsMenu) {
        htmlStats += `
            <div class="col-md-3">
                <div class="card bg-primary text-white mb-3">
                    <div class="card-body">
                        <h5>Total Items</h5>
                        <h2>${data.totalItems}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white mb-3">
                    <div class="card-body">
                        <h5>In Stock</h5>
                        <h2>${data.inStockItems}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white mb-3">
                    <div class="card-body">
                        <h5>Low Stock</h5>
                        <h2>${data.lowStockItems.length}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-danger text-white mb-3">
                    <div class="card-body">
                        <h5>Out of Stock</h5>
                        <h2>${data.outOfStockItems}</h2>
                    </div>
                </div>
            </div>
        `;
        $('#statsContainer').html(htmlStats);

        // low stock table
        if (data.lowStockItems.length > 0) {
            let rows = '';
            data.lowStockItems.forEach(item => {
                rows += `
                    <tr>
                        <td>${item.name}</td>
                        <td>${item.sku}</td>
                        <td><span class="badge bg-danger">${item.stock}</span></td>
                        <td>${item.min_stock}</td>
                        <td>
                            ${hasLogsMenu ? `<a href="/items/${item.id}/edit" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i> Update</a>` : ''}
                        </td>
                    </tr>
                `;
            });
            $('#lowStockContainer').html(`
                <div class="card mt-4">
                    <div class="card-header"><h5><i class="fas fa-exclamation-triangle text-warning"></i> Low Stock Alert</h5></div>
                    <div class="card-body">
                        <table class="table table-striped">
                            <thead>
                                <tr><th>Name</th><th>SKU</th><th>Stock</th><th>Min</th><th>Action</th></tr>
                            </thead>
                            <tbody>${rows}</tbody>
                        </table>
                    </div>
                </div>
            `);
        }
    }

    // kalau user punya menu User Management, tampilkan statistik user
    if (hasUserManagementMenu) {
        $('#adminContainer').html(`
            <div class="col-md-4">
                <div class="card bg-info text-white mb-3">
                    <div class="card-body">
                        <h5>Total Users</h5>
                        <h2>${data.totalUsers}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-success text-white mb-3">
                    <div class="card-body">
                        <h5>Active Users</h5>
                        <h2>${data.activeUsers}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-secondary text-white mb-3">
                    <div class="card-body">
                        <h5>Total Roles</h5>
                        <h2>${data.totalRoles}</h2>
                    </div>
                </div>
            </div>
        `);
    }

    // kalau user punya menu logs, tampilkan activity log
    if (hasLogsMenu) {
        let logs = '';
        data.recentLogs.forEach(log => {
            const badgeClass = log.action === 'create' ? 'success' :
                               (log.action === 'update' ? 'warning' : 'danger');
            const actionText = log.action.charAt(0).toUpperCase() + log.action.slice(1);
            logs += `
                <tr>
                    <td>${log.created_at}</td>
                    <td>${log.user_name}</td>
                    <td><span class="badge bg-${badgeClass}">${actionText}</span></td>
                    <td>${log.item_name || 'N/A'}</td>
                </tr>
            `;
        });

        $('#recentLogsContainer').html(`
            <div class="card mt-4">
                <div class="card-header"><h5><i class="fas fa-history"></i> Recent Activity</h5></div>
                <div class="card-body">
                    <table class="table table-striped">
                        <thead><tr><th>Date</th><th>User</th><th>Action</th><th>Item</th></tr></thead>
                        <tbody>${logs}</tbody>
                    </table>
                </div>
            </div>
        `);
    }
}

</script>
@endpush
