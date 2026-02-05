@extends('layouts.app')

@section('title', __('Bienvenido'))

@section('content')
<!-- Welcome Message -->
<div class="row mb-6">
    <div class="col-12">
        <div class="card bg-transparent shadow-none border-0">
            <div class="card-body p-0">
                <h4 class="mb-1">{{ __('¡Bienvenido') }}, {{ auth()->user()->name }}! 👋</h4>
                <p class="mb-0 text-muted">{{ __('Selecciona un módulo para comenzar a trabajar') }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Module Cards -->
<div class="row g-6">
    {{-- Core Module --}}
    {{-- @can('access.core') --}}
    <div class="col-xl-4 col-lg-6 col-md-6">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-4">
                    <div class="avatar avatar-lg flex-shrink-0 me-3">
                        <span class="avatar-initial rounded bg-label-primary">
                            <i class="ti tabler-settings ti-lg"></i>
                        </span>
                    </div>
                    <div>
                        <h5 class="mb-0">Core</h5>
                        <small class="text-muted">{{ __('Administración del Sistema') }}</small>
                    </div>
                </div>
                <p class="mb-4 text-muted">{{ __('Gestiona usuarios, roles, permisos y configuración general del sistema.') }}</p>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ url(app()->getLocale() . '/core/users') }}" class="btn btn-sm btn-outline-primary">
                        <i class="ti tabler-users me-1"></i>{{ __('Usuarios') }}
                    </a>
                    <a href="{{ url(app()->getLocale() . '/core/roles') }}" class="btn btn-sm btn-outline-primary">
                        <i class="ti tabler-shield me-1"></i>{{ __('Roles') }}
                    </a>
                    <a href="{{ url(app()->getLocale() . '/core/settings') }}" class="btn btn-sm btn-outline-primary">
                        <i class="ti tabler-adjustments me-1"></i>{{ __('Config') }}
                    </a>
                </div>
            </div>
            <div class="card-footer border-top">
                <a href="{{ url(app()->getLocale() . '/core') }}" class="btn btn-primary w-100">
                    <i class="ti tabler-arrow-right me-2"></i>{{ __('Ir a Core') }}
                </a>
            </div>
        </div>
    </div>
    {{-- @endcan --}}

    {{-- ERP Module --}}
    {{-- @can('access.erp') --}}
    <div class="col-xl-4 col-lg-6 col-md-6">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-4">
                    <div class="avatar avatar-lg flex-shrink-0 me-3">
                        <span class="avatar-initial rounded bg-label-info">
                            <i class="ti tabler-building ti-lg"></i>
                        </span>
                    </div>
                    <div>
                        <h5 class="mb-0">ERP</h5>
                        <small class="text-muted">{{ __('Planificación de Recursos') }}</small>
                    </div>
                </div>
                <p class="mb-4 text-muted">{{ __('Gestiona inventarios, compras, ventas y operaciones empresariales.') }}</p>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ url(app()->getLocale() . '/erp/inventory') }}" class="btn btn-sm btn-outline-info">
                        <i class="ti tabler-package me-1"></i>{{ __('Inventario') }}
                    </a>
                    <a href="{{ url(app()->getLocale() . '/erp/sales') }}" class="btn btn-sm btn-outline-info">
                        <i class="ti tabler-receipt me-1"></i>{{ __('Ventas') }}
                    </a>
                    <a href="{{ url(app()->getLocale() . '/erp/purchases') }}" class="btn btn-sm btn-outline-info">
                        <i class="ti tabler-shopping-cart me-1"></i>{{ __('Compras') }}
                    </a>
                </div>
            </div>
            <div class="card-footer border-top">
                <a href="{{ url(app()->getLocale() . '/erp') }}" class="btn btn-info w-100">
                    <i class="ti tabler-arrow-right me-2"></i>{{ __('Ir a ERP') }}
                </a>
            </div>
        </div>
    </div>
    {{-- @endcan --}}

    {{-- HR Module --}}
    {{-- @can('access.hr') --}}
    <div class="col-xl-4 col-lg-6 col-md-6">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-4">
                    <div class="avatar avatar-lg flex-shrink-0 me-3">
                        <span class="avatar-initial rounded bg-label-success">
                            <i class="ti tabler-users ti-lg"></i>
                        </span>
                    </div>
                    <div>
                        <h5 class="mb-0">RRHH</h5>
                        <small class="text-muted">{{ __('Recursos Humanos') }}</small>
                    </div>
                </div>
                <p class="mb-4 text-muted">{{ __('Administra empleados, contratos, asistencias, planillas y vacaciones.') }}</p>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ url(app()->getLocale() . '/hr/employees') }}" class="btn btn-sm btn-outline-success">
                        <i class="ti tabler-user me-1"></i>{{ __('Empleados') }}
                    </a>
                    <a href="{{ url(app()->getLocale() . '/hr/attendance') }}" class="btn btn-sm btn-outline-success">
                        <i class="ti tabler-clock me-1"></i>{{ __('Asistencia') }}
                    </a>
                    <a href="{{ url(app()->getLocale() . '/hr/payroll') }}" class="btn btn-sm btn-outline-success">
                        <i class="ti tabler-cash me-1"></i>{{ __('Planilla') }}
                    </a>
                </div>
            </div>
            <div class="card-footer border-top">
                <a href="{{ url(app()->getLocale() . '/hr') }}" class="btn btn-success w-100">
                    <i class="ti tabler-arrow-right me-2"></i>{{ __('Ir a RRHH') }}
                </a>
            </div>
        </div>
    </div>
    {{-- @endcan --}}

    {{-- CRM Module --}}
    {{-- @can('access.crm') --}}
    <div class="col-xl-4 col-lg-6 col-md-6">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-4">
                    <div class="avatar avatar-lg flex-shrink-0 me-3">
                        <span class="avatar-initial rounded bg-label-warning">
                            <i class="ti tabler-chart-pie ti-lg"></i>
                        </span>
                    </div>
                    <div>
                        <h5 class="mb-0">CRM</h5>
                        <small class="text-muted">{{ __('Gestión de Clientes') }}</small>
                    </div>
                </div>
                <p class="mb-4 text-muted">{{ __('Gestiona leads, oportunidades, actividades y pipeline de ventas.') }}</p>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ url(app()->getLocale() . '/crm/leads') }}" class="btn btn-sm btn-outline-warning">
                        <i class="ti tabler-user-plus me-1"></i>{{ __('Leads') }}
                    </a>
                    <a href="{{ url(app()->getLocale() . '/crm/opportunities') }}" class="btn btn-sm btn-outline-warning">
                        <i class="ti tabler-bulb me-1"></i>{{ __('Oportunidades') }}
                    </a>
                    <a href="{{ url(app()->getLocale() . '/crm/activities') }}" class="btn btn-sm btn-outline-warning">
                        <i class="ti tabler-activity me-1"></i>{{ __('Actividades') }}
                    </a>
                </div>
            </div>
            <div class="card-footer border-top">
                <a href="{{ url(app()->getLocale() . '/crm') }}" class="btn btn-warning w-100">
                    <i class="ti tabler-arrow-right me-2"></i>{{ __('Ir a CRM') }}
                </a>
            </div>
        </div>
    </div>
    {{-- @endcan --}}

    {{-- FMS Module --}}
    {{-- @can('access.fms') --}}
    <div class="col-xl-4 col-lg-6 col-md-6">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-4">
                    <div class="avatar avatar-lg flex-shrink-0 me-3">
                        <span class="avatar-initial rounded bg-label-danger">
                            <i class="ti tabler-calculator ti-lg"></i>
                        </span>
                    </div>
                    <div>
                        <h5 class="mb-0">FMS</h5>
                        <small class="text-muted">{{ __('Sistema Financiero') }}</small>
                    </div>
                </div>
                <p class="mb-4 text-muted">{{ __('Plan de cuentas, asientos contables, estados financieros y reportes.') }}</p>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ url(app()->getLocale() . '/fms/accounts') }}" class="btn btn-sm btn-outline-danger">
                        <i class="ti tabler-list me-1"></i>{{ __('Cuentas') }}
                    </a>
                    <a href="{{ url(app()->getLocale() . '/fms/entries') }}" class="btn btn-sm btn-outline-danger">
                        <i class="ti tabler-notebook me-1"></i>{{ __('Asientos') }}
                    </a>
                    <a href="{{ url(app()->getLocale() . '/fms/reports') }}" class="btn btn-sm btn-outline-danger">
                        <i class="ti tabler-chart-bar me-1"></i>{{ __('Reportes') }}
                    </a>
                </div>
            </div>
            <div class="card-footer border-top">
                <a href="{{ url(app()->getLocale() . '/fms') }}" class="btn btn-danger w-100">
                    <i class="ti tabler-arrow-right me-2"></i>{{ __('Ir a FMS') }}
                </a>
            </div>
        </div>
    </div>
    {{-- @endcan --}}

    {{-- Reports Module --}}
    {{-- @can('access.reports') --}}
    <div class="col-xl-4 col-lg-6 col-md-6">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-4">
                    <div class="avatar avatar-lg flex-shrink-0 me-3">
                        <span class="avatar-initial rounded bg-label-secondary">
                            <i class="ti tabler-file-analytics ti-lg"></i>
                        </span>
                    </div>
                    <div>
                        <h5 class="mb-0">{{ __('Reportes') }}</h5>
                        <small class="text-muted">{{ __('Centro de Reportes') }}</small>
                    </div>
                </div>
                <p class="mb-4 text-muted">{{ __('Generación de reportes dinámicos, exportación y análisis de datos.') }}</p>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ url(app()->getLocale() . '/reports/generate') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="ti tabler-file-plus me-1"></i>{{ __('Generar') }}
                    </a>
                    <a href="{{ url(app()->getLocale() . '/reports/scheduled') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="ti tabler-calendar me-1"></i>{{ __('Programados') }}
                    </a>
                    <a href="{{ url(app()->getLocale() . '/reports/templates') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="ti tabler-template me-1"></i>{{ __('Plantillas') }}
                    </a>
                </div>
            </div>
            <div class="card-footer border-top">
                <a href="{{ url(app()->getLocale() . '/reports') }}" class="btn btn-secondary w-100">
                    <i class="ti tabler-arrow-right me-2"></i>{{ __('Ir a Reportes') }}
                </a>
            </div>
        </div>
    </div>
    {{-- @endcan --}}
