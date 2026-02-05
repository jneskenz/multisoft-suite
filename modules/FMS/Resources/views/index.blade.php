@extends('layouts.app')

@section('title', __('FMS - Dashboard'))

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap mb-6">
    <div>
        <h4 class="mb-1 fw-bold">{{ __('Sistema Financiero') }}</h4>
        <p class="text-muted mb-0">{{ __('Gestión contable y financiera') }}</p>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('welcome', app()->getLocale()) }}">{{ __('Inicio') }}</a></li>
            <li class="breadcrumb-item active">FMS</li>
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
                        <span class="avatar-initial rounded bg-label-primary"><i class="ti tabler-list-tree ti-28px"></i></span>
                    </div>
                    <h4 class="mb-0">0</h4>
                </div>
                <p class="mb-0">{{ __('Cuentas Activas') }}</p>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card card-border-shadow-success h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <div class="avatar me-4">
                        <span class="avatar-initial rounded bg-label-success"><i class="ti tabler-receipt ti-28px"></i></span>
                    </div>
                    <h4 class="mb-0">0</h4>
                </div>
                <p class="mb-0">{{ __('Asientos del Mes') }}</p>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card card-border-shadow-warning h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <div class="avatar me-4">
                        <span class="avatar-initial rounded bg-label-warning"><i class="ti tabler-arrow-up ti-28px"></i></span>
                    </div>
                    <h4 class="mb-0">$0</h4>
                </div>
                <p class="mb-0">{{ __('Total Ingresos') }}</p>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card card-border-shadow-danger h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <div class="avatar me-4">
                        <span class="avatar-initial rounded bg-label-danger"><i class="ti tabler-arrow-down ti-28px"></i></span>
                    </div>
                    <h4 class="mb-0">$0</h4>
                </div>
                <p class="mb-0">{{ __('Total Egresos') }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Quick Access Cards -->
<div class="row g-6">
    @can('fms.accounts.view')
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <div class="avatar avatar-lg mb-3">
                    <span class="avatar-initial rounded bg-label-primary"><i class="ti tabler-list-tree ti-28px"></i></span>
                </div>
                <h5>{{ __('Plan de Cuentas') }}</h5>
                <p class="text-muted">{{ __('Catálogo de cuentas contables') }}</p>
                <a href="{{ route('fms.accounts.index', app()->getLocale()) }}" class="btn btn-primary">
                    {{ __('Acceder') }}
                </a>
            </div>
        </div>
    </div>
    @endcan

    @can('fms.entries.view')
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <div class="avatar avatar-lg mb-3">
                    <span class="avatar-initial rounded bg-label-success"><i class="ti tabler-receipt ti-28px"></i></span>
                </div>
                <h5>{{ __('Asientos Contables') }}</h5>
                <p class="text-muted">{{ __('Registro de movimientos contables') }}</p>
                <a href="{{ route('fms.entries.index', app()->getLocale()) }}" class="btn btn-success">
                    {{ __('Acceder') }}
                </a>
            </div>
        </div>
    </div>
    @endcan

    @can('fms.reports.view')
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <div class="avatar avatar-lg mb-3">
                    <span class="avatar-initial rounded bg-label-info"><i class="ti tabler-chart-bar ti-28px"></i></span>
                </div>
                <h5>{{ __('Estados Financieros') }}</h5>
                <p class="text-muted">{{ __('Balance y estado de resultados') }}</p>
                <a href="{{ route('fms.reports.index', app()->getLocale()) }}" class="btn btn-info">
                    {{ __('Acceder') }}
                </a>
            </div>
        </div>
    </div>
    @endcan
</div>
@endsection
