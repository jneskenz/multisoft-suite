<div>
    {{-- Card Body con filtros y tabla --}}
    <div class="card-body">
        <div class="row g-3">
            {{-- Búsqueda --}}
            <div class="col-12 col-md-4">
                <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="ti tabler-search"></i></span>
                    <input type="text" wire:model.live.debounce.300ms="search" class="form-control"
                        placeholder="{{ __('Buscar por nombre o email...') }}">
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

            {{-- Filtro por Rol --}}
            <div class="col-6 col-md-3">
                <select wire:model.live="roleFilter" class="form-select">
                    <option value="">{{ __('Todos los roles') }}</option>
                    @foreach ($this->roles as $role)
                        <option value="{{ $role->name }}">{{ $role->display_name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Filtro por Estado --}}
            <div class="col-6 col-md-2">
                <select wire:model.live="statusFilter" class="form-select">
                    <option value="">{{ __('Todos') }}</option>
                    <option value="active">{{ __('Activos') }}</option>
                    <option value="suspended">{{ __('Suspendidos') }}</option>
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
                @if ($search || $roleFilter || $statusFilter)
                    <button wire:click="clearFilters" class="btn btn-outline-secondary w-100"
                        title="{{ __('Limpiar filtros') }}">
                        <i class="ti tabler-filter-off"></i>
                    </button>
                @endif
                {{-- Botón ver eliminados --}}
                <button 
                    wire:click="toggleTrashedUsers" 
                    class="btn {{ $showTrashedUsers ? 'btn-danger' : 'btn-outline-secondary' }}"
                    title="{{ $showTrashedUsers ? __('Ver usuarios activos') : __('Ver usuarios eliminados') }}">
                    <i class="ti {{ $showTrashedUsers ? 'tabler-users' : 'tabler-trash' }}"></i>
                </button>
            </div>

            {{-- Indicador de usuarios eliminados --}}
            @if ($showTrashedUsers)
                <div class="col-12">
                    <div class="alert alert-warning d-flex align-items-center mb-0" role="alert">
                        <i class="ti tabler-trash me-2"></i>
                        <span>{{ __('Mostrando usuarios eliminados. Puedes restaurarlos o eliminarlos permanentemente.') }}</span>
                    </div>
                </div>
            @endif

            {{-- Tabla de usuarios --}}
            <div class="table-responsive">
                <table class="table table-hover table-sm align-middle">
                    <thead class="table-light">
                        <tr>
                            {{-- Columna: Acciones --}}
                            <th class="text-center" style="width: 120px;">
                                {{ __('Acciones') }}
                                @if ($sortField === 'id')
                                    <i
                                        class="ti tabler-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ms-1"></i>
                                @endif
                            </th>
                            {{-- Columna: Estado --}}
                            <th>{{ __('Estado') }}</th>
                            {{-- Columna: Usuario --}}
                            <th wire:click="sortBy('name')" class="cursor-pointer" style="min-width: 250px;">
                                <div class="d-flex align-items-center">
                                    {{ __('Usuario') }}
                                    @if ($sortField === 'name')
                                        <i
                                            class="ti tabler-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ms-1"></i>
                                    @endif
                                </div>
                            </th>
                            {{-- Columna: Rol --}}
                            <th style="min-width: 150px;">{{ __('Rol') }}</th>
                            {{-- Columna: Grupos --}}
                            <th style="min-width: 180px;">{{ __('Grupos') }}</th>
                            {{-- Columna: Fecha --}}
                            <th wire:click="sortBy('created_at')" class="cursor-pointer" style="width: 150px;">
                                <div class="d-flex align-items-center">
                                    {{ __('Creado') }}
                                    @if ($sortField === 'created_at')
                                        <i
                                            class="ti tabler-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ms-1"></i>
                                    @endif
                                </div>
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($this->users as $user)
                            <tr wire:key="user-{{ $user->id }}">
                                <td>
                                    <div class="d-flex justify-content-start align-items-center gap-1">
                                        {{-- Numeracion de registro --}}
                                        <button
                                            class="btn btn-sm rounded-pill btn-icon btn-label-secondary waves-effect me-1"
                                            data-bs-toggle="tooltip" title="Nro de registro: {{ $user->id }}">
                                            {{ $user->id }}
                                        </button>
                                        {{-- Acciones de registro --}}
                                        <button wire:click="edit({{ $user->id }})"
                                            class="btn btn-sm btn-icon btn-label-primary" data-bs-toggle="tooltip"
                                            title="Editar usuario">
                                            <i class="ti tabler-edit"></i>
                                        </button>
                                        <a href="#" class="btn btn-sm btn-icon btn-label-warning"
                                            data-bs-toggle="tooltip" title="Mostrar detalles de usuario">
                                            <i class="ti tabler-eye"></i>
                                        </a>
                                        @if ($user->id != auth()->id())
                                            <div class="dropdown">
                                                <button
                                                    class="btn btn-sm btn-icon border btn-text-secondary rounded dropdown-toggle hide-arrow"
                                                    data-bs-toggle="dropdown">
                                                    <i class="ti tabler-dots-vertical"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    @if (!$showTrashedUsers)
                                                        {{-- Suspender/Activar según estado actual --}}
                                                        @if ($user->estado === \App\Models\User::ESTADO_ACTIVO)
                                                            <li>
                                                                <a class="dropdown-item d-flex p-2 justify-content-start btn btn-label-warning align-items-center"
                                                                    wire:click="suspend({{ $user->id }})"
                                                                    data-bs-toggle="tooltip" data-bs-custom-class="tooltip-warning"
                                                                    title="Suspender usuario">
                                                                    <button class="btn btn-sm btn-icon btn-label-warning me-2">
                                                                        <i class="ti tabler-ban"></i>
                                                                    </button>
                                                                    <span class="lh-1">{{ __('Suspender') }}</span>
                                                                </a>
                                                            </li>
                                                        @else
                                                            <li>
                                                                <a class="dropdown-item d-flex p-2 justify-content-start btn btn-label-success align-items-center"
                                                                    wire:click="activate({{ $user->id }})"
                                                                    data-bs-toggle="tooltip" data-bs-custom-class="tooltip-success"
                                                                    title="Activar usuario">
                                                                    <button class="btn btn-sm btn-icon btn-label-success me-2">
                                                                        <i class="ti tabler-check"></i>
                                                                    </button>
                                                                    <span class="lh-1">{{ __('Activar') }}</span>
                                                                </a>
                                                            </li>
                                                        @endif
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li>
                                                            <a class="dropdown-item d-flex p-2 justify-content-start btn btn-label-danger align-items-center"
                                                                wire:click="confirmDelete({{ $user->id }})"
                                                                data-bs-toggle="tooltip" data-bs-custom-class="tooltip-danger"
                                                                title="Eliminar usuario del sistema">
                                                                <button class="btn btn-sm btn-icon btn-label-danger me-2">
                                                                    <i class="ti tabler-trash"></i>
                                                                </button>
                                                                <span class="lh-1">{{ __('Eliminar') }}</span>
                                                            </a>
                                                        </li>
                                                    @else
                                                        {{-- Acciones para usuarios eliminados --}}
                                                        <li>
                                                            <a class="dropdown-item d-flex p-2 justify-content-start btn btn-label-success align-items-center"
                                                                wire:click="confirmRestore({{ $user->id }})"
                                                                data-bs-toggle="tooltip" data-bs-custom-class="tooltip-success"
                                                                title="Restaurar usuario">
                                                                <button class="btn btn-sm btn-icon btn-label-success me-2">
                                                                    <i class="ti tabler-refresh"></i>
                                                                </button>
                                                                <span class="lh-1">{{ __('Restaurar') }}</span>
                                                            </a>
                                                        </li>
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li>
                                                            <a class="dropdown-item d-flex p-2 justify-content-start btn btn-label-dark align-items-center"
                                                                wire:click="confirmForceDelete({{ $user->id }})"
                                                                data-bs-toggle="tooltip" data-bs-custom-class="tooltip-dark"
                                                                title="Eliminar permanentemente">
                                                                <button class="btn btn-sm btn-icon btn-label-dark me-2">
                                                                    <i class="ti tabler-trash-x"></i>
                                                                </button>
                                                                <span class="lh-1">{{ __('Eliminar permanente') }}</span>
                                                            </a>
                                                        </li>
                                                    @endif
                                                </ul>
                                            </div>
                                        @endif

                                    </div>
                                </td>
                                {{-- Estado --}}
                                <td>
                                    <span class="badge {{ $user->estado === \App\Models\User::ESTADO_ACTIVO ? 'bg-label-success' : 'bg-label-warning' }}">
                                        {{ $user->estado_label }}
                                    </span>
                                </td>
                                {{-- Usuario --}}
                                <td>
                                    <div>
                                        <h6 class="mb-0">{{ $user->name }}</h6>
                                        <small class="text-muted">{{ $user->email }}</small>
                                    </div>
                                </td>
                                {{-- Rol --}}
                                <td>
                                    @php
                                        $userRole = optional($user->roles)->first();
                                    @endphp
                                    @if ($userRole)
                                        <span class="badge bg-label-{{ $user->isSuperAdmin() ? 'danger' : ($user->isAdmin() ? 'warning' : 'info') }}">
                                            {{ $userRole->display_name }}
                                        </span>
                                    @else
                                        <span class="badge bg-label-secondary">{{ __('Sin rol') }}</span>
                                    @endif
                                </td>
                                {{-- Grupos --}}
                                <td>
                                    @foreach($user->groupCompanies as $group)
                                        <span class="badge bg-label-primary me-1" title="{{ $group->business_name ?? $group->trade_name }}">
                                            {{ $group->code }}
                                        </span>
                                    @endforeach
                                </td>
                                {{-- Fecha de creación --}}
                                <td>
                                    <span title="{{ $user->created_at->format('d/m/Y H:i') }}">
                                        {{ $user->created_at->diffForHumans() }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="ti tabler-users-minus icon-48px text-muted mb-3"></i>
                                        <h6 class="mb-1">{{ __('No se encontraron usuarios') }}</h6>
                                        <p class="text-muted mb-0">
                                            @if ($search || $roleFilter || $statusFilter)
                                                {{ __('Intenta ajustar los filtros de búsqueda') }}
                                            @else
                                                {{ __('Aún no hay usuarios registrados') }}
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
            @if ($this->users->hasPages())
                <div class="card-footer d-flex justify-content-between align-items-center">
                    <div class="text-muted small">
                        {{ __('Mostrando') }} {{ $this->users->firstItem() }} - {{ $this->users->lastItem() }}
                        {{ __('de') }} {{ $this->users->total() }} {{ __('usuarios') }}
                    </div>
                    {{ $this->users->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- MODAL: CREAR / EDITAR USUARIO --}}
    @if ($showModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    {{-- Header --}}
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="ti tabler-{{ $isEditing ? 'pencil' : 'user-plus' }} me-2"></i>
                            {{ $isEditing ? __('Editar Usuario') : __('Nuevo Usuario') }}
                        </h5>
                        <button type="button" wire:click="closeModal" class="btn-close"></button>
                    </div>

                    {{-- Body --}}
                    <form wire:submit="save">
                        <div class="modal-body">
                            {{-- Nombre --}}
                            <div class="mb-3">
                                <label class="form-label" for="name">
                                    {{ __('Nombre') }} <span class="text-danger">*</span>
                                </label>
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="ti tabler-user"></i></span>
                                    <input type="text" wire:model="name" id="name"
                                        class="form-control @error('name') is-invalid @enderror"
                                        placeholder="{{ __('Nombre completo') }}" autofocus>
                                </div>
                                @error('name')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Email --}}
                            <div class="mb-3">
                                <label class="form-label" for="email">
                                    {{ __('Correo electrónico') }} <span class="text-danger">*</span>
                                </label>
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="ti tabler-mail"></i></span>
                                    <input type="email" wire:model="email" id="email"
                                        class="form-control @error('email') is-invalid @enderror"
                                        placeholder="{{ __('correo@ejemplo.com') }}">
                                </div>
                                @error('email')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Rol --}}
                            <div class="mb-3">
                                <label class="form-label" for="selectedRole">
                                    {{ __('Rol') }} <span class="text-danger">*</span>
                                </label>
                                <select wire:model="selectedRole" id="selectedRole"
                                    class="form-select @error('selectedRole') is-invalid @enderror">
                                    <option value="">{{ __('Seleccionar rol...') }}</option>
                                    @foreach ($this->roles as $role)
                                        <option value="{{ $role->name }}">{{ $role->display_name }}</option>
                                    @endforeach
                                </select>
                                @error('selectedRole')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Contraseña --}}
                            <div class="mb-3">
                                <label class="form-label" for="password">
                                    {{ __('Contraseña') }}
                                    @if (!$isEditing)
                                        <span class="text-danger">*</span>
                                    @endif
                                </label>
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="ti tabler-lock"></i></span>
                                    <input type="password" wire:model="password" id="password"
                                        class="form-control @error('password') is-invalid @enderror"
                                        placeholder="{{ $isEditing ? __('Dejar vacío para mantener') : __('Mínimo 8 caracteres') }}">
                                </div>
                                @error('password')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Confirmar Contraseña --}}
                            <div class="mb-3">
                                <label class="form-label" for="password_confirmation">
                                    {{ __('Confirmar contraseña') }}
                                </label>
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="ti tabler-lock-check"></i></span>
                                    <input type="password" wire:model="password_confirmation"
                                        id="password_confirmation" class="form-control"
                                        placeholder="{{ __('Repetir contraseña') }}">
                                </div>
                            </div>

                            {{-- Estado del usuario --}}
                            <div class="mb-3">
                                <label class="form-label" for="estado">
                                    {{ __('Estado') }}
                                </label>
                                <select wire:model="estado" id="estado"
                                    class="form-select @error('estado') is-invalid @enderror">
                                    <option value="1">{{ __('Activo') }}</option>
                                    <option value="0">{{ __('Suspendido') }}</option>
                                </select>
                                @error('estado')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Acceso a Grupos (Países) --}}
                            <div class="mb-3">
                                <label class="form-label">
                                    {{ __('Acceso a Grupos') }} <span class="text-danger">*</span>
                                </label>
                                <div class="border rounded p-3 @error('selectedGroups') border-danger @enderror">
                                    @forelse($this->availableGroups as $group)
                                        <div class="form-check mb-2">
                                            <input type="checkbox" 
                                                wire:model="selectedGroups" 
                                                value="{{ $group->id }}"
                                                id="group_{{ $group->id }}"
                                                class="form-check-input">
                                            <label class="form-check-label" for="group_{{ $group->id }}">
                                                <span class="badge bg-label-primary me-1">{{ $group->code }}</span>
                                                {{ $group->business_name ?? $group->trade_name }}
                                                <small class="text-muted">({{ $group->country_code }})</small>
                                            </label>
                                        </div>
                                    @empty
                                        <p class="text-muted mb-0">{{ __('No hay grupos disponibles') }}</p>
                                    @endforelse
                                </div>
                                @error('selectedGroups')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">
                                    {{ __('Selecciona los grupos (países) a los que tendrá acceso este usuario.') }}
                                </small>
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
                                    {{ $isEditing ? __('Actualizar') : __('Crear Usuario') }}
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

    {{-- MODAL: CONFIRMAR ELIMINACIÓN (Soft Delete) --}}
    @if ($showDeleteModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content">
                    <div class="modal-body text-center py-4">
                        <div class="mb-3">
                            <i class="ti tabler-alert-triangle icon-48px text-warning"></i>
                        </div>
                        <h5>{{ __('¿Eliminar usuario?') }}</h5>
                        <p class="text-muted mb-4">
                            {{ __('El usuario será movido a la papelera y podrá ser restaurado posteriormente.') }}
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

    {{-- MODAL: CONFIRMAR RESTAURACIÓN --}}
    @if ($showRestoreModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content">
                    <div class="modal-body text-center py-4">
                        <div class="mb-3">
                            <i class="ti tabler-refresh icon-48px text-success"></i>
                        </div>
                        <h5>{{ __('¿Restaurar usuario?') }}</h5>
                        <p class="text-muted mb-4">
                            {{ __('El usuario será restaurado y podrá volver a acceder al sistema.') }}
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

    {{-- MODAL: CONFIRMAR ELIMINACIÓN PERMANENTE --}}
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
                            {{ __('Esta acción no se puede deshacer. Todos los datos del usuario serán eliminados.') }}
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
