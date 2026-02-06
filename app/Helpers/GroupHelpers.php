<?php

use Modules\Core\Models\GroupCompany;

if (! function_exists('current_group')) {
    /**
     * Obtiene el grupo de empresas actual.
     *
     * @return \Modules\Core\Models\GroupCompany|null
     */
    function current_group(): ?GroupCompany
    {
        // Primero intentar desde el singleton
        if (app()->bound('current_group')) {
            return app('current_group');
        }

        // Si no está en singleton, recuperar desde sesión
        $groupId = session('current_group_id');

        if (! $groupId) {
            return null;
        }

        $group = GroupCompany::find($groupId);

        // Cachear como singleton para futuras llamadas
        if ($group) {
            app()->instance('current_group', $group);
        }

        return $group;
    }
}

if (! function_exists('current_group_code')) {
    /**
     * Obtiene el código del grupo actual.
     *
     * @return string|null
     */
    function current_group_code(): ?string
    {
        $group = current_group();

        return $group?->code ?? session('current_group_code');
    }
}

if (! function_exists('group_route')) {
    /**
     * Genera una URL para una ruta nombrada incluyendo locale y grupo.
     *
     * Simplifica la generación de rutas reemplazando:
     *   route('core.users.index', ['locale' => 'es', 'group' => 'PE'])
     * por:
     *   group_route('core.users.index')
     *
     * @param  string  $name       Nombre de la ruta
     * @param  array   $parameters Parámetros adicionales
     * @param  bool    $absolute   Si genera URL absoluta
     * @return string
     */
    function group_route(string $name, array $parameters = [], bool $absolute = true): string
    {
        // Agregar locale si no está especificado
        if (! isset($parameters['locale'])) {
            $parameters['locale'] = app()->getLocale();
        }

        // Agregar grupo si no está especificado
        if (! isset($parameters['group'])) {
            $parameters['group'] = current_group_code() ?? session('last_group_code', 'PE');
        }

        return route($name, $parameters, $absolute);
    }
}

if (! function_exists('switch_group_url')) {
    /**
     * Genera la URL actual pero con otro grupo.
     *
     * Útil para selectores de grupo que permiten cambiar entre
     * operaciones de diferentes países.
     *
     * @param  string  $groupCode  Código del nuevo grupo (ej: 'PE', 'EC')
     * @return string
     */
    function switch_group_url(string $groupCode): string
    {
        $currentUrl = request()->path();
        $segments = explode('/', $currentUrl);

        // Estructura esperada: {locale}/{group}/...
        if (count($segments) >= 2) {
            $segments[1] = strtoupper($groupCode);
        }

        return '/' . implode('/', $segments);
    }
}

if (! function_exists('switch_locale_url')) {
    /**
     * Genera la URL actual pero con otro locale.
     *
     * @param  string  $locale  Código del locale (ej: 'es', 'en')
     * @return string
     */
    function switch_locale_url(string $locale): string
    {
        $currentUrl = request()->path();
        $segments = explode('/', $currentUrl);

        // Estructura esperada: {locale}/{group}/...
        if (count($segments) >= 1) {
            $segments[0] = strtolower($locale);
        }

        return '/' . implode('/', $segments);
    }
}

if (! function_exists('user_groups')) {
    /**
     * Obtiene los grupos a los que tiene acceso el usuario autenticado.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    function user_groups()
    {
        if (! auth()->check()) {
            return collect();
        }

        return auth()->user()->group_companies;
    }
}

if (! function_exists('can_access_group')) {
    /**
     * Verifica si el usuario puede acceder a un grupo específico.
     *
     * @param  string  $groupCode
     * @return bool
     */
    function can_access_group(string $groupCode): bool
    {
        if (! auth()->check()) {
            return false;
        }

        return auth()->user()->hasAccessToGroup($groupCode);
    }
}
