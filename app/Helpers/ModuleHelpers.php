<?php

if (! function_exists('module_path')) {
    /**
     * Obtener la ruta de un módulo.
     *
     * @param  string  $name  Nombre del módulo
     * @param  string  $path  Ruta adicional dentro del módulo
     * @return string
     */
    function module_path(string $name, string $path = ''): string
    {
        $modulePath = base_path('modules/' . $name);

        return $path ? $modulePath . '/' . ltrim($path, '/') : $modulePath;
    }
}

if (! function_exists('module_url')) {
    /**
     * Generar URL para un módulo.
     *
     * @param  string  $module  Nombre del módulo
     * @param  string  $path    Ruta dentro del módulo
     * @return string
     */
    function module_url(string $module, string $path = ''): string
    {
        $locale = app()->getLocale();
        $base = "/{$locale}/" . strtolower($module);

        return $path ? $base . '/' . ltrim($path, '/') : $base;
    }
}

if (! function_exists('active_module')) {
    /**
     * Obtener el módulo activo basándose en la URL actual.
     *
     * @return string|null
     */
    function active_module(): ?string
    {
        $segments = request()->segments();
        
        // El primer segmento es el locale (es/en)
        // El segundo segmento puede ser el módulo
        if (count($segments) >= 2) {
            $potentialModule = ucfirst($segments[1]);
            $availableModules = ['Core', 'Partners', 'ERP', 'FMS', 'HR', 'CRM', 'Reports'];
            
            if (in_array($potentialModule, $availableModules)) {
                return $potentialModule;
            }
        }
        
        return 'Core'; // Módulo por defecto
    }
}

if (! function_exists('is_module_enabled')) {
    /**
     * Verificar si un módulo está habilitado.
     *
     * @param  string  $module  Nombre del módulo
     * @return bool
     */
    function is_module_enabled(string $module): bool
    {
        $modulePath = module_path($module);
        return is_dir($modulePath) && file_exists($modulePath . '/module.json');
    }
}
