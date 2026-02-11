<div>
    {{-- Card Body con filtros y tabla --}}
    <div class="card-body">
        <div class="row g-3">
            {{-- Búsqueda --}}
            <div class="col-12 col-md-4">
                <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="ti tabler-search"></i></span>
                    <input type="text" wire:model.live.debounce.300ms="search" class="form-control"
                        placeholder="{{ __('Buscar por nombre, ciudad o código...') }}">
                    <span class="input-group-text cursor-pointer">
                        <div wire:loading wire:target="search" class="spinner-border spinner-border-sm text-primary"
                            role="status">
                            <span class="visually-hidden">Buscando...</span>
                        </div>
                        @if ($search)
                            <i class="ti tabler-x" wire:click="$set('search', '')"></i>
                        @endif
                    </span>
                </div>
            </div>

            {{-- Filtro por Empresa --}}
            <div class="col-6 col-md-3">
                <select wire:model.live="companyFilter" class="form-select">
                    <option value="">{{ __('Todas las empresas') }}</option>
                    @foreach ($this->companies as $company)
                        <option value="{{ $company->id }}">{{ $company->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Filtro por Estado --}}
            <div class="col-6 col-md-2">
                <select wire:model.live="statusFilter" class="form-select">
                    <option value="">{{ __('Todos') }}</option>
                    <option value="active">{{ __('Activos') }}</option>
                    <option value="inactive">{{ __('Inactivos') }}</option>
                </select>
            </div>

            {{-- Por página --}}
            <div class="col-6 col-md-2">
                <select wire:model.live="perPage" class="form-select">
                    <option value="10">10 por página</option>
                    <option value="25">25 por página</option>
                    <option value="50">50 por página</option>
                </select>
            </div>

            {{-- Botón limpiar filtros --}}
            <div class="col-6 col-md-1 d-flex gap-1">
                @if ($search || $companyFilter || $statusFilter)
                    <button wire:click="clearFilters" class="btn btn-outline-secondary w-100"
                        title="{{ __('Limpiar filtros') }}">
                        <i class="ti tabler-filter-off"></i>
                    </button>
                @endif
                {{-- Botón ver eliminados --}}
                <button 
                    wire:click="toggleTrashedLocations" 
                    class="btn {{ $showTrashedLocations ? 'btn-danger' : 'btn-outline-secondary' }}"
                    title="{{ $showTrashedLocations ? __('Ver locales activos') : __('Ver locales eliminados') }}">
                    <i class="ti {{ $showTrashedLocations ? 'tabler-map-pin' : 'tabler-trash' }}"></i>
                </button>
            </div>

            {{-- Indicador de locales eliminados --}}
            @if ($showTrashedLocations)
                <div class="col-12">
                    <div class="alert alert-warning d-flex align-items-center mb-0" role="alert">
                        <i class="ti tabler-trash me-2"></i>
                        <span>{{ __('Mostrando locales eliminados. Puedes restaurarlos o eliminarlos permanentemente.') }}</span>
                    </div>
                </div>
            @endif

            {{-- Tabla de locales --}}
            <div class="table-responsive">
                <table class="table table-hover table-sm align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width: 120px;">{{ __('Acciones') }}</th>
                            <th>{{ __('Estado') }}</th>
                            <th wire:click="sortBy('name')" class="cursor-pointer" style="min-width: 200px;">
                                <div class="d-flex align-items-center">
                                    {{ __('Local') }}
                                    @if ($sortField === 'name')
                                        <i class="ti tabler-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ms-1"></i>
                                    @endif
                                </div>
                            </th>
                            <th style="min-width: 180px;">{{ __('Empresa') }}</th>
                            <th style="min-width: 120px;">{{ __('Ciudad') }}</th>
                            <th class="text-center" style="width: 100px;">{{ __('Principal') }}</th>
                            <th wire:click="sortBy('created_at')" class="cursor-pointer" style="width: 150px;">
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
                        @forelse($this->locations as $location)
                            <tr wire:key="location-{{ $location->id }}">
                                <td>
                                    <div class="d-flex justify-content-start align-items-center gap-1">
                                        <button class="btn btn-sm rounded-pill btn-icon btn-label-secondary waves-effect me-1"
                                            data-bs-toggle="tooltip" title="Nro de registro: {{ $location->id }}">
                                            {{ $location->id }}
                                        </button>
                                        <button wire:click="edit({{ $location->id }})"
                                            class="btn btn-sm btn-icon btn-label-primary" data-bs-toggle="tooltip"
                                            title="Editar local">
                                            <i class="ti tabler-edit"></i>
                                        </button>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-icon border btn-text-secondary rounded dropdown-toggle hide-arrow"
                                                data-bs-toggle="dropdown">
                                                <i class="ti tabler-dots-vertical"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                @if (!$showTrashedLocations)
                                                    @if (!$location->is_main)
                                                        <li>
                                                            <a class="dropdown-item d-flex p-2 justify-content-start btn btn-label-info align-items-center"
                                                                wire:click="setAsMain({{ $location->id }})">
                                                                <button class="btn btn-sm btn-icon btn-label-info me-2">
                                                                    <i class="ti tabler-star"></i>
                                                                </button>
                                                                <span class="lh-1">{{ __('Marcar como principal') }}</span>
                                                            </a>
                                                        </li>
                                                        <li><hr class="dropdown-divider"></li>
                                                    @endif
                                                    <li>
                                                        <a class="dropdown-item d-flex p-2 justify-content-start btn btn-label-{{ $location->status === 'active' ? 'warning' : 'success' }} align-items-center"
                                                            wire:click="toggleStatus({{ $location->id }})">
                                                            <button class="btn btn-sm btn-icon btn-label-{{ $location->status === 'active' ? 'warning' : 'success' }} me-2">
                                                                <i class="ti tabler-{{ $location->status === 'active' ? 'ban' : 'check' }}"></i>
                                                            </button>
                                                            <span class="lh-1">{{ $location->status === 'active' ? __('Desactivar') : __('Activar') }}</span>
                                                        </a>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <a class="dropdown-item d-flex p-2 justify-content-start btn btn-label-danger align-items-center"
                                                            wire:click="confirmDelete({{ $location->id }})">
                                                            <button class="btn btn-sm btn-icon btn-label-danger me-2">
                                                                <i class="ti tabler-trash"></i>
                                                            </button>
                                                            <span class="lh-1">{{ __('Eliminar') }}</span>
                                                        </a>
                                                    </li>
                                                @else
                                                    <li>
                                                        <a class="dropdown-item d-flex p-2 justify-content-start btn btn-label-success align-items-center"
                                                            wire:click="confirmRestore({{ $location->id }})">
                                                            <button class="btn btn-sm btn-icon btn-label-success me-2">
                                                                <i class="ti tabler-refresh"></i>
                                                            </button>
                                                            <span class="lh-1">{{ __('Restaurar') }}</span>
                                                        </a>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <a class="dropdown-item d-flex p-2 justify-content-start btn btn-label-dark align-items-center"
                                                            wire:click="confirmForceDelete({{ $location->id }})">
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
                                    <span class="badge {{ $location->status === 'active' ? 'bg-label-success' : 'bg-label-warning' }}">
                                        {{ $location->status_label }}
                                    </span>
                                </td>
                                <td>
                                    <div>
                                        <h6 class="mb-0">{{ $location->name }}</h6>
                                        @if($location->code)
                                            <small class="text-muted">{{ $location->code }}</small>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-label-primary" title="{{ $location->company->name ?? '' }}">
                                        {{ $location->company->display_name ?? 'N/A' }}
                                    </span>
                                </td>
                                <td>{{ $location->city ?? '-' }}</td>
                                <td class="text-center">
                                    @if($location->is_main)
                                        <span class="badge bg-label-info">
                                            <i class="ti tabler-star"></i> {{ __('Principal') }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <span title="{{ $location->created_at->format('d/m/Y H:i') }}">
                                        {{ $location->created_at->diffForHumans() }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="ti tabler-map-pin-off icon-48px text-muted mb-3"></i>
                                        <h6 class="mb-1">{{ __('No se encontraron locales') }}</h6>
                                        <p class="text-muted mb-0">
                                            @if ($search || $companyFilter || $statusFilter)
                                                {{ __('Intenta ajustar los filtros de búsqueda') }}
                                            @else
                                                {{ __('Aún no hay locales registrados') }}
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
            @if ($this->locations->hasPages())
                <div class="card-footer d-flex justify-content-between align-items-center">
                    <div class="text-muted small">
                        {{ __('Mostrando') }} {{ $this->locations->firstItem() }} - {{ $this->locations->lastItem() }}
                        {{ __('de') }} {{ $this->locations->total() }} {{ __('locales') }}
                    </div>
                    {{ $this->locations->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- MODAL: CREAR / EDITAR LOCAL --}}
    @if ($showModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="ti tabler-{{ $isEditing ? 'pencil' : 'map-pin-plus' }} me-2"></i>
                            {{ $isEditing ? __('Editar Local') : __('Nuevo Local') }}
                        </h5>
                        <button type="button" wire:click="closeModal" class="btn-close"></button>
                    </div>

                    <form wire:submit="save">
                        <div class="modal-body">
                            {{-- Empresa --}}
                            <div class="mb-3">
                                <label class="form-label">{{ __('Empresa') }} <span class="text-danger">*</span></label>
                                <select wire:model="company_id" class="form-select @error('company_id') is-invalid @enderror">
                                    <option value="">{{ __('Seleccionar empresa...') }}</option>
                                    @foreach ($this->companies as $company)
                                        <option value="{{ $company->id }}">{{ $company->name }}</option>
                                    @endforeach
                                </select>
                                @error('company_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>

                            {{-- Código --}}
                            <div class="mb-3">
                                <label class="form-label">{{ __('Código') }}</label>
                                <input type="text" wire:model="code" class="form-control @error('code') is-invalid @enderror"
                                    placeholder="{{ __('Código opcional') }}">
                                @error('code')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>

                            {{-- Nombre --}}
                            <div class="mb-3">
                                <label class="form-label">{{ __('Nombre del Local') }} <span class="text-danger">*</span></label>
                                <input type="text" wire:model="name" class="form-control @error('name') is-invalid @enderror"
                                    placeholder="{{ __('Nombre del local') }}" autofocus>
                                @error('name')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>

                            {{-- Dirección --}}
                            <div class="mb-3">
                                <label class="form-label">{{ __('Dirección') }}</label>
                                <textarea wire:model="address" class="form-control @error('address') is-invalid @enderror"
                                    rows="2" placeholder="{{ __('Dirección del local') }}"></textarea>
                                @error('address')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>

                            {{-- Ciudad y Teléfono --}}
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">{{ __('Ciudad') }}</label>
                                    <input type="text" wire:model="city" class="form-control @error('city') is-invalid @enderror"
                                        placeholder="{{ __('Ciudad') }}">
                                    @error('city')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">{{ __('Teléfono') }}</label>
                                    <input type="text" wire:model="phone" class="form-control @error('phone') is-invalid @enderror"
                                        placeholder="{{ __('Teléfono') }}">
                                    @error('phone')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            {{-- Local Principal --}}
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" wire:model="is_main" id="is_main">
                                    <label class="form-check-label" for="is_main">
                                        {{ __('Marcar como local principal') }}
                                    </label>
                                </div>
                                <small class="text-muted">{{ __('Solo puede haber un local principal por empresa') }}</small>
                            </div>

                            {{-- Estado --}}
                            <div class="mb-3">
                                <label class="form-label">{{ __('Estado') }}</label>
                                <select wire:model="status" class="form-select @error('status') is-invalid @enderror">
                                    <option value="active">{{ __('Activo') }}</option>
                                    <option value="inactive">{{ __('Inactivo') }}</option>
                                </select>
                                @error('status')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" wire:click="closeModal" class="btn btn-outline-secondary">{{ __('Cancelar') }}</button>
                            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="save">
                                    <i class="ti tabler-device-floppy me-1"></i>
                                    {{ $isEditing ? __('Actualizar') : __('Crear Local') }}
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

    {{-- MODAL: CONFIRMAR ELIMINACIÓN --}}
    @if ($showDeleteModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content">
                    <div class="modal-body text-center py-4">
                        <div class="mb-3"><i class="ti tabler-alert-triangle icon-48px text-warning"></i></div>
                        <h5>{{ __('¿Eliminar local?') }}</h5>
                        <p class="text-muted mb-4">{{ __('El local será movido a la papelera y podrá ser restaurado posteriormente.') }}</p>
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

    {{-- MODAL: CONFIRMAR RESTAURACIÓN --}}
    @if ($showRestoreModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content">
                    <div class="modal-body text-center py-4">
                        <div class="mb-3"><i class="ti tabler-refresh icon-48px text-success"></i></div>
                        <h5>{{ __('¿Restaurar local?') }}</h5>
                        <p class="text-muted mb-4">{{ __('El local será restaurado y volverá a estar disponible.') }}</p>
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

    {{-- MODAL: CONFIRMAR ELIMINACIÓN PERMANENTE --}}
    @if ($showForceDeleteModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content">
                    <div class="modal-body text-center py-4">
                        <div class="mb-3"><i class="ti tabler-trash-x icon-48px text-danger"></i></div>
                        <h5>{{ __('¿Eliminar permanentemente?') }}</h5>
                        <p class="text-muted mb-4">{{ __('Esta acción no se puede deshacer. Todos los datos del local serán eliminados.') }}</p>
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
