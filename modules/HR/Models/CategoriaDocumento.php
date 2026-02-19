<?php

namespace Modules\HR\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CategoriaDocumento extends Model
{
   use SoftDeletes;

   protected $table = 'hr_categorias_documento';

   protected $fillable = [
      'tipo_documento_id',
      'codigo',
      'nombre',
      'descripcion',
      'requiere_justificacion',
      'requiere_aprobacion',
      'nivel_aprobacion',
      'articulo_ley',
      'estado',
      'orden',
      'created_by',
      'updated_by',
   ];

   protected $casts = [
      'tipo_documento_id' => 'integer',
      'requiere_justificacion' => 'boolean',
      'requiere_aprobacion' => 'boolean',
      'estado' => 'boolean',
      'orden' => 'integer',
   ];

   public function tipoDocumento(): BelongsTo
   {
      return $this->belongsTo(TipoDocumento::class, 'tipo_documento_id');
   }

   public function plantillas(): HasMany
   {
      return $this->hasMany(PlantillaDocumento::class, 'categoria_documento_id');
   }

   public function scopeActive($query)
   {
      return $query->where('estado', true);
   }

   public function getEstadoLabelAttribute(): string
   {
      return $this->estado ? 'Activo' : 'Inactivo';
   }
}
