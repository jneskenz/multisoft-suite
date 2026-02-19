<?php

namespace Modules\HR\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cargo extends Model
{
    use SoftDeletes;

    public const ESTADO_INACTIVO = 0;
    public const ESTADO_ACTIVO = 1;

    protected $table = 'hr_cargos';

    protected $fillable = [
        'departamento_id',
        'codigo',
        'name',
        'descripcion',
        'nivel',
        'estado',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'departamento_id' => 'integer',
        'estado' => 'integer',
    ];

    public function departamento(): BelongsTo
    {
        return $this->belongsTo(Departamento::class, 'departamento_id');
    }

    public function scopeActive($query)
    {
        return $query->where('estado', self::ESTADO_ACTIVO);
    }

    public function getEstadoLabelAttribute(): string
    {
        return (int) $this->estado === self::ESTADO_ACTIVO ? 'Activo' : 'Inactivo';
    }
}
