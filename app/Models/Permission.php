<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'display_name',
        'module',
        'description',
    ];

    /**
     * Roles que tienen este permiso
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_permission')
            ->withTimestamps();
    }

    /**
     * Obtener permisos por módulo
     */
    public static function byModule(string $module)
    {
        return static::where('module', $module)->get();
    }

    /**
     * Obtener todos los módulos disponibles
     */
    public static function modules(): array
    {
        return ['core', 'erp', 'hr', 'crm', 'fms', 'reports'];
    }
}
