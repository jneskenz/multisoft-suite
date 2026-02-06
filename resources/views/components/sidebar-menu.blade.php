@php
    use Modules\Core\Services\ModuleService;
    
    $locale = app()->getLocale();
    $group = current_group_code() ?? request()->route('group') ?? 'PE';
    $currentModule = $activeModule ?? null;
    $hasModule = $currentModule !== null;
    
    // Obtener el menú filtrado por permisos si hay módulo activo
    $menu = null;
    if ($hasModule && app()->bound(ModuleService::class)) {
        $moduleService = app(ModuleService::class);
        $menu = $moduleService->getAccessibleMenu($currentModule['alias']);
    }
@endphp

<ul class="menu-inner py-1">
    @if (!$hasModule)
        {{-- Menú para Welcome (sin módulo activo) --}}
        <li class="menu-item {{ request()->routeIs('welcome') ? 'active' : '' }}">
            <a href="{{ url($locale . '/' . $group . '/welcome') }}" class="menu-link">
                <i class="menu-icon tf-icons ti tabler-home"></i>
                <div>{{ __('Inicio') }}</div>
            </a>
        </li>
        
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">{{ __('Aplicaciones') }}</span>
        </li>
        
        <li class="menu-item">
            <a href="javascript:void(0);" class="menu-link" data-bs-toggle="dropdown">
                <i class="menu-icon tf-icons ti tabler-apps"></i>
                <div>{{ __('Seleccione un módulo') }}</div>
            </a>
        </li>
    @else
        {{-- Menú del módulo activo --}}
        @php
            $moduleDisplayName = is_array($currentModule['display_name'] ?? null)
                ? ($currentModule['display_name'][$locale] ?? $currentModule['display_name']['en'] ?? $currentModule['alias'])
                : ($currentModule['display_name'] ?? $currentModule['alias']);
            $moduleIcon = $currentModule['icon'] ?? 'ti tabler-box';
            $moduleColor = $currentModule['color'] ?? 'primary';
        @endphp
        
        {{-- Header del módulo --}}
        <li class="menu-header small text-uppercase">
            <div class="menu-header-text d-flex align-items-center justify-content-between">
                <span class="d-flex align-items-center">
                    {{-- <i class="{{ $moduleIcon }} me-2"></i> --}}
                    {{ $moduleDisplayName }}
                </span>
                <span> {{ $group }} </span>
            </div>
        </li>
        
        {{-- Items del menú del módulo --}}
        @if ($menu && !empty($menu['items']))
            @foreach ($menu['items'] as $item)
                @include('components.sidebar-menu-item', ['item' => $item, 'locale' => $locale, 'group' => $group])
            @endforeach
        @else
            {{-- Fallback: Dashboard del módulo --}}
            <li class="menu-item active">
                <a href="{{ url($locale . '/' . $group . '/' . $currentModule['alias']) }}" class="menu-link">
                    <i class="menu-icon tf-icons ti tabler-smart-home"></i>
                    <div>Dashboard</div>
                </a>
            </li>
        @endif
        
        {{-- Separador y link para volver --}}
        <li class="menu-header small text-uppercase mt-4">
            <span class="menu-header-text">{{ __('Navegación') }}</span>
        </li>
        
        <li class="menu-item">
            <a href="{{ url($locale . '/' . $group . '/welcome') }}" class="menu-link">
                <i class="menu-icon tf-icons ti tabler-arrow-left"></i>
                <div>{{ __('Volver al inicio') }}</div>
            </a>
        </li>
    @endif
</ul>
