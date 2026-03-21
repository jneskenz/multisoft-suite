@extends('layouts.app')

@section('title', __('Recetas'))

@php
    $group = current_group();
    $groupCode = current_group_code() ?? request()->route('group') ?? 'PE';
    $breadcrumbs = [
        'title' => '',
        'description' => '',
        'icon' => 'ti tabler-file-certificate',
        'items' => [
            ['name' => 'ERP', 'url' => url(app()->getLocale() . '/' . $groupCode . '/erp')],
            ['name' => __('Optometria')],
            ['name' => __('Recetas')],
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
                <span class="badge bg-label-info">
                    <i class="ti tabler-file-certificate"></i>
                </span>
            </div>
        </x-slot:extra>
    </x-breadcrumbs>
@endsection

@section('content')
<div>
    <div class="card">
        <x-card-header
            title="{{ __('Gestion de Recetas') }}"
            description="{{ __('Registro y seguimiento de recetas emitidas para pacientes.') }}"
            textColor="text-plus"
            icon="ti tabler-file-certificate"
            iconColor="bg-label-info"
        >
            @can('access.erp')
                <a href="{{ group_route('erp.recetas.create') }}" class="btn btn-primary btn-md btn-md-normal px-1 px-md-3 waves-effect d-flex align-items-center" title="{{ __('Nueva Receta') }}">
                    <i class="ti tabler-plus me-md-1"></i>
                    <span class="d-none d-md-inline ms-1">{{ __('Nueva Receta') }}</span>
                </a>
            @endcan
        </x-card-header>

        <livewire:erp-receta-manager />
    </div>
</div>
@endsection

