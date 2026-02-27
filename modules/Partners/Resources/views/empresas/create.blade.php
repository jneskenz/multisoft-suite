@extends('layouts.app')

@section('title', __('Partners - Nueva Empresa'))

@php
    $group = current_group();
    $groupCode = current_group_code() ?? request()->route('group') ?? 'PE';
    $breadcrumbs = [
        'title' => '',
        'description' => '',
        'icon' => 'ti tabler-building-plus',
        'items' => [
            ['name' => __('Partners'), 'url' => route('partners.index')],
            ['name' => __('Empresas'), 'url' => route('partners.empresas.index')],
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
                <span class="badge bg-label-info">
                    <i class="ti tabler-building-plus"></i>
                </span>
            </div>
        </x-slot:extra>
    </x-breadcrumbs>
@endsection

@section('content')
<div>
    <div class="card">
        <x-card-header title="{{ __('Nueva Empresa') }}"
            description="{{ __('Registra una nueva empresa para operaciones con RUC') }}"
            textColor="text-plus" icon="ti tabler-building-plus" iconColor="bg-label-info" />

        <div class="card-body">
            <form action="{{ route('partners.empresas.store') }}" method="POST" class="row g-3">
                @csrf
                @include('partners::empresas.partials.form', [
                    'empresa' => $empresa,
                    'submitLabel' => __('Guardar'),
                ])
            </form>
        </div>
    </div>
</div>
@endsection
