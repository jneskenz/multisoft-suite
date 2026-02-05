@extends('layouts.app')

@section('title', __('RRHH - Dashboard'))

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap mb-6">
    <div>
        <h4 class="mb-1 fw-bold">{{ __('Recursos Humanos') }}</h4>
        <p class="text-muted mb-0">{{ __('Gestión del talento humano') }}</p>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('welcome', app()->getLocale()) }}">{{ __('Inicio') }}</a></li>
            <li class="breadcrumb-item active">RRHH</li>
        </ol>
    </nav>
</div>

<!-- Stats Cards -->
<div class="row g-6 mb-6">
    <div class="col-sm-6 col-lg-3">
        <div class="card card-border-shadow-primary h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <div class="avatar me-4">
                        <span class="avatar-initial rounded bg-label-primary"><i class="ti tabler-users ti-28px"></i></span>
                    </div>
                    <h4 class="mb-0">0</h4>
                </div>
                <p class="mb-0">{{ __('Empleados Activos') }}</p>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card card-border-shadow-success h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <div class="avatar me-4">
                        <span class="avatar-initial rounded bg-label-success"><i class="ti tabler-clock-check ti-28px"></i></span>
                    </div>
                    <h4 class="mb-0">0</h4>
                </div>
                <p class="mb-0">{{ __('Presentes Hoy') }}</p>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card card-border-shadow-warning h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <div class="avatar me-4">
                        <span class="avatar-initial rounded bg-label-warning"><i class="ti tabler-beach ti-28px"></i></span>
                    </div>
                    <h4 class="mb-0">0</h4>
                </div>
                <p class="mb-0">{{ __('Vacaciones Pendientes') }}</p>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card card-border-shadow-info h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <div class="avatar me-4">
                        <span class="avatar-initial rounded bg-label-info"><i class="ti tabler-cash ti-28px"></i></span>
                    </div>
                    <h4 class="mb-0">0</h4>
                </div>
                <p class="mb-0">{{ __('Planillas del Mes') }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Quick Access Cards -->
<div class="row g-6">
    @can('hr.employees.view')
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <div class="avatar avatar-lg mb-3">
                    <span class="avatar-initial rounded bg-label-primary"><i class="ti tabler-users ti-28px"></i></span>
                </div>
                <h5>{{ __('Empleados') }}</h5>
                <p class="text-muted">{{ __('Directorio de empleados') }}</p>
                <a href="{{ route('hr.employees.index', app()->getLocale()) }}" class="btn btn-primary">
                    {{ __('Acceder') }}
                </a>
            </div>
        </div>
    </div>
    @endcan

    @can('hr.attendance.view')
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <div class="avatar avatar-lg mb-3">
                    <span class="avatar-initial rounded bg-label-success"><i class="ti tabler-clock-check ti-28px"></i></span>
                </div>
                <h5>{{ __('Asistencia') }}</h5>
                <p class="text-muted">{{ __('Control de asistencia') }}</p>
                <a href="{{ route('hr.attendance.index', app()->getLocale()) }}" class="btn btn-success">
                    {{ __('Acceder') }}
                </a>
            </div>
        </div>
    </div>
    @endcan

    @can('hr.payroll.view')
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <div class="avatar avatar-lg mb-3">
                    <span class="avatar-initial rounded bg-label-info"><i class="ti tabler-cash ti-28px"></i></span>
                </div>
                <h5>{{ __('Planilla') }}</h5>
                <p class="text-muted">{{ __('Gestión de planillas de pago') }}</p>
                <a href="{{ route('hr.payroll.index', app()->getLocale()) }}" class="btn btn-info">
                    {{ __('Acceder') }}
                </a>
            </div>
        </div>
    </div>
    @endcan
</div>
@endsection
