<?php

namespace Modules\HR\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class PlantillaSeccionAsignada extends Pivot
{
   protected $table = 'hr_plantillas_secciones_asignadas';

   public $incrementing = true;

   protected $fillable = [
      'plantilla_id',
      'seccion_id',
      'orden',
      'es_obligatoria',
   ];

   protected $casts = [
      'plantilla_id' => 'integer',
      'seccion_id' => 'integer',
      'orden' => 'integer',
      'es_obligatoria' => 'boolean',
   ];
}
