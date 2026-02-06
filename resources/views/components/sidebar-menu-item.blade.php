@php
    $locale = $locale ?? app()->getLocale();
    $group = $group ?? current_group_code() ?? request()->route('group') ?? 'PE';
    $title = is_array($item['title'] ?? null) 
        ? ($item['title'][$locale] ?? $item['title']['en'] ?? $item['key']) 
        : ($item['title'] ?? $item['key']);
    $icon = $item['icon'] ?? 'ti tabler-point';
    $route = $item['route'] ?? null;
    $children = $item['children'] ?? [];
    $hasChildren = !empty($children);
    
    // Determinar si este item está activo
    $isActive = false;
    if ($route && \Route::has($route)) {
        $isActive = request()->routeIs($route . '*');
    }
    
    // Verificar si algún hijo está activo (para abrir el menú padre)
    $hasActiveChild = false;
    if ($hasChildren) {
        foreach ($children as $child) {
            if (isset($child['route']) && \Route::has($child['route'])) {
                if (request()->routeIs($child['route'] . '*')) {
                    $hasActiveChild = true;
                    break;
                }
            }
        }
    }
@endphp

@if ($hasChildren)
    {{-- Item con submenú --}}
    <li class="menu-item {{ $hasActiveChild ? 'active open' : '' }}">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
            <i class="menu-icon tf-icons {{ $icon }}"></i>
            <div>{{ $title }}</div>
        </a>
        <ul class="menu-sub">
            @foreach ($children as $child)
                @include('components.sidebar-menu-item', ['item' => $child, 'locale' => $locale, 'group' => $group])
            @endforeach
        </ul>
    </li>
@else
    {{-- Item simple --}}
    <li class="menu-item {{ $isActive ? 'active' : '' }}">
        @if ($route && \Route::has($route))
            <a href="{{ route($route, ['locale' => $locale, 'group' => $group]) }}" class="menu-link">
                <i class="menu-icon tf-icons {{ $icon }}"></i>
                <div>{{ $title }}</div>
            </a>
        @else
            <a href="javascript:void(0);" class="menu-link disabled">
                <i class="menu-icon tf-icons {{ $icon }}"></i>
                <div>{{ $title }}</div>
                <span class="badge bg-label-secondary ms-auto">Próximamente</span>
            </a>
        @endif
    </li>
@endif
