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
                <select wire:model.live="categoriaFilter" class="form-select">
                    <option value="">{{ __('Todas categorías') }}</option>
                    @foreach (\Modules\HR\Models\TipoDocumento::CATEGORIAS as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-6 col-md-2">
                <select wire:model.live="statusFilter" class="form-select">
                    <option value="">{{ __('Todos') }}</option>
                    <option value="active">{{ __('Activos') }}</option>
                    <option value="inactive">{{ __('Inactivos') }}</option>
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
                @if ($search || $categoriaFilter || $statusFilter)
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
                        <span>{{ __('Mostrando tipos eliminados. Puedes restaurarlos o eliminarlos permanentemente.') }}</span>
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
                                    {{ __('Nombre') }}
                                    @if ($sortField === 'nombre')
                                        <i class="ti tabler-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ms-1"></i>
                                    @endif
                                </div>
                            </th>
                            <th wire:click="sortBy('codigo')" class="cursor-pointer" style="min-width: 130px;">
                                <div class="d-flex align-items-center">
                                    {{ __('Código') }}
                                    @if ($sortField === 'codigo')
                                        <i class="ti tabler-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ms-1"></i>
                                    @endif
                                </div>
                            </th>
                            <th wire:click="sortBy('categoria')" class="cursor-pointer" style="min-width: 140px;">
                                <div class="d-flex align-items-center">
                                    {{ __('Categoría') }}
                                    @if ($sortField === 'categoria')
                                        <i class="ti tabler-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ms-1"></i>
                                    @endif
                                </div>
                            </th>
                            <th style="width: 100px;">{{ __('Firma Emp.') }}</th>
                            <th style="width: 100px;">{{ __('Firma Empr.') }}</th>
                            <th style="width: 130px;">{{ __('Numeración') }}</th>
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
                        @forelse($this->tipos as $tipo)
                            <tr wire:key="tipo-{{ $tipo->id }}">
                                <td>
                                    <div class="d-flex justify-content-start align-items-center gap-1">
                                        <button
                                            class="btn btn-sm rounded-pill btn-icon btn-label-secondary waves-effect me-1"
                                            data-bs-toggle="tooltip"
                                            title="Nro: {{ $tipo->id }}"
                                        >
                                            {{ $tipo->id }}
                                        </button>

                                        <button
                                            wire:click="edit({{ $tipo->id }})"
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
                                                            wire:click="toggleEstado({{ $tipo->id }})">
                                                            <button class="btn btn-sm btn-icon btn-label-warning me-2"><i class="ti tabler-refresh"></i></button>
                                                            <span class="lh-1">{{ __('Cambiar estado') }}</span>
                                                        </a>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <a class="dropdown-item d-flex p-2 justify-content-start btn btn-label-danger align-items-center"
                                                            wire:click="confirmDelete({{ $tipo->id }})">
                                                            <button class="btn btn-sm btn-icon btn-label-danger me-2"><i class="ti tabler-trash"></i></button>
                                                            <span class="lh-1">{{ __('Eliminar') }}</span>
                                                        </a>
                                                    </li>
                                                @else
                                                    <li>
                                                        <a class="dropdown-item d-flex p-2 justify-content-start btn btn-label-success align-items-center"
                                                            wire:click="confirmRestore({{ $tipo->id }})">
                                                            <button class="btn btn-sm btn-icon btn-label-success me-2"><i class="ti tabler-refresh"></i></button>
                                                            <span class="lh-1">{{ __('Restaurar') }}</span>
                                                        </a>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <a class="dropdown-item d-flex p-2 justify-content-start btn btn-label-dark align-items-center"
                                                            wire:click="confirmForceDelete({{ $tipo->id }})">
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
                                    <span class="badge {{ $tipo->esta_activo ? 'bg-label-success' : 'bg-label-secondary' }}">
                                        {{ $tipo->estado_label }}
                                    </span>
                                </td>
                                <td>
                                    <div>
                                        <h6 class="mb-0">{{ $tipo->nombre }}</h6>
                                        @if($tipo->descripcion)
                                            <small class="text-muted">{{ \Illuminate\Support\Str::limit($tipo->descripcion, 50) }}</small>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-label-info">{{ $tipo->codigo }}</span>
                                </td>
                                <td>
                                    @php
                                        $catClass = match($tipo->categoria) {
                                            'contractual' => 'bg-label-primary',
                                            'certificacion' => 'bg-label-success',
                                            'administrativo' => 'bg-label-info',
                                            'disciplinario' => 'bg-label-danger',
                                            'liquidacion' => 'bg-label-warning',
                                            default => 'bg-label-secondary',
                                        };
                                    @endphp
                                    <span class="badge {{ $catClass }}">{{ $tipo->categoria_label }}</span>
                                </td>
                                <td class="text-center">
                                    @if($tipo->requiere_firma_empleado)
                                        <i class="ti tabler-check text-success"></i>
                                    @else
                                        <i class="ti tabler-x text-muted"></i>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($tipo->requiere_firma_empleador)
                                        <i class="ti tabler-check text-success"></i>
                                    @else
                                        <i class="ti tabler-x text-muted"></i>
                                    @endif
                                </td>
                                <td>
                                    @if($tipo->prefijo_numeracion)
                                        <span class="badge bg-label-dark">{{ $tipo->prefijo_numeracion }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($tipo->created_at)
                                        <span title="{{ $tipo->created_at->format('d/m/Y H:i') }}">
                                            {{ $tipo->created_at->diffForHumans() }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="ti tabler-file-off icon-48px text-muted mb-3"></i>
                                        <h6 class="mb-1">{{ __('No se encontraron tipos de documento') }}</h6>
                                        <p class="text-muted mb-0">
                                            @if ($search || $categoriaFilter || $statusFilter)
                                                {{ __('Intenta ajustar los filtros de búsqueda') }}
                                            @else
                                                {{ __('Aún no hay tipos registrados') }}
                                            @endif
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($this->tipos->hasPages())
                <div class="card-footer d-flex justify-content-between align-items-center">
                    <div class="text-muted small">
                        {{ __('Mostrando') }} {{ $this->tipos->firstItem() }} - {{ $this->tipos->lastItem() }}
                        {{ __('de') }} {{ $this->tipos->total() }} {{ __('tipos') }}
                    </div>
                    {{ $this->tipos->links() }}
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
                            {{ $isEditing ? __('Editar Tipo de Documento') : __('Nuevo Tipo de Documento') }}
                        </h5>
                        <button type="button" wire:click="closeModal" class="btn-close"></button>
                    </div>

                    <form wire:submit="save">
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('Código') }} <span class="text-danger">*</span></label>
                                    <input type="text" wire:model="codigo" class="form-control @error('codigo') is-invalid @enderror" placeholder="Ej: CONT-INDEF">
                                    @error('codigo')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">{{ __('Nombre') }} <span class="text-danger">*</span></label>
                                    <input type="text" wire:model="nombre" class="form-control @error('nombre') is-invalid @enderror" placeholder="Nombre del tipo">
                                    @error('nombre')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">{{ __('Categoría') }} <span class="text-danger">*</span></label>
                                    <select wire:model="categoria" class="form-select @error('categoria') is-invalid @enderror">
                                        <option value="">{{ __('Seleccionar...') }}</option>
                                        @foreach (\Modules\HR\Models\TipoDocumento::CATEGORIAS as $key => $label)
                                            <option value="{{ $key }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('categoria')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">{{ __('Orden') }}</label>
                                    <input type="number" wire:model="orden" class="form-control" min="0">
                                </div>

                                <div class="col-12">
                                    <label class="form-label">{{ __('Descripción') }}</label>
                                    <textarea wire:model="descripcion" class="form-control" rows="2" placeholder="Descripción opcional"></textarea>
                                </div>

                                <div class="col-12">
                                    <hr class="my-1">
                                    <h6 class="mb-3"><i class="ti tabler-signature me-1"></i>{{ __('Configuración de Firmas') }}</h6>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" wire:model="requiere_firma_empleado" id="firma_empleado">
                                        <label class="form-check-label" for="firma_empleado">{{ __('Firma empleado') }}</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" wire:model="requiere_firma_empleador" id="firma_empleador">
                                        <label class="form-check-label" for="firma_empleador">{{ __('Firma empleador') }}</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" wire:model="requiere_testigos" id="testigos">
                                        <label class="form-check-label" for="testigos">{{ __('Testigos') }}</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" wire:model="requiere_notarizacion" id="notarizacion">
                                        <label class="form-check-label" for="notarizacion">{{ __('Notarización') }}</label>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <hr class="my-1">
                                    <h6 class="mb-3"><i class="ti tabler-number me-1"></i>{{ __('Numeración Automática') }}</h6>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" wire:model="usa_numeracion_automatica" id="num_auto">
                                        <label class="form-check-label" for="num_auto">{{ __('Activar') }}</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">{{ __('Prefijo') }}</label>
                                    <input type="text" wire:model="prefijo_numeracion" class="form-control" placeholder="Ej: CONT-">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">{{ __('Formato') }}</label>
                                    <input type="text" wire:model="formato_numeracion" class="form-control" placeholder="{prefijo}{año}-{numero:4}">
                                </div>

                                <div class="col-md-4">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" wire:model="esta_activo" id="tipo_activo">
                                        <label class="form-check-label" for="tipo_activo">{{ __('Activo') }}</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" wire:click="closeModal" class="btn btn-outline-secondary">{{ __('Cancelar') }}</button>
                            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="save">
                                    <i class="ti tabler-device-floppy me-1"></i>
                                    {{ $isEditing ? __('Actualizar') : __('Crear Tipo') }}
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
                        <h5>{{ __('¿Eliminar tipo?') }}</h5>
                        <p class="text-muted mb-4">{{ __('El tipo se moverá a la papelera.') }}</p>
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
                        <h5>{{ __('¿Restaurar tipo?') }}</h5>
                        <p class="text-muted mb-4">{{ __('El tipo volverá a estar disponible.') }}</p>
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
