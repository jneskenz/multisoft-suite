<?php

namespace Modules\ERP\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class RecetaGraduacion extends Model
{
    use SoftDeletes;

    protected $table = 'erp_receta_graduaciones';

    /**
     * @var array<int,string>
     */
    protected $fillable = [
        'receta_id',
        'lejos_od_esferico',
        'lejos_od_cilindro',
        'lejos_od_eje',
        'lejos_od_av',
        'lejos_od_prisma',
        'lejos_od_base',
        'lejos_od_dnp',
        'lejos_oi_esferico',
        'lejos_oi_cilindro',
        'lejos_oi_eje',
        'lejos_oi_av',
        'lejos_oi_prisma',
        'lejos_oi_base',
        'lejos_oi_dnp',
        'lejos_dip',
        'adicion_cerca_od',
        'adicion_cerca_oi',
        'adicion_intermedia_od',
        'adicion_intermedia_oi',
        'autorefractometro_ticket_numero',
        'autorefractometro_distancia_pupilar',
        'autorefractometro_od_json',
        'autorefractometro_oi_json',
        'cerca_od_esferico',
        'cerca_od_cilindro',
        'cerca_od_eje',
        'cerca_od_av',
        'cerca_od_prisma',
        'cerca_od_base',
        'cerca_od_dnp',
        'cerca_oi_esferico',
        'cerca_oi_cilindro',
        'cerca_oi_eje',
        'cerca_oi_av',
        'cerca_oi_prisma',
        'cerca_oi_base',
        'cerca_oi_dnp',
        'cerca_dip',
        'intermedia_od_esferico',
        'intermedia_od_cilindro',
        'intermedia_od_eje',
        'intermedia_od_av',
        'intermedia_od_prisma',
        'intermedia_od_base',
        'intermedia_od_dnp',
        'intermedia_oi_esferico',
        'intermedia_oi_cilindro',
        'intermedia_oi_eje',
        'intermedia_oi_av',
        'intermedia_oi_prisma',
        'intermedia_oi_base',
        'intermedia_oi_dnp',
        'intermedia_dip',
        'fecha_cita',
        'fecha_proxima_cita',
        'recomendaciones',
        'estado',
        'created_by',
        'updated_by',
    ];

    /**
     * @var array<string,string>
     */
    protected $casts = [
        'lejos_od_esferico' => 'decimal:2',
        'lejos_od_cilindro' => 'decimal:2',
        'lejos_oi_esferico' => 'decimal:2',
        'lejos_oi_cilindro' => 'decimal:2',
        'adicion_cerca_od' => 'decimal:2',
        'adicion_cerca_oi' => 'decimal:2',
        'adicion_intermedia_od' => 'decimal:2',
        'adicion_intermedia_oi' => 'decimal:2',
        'cerca_od_esferico' => 'decimal:2',
        'cerca_od_cilindro' => 'decimal:2',
        'cerca_oi_esferico' => 'decimal:2',
        'cerca_oi_cilindro' => 'decimal:2',
        'intermedia_od_esferico' => 'decimal:2',
        'intermedia_od_cilindro' => 'decimal:2',
        'intermedia_oi_esferico' => 'decimal:2',
        'intermedia_oi_cilindro' => 'decimal:2',
        'autorefractometro_od_json' => 'array',
        'autorefractometro_oi_json' => 'array',
        'fecha_cita' => 'date',
        'fecha_proxima_cita' => 'date',
        'estado' => 'integer',
    ];

    public function receta(): BelongsTo
    {
        return $this->belongsTo(Receta::class, 'receta_id');
    }
}