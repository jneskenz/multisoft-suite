<div>
    {{-- Card Body con filtros y tabla --}}
    <div class="card-body">
        <div class="row g-3">
            {{-- Búsqueda --}}
            <div class="col-12 col-md-4">
                <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="ti tabler-search"></i></span>
                    <input type="text" wire:model.live.debounce.300ms="search" class="form-control"
                        placeholder="{{ __('Buscar por nombre o descripción...') }}">
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

            {{-- Filtro por Módulo --}}
            <div class="col-6 col-md-3">
                <select wire:model.live="moduleFilter" class="form-select">
                    <option value="">{{ __('Todos los módulos') }}</option>
                    @foreach ($this->modules as $module)
                        <option value="{{ $module }}">{{ ucfirst($module) }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Filtro por Tipo --}}
            <div class="col-6 col-md-2">
                <select wire:model.live="systemFilter" class="form-select">
                    <option value="">{{ __('Todos') }}</option>
                    <option value="system">{{ __('Sistema') }}</option>
                    <option value="custom">{{ __('Personalizados') }}</option>
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
            <div class="col-6 col-md-1">
                @if ($search || $systemFilter || $moduleFilter)
                    <button wire:click="clearFilters" class="btn btn-outline-secondary w-100"
                        title="{{ __('Limpiar filtros') }}">
                        <i class="ti tabler-filter-off"></i>
                    </button>
                @endif
            </div>

            {{-- Tabla de roles --}}
            <div class="table-responsive">
                <table class="table table-hover table-sm align-middle">
                    <thead class="table-light">
                        <tr>
                            {{-- Columna: Acciones --}}
                            <th class="text-center" style="width: 140px;">
                                {{ __('Acciones') }}
                            </th>
                            {{-- Columna: Tipo --}}
                            <th class="text-center" style="width: 80px;">{{ __('Tipo') }}</th>
                            {{-- Columna: Rol --}}
                            <th wire:click="sortBy('display_name')" class="cursor-pointer" style="min-width: 200px;">
                                <div class="d-flex align-items-center">
                                    {{ __('Rol') }}
                                    @if ($sortField === 'display_name')
                                        <i class="ti tabler-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ms-1"></i>
                                    @endif
                                </div>
                            </th>
                            {{-- Columna: Identificador --}}
                            <th wire:click="sortBy('name')" class="cursor-pointer" style="min-width: 150px;">
                                <div class="d-flex align-items-center">
                                    {{ __('Identificador') }}
                                    @if ($sortField === 'name')
                                        <i class="ti tabler-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ms-1"></i>
                                    @endif
                                </div>
                            </th>
                            {{-- Columna: Permisos --}}
                            <th class="text-center" style="width: 100px;">{{ __('Permisos') }}</th>
                            {{-- Columna: Usuarios --}}
                            <th class="text-center" style="width: 100px;">{{ __('Usuarios') }}</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($this->roles as $role)
                            <tr wire:key="role-{{ $role->id }}">
                                <td>
                                    <div class="d-flex justify-content-start align-items-center gap-1">
                                        {{-- Numeración de registro --}}
                                        <button
                                            class="btn btn-sm rounded-pill btn-icon btn-label-secondary waves-effect me-1"
                                            data-bs-toggle="tooltip" title="ID: {{ $role->id }}">
                                            {{ $role->id }}
                                        </button>
                                        {{-- Editar --}}
                                        <button wire:click="edit({{ $role->id }})"
                                            class="btn btn-sm btn-icon btn-label-primary" data-bs-toggle="tooltip"
                                            title="{{ __('Editar rol') }}">
                                            <i class="ti tabler-edit"></i>
                                        </button>
                                        {{-- Gestionar Permisos --}}
                                        <button wire:click="managePermissions({{ $role->id }})"
                                            class="btn btn-sm btn-icon btn-label-warning" data-bs-toggle="tooltip"
                                            title="{{ __('Gestionar permisos') }}">
                                            <i class="ti tabler-shield-lock"></i>
                                        </button>
                                        {{-- Dropdown de acciones --}}
                                        @if (!$this->isProtectedRole($role->name))
                                            <div class="dropdown">
                                                <button
                                                    class="btn btn-sm btn-icon border btn-text-secondary rounded dropdown-toggle hide-arrow"
                                                    data-bs-toggle="dropdown">
                                                    <i class="ti tabler-dots-vertical"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li>
                                                        <a class="dropdown-item d-flex p-2 justify-content-start align-items-center"
                                                            wire:click="toggleSystem({{ $role->id }})">
                                                            <button class="btn btn-sm btn-icon btn-label-{{ $role->is_system ? 'warning' : 'info' }} me-2">
                                                                <i class="ti tabler-{{ $role->is_system ? 'user' : 'lock' }}"></i>
                                                            </button>
                                                            <span class="lh-1">{{ $role->is_system ? __('Hacer personalizado') : __('Marcar como sistema') }}</span>
                                                        </a>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <a class="dropdown-item d-flex p-2 justify-content-start align-items-center"
                                                            wire:click="confirmDelete({{ $role->id }})">
                                                            <button class="btn btn-sm btn-icon btn-label-danger me-2">
                                                                <i class="ti tabler-trash"></i>
                                                            </button>
                                                            <span class="lh-1">{{ __('Eliminar') }}</span>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                {{-- Tipo (Sistema/Personalizado) --}}
                                <td class="text-center">
                                    <span class="badge bg-label-{{ $role->is_system ? 'warning' : 'info' }}"
                                        data-bs-toggle="tooltip" data-bs-placement="right"
                                        title="{{ $role->is_system ? __('Rol del sistema') : __('Rol personalizado') }}">
                                        <i class="ti tabler-{{ $role->is_system ? 'lock' : 'user' }}"></i>
                                    </span>
                                </td>
                                {{-- Rol (Badge + Descripción) --}}
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="badge bg-label-{{ $this->getRoleBadgeColor($role->name) }} mb-1" style="width: fit-content;">
                                            {{ $role->display_name }}
                                        </span>
                                        @if($role->description)
                                            <small class="text-muted">{{ Str::limit($role->description, 50) }}</small>
                                        @endif
                                    </div>
                                </td>
                                {{-- Identificador --}}
                                <td>
                                    <code class="text-primary">{{ $role->name }}</code>
                                </td>
                                {{-- Permisos --}}
                                <td class="text-center">
                                    <span class="badge bg-label-info">
                                        <i class="ti tabler-shield-check me-1"></i>
                                        {{ $role->permissions_count }}
                                    </span>
                                </td>
                                {{-- Usuarios --}}
                                <td class="text-center">
                                    <span class="badge bg-label-{{ $role->users_count > 0 ? 'primary' : 'secondary' }}">
                                        <i class="ti tabler-users me-1"></i>
                                        {{ $role->users_count }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="ti tabler-shield-off icon-48px text-muted mb-3"></i>
                                        <h6 class="mb-1">{{ __('No se encontraron roles') }}</h6>
                                        <p class="text-muted mb-0">
                                            @if ($search || $systemFilter || $moduleFilter)
                                                {{ __('Intenta ajustar los filtros de búsqueda') }}
                                            @else
                                                {{ __('Aún no hay roles registrados') }}
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
            @if ($this->roles->hasPages())
                <div class="card-footer d-flex justify-content-between align-items-center">
                    <div class="text-muted small">
                        {{ __('Mostrando') }} {{ $this->roles->firstItem() }} - {{ $this->roles->lastItem() }}
                        {{ __('de') }} {{ $this->roles->total() }} {{ __('roles') }}
                    </div>
                    {{ $this->roles->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- MODAL: CREAR / EDITAR ROL --}}
    @if ($showModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    {{-- Header --}}
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="ti tabler-{{ $isEditing ? 'pencil' : 'shield-plus' }} me-2"></i>
                            {{ $isEditing ? __('Editar Rol') : __('Nuevo Rol') }}
                        </h5>
                        <button type="button" wire:click="closeModal" class="btn-close"></button>
                    </div>

                    {{-- Body --}}
                    <form wire:submit="save">
                        <div class="modal-body">
                            {{-- Nombre a mostrar --}}
                            <div class="mb-3">
                                <label class="form-label" for="display_name">
                                    {{ __('Nombre del Rol') }} <span class="text-danger">*</span>
                                </label>
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="ti tabler-shield"></i></span>
                                    <input type="text" wire:model="display_name" id="display_name"
                                        class="form-control @error('display_name') is-invalid @enderror"
                                        placeholder="{{ __('Ej: Administrador de Ventas') }}" autofocus>
                                </div>
                                @error('display_name')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Identificador --}}
                            <div class="mb-3">
                                <label class="form-label" for="name">
                                    {{ __('Identificador') }} <span class="text-danger">*</span>
                                </label>
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="ti tabler-code"></i></span>
                                    <input type="text" wire:model="name" id="name"
                                        class="form-control @error('name') is-invalid @enderror"
                                        placeholder="{{ __('Ej: sales-admin') }}"
                                        @if($isEditing && $this->isProtectedRole($name)) disabled @endif>
                                </div>
                                <small class="text-muted">{{ __('Solo letras minúsculas, números y guiones') }}</small>
                                @error('name')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Descripción --}}
                            <div class="mb-3">
                                <label class="form-label" for="description">
                                    {{ __('Descripción') }}
                                </label>
                                <textarea wire:model="description" id="description" rows="2"
                                    class="form-control @error('description') is-invalid @enderror"
                                    placeholder="{{ __('Descripción opcional del rol...') }}"></textarea>
                                @error('description')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Marcar como sistema --}}
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input type="checkbox" wire:model="is_system" id="is_system"
                                        class="form-check-input"
                                        @if($isEditing && $this->isProtectedRole($name)) disabled @endif>
                                    <label class="form-check-label" for="is_system">
                                        {{ __('Rol del sistema') }}
                                    </label>
                                </div>
                                <small class="text-muted">{{ __('Los roles del sistema no pueden ser eliminados') }}</small>
                            </div>
                        </div>

                        {{-- Footer --}}
                        <div class="modal-footer">
                            <button type="button" wire:click="closeModal" class="btn btn-outline-secondary">
                                {{ __('Cancelar') }}
                            </button>
                            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="save">
                                    <i class="ti tabler-device-floppy me-1"></i>
                                    {{ $isEditing ? __('Actualizar') : __('Crear Rol') }}
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

    {{-- MODAL: GESTIONAR PERMISOS --}}
    @if ($showPermissionsModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    {{-- Header --}}
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="ti tabler-shield-lock me-2"></i>
                            {{ __('Permisos de') }}: <span class="text-primary">{{ $display_name }}</span>
                        </h5>
                        <button type="button" wire:click="closeModal" class="btn-close"></button>
                    </div>

                    {{-- Body --}}
                    <div class="modal-body">
                        <div class="alert alert-info mb-3">
                            <i class="ti tabler-info-circle me-2"></i>
                            {{ __('Selecciona los permisos que deseas asignar a este rol. Puedes marcar módulos completos o permisos individuales.') }}
                        </div>

                        {{-- Permisos agrupados por módulo --}}
                        <div class="accordion" id="permissionsAccordion">
                            @foreach ($this->permissionsByModule as $module => $modulePermissions)
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }}" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#module-{{ $module }}">
                                            <div class="d-flex align-items-center w-100">
                                                <div class="form-check me-3" wire:click.stop>
                                                    <input type="checkbox" 
                                                        class="form-check-input"
                                                        wire:click="toggleModulePermissions('{{ $module }}')"
                                                        @checked($this->isModuleFullySelected($module))
                                                        @if($this->isModulePartiallySelected($module)) 
                                                            style="opacity: 0.5;" 
                                                        @endif>
                                                </div>
                                                <span class="badge bg-label-primary me-2">{{ strtoupper($module) }}</span>
                                                <span class="text-muted small">
                                                    ({{ count(array_intersect($modulePermissions->pluck('id')->toArray(), $selectedPermissions)) }}/{{ $modulePermissions->count() }} {{ __('seleccionados') }})
                                                </span>
                                            </div>
                                        </button>
                                    </h2>
                                    <div id="module-{{ $module }}" 
                                        class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}"
                                        data-bs-parent="#permissionsAccordion">
                                        <div class="accordion-body">
                                            <div class="row g-2">
                                                @foreach ($modulePermissions as $permission)
                                                    <div class="col-md-6">
                                                        <div class="form-check">
                                                            <input type="checkbox" 
                                                                class="form-check-input"
                                                                wire:model="selectedPermissions"
                                                                value="{{ $permission->id }}"
                                                                id="permission-{{ $permission->id }}">
                                                            <label class="form-check-label" for="permission-{{ $permission->id }}">
                                                                <span class="fw-medium">{{ $permission->display_name }}</span>
                                                                <br>
                                                                <code class="small text-muted">{{ $permission->name }}</code>
                                                            </label>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div class="modal-footer">
                        <div class="me-auto">
                            <span class="badge bg-primary">
                                {{ count($selectedPermissions) }} {{ __('permisos seleccionados') }}
                            </span>
                        </div>
                        <button type="button" wire:click="closeModal" class="btn btn-outline-secondary">
                            {{ __('Cancelar') }}
                        </button>
                        <button wire:click="savePermissions" class="btn btn-primary" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="savePermissions">
                                <i class="ti tabler-device-floppy me-1"></i>
                                {{ __('Guardar Permisos') }}
                            </span>
                            <span wire:loading wire:target="savePermissions">
                                <span class="spinner-border spinner-border-sm me-1"></span>
                                {{ __('Guardando...') }}
                            </span>
                        </button>
                    </div>
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
                        <div class="mb-3">
                            <i class="ti tabler-alert-triangle icon-48px text-warning"></i>
                        </div>
                        <h5>{{ __('¿Eliminar rol?') }}</h5>
                        <p class="text-muted mb-4">
                            {{ __('Esta acción eliminará el rol y todos sus permisos asociados.') }}
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
</div>
