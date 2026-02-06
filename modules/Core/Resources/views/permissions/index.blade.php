@extends('layouts.app')

@section('title', __('Permisos'))

@php
    $group = current_group();
    $groupCode = current_group_code() ?? request()->route('group') ?? 'PE';
    $breadcrumbs = [
        'title' => '',
        'description' => '',
        'icon' => 'ti tabler-shield-check',
        'items' => [
            ['name' => 'Core', 'url' => url(app()->getLocale() . '/' . $groupCode . '/core')],
            ['name' => __('Administración')],
            ['name' => __('Permisos')],
        ],
    ];
@endphp

@section('breadcrumb')
    <x-breadcrumbs :items="$breadcrumbs">
        <x-slot:extra>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-label-secondary" title="{{ __('Los permisos son definidos por el sistema') }}">
                    <i class="ti tabler-lock me-1"></i>{{ __('Sistema') }}
                </span>
                <span class="badge bg-label-success">
                    <i class="ti tabler-shield-check"></i>
                </span>
            </div>
        </x-slot:extra>
    </x-breadcrumbs>
@endsection

@section('content')
<div>
    {{-- Card --}}
    <div class="card">
        {{-- Header con indicador de sistema --}}
        <x-card-header title="{{ __('Catálogo de Permisos') }}" 
            description="{{ __('Permisos definidos por el sistema, compartidos globalmente') }}"
            textColor="text-plus" icon="ti tabler-shield-check" iconColor="bg-label-success">
            {{-- No hay botón de crear porque los permisos se definen en seeders --}}
        </x-card-header>

        {{-- Componente Livewire --}}
        <livewire:core-permission-manager />
    </div>
</div>
@endsection
