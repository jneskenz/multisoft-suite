@extends('layouts.app')

@section('title', __('Usuarios'))

@php
    $group = current_group();
    $groupCode = current_group_code() ?? request()->route('group') ?? 'PE';
    $breadcrumbs = [
        'title' => '',
        'description' => '',
        'icon' => 'ti tabler-users',
        'items' => [
            ['name' => 'Core', 'url' => url(app()->getLocale() . '/' . $groupCode . '/core')],
            ['name' => __('Administración')],
            ['name' => __('Usuarios')],
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
                    <i class="ti tabler-users"></i>
                </span>
            </div>
        </x-slot:extra>
    </x-breadcrumbs>
@endsection

@section('content')
<div>
   {{-- Card --}}
    <div class="card">
      {{-- Header con indicador de grupo --}}
      <x-card-header title="{{ __('Gestión de Usuarios') }}" 
         description="{{ __('Usuarios con acceso a') }} {{ $group?->business_name ?? $groupCode }}"
         textColor="text-plus" icon="ti tabler-users" iconColor="bg-label-info">
         @can('core.users.create')
         <button onclick="Livewire.dispatch('openCreateModal')"
               class="btn btn-primary btn-md btn-md-normal px-1 px-md-3 waves-effect d-flex align-items-center"
               title="{{ __('Nuevo Usuario') }}">
               <i class="ti tabler-plus me-md-1"></i>
               <span class="d-none d-md-inline ms-1">{{ __('Nuevo Usuario') }}</span>
         </button>
         @endcan

      </x-card-header>

      {{-- Componente Livewire --}}
      <livewire:core-user-manager />
    </div>
</div>
@endsection
