<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light-style layout-wide customizer-hide" dir="ltr" data-theme="theme-default" data-assets-path="/vuexy/" data-template="vertical-menu-template" data-style="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'Auth') - {{ config('app.name') }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="/vuexy/img/favicon/favicon.ico">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="/vuexy/vendor/fonts/fontawesome.css">
    <link rel="stylesheet" href="/vuexy/vendor/fonts/flag-icons.css">

    <!-- Core CSS -->
    <link rel="stylesheet" href="/vuexy/vendor/css/core.css" class="template-customizer-core-css">
    <link rel="stylesheet" href="/vuexy/vendor/css/theme-default.css" class="template-customizer-theme-css">
    <link rel="stylesheet" href="/vuexy/css/demo.css">

    <!-- Page CSS -->
    <link rel="stylesheet" href="/vuexy/vendor/css/pages/page-auth.css">

    <!-- Helpers -->
    <script src="/vuexy/vendor/js/helpers.js"></script>
    <script src="/vuexy/js/config.js"></script>

    @stack('styles')
</head>

<body>
    <!-- Content -->
    <div class="authentication-wrapper authentication-basic px-4">
        <div class="authentication-inner py-6">
            @yield('content')
        </div>
    </div>
    <!-- / Content -->

    <!-- Core JS -->
    <script src="/vuexy/vendor/libs/jquery/jquery.js"></script>
    <script src="/vuexy/vendor/libs/popper/popper.js"></script>
    <script src="/vuexy/vendor/js/bootstrap.js"></script>

    <!-- Main JS -->
    <script src="/vuexy/js/main.js"></script>

    @stack('scripts')
</body>
</html>
