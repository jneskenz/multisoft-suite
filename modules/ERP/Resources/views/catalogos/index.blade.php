@extends('layouts.app')

@section('title', __('Catalogos | ERP'))

@php
    $group = current_group();
    $groupCode = current_group_code() ?? (request()->route('group') ?? 'PE');
    $breadcrumbs = [
        'title' => '',
        'description' => '',
        'icon' => 'ti tabler-users',
        'items' => [
            ['name' => 'Core', 'url' => url(app()->getLocale() . '/' . $groupCode . '/core')],
            ['name' => __('Inventario')],
            ['name' => __('Catalogos')],
        ],
    ];

    $categoriaButtonIds = $categoriaButtonIds ?? [
        'MON' => 1,
        'LTE' => 2,
        'LST' => 3,
        'LCT' => 4,
        'SOL' => 5,
        'EST' => 6,
        'LIQ' => 7,
        'ACC' => 8,
        'EQP' => 9,
        'SER' => 10,
    ];

    $categoriasConMedidas = $categoriasConMedidas ?? ['LTE', 'LST', 'LCT'];
    $combinacionOptions = $combinacionOptions ?? [];
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
                    <i class="ti tabler-users"></i>
                </span>
            </div>
        </x-slot:extra>
    </x-breadcrumbs>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="nav-align-top nav-tabs-shadow">
                <ul class="nav nav-tabs flex-wrap" role="tablist">
                    <li class="nav-item" title="MONTURA">
                        <button type="button" class="nav-link px-4 py-3 active" role="tab" data-bs-toggle="tab"
                            data-bs-target="#tab_1" aria-controls="tab_1" aria-selected="true">
                            <small>MONTURAS</small>
                        </button>
                    </li>
                    <li class="nav-item" title="LENTES TERMINADOS">
                        <button type="button" class="nav-link px-4 py-3" role="tab" data-bs-toggle="tab"
                            data-bs-target="#tab_2" aria-controls="tab_2" aria-selected="false">
                            <small>L. TERMINADOS</small>
                        </button>
                    </li>
                    <li class="nav-item" title="LENTES SEMI-TERMINADOS">
                        <button type="button" class="nav-link px-4 py-3" role="tab" data-bs-toggle="tab"
                            data-bs-target="#tab_3" aria-controls="tab_3" aria-selected="false">
                            <small>L. SEMI-TERMINADOS</small>
                        </button>
                    </li>
                    <li class="nav-item" title="LENTES DE CONTACTO">
                        <button type="button" class="nav-link px-4 py-3" role="tab" data-bs-toggle="tab"
                            data-bs-target="#tab_4" aria-controls="tab_4" aria-selected="false">
                            <small>L. DE CONTACTO</small>
                        </button>
                    </li>
                    <li class="nav-item" title="LENTES SOLAR">
                        <button type="button" class="nav-link px-4 py-3" role="tab" data-bs-toggle="tab"
                            data-bs-target="#tab_5" aria-controls="tab_5" aria-selected="false">
                            <small>SOLARES</small>
                        </button>
                    </li>
                    <li class="nav-item" title="ESTUCHE">
                        <button type="button" class="nav-link px-4 py-3" role="tab" data-bs-toggle="tab"
                            data-bs-target="#tab_6" aria-controls="tab_6" aria-selected="false">
                            <small>ESTUCHES</small>
                        </button>
                    </li>
                    <li class="nav-item" title="LIQUIDOS">
                        <button type="button" class="nav-link px-4 py-3" role="tab" data-bs-toggle="tab"
                            data-bs-target="#tab_7" aria-controls="tab_7" aria-selected="false">
                            <small>LIQUIDOS</small>
                        </button>
                    </li>
                    <li class="nav-item" title="ACCESORIOS">
                        <button type="button" class="nav-link px-4 py-3" role="tab" data-bs-toggle="tab"
                            data-bs-target="#tab_8" aria-controls="tab_8" aria-selected="false">
                            <small>ACCESORIOS</small>
                        </button>
                    </li>
                    <li class="nav-item" title="EQUIPOS">
                        <button type="button" class="nav-link px-4 py-3" role="tab" data-bs-toggle="tab"
                            data-bs-target="#tab_9" aria-controls="tab_9" aria-selected="false">
                            <small>EQUIPOS</small>
                        </button>
                    </li>
                    <li class="nav-item" title="SERVICIOS">
                        <button type="button" class="nav-link px-4 py-3" role="tab" data-bs-toggle="tab"
                            data-bs-target="#tab_10" aria-controls="tab_10" aria-selected="false">
                            <small>SERVICIOS</small>
                        </button>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="tab_1" role="tabpanel">
                        <div class="card shadow-none border rounded">
                            <x-card-header title="{{ __('Listado de Monturas') }}"
                                description="{{ __('Catalogos con acceso a') }} {{ $group?->business_name ?? $groupCode }}"
                                textColor="text-plus" icon="ti tabler-users" iconColor="bg-label-info">
                                @can('erp.catalogos.create')
                                    <button type="button"
                                        class="btn btn-primary btn-md btn-md-normal px-1 px-md-3 waves-effect d-flex align-items-center"
                                        data-catalogo-open="1" data-categoria-id="{{ $categoriaButtonIds['MON'] }}"
                                        data-categoria-slot="1" data-categoria-nombre="MONTURA"
                                        data-categoria-tabla="erp_catalogos" title="{{ __('Nuevo Montura') }}">
                                        <i class="ti tabler-plus me-md-1"></i>
                                        <span class="d-none d-md-inline ms-1">{{ __('Nuevo Montura') }}</span>
                                    </button>
                                @endcan
                            </x-card-header>
                            <div class="card-body">
                                <livewire:erp-catalogo-table-manager :categoria-id="$categoriaButtonIds['MON']"
                                    categoria-nombre="MONTURA" categoria-codigo="MON"
                                    :categorias-con-medidas="$categoriasConMedidas"
                                    :key="'erp-catalogo-tab-' . $categoriaButtonIds['MON']" />
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="tab_2" role="tabpanel">
                        <div class="card shadow-none border rounded">
                            <x-card-header title="{{ __('Listado de Lentes Terminados') }}"
                                description="{{ __('Catalogos con acceso a') }} {{ $group?->business_name ?? $groupCode }}"
                                textColor="text-plus" icon="ti tabler-users" iconColor="bg-label-info">
                                @can('erp.catalogos.create')
                                    <button type="button"
                                        class="btn btn-primary btn-md btn-md-normal px-1 px-md-3 waves-effect d-flex align-items-center"
                                        data-catalogo-open="1" data-categoria-id="{{ $categoriaButtonIds['LTE'] }}"
                                        data-categoria-slot="2" data-categoria-nombre="LENTES TERMINADOS"
                                        data-categoria-tabla="erp_catalogos" title="{{ __('Nuevo Lente Terminado') }}">
                                        <i class="ti tabler-plus me-md-1"></i>
                                        <span class="d-none d-md-inline ms-1">{{ __('Nuevo Lente Terminado') }}</span>
                                    </button>
                                @endcan
                            </x-card-header>
                            <div class="card-body">
                                <livewire:erp-catalogo-table-manager :categoria-id="$categoriaButtonIds['LTE']"
                                    categoria-nombre="LENTES TERMINADOS" categoria-codigo="LTE"
                                    :categorias-con-medidas="$categoriasConMedidas"
                                    :key="'erp-catalogo-tab-' . $categoriaButtonIds['LTE']" />
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="tab_3" role="tabpanel">
                        <div class="card shadow-none border rounded">
                            <x-card-header title="{{ __('Listado de Lentes semi-terminados') }}"
                                description="{{ __('Catalogos con acceso a') }} {{ $group?->business_name ?? $groupCode }}"
                                textColor="text-plus" icon="ti tabler-users" iconColor="bg-label-info">
                                @can('erp.catalogos.create')
                                    <button type="button"
                                        class="btn btn-primary btn-md btn-md-normal px-1 px-md-3 waves-effect d-flex align-items-center"
                                        data-catalogo-open="1" data-categoria-id="{{ $categoriaButtonIds['LST'] }}"
                                        data-categoria-slot="3" data-categoria-nombre="LENTES SEMI TERMINADOS"
                                        data-categoria-tabla="erp_catalogos" title="{{ __('Nuevo Lente Semi-Terminado') }}">
                                        <i class="ti tabler-plus me-md-1"></i>
                                        <span class="d-none d-md-inline ms-1">{{ __('Nuevo Lente Semi-Terminado') }}</span>
                                    </button>
                                @endcan
                            </x-card-header>
                            <div class="card-body">
                                <livewire:erp-catalogo-table-manager :categoria-id="$categoriaButtonIds['LST']"
                                    categoria-nombre="LENTES SEMI TERMINADOS" categoria-codigo="LST"
                                    :categorias-con-medidas="$categoriasConMedidas"
                                    :key="'erp-catalogo-tab-' . $categoriaButtonIds['LST']" />
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="tab_4" role="tabpanel">
                        <div class="card shadow-none border rounded">
                            <x-card-header title="{{ __('Listado de Lentes de Contacto') }}"
                                description="{{ __('Catalogos con acceso a') }} {{ $group?->business_name ?? $groupCode }}"
                                textColor="text-plus" icon="ti tabler-users" iconColor="bg-label-info">
                                @can('erp.catalogos.create')
                                    <button type="button"
                                        class="btn btn-primary btn-md btn-md-normal px-1 px-md-3 waves-effect d-flex align-items-center"
                                        data-catalogo-open="1" data-categoria-id="{{ $categoriaButtonIds['LCT'] }}"
                                        data-categoria-slot="4" data-categoria-nombre="LENTES DE CONTACTO"
                                        data-categoria-tabla="erp_catalogos" title="{{ __('Nuevo Lente de Contacto') }}">
                                        <i class="ti tabler-plus me-md-1"></i>
                                        <span class="d-none d-md-inline ms-1">{{ __('Nuevo Lente de Contacto') }}</span>
                                    </button>
                                @endcan
                            </x-card-header>
                            <div class="card-body">
                                <livewire:erp-catalogo-table-manager :categoria-id="$categoriaButtonIds['LCT']"
                                    categoria-nombre="LENTES DE CONTACTO" categoria-codigo="LCT"
                                    :categorias-con-medidas="$categoriasConMedidas"
                                    :key="'erp-catalogo-tab-' . $categoriaButtonIds['LCT']" />
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="tab_5" role="tabpanel">
                        <div class="card shadow-none border rounded">
                            <x-card-header title="{{ __('Listado de Lente Solar') }}"
                                description="{{ __('Catalogos con acceso a') }} {{ $group?->business_name ?? $groupCode }}"
                                textColor="text-plus" icon="ti tabler-users" iconColor="bg-label-info">
                                @can('erp.catalogos.create')
                                    <button type="button"
                                        class="btn btn-primary btn-md btn-md-normal px-1 px-md-3 waves-effect d-flex align-items-center"
                                        data-catalogo-open="1" data-categoria-id="{{ $categoriaButtonIds['SOL'] }}"
                                        data-categoria-slot="5" data-categoria-nombre="SOLAR"
                                        data-categoria-tabla="erp_catalogos" title="{{ __('Nuevo Lente Solar') }}">
                                        <i class="ti tabler-plus me-md-1"></i>
                                        <span class="d-none d-md-inline ms-1">{{ __('Nuevo Lente Solar') }}</span>
                                    </button>
                                @endcan
                            </x-card-header>
                            <div class="card-body">
                                <livewire:erp-catalogo-table-manager :categoria-id="$categoriaButtonIds['SOL']"
                                    categoria-nombre="SOLAR" categoria-codigo="SOL"
                                    :categorias-con-medidas="$categoriasConMedidas"
                                    :key="'erp-catalogo-tab-' . $categoriaButtonIds['SOL']" />
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="tab_6" role="tabpanel">
                        <div class="card shadow-none border rounded">
                            <x-card-header title="{{ __('Listado de Estuches') }}"
                                description="{{ __('Catalogos con acceso a') }} {{ $group?->business_name ?? $groupCode }}"
                                textColor="text-plus" icon="ti tabler-users" iconColor="bg-label-info">
                                @can('erp.catalogos.create')
                                    <button type="button"
                                        class="btn btn-primary btn-md btn-md-normal px-1 px-md-3 waves-effect d-flex align-items-center"
                                        data-catalogo-open="1" data-categoria-id="{{ $categoriaButtonIds['EST'] }}"
                                        data-categoria-slot="6" data-categoria-nombre="ESTUCHE"
                                        data-categoria-tabla="erp_catalogos" title="{{ __('Nuevo Estuche') }}">
                                        <i class="ti tabler-plus me-md-1"></i>
                                        <span class="d-none d-md-inline ms-1">{{ __('Nuevo Estuche') }}</span>
                                    </button>
                                @endcan
                            </x-card-header>
                            <div class="card-body">
                                <livewire:erp-catalogo-table-manager :categoria-id="$categoriaButtonIds['EST']"
                                    categoria-nombre="ESTUCHE" categoria-codigo="EST"
                                    :categorias-con-medidas="$categoriasConMedidas"
                                    :key="'erp-catalogo-tab-' . $categoriaButtonIds['EST']" />
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="tab_7" role="tabpanel">
                        <div class="card shadow-none border rounded">
                            <x-card-header title="{{ __('Listado de Liquidos') }}"
                                description="{{ __('Catalogos con acceso a') }} {{ $group?->business_name ?? $groupCode }}"
                                textColor="text-plus" icon="ti tabler-users" iconColor="bg-label-info">
                                @can('erp.catalogos.create')
                                    <button type="button"
                                        class="btn btn-primary btn-md btn-md-normal px-1 px-md-3 waves-effect d-flex align-items-center"
                                        data-catalogo-open="1" data-categoria-id="{{ $categoriaButtonIds['LIQ'] }}"
                                        data-categoria-slot="7" data-categoria-nombre="LIQUIDOS"
                                        data-categoria-tabla="erp_catalogos" title="{{ __('Nuevo Liquido') }}">
                                        <i class="ti tabler-plus me-md-1"></i>
                                        <span class="d-none d-md-inline ms-1">{{ __('Nuevo Liquido') }}</span>
                                    </button>
                                @endcan
                            </x-card-header>
                            <div class="card-body">
                                <livewire:erp-catalogo-table-manager :categoria-id="$categoriaButtonIds['LIQ']"
                                    categoria-nombre="LIQUIDOS" categoria-codigo="LIQ"
                                    :categorias-con-medidas="$categoriasConMedidas"
                                    :key="'erp-catalogo-tab-' . $categoriaButtonIds['LIQ']" />
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="tab_8" role="tabpanel">
                        <div class="card shadow-none border rounded">
                            <x-card-header title="{{ __('Listado de Accesorios') }}"
                                description="{{ __('Catalogos con acceso a') }} {{ $group?->business_name ?? $groupCode }}"
                                textColor="text-plus" icon="ti tabler-users" iconColor="bg-label-info">
                                @can('erp.catalogos.create')
                                    <button type="button"
                                        class="btn btn-primary btn-md btn-md-normal px-1 px-md-3 waves-effect d-flex align-items-center"
                                        data-catalogo-open="1" data-categoria-id="{{ $categoriaButtonIds['ACC'] }}"
                                        data-categoria-slot="8" data-categoria-nombre="ACCESORIOS"
                                        data-categoria-tabla="erp_catalogos" title="{{ __('Nuevo Accesorio') }}">
                                        <i class="ti tabler-plus me-md-1"></i>
                                        <span class="d-none d-md-inline ms-1">{{ __('Nuevo Accesorio') }}</span>
                                    </button>
                                @endcan
                            </x-card-header>
                            <div class="card-body">
                                <livewire:erp-catalogo-table-manager :categoria-id="$categoriaButtonIds['ACC']"
                                    categoria-nombre="ACCESORIOS" categoria-codigo="ACC"
                                    :categorias-con-medidas="$categoriasConMedidas"
                                    :key="'erp-catalogo-tab-' . $categoriaButtonIds['ACC']" />
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="tab_9" role="tabpanel">
                        <div class="card shadow-none border rounded">
                            <x-card-header title="{{ __('Listado de Equipos') }}"
                                description="{{ __('Catalogos con acceso a') }} {{ $group?->business_name ?? $groupCode }}"
                                textColor="text-plus" icon="ti tabler-users" iconColor="bg-label-info">
                                @can('erp.catalogos.create')
                                    <button type="button"
                                        class="btn btn-primary btn-md btn-md-normal px-1 px-md-3 waves-effect d-flex align-items-center"
                                        data-catalogo-open="1" data-categoria-id="{{ $categoriaButtonIds['EQP'] }}"
                                        data-categoria-slot="9" data-categoria-nombre="EQUIPOS"
                                        data-categoria-tabla="erp_catalogos" title="{{ __('Nuevo Equipo') }}">
                                        <i class="ti tabler-plus me-md-1"></i>
                                        <span class="d-none d-md-inline ms-1">{{ __('Nuevo Equipo') }}</span>
                                    </button>
                                @endcan
                            </x-card-header>
                            <div class="card-body">
                                <livewire:erp-catalogo-table-manager :categoria-id="$categoriaButtonIds['EQP']"
                                    categoria-nombre="EQUIPOS" categoria-codigo="EQP"
                                    :categorias-con-medidas="$categoriasConMedidas"
                                    :key="'erp-catalogo-tab-' . $categoriaButtonIds['EQP']" />
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="tab_10" role="tabpanel">
                        <div class="card shadow-none border rounded">
                            <x-card-header title="{{ __('Listado de Servicios') }}"
                                description="{{ __('Catalogos con acceso a') }} {{ $group?->business_name ?? $groupCode }}"
                                textColor="text-plus" icon="ti tabler-users" iconColor="bg-label-info">
                                @can('erp.catalogos.create')
                                    <button type="button"
                                        class="btn btn-primary btn-md btn-md-normal px-1 px-md-3 waves-effect d-flex align-items-center"
                                        data-catalogo-open="1" data-categoria-id="{{ $categoriaButtonIds['SER'] }}"
                                        data-categoria-slot="10" data-categoria-nombre="SERVICIO"
                                        data-categoria-tabla="erp_catalogos" title="{{ __('Nuevo Servicio') }}">
                                        <i class="ti tabler-plus me-md-1"></i>
                                        <span class="d-none d-md-inline ms-1">{{ __('Nuevo Servicio') }}</span>
                                    </button>
                                @endcan
                            </x-card-header>
                            <div class="card-body">
                                <livewire:erp-catalogo-table-manager :categoria-id="$categoriaButtonIds['SER']"
                                    categoria-nombre="SERVICIO" categoria-codigo="SER"
                                    :categorias-con-medidas="$categoriasConMedidas"
                                    :key="'erp-catalogo-tab-' . $categoriaButtonIds['SER']" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <livewire:erp-catalogo-medidas-manager :combinacion-options="$combinacionOptions ?? []" />

    <livewire:erp-catalogo-modal-manager :catalogo-options="$catalogoOptions ?? []"
        :catalogo-category-meta="$catalogoCategoryMeta ?? []" :categoria-slot-by-id="$categoriaSlotById ?? []" />
