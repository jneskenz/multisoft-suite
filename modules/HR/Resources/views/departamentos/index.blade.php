@extends('layouts.app')

@section('title', __('Departamentos'))

@php
    $group = current_group();
    $groupCode = current_group_code() ?? request()->route('group') ?? 'PE';
    $breadcrumbs = [
        'title' => '',
        'description' => '',
        'icon' => 'ti tabler-building',
        'items' => [
            ['name' => 'RRHH', 'url' => url(app()->getLocale() . '/' . $groupCode . '/hr')],
            ['name' => __('Config. administrativo')],
            ['name' => __('Departamentos')],
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
                    <i class="ti tabler-building"></i>
                </span>
            </div>
        </x-slot:extra>
    </x-breadcrumbs>
@endsection

@section('content')
<div>
    <div class="card">
        <x-card-header
            title="{{ __('Registros de departamentos') }}"
            description="{{ __('Departamentos del grupo') }} {{ $group?->business_name ?? $groupCode }}"
            textColor="text-plus"
            icon="ti tabler-building"
            iconColor="bg-label-info"
        >
            @canany(['hr.settings.departments.create', 'hr.empleados.create'])
                <button
                    onclick="Livewire.dispatch('openCreateModal')"
                    class="btn btn-primary btn-md btn-md-normal px-1 px-md-3 waves-effect d-flex align-items-center"
                    title="{{ __('Nuevo Departamento') }}"
                >
                    <i class="ti tabler-plus me-md-1"></i>
                    <span class="d-none d-md-inline ms-1">{{ __('Nuevo Departamento') }}</span>
                </button>
            @endcanany
        </x-card-header>

        <livewire:hr-departamento-manager />
    </div>
</div>
@endsection
