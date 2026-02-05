@extends('layouts.app')

@section('title', __('Reportes - Dashboard'))

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap mb-6">
    <div>
        <h4 class="mb-1 fw-bold">{{ __('Reportes') }}</h4>
        <p class="text-muted mb-0">{{ __('Centro de reportes del sistema') }}</p>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('welcome', app()->getLocale()) }}">{{ __('Inicio') }}</a></li>
            <li class="breadcrumb-item active">{{ __('Reportes') }}</li>
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
                        <span class="avatar-initial rounded bg-label-primary"><i class="ti tabler-file-analytics ti-28px"></i></span>
                    </div>
                    <h4 class="mb-0">0</h4>
                </div>
                <p class="mb-0">{{ __('Reportes Generados') }}</p>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card card-border-shadow-success h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <div class="avatar me-4">
                        <span class="avatar-initial rounded bg-label-success"><i class="ti tabler-calendar-repeat ti-28px"></i></span>
                    </div>
                    <h4 class="mb-0">0</h4>
                </div>
                <p class="mb-0">{{ __('Programados Activos') }}</p>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card card-border-shadow-warning h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <div class="avatar me-4">
                        <span class="avatar-initial rounded bg-label-warning"><i class="ti tabler-template ti-28px"></i></span>
                    </div>
                    <h4 class="mb-0">0</h4>
                </div>
                <p class="mb-0">{{ __('Plantillas') }}</p>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card card-border-shadow-info h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <div class="avatar me-4">
                        <span class="avatar-initial rounded bg-label-info"><i class="ti tabler-download ti-28px"></i></span>
                    </div>
                    <h4 class="mb-0">0</h4>
                </div>
                <p class="mb-0">{{ __('Exportaciones Hoy') }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Quick Access Cards -->
<div class="row g-6">
    @can('reports.generate')
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <div class="avatar avatar-lg mb-3">
                    <span class="avatar-initial rounded bg-label-primary"><i class="ti tabler-file-analytics ti-28px"></i></span>
                </div>
                <h5>{{ __('Generar Reporte') }}</h5>
                <p class="text-muted">{{ __('Crear reportes personalizados') }}</p>
                <a href="{{ route('reports.generate.index', app()->getLocale()) }}" class="btn btn-primary">
                    {{ __('Acceder') }}
                </a>
            </div>
        </div>
    </div>
    @endcan

    @can('reports.schedule')
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <div class="avatar avatar-lg mb-3">
                    <span class="avatar-initial rounded bg-label-success"><i class="ti tabler-calendar-repeat ti-28px"></i></span>
                </div>
                <h5>{{ __('Programados') }}</h5>
                <p class="text-muted">{{ __('Reportes automáticos periódicos') }}</p>
                <a href="{{ route('reports.scheduled.index', app()->getLocale()) }}" class="btn btn-success">
                    {{ __('Acceder') }}
                </a>
            </div>
        </div>
    </div>
    @endcan

    @can('reports.templates.view')
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <div class="avatar avatar-lg mb-3">
                    <span class="avatar-initial rounded bg-label-warning"><i class="ti tabler-template ti-28px"></i></span>
                </div>
                <h5>{{ __('Plantillas') }}</h5>
                <p class="text-muted">{{ __('Plantillas de reportes reutilizables') }}</p>
                <a href="{{ route('reports.templates.index', app()->getLocale()) }}" class="btn btn-warning">
                    {{ __('Acceder') }}
                </a>
            </div>
        </div>
    </div>
    @endcan
</div>
@endsection
