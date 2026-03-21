<?php

namespace Modules\ERP\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Partners\Models\Persona;

class Receta extends Model
{
    use SoftDeletes;

    protected $table = 'erp_recetas';

    /**
     * @var array<int,string>
     */
    protected $fillable = [
        'tenant_id',
        'group_company_id',
        'ticket_id',
        'paciente_id',
        'especialista_id',
        'receta_numero',
        'fecha_receta',
        'tipo_receta',
        'estado_receta',
        'motivo_consulta',
        'observaciones_generales',
        'estado',
        'created_by',
        'updated_by',
    ];

    /**
     * @var array<string,string>
     */
    protected $casts = [
        'fecha_receta' => 'datetime',
        'estado' => 'integer',
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class, 'ticket_id');
    }

    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Persona::class, 'paciente_id');
    }

    public function especialista(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'especialista_id');
    }

    public function creadoPor(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function actualizadoPor(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'updated_by');
    }

    public function oftalmica(): HasOne
    {
        return $this->hasOne(RecetaOftalmica::class, 'receta_id');
    }

    public function graduacion(): HasOne
    {
        return $this->hasOne(RecetaGraduacion::class, 'receta_id');
    }

    public function contactologia(): HasOne
    {
        return $this->hasOne(RecetaContactologia::class, 'receta_id');
    }

    public function lentesEnUso(): HasOne
    {
        return $this->hasOne(RecetaLentesEnUso::class, 'receta_id');
    }

    public function ordenesTrabajo(): HasMany
    {
        return $this->hasMany(OrdenTrabajo::class, 'receta_id');
    }

    public function scopeTenant(Builder $query, int $tenantId): Builder
    {
        return $query->where('tenant_id', $tenantId);
    }

    public function scopeActivos(Builder $query): Builder
    {
        return $query->where('estado', 1);
    }

    public function scopeEstadoReceta(Builder $query, string $estadoReceta): Builder
    {
        return $query->where('estado_receta', $estadoReceta);
    }
}
