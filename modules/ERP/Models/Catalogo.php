<?php

namespace Modules\ERP\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Catalogo extends Model
{
    use SoftDeletes;

    protected $table = 'erp_catalogos';

    /**
     * @var array<int,string>
     */
    protected $fillable = [
        'categoria_id',
        'subcategoria_id',
        'codigo',
        'descripcion',
        'estado',
        'material_id',
        'marca_id',
        'tipo_id',
        'talla_id',
        'color_id',
        'detallecolor_id',
        'clase_id',
        'genero_id',
        'presentacion_id',
        'fotocromatico_id',
        'tratamiento_id',
        'indice_id',
        'ojobifocal_id',
        'adicion_id',
        'modalidad_id',
        'cb_id',
        'o_id',
        'colorluna_id',
        'modelo_id',
        'base_id',
        'medida_id',
        'diametro_id',
        'imagen',
        'created_by',
        'updated_by',
    ];

    /**
     * @var array<string,string>
     */
    protected $casts = [
        'estado' => 'integer',
    ];

    public function scopePorCategoria(Builder $query, int $categoriaId): Builder
    {
        return $query->where('categoria_id', $categoriaId);
    }

    public function scopeActivos(Builder $query): Builder
    {
        return $query->where('estado', 1);
    }
}

