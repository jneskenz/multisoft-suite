@extends('layouts.app')

@section('title', __('Detalle Clinico de Receta'))

@php
    $group = current_group();
    $groupCode = current_group_code() ?? request()->route('group') ?? 'PE';
    $breadcrumbs = [
        'title' => '',
        'description' => '',
        'icon' => 'ti tabler-stethoscope',
        'items' => [
            ['name' => 'ERP', 'url' => url(app()->getLocale() . '/' . $groupCode . '/erp')],
            ['name' => __('Optometria'), 'url' => group_route('erp.recetas.index')],
            ['name' => __('Detalle Clinico')],
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
                <a href="{{ group_route('erp.recetas.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="ti tabler-arrow-left me-1"></i>{{ __('Volver') }}
                </a>
            </div>
        </x-slot:extra>
    </x-breadcrumbs>
@endsection

@section('content')
<div>
    <div class="card">
        <x-card-header
            title="{{ __('Detalle Clinico de Receta') }}"
            description="{{ __('Completa y actualiza las secciones clinicas de la receta seleccionada.') }}"
            textColor="text-plus"
            icon="ti tabler-stethoscope"
            iconColor="bg-label-info"
        />

        <livewire:erp-receta-detalle-form :receta-id="$recetaId" />
    </div>
</div>
@endsection
