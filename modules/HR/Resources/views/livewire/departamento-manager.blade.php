<div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-12 col-md-5">
                <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="ti tabler-search"></i></span>
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        class="form-control"
                        placeholder="{{ __('Buscar por nombre, codigo o descripcion...') }}"
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

            <div class="col-6 col-md-3">
                <select wire:model.live="statusFilter" class="form-select">
                    <option value="">{{ __('Todos') }}</option>
                    <option value="active">{{ __('Activos') }}</option>
                    <option value="inactive">{{ __('Inactivos') }}</option>
                </select>
            </div>

            <div class="col-6 col-md-2">
                <select wire:model.live="perPage" class="form-select">
                    <option value="10">10 por pagina</option>
                    <option value="25">25 por pagina</option>
                    <option value="50">50 por pagina</option>
                </select>
            </div>

            <div class="col-12 col-md-2 d-flex gap-2">
                @if ($search || $statusFilter)
                    <button wire:click="clearFilters" class="btn btn-outline-secondary flex-fill" title="{{ __('Limpiar filtros') }}">
                        <i class="ti tabler-filter-off"></i>
                    </button>
                @endif
                <button
                    wire:click="toggleTrashedDepartamentos"
                    class="btn {{ $showTrashedDepartamentos ? 'btn-danger' : 'btn-outline-secondary' }}"
                    title="{{ $showTrashedDepartamentos ? __('Ver activos') : __('Ver eliminados') }}"
                >
                    <i class="ti {{ $showTrashedDepartamentos ? 'tabler-building' : 'tabler-trash' }}"></i>
                </button>
            </div>

            @if ($showTrashedDepartamentos)
                <div class="col-12">
                    <div class="alert alert-warning d-flex align-items-center mb-0" role="alert">
                        <i class="ti tabler-trash me-2"></i>
                        <span>{{ __('Mostrando departamentos eliminados. Puedes restaurarlos o eliminarlos permanentemente.') }}</span>
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
                                    {{ __('Departamento') }}
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
                            <th style="min-width: 150px;">{{ __('Tipo') }}</th>
                            <th style="min-width: 170px;">{{ __('Dep. padre') }}</th>
                            <th style="min-width: 170px;">{{ __('Jefe') }}</th>
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
                        @forelse($this->departamentos as $departamento)
                            <tr wire:key="departamento-{{ $departamento->id }}">
                                <td>
                                    <div class="d-flex justify-content-start align-items-center gap-1">
                                        <button
                                            class="btn btn-sm rounded-pill btn-icon btn-label-secondary waves-effect me-1"
                                            data-bs-toggle="tooltip"
                                            title="Nro de registro: {{ $departamento->id }}"
                                        >
                                            {{ $departamento->id }}
                                        </button>

                                        <button
                                            wire:click="edit({{ $departamento->id }})"
                                            class="btn btn-sm btn-icon btn-label-primary"
                                            data-bs-toggle="tooltip"
                                            title="{{ __('Editar') }}"
                                        >
                                            <i class="ti tabler-edit"></i>
                                        </button>

                                        <a
                                            href="{{ route('hr.departamentos.show', $departamento->id) }}"
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
                                                @if (!$showTrashedDepartamentos)
                                                    <li>
                                                        <a
                                                            class="dropdown-item d-flex p-2 justify-content-start btn btn-label-warning align-items-center"
                                                            wire:click="toggleEstado({{ $departamento->id }})"
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
                                                            wire:click="confirmDelete({{ $departamento->id }})"
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
                                                            wire:click="confirmRestore({{ $departamento->id }})"
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
                                                            wire:click="confirmForceDelete({{ $departamento->id }})"
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
                                    <span class="badge {{ (int) $departamento->estado === \Modules\HR\Models\Departamento::ESTADO_ACTIVO ? 'bg-label-success' : 'bg-label-secondary' }}">
                                        {{ $departamento->estado_label }}
                                    </span>
                                </td>
                                <td>
                                    <div>
                                        <h6 class="mb-0">{{ $departamento->name }}</h6>
                                        @if($departamento->descripcion)
                                            <small class="text-muted">{{ \Illuminate\Support\Str::limit($departamento->descripcion, 50) }}</small>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @if($departamento->codigo)
                                        <span class="badge bg-label-info">{{ $departamento->codigo }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    {{ $departamento->tipoDepartamento?->name ?? '-' }}
                                </td>
                                <td>
                                    {{ $departamento->padre?->name ?? '-' }}
                                </td>
                                <td>
                                    {{ $departamento->jefe?->nombre ?? '-' }}
                                </td>
                                <td>
                                    @if($departamento->created_at)
                                        <span title="{{ $departamento->created_at->format('d/m/Y H:i') }}">
                                            {{ $departamento->created_at->diffForHumans() }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="ti tabler-building-skyscraper icon-48px text-muted mb-3"></i>
                                        <h6 class="mb-1">{{ __('No se encontraron departamentos') }}</h6>
                                        <p class="text-muted mb-0">
                                            @if ($search || $statusFilter)
                                                {{ __('Intenta ajustar los filtros de busqueda') }}
                                            @else
                                                {{ __('Aun no hay departamentos registrados') }}
                                            @endif
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($this->departamentos->hasPages())
                <div class="card-footer d-flex justify-content-between align-items-center">
                    <div class="text-muted small">
                        {{ __('Mostrando') }} {{ $this->departamentos->firstItem() }} - {{ $this->departamentos->lastItem() }}
                        {{ __('de') }} {{ $this->departamentos->total() }} {{ __('departamentos') }}
                    </div>
                    {{ $this->departamentos->links() }}
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
                            {{ $isEditing ? __('Editar Departamento') : __('Nuevo Departamento') }}
                        </h5>
                        <button type="button" wire:click="closeModal" class="btn-close"></button>
                    </div>

                    <form wire:submit="save">
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-md-7">
                                    <label class="form-label" for="departamento_name">
                                        {{ __('Nombre') }} <span class="text-danger">*</span>
                                    </label>
                                    <input
                                        type="text"
                                        id="departamento_name"
                                        wire:model="name"
                                        class="form-control @error('name') is-invalid @enderror"
                                        placeholder="{{ __('Nombre del departamento') }}"
                                        autofocus
                                    >
                                    @error('name')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-5">
                                    <label class="form-label" for="departamento_codigo">{{ __('Codigo') }}</label>
                                    <input
                                        type="text"
                                        id="departamento_codigo"
                                        wire:model="codigo"
                                        class="form-control @error('codigo') is-invalid @enderror"
                                        placeholder="{{ __('Ej: DEP-001') }}"
                                    >
                                    @error('codigo')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label" for="departamento_tipo">{{ __('Tipo') }}</label>
                                    <select
                                        id="departamento_tipo"
                                        wire:model="tipo_departamento_id"
                                        class="form-select @error('tipo_departamento_id') is-invalid @enderror"
                                    >
                                        <option value="">{{ __('Seleccionar...') }}</option>
                                        @foreach ($this->tiposDepartamento as $tipo)
                                            <option value="{{ $tipo->id }}">{{ $tipo->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('tipo_departamento_id')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label" for="departamento_padre">{{ __('Departamento padre') }}</label>
                                    <select
                                        id="departamento_padre"
                                        wire:model="padre_id"
                                        class="form-select @error('padre_id') is-invalid @enderror"
                                    >
                                        <option value="">{{ __('Sin padre') }}</option>
                                        @foreach ($this->padresDisponibles as $padre)
                                            <option value="{{ $padre->id }}">{{ $padre->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('padre_id')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label" for="departamento_jefe">{{ __('Jefe') }}</label>
                                    <select
                                        id="departamento_jefe"
                                        wire:model="jefe_id"
                                        class="form-select @error('jefe_id') is-invalid @enderror"
                                    >
                                        <option value="">{{ __('Sin jefe') }}</option>
                                        @foreach ($this->jefesDisponibles as $jefe)
                                            <option value="{{ $jefe->id }}">{{ $jefe->nombre }}</option>
                                        @endforeach
                                    </select>
                                    @error('jefe_id')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <label class="form-label" for="departamento_descripcion">{{ __('Descripcion') }}</label>
                                    <textarea
                                        id="departamento_descripcion"
                                        wire:model="descripcion"
                                        class="form-control @error('descripcion') is-invalid @enderror"
                                        rows="3"
                                        placeholder="{{ __('Descripcion del departamento (opcional)') }}"
                                    ></textarea>
                                    @error('descripcion')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <label class="form-label" for="departamento_estado">{{ __('Estado') }}</label>
                                    <select
                                        id="departamento_estado"
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
                                    {{ $isEditing ? __('Actualizar') : __('Crear Departamento') }}
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
                        <h5>{{ __('Eliminar departamento?') }}</h5>
                        <p class="text-muted mb-4">
                            {{ __('El departamento se movera a la papelera y podra restaurarse.') }}
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
                        <h5>{{ __('Restaurar departamento?') }}</h5>
                        <p class="text-muted mb-4">
                            {{ __('El departamento volvera a estar disponible.') }}
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
