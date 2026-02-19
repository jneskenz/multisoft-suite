<?php

namespace Modules\HR\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PlantillaVersion extends Model
{
   use SoftDeletes;

   protected $table = 'hr_plantillas_versiones';

   public $timestamps = false;

   protected $fillable = [
      'plantilla_id',
      'version',
      'fecha_version',
      'contenido_html',
      'contenido_texto',
      'configuracion',
      'motivo_cambio',
      'cambios_detalle',
      'documentos_generados',
      'created_by',
   ];

   protected $casts = [
      'plantilla_id' => 'integer',
      'configuracion' => 'array',
      'documentos_generados' => 'integer',
      'fecha_version' => 'datetime',
      'created_at' => 'datetime',
   ];

   public function plantilla(): BelongsTo
   {
      return $this->belongsTo(PlantillaDocumento::class, 'plantilla_id');
   }

   public function creador(): BelongsTo
   {
      return $this->belongsTo(\App\Models\User::class, 'created_by');
   }
}
