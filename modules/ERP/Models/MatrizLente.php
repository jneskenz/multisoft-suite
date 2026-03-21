<?php

namespace Modules\ERP\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class MatrizLente extends Model
{
    use SoftDeletes;

    protected $table = 'erp_matriz_lentes';

    /**
     * @var array<int,string>
     */
    protected $fillable = [
        'catalogo_id',
        'combinacion_medida_id',
        'categoria_id',
        'serie_visual_id',
        'subserie_visual_id',
        'adicion_id',
        'medida_esferica_id',
        'medida_cilindrica_id',
        'codigo_matriz',
        'estado',
        'generado_at',
        'created_by',
        'updated_by',
    ];

    /**
     * @var array<string,string>
     */
    protected $casts = [
        'estado' => 'integer',
        'generado_at' => 'datetime',
    ];

    public function catalogo(): BelongsTo
    {
        return $this->belongsTo(Catalogo::class, 'catalogo_id');
    }

    public function combinacionMedida(): BelongsTo
    {
        return $this->belongsTo(CombinacionMedida::class, 'combinacion_medida_id');
    }

    public function stocks(): HasMany
    {
        return $this->hasMany(MatrizLenteStock::class, 'matriz_lente_id');
    }

    public function movimientos(): HasMany
    {
        return $this->hasMany(MatrizLenteMovimiento::class, 'matriz_lente_id');
    }

    public function ordenTrabajoDetalles(): HasMany
    {
        return $this->hasMany(OrdenTrabajoDetalle::class, 'matriz_lente_id');
    }
}
