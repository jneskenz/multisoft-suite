<?php

namespace Modules\ERP\Services;

final class CatalogoFieldMap
{
    /**
     * Campos base del modal -> columnas en erp_catalogos.
     *
     * @var array<string,string>
     */
    public const BASE_FIELDS = [
        'categoria' => 'categoria_id',
        'subcategoria' => 'subcategoria_id',
        'codigo' => 'codigo',
        'descripcion' => 'descripcion',
        'estado' => 'estado',
        'imagen' => 'imagen',
    ];

    /**
     * Campos dinamicos del modal -> columnas en erp_catalogos.
     *
     * @var array<string,string>
     */
    public const CHARACTERISTIC_FIELDS = [
        'material' => 'material_id',
        'marca' => 'marca_id',
        'tipo' => 'tipo_id',
        'talla' => 'talla_id',
        'color' => 'color_id',
        'detallecolor' => 'detallecolor_id',
        'clase' => 'clase_id',
        'genero' => 'genero_id',
        'presentacion' => 'presentacion_id',
        'fotocromatico' => 'fotocromatico_id',
        'tratamiento' => 'tratamiento_id',
        'indice' => 'indice_id',
        'ojobifocal' => 'ojobifocal_id',
        'adicion' => 'adicion_id',
        'modalidad' => 'modalidad_id',
        'cb' => 'cb_id',
        'o' => 'o_id',
        'colorluna' => 'colorluna_id',
        'modelo' => 'modelo_id',
        'base' => 'base_id',
        'medida' => 'medida_id',
        'diametro' => 'diametro_id',
    ];

    /**
     * Campo del modal -> tabla catalogo fuente.
     *
     * @var array<string,string>
     */
    public const SOURCE_TABLES = [
        'subcategoria' => 'erp_subcategorias',
        'material' => 'erp_material',
        'marca' => 'erp_marcas',
        'tipo' => 'erp_tipos',
        'talla' => 'erp_tallas',
        'color' => 'erp_colores',
        'detallecolor' => 'erp_detalle_colores',
        'clase' => 'erp_clases',
        'genero' => 'erp_generos',
        'presentacion' => 'erp_unidad_medidas',
        'fotocromatico' => 'erp_fotocromaticos',
        'tratamiento' => 'erp_tratamientos',
        'indice' => 'erp_indices',
        'ojobifocal' => 'erp_ojobifocales',
        'adicion' => 'erp_adiciones',
        'modalidad' => 'erp_modalidades',
        'cb' => 'erp_cb',
        'o' => 'erp_o',
        'colorluna' => 'erp_colores',
        'modelo' => 'erp_modelos',
        'base' => 'erp_poderes',
        'medida' => 'erp_unidad_medidas',
        'diametro' => 'erp_diametros',
    ];

    /**
     * @return array<string,string>
     */
    public static function allFieldColumnMap(): array
    {
        return self::BASE_FIELDS + self::CHARACTERISTIC_FIELDS;
    }

    public static function columnByField(string $field): ?string
    {
        $field = trim($field);
        if ($field === '') {
            return null;
        }

        return self::allFieldColumnMap()[$field] ?? null;
    }

    /**
     * Construye payload persistible en erp_catalogos desde estado de modal.
     *
     * @param array<string,mixed> $values
     * @return array<string,mixed>
     */
    public static function payloadFromModal(
        int $categoriaId,
        mixed $subcategoria,
        string $codigo,
        string $descripcion,
        int $estado,
        array $values = [],
        ?string $imagen = null
    ): array {
        $payload = [
            'categoria_id' => $categoriaId > 0 ? $categoriaId : null,
            'subcategoria_id' => self::nullableInt($subcategoria),
            'codigo' => trim($codigo) !== '' ? trim($codigo) : null,
            'descripcion' => trim($descripcion) !== '' ? trim($descripcion) : null,
            'estado' => (int) $estado === 0 ? 0 : 1,
            'imagen' => $imagen !== null && trim($imagen) !== '' ? trim($imagen) : null,
        ];

        foreach (self::CHARACTERISTIC_FIELDS as $field => $column) {
            $payload[$column] = self::nullableInt($values[$field] ?? null);
        }

        return $payload;
    }

    private static function nullableInt(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        $intValue = (int) $value;

        return $intValue > 0 ? $intValue : null;
    }
}

