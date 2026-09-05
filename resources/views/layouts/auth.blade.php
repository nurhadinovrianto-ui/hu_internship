<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Masuk') - {{ \App\Models\Setting::getValue('app_name', 'Sistem Magang') }}</title>
    
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('edumin/images/favicon.png') }}">
    
    <!-- STYLESHEETS -->
    <link rel="stylesheet" href="{{ asset('edumin/vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}">
    <link class="main-css" rel="stylesheet" href="{{ asset('edumin/css/style.css') }}">
    
    <style>
        body {
            background-color: #ffffff !important;
            background-image: none !important;
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
    </style>
    @yield('styles')
</head>
<body>
    
    @yield('content')

    <!-- Scripts -->
    <script src="{{ asset('edumin/vendor/global/global.min.js') }}"></script>
    <script src="{{ asset('edumin/vendor/bootstrap-select/dist/js/bootstrap-select.min.js') }}"></script>
    <script src="{{ asset('edumin/js/custom.min.js') }}"></script>
    <script src="{{ asset('edumin/js/dlabnav-init.js') }}"></script>
    @yield('scripts')
</body>
</html>
