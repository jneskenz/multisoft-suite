<?php

namespace Modules\ERP\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CombinacionMedida extends Model
{
    use SoftDeletes;

    protected $table = 'erp_combinacion_medidas';

    /**
     * @var array<int,string>
     */
    protected $fillable = [
        'catalogo_id',
        'serie_visual_id',
        'subserie_visual_id',
        'medida_esferica_desde_id',
        'medida_esferica_hasta_id',
        'medida_cilindrica_desde_id',
        'medida_cilindrica_hasta_id',
        'adicion_desde_id',
        'adicion_hasta_id',
        'preciobase',
        'precio_x_menor_minimo',
        'precio_x_menor_base',
        'precio_x_menor_maximo',
        'precio_x_mayor_minimo',
        'precio_x_mayor_base',
        'precio_x_mayor_maximo',
        'estado',
        'created_by',
        'updated_by',
    ];

    /**
     * @var array<string,string>
     */
    protected $casts = [
        'preciobase' => 'decimal:2',
        'precio_x_menor_minimo' => 'decimal:2',
        'precio_x_menor_base' => 'decimal:2',
        'precio_x_menor_maximo' => 'decimal:2',
        'precio_x_mayor_minimo' => 'decimal:2',
        'precio_x_mayor_base' => 'decimal:2',
        'precio_x_mayor_maximo' => 'decimal:2',
        'estado' => 'integer',
    ];

    public function catalogo(): BelongsTo
    {
        return $this->belongsTo(Catalogo::class, 'catalogo_id');
    }
}

