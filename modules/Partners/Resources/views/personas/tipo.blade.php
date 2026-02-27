@extends('layouts.app')

@section('title', __('Partners - :tipo', ['tipo' => $titulo ?? 'Personas']))

@php
    $group = current_group();
    $groupCode = current_group_code() ?? request()->route('group') ?? 'PE';

    $tipo = (string) ($tipo ?? '');
    $titulo = (string) ($titulo ?? __('Personas'));

    $tipoMeta = [
        'cliente' => ['icon' => 'ti tabler-user-check', 'color' => 'bg-label-primary'],
        'proveedor' => ['icon' => 'ti tabler-truck', 'color' => 'bg-label-warning'],
        'paciente' => ['icon' => 'ti tabler-heartbeat', 'color' => 'bg-label-success'],
    ];

    $meta = $tipoMeta[$tipo] ?? ['icon' => 'ti tabler-users', 'color' => 'bg-label-info'];

    $breadcrumbs = [
        'title' => '',
        'description' => '',
        'icon' => $meta['icon'],
        'items' => [
            ['name' => __('Partners'), 'url' => route('partners.index')],
            ['name' => $titulo],
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
                <span class="badge {{ $meta['color'] }}">
                    <i class="{{ $meta['icon'] }}"></i>
                </span>
            </div>
        </x-slot:extra>
    </x-breadcrumbs>
@endsection

@section('content')
<div>
    <div class="card">
        <x-card-header title="{{ __('Gestion de :titulo', ['titulo' => $titulo]) }}"
            description="{{ __($descripcion ?? '') }}"
            textColor="text-plus" icon="{{ $meta['icon'] }}" iconColor="{{ $meta['color'] }}">
            @can('partners.create')
                <a href="{{ route('partners.personas.create', array_merge(request()->route()->parameters(), ['tipo' => $tipo])) }}"
                   class="btn btn-primary btn-md btn-md-normal px-1 px-md-3 waves-effect d-flex align-items-center"
                   title="{{ __('Nueva Persona') }}">
                    <i class="ti tabler-plus me-md-1"></i>
                    <span class="d-none d-md-inline ms-1">{{ __('Nueva Persona') }}</span>
                </a>
            @endcan
        </x-card-header>

        <div class="card-body">
            <div class="mb-3">
                <span class="badge {{ $meta['color'] }}">{{ __('Filtro') }}: {{ strtoupper($tipo) }}</span>
            </div>
            @include('partners::personas.partials.table', ['personas' => $personas])
        </div>
    </div>
</div>
@endsection
