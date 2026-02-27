<?php

namespace Modules\Partners\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Empresa extends Model
{
    use SoftDeletes;

    protected $table = 'partners_empresas';

    protected $fillable = [
        'tenant_id',
        'group_company_id',
        'ruc',
        'razon_social',
        'nombre_comercial',
        'direccion',
        'email',
        'telefono',
        'estado',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'estado' => 'boolean',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(\Modules\Core\Models\Tenant::class, 'tenant_id');
    }

    public function groupCompany(): BelongsTo
    {
        return $this->belongsTo(\Modules\Core\Models\GroupCompany::class, 'group_company_id');
    }

    public function relacionesPersonas(): HasMany
    {
        return $this->hasMany(PersonaEmpresa::class, 'empresa_id');
    }

    public function personas(): BelongsToMany
    {
        return $this->belongsToMany(Persona::class, 'partners_persona_empresa', 'empresa_id', 'persona_id')
            ->withPivot(['tipo_relacion', 'es_principal', 'estado', 'created_by', 'updated_by'])
            ->withTimestamps();
    }

    public function scopeActivas($query)
    {
        return $query->where('estado', true);
    }
}
