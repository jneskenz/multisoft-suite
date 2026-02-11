@extends('layouts.app')

@section('title', __('Grupos de Empresa'))

@php
    $group = current_group();
    $groupCode = current_group_code() ?? request()->route('group') ?? 'PE';
    $breadcrumbs = [
        'title' => '',
        'description' => '',
        'icon' => 'ti tabler-building-skyscraper',
        'items' => [
            ['name' => 'Core', 'url' => url(app()->getLocale() . '/' . $groupCode . '/core')],
            ['name' => __('Administración')],
            ['name' => __('Grupos de Empresa')],
        ],
    ];
    $isEmpty = ($grupos instanceof \Illuminate\Support\Collection) ? $grupos->isEmpty() : (count($grupos ?? []) === 0);
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
                <span class="badge bg-label-secondary">
                    <i class="ti tabler-building-community"></i>
                </span>
            </div>
        </x-slot:extra>
    </x-breadcrumbs>
@endsection

@section('content')
<div>
    <div class="card">
        <x-card-header title="{{ __('Gestión de Grupos de Empresa') }}"
            description="{{ __('Administra los países/operaciones disponibles en el tenant') }}"
            textColor="text-plus" icon="ti tabler-building-skyscraper" iconColor="bg-label-secondary">
            <a href="{{ route('core.grupo_empresa.create') }}"
               class="btn btn-primary btn-md btn-md-normal px-1 px-md-3 d-flex align-items-center">
                <i class="ti tabler-plus me-md-1"></i>
                <span class="d-none d-md-inline ms-1">{{ __('Nuevo Grupo') }}</span>
            </a>
        </x-card-header>

        <div class="card-body">
            @if($isEmpty)
                <div class="alert alert-info d-flex align-items-center mb-0">
                    <i class="ti tabler-info-circle me-2"></i>
                    <div>
                        <strong>{{ __('Sin grupos') }}.</strong>
                        <span>{{ __('Aún no has creado grupos de empresa.') }}</span>
                    </div>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th class="text-nowrap">{{ __('Código') }}</th>
                                <th>{{ __('Nombre') }}</th>
                                <th class="w-50">{{ __('Descripción') }}</th>
                                <th class="text-center">{{ __('Acciones') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($grupos as $grupo)
                                <tr>
                                    <td class="fw-semibold">
                                        <span class="badge bg-label-primary">{{ $grupo->code ?? $grupo->id }}</span>
                                    </td>
                                    <td>{{ $grupo->nombre ?? $grupo->display_name ?? 'N/A' }}</td>
                                    <td class="text-muted">{{ $grupo->descripcion ?? $grupo->business_name ?? '' }}</td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('core.grupo_empresa.show', $grupo->id) }}" class="btn btn-sm btn-icon btn-text-secondary" title="{{ __('Ver detalle') }}">
                                                <i class="ti tabler-eye"></i>
                                            </a>
                                            <a href="{{ route('core.grupo_empresa.edit', $grupo->id) }}" class="btn btn-sm btn-icon btn-text-warning" title="{{ __('Editar') }}">
                                                <i class="ti tabler-edit"></i>
                                            </a>
                                            <form action="{{ route('core.grupo_empresa.destroy', $grupo->id) }}" method="POST" onsubmit="return confirm('{{ __('�Est�s seguro de eliminar este grupo?') }}');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-icon btn-text-danger" title="{{ __('Eliminar') }}">
                                                    <i class="ti tabler-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
