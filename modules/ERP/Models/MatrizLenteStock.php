<?php

namespace Modules\ERP\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class MatrizLenteStock extends Model
{
    use SoftDeletes;

    protected $table = 'erp_matriz_lentes_stock';

    /**
     * @var array<int,string>
     */
    protected $fillable = [
        'matriz_lente_id',
        'almacen_id',
        'stock_actual',
        'stock_reservado',
        'stock_disponible',
        'stock_minimo',
        'stock_maximo',
        'costo_promedio',
        'ultimo_costo',
        'precio_venta',
        'estado',
        'created_by',
        'updated_by',
    ];

    /**
     * @var array<string,string>
     */
    protected $casts = [
        'stock_actual' => 'decimal:2',
        'stock_reservado' => 'decimal:2',
        'stock_disponible' => 'decimal:2',
        'stock_minimo' => 'decimal:2',
        'stock_maximo' => 'decimal:2',
        'costo_promedio' => 'decimal:4',
        'ultimo_costo' => 'decimal:4',
        'precio_venta' => 'decimal:2',
        'estado' => 'integer',
    ];

    public function matrizLente(): BelongsTo
    {
        return $this->belongsTo(MatrizLente::class, 'matriz_lente_id');
    }
}

