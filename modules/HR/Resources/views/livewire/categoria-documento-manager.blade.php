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
                        placeholder="{{ __('Buscar por nombre, código...') }}"
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
                <select wire:model.live="tipoFilter" class="form-select">
                    <option value="">{{ __('Todos los tipos') }}</option>
                    @foreach ($this->tiposDocumento as $tipo)
                        <option value="{{ $tipo->id }}">{{ $tipo->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-6 col-md-2">
                <select wire:model.live="statusFilter" class="form-select">
                    <option value="">{{ __('Todos') }}</option>
                    <option value="1">{{ __('Activos') }}</option>
                    <option value="0">{{ __('Inactivos') }}</option>
                </select>
            </div>

            <div class="col-6 col-md-2">
                <select wire:model.live="perPage" class="form-select">
                    <option value="10">10 por página</option>
                    <option value="25">25 por página</option>
                    <option value="50">50 por página</option>
                </select>
            </div>

            <div class="col-6 col-md-2 d-flex gap-1">
                @if ($search || $tipoFilter || $statusFilter)
                    <button wire:click="clearFilters" class="btn btn-outline-secondary w-100" title="{{ __('Limpiar filtros') }}">
                        <i class="ti tabler-filter-off"></i>
                    </button>
                @endif
                <button
                    wire:click="toggleTrashed"
                    class="btn {{ $showTrashed ? 'btn-danger' : 'btn-outline-secondary' }}"
                    title="{{ $showTrashed ? __('Ver activos') : __('Ver eliminados') }}"
                >
                    <i class="ti {{ $showTrashed ? 'tabler-file-text' : 'tabler-trash' }}"></i>
                </button>
            </div>

            @if ($showTrashed)
                <div class="col-12">
                    <div class="alert alert-warning d-flex align-items-center mb-0" role="alert">
                        <i class="ti tabler-trash me-2"></i>
                        <span>{{ __('Mostrando categorías eliminadas. Puedes restaurarlas o eliminarlas permanentemente.') }}</span>
                    </div>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-hover table-sm align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width: 130px;">{{ __('Acciones') }}</th>
                            <th style="width: 100px;">{{ __('Estado') }}</th>
                            <th wire:click="sortBy('nombre')" class="cursor-pointer" style="min-width: 200px;">
                                <div class="d-flex align-items-center">
                                    {{ __('Categoría') }}
                                    @if ($sortField === 'nombre')
                                        <i class="ti tabler-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ms-1"></i>
                                    @endif
                                </div>
                            </th>
                            <th wire:click="sortBy('codigo')" class="cursor-pointer" style="min-width: 140px;">
                                <div class="d-flex align-items-center">
                                    {{ __('Código') }}
                                    @if ($sortField === 'codigo')
                                        <i class="ti tabler-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ms-1"></i>
                                    @endif
                                </div>
                            </th>
                            <th style="min-width: 150px;">{{ __('Tipo Doc.') }}</th>
                            <th style="min-width: 130px;">{{ __('Nivel Aprob.') }}</th>
                            <th wire:click="sortBy('orden')" class="cursor-pointer" style="width: 90px;">
                                <div class="d-flex align-items-center">
                                    {{ __('Orden') }}
                                    @if ($sortField === 'orden')
                                        <i class="ti tabler-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ms-1"></i>
                                    @endif
                                </div>
                            </th>
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
                        @forelse($this->categorias as $categoria)
                            <tr wire:key="categoria-{{ $categoria->id }}">
                                <td>
                                    <div class="d-flex justify-content-start align-items-center gap-1">
                                        <button
                                            class="btn btn-sm rounded-pill btn-icon btn-label-secondary waves-effect me-1"
                                            data-bs-toggle="tooltip"
                                            title="Nro: {{ $categoria->id }}"
                                        >
                                            {{ $categoria->id }}
                                        </button>

                                        <button
                                            wire:click="edit({{ $categoria->id }})"
                                            class="btn btn-sm btn-icon btn-label-primary"
                                            data-bs-toggle="tooltip"
                                            title="{{ __('Editar') }}"
                                        >
                                            <i class="ti tabler-edit"></i>
                                        </button>

                                        <div class="dropdown">
                                            <button
                                                class="btn btn-sm btn-icon border btn-text-secondary rounded dropdown-toggle hide-arrow"
                                                data-bs-toggle="dropdown"
                                            >
                                                <i class="ti tabler-dots-vertical"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                @if (!$showTrashed)
                                                    <li>
                                                        <a class="dropdown-item d-flex p-2 justify-content-start btn btn-label-warning align-items-center"
                                                            wire:click="toggleEstado({{ $categoria->id }})">
                                                            <button class="btn btn-sm btn-icon btn-label-warning me-2"><i class="ti tabler-refresh"></i></button>
                                                            <span class="lh-1">{{ __('Cambiar estado') }}</span>
                                                        </a>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <a class="dropdown-item d-flex p-2 justify-content-start btn btn-label-danger align-items-center"
                                                            wire:click="confirmDelete({{ $categoria->id }})">
                                                            <button class="btn btn-sm btn-icon btn-label-danger me-2"><i class="ti tabler-trash"></i></button>
                                                            <span class="lh-1">{{ __('Eliminar') }}</span>
                                                        </a>
                                                    </li>
                                                @else
                                                    <li>
                                                        <a class="dropdown-item d-flex p-2 justify-content-start btn btn-label-success align-items-center"
                                                            wire:click="confirmRestore({{ $categoria->id }})">
                                                            <button class="btn btn-sm btn-icon btn-label-success me-2"><i class="ti tabler-refresh"></i></button>
                                                            <span class="lh-1">{{ __('Restaurar') }}</span>
                                                        </a>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <a class="dropdown-item d-flex p-2 justify-content-start btn btn-label-dark align-items-center"
                                                            wire:click="confirmForceDelete({{ $categoria->id }})">
                                                            <button class="btn btn-sm btn-icon btn-label-dark me-2"><i class="ti tabler-trash-x"></i></button>
                                                            <span class="lh-1">{{ __('Eliminar permanente') }}</span>
                                                        </a>
                                                    </li>
                                                @endif
                                            </ul>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @php
                                        $estadoClass = match($categoria->estado) {
                                            '1' => 'bg-label-success',
                                            '0' => 'bg-label-secondary',
                                            default => 'bg-label-secondary',
                                        };
                                    @endphp
                                    <span class="badge {{ $estadoClass }}">
                                        {{ $categoria->estado_label }}
                                    </span>
                                </td>
                                <td>
                                    <div>
                                        <h6 class="mb-0">{{ $categoria->nombre }}</h6>
                                        @if($categoria->descripcion)
                                            <small class="text-muted">{{ \Illuminate\Support\Str::limit($categoria->descripcion, 50) }}</small>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-label-info">{{ $categoria->codigo }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-label-primary">
                                        {{ $categoria->tipoDocumento?->nombre ?? '-' }}
                                    </span>
                                </td>
                                <td>
                                    @if($categoria->nivel_aprobacion)
                                        <span class="badge bg-label-warning">{{ ucfirst(str_replace('_', ' ', $categoria->nivel_aprobacion)) }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>{{ $categoria->orden }}</td>
                                <td>
                                    @if($categoria->created_at)
                                        <span title="{{ $categoria->created_at->format('d/m/Y H:i') }}">
                                            {{ $categoria->created_at->diffForHumans() }}
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
                                        <i class="ti tabler-category-off icon-48px text-muted mb-3"></i>
                                        <h6 class="mb-1">{{ __('No se encontraron categorías') }}</h6>
                                        <p class="text-muted mb-0">
                                            @if ($search || $tipoFilter || $statusFilter)
                                                {{ __('Intenta ajustar los filtros de búsqueda') }}
                                            @else
                                                {{ __('Aún no hay categorías registradas') }}
                                            @endif
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($this->categorias->hasPages())
                <div class="card-footer d-flex justify-content-between align-items-center">
                    <div class="text-muted small">
                        {{ __('Mostrando') }} {{ $this->categorias->firstItem() }} - {{ $this->categorias->lastItem() }}
                        {{ __('de') }} {{ $this->categorias->total() }} {{ __('categorías') }}
                    </div>
                    {{ $this->categorias->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- Modal Crear/Editar --}}
    @if ($showModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="ti tabler-{{ $isEditing ? 'pencil' : 'plus' }} me-2"></i>
                            {{ $isEditing ? __('Editar Categoría') : __('Nueva Categoría') }}
                        </h5>
                        <button type="button" wire:click="closeModal" class="btn-close"></button>
                    </div>

                    <form wire:submit="save">
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('Tipo de Documento') }} <span class="text-danger">*</span></label>
                                    <select wire:model="tipo_documento_id" class="form-select @error('tipo_documento_id') is-invalid @enderror">
                                        <option value="">{{ __('Seleccionar...') }}</option>
                                        @foreach ($this->tiposDocumento as $tipo)
                                            <option value="{{ $tipo->id }}">{{ $tipo->codigo }} - {{ $tipo->nombre }}</option>
                                        @endforeach
                                    </select>
                                    @error('tipo_documento_id')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">{{ __('Código') }} <span class="text-danger">*</span></label>
                                    <input type="text" wire:model="codigo" class="form-control @error('codigo') is-invalid @enderror" placeholder="Ej: TEMP-NEC-MER">
                                    @error('codigo')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">{{ __('Orden') }}</label>
                                    <input type="number" wire:model="orden" class="form-control" min="0">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">{{ __('Nombre') }} <span class="text-danger">*</span></label>
                                    <input type="text" wire:model="nombre" class="form-control @error('nombre') is-invalid @enderror" placeholder="Nombre de la categoría">
                                    @error('nombre')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">{{ __('Estado') }}</label>
                                    <select wire:model="estado" class="form-select">
                                        <option value="1">Activo</option>
                                        <option value="0">Inactivo</option>
                                    </select>
                                </div>

                                <div class="col-12">
                                    <label class="form-label">{{ __('Descripción') }}</label>
                                    <textarea wire:model="descripcion" class="form-control" rows="2" placeholder="Descripción opcional"></textarea>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">{{ __('Nivel de Aprobación') }}</label>
                                    <select wire:model="nivel_aprobacion" class="form-select">
                                        <option value="">{{ __('Sin nivel') }}</option>
                                        <option value="jefe_directo">Jefe Directo</option>
                                        <option value="rrhh">RRHH</option>
                                        <option value="gerencia">Gerencia</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">{{ __('Artículo de Ley') }}</label>
                                    <input type="text" wire:model="articulo_ley" class="form-control" placeholder="Ej: Art. 57 D.S. 003-97-TR">
                                </div>

                                <div class="col-md-6 d-flex align-items-end">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" wire:model="requiere_justificacion" id="requiere_justificacion">
                                        <label class="form-check-label" for="requiere_justificacion">{{ __('Requiere Justificación') }}</label>
                                    </div>
                                </div>

                                <div class="col-md-6 d-flex align-items-end">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" wire:model="requiere_aprobacion" id="requiere_aprobacion">
                                        <label class="form-check-label" for="requiere_aprobacion">{{ __('Requiere Aprobación') }}</label>
                                    </div>
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
                                    {{ $isEditing ? __('Actualizar') : __('Crear Categoría') }}
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

    {{-- Modal Eliminar --}}
    @if ($showDeleteModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content">
                    <div class="modal-body text-center py-4">
                        <div class="mb-3"><i class="ti tabler-alert-triangle icon-48px text-warning"></i></div>
                        <h5>{{ __('¿Eliminar categoría?') }}</h5>
                        <p class="text-muted mb-4">{{ __('La categoría se moverá a la papelera.') }}</p>
                        <div class="d-flex justify-content-center gap-2">
                            <button wire:click="closeModal" class="btn btn-outline-secondary">{{ __('Cancelar') }}</button>
                            <button wire:click="delete" class="btn btn-danger" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="delete"><i class="ti tabler-trash me-1"></i>{{ __('Eliminar') }}</span>
                                <span wire:loading wire:target="delete"><span class="spinner-border spinner-border-sm"></span></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal Restaurar --}}
    @if ($showRestoreModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content">
                    <div class="modal-body text-center py-4">
                        <div class="mb-3"><i class="ti tabler-refresh icon-48px text-success"></i></div>
                        <h5>{{ __('¿Restaurar categoría?') }}</h5>
                        <p class="text-muted mb-4">{{ __('La categoría volverá a estar disponible.') }}</p>
                        <div class="d-flex justify-content-center gap-2">
                            <button wire:click="closeModal" class="btn btn-outline-secondary">{{ __('Cancelar') }}</button>
                            <button wire:click="restore" class="btn btn-success" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="restore"><i class="ti tabler-refresh me-1"></i>{{ __('Restaurar') }}</span>
                                <span wire:loading wire:target="restore"><span class="spinner-border spinner-border-sm"></span></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal Eliminar Permanente --}}
    @if ($showForceDeleteModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content">
                    <div class="modal-body text-center py-4">
                        <div class="mb-3"><i class="ti tabler-trash-x icon-48px text-danger"></i></div>
                        <h5>{{ __('¿Eliminar permanentemente?') }}</h5>
                        <p class="text-muted mb-4">{{ __('Esta acción no se puede deshacer.') }}</p>
                        <div class="d-flex justify-content-center gap-2">
                            <button wire:click="closeModal" class="btn btn-outline-secondary">{{ __('Cancelar') }}</button>
                            <button wire:click="forceDelete" class="btn btn-danger" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="forceDelete"><i class="ti tabler-trash-x me-1"></i>{{ __('Eliminar') }}</span>
                                <span wire:loading wire:target="forceDelete"><span class="spinner-border spinner-border-sm"></span></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
