<?php

namespace Modules\HR\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\HR\Enums\EstadoContrato;

class Contrato extends Model
{
   use SoftDeletes;

   protected $table = 'hr_contratos';

   protected $fillable = [
      'empleado_id',
      'numero_contrato',
      'tipo_contrato_id',
      'modalidad_id',
      'fecha_inicio',
      'fecha_fin',
      'salario_base',
      'estado',
      'estado_contrato',
      'motivo_terminacion',
      'fecha_terminacion',
      'archivo_contrato',
      'notas',
      'horas_semanales',
      'descripcion_horario',
      'created_by',
      'updated_by',
   ];

   protected $casts = [
      'empleado_id'       => 'integer',
      'tipo_contrato_id'  => 'integer',
      'modalidad_id'      => 'integer',
      'salario_base'      => 'decimal:2',
      'horas_semanales'   => 'decimal:2',
      'estado'            => 'integer',
      'estado_contrato'   => EstadoContrato::class,
      'fecha_inicio'      => 'date',
      'fecha_fin'         => 'date',
      'fecha_terminacion' => 'date',
      'created_by'        => 'integer',
      'updated_by'        => 'integer',
   ];

   // ─── Relaciones ─────────────────────────────────────────────

   /**
    * Empleado al que pertenece el contrato.
    */
   public function empleado(): BelongsTo
   {
      return $this->belongsTo(Empleado::class, 'empleado_id');
   }

   /**
    * Tipo de documento (contrato indefinido, temporal, prácticas, etc.)
    * Solo deben usarse tipos con categoria='contractual'.
    */
   public function tipoContrato(): BelongsTo
   {
      return $this->belongsTo(TipoDocumento::class, 'tipo_contrato_id');
   }

   /**
    * Modalidad / categoría del documento (necesidad de mercado, suplencia, etc.)
    * Estas son sub-categorías del tipo de documento seleccionado.
    */
   public function modalidad(): BelongsTo
   {
      return $this->belongsTo(CategoriaDocumento::class, 'modalidad_id');
   }

   /**
    * Documentos generados asociados a este contrato.
    */
   public function documentosGenerados(): HasMany
   {
      return $this->hasMany(DocumentoGenerado::class, 'contrato_id');
   }

   /**
    * Usuario que creó el contrato.
    */
   public function creadoPor(): BelongsTo
   {
      return $this->belongsTo(\App\Models\User::class, 'created_by');
   }

   /**
    * Usuario que actualizó el contrato.
    */
   public function actualizadoPor(): BelongsTo
   {
      return $this->belongsTo(\App\Models\User::class, 'updated_by');
   }

   // ─── Scopes ────────────────────────────────────────────────

   /**
    * Solo contratos con estado_contrato = FIRMADO y sin fecha_terminacion.
    */
   public function scopeVigente($query)
   {
      return $query->where('estado_contrato', EstadoContrato::FIRMADO)
         ->whereNull('fecha_terminacion');
   }

   /**
    * Contratos cuya fecha_fin ya pasó.
    */
   public function scopeVencido($query)
   {
      return $query->whereNotNull('fecha_fin')
         ->where('fecha_fin', '<', now());
   }

   /**
    * Contratos que vencen en los próximos N días.
    */
   public function scopePorVencer($query, int $dias = 30)
   {
      return $query->whereNotNull('fecha_fin')
         ->whereBetween('fecha_fin', [now(), now()->addDays($dias)]);
   }

   /**
    * Contratos activos (estado genérico = 1).
    */
   public function scopeActive($query)
   {
      return $query->where('estado', 1);
   }

   // ─── Accessors ─────────────────────────────────────────────

   /**
    * Label del tipo de contrato (desde la relación).
    */
   public function getTipoContratoLabelAttribute(): string
   {
      return $this->tipoContrato?->nombre ?? '-';
   }

   /**
    * Label de la modalidad (desde la relación).
    */
   public function getModalidadLabelAttribute(): string
   {
      return $this->modalidad?->nombre ?? '';
   }

   /**
    * ¿El contrato está vigente?
    */
   public function getIsVigenteAttribute(): bool
   {
      return $this->estado_contrato === EstadoContrato::FIRMADO
         && is_null($this->fecha_terminacion)
         && (is_null($this->fecha_fin) || $this->fecha_fin->isFuture());
   }

   /**
    * ¿El contrato está por vencer en los próximos 30 días?
    */
   public function getIsPorVencerAttribute(): bool
   {
      return $this->is_vigente
         && $this->fecha_fin
         && $this->fecha_fin->diffInDays(now()) <= 30;
   }

   /**
    * Color del badge según estado_contrato.
    */
   public function getEstadoBadgeClassAttribute(): string
   {
      return 'bg-label-' . ($this->estado_contrato?->color() ?? 'secondary');
   }
}
