<?php

namespace Modules\HR\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PlantillaSeccion extends Model
{
   use SoftDeletes;

   protected $table = 'hr_plantillas_secciones';

   protected $fillable = [
      'codigo',
      'nombre',
      'descripcion',
      'contenido_html',
      'contenido_texto',
      'categoria',
      'aplicable_a',
      'es_obligatoria',
      'variables_usadas',
      'estado',
      'orden',
      'created_by',
      'updated_by',
   ];

   protected $casts = [
      'aplicable_a' => 'array',
      'variables_usadas' => 'array',
      'es_obligatoria' => 'boolean',
      'orden' => 'integer',
   ];

   public const CATEGORIAS_SECCION = [
      'encabezado' => 'Encabezado',
      'cuerpo' => 'Cuerpo',
      'clausula' => 'Cláusula',
      'footer' => 'Footer',
      'firma' => 'Firma',
   ];

   public function scopeActive($query)
   {
      return $query->where('estado', '1');
   }

   public function plantillas(): BelongsToMany
   {
      return $this->belongsToMany(
         PlantillaDocumento::class,
         'hr_plantillas_secciones_asignadas',
         'seccion_id',
         'plantilla_id'
      )->withPivot('orden', 'es_obligatoria')->withTimestamps();
   }

   public function getEstadoLabelAttribute(): string
   {
      return $this->estado === '1' ? 'Activo' : 'Inactivo';
   }

   public function getCategoriaLabelAttribute(): string
   {
      return self::CATEGORIAS_SECCION[$this->categoria] ?? ucfirst($this->categoria ?? '-');
   }
}
