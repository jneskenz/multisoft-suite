<?php

namespace Modules\Core\Models;

use Spatie\Permission\Models\Role as SpatieRole;

/**
 * Modelo Role extendido de Spatie Permission.
 *
 * Este modelo permite agregar funcionalidad adicional
 * manteniendo toda la potencia de Spatie Permission.
 *
 * @property int $id
 * @property string $name
 * @property string $guard_name
 * @property string|null $display_name
 * @property string|null $description
 * @property bool $is_system
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Role extends SpatieRole
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
        'description',
        'is_system',
    ];

    /**
     * Cast de atributos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_system' => 'boolean',
    ];

    /**
     * Roles del sistema que no pueden ser eliminados.
     *
     * @var array<string>
     */
    public const SYSTEM_ROLES = [
        'superadmin',
        'admin',
        'user',
    ];

    /**
     * Verificar si es un rol del sistema.
     */
    public function isSystemRole(): bool
    {
        return $this->is_system || in_array($this->name, self::SYSTEM_ROLES);
    }

    /**
     * Verificar si el rol tiene acceso a un módulo.
     */
    public function hasModuleAccess(string $module): bool
    {
        return $this->hasPermissionTo("access.{$module}");
    }

    /**
     * Obtener el nombre para mostrar.
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->attributes['display_name'] ?? ucfirst($this->name);
    }

    /**
     * Scope para roles activos (no del sistema).
     */
    public function scopeCustom($query)
    {
        return $query->where('is_system', false);
    }

    /**
     * Scope para roles del sistema.
     */
    public function scopeSystem($query)
    {
        return $query->where('is_system', true);
    }
}
