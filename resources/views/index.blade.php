<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light-style" dir="ltr" data-theme="theme-default">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Multisoft Suite - Solución empresarial integral con módulos ERP, CRM, RRHH y Finanzas">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ config('app.name') }} - {{ __('Solución Empresarial Integral') }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="/vuexy/img/favicon/favicon.ico">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">

    <!-- Core CSS -->
    <link rel="stylesheet" href="/vuexy/vendor/css/core.css">
    <link rel="stylesheet" href="/vuexy/vendor/css/theme-default.css">

    <style>
        :root {
            --primary: #7367f0;
            --primary-dark: #5e50ee;
            --secondary: #a8aaae;
            --dark: #22303e;
            --light: #f8f9fa;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e8ec 100%);
            min-height: 100vh;
        }

        /* Navbar */
        .navbar-landing {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 20px rgba(0,0,0,0.06);
            padding: 1rem 0;
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            color: var(--primary);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .navbar-brand svg {
            width: 36px;
            height: 36px;
        }

        .nav-link-landing {
            color: var(--dark);
            font-weight: 500;
            padding: 0.5rem 1rem;
            transition: color 0.2s;
        }

        .nav-link-landing:hover {
            color: var(--primary);
        }

        /* Hero Section */
        .hero-section {
            padding: 5rem 0 4rem;
            text-align: center;
        }

        .hero-title {
            font-size: 3.25rem;
            font-weight: 800;
            color: var(--dark);
            line-height: 1.2;
            margin-bottom: 1.25rem;
        }

        .hero-title span {
            background: linear-gradient(135deg, var(--primary) 0%, #9f95f5 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero-subtitle {
            font-size: 1.25rem;
            color: #6c757d;
            max-width: 600px;
            margin: 0 auto 2rem;
            line-height: 1.6;
        }

        .btn-primary-landing {
            background: var(--primary);
            border: none;
            padding: 0.875rem 2rem;
            font-weight: 600;
            font-size: 1rem;
            border-radius: 8px;
            transition: all 0.3s;
        }

        .btn-primary-landing:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(115, 103, 240, 0.35);
        }

        .btn-outline-landing {
            border: 2px solid var(--dark);
            color: var(--dark);
            padding: 0.75rem 1.75rem;
            font-weight: 600;
            font-size: 1rem;
            border-radius: 8px;
            background: transparent;
            transition: all 0.3s;
        }

        .btn-outline-landing:hover {
            background: var(--dark);
            color: white;
        }

        /* Modules Section */
        .modules-section {
            padding: 3rem 0 4rem;
        }

        .section-title {
            font-size: 1rem;
            font-weight: 600;
            color: var(--primary);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 0.5rem;
        }

        .section-heading {
            font-size: 2rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 3rem;
        }

        .module-card {
            background: white;
            border-radius: 16px;
            padding: 2rem;
            height: 100%;
            border: 1px solid rgba(0,0,0,0.05);
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
        }

        .module-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.08);
        }

        .module-icon {
            width: 56px;
            height: 56px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1.25rem;
        }

        .module-icon.core { background: rgba(115, 103, 240, 0.12); color: #7367f0; }
        .module-icon.erp { background: rgba(0, 207, 232, 0.12); color: #00cfe8; }
        .module-icon.hr { background: rgba(40, 199, 111, 0.12); color: #28c76f; }
        .module-icon.crm { background: rgba(255, 159, 67, 0.12); color: #ff9f43; }
        .module-icon.fms { background: rgba(234, 84, 85, 0.12); color: #ea5455; }
        .module-icon.reports { background: rgba(130, 134, 139, 0.12); color: #82868b; }

        .module-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 0.5rem;
        }

        .module-desc {
            font-size: 0.9rem;
            color: #6c757d;
            line-height: 1.5;
            margin: 0;
        }

        /* Footer */
        .footer-landing {
            padding: 2rem 0;
            text-align: center;
            color: #6c757d;
            font-size: 0.875rem;
        }

        /* Language Switcher */
        .lang-switch {
            display: flex;
            gap: 0.5rem;
        }

        .lang-btn {
            padding: 0.375rem 0.75rem;
            border-radius: 6px;
            font-size: 0.875rem;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s;
        }

        .lang-btn.active {
            background: var(--primary);
            color: white;
        }

        .lang-btn:not(.active) {
            color: var(--dark);
            background: rgba(0,0,0,0.05);
        }

        .lang-btn:not(.active):hover {
            background: rgba(0,0,0,0.1);
        }

        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.25rem;
            }
            .hero-subtitle {
                font-size: 1.1rem;
            }
            .module-card {
                padding: 1.5rem;
            }
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-landing fixed-top">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">
                <svg viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M16 2L2 9L16 16L30 9L16 2Z" fill="#7367f0"/>
                    <path d="M2 23L16 30L30 23V9L16 16L2 9V23Z" fill="#7367f0" fill-opacity="0.5"/>
                </svg>
                {{ config('app.name') }}
            </a>
            
            <div class="d-flex align-items-center gap-3">
                <div class="lang-switch d-none d-sm-flex">
                    <a href="{{ url('es') }}" class="lang-btn {{ app()->getLocale() === 'es' ? 'active' : '' }}">ES</a>
                    <a href="{{ url('en') }}" class="lang-btn {{ app()->getLocale() === 'en' ? 'active' : '' }}">EN</a>
                </div>
                <a href="{{ url(app()->getLocale() . '/login') }}" class="btn btn-primary-landing">
                    <i class="ti tabler-login me-1"></i>{{ __('Iniciar Sesión') }}
                </a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section" style="margin-top: 80px;">
        <div class="container">
            <h1 class="hero-title">
                {{ __('Gestiona tu empresa') }}<br>
                <span>{{ __('de forma inteligente') }}</span>
            </h1>
            <p class="hero-subtitle">
                {{ __('Una suite empresarial completa que integra ERP, CRM, Recursos Humanos y Finanzas en una sola plataforma moderna y fácil de usar.') }}
            </p>
            <div class="d-flex justify-content-center gap-3 flex-wrap">
                <a href="{{ url(app()->getLocale() . '/login') }}" class="btn btn-primary-landing">
                    {{ __('Comenzar ahora') }}
                    <i class="ti tabler-arrow-right ms-2"></i>
                </a>
                <a href="#modules" class="btn btn-outline-landing">
                    {{ __('Ver módulos') }}
                </a>
            </div>
        </div>
    </section>

    <!-- Modules Section -->
    <section class="modules-section" id="modules">
        <div class="container">
            <div class="text-center">
                <p class="section-title">{{ __('Módulos') }}</p>
                <h2 class="section-heading">{{ __('Todo lo que necesitas en un solo lugar') }}</h2>
            </div>

            <div class="row g-4">
                <!-- Core -->
                <div class="col-lg-4 col-md-6">
                    <div class="module-card">
                        <div class="module-icon core">
                            <i class="ti tabler-settings"></i>
                        </div>
                        <h3 class="module-title">Core</h3>
                        <p class="module-desc">{{ __('Administración central del sistema: usuarios, roles, permisos y configuración global.') }}</p>
                    </div>
                </div>

                <!-- ERP -->
                <div class="col-lg-4 col-md-6">
                    <div class="module-card">
                        <div class="module-icon erp">
                            <i class="ti tabler-building"></i>
                        </div>
                        <h3 class="module-title">ERP</h3>
                        <p class="module-desc">{{ __('Planificación de recursos: inventarios, compras, ventas y operaciones empresariales.') }}</p>
                    </div>
                </div>

                <!-- HR -->
                <div class="col-lg-4 col-md-6">
                    <div class="module-card">
                        <div class="module-icon hr">
                            <i class="ti tabler-users"></i>
                        </div>
                        <h3 class="module-title">{{ __('Recursos Humanos') }}</h3>
                        <p class="module-desc">{{ __('Gestión de empleados, contratos, asistencias, planillas y evaluaciones.') }}</p>
                    </div>
                </div>

                <!-- CRM -->
                <div class="col-lg-4 col-md-6">
                    <div class="module-card">
                        <div class="module-icon crm">
                            <i class="ti tabler-chart-pie"></i>
                        </div>
                        <h3 class="module-title">CRM</h3>
                        <p class="module-desc">{{ __('Gestión de clientes, leads, oportunidades y seguimiento del pipeline de ventas.') }}</p>
                    </div>
                </div>

                <!-- FMS -->
                <div class="col-lg-4 col-md-6">
                    <div class="module-card">
                        <div class="module-icon fms">
                            <i class="ti tabler-calculator"></i>
                        </div>
                        <h3 class="module-title">{{ __('Finanzas') }}</h3>
                        <p class="module-desc">{{ __('Contabilidad, plan de cuentas, asientos contables y estados financieros.') }}</p>
                    </div>
                </div>

                <!-- Reports -->
                <div class="col-lg-4 col-md-6">
                    <div class="module-card">
                        <div class="module-icon reports">
                            <i class="ti tabler-file-analytics"></i>
                        </div>
                        <h3 class="module-title">{{ __('Reportes') }}</h3>
                        <p class="module-desc">{{ __('Generación de reportes dinámicos, dashboards y análisis de datos en tiempo real.') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer-landing">
        <div class="container">
            <p class="mb-0">© {{ date('Y') }} {{ config('app.name') }}. {{ __('Todos los derechos reservados.') }}</p>
        </div>
    </footer>

    <!-- Core JS -->
    <script src="/vuexy/vendor/libs/jquery/jquery.js"></script>
    <script src="/vuexy/vendor/js/bootstrap.js"></script>
</body>
</html>
