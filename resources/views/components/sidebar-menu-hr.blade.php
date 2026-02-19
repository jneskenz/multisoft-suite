@php
    use Illuminate\Support\Str;
    use Modules\Core\Services\ModuleService;

    $locale = app()->getLocale();
    $group = current_group_code() ?? request()->route('group') ?? 'PE';

    $menu = ['items' => []];
    if (app()->bound(ModuleService::class)) {
        $menu = app(ModuleService::class)->getAccessibleMenu('hr');
    }

    $items = $menu['items'] ?? [];
    $currentRouteName = request()->route()?->getName();

    $resolveTitle = function (array $item) use ($locale): string {
        if (is_array($item['title'] ?? null)) {
            return $item['title'][$locale] ?? ($item['title']['en'] ?? ($item['key'] ?? ''));
        }

        return $item['title'] ?? ($item['key'] ?? '');
    };

    $matchesRoute = function (?string $routeName) use ($currentRouteName): bool {
        if (!$routeName || !$currentRouteName || !Route::has($routeName)) {
            return false;
        }

        return $currentRouteName === $routeName || Str::startsWith($currentRouteName, $routeName . '.');
    };

    $nodeIsActive = function (array $node) use (&$nodeIsActive, $matchesRoute): bool {
        if ($matchesRoute($node['route'] ?? null)) {
            return true;
        }

        foreach (($node['children'] ?? []) as $childNode) {
            if ($nodeIsActive($childNode)) {
                return true;
            }
        }

        return false;
    };

    $renderNodes = function (array $nodes) use (&$renderNodes, $resolveTitle, $nodeIsActive, $locale, $group): string {
        $html = '';

        foreach ($nodes as $node) {
            $title = e($resolveTitle($node));
            $icon = e($node['icon'] ?? 'ti tabler-point');
            $children = $node['children'] ?? [];
            $hasChildren = !empty($children);
            $routeName = $node['route'] ?? null;
            $isActive = $nodeIsActive($node);

            $liClass = 'menu-item' . ($isActive ? ' active' : '') . ($hasChildren && $isActive ? ' open' : '');

            if ($hasChildren) {
                $html .= '<li class="' . $liClass . '">';
                $html .= '<a href="javascript:void(0);" class="menu-link menu-toggle">';
                $html .= '<i class="menu-icon tf-icons ' . $icon . '"></i>';
                $html .= '<div>' . $title . '</div>';
                $html .= '</a>';
                $html .= '<ul class="menu-sub">';
                $html .= $renderNodes($children);
                $html .= '</ul>';
                $html .= '</li>';
                continue;
            }

            if ($routeName && Route::has($routeName)) {
                $url = e(route($routeName, ['locale' => $locale, 'group' => $group]));
                $html .= '<li class="' . $liClass . '">';
                $html .= '<a href="' . $url . '" class="menu-link">';
                $html .= '<i class="menu-icon tf-icons ' . $icon . '"></i>';
                $html .= '<div>' . $title . '</div>';
                $html .= '</a>';
                $html .= '</li>';
                continue;
            }

            $html .= '<li class="' . $liClass . '">';
            $html .= '<a href="javascript:void(0);" class="menu-link disabled">';
            $html .= '<i class="menu-icon tf-icons ' . $icon . '"></i>';
            $html .= '<div title="' . $title . '">' . $title . '</div>';
            $html .= '<span class="badge bg-label-secondary ms-auto" title="Próximamente">Prox.</span>';
            $html .= '</a>';
            $html .= '</li>';
        }

        return $html;
    };
@endphp

<ul class="menu-inner py-1">
    @if (!empty($items))
        {!! $renderNodes($items) !!}
    @else
        <li class="menu-item active">
            <a href="{{ url($locale . '/' . $group . '/hr') }}" class="menu-link">
                <i class="menu-icon tf-icons ti tabler-smart-home"></i>
                <div>Dashboard</div>
            </a>
        </li>
    @endif
</ul>