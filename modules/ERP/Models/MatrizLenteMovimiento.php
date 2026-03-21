<?php

namespace Modules\ERP\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MatrizLenteMovimiento extends Model
{
    protected $table = 'erp_matriz_lentes_movimientos';

    /**
     * @var array<int,string>
     */
    protected $fillable = [
        'matriz_lente_id',
        'almacen_id',
        'tipo_movimiento',
        'cantidad',
        'stock_anterior',
        'stock_nuevo',
        'referencia_tipo',
        'referencia_id',
        'observacion',
        'created_by',
    ];

    /**
     * @var array<string,string>
     */
    protected $casts = [
        'cantidad' => 'decimal:2',
        'stock_anterior' => 'decimal:2',
        'stock_nuevo' => 'decimal:2',
    ];

    public function matrizLente(): BelongsTo
    {
        return $this->belongsTo(MatrizLente::class, 'matriz_lente_id');
    }
}

