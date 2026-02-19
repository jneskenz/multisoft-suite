<div>
    <div class="card-body">
        {{-- Flash Messages --}}
        @if (session()->has('message'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="ti tabler-check me-1"></i>{{ session('message') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row g-3">
            {{-- Búsqueda --}}
            <div class="col-12 col-md-4">
                <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="ti tabler-search"></i></span>
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        class="form-control"
                        placeholder="{{ __('Buscar por Nro contrato, empleado...') }}"
                    >
                    <span class="input-group-text cursor-pointer">
                        <div wire:loading wire:target="search" class="spinner-border spinner-border-sm text-primary" role="status">
                            <span class="visually-hidden">Buscando...</span>
                        </div>
                        @if ($search)
                            <i class="ti tabler-x" wire:click="$set('search', '')"></i>
                        @endif
                    </span>
                </div>
            </div>

            {{-- Filtro Tipo --}}
            <div class="col-6 col-md-2">
                <select wire:model.live="tipoFilter" class="form-select">
                    <option value="">{{ __('Tipo: Todos') }}</option>
                    @foreach ($this->tiposContrato as $tipo)
                        <option value="{{ $tipo->id }}">{{ $tipo->nombre }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Filtro Estado --}}
            <div class="col-6 col-md-2">
                <select wire:model.live="estadoFilter" class="form-select">
                    <option value="">{{ __('Estado: Todos') }}</option>
                    <option value="vigente">{{ __('Vigentes') }}</option>
                    <option value="por_vencer">{{ __('Por vencer') }}</option>
                    <option value="vencido">{{ __('Vencidos') }}</option>
                    @foreach (\Modules\HR\Enums\EstadoContrato::options() as $opt)
                        <option value="{{ $opt['value'] }}">{{ $opt['label'] }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Per Page --}}
            <div class="col-6 col-md-2">
                <select wire:model.live="perPage" class="form-select">
                    <option value="10">10 por pagina</option>
                    <option value="25">25 por pagina</option>
                    <option value="50">50 por pagina</option>
                </select>
            </div>

            {{-- Acciones de filtro --}}
            <div class="col-12 col-md-2 d-flex gap-2">
                @if ($search || $tipoFilter || $estadoFilter)
                    <button wire:click="clearFilters" class="btn btn-outline-secondary flex-fill" title="{{ __('Limpiar filtros') }}">
                        <i class="ti tabler-filter-off"></i>
                    </button>
                @endif
                <button
                    wire:click="toggleTrashedContratos"
                    class="btn {{ $showTrashedContratos ? 'btn-danger' : 'btn-outline-secondary' }}"
                    title="{{ $showTrashedContratos ? __('Ver contratos activos') : __('Ver contratos eliminados') }}"
                >
                    <i class="ti {{ $showTrashedContratos ? 'tabler-file-text' : 'tabler-trash' }}"></i>
                </button>
            </div>

            @if ($showTrashedContratos)
                <div class="col-12">
                    <div class="alert alert-warning d-flex align-items-center mb-0" role="alert">
                        <i class="ti tabler-trash me-2"></i>
                        <span>{{ __('Mostrando contratos eliminados. Puedes restaurarlos o eliminarlos permanentemente.') }}</span>
                    </div>
                </div>
            @endif

            {{-- ═══ TABLA ═══ --}}
            <div class="table-responsive">
                <table class="table table-hover table-sm align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width: 130px;">{{ __('Acciones') }}</th>
                            <th style="width: 120px;">{{ __('Estado') }}</th>
                            <th wire:click="sortBy('numero_contrato')" class="cursor-pointer" style="min-width: 140px;">
                                <div class="d-flex align-items-center">
                                    {{ __('Nro. Contrato') }}
                                    @if ($sortField === 'numero_contrato')
                                        <i class="ti tabler-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ms-1"></i>
                                    @endif
                                </div>
                            </th>
                            <th style="min-width: 200px;">{{ __('Empleado') }}</th>
                            <th style="min-width: 150px;">{{ __('Tipo') }}</th>
                            <th wire:click="sortBy('fecha_inicio')" class="cursor-pointer" style="width: 120px;">
                                <div class="d-flex align-items-center">
                                    {{ __('Inicio') }}
                                    @if ($sortField === 'fecha_inicio')
                                        <i class="ti tabler-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ms-1"></i>
                                    @endif
                                </div>
                            </th>
                            <th style="width: 120px;">{{ __('Fin') }}</th>
                            <th style="width: 120px;" class="text-end">{{ __('Salario') }}</th>
                            <th wire:click="sortBy('created_at')" class="cursor-pointer" style="width: 140px;">
                                <div class="d-flex align-items-center">
                                    {{ __('Creado') }}
                                    @if ($sortField === 'created_at')
                                        <i class="ti tabler-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ms-1"></i>
                                    @endif
                                </div>
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($this->contratos as $contrato)
                            <tr wire:key="contrato-{{ $contrato->id }}">
                                {{-- Acciones --}}
                                <td>
                                    <div class="d-flex justify-content-start align-items-center gap-1">
                                        <button
                                            class="btn btn-sm rounded-pill btn-icon btn-label-secondary waves-effect me-1"
                                            data-bs-toggle="tooltip"
                                            title="Nro de registro: {{ $contrato->id }}"
                                        >
                                            {{ $contrato->id }}
                                        </button>

                                        <button
                                            wire:click="edit({{ $contrato->id }})"
                                            class="btn btn-sm btn-icon btn-label-primary"
                                            data-bs-toggle="tooltip"
                                            title="{{ __('Editar contrato') }}"
                                        >
                                            <i class="ti tabler-edit icon-18px"></i>
                                        </button>

                                        <div class="dropdown">
                                            <button
                                                class="btn btn-sm btn-icon border btn-text-secondary rounded dropdown-toggle hide-arrow"
                                                data-bs-toggle="dropdown"
                                            >
                                                <i class="ti tabler-dots-vertical"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                @if (!$showTrashedContratos)
                                                    {{-- Generar documento --}}
                                                    @if ($contrato->tipo_contrato_id)
                                                        <li>
                                                            <a class="dropdown-item d-flex p-2 justify-content-start btn btn-label-info align-items-center"
                                                               wire:click="openGenerarDocModal({{ $contrato->id }})">
                                                                <button class="btn btn-sm btn-icon btn-label-info me-2">
                                                                    <i class="ti tabler-file-plus icon-18px"></i>
                                                                </button>
                                                                <span class="lh-1">{{ __('Generar documento') }}</span>
                                                            </a>
                                                        </li>
                                                    @endif
                                                    {{-- Ver documentos generados --}}
                                                    @if ($contrato->documentosGenerados && $contrato->documentosGenerados->count() > 0)
                                                        <li>
                                                            <a class="dropdown-item d-flex p-2 justify-content-start btn btn-label-success align-items-center"
                                                               href="{{ group_route('hr.contratos.ver-documento', ['documento' => $contrato->documentosGenerados->last()->id]) }}"
                                                               target="_blank">
                                                                <button class="btn btn-sm btn-icon btn-label-success me-2">
                                                                    <i class="ti tabler-file-text icon-18px"></i>
                                                                </button>
                                                                <span class="lh-1">{{ __('Ver documento') }}</span>
                                                            </a>
                                                        </li>
                                                        <li><hr class="dropdown-divider"></li>
                                                    @endif
                                                    @if ($contrato->is_vigente)
                                                        <li>
                                                            <a class="dropdown-item d-flex p-2 justify-content-start btn btn-label-dark align-items-center"
                                                               wire:click="confirmTerminar({{ $contrato->id }})">
                                                                <button class="btn btn-sm btn-icon btn-label-dark me-2">
                                                                    <i class="ti tabler-flag icon-18px"></i>
                                                                </button>
                                                                <span class="lh-1">{{ __('Terminar contrato') }}</span>
                                                            </a>
                                                        </li>
                                                        <li><hr class="dropdown-divider"></li>
                                                    @endif
                                                    <li>
                                                        <a class="dropdown-item d-flex p-2 justify-content-start btn btn-label-danger align-items-center"
                                                           wire:click="confirmDelete({{ $contrato->id }})">
                                                            <button class="btn btn-sm btn-icon btn-label-danger me-2">
                                                                <i class="ti tabler-trash icon-18px"></i>
                                                            </button>
                                                            <span class="lh-1">{{ __('Eliminar') }}</span>
                                                        </a>
                                                    </li>
                                                @else
                                                    <li>
                                                        <a class="dropdown-item d-flex p-2 justify-content-start btn btn-label-success align-items-center"
                                                           wire:click="confirmRestore({{ $contrato->id }})">
                                                            <button class="btn btn-sm btn-icon btn-label-success me-2">
                                                                <i class="ti tabler-refresh icon-18px"></i>
                                                            </button>
                                                            <span class="lh-1">{{ __('Restaurar') }}</span>
                                                        </a>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <a class="dropdown-item d-flex p-2 justify-content-start btn btn-label-dark align-items-center"
                                                           wire:click="confirmForceDelete({{ $contrato->id }})">
                                                            <button class="btn btn-sm btn-icon btn-label-dark me-2">
                                                                <i class="ti tabler-trash-x icon-18px"></i>
                                                            </button>
                                                            <span class="lh-1">{{ __('Eliminar permanente') }}</span>
                                                        </a>
                                                    </li>
                                                @endif
                                            </ul>
                                        </div>
                                    </div>
                                </td>

                                {{-- Estado --}}
                                <td>
                                    @if ($contrato->is_por_vencer)
                                        <span class="badge bg-label-warning">
                                            <i class="ti tabler-alert-triangle ti-xs me-1"></i>{{ __('Por vencer') }}
                                        </span>
                                    @else
                                        <span class="badge {{ $contrato->estado_badge_class }}">
                                            {{ $contrato->estado_contrato?->label() ?? __('Borrador') }}
                                        </span>
                                    @endif
                                </td>

                                {{-- Número --}}
                                <td>
                                    <span class="fw-medium">{{ $contrato->numero_contrato }}</span>
                                </td>

                                {{-- Empleado --}}
                                <td>
                                    <div>
                                        <h6 class="mb-0">{{ $contrato->empleado?->nombre }}</h6>
                                        @if($contrato->empleado?->codigo_empleado)
                                            <small class="text-muted">{{ $contrato->empleado->codigo_empleado }}</small>
                                        @endif
                                        @if($contrato->empleado?->company)
                                            <small class="text-muted"> · {{ $contrato->empleado->company->name }}</small>
                                        @endif
                                    </div>
                                </td>

                                {{-- Tipo --}}
                                <td>
                                    <span>{{ $contrato->tipoContrato?->nombre ?? '-' }}</span>
                                    @if ($contrato->modalidad)
                                        <br><small class="text-muted">{{ $contrato->modalidad->nombre }}</small>
                                    @endif
                                </td>

                                {{-- Fechas --}}
                                <td>{{ $contrato->fecha_inicio?->format('d/m/Y') }}</td>
                                <td>
                                    @if ($contrato->fecha_fin)
                                        {{ $contrato->fecha_fin->format('d/m/Y') }}
                                    @else
                                        <span class="badge bg-label-success">{{ __('Indefinido') }}</span>
                                    @endif
                                </td>

                                {{-- Salario --}}
                                <td class="text-end">
                                    @if ($contrato->salario_base)
                                        S/ {{ number_format($contrato->salario_base, 2) }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>

                                {{-- Creado --}}
                                <td>
                                    <span title="{{ optional($contrato->created_at)->format('d/m/Y H:i') }}">
                                        {{ optional($contrato->created_at)->diffForHumans() }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="ti tabler-file-off icon-48px text-muted mb-3"></i>
                                        <h6 class="mb-1">{{ __('No se encontraron contratos') }}</h6>
                                        <p class="text-muted mb-0">
                                            @if ($search || $tipoFilter || $estadoFilter)
                                                {{ __('Intenta ajustar los filtros de búsqueda') }}
                                            @else
                                                {{ __('Aún no hay contratos registrados') }}
                                            @endif
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Paginación --}}
            @if ($this->contratos->hasPages())
                <div class="card-footer d-flex justify-content-between align-items-center">
                    <div class="text-muted small">
                        {{ __('Mostrando') }} {{ $this->contratos->firstItem() }} - {{ $this->contratos->lastItem() }}
                        {{ __('de') }} {{ $this->contratos->total() }} {{ __('contratos') }}
                    </div>
                    {{ $this->contratos->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- ═══ MODAL CREAR / EDITAR ═══ --}}
    @if ($showModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="ti tabler-{{ $isEditing ? 'pencil' : 'file-plus' }} me-2"></i>
                            {{ $isEditing ? __('Editar Contrato') : __('Nuevo Contrato') }}
                        </h5>
                        <button type="button" wire:click="closeModal" class="btn-close"></button>
                    </div>

                    <form wire:submit="save">
                        <div class="modal-body">
                            <div class="row">
                                {{-- Empleado --}}
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="ct_empleado_id">
                                        {{ __('Empleado') }} <span class="text-danger">*</span>
                                    </label>
                                    <select id="ct_empleado_id" wire:model="empleado_id"
                                        class="form-select @error('empleado_id') is-invalid @enderror"
                                        @if($isEditing) disabled @endif>
                                        <option value="">{{ __('Seleccionar empleado...') }}</option>
                                        @foreach($this->empleados as $emp)
                                            <option value="{{ $emp->id }}">
                                                {{ $emp->nombre }}
                                                @if($emp->codigo_empleado) ({{ $emp->codigo_empleado }}) @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('empleado_id')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Número de contrato --}}
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="ct_numero">
                                        {{ __('Nro. Contrato') }} <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group input-group-merge">
                                        <span class="input-group-text"><i class="ti tabler-hash"></i></span>
                                        <input type="text" id="ct_numero" wire:model="numero_contrato"
                                            class="form-control @error('numero_contrato') is-invalid @enderror"
                                            placeholder="CTR-2026-0001">
                                    </div>
                                    @error('numero_contrato')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Tipo de contrato --}}
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="ct_tipo">
                                        {{ __('Tipo de contrato') }} <span class="text-danger">*</span>
                                    </label>
                                    <select id="ct_tipo" wire:model.live="tipo_contrato_id"
                                        class="form-select @error('tipo_contrato_id') is-invalid @enderror">
                                        <option value="">{{ __('Seleccionar tipo...') }}</option>
                                        @foreach ($this->tiposContrato as $tipo)
                                            <option value="{{ $tipo->id }}">{{ $tipo->nombre }}</option>
                                        @endforeach
                                    </select>
                                    @error('tipo_contrato_id')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Modalidad / Categoría --}}
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="ct_modalidad">{{ __('Modalidad') }}</label>
                                    <select id="ct_modalidad" wire:model="modalidad_id"
                                        class="form-select @error('modalidad_id') is-invalid @enderror"
                                        @if(!$tipo_contrato_id) disabled @endif>
                                        <option value="">{{ __('Sin modalidad específica') }}</option>
                                        @foreach ($this->modalidades as $mod)
                                            <option value="{{ $mod->id }}">{{ $mod->nombre }}</option>
                                        @endforeach
                                    </select>
                                    @error('modalidad_id')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    @if(!$tipo_contrato_id)
                                        <small class="text-muted">{{ __('Seleccione un tipo de contrato primero') }}</small>
                                    @endif
                                </div>

                                {{-- Fecha inicio --}}
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="ct_fecha_inicio">
                                        {{ __('Fecha de inicio') }} <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group input-group-merge">
                                        <span class="input-group-text"><i class="ti tabler-calendar"></i></span>
                                        <input type="date" id="ct_fecha_inicio" wire:model="fecha_inicio"
                                            class="form-control @error('fecha_inicio') is-invalid @enderror">
                                    </div>
                                    @error('fecha_inicio')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Fecha fin --}}
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="ct_fecha_fin">{{ __('Fecha de fin') }}</label>
                                    <div class="input-group input-group-merge">
                                        <span class="input-group-text"><i class="ti tabler-calendar-off"></i></span>
                                        <input type="date" id="ct_fecha_fin" wire:model="fecha_fin"
                                            class="form-control @error('fecha_fin') is-invalid @enderror">
                                    </div>
                                    @error('fecha_fin')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">{{ __('Dejar vacío para contratos indefinidos') }}</small>
                                </div>

                                {{-- Salario --}}
                                <div class="col-md-4 mb-3">
                                    <label class="form-label" for="ct_salario">{{ __('Salario base') }}</label>
                                    <div class="input-group input-group-merge">
                                        <span class="input-group-text">S/</span>
                                        <input type="number" id="ct_salario" wire:model="salario_base"
                                            class="form-control @error('salario_base') is-invalid @enderror"
                                            step="0.01" min="0" placeholder="0.00">
                                    </div>
                                    @error('salario_base')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Horas semanales --}}
                                <div class="col-md-4 mb-3">
                                    <label class="form-label" for="ct_horas">{{ __('Horas semanales') }}</label>
                                    <div class="input-group input-group-merge">
                                        <span class="input-group-text"><i class="ti tabler-clock"></i></span>
                                        <input type="number" id="ct_horas" wire:model="horas_semanales"
                                            class="form-control @error('horas_semanales') is-invalid @enderror"
                                            step="0.5" min="0" max="168" placeholder="48">
                                    </div>
                                    @error('horas_semanales')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Estado del contrato --}}
                                <div class="col-md-4 mb-3">
                                    <label class="form-label" for="ct_estado_contrato">{{ __('Estado') }}</label>
                                    <select id="ct_estado_contrato" wire:model="estado_contrato"
                                        class="form-select @error('estado_contrato') is-invalid @enderror">
                                        @foreach (\Modules\HR\Enums\EstadoContrato::options() as $opt)
                                            <option value="{{ $opt['value'] }}">{{ $opt['label'] }}</option>
                                        @endforeach
                                    </select>
                                    @error('estado_contrato')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Descripción horario --}}
                                <div class="col-12 mb-3">
                                    <label class="form-label" for="ct_horario">{{ __('Descripción de horario') }}</label>
                                    <input type="text" id="ct_horario" wire:model="descripcion_horario"
                                        class="form-control @error('descripcion_horario') is-invalid @enderror"
                                        placeholder="{{ __('Ej: Lunes a Viernes 8:00 - 17:00') }}">
                                    @error('descripcion_horario')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Notas --}}
                                <div class="col-12 mb-3">
                                    <label class="form-label" for="ct_notas">{{ __('Notas') }}</label>
                                    <textarea id="ct_notas" wire:model="notas" rows="2"
                                        class="form-control @error('notas') is-invalid @enderror"
                                        placeholder="{{ __('Observaciones adicionales...') }}"></textarea>
                                    @error('notas')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" wire:click="closeModal" class="btn btn-outline-secondary">
                                {{ __('Cancelar') }}
                            </button>
                            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="save">
                                    <i class="ti tabler-device-floppy me-1"></i>
                                    {{ $isEditing ? __('Actualizar') : __('Crear Contrato') }}
                                </span>
                                <span wire:loading wire:target="save">
                                    <span class="spinner-border spinner-border-sm me-1"></span>
                                    {{ __('Guardando...') }}
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- ═══ MODAL ELIMINAR ═══ --}}
    @if ($showDeleteModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content">
                    <div class="modal-body text-center py-4">
                        <div class="mb-3">
                            <i class="ti tabler-alert-triangle icon-48px text-warning"></i>
                        </div>
                        <h5>{{ __('¿Eliminar contrato?') }}</h5>
                        <p class="text-muted mb-4">
                            {{ __('El contrato será movido a la papelera y podrá ser restaurado posteriormente.') }}
                        </p>
                        <div class="d-flex justify-content-center gap-2">
                            <button wire:click="closeModal" class="btn btn-outline-secondary">
                                {{ __('Cancelar') }}
                            </button>
                            <button wire:click="delete" class="btn btn-danger" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="delete">
                                    <i class="ti tabler-trash me-1"></i>{{ __('Eliminar') }}
                                </span>
                                <span wire:loading wire:target="delete">
                                    <span class="spinner-border spinner-border-sm"></span>
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- ═══ MODAL TERMINAR CONTRATO ═══ --}}
    @if ($showTerminarModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="ti tabler-flag me-2"></i>{{ __('Terminar Contrato') }}
                        </h5>
                        <button type="button" wire:click="closeModal" class="btn-close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-warning d-flex align-items-center" role="alert">
                            <i class="ti tabler-alert-triangle me-2"></i>
                            <span>{{ __('Esta acción marcará el contrato como terminado. No se puede deshacer fácilmente.') }}</span>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="ct_fecha_terminacion">
                                {{ __('Fecha de terminación') }} <span class="text-danger">*</span>
                            </label>
                            <input type="date" id="ct_fecha_terminacion" wire:model="fecha_terminacion"
                                class="form-control @error('fecha_terminacion') is-invalid @enderror">
                            @error('fecha_terminacion')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="ct_motivo">
                                {{ __('Motivo de terminación') }} <span class="text-danger">*</span>
                            </label>
                            <select id="ct_motivo" wire:model="motivo_terminacion"
                                class="form-select @error('motivo_terminacion') is-invalid @enderror">
                                <option value="">{{ __('Seleccionar motivo...') }}</option>
                                <option value="Renuncia voluntaria">{{ __('Renuncia voluntaria') }}</option>
                                <option value="Mutuo acuerdo">{{ __('Mutuo acuerdo') }}</option>
                                <option value="Despido justificado">{{ __('Despido justificado') }}</option>
                                <option value="Despido arbitrario">{{ __('Despido arbitrario') }}</option>
                                <option value="Vencimiento de plazo">{{ __('Vencimiento de plazo') }}</option>
                                <option value="Jubilación">{{ __('Jubilación') }}</option>
                                <option value="Fallecimiento">{{ __('Fallecimiento') }}</option>
                                <option value="Incapacidad permanente">{{ __('Incapacidad permanente') }}</option>
                                <option value="Otro">{{ __('Otro') }}</option>
                            </select>
                            @error('motivo_terminacion')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" wire:click="closeModal" class="btn btn-outline-secondary">
                            {{ __('Cancelar') }}
                        </button>
                        <button wire:click="terminar" class="btn btn-danger" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="terminar">
                                <i class="ti tabler-flag me-1"></i>{{ __('Terminar Contrato') }}
                            </span>
                            <span wire:loading wire:target="terminar">
                                <span class="spinner-border spinner-border-sm me-1"></span>
                                {{ __('Procesando...') }}
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- ═══ MODAL RESTAURAR ═══ --}}
    @if ($showRestoreModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content">
                    <div class="modal-body text-center py-4">
                        <div class="mb-3">
                            <i class="ti tabler-refresh icon-48px text-success"></i>
                        </div>
                        <h5>{{ __('¿Restaurar contrato?') }}</h5>
                        <p class="text-muted mb-4">
                            {{ __('El contrato será restaurado y volverá a estar disponible.') }}
                        </p>
                        <div class="d-flex justify-content-center gap-2">
                            <button wire:click="closeModal" class="btn btn-outline-secondary">
                                {{ __('Cancelar') }}
                            </button>
                            <button wire:click="restore" class="btn btn-success" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="restore">
                                    <i class="ti tabler-refresh me-1"></i>{{ __('Restaurar') }}
                                </span>
                                <span wire:loading wire:target="restore">
                                    <span class="spinner-border spinner-border-sm"></span>
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- ═══ MODAL ELIMINAR PERMANENTE ═══ --}}
    @if ($showForceDeleteModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content">
                    <div class="modal-body text-center py-4">
                        <div class="mb-3">
                            <i class="ti tabler-trash-x icon-48px text-danger"></i>
                        </div>
                        <h5>{{ __('¿Eliminar permanentemente?') }}</h5>
                        <p class="text-muted mb-4">
                            {{ __('Esta acción no se puede deshacer. Todos los datos del contrato serán eliminados.') }}
                        </p>
                        <div class="d-flex justify-content-center gap-2">
                            <button wire:click="closeModal" class="btn btn-outline-secondary">
                                {{ __('Cancelar') }}
                            </button>
                            <button wire:click="forceDelete" class="btn btn-danger" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="forceDelete">
                                    <i class="ti tabler-trash-x me-1"></i>{{ __('Eliminar') }}
                                </span>
                                <span wire:loading wire:target="forceDelete">
                                    <span class="spinner-border spinner-border-sm"></span>
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- ═══ MODAL GENERAR DOCUMENTO ═══ --}}
    @if ($showGenerarDocModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="ti tabler-file-plus me-2"></i>{{ __('Generar documento') }}
                        </h5>
                        <button type="button" wire:click="closeModal" class="btn-close"></button>
                    </div>
                    <div class="modal-body">
                        @if ($this->plantillasDisponibles->isEmpty())
                            <div class="alert alert-warning d-flex align-items-center">
                                <i class="ti tabler-alert-triangle me-2 icon-24px"></i>
                                <div>
                                    <strong>{{ __('No hay plantillas disponibles') }}</strong>
                                    <p class="mb-0 mt-1">{{ __('No se encontraron plantillas de documento para el tipo de contrato seleccionado. Debe crear una plantilla antes de generar documentos.') }}</p>
                                </div>
                            </div>
                        @else
                            <p class="text-muted small mb-3">
                                {{ __('Seleccione la plantilla que desea utilizar para generar el documento del contrato.') }}
                            </p>
                            <div class="mb-3">
                                <label class="form-label" for="gen_plantilla">
                                    {{ __('Plantilla') }} <span class="text-danger">*</span>
                                </label>
                                <select id="gen_plantilla" wire:model="plantilla_seleccionada_id"
                                    class="form-select @error('plantilla_seleccionada_id') is-invalid @enderror">
                                    <option value="">{{ __('Seleccionar plantilla...') }}</option>
                                    @foreach ($this->plantillasDisponibles as $pl)
                                        <option value="{{ $pl->id }}">
                                            {{ $pl->nombre }}
                                            ({{ $pl->codigo }})
                                            @if ($pl->es_predeterminada) ★ @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('plantilla_seleccionada_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button wire:click="closeModal" class="btn btn-outline-secondary">
                            {{ __('Cancelar') }}
                        </button>
                        @if ($this->plantillasDisponibles->isNotEmpty())
                            <button wire:click="generarDocumento" class="btn btn-primary" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="generarDocumento">
                                    <i class="ti tabler-file-check me-1"></i>{{ __('Generar PDF') }}
                                </span>
                                <span wire:loading wire:target="generarDocumento">
                                    <span class="spinner-border spinner-border-sm me-1"></span>{{ __('Generando...') }}
                                </span>
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- JavaScript: abrir PDF en nueva pestaña --}}
    @script
    <script>
        $wire.on('openPdfInNewTab', ({ url }) => {
            window.open(url, '_blank');
        });
    </script>
    @endscript
</div>
