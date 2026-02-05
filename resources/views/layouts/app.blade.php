<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default" data-assets-path="/vuexy/" data-template="vertical-menu-template" data-style="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'Dashboard') - {{ config('app.name') }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="/vuexy/img/favicon/favicon.ico">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="/vuexy/vendor/fonts/fontawesome.css">
    <link rel="stylesheet" href="/vuexy/vendor/fonts/flag-icons.css">
    <link rel="stylesheet" href="{{ asset('vuexy/vendor/fonts/iconify-icons.css') }}">


    <!-- Core CSS -->
    <link rel="stylesheet" href="/vuexy/vendor/css/core.css" class="template-customizer-core-css">
    <link rel="stylesheet" href="/vuexy/vendor/css/theme-default.css" class="template-customizer-theme-css">
    <link rel="stylesheet" href="/vuexy/css/demo.css">

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="/vuexy/vendor/libs/perfect-scrollbar/perfect-scrollbar.css">

    <!-- Helpers -->
    <script src="/vuexy/vendor/js/helpers.js"></script>
    <script src="/vuexy/js/config.js"></script>

    @stack('styles')
    @livewireStyles
</head>

<body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            
            <!-- Menu -->
            <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
                <div class="app-brand demo">
                    <a href="{{ url(app()->getLocale() . '/welcome') }}" class="app-brand-link">
                        <span class="app-brand-logo demo">
                            <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M16 2L2 9L16 16L30 9L16 2Z" fill="#7367f0"/>
                                <path d="M2 23L16 30L30 23V9L16 16L2 9V23Z" fill="#7367f0" fill-opacity="0.5"/>
                            </svg>
                        </span>
                        <span class="app-brand-text demo menu-text fw-bold">{{ config('app.name') }}</span>
                    </a>
                    <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
                        <i class="ti tabler-circle-dot menu-toggle-icon d-none d-xl-block align-middle"></i>
                        <i class="ti tabler-x d-block d-xl-none align-middle"></i>
                    </a>
                </div>

                <div class="menu-inner-shadow"></div>

                <ul class="menu-inner py-1">
                    <!-- Dashboard -->
                    <li class="menu-item {{ request()->routeIs('welcome') ? 'active' : '' }}">
                        <a href="{{ url(app()->getLocale() . '/welcome') }}" class="menu-link">
                            <i class="menu-icon tf-icons ti tabler-home"></i>
                            <div>{{ __('Inicio') }}</div>
                        </a>
                    </li>

                    <!-- Modules Section -->
                    <li class="menu-header small text-uppercase">
                        <span class="menu-header-text">{{ __('Módulos') }}</span>
                    </li>

                    @can('access.core')
                    <li class="menu-item {{ request()->is('*/core*') ? 'active open' : '' }}">
                        <a href="javascript:void(0);" class="menu-link menu-toggle">
                            <i class="menu-icon tf-icons ti tabler-settings"></i>
                            <div>Core</div>
                        </a>
                        <ul class="menu-sub">
                            <li class="menu-item">
                                <a href="{{ url(app()->getLocale() . '/core/users') }}" class="menu-link">
                                    <div>{{ __('Usuarios') }}</div>
                                </a>
                            </li>
                            <li class="menu-item">
                                <a href="{{ url(app()->getLocale() . '/core/roles') }}" class="menu-link">
                                    <div>{{ __('Roles') }}</div>
                                </a>
                            </li>
                        </ul>
                    </li>
                    @endcan

                    @can('access.erp')
                    <li class="menu-item {{ request()->is('*/erp*') ? 'active' : '' }}">
                        <a href="{{ url(app()->getLocale() . '/erp') }}" class="menu-link">
                            <i class="menu-icon tf-icons ti tabler-building"></i>
                            <div>ERP</div>
                        </a>
                    </li>
                    @endcan

                    @can('access.hr')
                    <li class="menu-item {{ request()->is('*/hr*') ? 'active' : '' }}">
                        <a href="{{ url(app()->getLocale() . '/hr') }}" class="menu-link">
                            <i class="menu-icon tf-icons ti tabler-users"></i>
                            <div>RRHH</div>
                        </a>
                    </li>
                    @endcan

                    @can('access.crm')
                    <li class="menu-item {{ request()->is('*/crm*') ? 'active' : '' }}">
                        <a href="{{ url(app()->getLocale() . '/crm') }}" class="menu-link">
                            <i class="menu-icon tf-icons ti tabler-chart-pie"></i>
                            <div>CRM</div>
                        </a>
                    </li>
                    @endcan

                    @can('access.fms')
                    <li class="menu-item {{ request()->is('*/fms*') ? 'active' : '' }}">
                        <a href="{{ url(app()->getLocale() . '/fms') }}" class="menu-link">
                            <i class="menu-icon tf-icons ti tabler-calculator"></i>
                            <div>FMS</div>
                        </a>
                    </li>
                    @endcan

                    @can('access.reports')
                    <li class="menu-item {{ request()->is('*/reports*') ? 'active' : '' }}">
                        <a href="{{ url(app()->getLocale() . '/reports') }}" class="menu-link">
                            <i class="menu-icon tf-icons ti tabler-file-analytics"></i>
                            <div>{{ __('Reportes') }}</div>
                        </a>
                    </li>
                    @endcan
                </ul>
            </aside>
            <!-- / Menu -->

            <!-- Layout container -->
            <div class="layout-page">
                
                <!-- Navbar -->
                <nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme" id="layout-navbar">
                    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
                        <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
                            <i class="ti tabler-menu-2 ti-md"></i>
                        </a>
                    </div>

                    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
                        <!-- Search -->
                        <div class="navbar-nav align-items-center">
                            <div class="nav-item navbar-search-wrapper mb-0">
                                <a class="nav-item nav-link search-toggler fw-normal px-0" href="javascript:void(0);">
                                    <i class="ti tabler-search ti-md"></i>
                                    <span class="d-none d-md-inline-block text-muted ms-2">{{ __('Buscar') }} (Ctrl+/)</span>
                                </a>
                            </div>
                        </div>
                        <!-- /Search -->

                        <ul class="navbar-nav flex-row align-items-center ms-auto">
                            <!-- Language Switcher -->
                            <li class="nav-item dropdown-language dropdown me-2 me-xl-0">
                                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                                    <i class="ti tabler-language ti-md"></i>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item {{ app()->getLocale() === 'es' ? 'active' : '' }}" href="{{ url('es' . '/' . request()->segment(2)) }}">
                                            <span class="fi fi-es fis rounded-circle me-2 fs-5"></span>
                                            <span class="align-middle">Español</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item {{ app()->getLocale() === 'en' ? 'active' : '' }}" href="{{ url('en' . '/' . request()->segment(2)) }}">
                                            <span class="fi fi-us fis rounded-circle me-2 fs-5"></span>
                                            <span class="align-middle">English</span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <!-- /Language Switcher -->

                            <!-- Notifications -->
                            <li class="nav-item dropdown-notifications navbar-dropdown dropdown me-3 me-xl-1">
                                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                                    <i class="ti tabler-bell ti-md"></i>
                                    <span class="badge bg-danger rounded-pill badge-notifications">5</span>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end py-0">
                                    <li class="dropdown-menu-header border-bottom">
                                        <div class="dropdown-header d-flex align-items-center py-3">
                                            <h6 class="mb-0 me-auto">{{ __('Notificaciones') }}</h6>
                                            <span class="badge rounded-pill bg-label-primary">5 {{ __('Nuevas') }}</span>
                                        </div>
                                    </li>
                                    <li class="dropdown-notifications-list scrollable-container">
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item list-group-item-action dropdown-notifications-item">
                                                <div class="d-flex">
                                                    <div class="flex-shrink-0 me-3">
                                                        <div class="avatar">
                                                            <span class="avatar-initial rounded-circle bg-label-success"><i class="ti tabler-check"></i></span>
                                                        </div>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <h6 class="mb-1 small">{{ __('Bienvenido al sistema') }}</h6>
                                                        <small class="text-muted">{{ __('Hace un momento') }}</small>
                                                    </div>
                                                </div>
                                            </li>
                                        </ul>
                                    </li>
                                    <li class="dropdown-menu-footer border-top p-3">
                                        <a href="javascript:void(0)" class="btn btn-primary d-flex justify-content-center">
                                            {{ __('Ver todas las notificaciones') }}
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <!-- /Notifications -->

                            <!-- User -->
                            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                                <a class="nav-link dropdown-toggle hide-arrow p-0" href="javascript:void(0);" data-bs-toggle="dropdown">
                                    <div class="avatar avatar-online">
                                        <span class="avatar-initial rounded-circle bg-primary">
                                            {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                                        </span>
                                    </div>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item mt-0" href="javascript:void(0);">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-0 me-2">
                                                    <div class="avatar avatar-online">
                                                        <span class="avatar-initial rounded-circle bg-primary">
                                                            {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-0">{{ auth()->user()->name ?? 'Usuario' }}</h6>
                                                    <small class="text-muted">{{ auth()->user()->role?->name ?? 'Usuario' }}</small>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                    <li>
                                        <div class="dropdown-divider"></div>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ url(app()->getLocale() . '/user/profile') }}">
                                            <i class="ti tabler-user me-3"></i><span class="align-middle">{{ __('Mi Perfil') }}</span>
                                        </a>
                                    </li>
                                    <li>
                                        <div class="dropdown-divider"></div>
                                    </li>
                                    <li>
                                        <div class="d-grid px-2 pt-2 pb-1">
                                            <form method="POST" action="{{ route('logout') }}">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-danger d-flex w-100 justify-content-center align-items-center">
                                                    <small class="align-middle">{{ __('Cerrar Sesión') }}</small>
                                                    <i class="ti tabler-logout ms-2"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </li>
                                </ul>
                            </li>
                            <!-- /User -->
                        </ul>
                    </div>
                </nav>
                <!-- / Navbar -->

                <!-- Content wrapper -->
                <div class="content-wrapper">
                    <!-- Content -->
                    <div class="container-xxl flex-grow-1 container-p-y">
                        @if (session('status'))
                            <div class="alert alert-success alert-dismissible mb-4" role="alert">
                                {{ session('status') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        @yield('content')
                    </div>
                    <!-- / Content -->

                    <!-- Footer -->
                    <footer class="content-footer footer bg-footer-theme">
                        <div class="container-xxl">
                            <div class="footer-container d-flex align-items-center justify-content-between py-4 flex-md-row flex-column">
                                <div class="text-body">
                                    © {{ date('Y') }} {{ config('app.name') }}. {{ __('Todos los derechos reservados.') }}
                                </div>
                            </div>
                        </div>
                    </footer>
                    <!-- / Footer -->

                    <div class="content-backdrop fade"></div>
                </div>
                <!-- Content wrapper -->
            </div>
            <!-- / Layout page -->
        </div>

        <!-- Overlay -->
        <div class="layout-overlay layout-menu-toggle"></div>
        
        <!-- Drag Target Area To Slide In Sidebar -->
        <div class="drag-target"></div>
    </div>
    <!-- / Layout wrapper -->

    <!-- Core JS -->
    <script src="/vuexy/vendor/libs/jquery/jquery.js"></script>
    <script src="/vuexy/vendor/libs/popper/popper.js"></script>
    <script src="/vuexy/vendor/js/bootstrap.js"></script>
    <script src="/vuexy/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="/vuexy/vendor/js/menu.js"></script>

    <!-- Main JS -->
    <script src="/vuexy/js/main.js"></script>

    @livewireScripts
    @stack('scripts')
</body>
</html>
