<?php

namespace Modules\ERP\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Partners\Models\Persona;

class OrdenTrabajo extends Model
{
    use SoftDeletes;

    protected $table = 'erp_ordenes_trabajo';

    /**
     * @var array<int,string>
     */
    protected $fillable = [
        'tenant_id',
        'group_company_id',
        'ticket_id',
        'receta_id',
        'paciente_id',
        'numero_ot',
        'fecha_orden',
        'fecha_prometida',
        'tipo_orden',
        'estado_ot',
        'prioridad',
        'local_id',
        'almacen_id',
        'observaciones',
        'indicaciones_entrega',
        'estado',
        'created_by',
        'updated_by',
    ];

    /**
     * @var array<string,string>
     */
    protected $casts = [
        'fecha_orden' => 'datetime',
        'fecha_prometida' => 'datetime',
        'estado' => 'boolean',
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class, 'ticket_id');
    }

    public function receta(): BelongsTo
    {
        return $this->belongsTo(Receta::class, 'receta_id');
    }

    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Persona::class, 'paciente_id');
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(OrdenTrabajoDetalle::class, 'orden_trabajo_id')->orderBy('secuencia');
    }

    public function historial(): HasMany
    {
        return $this->hasMany(OrdenTrabajoHistorial::class, 'orden_trabajo_id')->orderByDesc('created_at');
    }

    public function creadoPor(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function actualizadoPor(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'updated_by');
    }

    public function scopeTenant(Builder $query, int $tenantId): Builder
    {
        return $query->where('tenant_id', $tenantId);
    }

    public function scopeActivos(Builder $query): Builder
    {
        return $query->where('estado', true);
    }

    public function scopeEstadoOt(Builder $query, string $estadoOt): Builder
    {
        return $query->where('estado_ot', $estadoOt);
    }
}
