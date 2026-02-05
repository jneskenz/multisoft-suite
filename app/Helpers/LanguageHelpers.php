<?php

if (! function_exists('multilang_route')) {
    /**
     * Generar URL con locale específico
     *
     * @param  string  $name Nombre de la ruta
     * @param  string  $locale Código de idioma (es, en)
     * @param  array  $parameters Parámetros adicionales
     * @return string
     */
    function multilang_route(string $name, string $locale, array $parameters = []): string
    {
        return route($name, array_merge(['locale' => $locale], $parameters));
    }
}

if (! function_exists('current_route_multilang')) {
    /**
     * Ruta actual con otro idioma
     *
     * @param  string  $locale Código de idioma (es, en)
     * @return string
     */
    function current_route_multilang(string $locale): string
    {
        $routeName = request()->route()->getName();
        $parameters = request()->route()->parameters();
        $parameters['locale'] = $locale;

        return route($routeName, $parameters);
    }
}

if (! function_exists('supported_locales')) {
    /**
     * Array de idiomas soportados
     *
     * @return array
     */
    function supported_locales(): array
    {
        return ['es', 'en'];
    }
}

if (! function_exists('locale_name')) {
    /**
     * Nombre display del idioma
     *
     * @param  string  $locale Código de idioma
     * @return string
     */
    function locale_name(string $locale): string
    {
        return match ($locale) {
            'es' => 'Español',
            'en' => 'English',
            default => $locale,
        };
    }
}

if (! function_exists('locale_flag')) {
    /**
     * Emoji bandera del idioma
     *
     * @param  string  $locale Código de idioma
     * @return string
     */
    function locale_flag(string $locale): string
    {
        return match ($locale) {
            'es' => '🇪🇸',
            'en' => '🇺🇸',
            default => '🌐',
        };
    }
}
