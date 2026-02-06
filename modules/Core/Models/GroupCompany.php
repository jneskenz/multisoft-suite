<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GroupCompany extends Model
{
    /**
     * La tabla asociada al modelo.
     */
    protected $table = 'core_group_companies';

    /**
     * Los atributos que son asignables masivamente.
     */
    protected $fillable = [
        'tenant_id',
        'code',
        'country_code',
        'business_name',
        'trade_name',
        'tax_id',
        'tax_id_type',
        'app_name',
        'logo',
        'favicon',
        'address',
        'city',
        'phone',
        'email',
        'website',
        'timezone',
        'locale',
        'currency_code',
        'currency_symbol',
        'date_format',
        'time_format',
        'decimal_separator',
        'thousands_separator',
        'status',
    ];

    /**
     * Los atributos que deben ser convertidos a tipos nativos.
     */
    protected function casts(): array
    {
        return [
            'tenant_id' => 'integer',
        ];
    }

    /**
     * Obtener el tenant al que pertenece.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    /**
     * Obtener las configuraciones del grupo.
     */
    public function settings(): HasMany
    {
        return $this->hasMany(Setting::class, 'group_company_id');
    }

    /**
     * Verificar si el grupo está activo.
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
        return $this->app_name ?? $this->trade_name ?? $this->business_name;
    }

    /**
     * Obtener el logo efectivo (del grupo o del tenant).
     */
    public function getEffectiveLogoAttribute(): ?string
    {
        return $this->logo ?? $this->tenant?->logo;
    }

    /**
     * Obtener el favicon efectivo (del grupo o del tenant).
     */
    public function getEffectiveFaviconAttribute(): ?string
    {
        return $this->favicon ?? $this->tenant?->favicon;
    }

    /**
     * Obtener emoji de bandera según el código de país.
     */
    public function getFlagEmojiAttribute(): string
    {
        $flags = [
            'PE' => '🇵🇪',
            'EC' => '🇪🇨',
            'CO' => '🇨🇴',
            'CL' => '🇨🇱',
            'AR' => '🇦🇷',
            'MX' => '🇲🇽',
            'BR' => '🇧🇷',
            'US' => '🇺🇸',
            'ES' => '🇪🇸',
        ];

        return $flags[$this->country_code] ?? '🏳️';
    }

    /**
     * Scope para filtrar solo grupos activos.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope para filtrar por tenant.
     */
    public function scopeForTenant($query, int $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    /**
     * Scope para filtrar por país.
     */
    public function scopeByCountry($query, string $countryCode)
    {
        return $query->where('country_code', $countryCode);
    }

    /**
     * Buscar grupo por código.
     */
    public static function findByCode(string $code, ?int $tenantId = null): ?self
    {
        $query = static::where('code', $code);
        
        if ($tenantId) {
            $query->where('tenant_id', $tenantId);
        }
        
        return $query->first();
    }
}
