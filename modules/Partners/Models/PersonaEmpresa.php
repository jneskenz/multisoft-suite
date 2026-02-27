<?php

namespace Modules\Partners\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PersonaEmpresa extends Model
{
    protected $table = 'partners_persona_empresa';

    protected $fillable = [
        'persona_id',
        'empresa_id',
        'tipo_relacion',
        'es_principal',
        'estado',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'es_principal' => 'boolean',
            'estado' => 'boolean',
        ];
    }

    public function persona(): BelongsTo
    {
        return $this->belongsTo(Persona::class, 'persona_id');
    }

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }
}