</div>

{{-- Quick Stats --}}
<div class="row g-6 mt-2">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">{{ __('Resumen Rápido') }}</h5>
                <small class="text-muted">{{ __('Actualizado') }}: {{ now()->format('d/m/Y H:i') }}</small>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-md-3 col-6">
                        <div class="d-flex align-items-center">
                            <div class="avatar">
                                <div class="avatar-initial bg-label-primary rounded">
                                    <i class="ti tabler-users"></i>
                                </div>
                            </div>
                            <div class="ms-3">
                                <p class="mb-0 text-muted">{{ __('Usuarios') }}</p>
                                <h5 class="mb-0">{{ \App\Models\User::count() ?? 0 }}</h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="d-flex align-items-center">
                            <div class="avatar">
                                <div class="avatar-initial bg-label-info rounded">
                                    <i class="ti tabler-calendar"></i>
                                </div>
                            </div>
                            <div class="ms-3">
                                <p class="mb-0 text-muted">{{ __('Fecha') }}</p>
                                <h5 class="mb-0">{{ now()->format('d/m/Y') }}</h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="d-flex align-items-center">
                            <div class="avatar">
                                <div class="avatar-initial bg-label-success rounded">
                                    <i class="ti tabler-clock"></i>
                                </div>
                            </div>
                            <div class="ms-3">
                                <p class="mb-0 text-muted">{{ __('Hora') }}</p>
                                <h5 class="mb-0">{{ now()->format('H:i') }}</h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="d-flex align-items-center">
                            <div class="avatar">
                                <div class="avatar-initial bg-label-warning rounded">
                                    <i class="ti tabler-language"></i>
                                </div>
                            </div>
                            <div class="ms-3">
                                <p class="mb-0 text-muted">{{ __('Idioma') }}</p>
                                <h5 class="mb-0">{{ strtoupper(app()->getLocale()) }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
