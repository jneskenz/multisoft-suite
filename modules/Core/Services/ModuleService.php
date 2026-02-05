<?php

namespace Modules\Core\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;

class ModuleService
{
    /**
     * Tiempo de caché en segundos (1 hora)
     */
    protected int $cacheTtl = 3600;

    /**
     * Clave de caché para módulos
     */
    protected string $cacheKey = 'multisoft.modules';

    /**
     * Obtener todos los módulos instalados
     */
    public function all(): array
    {
        return Cache::remember($this->cacheKey, $this->cacheTtl, function () {
            return $this->discover();
        });
    }

    /**
     * Obtener módulos habilitados
     */
    public function enabled(): array
    {
        return collect($this->all())
            ->filter(fn ($module) => $module['enabled'] ?? true)
            ->sortBy('order')
            ->values()
            ->toArray();
    }

    /**
     * Obtener módulos accesibles para el usuario actual
     */
    public function accessible(): array
    {
        $user = auth()->user();
        
        if (!$user) {
            return [];
        }

        return collect($this->enabled())
            ->filter(function ($module) use ($user) {
                $permission = "access.{$module['name']}";
                
                // Core siempre accesible para usuarios autenticados
                if ($module['name'] === 'core') {
                    return true;
                }
                
                return Gate::forUser($user)->allows($permission);
            })
            ->values()
            ->toArray();
    }

    /**
     * Obtener un módulo por nombre o alias
     */
    public function find(string $name): ?array
    {
        $name = strtolower($name);
        
        return collect($this->all())
            ->first(fn ($module) => 
                $module['name'] === $name || 
                $module['alias'] === $name
            );
    }

    /**
     * Detectar módulo activo basado en la URL
     */
    public function detectActive($request = null): ?array
    {
        if ($request instanceof \Illuminate\Http\Request) {
            $path = $request->path();
        } else {
            $path = $request ?? request()->path();
        }
        
        $segments = explode('/', trim($path, '/'));
        
        // Ignorar el locale (primer segmento)
        $moduleSegment = $segments[1] ?? null;
        
        if (!$moduleSegment) {
            return $this->find('core');
        }

        // Buscar módulo que coincida con el segmento
        $module = $this->find($moduleSegment);
        
        return $module ?? $this->find('core');
    }

    /**
     * Obtener el menú de un módulo
     */
    public function getMenu(string $moduleName): array
    {
        $cacheKey = "multisoft.menu.{$moduleName}";
        
        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($moduleName) {
            $menuPath = base_path("modules/{$this->normalizeModuleName($moduleName)}/Config/menu.php");
            
            if (File::exists($menuPath)) {
                return require $menuPath;
            }
            
            return ['items' => []];
        });
    }

    /**
     * Obtener items del menú filtrados por permisos
     */
    public function getAccessibleMenu(string $moduleName): array
    {
        $menu = $this->getMenu($moduleName);
        $user = auth()->user();
        
        if (!$user) {
            return ['items' => []];
        }

        $menu['items'] = $this->filterMenuByPermissions($menu['items'] ?? [], $user);
        
        return $menu;
    }

    /**
     * Limpiar caché de módulos
     */
    public function clearCache(): void
    {
        Cache::forget($this->cacheKey);
        
        // Limpiar caché de menús
        foreach ($this->all() as $module) {
            Cache::forget("multisoft.menu.{$module['name']}");
        }
    }

    /**
     * Descubrir módulos desde el filesystem
     */
    protected function discover(): array
    {
        $modulesPath = base_path('modules');
        $modules = [];

        if (!File::isDirectory($modulesPath)) {
            return $modules;
        }

        $directories = File::directories($modulesPath);

        foreach ($directories as $directory) {
            $moduleJsonPath = "{$directory}/module.json";

            if (File::exists($moduleJsonPath)) {
                $moduleData = json_decode(File::get($moduleJsonPath), true);
                
                if ($moduleData && isset($moduleData['name'])) {
                    $modules[] = $this->normalizeModuleData($moduleData, $directory);
                }
            }
        }

        // Ordenar por prioridad/orden
        usort($modules, fn ($a, $b) => ($a['order'] ?? 99) <=> ($b['order'] ?? 99));

        return $modules;
    }

    /**
     * Normalizar datos del módulo
     */
    protected function normalizeModuleData(array $data, string $path): array
    {
        $name = strtolower($data['alias'] ?? $data['name']);
        
        return [
            'name' => $name,
            'alias' => $name,
            'display_name' => $data['display_name'] ?? $data['alias'] ?? ucfirst($name),
            'description' => $data['description'] ?? '',
            'icon' => $data['icon'] ?? 'ti tabler-box',
            'color' => $data['color'] ?? 'secondary',
            'version' => $data['version'] ?? '1.0.0',
            'order' => $data['order'] ?? 99,
            'enabled' => $data['enabled'] ?? true,
            'providers' => $data['providers'] ?? [],
            'requires_context' => $data['requires_context'] ?? false,
            'dependencies' => $data['dependencies'] ?? [],
            'path' => $path,
        ];
    }

    /**
     * Normalizar nombre del módulo para paths
     */
    protected function normalizeModuleName(string $name): string
    {
        return ucfirst(strtolower($name));
    }

    /**
     * Filtrar items del menú por permisos
     */
    protected function filterMenuByPermissions(array $items, $user): array
    {
        return collect($items)
            ->filter(function ($item) use ($user) {
                // Si no tiene permiso definido, es accesible
                if (empty($item['permission'])) {
                    return true;
                }
                
                return Gate::forUser($user)->allows($item['permission']);
            })
            ->map(function ($item) use ($user) {
                // Filtrar hijos recursivamente
                if (!empty($item['children'])) {
                    $item['children'] = $this->filterMenuByPermissions($item['children'], $user);
                    
                    // Si no quedan hijos, ocultar el padre
                    if (empty($item['children'])) {
                        return null;
                    }
                }
                
                return $item;
            })
            ->filter()
            ->values()
            ->toArray();
    }
}
