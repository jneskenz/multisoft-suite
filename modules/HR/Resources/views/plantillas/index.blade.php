@extends('layouts.app')

@section('title', __('Plantillas'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('vuexy/vendor/libs/quill/typography.css') }}">
    <link rel="stylesheet" href="{{ asset('vuexy/vendor/libs/quill/editor.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('vuexy/vendor/libs/quill/quill.js') }}"></script>
@endpush

@php
    $group = current_group();
    $groupCode = current_group_code() ?? (request()->route('group') ?? 'PE');
    $breadcrumbs = [
        'title' => '',
        'description' => '',
        'icon' => 'ti tabler-briefcase',
        'items' => [
            ['name' => 'RRHH', 'url' => url(app()->getLocale() . '/' . $groupCode . '/hr')],
            ['name' => __('Config. administrativo')],
            ['name' => __('Plantillas')],
        ],
    ];
@endphp

@section('breadcrumb')
    <x-breadcrumbs :items="$breadcrumbs">
        <x-slot:extra>
            <div class="d-flex align-items-center gap-2">
                @if ($group)
                    <span class="badge bg-label-primary" title="{{ $group->business_name ?? $group->trade_name }}">
                        <i class="ti tabler-map-pin me-1"></i>{{ $group->code }}
                    </span>
                @endif
                <span class="badge bg-label-info">
                    <i class="ti tabler-briefcase"></i>
                </span>
            </div>
        </x-slot:extra>
    </x-breadcrumbs>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card text-center">
                <div class="card-header">
                    <div class="nav-align-top">
                        <ul class="nav nav-pills" role="tablist">
                            <li class="nav-item">
                                <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab"
                                    data-bs-target="#tab_plantillas"
                                    aria-controls="tab_plantillas" aria-selected="true">
                                    Plantillas
                                </button>
                            </li>
                            <li class="nav-item">
                                <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
                                    data-bs-target="#tab_tipo-documento"
                                    aria-controls="tab_tipo-documento" aria-selected="false">
                                    Tipo documentos
                                </button>
                            </li>
                            <li class="nav-item">
                                <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
                                    data-bs-target="#tab_categorias"
                                    aria-controls="tab_categorias" aria-selected="false">
                                    Categorías
                                </button>
                            </li>
                            <li class="nav-item">
                                <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
                                    data-bs-target="#tab_secciones-reutilizables"
                                    aria-controls="tab_secciones-reutilizables" aria-selected="false">
                                    Secciones reutilizables
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    <div class="tab-content p-0">
                        <div class="tab-pane fade show active" id="tab_plantillas" role="tabpanel">
                            <div class="border rounded">
                                <x-card-header
                                    title="{{ __('Registros de plantillas') }}"
                                    description="{{ __('Gestion de plantillas por departamento') }}"
                                    textColor="text-plus"
                                    icon="ti tabler-briefcase"
                                    iconColor="bg-label-info"
                                >
                                    @canany(['hr.settings.positions.create', 'hr.empleados.create'])
                                        <button
                                            onclick="Livewire.dispatch('openCreatePlantillaModal')"
                                            class="btn btn-primary btn-md btn-md-normal px-1 px-md-3 waves-effect d-flex align-items-center"
                                            title="{{ __('Nueva Plantilla') }}"
                                        >
                                            <i class="ti tabler-plus me-md-1"></i>
                                            <span class="d-none d-md-inline ms-1">{{ __('Nueva Plantilla') }}</span>
                                        </button>
                                    @endcanany
                                </x-card-header>
                                <livewire:hr-plantilla-documento-manager />

                            </div>
                        </div>
                        <div class="tab-pane fade" id="tab_tipo-documento" role="tabpanel">
                            <div class="border rounded">
                                <x-card-header
                                    title="{{ __('Registros de tipo documentos') }}"
                                    description="{{ __('Gestiona de tipo documentos') }}"
                                    textColor="text-plus"
                                    icon="ti tabler-briefcase"
                                    iconColor="bg-label-info"
                                >
                                    @canany(['hr.settings.positions.create', 'hr.empleados.create'])
                                        <button
                                            onclick="Livewire.dispatch('openCreateTipoModal')"
                                            class="btn btn-primary btn-md btn-md-normal px-1 px-md-3 waves-effect d-flex align-items-center"
                                            title="{{ __('Nuevo tipo') }}"
                                        >
                                            <i class="ti tabler-plus me-md-1"></i>
                                            <span class="d-none d-md-inline ms-1">{{ __('Nuevo tipo') }}</span>
                                        </button>
                                    @endcanany
                                </x-card-header>

                                <livewire:hr-tipo-documento-manager />

                            </div>
                        </div>
                        <div class="tab-pane fade" id="tab_categorias" role="tabpanel">
                            <div class="border rounded">
                                <x-card-header
                                    title="{{ __('Registros de categorías de documento') }}"
                                    description="{{ __('Gestiona las categorías para clasificar documentos dentro de cada tipo') }}"
                                    textColor="text-plus"
                                    icon="ti tabler-category"
                                    iconColor="bg-label-info"
                                >
                                    @canany(['hr.settings.positions.create', 'hr.empleados.create'])
                                        <button
                                            onclick="Livewire.dispatch('openCreateCategoriaModal')"
                                            class="btn btn-primary btn-md btn-md-normal px-1 px-md-3 waves-effect d-flex align-items-center"
                                            title="{{ __('Nueva Categoría') }}"
                                        >
                                            <i class="ti tabler-plus me-md-1"></i>
                                            <span class="d-none d-md-inline ms-1">{{ __('Nueva Categoría') }}</span>
                                        </button>
                                    @endcanany
                                </x-card-header>

                                <livewire:hr-categoria-documento-manager />

                            </div>
                        </div>
                        <div class="tab-pane fade" id="tab_secciones-reutilizables" role="tabpanel">
                            <div class="border rounded">
                                <x-card-header
                                    title="{{ __('Registros de Secciones') }}"
                                    description="{{ __('Gestiona bloques de contenido que puedes reutilizar en múltiples plantillas') }}"
                                    textColor="text-plus"
                                    icon="ti tabler-briefcase"
                                    iconColor="bg-label-info"
                                >
                                    @canany(['hr.settings.positions.create', 'hr.empleados.create'])
                                        <button
                                            onclick="Livewire.dispatch('openCreateSeccionModal')"
                                            class="btn btn-primary btn-md btn-md-normal px-1 px-md-3 waves-effect d-flex align-items-center"
                                            title="{{ __('Nueva Sección') }}"
                                        >
                                            <i class="ti tabler-plus me-md-1"></i>
                                            <span class="d-none d-md-inline ms-1">{{ __('Nueva Sección') }}</span>
                                        </button>
                                    @endcanany
                                </x-card-header>

                                <livewire:hr-seccion-manager />

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
