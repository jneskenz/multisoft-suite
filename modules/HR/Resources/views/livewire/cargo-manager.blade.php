<div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-12 col-md-4">
                <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="ti tabler-search"></i></span>
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        class="form-control"
                        placeholder="{{ __('Buscar por cargo, codigo o nivel...') }}"
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

            <div class="col-6 col-md-2">
                <select wire:model.live="statusFilter" class="form-select">
                    <option value="">{{ __('Todos') }}</option>
                    <option value="active">{{ __('Activos') }}</option>
                    <option value="inactive">{{ __('Inactivos') }}</option>
                </select>
            </div>

            <div class="col-6 col-md-3">
                <select wire:model.live="departamentoFilter" class="form-select">
                    <option value="">{{ __('Todos los departamentos') }}</option>
                    @foreach ($this->departamentos as $departamento)
                        <option value="{{ $departamento->id }}">{{ $departamento->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-6 col-md-2">
                <select wire:model.live="perPage" class="form-select">
                    <option value="10">10 por pagina</option>
                    <option value="25">25 por pagina</option>
                    <option value="50">50 por pagina</option>
                </select>
            </div>

            <div class="col-6 col-md-1 d-flex gap-1">
                @if ($search || $statusFilter || $departamentoFilter)
                    <button wire:click="clearFilters" class="btn btn-outline-secondary w-100" title="{{ __('Limpiar filtros') }}">
                        <i class="ti tabler-filter-off"></i>
                    </button>
                @endif
                <button
                    wire:click="toggleTrashedCargos"
                    class="btn {{ $showTrashedCargos ? 'btn-danger' : 'btn-outline-secondary' }}"
                    title="{{ $showTrashedCargos ? __('Ver activos') : __('Ver eliminados') }}"
                >
                    <i class="ti {{ $showTrashedCargos ? 'tabler-briefcase' : 'tabler-trash' }}"></i>
                </button>
            </div>

            @if ($showTrashedCargos)
                <div class="col-12">
                    <div class="alert alert-warning d-flex align-items-center mb-0" role="alert">
                        <i class="ti tabler-trash me-2"></i>
                        <span>{{ __('Mostrando cargos eliminados. Puedes restaurarlos o eliminarlos permanentemente.') }}</span>
                    </div>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-hover table-sm align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width: 130px;">{{ __('Acciones') }}</th>
                            <th style="width: 120px;">{{ __('Estado') }}</th>
                            <th wire:click="sortBy('name')" class="cursor-pointer" style="min-width: 220px;">
                                <div class="d-flex align-items-center">
                                    {{ __('Cargo') }}
                                    @if ($sortField === 'name')
                                        <i class="ti tabler-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ms-1"></i>
                                    @endif
                                </div>
                            </th>
                            <th wire:click="sortBy('codigo')" class="cursor-pointer" style="min-width: 120px;">
                                <div class="d-flex align-items-center">
                                    {{ __('Codigo') }}
                                    @if ($sortField === 'codigo')
                                        <i class="ti tabler-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ms-1"></i>
                                    @endif
                                </div>
                            </th>
                            <th style="min-width: 180px;">{{ __('Departamento') }}</th>
                            <th wire:click="sortBy('nivel')" class="cursor-pointer" style="min-width: 130px;">
                                <div class="d-flex align-items-center">
                                    {{ __('Nivel') }}
                                    @if ($sortField === 'nivel')
                                        <i class="ti tabler-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ms-1"></i>
                                    @endif
                                </div>
                            </th>
                            <th wire:click="sortBy('created_at')" class="cursor-pointer" style="width: 160px;">
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
                        @forelse($this->cargos as $cargo)
                            <tr wire:key="cargo-{{ $cargo->id }}">
                                <td>
                                    <div class="d-flex justify-content-start align-items-center gap-1">
                                        <button
                                            class="btn btn-sm rounded-pill btn-icon btn-label-secondary waves-effect me-1"
                                            data-bs-toggle="tooltip"
                                            title="Nro de registro: {{ $cargo->id }}"
                                        >
                                            {{ $cargo->id }}
                                        </button>

                                        <button
                                            wire:click="edit({{ $cargo->id }})"
                                            class="btn btn-sm btn-icon btn-label-primary"
                                            data-bs-toggle="tooltip"
                                            title="{{ __('Editar cargo') }}"
                                        >
                                            <i class="ti tabler-edit"></i>
                                        </button>

                                        <a
                                            {{-- href="{{ route('hr.cargos.show', $cargo->id) }}" --}}
                                            href="#"
                                            class="btn btn-sm btn-icon btn-label-info"
                                            data-bs-toggle="tooltip"
                                            title="{{ __('Ver detalle') }}"
                                        >
                                            <i class="ti tabler-eye"></i>
                                        </a>

                                        <div class="dropdown">
                                            <button
                                                class="btn btn-sm btn-icon border btn-text-secondary rounded dropdown-toggle hide-arrow"
                                                data-bs-toggle="dropdown"
                                            >
                                                <i class="ti tabler-dots-vertical"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                @if (!$showTrashedCargos)
                                                    <li>
                                                        <a
                                                            class="dropdown-item d-flex p-2 justify-content-start btn btn-label-warning align-items-center"
                                                            wire:click="toggleEstado({{ $cargo->id }})"
                                                        >
                                                            <button class="btn btn-sm btn-icon btn-label-warning me-2">
                                                                <i class="ti tabler-refresh"></i>
                                                            </button>
                                                            <span class="lh-1">{{ __('Cambiar estado') }}</span>
                                                        </a>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <a
                                                            class="dropdown-item d-flex p-2 justify-content-start btn btn-label-danger align-items-center"
                                                            wire:click="confirmDelete({{ $cargo->id }})"
                                                        >
                                                            <button class="btn btn-sm btn-icon btn-label-danger me-2">
                                                                <i class="ti tabler-trash"></i>
                                                            </button>
                                                            <span class="lh-1">{{ __('Eliminar') }}</span>
                                                        </a>
                                                    </li>
                                                @else
                                                    <li>
                                                        <a
                                                            class="dropdown-item d-flex p-2 justify-content-start btn btn-label-success align-items-center"
                                                            wire:click="confirmRestore({{ $cargo->id }})"
                                                        >
                                                            <button class="btn btn-sm btn-icon btn-label-success me-2">
                                                                <i class="ti tabler-refresh"></i>
                                                            </button>
                                                            <span class="lh-1">{{ __('Restaurar') }}</span>
                                                        </a>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <a
                                                            class="dropdown-item d-flex p-2 justify-content-start btn btn-label-dark align-items-center"
                                                            wire:click="confirmForceDelete({{ $cargo->id }})"
                                                        >
                                                            <button class="btn btn-sm btn-icon btn-label-dark me-2">
                                                                <i class="ti tabler-trash-x"></i>
                                                            </button>
                                                            <span class="lh-1">{{ __('Eliminar permanente') }}</span>
                                                        </a>
                                                    </li>
                                                @endif
                                            </ul>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge {{ (int) $cargo->estado === \Modules\HR\Models\Cargo::ESTADO_ACTIVO ? 'bg-label-success' : 'bg-label-secondary' }}">
                                        {{ $cargo->estado_label }}
                                    </span>
                                </td>
                                <td>
                                    <div>
                                        <h6 class="mb-0">{{ $cargo->name }}</h6>
                                        @if($cargo->descripcion)
                                            <small class="text-muted">{{ \Illuminate\Support\Str::limit($cargo->descripcion, 50) }}</small>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @if($cargo->codigo)
                                        <span class="badge bg-label-info">{{ $cargo->codigo }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    {{ $cargo->departamento?->name ?? '-' }}
                                </td>
                                <td>
                                    {{ $cargo->nivel ?? '-' }}
                                </td>
                                <td>
                                    @if($cargo->created_at)
                                        <span title="{{ $cargo->created_at->format('d/m/Y H:i') }}">
                                            {{ $cargo->created_at->diffForHumans() }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="ti tabler-briefcase-off icon-48px text-muted mb-3"></i>
                                        <h6 class="mb-1">{{ __('No se encontraron cargos') }}</h6>
                                        <p class="text-muted mb-0">
                                            @if ($search || $statusFilter || $departamentoFilter)
                                                {{ __('Intenta ajustar los filtros de busqueda') }}
                                            @else
                                                {{ __('Aun no hay cargos registrados') }}
                                            @endif
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($this->cargos->hasPages())
                <div class="card-footer d-flex justify-content-between align-items-center">
                    <div class="text-muted small">
                        {{ __('Mostrando') }} {{ $this->cargos->firstItem() }} - {{ $this->cargos->lastItem() }}
                        {{ __('de') }} {{ $this->cargos->total() }} {{ __('cargos') }}
                    </div>
                    {{ $this->cargos->links() }}
                </div>
            @endif
        </div>
    </div>

    @if ($showModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="ti tabler-{{ $isEditing ? 'pencil' : 'plus' }} me-2"></i>
                            {{ $isEditing ? __('Editar Cargo') : __('Nuevo Cargo') }}
                        </h5>
                        <button type="button" wire:click="closeModal" class="btn-close"></button>
                    </div>

                    <form wire:submit="save">
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label" for="cargo_departamento">
                                        {{ __('Departamento') }} <span class="text-danger">*</span>
                                    </label>
                                    <select
                                        id="cargo_departamento"
                                        wire:model="departamento_id"
                                        class="form-select @error('departamento_id') is-invalid @enderror"
                                    >
                                        <option value="">{{ __('Seleccionar...') }}</option>
                                        @foreach ($this->departamentos as $departamento)
                                            <option value="{{ $departamento->id }}">{{ $departamento->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('departamento_id')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label" for="cargo_name">
                                        {{ __('Nombre del cargo') }} <span class="text-danger">*</span>
                                    </label>
                                    <input
                                        type="text"
                                        id="cargo_name"
                                        wire:model="name"
                                        class="form-control @error('name') is-invalid @enderror"
                                        placeholder="{{ __('Ej: Jefe de RRHH') }}"
                                        autofocus
                                    >
                                    @error('name')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label" for="cargo_codigo">{{ __('Codigo') }}</label>
                                    <input
                                        type="text"
                                        id="cargo_codigo"
                                        wire:model="codigo"
                                        class="form-control @error('codigo') is-invalid @enderror"
                                        placeholder="{{ __('Ej: RRH-JEF') }}"
                                    >
                                    @error('codigo')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label" for="cargo_nivel">{{ __('Nivel') }}</label>
                                    <select
                                        id="cargo_nivel"
                                        wire:model="nivel"
                                        class="form-select @error('nivel') is-invalid @enderror"
                                    >
                                        <option value="">{{ __('Seleccionar...') }}</option>
                                        <option value="Gerencial">{{ __('Gerencial') }}</option>
                                        <option value="Jefatura">{{ __('Jefatura') }}</option>
                                        <option value="Analista">{{ __('Analista') }}</option>
                                        <option value="Asistente">{{ __('Asistente') }}</option>
                                        <option value="Operativo">{{ __('Operativo') }}</option>
                                    </select>
                                    @error('nivel')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <label class="form-label" for="cargo_descripcion">{{ __('Descripcion') }}</label>
                                    <textarea
                                        id="cargo_descripcion"
                                        wire:model="descripcion"
                                        class="form-control @error('descripcion') is-invalid @enderror"
                                        rows="3"
                                        placeholder="{{ __('Descripcion del cargo (opcional)') }}"
                                    ></textarea>
                                    @error('descripcion')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <label class="form-label" for="cargo_estado">{{ __('Estado') }}</label>
                                    <select
                                        id="cargo_estado"
                                        wire:model="estado"
                                        class="form-select @error('estado') is-invalid @enderror"
                                    >
                                        <option value="1">{{ __('Activo') }}</option>
                                        <option value="0">{{ __('Inactivo') }}</option>
                                    </select>
                                    @error('estado')
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
                                    {{ $isEditing ? __('Actualizar') : __('Crear Cargo') }}
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

    @if ($showDeleteModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content">
                    <div class="modal-body text-center py-4">
                        <div class="mb-3">
                            <i class="ti tabler-alert-triangle icon-48px text-warning"></i>
                        </div>
                        <h5>{{ __('Eliminar cargo?') }}</h5>
                        <p class="text-muted mb-4">
                            {{ __('El cargo se movera a la papelera y podra restaurarse.') }}
                        </p>
                        <div class="d-flex justify-content-center gap-2">
                            <button wire:click="closeModal" class="btn btn-outline-secondary">
                                {{ __('Cancelar') }}
                            </button>
                            <button wire:click="delete" class="btn btn-danger" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="delete">
                                    <i class="ti tabler-trash me-1"></i>
                                    {{ __('Eliminar') }}
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

    @if ($showRestoreModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content">
                    <div class="modal-body text-center py-4">
                        <div class="mb-3">
                            <i class="ti tabler-refresh icon-48px text-success"></i>
                        </div>
                        <h5>{{ __('Restaurar cargo?') }}</h5>
                        <p class="text-muted mb-4">
                            {{ __('El cargo volvera a estar disponible.') }}
                        </p>
                        <div class="d-flex justify-content-center gap-2">
                            <button wire:click="closeModal" class="btn btn-outline-secondary">
                                {{ __('Cancelar') }}
                            </button>
                            <button wire:click="restore" class="btn btn-success" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="restore">
                                    <i class="ti tabler-refresh me-1"></i>
                                    {{ __('Restaurar') }}
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

    @if ($showForceDeleteModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content">
                    <div class="modal-body text-center py-4">
                        <div class="mb-3">
                            <i class="ti tabler-trash-x icon-48px text-danger"></i>
                        </div>
                        <h5>{{ __('Eliminar permanentemente?') }}</h5>
                        <p class="text-muted mb-4">
                            {{ __('Esta accion no se puede deshacer.') }}
                        </p>
                        <div class="d-flex justify-content-center gap-2">
                            <button wire:click="closeModal" class="btn btn-outline-secondary">
                                {{ __('Cancelar') }}
                            </button>
                            <button wire:click="forceDelete" class="btn btn-danger" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="forceDelete">
                                    <i class="ti tabler-trash-x me-1"></i>
                                    {{ __('Eliminar') }}
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
</div>
