<?php

namespace Modules\HR\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PlantillaDocumento extends Model
{
   use SoftDeletes;

   protected $table = 'hr_plantillas_documento';

   protected $fillable = [
      'codigo',
      'nombre',
      'descripcion',
      'tipo_documento_id',
      'categoria_documento_id',
      'contenido_html',
      'contenido_texto',
      'idioma',
      'formato_papel',
      'orientacion',
      'margenes',
      'variables_disponibles',
      'version',
      'es_predeterminada',
      'requiere_firma_empleado',
      'requiere_firma_empleador',
      'requiere_testigos',
      'posicion_firmas',
      'permite_anexos',
      'anexos_requeridos',
      'estado',
      'created_by',
      'updated_by',
   ];

   protected $casts = [
      'tipo_documento_id' => 'integer',
      'categoria_documento_id' => 'integer',
      'margenes' => 'array',
      'variables_disponibles' => 'array',
      'posicion_firmas' => 'array',
      'anexos_requeridos' => 'array',
      'es_predeterminada' => 'boolean',
      'requiere_firma_empleado' => 'boolean',
      'requiere_firma_empleador' => 'boolean',
      'requiere_testigos' => 'boolean',
      'permite_anexos' => 'boolean',
   ];

   public const ESTADOS = [
      '1' => 'Activo',
      '0' => 'Inactivo',
   ];

   public function tipoDocumento(): BelongsTo
   {
      return $this->belongsTo(TipoDocumento::class, 'tipo_documento_id');
   }

   public function categoriaDocumento(): BelongsTo
   {
      return $this->belongsTo(CategoriaDocumento::class, 'categoria_documento_id');
   }

   public function versiones(): HasMany
   {
      return $this->hasMany(PlantillaVersion::class, 'plantilla_id');
   }

   public function secciones(): BelongsToMany
   {
      return $this->belongsToMany(
         PlantillaSeccion::class,
         'hr_plantillas_secciones_asignadas',
         'plantilla_id',
         'seccion_id'
      )->withPivot('orden', 'es_obligatoria')->withTimestamps();
   }

   public function getEstadoLabelAttribute(): string
   {
      return self::ESTADOS[$this->estado] ?? ucfirst($this->estado);
   }
}
