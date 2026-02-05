@extends('layouts.app')

@section('title', __('Inventario'))

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap mb-6">
    <div>
        <h4 class="mb-1 fw-bold">{{ __('Inventario') }}</h4>
        <p class="text-muted mb-0">{{ __('Gestión de productos y stock') }}</p>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('welcome', app()->getLocale()) }}">{{ __('Inicio') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('erp.index', app()->getLocale()) }}">ERP</a></li>
            <li class="breadcrumb-item active">{{ __('Inventario') }}</li>
        </ol>
    </nav>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">{{ __('Listado de Productos') }}</h5>
        @can('erp.inventory.create')
        <button type="button" class="btn btn-primary">
            <i class="ti tabler-plus me-1"></i>{{ __('Nuevo Producto') }}
        </button>
        @endcan
    </div>
    <div class="card-body">
        <div class="alert alert-info">
            <i class="ti tabler-info-circle me-2"></i>
            {{ __('Próximamente: Gestión de inventario') }}
        </div>
    </div>
</div>
@endsection
