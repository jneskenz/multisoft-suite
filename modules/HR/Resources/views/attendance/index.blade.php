@extends('layouts.app')

@section('title', __('Asistencia'))

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap mb-6">
    <div>
        <h4 class="mb-1 fw-bold">{{ __('Asistencia') }}</h4>
        <p class="text-muted mb-0">{{ __('Control de asistencia') }}</p>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('welcome', app()->getLocale()) }}">{{ __('Inicio') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('hr.index', app()->getLocale()) }}">RRHH</a></li>
            <li class="breadcrumb-item active">{{ __('Asistencia') }}</li>
        </ol>
    </nav>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">{{ __('Registro de Asistencia') }}</h5>
    </div>
    <div class="card-body">
        <div class="alert alert-info">
            <i class="ti tabler-info-circle me-2"></i>
            {{ __('Próximamente: Control de asistencia') }}
        </div>
    </div>
</div>
@endsection
