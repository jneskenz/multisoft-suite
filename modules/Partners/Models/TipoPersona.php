<?php

namespace Modules\Partners\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TipoPersona extends Model
{
    protected $table = 'partners_tipo_personas';

    protected $fillable = [
        'persona_id',
        'tipo',
        'estado',
        'observacion',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'estado' => 'boolean',
        ];
    }

    public function persona(): BelongsTo
    {
        return $this->belongsTo(Persona::class, 'persona_id');
    }
}
