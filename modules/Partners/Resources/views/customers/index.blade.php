@extends('layouts.app')

@section('title', __('Partners - Clientes'))

@php
    $group = current_group_code() ?? request()->route('group') ?? 'PE';
@endphp

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap mb-6">
    <div>
        <h4 class="mb-1 fw-bold">{{ __('Clientes') }}</h4>
        <p class="text-muted mb-0">{{ __('Módulo en construcción') }}</p>
    </div>
    <a href="{{ url(app()->getLocale() . '/' . $group . '/partners') }}" class="btn btn-outline-secondary">
        <i class="ti tabler-arrow-left me-1"></i>{{ __('Volver a Partners') }}
    </a>
</div>

<div class="card">
    <div class="card-body">
        <div class="alert alert-primary mb-0">
            {{ __('Esta sección de clientes ya está habilitada como base. Próximo paso: CRUD de clientes.') }}
        </div>
    </div>
</div>
@endsection
