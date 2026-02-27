<?php

namespace Modules\ERP\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Partners\Models\Persona;

class Ticket extends Model
{
    use SoftDeletes;

    protected $table = 'erp_tickets';

    /**
     * @var array<int,string>
     */
    protected $fillable = [
        'tenant_id',
        'group_company_id',
        'paciente_id',
        'ticket_numero',
        'fecha_ticket',
        'estado_ticket',
        'prioridad',
        'canal',
        'resumen',
        'moneda',
        'subtotal',
        'descuento_total',
        'impuesto_total',
        'total',
        'saldo_pendiente',
        'fecha_cierre',
        'cerrado_por',
        'estado',
        'created_by',
        'updated_by',
    ];

    /**
     * @var array<string,string>
     */
    protected $casts = [
        'fecha_ticket' => 'datetime',
        'fecha_cierre' => 'datetime',
        'subtotal' => 'decimal:2',
        'descuento_total' => 'decimal:2',
        'impuesto_total' => 'decimal:2',
        'total' => 'decimal:2',
        'saldo_pendiente' => 'decimal:2',
        'estado' => 'boolean',
    ];

    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Persona::class, 'paciente_id');
    }

    public function creadoPor(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function actualizadoPor(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'updated_by');
    }

    public function cerradoPor(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'cerrado_por');
    }

    public function scopeTenant(Builder $query, int $tenantId): Builder
    {
        return $query->where('tenant_id', $tenantId);
    }

    public function scopeActivos(Builder $query): Builder
    {
        return $query->where('estado', true);
    }

    public function scopeEstadoTicket(Builder $query, string $estadoTicket): Builder
    {
        return $query->where('estado_ticket', $estadoTicket);
    }
}
