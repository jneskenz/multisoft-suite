@extends('layouts.app')

@section('title', __('Partners - Dashboard'))

@php
    $group = current_group();
    $groupCode = current_group_code() ?? request()->route('group') ?? 'PE';
    $baseUrl = app()->getLocale() . '/' . $groupCode . '/partners';
    $stats = $stats ?? [];
    $breadcrumbs = [
        'title' => '',
        'description' => '',
        'icon' => 'ti tabler-address-book',
        'items' => [
            ['name' => __('Inicio'), 'url' => url(app()->getLocale() . '/' . $groupCode . '/welcome')],
            ['name' => __('Partners')],
        ],
    ];
@endphp

@section('breadcrumb')
    <x-breadcrumbs :items="$breadcrumbs">
        <x-slot:extra>
            <div class="d-flex align-items-center gap-2">
                @if($group)
                    <span class="badge bg-label-primary" title="{{ $group->business_name ?? $group->trade_name }}">
                        <i class="ti tabler-map-pin me-1"></i>{{ $group->code }}
                    </span>
                @endif
                <span class="badge bg-label-dark">
                    <i class="ti tabler-address-book"></i>
                </span>
            </div>
        </x-slot:extra>
    </x-breadcrumbs>
@endsection

@section('content')
<div>
    <div class="row g-6 mb-6">
        <div class="col-sm-6 col-lg-3">
            <div class="card card-border-shadow-primary h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded bg-label-primary"><i class="ti tabler-users ti-28px"></i></span>
                        </div>
                        <h4 class="mb-0">{{ (int) ($stats['personas'] ?? 0) }}</h4>
                    </div>
                    <p class="mb-0">{{ __('Personas') }}</p>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card card-border-shadow-info h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded bg-label-info"><i class="ti tabler-building ti-28px"></i></span>
                        </div>
                        <h4 class="mb-0">{{ (int) ($stats['empresas'] ?? 0) }}</h4>
                    </div>
                    <p class="mb-0">{{ __('Empresas') }}</p>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card card-border-shadow-warning h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded bg-label-warning"><i class="ti tabler-link ti-28px"></i></span>
                        </div>
                        <h4 class="mb-0">{{ (int) ($stats['relaciones'] ?? 0) }}</h4>
                    </div>
                    <p class="mb-0">{{ __('Relaciones Persona-Empresa') }}</p>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card card-border-shadow-success h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded bg-label-success"><i class="ti tabler-heartbeat ti-28px"></i></span>
                        </div>
                        <h4 class="mb-0">{{ (int) (($stats['clientes'] ?? 0) + ($stats['proveedores'] ?? 0) + ($stats['pacientes'] ?? 0)) }}</h4>
                    </div>
                    <p class="mb-0">{{ __('Tipos de persona activos') }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-6">
        @can('partners.personas.view')
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="avatar avatar-lg mb-3">
                        <span class="avatar-initial rounded bg-label-primary"><i class="ti tabler-users ti-28px"></i></span>
                    </div>
                    <h5>{{ __('Personas') }}</h5>
                    <p class="text-muted">{{ __('Maestro general de personas') }}</p>
                    <a href="{{ url($baseUrl . '/personas') }}" class="btn btn-primary">{{ __('Acceder') }}</a>
                </div>
            </div>
        </div>
        @endcan

        @can('partners.empresas.view')
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="avatar avatar-lg mb-3">
                        <span class="avatar-initial rounded bg-label-info"><i class="ti tabler-building ti-28px"></i></span>
                    </div>
                    <h5>{{ __('Empresas') }}</h5>
                    <p class="text-muted">{{ __('Registro fiscal y comercial de empresas') }}</p>
                    <a href="{{ url($baseUrl . '/empresas') }}" class="btn btn-info">{{ __('Acceder') }}</a>
                </div>
            </div>
        </div>
        @endcan

        @can('partners.relaciones.view')
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="avatar avatar-lg mb-3">
                        <span class="avatar-initial rounded bg-label-warning"><i class="ti tabler-link ti-28px"></i></span>
                    </div>
                    <h5>{{ __('Relaciones') }}</h5>
                    <p class="text-muted">{{ __('Vinculos entre personas y empresas') }}</p>
                    <a href="{{ url($baseUrl . '/relaciones') }}" class="btn btn-warning">{{ __('Acceder') }}</a>
                </div>
            </div>
        </div>
        @endcan
    </div>
</div>
@endsection
