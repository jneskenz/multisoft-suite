@extends('layouts.app')

@section('title', __('Partners - Empresas'))

@php
    $group = current_group();
    $groupCode = current_group_code() ?? request()->route('group') ?? 'PE';
    $showActions = auth()->user()?->can('partners.edit') || auth()->user()?->can('partners.delete');

    $breadcrumbs = [
        'title' => '',
        'description' => '',
        'icon' => 'ti tabler-building',
        'items' => [
            ['name' => __('Partners'), 'url' => route('partners.index')],
            ['name' => __('Empresas')],
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
                    <i class="ti tabler-building"></i>
                </span>
            </div>
        </x-slot:extra>
    </x-breadcrumbs>
@endsection

@section('content')
<div>
    <div class="card">
        <x-card-header title="{{ __('Gestion de Empresas') }}"
            description="{{ __('Empresas registradas para') }} {{ $group?->business_name ?? $groupCode }}"
            textColor="text-plus" icon="ti tabler-building" iconColor="bg-label-info">
            @can('partners.create')
                <a href="{{ route('partners.empresas.create') }}"
                   class="btn btn-primary btn-md btn-md-normal px-1 px-md-3 waves-effect d-flex align-items-center"
                   title="{{ __('Nueva Empresa') }}">
                    <i class="ti tabler-plus me-md-1"></i>
                    <span class="d-none d-md-inline ms-1">{{ __('Nueva Empresa') }}</span>
                </a>
            @endcan
        </x-card-header>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm align-middle">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 130px;">{{ __('Acciones') }}</th>
                            <th style="width: 10rem;">{{ __('RUC') }}</th>
                            <th>{{ __('Razon social') }}</th>
                            <th>{{ __('Nombre comercial') }}</th>
                            <th style="width: 16rem;">{{ __('Contacto') }}</th>
                            <th style="width: 8rem;">{{ __('Personas') }}</th>
                            <th style="width: 7rem;">{{ __('Estado') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($empresas as $empresa)
                            @php
                                $contacto = trim(implode(' · ', array_filter([
                                    (string) ($empresa->email ?? ''),
                                    (string) ($empresa->telefono ?? ''),
                                ])));
                            @endphp
                            <tr>
                                <td>
                                    <div class="d-flex justify-content-start align-items-center gap-1">
                                        <button
                                            class="btn btn-sm rounded-pill btn-icon btn-label-secondary waves-effect me-1"
                                            data-bs-toggle="tooltip" title="{{ __('Nro de registro: :id', ['id' => $empresa->id]) }}">
                                            {{ $empresa->id }}
                                        </button>

                                        @can('partners.edit')
                                            <a href="{{ route('partners.empresas.edit', $empresa->id) }}"
                                               class="btn btn-sm btn-icon btn-label-primary"
                                               data-bs-toggle="tooltip"
                                               title="{{ __('Editar empresa') }}">
                                                <i class="ti tabler-edit"></i>
                                            </a>
                                        @endcan

                                        @can('partners.delete')
                                            <form class="d-inline"
                                                  action="{{ route('partners.empresas.destroy', $empresa->id) }}"
                                                  method="POST"
                                                  onsubmit="return confirm('{{ __('Estas seguro de eliminar esta empresa?') }}');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="btn btn-sm btn-icon btn-label-danger"
                                                        data-bs-toggle="tooltip"
                                                        title="{{ __('Eliminar empresa') }}">
                                                    <i class="ti tabler-trash"></i>
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                                <td>{{ $empresa->ruc }}</td>
                                <td>{{ $empresa->razon_social }}</td>
                                <td>{{ $empresa->nombre_comercial ?: '-' }}</td>
                                <td>{{ $contacto !== '' ? $contacto : '-' }}</td>
                                <td>{{ (int) ($empresa->personas_count ?? 0) }}</td>
                                <td>
                                    <span class="badge {{ (bool) ($empresa->estado ?? false) ? 'bg-label-success' : 'bg-label-secondary' }}">
                                        {{ (bool) ($empresa->estado ?? false) ? __('Activo') : __('Inactivo') }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-3">{{ __('Sin registros.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
