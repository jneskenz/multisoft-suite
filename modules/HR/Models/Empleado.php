<?php

namespace Modules\HR\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Modules\HR\Enums\EstadoContrato;
use Modules\Core\Models\Company;
use Modules\Core\Models\GroupCompany;
use Modules\Core\Models\Location;
use Modules\Core\Models\Tenant;

class Empleado extends Model
{
    use SoftDeletes;

    protected $table = 'hr_empleados';

    protected $fillable = [
        'user_id',
        'nombre',
        'email',
        'documento_tipo',
        'documento_numero',
        'telefono',
        'tenant_id',
        'group_company_id',
        'company_id',
        'location_id',
        'codigo_empleado',
        'cargo',
        'fecha_ingreso',
        'fecha_cese',
        'estado',
    ];

    protected $casts = [
        'estado' => 'integer',
        'fecha_ingreso' => 'date',
        'fecha_cese' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Estados
    const ESTADO_SUSPENDIDO = 0;
    const ESTADO_ACTIVO = 1;
    const ESTADO_CESADO = 2;

    /**
     * Relación opcional con usuario del sistema
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación con tenant
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Relación con grupo empresa
     */
    public function groupCompany(): BelongsTo
    {
        return $this->belongsTo(GroupCompany::class);
    }

    /**
     * Relación con empresa
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Relación con local (opcional)
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    /**
     * Todos los contratos del empleado
     */
    public function contratos(): HasMany
    {
        return $this->hasMany(Contrato::class, 'empleado_id');
    }

    /**
     * Contrato vigente (firmado y sin terminar)
     */
    public function contratoVigente(): HasOne
    {
        return $this->hasOne(Contrato::class, 'empleado_id')
            ->where('estado_contrato', EstadoContrato::FIRMADO)
            ->whereNull('fecha_terminacion')
            ->latest('fecha_inicio');
    }

    /**
     * Scope para filtrar por grupo actual
     */
    public function scopeForCurrentGroup($query)
    {
        if ($groupId = session('group_company_id')) {
            return $query->where('group_company_id', $groupId);
        }
        return $query;
    }

    /**
     * Scope para filtrar por empresa
     */
    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    /**
     * Scope para filtrar por local
     */
    public function scopeForLocation($query, $locationId)
    {
        return $query->where('location_id', $locationId);
    }

    /**
     * Scope para empleados activos
     */
    public function scopeActive($query)
    {
        return $query->where('estado', self::ESTADO_ACTIVO);
    }

    /**
     * Scope para empleados suspendidos
     */
    public function scopeSuspended($query)
    {
        return $query->where('estado', self::ESTADO_SUSPENDIDO);
    }

    /**
     * Scope para empleados cesados
     */
    public function scopeCesado($query)
    {
        return $query->where('estado', self::ESTADO_CESADO);
    }

    /**
     * Verificar si el empleado está activo
     */
    public function isActivo(): bool
    {
        return $this->estado === self::ESTADO_ACTIVO;
    }

    /**
     * Verificar si el empleado está suspendido
     */
    public function isSuspendido(): bool
    {
        return $this->estado === self::ESTADO_SUSPENDIDO;
    }

    /**
     * Verificar si el empleado está cesado
     */
    public function isCesado(): bool
    {
        return $this->estado === self::ESTADO_CESADO;
    }

    /**
     * Verificar si el empleado tiene acceso al sistema
     */
    public function hasSystemAccess(): bool
    {
        return !is_null($this->user_id);
    }

    /**
     * Obtener el label del estado
     */
    public function getEstadoLabelAttribute(): string
    {
        return match ($this->estado) {
            self::ESTADO_ACTIVO => 'Activo',
            self::ESTADO_SUSPENDIDO => 'Suspendido',
            self::ESTADO_CESADO => 'Cesado',
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
            self::ESTADO_CESADO => 'bg-label-danger',
            default => 'bg-label-secondary',
        };
    }
}
