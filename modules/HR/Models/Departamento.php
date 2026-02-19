<?php

namespace Modules\HR\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Departamento extends Model
{
    use SoftDeletes;

    public const ESTADO_INACTIVO = 0;
    public const ESTADO_ACTIVO = 1;

    protected $table = 'hr_departamentos';

    protected $fillable = [
        'tipo_departamento_id',
        'padre_id',
        'jefe_id',
        'codigo',
        'name',
        'descripcion',
        'estado',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'tipo_departamento_id' => 'integer',
        'padre_id' => 'integer',
        'jefe_id' => 'integer',
        'estado' => 'integer',
    ];

    public function padre(): BelongsTo
    {
        return $this->belongsTo(self::class, 'padre_id');
    }

    public function hijos(): HasMany
    {
        return $this->hasMany(self::class, 'padre_id');
    }

    public function jefe(): BelongsTo
    {
        return $this->belongsTo(Empleado::class, 'jefe_id');
    }

    public function tipoDepartamento(): BelongsTo
    {
        return $this->belongsTo(TipoDepartamento::class, 'tipo_departamento_id');
    }

    public function cargos(): HasMany
    {
        return $this->hasMany(Cargo::class, 'departamento_id');
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
