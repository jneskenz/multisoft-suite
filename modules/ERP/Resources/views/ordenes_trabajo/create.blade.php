@extends('layouts.app')

@section('title', __('Nueva Orden de Trabajo'))

@php
    $group = current_group();
    $groupCode = current_group_code() ?? request()->route('group') ?? 'PE';
    $breadcrumbs = [
        'title' => '',
        'description' => '',
        'icon' => 'ti tabler-clipboard-plus',
        'items' => [
            ['name' => 'ERP', 'url' => url(app()->getLocale() . '/' . $groupCode . '/erp')],
            ['name' => __('Atencion al Cliente'), 'url' => group_route('erp.work-orders.index')],
            ['name' => __('Nueva Orden de Trabajo')],
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
                <a href="{{ group_route('erp.work-orders.index') }}" class="btn btn-sm btn-outline-secondary">
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
            title="{{ __('Nueva Orden de Trabajo') }}"
            description="{{ __('Cabecera, referencias, multiples items y detalle tecnico de lentes en una sola orden.') }}"
            textColor="text-plus"
            icon="ti tabler-clipboard-plus"
            iconColor="bg-label-warning"
        />

        <livewire:erp-orden-trabajo-form />
    </div>
</div>
@endsection
