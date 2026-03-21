<?php

namespace Modules\HR\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Core\Models\Company;
use Modules\Core\Models\GroupCompany;
use Modules\Core\Models\Location;
use Modules\Core\Models\Tenant;
use Modules\HR\Enums\EstadoContrato;

class Empleado extends Model
{
    use SoftDeletes;

    protected $table = 'hr_empleados';

    protected $fillable = [
        'user_id',
        'nombres',
        'apellidos',
        'nombre', // Legacy full name field kept for compatibility.
        'email',
        'documento_tipo',
        'documento_numero',
        'telefono',
        'direccion',
        'estado_civil',
        'genero',
        'fecha_nacimiento',
        'tenant_id',
        'group_company_id',
        'company_id',
        'location_id',
        'codigo_empleado',
        'fecha_ingreso',
        'fecha_cese',
        'estado',
    ];

    protected $casts = [
        'estado' => 'integer',
        'fecha_nacimiento' => 'date',
        'fecha_ingreso' => 'date',
        'fecha_cese' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public const ESTADO_SUSPENDIDO = 0;
    public const ESTADO_ACTIVO = 1;
    public const ESTADO_CESADO = 2;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function groupCompany(): BelongsTo
    {
        return $this->belongsTo(GroupCompany::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function contratos(): HasMany
    {
        return $this->hasMany(Contrato::class, 'empleado_id');
    }

    public function contratoVigente(): HasOne
    {
        return $this->hasOne(Contrato::class, 'empleado_id')
            ->where('estado_contrato', EstadoContrato::FIRMADO)
            ->whereNull('fecha_terminacion')
            ->latest('fecha_inicio');
    }

    public function scopeForCurrentGroup($query)
    {
        if ($groupId = session('group_company_id')) {
            return $query->where('group_company_id', $groupId);
        }

        return $query;
    }

    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeForLocation($query, $locationId)
    {
        return $query->where('location_id', $locationId);
    }

    public function scopeActive($query)
    {
        return $query->where('estado', self::ESTADO_ACTIVO);
    }

    public function scopeSuspended($query)
    {
        return $query->where('estado', self::ESTADO_SUSPENDIDO);
    }

    public function scopeCesado($query)
    {
        return $query->where('estado', self::ESTADO_CESADO);
    }

    public function isActivo(): bool
    {
        return $this->estado === self::ESTADO_ACTIVO;
    }

    public function isSuspendido(): bool
    {
        return $this->estado === self::ESTADO_SUSPENDIDO;
    }

    public function isCesado(): bool
    {
        return $this->estado === self::ESTADO_CESADO;
    }

    public function hasSystemAccess(): bool
    {
        return !is_null($this->user_id);
    }

    public function getNombreCompletoAttribute(): string
    {
        $full = trim(
            implode(' ', array_filter([
                $this->nombres,
                $this->apellidos,
            ]))
        );

        if ($full !== '') {
            return $full;
        }

        return (string) ($this->attributes['nombre'] ?? '');
    }

    public function getEstadoLabelAttribute(): string
    {
        return match ($this->estado) {
            self::ESTADO_ACTIVO => 'Activo',
            self::ESTADO_SUSPENDIDO => 'Suspendido',
            self::ESTADO_CESADO => 'Cesado',
            default => 'Desconocido',
        };
    }

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
