@extends('layouts.app')

@section('title', __('Auditoría'))

@php
    $group = current_group_code() ?? request()->route('group') ?? 'PE';
    $breadcrumbs = [
        'title' => '',
        'description' => '',
        'icon' => 'ti tabler-history',
        'items' => [
            ['name' => 'Core', 'url' => url(app()->getLocale() . '/' . $group . '/core')],
            ['name' => __('Auditoría')],
        ],
    ];
@endphp

@section('breadcrumb')
    <x-breadcrumbs :items="$breadcrumbs">
        <x-slot:extra>
            <div class="d-flex align-items-center">
                <span class="badge bg-label-warning">
                    <i class="ti tabler-history"></i>
                </span>
            </div>
        </x-slot:extra>
    </x-breadcrumbs>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">{{ __('Registro de Auditoría') }}</h5>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-secondary btn-sm">
                        <i class="ti tabler-filter me-1"></i>{{ __('Filtrar') }}
                    </button>
                    <button class="btn btn-outline-primary btn-sm">
                        <i class="ti tabler-download me-1"></i>{{ __('Exportar') }}
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <div class="d-flex align-items-center">
                        <i class="ti tabler-info-circle me-2"></i>
                        <div>
                            <h6 class="mb-0">{{ __('Próximamente') }}</h6>
                            <small>{{ __('El módulo de auditoría estará disponible próximamente.') }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
