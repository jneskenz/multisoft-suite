<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    /**
     * La tabla asociada al modelo.
     */
    protected $table = 'core_settings';

    /**
     * Los atributos que son asignables masivamente.
     */
    protected $fillable = [
        'tenant_id',
        'group_company_id',
        'company_id',
        'location_id',
        'category',
        'key',
        'value',
        'type',
        'display_name',
        'description',
    ];

    /**
     * Los atributos que deben ser convertidos a tipos nativos.
     */
    protected function casts(): array
    {
        return [
            'tenant_id' => 'integer',
            'group_company_id' => 'integer',
            'company_id' => 'integer',
            'location_id' => 'integer',
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
     * Obtener el grupo empresa al que pertenece.
     */
    public function groupCompany(): BelongsTo
    {
        return $this->belongsTo(GroupCompany::class, 'group_company_id');
    }

    /**
     * Obtener el valor convertido al tipo correcto.
     */
    public function getTypedValueAttribute()
    {
        return match ($this->type) {
            'boolean' => filter_var($this->value, FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) $this->value,
            'json', 'array' => json_decode($this->value, true),
            default => $this->value,
        };
    }

    /**
     * Establecer el valor convirtiéndolo al formato correcto.
     */
    public function setValueAttribute($value): void
    {
        if (is_array($value) || is_object($value)) {
            $this->attributes['value'] = json_encode($value);
            $this->attributes['type'] = 'json';
        } elseif (is_bool($value)) {
            $this->attributes['value'] = $value ? 'true' : 'false';
            $this->attributes['type'] = 'boolean';
        } elseif (is_int($value)) {
            $this->attributes['value'] = (string) $value;
            $this->attributes['type'] = 'integer';
        } else {
            $this->attributes['value'] = $value;
        }
    }

    /**
     * Obtener un setting con herencia.
     * Busca en orden: Location → Company → Group → Tenant
     */
    public static function get(
        string $key,
        int $tenantId,
        ?int $groupCompanyId = null,
        ?int $companyId = null,
        ?int $locationId = null,
        $default = null
    ) {
        // Separar categoría y clave si viene en formato "category.key"
        if (str_contains($key, '.')) {
            [$category, $key] = explode('.', $key, 2);
        } else {
            $category = 'general';
        }

        $cacheKey = "setting:{$tenantId}:{$groupCompanyId}:{$companyId}:{$locationId}:{$category}:{$key}";

        return Cache::remember($cacheKey, 3600, function () use ($tenantId, $groupCompanyId, $companyId, $locationId, $category, $key, $default) {
            // Buscar en orden de especificidad (más específico primero)
            $scopes = [];

            // Nivel Location (más específico)
            if ($locationId) {
                $scopes[] = [
                    'tenant_id' => $tenantId,
                    'group_company_id' => $groupCompanyId,
                    'company_id' => $companyId,
                    'location_id' => $locationId,
                ];
            }

            // Nivel Company
            if ($companyId) {
                $scopes[] = [
                    'tenant_id' => $tenantId,
                    'group_company_id' => $groupCompanyId,
                    'company_id' => $companyId,
                    'location_id' => null,
                ];
            }

            // Nivel Group Company
            if ($groupCompanyId) {
                $scopes[] = [
                    'tenant_id' => $tenantId,
                    'group_company_id' => $groupCompanyId,
                    'company_id' => null,
                    'location_id' => null,
                ];
            }

            // Nivel Tenant (más general)
            $scopes[] = [
                'tenant_id' => $tenantId,
                'group_company_id' => null,
                'company_id' => null,
                'location_id' => null,
            ];

            foreach ($scopes as $scope) {
                $setting = static::where('category', $category)
                    ->where('key', $key)
                    ->where('tenant_id', $scope['tenant_id'])
                    ->where('group_company_id', $scope['group_company_id'])
                    ->where('company_id', $scope['company_id'])
                    ->where('location_id', $scope['location_id'])
                    ->first();

                if ($setting) {
                    return $setting->typed_value;
                }
            }

            return $default;
        });
    }

    /**
     * Establecer un setting en el scope especificado.
     */
    public static function set(
        string $key,
        $value,
        int $tenantId,
        ?int $groupCompanyId = null,
        ?int $companyId = null,
        ?int $locationId = null,
        ?string $displayName = null,
        ?string $description = null
    ): self {
        // Separar categoría y clave
        if (str_contains($key, '.')) {
            [$category, $key] = explode('.', $key, 2);
        } else {
            $category = 'general';
        }

        $setting = static::updateOrCreate(
            [
                'tenant_id' => $tenantId,
                'group_company_id' => $groupCompanyId,
                'company_id' => $companyId,
                'location_id' => $locationId,
                'category' => $category,
                'key' => $key,
            ],
            [
                'value' => $value,
                'display_name' => $displayName,
                'description' => $description,
            ]
        );

        // Limpiar caché
        $cacheKey = "setting:{$tenantId}:{$groupCompanyId}:{$companyId}:{$locationId}:{$category}:{$key}";
        Cache::forget($cacheKey);

        return $setting;
    }

    /**
     * Scope para filtrar por categoría.
     */
    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope para filtrar por tenant.
     */
    public function scopeForTenant($query, int $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    /**
     * Scope para nivel tenant (sin grupo/company/location).
     */
    public function scopeTenantLevel($query)
    {
        return $query->whereNull('group_company_id')
            ->whereNull('company_id')
            ->whereNull('location_id');
    }
}
