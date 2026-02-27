@extends('layouts.app')

@section('title', __('Partners - Nueva Persona'))

@php
    $group = current_group();
    $groupCode = current_group_code() ?? request()->route('group') ?? 'PE';
    $breadcrumbs = [
        'title' => '',
        'description' => '',
        'icon' => 'ti tabler-user-plus',
        'items' => [
            ['name' => __('Partners'), 'url' => route('partners.index')],
            ['name' => __('Personas'), 'url' => route('partners.personas.index')],
            ['name' => __('Nuevo')],
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
                <span class="badge bg-label-primary">
                    <i class="ti tabler-user-plus"></i>
                </span>
            </div>
        </x-slot:extra>
    </x-breadcrumbs>
@endsection

@section('content')
<div>
    <div class="card">
        <x-card-header title="{{ __('Nueva Persona') }}"
            description="{{ __('Registra una nueva persona para el directorio de Partners') }}"
            textColor="text-plus" icon="ti tabler-user-plus" iconColor="bg-label-primary" />

        <div class="card-body">
            <form action="{{ route('partners.personas.store') }}" method="POST" class="row g-3">
                @csrf
                @include('partners::personas.partials.form', [
                    'persona' => $persona,
                    'tiposDisponibles' => $tiposDisponibles,
                    'tiposSeleccionados' => $tiposSeleccionados,
                    'submitLabel' => __('Guardar'),
                ])
            </form>
        </div>
    </div>
</div>
@endsection
