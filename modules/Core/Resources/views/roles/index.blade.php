@extends('layouts.app')

@section('title', __('Roles'))

@php
    $group = current_group();
    $groupCode = current_group_code() ?? request()->route('group') ?? 'PE';
    $breadcrumbs = [
        'title' => '',
        'description' => '',
        'icon' => 'ti tabler-shield',
        'items' => [
            ['name' => 'Core', 'url' => url(app()->getLocale() . '/' . $groupCode . '/core')],
            ['name' => __('Administración')],
            ['name' => __('Roles')],
        ],
    ];
@endphp

@section('breadcrumb')
    <x-breadcrumbs :items="$breadcrumbs">
        <x-slot:extra>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-label-secondary" title="{{ __('Los roles son compartidos entre todos los grupos') }}">
                    <i class="ti tabler-world me-1"></i>{{ __('Global') }}
                </span>
                <span class="badge bg-label-warning">
                    <i class="ti tabler-shield"></i>
                </span>
            </div>
        </x-slot:extra>
    </x-breadcrumbs>
@endsection

@section('content')
<div>
    {{-- Card --}}
    <div class="card">
        {{-- Header con indicador global --}}
        <x-card-header title="{{ __('Gestión de Roles') }}" 
            description="{{ __('Los roles son compartidos entre todos los grupos del tenant') }}"
            textColor="text-plus" icon="ti tabler-shield" iconColor="bg-label-warning">
            @can('core.roles.create')
            <button onclick="Livewire.dispatch('openCreateModal')"
                class="btn btn-primary btn-md btn-md-normal px-1 px-md-3 waves-effect d-flex align-items-center"
                title="{{ __('Nuevo Rol') }}">
                <i class="ti tabler-plus me-md-1"></i>
                <span class="d-none d-md-inline ms-1">{{ __('Nuevo Rol') }}</span>
            </button>
            @endcan
        </x-card-header>

        {{-- Componente Livewire --}}
        <livewire:core-role-manager />
    </div>
</div>
@endsection
