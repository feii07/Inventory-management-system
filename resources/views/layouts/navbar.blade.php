<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="/dashboard">
            <i class="fas fa-boxes"></i> IMS
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto" id="menu-left">
                <!-- Menu tambahan akan dimasukkan lewat JS -->
            </ul>

            <ul class="navbar-nav" id="menu-right">
                <li class="nav-item dropdown d-none" id="userDropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="profileDropdown" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle"></i> <span id="userName">User</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#" id="logoutBtn">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
@push('scripts')
<script>
$(function(){
    document.addEventListener('authUserReady', function(e){
        const user = e.detail.user;
        const menu = e.detail.menu;

        $('#userName').text(user.name);
        $('#userRole').text(user.role);
        $('#userDropdown').removeClass('d-none');

        const menuLeft = $('#menu-left');
        menu.forEach(item => {
            if (item.children) {
                let childrenHtml = '';
                item.children.forEach(child => {
                    childrenHtml += `<li><a class="dropdown-item" href="${child.route}">
                        <i class="${child.icon}"></i> ${child.name}
                    </a></li>`;
                });
                menuLeft.append(`
                    <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="${item.icon}"></i> ${item.name}
                    </a>
                    <ul class="dropdown-menu">${childrenHtml}</ul>
                    </li>
                `);
            } else {
            menuLeft.append(`
                    <li class="nav-item">
                    <a class="nav-link" href="${item.route}">
                        <i class="${item.icon}"></i> ${item.name}
                    </a>
                    </li>
                `);
            }
        });
        });
    
    // Logout
    $('#logoutBtn').on('click', function(e){
        e.preventDefault();
        if (!confirm('Are you sure you want to logout?')) return;

        $.ajax({
            url: '/api/logout',
            method: 'POST',
            headers: {
                'Authorization': 'Bearer ' + token,
                'Accept': 'application/json'
            },
            success: function(){
                localStorage.removeItem('api_token');
                window.location.href = '/';
            },
            error: function(){
                localStorage.removeItem('api_token');
                window.location.href = '/';
            }
        });
    });
});
</script>
@endpush
