@php
    // Usar datos del ViewComposer (ModulesComposer)
    $modules = $accessibleModules ?? [];
    $currentModule = $activeModule ?? null;
    $locale = app()->getLocale();
    $group = current_group_code() ?? request()->route('group') ?? 'PE';
    
    // Obtener info del módulo activo para el botón (null si no hay módulo activo)
    $hasActiveModule = $currentModule !== null;
    $activeModuleName = $currentModule['alias'] ?? null;
    $activeIcon = $hasActiveModule ? ($currentModule['icon'] ?? 'ti tabler-apps') : 'ti tabler-apps';
    $activeColor = $hasActiveModule ? ($currentModule['color'] ?? 'primary') : 'primary';
    $activeDisplayName = $hasActiveModule 
        ? (is_array($currentModule['display_name'] ?? null) 
            ? ($currentModule['display_name'][$locale] ?? $currentModule['display_name']['en'] ?? 'Módulos')
            : ($currentModule['display_name'] ?? 'Módulos'))
        : 'Módulos';
@endphp

<li class="nav-item dropdown-shortcuts navbar-dropdown dropdown">
    
    <a class="nav-link dropdown-toggle hide-arrow btn btn-text-{{ $activeColor }} rounded-pill px-2 px-md-3"
        href="javascript:void(0);" 
        data-bs-toggle="dropdown" 
        aria-expanded="false">
        <i class="icon-base {{ $activeIcon }} icon-22px text-heading me-0 me-md-1"></i>
        <span class="d-none d-md-inline">{{ $activeDisplayName }}</span>
    </a>
    <div class="dropdown-menu dropdown-menu-start p-0" aria-labelledby="nav-apps-text">
        <div class="dropdown-menu-header border-bottom">
            <div class="dropdown-header d-flex align-items-center">
                <h6 class="mb-0 me-auto"> {{ $group}} · Multisoft Suite</h6>
                <span class="badge bg-label-primary">{{ count($modules) }} módulos</span>
            </div>
        </div>
        <div class="dropdown-shortcuts-list scrollable-container">
            <div class="row row-bordered overflow-visible g-0">
                <div class="col-12 text-center p-2 border-bottom">
                    <small class="text-muted">Seleccione una aplicación</small>
                </div>
                
                @forelse ($modules as $module)
                    @php
                        $moduleName = $module['alias'] ?? $module['name'] ?? '';
                        $isActive = $moduleName === $activeModuleName;
                        $displayName = is_array($module['display_name'] ?? null) 
                            ? ($module['display_name'][$locale] ?? $module['display_name']['en'] ?? $moduleName) 
                            : ($module['display_name'] ?? $moduleName);
                        $description = is_array($module['description'] ?? null) 
                            ? ($module['description'][$locale] ?? $module['description']['en'] ?? '') 
                            : ($module['description'] ?? '');
                        $icon = $module['icon'] ?? 'ti tabler-apps';
                        $color = $module['color'] ?? 'secondary';
                        $requiresContext = $module['requires_context'] ?? false;

                        // Generar URL del módulo con locale y grupo
                        $moduleUrl = url("/{$locale}/{$group}/{$moduleName}");
                    @endphp
                    <div class="dropdown-shortcuts-item col-6 px-2 py-3 {{ $isActive ? 'bg-light' : '' }}">
                        <span class="dropdown-shortcuts-icon bg-label-{{ $color }} rounded mb-2">
                            <i class="icon-base {{ $icon }} icon-26px"></i>
                        </span>
                        <a href="{{ $moduleUrl }}" class="stretched-link fw-medium {{ $isActive ? 'text-primary' : 'text-heading' }}">
                            {{ $displayName }}
                            @if ($isActive)
                                <i class="ti tabler-check ms-1 text-success"></i>
                            @endif
                        </a>
                        <small class="text-muted d-block">{{ Str::limit($description, 30) }}</small>
                        @if ($requiresContext)
                            <span class="badge bg-label-warning mt-1" style="font-size: 0.65rem;">
                                <i class="ti tabler-building-store me-1"></i>Empresa
                            </span>
                        @endif
                    </div>
                @empty
                    <div class="col-12 text-center p-4">
                        <i class="ti tabler-lock-off icon-48px text-muted mb-2"></i>
                        <p class="text-muted mb-0">No tienes acceso a ningún módulo</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</li>
