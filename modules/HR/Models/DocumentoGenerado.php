<?php

namespace Modules\HR\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentoGenerado extends Model
{
   use SoftDeletes;

   protected $table = 'hr_documentos_generados';

   protected $fillable = [
      'numero_documento',
      'tipo_documento_id',
      'categoria_documento_id',
      'plantilla_utilizada_id',
      'plantilla_version',
      'contenido_generado',
      'variables_aplicadas',
      'fecha_generacion',
      'empleado_id',
      'contrato_id',
      'relacionado_con_tipo',
      'relacionado_con_id',
      'estado_firmas',
      'firmado_por_empleado_at',
      'firmado_por_empleador_at',
      'firmas_digitales',
      'ruta_archivo_pdf',
      'archivos_anexos',
      'estado_documento',
      'estado',
      'fecha_vigencia_desde',
      'fecha_vigencia_hasta',
      'motivo_anulacion',
      'anulado_por',
      'anulado_at',
      'observaciones',
      'creado_por',
      'actualizado_por',
   ];

   protected $casts = [
      'tipo_documento_id' => 'integer',
      'categoria_documento_id' => 'integer',
      'plantilla_utilizada_id' => 'integer',
      'empleado_id' => 'integer',
      'contrato_id' => 'integer',
      'relacionado_con_id' => 'integer',
      'variables_aplicadas' => 'array',
      'firmas_digitales' => 'array',
      'archivos_anexos' => 'array',
      'fecha_generacion' => 'datetime',
      'firmado_por_empleado_at' => 'datetime',
      'firmado_por_empleador_at' => 'datetime',
      'anulado_at' => 'datetime',
      'fecha_vigencia_desde' => 'date',
      'fecha_vigencia_hasta' => 'date',
   ];

   public const ESTADOS_DOCUMENTO = [
      '0' => 'Borrador',
      '1' => 'Por firmar',
      '2' => 'Vigente',
      '3' => 'Por vencer',
      '4' => 'Vencido',
      '5' => 'Anulado/Cancelado',
      '6' => 'Resuelto/Terminado',
   ];

   public const ESTADOS_FIRMA = [
      'pendiente' => 'Pendiente',
      'firmado' => 'Firmado',
      'rechazado' => 'Rechazado',
   ];

   public function tipoDocumento(): BelongsTo
   {
      return $this->belongsTo(TipoDocumento::class, 'tipo_documento_id');
   }

   public function categoriaDocumento(): BelongsTo
   {
      return $this->belongsTo(CategoriaDocumento::class, 'categoria_documento_id');
   }

   public function plantillaUtilizada(): BelongsTo
   {
      return $this->belongsTo(PlantillaDocumento::class, 'plantilla_utilizada_id');
   }

   public function empleado(): BelongsTo
   {
      return $this->belongsTo(Empleado::class, 'empleado_id');
   }

   public function contrato(): BelongsTo
   {
      return $this->belongsTo(Contrato::class, 'contrato_id');
   }

   public function getEstadoDocumentoLabelAttribute(): string
   {
      return self::ESTADOS_DOCUMENTO[$this->estado_documento] ?? ucfirst($this->estado_documento);
   }

   public function getEstadoFirmasLabelAttribute(): string
   {
      return self::ESTADOS_FIRMA[$this->estado_firmas] ?? ucfirst($this->estado_firmas);
   }

   public function getEstadoLabelAttribute(): string
   {
      return $this->estado === '1' ? 'Activo' : 'Inactivo';
   }

   public function scopeActive($query)
   {
      return $query->where('estado', '1');
   }

   public function scopeVigente($query)
   {
      return $query->where('estado_documento', '2');
   }
}
