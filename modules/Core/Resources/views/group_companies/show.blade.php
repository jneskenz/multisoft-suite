@extends('layouts.app')

@section('title', __('Detalle de Grupo'))

@php
    $groupCode = current_group_code() ?? request()->route('group') ?? 'PE';
    $breadcrumbs = [
        'title' => '',
        'description' => '',
        'icon' => 'ti tabler-building-community',
        'items' => [
            ['name' => 'Core', 'url' => url(app()->getLocale() . '/' . $groupCode . '/core')],
            ['name' => __('Administraci�n')],
            ['name' => __('Grupos de Empresa'), 'url' => route('core.group_companies.index')],
            ['name' => __('Detalle')],
        ],
    ];
@endphp

@section('breadcrumb')
    <x-breadcrumbs :items="$breadcrumbs" />
@endsection

@section('content')
<div>
    <div class="card">
        <x-card-header title="{{ __('Informaci�n del Grupo') }}"
            description="{{ __('Consulta los datos del grupo seleccionado') }}"
            textColor="text-plus" icon="ti tabler-building-community" iconColor="bg-label-secondary" />

        <div class="card-body">
            <div class="row gy-3">
                <div class="col-md-4">
                    <small class="text-muted">{{ __('ID') }}</small>
                    <div class="fw-semibold">{{ $grupo->id }}</div>
                </div>
                <div class="col-md-4">
                    <small class="text-muted">{{ __('Nombre') }}</small>
                    <div class="fw-semibold">{{ $grupo->nombre ?? $grupo->display_name ?? 'N/A' }}</div>
                </div>
                <div class="col-md-4">
                    <small class="text-muted">{{ __('C�digo') }}</small>
                    <div class="fw-semibold">{{ $grupo->code ?? '-' }}</div>
                </div>
                <div class="col-12">
                    <small class="text-muted">{{ __('Descripci�n') }}</small>
                    <p class="mb-0 text-muted">{{ $grupo->descripcion ?? __('Sin descripci�n') }}</p>
                </div>
            </div>

            <div class="d-flex gap-2 justify-content-end mt-4">
                <a href="{{ route('core.group_companies.index') }}" class="btn btn-outline-secondary">
                    <i class="ti tabler-arrow-left me-1"></i>{{ __('Volver') }}
                </a>
                <a href="{{ route('core.group_companies.edit', $grupo->id) }}" class="btn btn-warning">
                    <i class="ti tabler-edit me-1"></i>{{ __('Editar') }}
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
