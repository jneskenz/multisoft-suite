@extends('layouts.app')

@section('title', __('Partners - Personas'))

@php
    $group = current_group();
    $groupCode = current_group_code() ?? request()->route('group') ?? 'PE';
    $breadcrumbs = [
        'title' => '',
        'description' => '',
        'icon' => 'ti tabler-users',
        'items' => [
            ['name' => __('Partners'), 'url' => route('partners.index')],
            ['name' => __('Personas')],
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
                    <i class="ti tabler-users"></i>
                </span>
            </div>
        </x-slot:extra>
    </x-breadcrumbs>
@endsection

@section('content')
<div>
    <div class="card">
        <x-card-header title="{{ __('Gestion de Personas') }}"
            description="{{ __('Personas registradas para') }} {{ $group?->business_name ?? $groupCode }}"
            textColor="text-plus" icon="ti tabler-users" iconColor="bg-label-primary">
            @can('partners.create')
                <a href="{{ route('partners.personas.create') }}"
                   class="btn btn-primary btn-md btn-md-normal px-1 px-md-3 waves-effect d-flex align-items-center"
                   title="{{ __('Nueva Persona') }}">
                    <i class="ti tabler-plus me-md-1"></i>
                    <span class="d-none d-md-inline ms-1">{{ __('Nueva Persona') }}</span>
                </a>
            @endcan
        </x-card-header>

        <div class="card-body">
            @include('partners::personas.partials.table', ['personas' => $personas])
        </div>
    </div>
</div>
@endsection
