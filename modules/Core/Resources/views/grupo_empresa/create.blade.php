@extends('layouts.app')

@section('title', __('Nuevo Grupo'))

@php
    $groupCode = current_group_code() ?? request()->route('group') ?? 'PE';
    $breadcrumbs = [
        'title' => '',
        'description' => '',
        'icon' => 'ti tabler-building-warehouse',
        'items' => [
            ['name' => 'Core', 'url' => url(app()->getLocale() . '/' . $groupCode . '/core')],
            ['name' => __('Administración')],
            ['name' => __('Grupos de Empresa'), 'url' => route('core.grupo_empresa.index')],
            ['name' => __('Nuevo')],
        ],
    ];
@endphp

@section('breadcrumb')
    <x-breadcrumbs :items="$breadcrumbs" />
@endsection

@section('content')
<div>
    <div class="card">
        <x-card-header title="{{ __('Nuevo Grupo de Empresa') }}"
            description="{{ __('Registra un nuevo país/operación para tu tenant') }}"
            textColor="text-plus" icon="ti tabler-building-warehouse" iconColor="bg-label-secondary" />

        <div class="card-body">
            <form action="{{ route('core.grupo_empresa.store') }}" method="POST" class="row g-3">
                @csrf
                <div class="col-md-6">
                    <label for="nombre" class="form-label">{{ __('Nombre') }}</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" value="{{ old('nombre') }}" required>
                </div>
                <div class="col-md-6">
                    <label for="descripcion" class="form-label">{{ __('Descripción') }}</label>
                    <textarea class="form-control" id="descripcion" name="descripcion" rows="1">{{ old('descripcion') }}</textarea>
                </div>

                <div class="col-12 d-flex gap-2 justify-content-end mt-2">
                    <a href="{{ route('core.grupo_empresa.index') }}" class="btn btn-outline-secondary">{{ __('Cancelar') }}</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="ti tabler-device-floppy me-1"></i>{{ __('Guardar') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
