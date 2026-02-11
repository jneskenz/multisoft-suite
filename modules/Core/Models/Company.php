<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
   use SoftDeletes;

   /**
    * La tabla asociada al modelo.
    */
   protected $table = 'core_companies';

   /**
    * Los atributos que son asignables masivamente.
    */
   protected $fillable = [
      'group_company_id',
      'code',
      'name',
      'trade_name',
      'tax_id',
      'timezone',
      'address',
      'phone',
      'email',
      'status',
   ];

   /**
    * Los atributos que deben ser convertidos a tipos nativos.
    */
   protected function casts(): array
   {
      return [
         'group_company_id' => 'integer',
      ];
   }

   /**
    * Obtener el grupo al que pertenece.
    */
   public function groupCompany(): BelongsTo
   {
      return $this->belongsTo(GroupCompany::class, 'group_company_id');
   }

   /**
    * Obtener los locales de esta empresa.
    */
   public function locations(): HasMany
   {
      return $this->hasMany(Location::class, 'company_id');
   }

   /**
    * Verificar si la empresa está activa.
    */
   public function isActive(): bool
   {
      return $this->status === 'active';
   }

   /**
    * Obtener el nombre para mostrar.
    */
   public function getDisplayNameAttribute(): string
   {
      return $this->trade_name ?? $this->name;
   }

   /**
    * Obtener el timezone efectivo (del company o del grupo).
    */
   public function getEffectiveTimezoneAttribute(): string
   {
      return $this->timezone ?? $this->groupCompany?->timezone ?? 'America/Lima';
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
    * Scope para filtrar solo empresas activas.
    */
   public function scopeActive($query)
   {
      return $query->where('status', 'active');
   }

   /**
    * Scope para filtrar por grupo.
    */
   public function scopeForGroup($query, int $groupCompanyId)
   {
      return $query->where('group_company_id', $groupCompanyId);
   }

   /**
    * Scope para filtrar por código.
    */
   public function scopeByCode($query, string $code)
   {
      return $query->where('code', $code);
   }

   /**
    * Buscar empresa por código.
    */
   public static function findByCode(string $code, ?int $groupCompanyId = null): ?self
   {
      $query = static::where('code', $code);

      if ($groupCompanyId) {
         $query->where('group_company_id', $groupCompanyId);
      }

      return $query->first();
   }
}
