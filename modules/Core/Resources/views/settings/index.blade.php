@extends('layouts.app')

@section('title', __('Configuración'))

@php
   $group = current_group_code() ?? request()->route('group') ?? 'PE';
   $breadcrumbs = [
      'title' => '',
      'description' => '',
      'icon' => 'ti tabler-settings',
      'items' => [
         ['name' => 'Core', 'url' => url(app()->getLocale() . '/' . $group . '/core')], 
         ['name' => 'Configuración', 'url' => '#']
      ],
   ];
@endphp

@section('breadcrumb')
    {{-- @include('components.breadcrumbs', $breadcrumbs) --}}
    <x-breadcrumbs :items="$breadcrumbs">
        <x-slot:extra>
            <div class="d-flex align-items-center">
                <span class="badge bg-label-primary me-2">
                    <i class="ti tabler-list"></i>
                </span>
                <span class="text-muted"></span>
            </div>
        </x-slot:extra>
        <x-slot:acciones>
        </x-slot:acciones>
    </x-breadcrumbs>
@endsection


@section('content')
    {{-- <div class="d-flex justify-content-between align-items-center flex-wrap mb-6">
    <div>
        <h4 class="mb-1 fw-bold">{{ __('Configuración') }}</h4>
        <p class="text-muted mb-0">{{ __('Ajustes del sistema') }}</p>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('welcome', app()->getLocale()) }}">{{ __('Inicio') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('core.index', app()->getLocale()) }}">Core</a></li>
            <li class="breadcrumb-item active">{{ __('Configuración') }}</li>
        </ol>
    </nav>
</div> --}}

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">{{ __('Panel de Configuración') }}</h5>
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                <i class="ti tabler-info-circle me-2"></i>
                {{ __('Próximamente: Configuración del sistema') }}
            </div>
        </div>
    </div>
@endsection
