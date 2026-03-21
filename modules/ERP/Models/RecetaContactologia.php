<?php

namespace Modules\ERP\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class RecetaContactologia extends Model
{
    use SoftDeletes;

    protected $table = 'erp_receta_contactologia';

    /**
     * @var array<int,string>
     */
    protected $fillable = [
        'receta_id',
        'queratometria_od_horizontal',
        'queratometria_od_vertical',
        'queratometria_od_eje',
        'queratometria_oi_horizontal',
        'queratometria_oi_vertical',
        'queratometria_oi_eje',
        'prueba_od_esferico',
        'prueba_od_cilindro',
        'prueba_od_eje',
        'prueba_od_cb',
        'prueba_od_diametro',
        'prueba_oi_esferico',
        'prueba_oi_cilindro',
        'prueba_oi_eje',
        'prueba_oi_cb',
        'prueba_oi_diametro',
        'definitivo_od_esferico',
        'definitivo_od_cilindro',
        'definitivo_od_eje',
        'definitivo_od_cb',
        'definitivo_od_diametro',
        'definitivo_oi_esferico',
        'definitivo_oi_cilindro',
        'definitivo_oi_eje',
        'definitivo_oi_cb',
        'definitivo_oi_diametro',
        'sobrerefraccion_od_esferico',
        'sobrerefraccion_od_cilindro',
        'sobrerefraccion_od_eje',
        'sobrerefraccion_od_giro',
        'sobrerefraccion_oi_esferico',
        'sobrerefraccion_oi_cilindro',
        'sobrerefraccion_oi_eje',
        'sobrerefraccion_oi_giro',
        'material',
        'tipo_uso',
        'marca',
        'shirmer_od',
        'shirmer_oi',
        'but_od',
        'but_oi',
        'estado',
        'created_by',
        'updated_by',
    ];

    /**
     * @var array<string,string>
     */
    protected $casts = [
        'prueba_od_esferico' => 'decimal:2',
        'prueba_od_cilindro' => 'decimal:2',
        'prueba_od_cb' => 'decimal:2',
        'prueba_od_diametro' => 'decimal:2',
        'prueba_oi_esferico' => 'decimal:2',
        'prueba_oi_cilindro' => 'decimal:2',
        'prueba_oi_cb' => 'decimal:2',
        'prueba_oi_diametro' => 'decimal:2',
        'definitivo_od_esferico' => 'decimal:2',
        'definitivo_od_cilindro' => 'decimal:2',
        'definitivo_od_cb' => 'decimal:2',
        'definitivo_od_diametro' => 'decimal:2',
        'definitivo_oi_esferico' => 'decimal:2',
        'definitivo_oi_cilindro' => 'decimal:2',
        'definitivo_oi_cb' => 'decimal:2',
        'definitivo_oi_diametro' => 'decimal:2',
        'sobrerefraccion_od_esferico' => 'decimal:2',
        'sobrerefraccion_od_cilindro' => 'decimal:2',
        'sobrerefraccion_oi_esferico' => 'decimal:2',
        'sobrerefraccion_oi_cilindro' => 'decimal:2',
        'estado' => 'integer',
    ];

    public function receta(): BelongsTo
    {
        return $this->belongsTo(Receta::class, 'receta_id');
    }
}
