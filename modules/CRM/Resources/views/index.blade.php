@extends('layouts.app')

@section('title', __('CRM - Dashboard'))

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap mb-6">
    <div>
        <h4 class="mb-1 fw-bold">{{ __('CRM') }}</h4>
        <p class="text-muted mb-0">{{ __('Gestión de Relaciones con Clientes') }}</p>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('welcome', app()->getLocale()) }}">{{ __('Inicio') }}</a></li>
            <li class="breadcrumb-item active">CRM</li>
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
                        <span class="avatar-initial rounded bg-label-primary"><i class="ti tabler-user-search ti-28px"></i></span>
                    </div>
                    <h4 class="mb-0">0</h4>
                </div>
                <p class="mb-0">{{ __('Leads Nuevos') }}</p>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card card-border-shadow-success h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <div class="avatar me-4">
                        <span class="avatar-initial rounded bg-label-success"><i class="ti tabler-target-arrow ti-28px"></i></span>
                    </div>
                    <h4 class="mb-0">0</h4>
                </div>
                <p class="mb-0">{{ __('Oportunidades Activas') }}</p>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card card-border-shadow-warning h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <div class="avatar me-4">
                        <span class="avatar-initial rounded bg-label-warning"><i class="ti tabler-calendar-event ti-28px"></i></span>
                    </div>
                    <h4 class="mb-0">0</h4>
                </div>
                <p class="mb-0">{{ __('Actividades Pendientes') }}</p>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card card-border-shadow-info h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <div class="avatar me-4">
                        <span class="avatar-initial rounded bg-label-info"><i class="ti tabler-trophy ti-28px"></i></span>
                    </div>
                    <h4 class="mb-0">$0</h4>
                </div>
                <p class="mb-0">{{ __('Ventas Ganadas') }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Quick Access Cards -->
<div class="row g-6">
    @can('crm.leads.view')
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <div class="avatar avatar-lg mb-3">
                    <span class="avatar-initial rounded bg-label-primary"><i class="ti tabler-user-search ti-28px"></i></span>
                </div>
                <h5>{{ __('Leads') }}</h5>
                <p class="text-muted">{{ __('Gestión de prospectos') }}</p>
                <a href="{{ route('crm.leads.index', app()->getLocale()) }}" class="btn btn-primary">
                    {{ __('Acceder') }}
                </a>
            </div>
        </div>
    </div>
    @endcan

    @can('crm.opportunities.view')
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <div class="avatar avatar-lg mb-3">
                    <span class="avatar-initial rounded bg-label-success"><i class="ti tabler-target-arrow ti-28px"></i></span>
                </div>
                <h5>{{ __('Oportunidades') }}</h5>
                <p class="text-muted">{{ __('Pipeline de ventas') }}</p>
                <a href="{{ route('crm.opportunities.index', app()->getLocale()) }}" class="btn btn-success">
                    {{ __('Acceder') }}
                </a>
            </div>
        </div>
    </div>
    @endcan

    @can('crm.activities.view')
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <div class="avatar avatar-lg mb-3">
                    <span class="avatar-initial rounded bg-label-warning"><i class="ti tabler-calendar-event ti-28px"></i></span>
                </div>
                <h5>{{ __('Actividades') }}</h5>
                <p class="text-muted">{{ __('Tareas, llamadas y reuniones') }}</p>
                <a href="{{ route('crm.activities.index', app()->getLocale()) }}" class="btn btn-warning">
                    {{ __('Acceder') }}
                </a>
            </div>
        </div>
    </div>
    @endcan
</div>
@endsection
