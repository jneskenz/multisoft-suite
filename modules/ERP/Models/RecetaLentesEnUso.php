<?php

namespace Modules\ERP\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class RecetaLentesEnUso extends Model
{
    use SoftDeletes;

    protected $table = 'erp_receta_lentes_en_uso';

    /**
     * @var array<int,string>
     */
    protected $fillable = [
        'receta_id',
        'od_esferico',
        'od_cilindro',
        'od_eje',
        'od_av_cc',
        'od_altura',
        'od_adicion',
        'oi_esferico',
        'oi_cilindro',
        'oi_eje',
        'oi_av_cc',
        'oi_altura',
        'oi_adicion',
        'dip',
        'usa_lejos',
        'usa_cerca',
        'observaciones',
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
        'usa_lejos' => 'boolean',
        'usa_cerca' => 'boolean',
        'estado' => 'integer',
    ];

    public function receta(): BelongsTo
    {
        return $this->belongsTo(Receta::class, 'receta_id');
    }
}
