@extends('layouts.app')

@section('title', __('Editar Receta'))

@php
    $group = current_group();
    $groupCode = current_group_code() ?? request()->route('group') ?? 'PE';
    $breadcrumbs = [
        'title' => '',
        'description' => '',
        'icon' => 'ti tabler-file-certificate',
        'items' => [
            ['name' => 'ERP', 'url' => url(app()->getLocale() . '/' . $groupCode . '/erp')],
            ['name' => __('Optometria'), 'url' => group_route('erp.recetas.index')],
            ['name' => __('Editar Receta')],
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
                    <i class="ti tabler-edit"></i>
                </span>
            </div>
        </x-slot:extra>
    </x-breadcrumbs>
@endsection

@section('content')
    <livewire:erp-receta-form :receta-id="$recetaId" />
@endsection
