<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Location extends Model
{
   use SoftDeletes;

   /**
    * La tabla asociada al modelo.
    */
   protected $table = 'core_locations';

   /**
    * Los atributos que son asignables masivamente.
    */
   protected $fillable = [
      'company_id',
      'code',
      'name',
      'timezone',
      'address',
      'city',
      'phone',
      'is_main',
      'status',
   ];

   /**
    * Los atributos que deben ser convertidos a tipos nativos.
    */
   protected function casts(): array
   {
      return [
         'company_id' => 'integer',
         'is_main' => 'boolean',
      ];
   }

   /**
    * Obtener la empresa a la que pertenece.
    */
   public function company(): BelongsTo
   {
      return $this->belongsTo(Company::class, 'company_id');
   }

   /**
    * Verificar si el local está activo.
    */
   public function isActive(): bool
   {
      return $this->status === 'active';
   }

   /**
    * Verificar si es el local principal.
    */
   public function isMain(): bool
   {
      return $this->is_main === true;
   }

   /**
    * Obtener el nombre para mostrar.
    */
   public function getDisplayNameAttribute(): string
   {
      return $this->name;
   }

   /**
    * Obtener el timezone efectivo (del location, company o grupo).
    */
   public function getEffectiveTimezoneAttribute(): string
   {
      return $this->timezone
         ?? $this->company?->timezone
         ?? $this->company?->groupCompany?->timezone
         ?? 'America/Lima';
   }

   /**
    * Obtener el label del estado.
    */
   public function getStatusLabelAttribute(): string
   {
      return match ($this->status) {
         'active' => 'Activo',
         'inactive' => 'Inactivo',
         default => 'Desconocido',
      };
   }

   /**
    * Scope para filtrar solo locales activos.
    */
   public function scopeActive($query)
   {
      return $query->where('status', 'active');
   }

   /**
    * Scope para filtrar por empresa.
    */
   public function scopeForCompany($query, int $companyId)
   {
      return $query->where('company_id', $companyId);
   }

   /**
    * Scope para filtrar solo locales principales.
    */
   public function scopeMain($query)
   {
      return $query->where('is_main', true);
   }

   /**
    * Scope para filtrar por código.
    */
   public function scopeByCode($query, string $code)
   {
      return $query->where('code', $code);
   }

   /**
    * Buscar local por código.
    */
   public static function findByCode(string $code, ?int $companyId = null): ?self
   {
      $query = static::where('code', $code);

      if ($companyId) {
         $query->where('company_id', $companyId);
      }

      return $query->first();
   }

   /**
    * Establecer este local como principal y quitar el flag de otros.
    */
   public function setAsMain(): void
   {
      // Quitar el flag is_main de otros locales de la misma empresa
      static::where('company_id', $this->company_id)
         ->where('id', '!=', $this->id)
         ->update(['is_main' => false]);

      // Establecer este como principal
      $this->update(['is_main' => true]);
   }
}
