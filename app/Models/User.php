<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\GroupCompany;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes, HasRoles;

    /**
     * Estados del usuario
     */
    const ESTADO_SUSPENDIDO = 0;
    const ESTADO_ACTIVO = 1;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'tenant_id',
        'name',
        'email',
        'password',
        'estado',
        'avatar',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'estado' => 'integer',
        ];
    }

    /**
     * Tenant al que pertenece el usuario
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    /**
     * Relación many-to-many con grupos a través de la tabla pivot.
     * Define qué grupos puede acceder el usuario.
     */
    public function groupCompanies(): BelongsToMany
    {
        return $this->belongsToMany(
            GroupCompany::class,
            'core_user_group_access',
            'user_id',
            'group_company_id'
        )->withTimestamps();
    }

    /**
     * Obtener los grupos empresa a los que tiene acceso el usuario.
     * Usa la tabla pivot para control granular.
     */
    public function getAccessibleGroupsAttribute()
    {
        // Super-admin sin tenant: acceso a todos los grupos activos
        if (!$this->tenant_id) {
            return GroupCompany::active()->get();
        }

        // Usuarios normales: solo grupos asignados en la tabla pivot
        return $this->groupCompanies()->active()->get();
    }

    /**
     * Verificar si el usuario tiene acceso a un grupo específico por código
     */
    public function hasAccessToGroup(string $groupCode): bool
    {
        // Super-admin sin tenant tiene acceso a todo
        if (!$this->tenant_id) {
            return true;
        }

        // Verificar en la tabla pivot
        return $this->groupCompanies()
            ->where('code', $groupCode)
            ->active()
            ->exists();
    }

    /**
     * Verificar si el usuario tiene acceso a un grupo por ID
     */
    public function hasAccessToGroupById(int $groupId): bool
    {
        if (!$this->tenant_id) {
            return true;
        }

        return $this->groupCompanies()
            ->where('core_group_companies.id', $groupId)
            ->active()
            ->exists();
    }

    /**
     * Asignar acceso a grupos específicos.
     * Reemplaza los grupos actuales con los nuevos.
     * 
     * @param array $groupIds Array de IDs de grupos
     */
    public function syncGroupAccess(array $groupIds): void
    {
        $this->groupCompanies()->sync($groupIds);
    }

    /**
     * Agregar acceso a un grupo.
     */
    public function grantGroupAccess(int $groupId): void
    {
        $this->groupCompanies()->syncWithoutDetaching([$groupId]);
    }

    /**
     * Revocar acceso a un grupo.
     */
    public function revokeGroupAccess(int $groupId): void
    {
        $this->groupCompanies()->detach($groupId);
    }

    /**
     * Obtener IDs de grupos a los que tiene acceso
     */
    public function getGroupAccessIds(): array
    {
        if (!$this->tenant_id) {
            return GroupCompany::active()->pluck('id')->toArray();
        }
        
        return $this->groupCompanies()->pluck('core_group_companies.id')->toArray();
    }

    /**
     * Verificar si el usuario tiene acceso a un módulo
     * Usa Spatie Permission - el permiso debe ser 'access.{module}'
     */
    public function hasModuleAccess(string $module): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }
        
        return $this->hasPermissionTo("access.{$module}");
    }

    /**
     * Verificar si el usuario es super admin
     */
    public function isSuperAdmin(): bool
    {
        return $this->hasRole('superadmin');
    }

    /**
     * Verificar si el usuario es administrador
     */
    public function isAdmin(): bool
    {
        return $this->hasAnyRole(['superadmin', 'admin']);
    }

    /**
     * Verificar si el usuario está activo
     */
    public function isActivo(): bool
    {
        return $this->estado === self::ESTADO_ACTIVO;
    }

    /**
     * Verificar si el usuario está suspendido
     */
    public function isSuspendido(): bool
    {
        return $this->estado === self::ESTADO_SUSPENDIDO;
    }

    /**
     * Obtener el label del estado
     */
    public function getEstadoLabelAttribute(): string
    {
        return match ($this->estado) {
            self::ESTADO_ACTIVO => 'Activo',
            self::ESTADO_SUSPENDIDO => 'Suspendido',
            default => 'Desconocido',
        };
    }

    /**
     * Obtener el color del badge del estado
     */
    public function getEstadoBadgeClassAttribute(): string
    {
        return match ($this->estado) {
            self::ESTADO_ACTIVO => 'bg-label-success',
            self::ESTADO_SUSPENDIDO => 'bg-label-warning',
            default => 'bg-label-secondary',
        };
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }
}
