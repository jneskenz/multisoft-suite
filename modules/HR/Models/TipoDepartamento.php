<?php

namespace Modules\HR\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class TipoDepartamento extends Model
{
    use SoftDeletes;

    protected $table = 'hr_tipo_departamentos';

    protected $fillable = [
        'codigo',
        'name',
        'descripcion',
        'estado',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'estado' => 'integer',
    ];

    public function departamentos(): HasMany
    {
        return $this->hasMany(Departamento::class, 'tipo_departamento_id');
    }
}
