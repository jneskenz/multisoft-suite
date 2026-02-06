<?php

namespace Modules\Core\Models;

use Spatie\Permission\Models\Permission as SpatiePermission;

/**
 * Modelo Permission extendido de Spatie Permission.
 *
 * Este modelo permite agregar funcionalidad adicional
 * manteniendo toda la potencia de Spatie Permission.
 *
 * @property int $id
 * @property string $name
 * @property string $guard_name
 * @property string|null $display_name
 * @property string|null $module
 * @property string|null $description
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Permission extends SpatiePermission
{
    /**
     * Atributos adicionales fillable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'guard_name',
        'display_name',
        'module',
        'description',
    ];

    /**
     * Módulos disponibles.
     *
     * @var array<string>
     */
    public const MODULES = [
        'core',
        'erp',
        'hr',
        'crm',
        'fms',
        'reports',
        'partners',
    ];

    /**
     * Acciones CRUD estándar.
     *
     * @var array<string>
     */
    public const ACTIONS = [
        'view',
        'create',
        'edit',
        'delete',
        'export',
        'import',
    ];

    /**
     * Obtener el nombre para mostrar.
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->attributes['display_name'] ?? $this->generateDisplayName();
    }

    /**
     * Generar nombre para mostrar basado en el nombre del permiso.
     */
    protected function generateDisplayName(): string
    {
        // Convertir "core.users.create" a "Core: Crear Usuarios"
        $parts = explode('.', $this->name);

        if (count($parts) >= 3) {
            $module = ucfirst($parts[0]);
            $resource = ucfirst($parts[1]);
            $action = $this->translateAction($parts[2]);

            return "{$module}: {$action} {$resource}";
        }

        return ucfirst(str_replace(['.', '_'], ' ', $this->name));
    }

    /**
     * Traducir acción a español.
     */
    protected function translateAction(string $action): string
    {
        return match ($action) {
            'view' => 'Ver',
            'create' => 'Crear',
            'edit' => 'Editar',
            'delete' => 'Eliminar',
            'export' => 'Exportar',
            'import' => 'Importar',
            'access' => 'Acceder',
            default => ucfirst($action),
        };
    }

    /**
     * Scope para permisos de un módulo específico.
     */
    public function scopeForModule($query, string $module)
    {
        return $query->where('module', $module)
            ->orWhere('name', 'like', "{$module}.%");
    }

    /**
     * Scope para permisos de acceso a módulos.
     */
    public function scopeModuleAccess($query)
    {
        return $query->where('name', 'like', 'access.%');
    }

    /**
     * Obtener permisos agrupados por módulo.
     */
    public static function groupedByModule(): array
    {
        return static::all()
            ->groupBy(function ($permission) {
                return $permission->module ?? explode('.', $permission->name)[0] ?? 'other';
            })
            ->toArray();
    }
}
