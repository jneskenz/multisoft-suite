<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tenant extends Model
{
    /**
     * La tabla asociada al modelo.
     */
    protected $table = 'core_tenants';

    /**
     * Los atributos que son asignables masivamente.
     */
    protected $fillable = [
        'code',
        'slug',
        'name',
        'legal_name',
        'tax_id',
        'contact_name',
        'contact_email',
        'contact_phone',
        'plan',
        'billing_cycle',
        'max_users',
        'max_group_companies',
        'max_companies',
        'max_locations',
        'modules_enabled',
        'trial_ends_at',
        'subscription_starts_at',
        'subscription_ends_at',
        'logo',
        'favicon',
        'primary_color',
        'status',
    ];

    /**
     * Los atributos que deben ser convertidos a tipos nativos.
     */
    protected function casts(): array
    {
        return [
            'modules_enabled' => 'array',
            'trial_ends_at' => 'datetime',
            'subscription_starts_at' => 'date',
            'subscription_ends_at' => 'date',
            'max_users' => 'integer',
            'max_group_companies' => 'integer',
            'max_companies' => 'integer',
            'max_locations' => 'integer',
        ];
    }

    /**
     * Obtener los grupos empresa del tenant.
     */
    public function groupCompanies(): HasMany
    {
        return $this->hasMany(GroupCompany::class, 'tenant_id');
    }

    /**
     * Obtener los usuarios del tenant.
     */
    public function users(): HasMany
    {
        return $this->hasMany(\App\Models\User::class, 'tenant_id');
    }

    /**
     * Obtener las configuraciones del tenant.
     */
    public function settings(): HasMany
    {
        return $this->hasMany(Setting::class, 'tenant_id');
    }

    /**
     * Verificar si el tenant está activo.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Verificar si el tenant está en período de prueba.
     */
    public function isOnTrial(): bool
    {
        return $this->status === 'trial' && 
               $this->trial_ends_at && 
               $this->trial_ends_at->isFuture();
    }

    /**
     * Verificar si la suscripción está vigente.
     */
    public function hasActiveSubscription(): bool
    {
        if ($this->isOnTrial()) {
            return true;
        }

        return $this->subscription_ends_at === null || 
               $this->subscription_ends_at->isFuture();
    }

    /**
     * Verificar si un módulo está habilitado.
     */
    public function hasModule(string $module): bool
    {
        if (empty($this->modules_enabled)) {
            return false;
        }

        return in_array($module, $this->modules_enabled);
    }

    /**
     * Verificar si puede agregar más usuarios.
     */
    public function canAddUser(): bool
    {
        return $this->users()->count() < $this->max_users;
    }

    /**
     * Verificar si puede agregar más grupos empresa.
     */
    public function canAddGroupCompany(): bool
    {
        return $this->groupCompanies()->count() < $this->max_group_companies;
    }

    /**
     * Scope para filtrar solo tenants activos.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope para filtrar por plan.
     */
    public function scopeByPlan($query, string $plan)
    {
        return $query->where('plan', $plan);
    }
}
