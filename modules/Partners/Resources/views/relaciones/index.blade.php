@extends('layouts.app')

@section('title', __('Partners - Relaciones Persona-Empresa'))

@php
    $group = current_group();
    $groupCode = current_group_code() ?? request()->route('group') ?? 'PE';
    $showActions = auth()->user()?->can('partners.edit') || auth()->user()?->can('partners.delete');

    $breadcrumbs = [
        'title' => '',
        'description' => '',
        'icon' => 'ti tabler-link',
        'items' => [
            ['name' => __('Partners'), 'url' => route('partners.index')],
            ['name' => __('Relaciones')],
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
                    <i class="ti tabler-link"></i>
                </span>
            </div>
        </x-slot:extra>
    </x-breadcrumbs>
@endsection

@section('content')
<div>
    <div class="card">
        <x-card-header title="{{ __('Gestion de Relaciones Persona-Empresa') }}"
            description="{{ __('Relaciones registradas para') }} {{ $group?->business_name ?? $groupCode }}"
            textColor="text-plus" icon="ti tabler-link" iconColor="bg-label-warning">
            @can('partners.create')
                <a href="{{ route('partners.relaciones.create') }}"
                   class="btn btn-primary btn-md btn-md-normal px-1 px-md-3 waves-effect d-flex align-items-center"
                   title="{{ __('Nueva Relacion') }}">
                    <i class="ti tabler-plus me-md-1"></i>
                    <span class="d-none d-md-inline ms-1">{{ __('Nueva Relacion') }}</span>
                </a>
            @endcan
        </x-card-header>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm align-middle">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 130px;">{{ __('Acciones') }}</th>
                            <th>{{ __('Persona') }}</th>
                            <th>{{ __('Empresa') }}</th>
                            <th style="width: 11rem;">{{ __('Tipo relacion') }}</th>
                            <th style="width: 8rem;">{{ __('Principal') }}</th>
                            <th style="width: 7rem;">{{ __('Estado') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($relaciones as $relacion)
                            @php
                                $persona = $relacion->persona;
                                $personaNombre = trim((string) ($persona->nombre_completo ?? ''));
                                if ($personaNombre === '') {
                                    $personaNombre = trim(implode(' ', array_filter([
                                        $persona->nombres ?? '',
                                        $persona->apellido_paterno ?? '',
                                        $persona->apellido_materno ?? '',
                                    ])));
                                }

                                $empresa = $relacion->empresa;
                                $empresaNombre = trim((string) ($empresa->razon_social ?? ''));
                                $empresaRuc = trim((string) ($empresa->ruc ?? ''));
                            @endphp
                            <tr>
                                <td>
                                    <div class="d-flex justify-content-start align-items-center gap-1">
                                        <button
                                            class="btn btn-sm rounded-pill btn-icon btn-label-secondary waves-effect me-1"
                                            data-bs-toggle="tooltip" title="{{ __('Nro de registro: :id', ['id' => $relacion->id]) }}">
                                            {{ $relacion->id }}
                                        </button>

                                        @can('partners.edit')
                                            <a href="{{ route('partners.relaciones.edit', $relacion->id) }}"
                                               class="btn btn-sm btn-icon btn-label-primary"
                                               data-bs-toggle="tooltip"
                                               title="{{ __('Editar relacion') }}">
                                                <i class="ti tabler-edit"></i>
                                            </a>
                                        @endcan

                                        @can('partners.delete')
                                            <form class="d-inline"
                                                  action="{{ route('partners.relaciones.destroy', $relacion->id) }}"
                                                  method="POST"
                                                  onsubmit="return confirm('{{ __('Estas seguro de eliminar esta relacion?') }}');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="btn btn-sm btn-icon btn-label-danger"
                                                        data-bs-toggle="tooltip"
                                                        title="{{ __('Eliminar relacion') }}">
                                                    <i class="ti tabler-trash"></i>
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                                <td>{{ $personaNombre !== '' ? $personaNombre : '-' }}</td>
                                <td>
                                    {{ $empresaNombre !== '' ? $empresaNombre : '-' }}
                                    @if ($empresaRuc !== '')
                                        <small class="text-muted d-block">RUC: {{ $empresaRuc }}</small>
                                    @endif
                                </td>
                                <td>{{ ucfirst((string) ($relacion->tipo_relacion ?? '')) }}</td>
                                <td>
                                    <span class="badge {{ (bool) ($relacion->es_principal ?? false) ? 'bg-label-primary' : 'bg-label-secondary' }}">
                                        {{ (bool) ($relacion->es_principal ?? false) ? __('Si') : __('No') }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge {{ (bool) ($relacion->estado ?? false) ? 'bg-label-success' : 'bg-label-secondary' }}">
                                        {{ (bool) ($relacion->estado ?? false) ? __('Activo') : __('Inactivo') }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-3">{{ __('Sin registros.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
