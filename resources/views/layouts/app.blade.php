<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    @include('layouts.navbar')

    <main class="container-fluid mt-4">
        @include('layouts.alerts')
        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    @stack('scripts')
    <script>
        window.authUser = null;
        const token = localStorage.getItem('api_token');

        if (!token) {
            window.location.href = '/';
        }

        // Ambil data user
        $.ajax({
            url: '/api/me',
            method: 'GET',
            headers: { 'Authorization': 'Bearer ' + token },
            success: function(res){
                if(res.success){
                    window.authUser = res.data;
                    console.log(window.authUser);
                    document.dispatchEvent(new CustomEvent('authUserReady', { detail: window.authUser }));
                }else{
                    window.location.href = '/';
                }
            },
            error: function(){
                window.location.href = '/';
            }
        });
    </script>
</body>