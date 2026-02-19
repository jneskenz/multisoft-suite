<?php

namespace Modules\HR\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class TipoDocumento extends Model
{
   use SoftDeletes;

   protected $table = 'hr_tipos_documento';

   protected $fillable = [
      'codigo',
      'nombre',
      'categoria',
      'descripcion',
      'requiere_firma_empleado',
      'requiere_firma_empleador',
      'requiere_testigos',
      'requiere_notarizacion',
      'usa_numeracion_automatica',
      'prefijo_numeracion',
      'formato_numeracion',
      'estado',
      'orden',
      'created_by',
      'updated_by',
   ];

   protected $casts = [
      'requiere_firma_empleado' => 'boolean',
      'requiere_firma_empleador' => 'boolean',
      'requiere_testigos' => 'boolean',
      'requiere_notarizacion' => 'boolean',
      'usa_numeracion_automatica' => 'boolean',
      'estado' => 'boolean',
      'orden' => 'integer',
   ];

   public const CATEGORIAS = [
      'contractual' => 'Contractual',
      'certificacion' => 'Certificación',
      'administrativo' => 'Administrativo',
      'disciplinario' => 'Disciplinario',
      'liquidacion' => 'Liquidación',
   ];

   public function categorias(): HasMany
   {
      return $this->hasMany(CategoriaDocumento::class, 'tipo_documento_id');
   }

   public function plantillas(): HasMany
   {
      return $this->hasMany(PlantillaDocumento::class, 'tipo_documento_id');
   }

   public function scopeActive($query)
   {
      return $query->where('estado', true);
   }

   public function getEstadoLabelAttribute(): string
   {
      return $this->estado ? 'Activo' : 'Inactivo';
   }

   public function getCategoriaLabelAttribute(): string
   {
      return self::CATEGORIAS[$this->categoria] ?? ucfirst($this->categoria);
   }
}
