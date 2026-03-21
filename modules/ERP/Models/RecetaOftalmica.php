<?php

namespace Modules\ERP\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class RecetaOftalmica extends Model
{
    use SoftDeletes;

    protected $table = 'erp_receta_oftalmicas';

    /**
     * @var array<int,string>
     */
    protected $fillable = [
        'receta_id',
        'av_sc_od',
        'av_sc_oi',
        'av_cc_od',
        'av_cc_oi',
        'av_ae_od',
        'av_ae_oi',
        'tonometria_od',
        'tonometria_oi',
        'fondo_ojo_od',
        'fondo_ojo_oi',
        'anamnesis',
        'antecedentes_personales',
        'antecedentes_familiares',
        'antecedentes_quirurgicos',
        'biomicroscopia_od',
        'biomicroscopia_oi',
        'diagnostico_od',
        'diagnostico_od_observacion',
        'diagnostico_oi',
        'diagnostico_oi_observacion',
        'tratamiento_od',
        'tratamiento_od_observacion',
        'tratamiento_oi',
        'tratamiento_oi_observacion',
        'created_by',
        'updated_by',
    ];

    /**
     * @var array<string,string>
     */
    protected $casts = [
        'tonometria_od' => 'decimal:2',
        'tonometria_oi' => 'decimal:2',
    ];

    public function receta(): BelongsTo
    {
        return $this->belongsTo(Receta::class, 'receta_id');
    }
}