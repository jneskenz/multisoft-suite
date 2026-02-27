<?php

namespace Modules\Partners\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Persona extends Model
{
    use SoftDeletes;

    protected $table = 'partners_personas';

    protected $fillable = [
        'tenant_id',
        'group_company_id',
        'tipo_documento',
        'numero_documento',
        'nombres',
        'apellido_paterno',
        'apellido_materno',
        'nombre_completo',
        'email',
        'telefono',
        'fecha_nacimiento',
        'estado',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'fecha_nacimiento' => 'date',
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

    public function tipos(): HasMany
    {
        return $this->hasMany(TipoPersona::class, 'persona_id');
    }

    public function relacionesEmpresas(): HasMany
    {
        return $this->hasMany(PersonaEmpresa::class, 'persona_id');
    }

    public function empresas(): BelongsToMany
    {
        return $this->belongsToMany(Empresa::class, 'partners_persona_empresa', 'persona_id', 'empresa_id')
            ->withPivot(['tipo_relacion', 'es_principal', 'estado', 'created_by', 'updated_by'])
            ->withTimestamps();
    }

    public function scopeActivos($query)
    {
        return $query->where('estado', true);
    }

    public function scopeConTipo($query, string $tipo)
    {
        return $query->whereHas('tipos', function ($q) use ($tipo) {
            $q->where('tipo', $tipo)->where('estado', true);
        });
    }
}
