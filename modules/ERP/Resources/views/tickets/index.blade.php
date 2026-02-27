@extends('layouts.app')

@section('title', __('Tickets'))

@php
    $group = current_group();
    $groupCode = current_group_code() ?? request()->route('group') ?? 'PE';
    $breadcrumbs = [
        'title' => '',
        'description' => '',
        'icon' => 'ti tabler-ticket',
        'items' => [
            ['name' => 'ERP', 'url' => url(app()->getLocale() . '/' . $groupCode . '/erp')],
            ['name' => __('Atencion al Cliente')],
            ['name' => __('Tickets')],
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
                    <i class="ti tabler-ticket"></i>
                </span>
            </div>
        </x-slot:extra>
    </x-breadcrumbs>
@endsection

@section('content')
<div>
    <div class="card">
        <x-card-header
            title="{{ __('Gestion de Tickets') }}"
            description="{{ __('Atencion al cliente: historial, estado y cobranza de tickets.') }}"
            textColor="text-plus"
            icon="ti tabler-ticket"
            iconColor="bg-label-info"
        >
            @can('access.erp')
                <a href="{{ group_route('erp.tickets.create') }}" class="btn btn-primary btn-md btn-md-normal px-1 px-md-3 waves-effect d-flex align-items-center" title="{{ __('Nuevo Ticket') }}">
                    <i class="ti tabler-plus me-md-1"></i>
                    <span class="d-none d-md-inline ms-1">{{ __('Nuevo Ticket') }}</span>
                </a>
            @endcan
        </x-card-header>

        <livewire:erp-ticket-manager />
    </div>
</div>
@endsection
