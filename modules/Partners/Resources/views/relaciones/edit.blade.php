@extends('layouts.app')

@section('title', __('Partners - Editar Relacion'))

@php
    $group = current_group();
    $groupCode = current_group_code() ?? request()->route('group') ?? 'PE';
    $breadcrumbs = [
        'title' => '',
        'description' => '',
        'icon' => 'ti tabler-link-off',
        'items' => [
            ['name' => __('Partners'), 'url' => route('partners.index')],
            ['name' => __('Relaciones'), 'url' => route('partners.relaciones.index')],
            ['name' => __('Editar')],
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
                <span class="badge bg-label-warning">
                    <i class="ti tabler-link-off"></i>
                </span>
            </div>
        </x-slot:extra>
    </x-breadcrumbs>
@endsection

@section('content')
<div>
    <div class="card">
        <x-card-header title="{{ __('Editar Relacion Persona-Empresa') }}"
            description="{{ __('Actualiza el vinculo entre persona y empresa') }}"
            textColor="text-plus" icon="ti tabler-link-off" iconColor="bg-label-warning" />

        <div class="card-body">
            <form action="{{ route('partners.relaciones.update', $relacion->id) }}" method="POST" class="row g-3">
                @csrf
                @method('PUT')
                @include('partners::relaciones.partials.form', [
                    'relacion' => $relacion,
                    'personas' => $personas,
                    'empresas' => $empresas,
                    'tiposRelacion' => $tiposRelacion,
                    'submitLabel' => __('Actualizar'),
                ])
            </form>
        </div>
    </div>
</div>
@endsection
