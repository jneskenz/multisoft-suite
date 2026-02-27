@extends('layouts.app')

@section('title', __('ERP - Dashboard'))

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap mb-6">
    <div>
        <h4 class="mb-1 fw-bold">{{ __('ERP') }}</h4>
        <p class="text-muted mb-0">{{ __('Planificación de Recursos Empresariales') }}</p>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('welcome', app()->getLocale()) }}">{{ __('Inicio') }}</a></li>
            <li class="breadcrumb-item active">ERP</li>
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
                        <span class="avatar-initial rounded bg-label-primary"><i class="ti tabler-package ti-28px"></i></span>
                    </div>
                    <h4 class="mb-0">0</h4>
                </div>
                <p class="mb-0">{{ __('Productos') }}</p>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card card-border-shadow-success h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <div class="avatar me-4">
                        <span class="avatar-initial rounded bg-label-success"><i class="ti tabler-shopping-cart ti-28px"></i></span>
                    </div>
                    <h4 class="mb-0">0</h4>
                </div>
                <p class="mb-0">{{ __('Ventas Hoy') }}</p>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card card-border-shadow-warning h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <div class="avatar me-4">
                        <span class="avatar-initial rounded bg-label-warning"><i class="ti tabler-truck ti-28px"></i></span>
                    </div>
                    <h4 class="mb-0">0</h4>
                </div>
                <p class="mb-0">{{ __('Compras Pendientes') }}</p>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card card-border-shadow-danger h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <div class="avatar me-4">
                        <span class="avatar-initial rounded bg-label-danger"><i class="ti tabler-alert-triangle ti-28px"></i></span>
                    </div>
                    <h4 class="mb-0">0</h4>
                </div>
                <p class="mb-0">{{ __('Stock Bajo') }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Quick Access Cards -->
<div class="row g-6">
    @can('erp.inventory.view')
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <div class="avatar avatar-lg mb-3">
                    <span class="avatar-initial rounded bg-label-primary"><i class="ti tabler-package ti-28px"></i></span>
                </div>
                <h5>{{ __('Inventario') }}</h5>
                <p class="text-muted">{{ __('Gestión de productos y stock') }}</p>
                <a href="#" class="btn btn-primary">
                    {{ __('Acceder') }}
                </a>
            </div>
        </div>
    </div>
    @endcan

    @can('erp.sales.view')
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <div class="avatar avatar-lg mb-3">
                    <span class="avatar-initial rounded bg-label-success"><i class="ti tabler-shopping-cart ti-28px"></i></span>
                </div>
                <h5>{{ __('Ventas') }}</h5>
                <p class="text-muted">{{ __('Registro de ventas') }}</p>
                <a href="#" class="btn btn-success">
                    {{ __('Acceder') }}
                </a>
            </div>
        </div>
    </div>
    @endcan

    @can('erp.purchases.view')
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <div class="avatar avatar-lg mb-3">
                    <span class="avatar-initial rounded bg-label-warning"><i class="ti tabler-truck ti-28px"></i></span>
                </div>
                <h5>{{ __('Compras') }}</h5>
                <p class="text-muted">{{ __('Gestión de compras y proveedores') }}</p>
                <a href="#" class="btn btn-warning">
                    {{ __('Acceder') }}
                </a>
            </div>
        </div>
    </div>
    @endcan
</div>
@endsection
