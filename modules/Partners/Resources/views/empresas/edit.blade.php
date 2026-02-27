@extends('layouts.app')

@section('title', __('Partners - Editar Empresa'))

@php
    $group = current_group();
    $groupCode = current_group_code() ?? request()->route('group') ?? 'PE';
    $breadcrumbs = [
        'title' => '',
        'description' => '',
        'icon' => 'ti tabler-building-cog',
        'items' => [
            ['name' => __('Partners'), 'url' => route('partners.index')],
            ['name' => __('Empresas'), 'url' => route('partners.empresas.index')],
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
                    <i class="ti tabler-building-cog"></i>
                </span>
            </div>
        </x-slot:extra>
    </x-breadcrumbs>
@endsection

@section('content')
<div>
    <div class="card">
        <x-card-header title="{{ __('Editar Empresa') }}"
            description="{{ __('Actualiza los datos de la empresa seleccionada') }}"
            textColor="text-plus" icon="ti tabler-building-cog" iconColor="bg-label-warning" />

        <div class="card-body">
            <form action="{{ route('partners.empresas.update', $empresa->id) }}" method="POST" class="row g-3">
                @csrf
                @method('PUT')
                @include('partners::empresas.partials.form', [
                    'empresa' => $empresa,
                    'submitLabel' => __('Actualizar'),
                ])
            </form>
        </div>
    </div>
</div>
@endsection