@endsection

@push('scripts')
    <script>
        function openCatalogoMedidasModal(trigger) {
            if (!trigger) {
                return;
            }

            const registroId = Number(trigger.dataset.registroId || 0);
            const categoriaId = Number(trigger.dataset.categoriaId || 0);
            const categoriaCodigo = (trigger.dataset.categoriaCodigo || '').toString().trim().toUpperCase();

            if (typeof Livewire !== 'undefined') {
                Livewire.dispatch('erp-open-medidas-modal', {
                    catalogoId: registroId,
                    categoriaId: categoriaId,
                    categoriaCodigo: categoriaCodigo,
                    categoriaNombre: trigger.dataset.categoriaNombre || '',
                    articuloCodigo: trigger.dataset.articuloCodigo || '',
                    articuloSubcategoria: trigger.dataset.articuloSubcategoria || '',
                    articuloDescripcion: trigger.dataset.articuloDescripcion || '',
                });
            }
        }

        document.addEventListener('click', function(event) {
            const trigger = event.target.closest('[data-catalogo-open="1"]');
            if (trigger && typeof Livewire !== 'undefined') {
                event.preventDefault();

                Livewire.dispatch('erp-open-catalogo-modal', {
                    categoriaId: Number(trigger.dataset.categoriaId || 0),
                    categoriaSlot: Number(trigger.dataset.categoriaSlot || 0),
                    categoriaNombre: trigger.dataset.categoriaNombre || '',
                    categoriaTabla: trigger.dataset.categoriaTabla || 'erp_catalogos',
                    registroId: 0
                });
                return;
            }

            const editTrigger = event.target.closest('[data-catalogo-edit="1"]');
            if (editTrigger && typeof Livewire !== 'undefined') {
                event.preventDefault();

                Livewire.dispatch('erp-open-catalogo-modal', {
                    categoriaId: Number(editTrigger.dataset.categoriaId || 0),
                    categoriaSlot: 0,
                    categoriaNombre: editTrigger.dataset.categoriaNombre || '',
                    categoriaTabla: 'erp_catalogos',
                    registroId: Number(editTrigger.dataset.registroId || 0)
                });
                return;
            }

            const duplicateTrigger = event.target.closest('[data-catalogo-duplicate="1"]');
            if (duplicateTrigger && typeof Livewire !== 'undefined') {
                event.preventDefault();

                Livewire.dispatch('erp-open-catalogo-modal', {
                    categoriaId: Number(duplicateTrigger.dataset.categoriaId || 0),
                    categoriaSlot: 0,
                    categoriaNombre: duplicateTrigger.dataset.categoriaNombre || '',
                    categoriaTabla: 'erp_catalogos',
                    registroId: Number(duplicateTrigger.dataset.registroId || 0),
                    duplicar: 1
                });
                return;
            }

            const medidasTrigger = event.target.closest('[data-catalogo-medidas="1"]');
            if (medidasTrigger) {
                event.preventDefault();
                openCatalogoMedidasModal(medidasTrigger);
                return;
            }

            const deleteTrigger = event.target.closest('[data-catalogo-delete="1"]');
            if (deleteTrigger && typeof Livewire !== 'undefined') {
                event.preventDefault();

                if (!window.confirm('{{ __('Desea eliminar este registro?') }}')) {
                    return;
                }

                Livewire.dispatch('erp-delete-catalogo', {
                    categoriaId: Number(deleteTrigger.dataset.categoriaId || 0),
                    registroId: Number(deleteTrigger.dataset.registroId || 0)
                });
            }
        });

        function normalizeFilterValue(value) {
            return (value || '').toString().trim().toLowerCase();
        }

        function applyCatalogoTableFilters(wrapper) {
            if (!wrapper) {
                return;
            }

            const codigoFilter = normalizeFilterValue(wrapper.querySelector('[data-filter-codigo]')?.value);
            const subcategoriaFilter = normalizeFilterValue(wrapper.querySelector('[data-filter-subcategoria]')?.value);
            const descripcionFilter = normalizeFilterValue(wrapper.querySelector('[data-filter-descripcion]')?.value);
            const estadoFilter = (wrapper.querySelector('[data-filter-estado]')?.value || '').toString().trim();

            const rows = wrapper.querySelectorAll('tr[data-catalogo-row="1"]');
            let visibleRows = 0;

            rows.forEach((row) => {
                const rowCodigo = normalizeFilterValue(row.dataset.codigo);
                const rowSubcategoria = normalizeFilterValue(row.dataset.subcategoria);
                const rowDescripcion = normalizeFilterValue(row.dataset.descripcion);
                const rowEstado = (row.dataset.estado || '').toString().trim();

                const matchCodigo = !codigoFilter || rowCodigo.includes(codigoFilter);
                const matchSubcategoria = !subcategoriaFilter || rowSubcategoria.includes(subcategoriaFilter);
                const matchDescripcion = !descripcionFilter || rowDescripcion.includes(descripcionFilter);
                const matchEstado = !estadoFilter || rowEstado === estadoFilter;

                const visible = matchCodigo && matchSubcategoria && matchDescripcion && matchEstado;
                row.style.display = visible ? '' : 'none';
                if (visible) {
                    visibleRows += 1;
                }
            });

            const emptyRow = wrapper.querySelector('tr[data-catalogo-empty-row="1"]');
            if (emptyRow) {
                emptyRow.style.display = visibleRows === 0 ? '' : 'none';
            }
        }

        function setupCatalogoTableFilters() {
            document.querySelectorAll('[data-catalogo-table-wrapper="1"]').forEach((wrapper) => {
                if (wrapper.dataset.filtersBound === '1') {
                    applyCatalogoTableFilters(wrapper);
                    return;
                }

                const codigoInput = wrapper.querySelector('[data-filter-codigo]');
                const subcategoriaInput = wrapper.querySelector('[data-filter-subcategoria]');
                const descripcionInput = wrapper.querySelector('[data-filter-descripcion]');
                const estadoSelect = wrapper.querySelector('[data-filter-estado]');
                const clearButton = wrapper.querySelector('[data-filter-clear="1"]');

                [codigoInput, subcategoriaInput, descripcionInput].forEach((input) => {
                    if (!input) {
                        return;
                    }

                    input.addEventListener('input', () => applyCatalogoTableFilters(wrapper));
                });

                if (estadoSelect) {
                    estadoSelect.addEventListener('change', () => applyCatalogoTableFilters(wrapper));
                }

                if (clearButton) {
                    clearButton.addEventListener('click', () => {
                        if (codigoInput) {
                            codigoInput.value = '';
                        }
                        if (subcategoriaInput) {
                            subcategoriaInput.value = '';
                        }
                        if (descripcionInput) {
                            descripcionInput.value = '';
                        }
                        if (estadoSelect) {
                            estadoSelect.value = '';
                        }

                        applyCatalogoTableFilters(wrapper);
                    });
                }

                wrapper.dataset.filtersBound = '1';
                applyCatalogoTableFilters(wrapper);
            });
        }

        function setupCatalogoDetailModals() {
            document.querySelectorAll('[data-catalogo-detail-modal="1"]').forEach((modal) => {
                if (modal.parentElement !== document.body) {
                    document.body.appendChild(modal);
                }

                if (modal.dataset.boundDetail === '1') {
                    return;
                }
                modal.dataset.boundDetail = '1';

                modal.addEventListener('show.bs.modal', function(event) {
                    const trigger = event.relatedTarget;
                    if (!trigger) {
                        return;
                    }

                    const setText = (key, value) => {
                        const el = modal.querySelector(`[data-detail-target="${key}"]`);
                        if (!el) {
                            return;
                        }
                        el.textContent = (value || '').toString().trim() !== '' ? value : '-';
                    };

                    setText('id', trigger.dataset.detailId || '-');
                    setText('categoria', trigger.dataset.detailCategoria || modal.dataset.categoriaNombre || '-');
                    setText('codigo', trigger.dataset.detailCodigo || '-');
                    setText('subcategoria', trigger.dataset.detailSubcategoria || '-');
                    setText('fecha', trigger.dataset.detailFecha || '-');
                    setText('descripcion', trigger.dataset.detailDescripcion || '-');

                    const estadoEl = modal.querySelector('[data-detail-target="estado"]');
                    if (estadoEl) {
                        const estadoValue = (trigger.dataset.detailEstado || '').toString().trim();
                        const estadoLabel = (trigger.dataset.detailEstadoLabel || '').toString().trim() || '-';
                        estadoEl.textContent = estadoLabel;
                        estadoEl.className = 'badge ' + (estadoValue === '1' ? 'bg-label-success' : 'bg-label-secondary');
                    }
                });

                modal.addEventListener('shown.bs.modal', function() {
                    modal.style.zIndex = '2100';

                    const backdrops = document.querySelectorAll('.modal-backdrop');
                    if (backdrops.length > 0) {
                        const lastBackdrop = backdrops[backdrops.length - 1];
                        lastBackdrop.style.zIndex = '2090';
                    }
                });

                modal.addEventListener('hidden.bs.modal', function() {
                    modal.style.removeProperty('z-index');

                    if (document.querySelectorAll('.modal.show').length === 0) {
                        document.querySelectorAll('.modal-backdrop').forEach((backdrop) => backdrop.remove());
                        document.body.classList.remove('modal-open');
                        document.body.style.removeProperty('padding-right');
                    }
                });
            });
        }

        function bindCatalogoUiRefreshEvents() {
            if (window.__catalogoUiRefreshBound === true) {
                return;
            }
            window.__catalogoUiRefreshBound = true;

            const rebind = () => {
                setTimeout(() => {
                    setupCatalogoTableFilters();
                    setupCatalogoDetailModals();
                }, 50);
            };

            window.addEventListener('erp-catalogo-saved', rebind);
            window.addEventListener('erp-catalogo-deleted', rebind);
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function() {
                bindCatalogoUiRefreshEvents();
                setupCatalogoTableFilters();
                setupCatalogoDetailModals();
            });
        } else {
            bindCatalogoUiRefreshEvents();
            setupCatalogoTableFilters();
            setupCatalogoDetailModals();
        }
    </script>
@endpush
