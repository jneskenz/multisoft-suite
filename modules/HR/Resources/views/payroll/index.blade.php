@extends('layouts.app')

@section('title', __('Planilla'))

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap mb-6">
    <div>
        <h4 class="mb-1 fw-bold">{{ __('Planilla') }}</h4>
        <p class="text-muted mb-0">{{ __('Gestión de planillas de pago') }}</p>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('welcome', app()->getLocale()) }}">{{ __('Inicio') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('hr.index', app()->getLocale()) }}">RRHH</a></li>
            <li class="breadcrumb-item active">{{ __('Planilla') }}</li>
        </ol>
    </nav>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">{{ __('Planillas') }}</h5>
        @can('hr.payroll.create')
        <button type="button" class="btn btn-primary">
            <i class="ti tabler-plus me-1"></i>{{ __('Nueva Planilla') }}
        </button>
        @endcan
    </div>
    <div class="card-body">
        <div class="alert alert-info">
            <i class="ti tabler-info-circle me-2"></i>
            {{ __('Próximamente: Procesamiento de planillas') }}
        </div>
    </div>
</div>
@endsection
