<?php

namespace Modules\ERP\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrdenTrabajoDetalle extends Model
{
    use SoftDeletes;

    protected $table = 'erp_orden_trabajo_detalles';

    /**
     * @var array<int,string>
     */
    protected $fillable = [
        'orden_trabajo_id',
        'secuencia',
        'tipo_detalle',
        'catalogo_id',
        'matriz_lente_id',
        'producto_id',
        'descripcion',
        'cantidad',
        'unidad',
        'precio_unitario',
        'subtotal',
        'estado_detalle',
        'observaciones',
        'estado',
        'created_by',
        'updated_by',
    ];

    /**
     * @var array<string,string>
     */
    protected $casts = [
        'cantidad' => 'decimal:2',
        'precio_unitario' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'estado' => 'boolean',
    ];

    public function ordenTrabajo(): BelongsTo
    {
        return $this->belongsTo(OrdenTrabajo::class, 'orden_trabajo_id');
    }

    public function catalogo(): BelongsTo
    {
        return $this->belongsTo(Catalogo::class, 'catalogo_id');
    }

    public function matrizLente(): BelongsTo
    {
        return $this->belongsTo(MatrizLente::class, 'matriz_lente_id');
    }

    public function lente(): HasOne
    {
        return $this->hasOne(OrdenTrabajoLente::class, 'orden_trabajo_detalle_id');
    }

    public function creadoPor(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function actualizadoPor(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'updated_by');
    }
}
