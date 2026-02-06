<div>
    {{-- Tarjetas de resumen por módulo --}}
    <div class="card-body border-bottom">
        <div class="row g-3">
            @foreach ($this->permissionsPerModule as $module => $count)
                <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                    <div class="card card-border-shadow-{{ $this->getModuleBadgeColor($module) }} h-100 cursor-pointer {{ $moduleFilter === $module ? 'border-2 border-primary' : '' }}"
                        wire:click="filterByModule('{{ $module }}')">
                        <div class="card-body p-3 text-center">
                            <div class="avatar avatar-sm mx-auto mb-2">
                                <span class="avatar-initial rounded bg-label-{{ $this->getModuleBadgeColor($module) }}">
                                    <i class="ti {{ $this->getModuleIcon($module) }}"></i>
                                </span>
                            </div>
                            <h6 class="mb-0 text-uppercase small">{{ $module }}</h6>
                            <span class="badge bg-{{ $this->getModuleBadgeColor($module) }} mt-1">{{ $count }}</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Filtros --}}
    <div class="card-body">
        <div class="row g-3 mb-3">
            {{-- Búsqueda --}}
            <div class="col-12 col-md-5">
                <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="ti tabler-search"></i></span>
                    <input type="text" wire:model.live.debounce.300ms="search" class="form-control"
                        placeholder="{{ __('Buscar permiso por nombre o descripción...') }}">
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
                        <option value="{{ $module }}">{{ $this->getModuleDisplayName($module) }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Por página --}}
            <div class="col-4 col-md-2">
                <select wire:model.live="perPage" class="form-select">
                    <option value="25">25 por página</option>
                    <option value="50">50 por página</option>
                    <option value="100">100 por página</option>
                </select>
            </div>

            {{-- Botón limpiar filtros --}}
            <div class="col-2 col-md-2">
                @if ($search || $moduleFilter)
                    <button wire:click="clearFilters" class="btn btn-outline-secondary w-100"
                        title="{{ __('Limpiar filtros') }}">
                        <i class="ti tabler-filter-off"></i>
                        <span class="d-none d-md-inline ms-1">{{ __('Limpiar') }}</span>
                    </button>
                @endif
            </div>
        </div>

        {{-- Vista agrupada por módulo --}}
        @if (!$moduleFilter)
            <div class="accordion" id="permissionsAccordion">
                @foreach ($this->permissionsByModule as $module => $modulePermissions)
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }}" type="button"
                                data-bs-toggle="collapse" data-bs-target="#perms-{{ $module }}">
                                <span class="avatar avatar-xs me-2">
                                    <span class="avatar-initial rounded bg-label-{{ $this->getModuleBadgeColor($module) }}">
                                        <i class="ti {{ $this->getModuleIcon($module) }} ti-xs"></i>
                                    </span>
                                </span>
                                <span class="fw-medium">{{ $this->getModuleDisplayName($module) }}</span>
                                <span class="badge bg-label-secondary ms-2">{{ $modulePermissions->count() }} {{ __('permisos') }}</span>
                            </button>
                        </h2>
                        <div id="perms-{{ $module }}" 
                            class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}"
                            data-bs-parent="#permissionsAccordion">
                            <div class="accordion-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-sm table-hover mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="width: 250px;">{{ __('Nombre') }}</th>
                                                <th>{{ __('Identificador') }}</th>
                                                <th>{{ __('Descripción') }}</th>
                                                <th class="text-center" style="width: 100px;">{{ __('Roles') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($modulePermissions as $permission)
                                                <tr>
                                                    <td>
                                                        <span class="fw-medium">{{ $permission->display_name }}</span>
                                                    </td>
                                                    <td>
                                                        <code class="text-primary">{{ $permission->name }}</code>
                                                    </td>
                                                    <td>
                                                        <small class="text-muted">{{ $permission->description ?: '-' }}</small>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge bg-label-{{ $permission->roles_count > 0 ? 'info' : 'secondary' }}">
                                                            {{ $permission->roles_count }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            {{-- Vista de tabla cuando hay filtro de módulo --}}
            <div class="table-responsive">
                <table class="table table-hover table-sm align-middle">
                    <thead class="table-light">
                        <tr>
                            <th wire:click="sortBy('display_name')" class="cursor-pointer" style="min-width: 200px;">
                                <div class="d-flex align-items-center">
                                    {{ __('Nombre') }}
                                    @if ($sortField === 'display_name')
                                        <i class="ti tabler-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ms-1"></i>
                                    @endif
                                </div>
                            </th>
                            <th wire:click="sortBy('name')" class="cursor-pointer" style="min-width: 200px;">
                                <div class="d-flex align-items-center">
                                    {{ __('Identificador') }}
                                    @if ($sortField === 'name')
                                        <i class="ti tabler-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ms-1"></i>
                                    @endif
                                </div>
                            </th>
                            <th>{{ __('Descripción') }}</th>
                            <th class="text-center" style="width: 100px;">{{ __('Roles') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($this->permissions as $permission)
                            <tr wire:key="permission-{{ $permission->id }}">
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span class="avatar avatar-xs me-2">
                                            <span class="avatar-initial rounded bg-label-{{ $this->getModuleBadgeColor($permission->module) }}">
                                                <i class="ti {{ $this->getModuleIcon($permission->module) }} ti-xs"></i>
                                            </span>
                                        </span>
                                        <span class="fw-medium">{{ $permission->display_name }}</span>
                                    </div>
                                </td>
                                <td>
                                    <code class="text-primary">{{ $permission->name }}</code>
                                </td>
                                <td>
                                    <small class="text-muted">{{ $permission->description ?: '-' }}</small>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-label-{{ $permission->roles_count > 0 ? 'info' : 'secondary' }}">
                                        <i class="ti tabler-shield me-1"></i>
                                        {{ $permission->roles_count }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="ti tabler-shield-off icon-48px text-muted mb-3"></i>
                                        <h6 class="mb-1">{{ __('No se encontraron permisos') }}</h6>
                                        <p class="text-muted mb-0">
                                            {{ __('Intenta ajustar los filtros de búsqueda') }}
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Paginación --}}
            @if ($this->permissions->hasPages())
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="text-muted small">
                        {{ __('Mostrando') }} {{ $this->permissions->firstItem() }} - {{ $this->permissions->lastItem() }}
                        {{ __('de') }} {{ $this->permissions->total() }} {{ __('permisos') }}
                    </div>
                    {{ $this->permissions->links() }}
                </div>
            @endif
        @endif
    </div>

    {{-- Info footer --}}
    <div class="card-footer">
        <div class="d-flex justify-content-between align-items-center">
            <div class="text-muted small">
                <i class="ti tabler-info-circle me-1"></i>
                {{ __('Los permisos se definen en el seeder del sistema y se asignan a los roles.') }}
            </div>
            <span class="badge bg-primary">
                <i class="ti tabler-shield-check me-1"></i>
                {{ $this->totalPermissions }} {{ __('permisos totales') }}
            </span>
        </div>
    </div>
</div>
