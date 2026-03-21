<?php

namespace Modules\ERP\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrdenTrabajoLente extends Model
{
    use SoftDeletes;

    protected $table = 'erp_orden_trabajo_lentes';

    /**
     * @var array<int,string>
     */
    protected $fillable = [
        'orden_trabajo_detalle_id',
        'tipo_vision',
        'material_id',
        'tratamiento_id',
        'color_id',
        'diseno_id',
        'indice_id',
        'od_esferico',
        'od_cilindro',
        'od_eje',
        'od_adicion',
        'oi_esferico',
        'oi_cilindro',
        'oi_eje',
        'oi_adicion',
        'dp',
        'altura_oblea',
        'observaciones_tecnicas',
        'estado',
        'created_by',
        'updated_by',
    ];

    /**
     * @var array<string,string>
     */
    protected $casts = [
        'od_esferico' => 'decimal:2',
        'od_cilindro' => 'decimal:2',
        'od_adicion' => 'decimal:2',
        'oi_esferico' => 'decimal:2',
        'oi_cilindro' => 'decimal:2',
        'oi_adicion' => 'decimal:2',
        'dp' => 'decimal:2',
        'altura_oblea' => 'decimal:2',
        'estado' => 'boolean',
    ];

    public function detalle(): BelongsTo
    {
        return $this->belongsTo(OrdenTrabajoDetalle::class, 'orden_trabajo_detalle_id');
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
