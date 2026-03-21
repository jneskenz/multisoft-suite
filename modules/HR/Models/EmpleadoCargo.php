<?php

namespace Modules\HR\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmpleadoCargo extends Model
{
    use SoftDeletes;

    protected $table = 'hr_empleado_cargos';

    protected $fillable = [
        'empleado_id',
        'cargo_id',
        'departamento_id',
        'reporta_a',
        'fecha_inicio',
        'fecha_fin',
        'es_actual',
        'estado',
        'notas',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'empleado_id' => 'integer',
        'cargo_id' => 'integer',
        'departamento_id' => 'integer',
        'reporta_a' => 'integer',
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'es_actual' => 'boolean',
        'estado' => 'integer',
    ];

    public function empleado(): BelongsTo
    {
        return $this->belongsTo(Empleado::class, 'empleado_id');
    }

    public function cargo(): BelongsTo
    {
        return $this->belongsTo(Cargo::class, 'cargo_id');
    }

    public function departamento(): BelongsTo
    {
        return $this->belongsTo(Departamento::class, 'departamento_id');
    }

    public function reportaA(): BelongsTo
    {
        return $this->belongsTo(Empleado::class, 'reporta_a');
    }

    public function scopeActual($query)
    {
        return $query->where('es_actual', true);
    }
}
