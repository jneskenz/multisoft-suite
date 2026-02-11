<div>
    {{-- Card Body con filtros y tabla --}}
    <div class="card-body">
        <div class="row g-3">
            {{-- Búsqueda --}}
            <div class="col-12 col-md-4">
                <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="ti tabler-search"></i></span>
                    <input type="text" wire:model.live.debounce.300ms="search" class="form-control"
                        placeholder="{{ __('Buscar por nombre, RUC o código...') }}">
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

            {{-- Filtro por Grupo --}}
            <div class="col-6 col-md-3">
                <select wire:model.live="groupFilter" class="form-select">
                    <option value="">{{ __('Todos los grupos') }}</option>
                    @foreach ($this->groups as $group)
                        <option value="{{ $group->id }}">{{ $group->code }} - {{ $group->business_name }}</option>
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
                @if ($search || $groupFilter || $statusFilter)
                    <button wire:click="clearFilters" class="btn btn-outline-secondary w-100"
                        title="{{ __('Limpiar filtros') }}">
                        <i class="ti tabler-filter-off"></i>
                    </button>
                @endif
                {{-- Botón ver eliminados --}}
                <button 
                    wire:click="toggleTrashedCompanies" 
                    class="btn {{ $showTrashedCompanies ? 'btn-danger' : 'btn-outline-secondary' }}"
                    title="{{ $showTrashedCompanies ? __('Ver empresas activas') : __('Ver empresas eliminadas') }}">
                    <i class="ti {{ $showTrashedCompanies ? 'tabler-building' : 'tabler-trash' }}"></i>
                </button>
            </div>

            {{-- Indicador de empresas eliminadas --}}
            @if ($showTrashedCompanies)
                <div class="col-12">
                    <div class="alert alert-warning d-flex align-items-center mb-0" role="alert">
                        <i class="ti tabler-trash me-2"></i>
                        <span>{{ __('Mostrando empresas eliminadas. Puedes restaurarlas o eliminarlas permanentemente.') }}</span>
                    </div>
                </div>
            @endif

            {{-- Tabla de empresas --}}
            <div class="table-responsive">
                <table class="table table-hover table-sm align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width: 120px;">{{ __('Acciones') }}</th>
                            <th>{{ __('Estado') }}</th>
                            <th wire:click="sortBy('name')" class="cursor-pointer" style="min-width: 250px;">
                                <div class="d-flex align-items-center">
                                    {{ __('Empresa') }}
                                    @if ($sortField === 'name')
                                        <i class="ti tabler-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ms-1"></i>
                                    @endif
                                </div>
                            </th>
                            <th style="min-width: 150px;">{{ __('Grupo') }}</th>
                            <th style="min-width: 120px;">{{ __('RUC') }}</th>
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
                        @forelse($this->companies as $company)
                            <tr wire:key="company-{{ $company->id }}">
                                <td>
                                    <div class="d-flex justify-content-start align-items-center gap-1">
                                        <button class="btn btn-sm rounded-pill btn-icon btn-label-secondary waves-effect me-1"
                                            data-bs-toggle="tooltip" title="Nro de registro: {{ $company->id }}">
                                            {{ $company->id }}
                                        </button>
                                        <button wire:click="edit({{ $company->id }})"
                                            class="btn btn-sm btn-icon btn-label-primary" data-bs-toggle="tooltip"
                                            title="Editar empresa">
                                            <i class="ti tabler-edit"></i>
                                        </button>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-icon border btn-text-secondary rounded dropdown-toggle hide-arrow"
                                                data-bs-toggle="dropdown">
                                                <i class="ti tabler-dots-vertical"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                @if (!$showTrashedCompanies)
                                                    <li>
                                                        <a class="dropdown-item d-flex p-2 justify-content-start btn btn-label-{{ $company->status === 'active' ? 'warning' : 'success' }} align-items-center"
                                                            wire:click="toggleStatus({{ $company->id }})">
                                                            <button class="btn btn-sm btn-icon btn-label-{{ $company->status === 'active' ? 'warning' : 'success' }} me-2">
                                                                <i class="ti tabler-{{ $company->status === 'active' ? 'ban' : 'check' }}"></i>
                                                            </button>
                                                            <span class="lh-1">{{ $company->status === 'active' ? __('Desactivar') : __('Activar') }}</span>
                                                        </a>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <a class="dropdown-item d-flex p-2 justify-content-start btn btn-label-danger align-items-center"
                                                            wire:click="confirmDelete({{ $company->id }})">
                                                            <button class="btn btn-sm btn-icon btn-label-danger me-2">
                                                                <i class="ti tabler-trash"></i>
                                                            </button>
                                                            <span class="lh-1">{{ __('Eliminar') }}</span>
                                                        </a>
                                                    </li>
                                                @else
                                                    <li>
                                                        <a class="dropdown-item d-flex p-2 justify-content-start btn btn-label-success align-items-center"
                                                            wire:click="confirmRestore({{ $company->id }})">
                                                            <button class="btn btn-sm btn-icon btn-label-success me-2">
                                                                <i class="ti tabler-refresh"></i>
                                                            </button>
                                                            <span class="lh-1">{{ __('Restaurar') }}</span>
                                                        </a>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <a class="dropdown-item d-flex p-2 justify-content-start btn btn-label-dark align-items-center"
                                                            wire:click="confirmForceDelete({{ $company->id }})">
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
                                    <span class="badge {{ $company->status === 'active' ? 'bg-label-success' : 'bg-label-warning' }}">
                                        {{ $company->status_label }}
                                    </span>
                                </td>
                                <td>
                                    <div>
                                        <h6 class="mb-0">{{ $company->name }}</h6>
                                        @if($company->trade_name)
                                            <small class="text-muted">{{ $company->trade_name }}</small>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-label-primary" title="{{ $company->groupCompany->business_name ?? '' }}">
                                        {{ $company->groupCompany->code ?? 'N/A' }}
                                    </span>
                                </td>
                                <td>{{ $company->tax_id ?? '-' }}</td>
                                <td>
                                    <span title="{{ $company->created_at->format('d/m/Y H:i') }}">
                                        {{ $company->created_at->diffForHumans() }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="ti tabler-building-minus icon-48px text-muted mb-3"></i>
                                        <h6 class="mb-1">{{ __('No se encontraron empresas') }}</h6>
                                        <p class="text-muted mb-0">
                                            @if ($search || $groupFilter || $statusFilter)
                                                {{ __('Intenta ajustar los filtros de búsqueda') }}
                                            @else
                                                {{ __('Aún no hay empresas registradas') }}
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
            @if ($this->companies->hasPages())
                <div class="card-footer d-flex justify-content-between align-items-center">
                    <div class="text-muted small">
                        {{ __('Mostrando') }} {{ $this->companies->firstItem() }} - {{ $this->companies->lastItem() }}
                        {{ __('de') }} {{ $this->companies->total() }} {{ __('empresas') }}
                    </div>
                    {{ $this->companies->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- MODAL: CREAR / EDITAR EMPRESA --}}
    @if ($showModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="ti tabler-{{ $isEditing ? 'pencil' : 'building-plus' }} me-2"></i>
                            {{ $isEditing ? __('Editar Empresa') : __('Nueva Empresa') }}
                        </h5>
                        <button type="button" wire:click="closeModal" class="btn-close"></button>
                    </div>

                    <form wire:submit="save">
                        <div class="modal-body">
                            {{-- Grupo --}}
                            <div class="mb-3">
                                <label class="form-label">{{ __('Grupo') }} <span class="text-danger">*</span></label>
                                <select wire:model="group_company_id" class="form-select @error('group_company_id') is-invalid @enderror">
                                    <option value="">{{ __('Seleccionar grupo...') }}</option>
                                    @foreach ($this->groups as $group)
                                        <option value="{{ $group->id }}">{{ $group->code }} - {{ $group->business_name }}</option>
                                    @endforeach
                                </select>
                                @error('group_company_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>

                            {{-- Código --}}
                            <div class="mb-3">
                                <label class="form-label">{{ __('Código') }}</label>
                                <input type="text" wire:model="code" class="form-control @error('code') is-invalid @enderror"
                                    placeholder="{{ __('Código opcional') }}">
                                @error('code')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>

                            {{-- Razón Social --}}
                            <div class="mb-3">
                                <label class="form-label">{{ __('Razón Social') }} <span class="text-danger">*</span></label>
                                <input type="text" wire:model="name" class="form-control @error('name') is-invalid @enderror"
                                    placeholder="{{ __('Razón social de la empresa') }}" autofocus>
                                @error('name')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>

                            {{-- Nombre Comercial --}}
                            <div class="mb-3">
                                <label class="form-label">{{ __('Nombre Comercial') }}</label>
                                <input type="text" wire:model="trade_name" class="form-control @error('trade_name') is-invalid @enderror"
                                    placeholder="{{ __('Nombre comercial') }}">
                                @error('trade_name')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>

                            {{-- RUC --}}
                            <div class="mb-3">
                                <label class="form-label">{{ __('RUC') }}</label>
                                <input type="text" wire:model="tax_id" class="form-control @error('tax_id') is-invalid @enderror"
                                    placeholder="{{ __('RUC de la empresa') }}">
                                @error('tax_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>

                            {{-- Dirección --}}
                            <div class="mb-3">
                                <label class="form-label">{{ __('Dirección') }}</label>
                                <textarea wire:model="address" class="form-control @error('address') is-invalid @enderror"
                                    rows="2" placeholder="{{ __('Dirección fiscal') }}"></textarea>
                                @error('address')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>

                            {{-- Teléfono y Email --}}
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">{{ __('Teléfono') }}</label>
                                    <input type="text" wire:model="phone" class="form-control @error('phone') is-invalid @enderror"
                                        placeholder="{{ __('Teléfono') }}">
                                    @error('phone')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">{{ __('Email') }}</label>
                                    <input type="email" wire:model="email" class="form-control @error('email') is-invalid @enderror"
                                        placeholder="{{ __('correo@ejemplo.com') }}">
                                    @error('email')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                </div>
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
                                    {{ $isEditing ? __('Actualizar') : __('Crear Empresa') }}
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
                        <h5>{{ __('¿Eliminar empresa?') }}</h5>
                        <p class="text-muted mb-4">{{ __('La empresa será movida a la papelera y podrá ser restaurada posteriormente.') }}</p>
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
                        <h5>{{ __('¿Restaurar empresa?') }}</h5>
                        <p class="text-muted mb-4">{{ __('La empresa será restaurada y volverá a estar disponible.') }}</p>
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
                        <p class="text-muted mb-4">{{ __('Esta acción no se puede deshacer. Todos los datos de la empresa serán eliminados.') }}</p>
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
