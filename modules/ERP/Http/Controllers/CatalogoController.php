<?php

namespace Modules\ERP\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class CatalogoController extends BaseController
{
    /**
     * Categorias que muestran el boton "Medidas" en la grilla.
     *
     * @var array<int,string>
     */
    private const CATEGORIES_WITH_MEASURES = ['LTE', 'LST', 'LCT'];

    /**
     * Relaciona codigo de categoria con el orden/slot del tab en la UI.
     */
    private const CATEGORY_SLOTS = [
        1 => 'MON',
        2 => 'LTE',
        3 => 'LST',
        4 => 'LCT',
        5 => 'SOL',
        6 => 'EST',
        7 => 'LIQ',
        8 => 'ACC',
        9 => 'EQP',
        10 => 'SER',
    ];

    /**
     * Campos dinamicos por categoria (fallback cuando no hay configuracion en BD).
     */
    private const DEFAULT_FEATURE_FIELDS = [
        'MON' => ['material', 'marca', 'tipo', 'talla', 'color', 'detallecolor', 'clase', 'genero', 'presentacion', 'imagen'],
        'LTE' => ['material', 'tipo', 'marca', 'fotocromatico', 'tratamiento', 'indice', 'ojobifocal', 'adicion', 'imagen'],
        'LST' => ['base', 'material', 'tipo', 'marca', 'fotocromatico', 'tratamiento', 'indice', 'ojobifocal', 'adicion', 'imagen'],
        'LCT' => ['material', 'tipo', 'marca', 'modalidad', 'color', 'detallecolor', 'cb', 'o', 'clase', 'presentacion', 'imagen'],
        'SOL' => ['material', 'marca', 'tipo', 'talla', 'color', 'colorluna', 'clase', 'genero', 'modelo', 'presentacion', 'imagen'],
        'EST' => ['material', 'modelo', 'marca', 'color', 'detallecolor', 'clase', 'genero', 'presentacion', 'imagen'],
        'LIQ' => ['tipo', 'marca', 'clase', 'presentacion', 'imagen'],
        'ACC' => ['modelo', 'marca', 'tipo', 'presentacion', 'imagen'],
        'EQP' => ['tipo', 'modelo', 'marca', 'presentacion', 'imagen'],
        'SER' => ['tipo', 'modelo'],
    ];

    /**
     * Orden de campos para autocompletar descripcion (fallback).
     */
    private const DEFAULT_AUTOCOMPLETE_FIELDS = [
        'MON' => ['categoria', 'subcategoria', 'material', 'marca', 'codigo', 'tipo', 'talla', 'color', 'detallecolor'],
        'LTE' => ['subcategoria', 'material', 'tipo', 'fotocromatico', 'tratamiento', 'diametro'],
        'LST' => ['material', 'tipo', 'fotocromatico', 'tratamiento', 'diametro', 'medida', 'adicion'],
        'LCT' => ['categoria', 'subcategoria', 'material', 'tipo', 'marca', 'modalidad', 'color'],
        'SOL' => ['categoria', 'subcategoria', 'material', 'marca', 'codigo', 'tipo', 'talla', 'color', 'colorluna'],
        'EST' => ['categoria', 'subcategoria', 'material', 'modelo', 'marca', 'color'],
        'LIQ' => ['categoria', 'subcategoria', 'marca'],
        'ACC' => ['subcategoria', 'modelo', 'tipo', 'marca'],
        'EQP' => ['subcategoria', 'tipo', 'modelo', 'marca'],
        'SER' => ['subcategoria', 'tipo', 'modelo'],
    ];

    public function index(): View
    {
        [$categoriaButtonIds, $catalogoOptions, $catalogoCategoryMeta, $categoriaSlotById] = $this->construirOpcionesCatalogo();
        $combinacionOptions = $this->construirOpcionesCombinacion();

        return view('erp::catalogos.index', [
            'categoriaButtonIds' => $categoriaButtonIds,
            'catalogoOptions' => $catalogoOptions,
            'catalogoCategoryMeta' => $catalogoCategoryMeta,
            'categoriaSlotById' => $categoriaSlotById,
            'categoriasConMedidas' => self::CATEGORIES_WITH_MEASURES,
            'combinacionOptions' => $combinacionOptions,
        ]);
    }

    public function show(string $catalogo): View
    {
        abort_if(!view()->exists('erp::catalogos.show'), 404);

        return view('erp::catalogos.show', compact('catalogo'));
    }

    /**
     * Construye ids de categoria usados por los tabs y opciones de selects por categoria.
     *
     * @return array{
     *   0: array<string,int>,
     *   1: array<string,array<string,array<int,array{id:mixed,nombre:string,codigo:?string}>>>,
     *   2: array<string,array{codigo:string,nombre:string,caracteristicas:array<int,string>,campos_autocompletado:array<int,string>}>,
     *   3: array<string,int>
     * }
     */
    private function construirOpcionesCatalogo(): array
    {
        if (!Schema::hasTable('erp_categorias')) {
            $defaultIds = $this->idsBotonCategoriaPorDefecto();
            $defaultSlots = [];
            foreach (self::CATEGORY_SLOTS as $slot => $code) {
                $defaultSlots[(string) $defaultIds[$code]] = $slot;
            }

            return [$defaultIds, [], $this->metadatosCategoriaPorDefecto($defaultIds), $defaultSlots];
        }

        $codes = array_values(self::CATEGORY_SLOTS);
        $categoryQuery = DB::table('erp_categorias')
            ->whereIn('codigo', $codes)
            ->select('id', 'codigo', 'nombre');

        if (Schema::hasColumn('erp_categorias', 'caracteristicas')) {
            $categoryQuery->addSelect('caracteristicas');
        }

        if (Schema::hasColumn('erp_categorias', 'campos_autocompletado')) {
            $categoryQuery->addSelect('campos_autocompletado');
        }

        $categoriesByCode = $categoryQuery->get()->keyBy('codigo');

        $categoriaButtonIds = [];
        $catalogoCategoryMeta = [];
        $categoriaSlotById = [];
        foreach (self::CATEGORY_SLOTS as $slot => $code) {
            $category = $categoriesByCode->get($code);
            // Si no encuentra la categoria en BD, mantiene el id del tab como fallback.
            $buttonCategoryId = (int) ($category->id ?? $slot);
            $categoriaButtonIds[$code] = $buttonCategoryId;

            $catalogoCategoryMeta[(string) $buttonCategoryId] = [
                'codigo' => $code,
                'nombre' => (string) ($category->nombre ?? $code),
                'caracteristicas' => $this->decodificarArregloConfiguracion(
                    $category->caracteristicas ?? null,
                    self::DEFAULT_FEATURE_FIELDS[$code] ?? []
                ),
                'campos_autocompletado' => $this->decodificarArregloConfiguracion(
                    $category->campos_autocompletado ?? null,
                    self::DEFAULT_AUTOCOMPLETE_FIELDS[$code] ?? []
                ),
            ];

            $categoriaSlotById[(string) $buttonCategoryId] = $slot;
        }

        $catalogoOptions = [];
        foreach (self::CATEGORY_SLOTS as $slot => $code) {
            $category = $categoriesByCode->get($code);
            $realCategoryId = $category ? (int) $category->id : null;
            $buttonCategoryId = $categoriaButtonIds[$code];

            $catalogoOptions[(string) $buttonCategoryId] = $this->construirOpcionesPorCategoria($realCategoryId);
        }

        return [$categoriaButtonIds, $catalogoOptions, $catalogoCategoryMeta, $categoriaSlotById];
    }

    private function idsBotonCategoriaPorDefecto(): array
    {
        $result = [];
        foreach (self::CATEGORY_SLOTS as $slot => $code) {
            $result[$code] = $slot;
        }

        return $result;
    }

    private function metadatosCategoriaPorDefecto(array $buttonIds): array
    {
        $meta = [];

        foreach ($buttonIds as $code => $id) {
            $meta[(string) $id] = [
                'codigo' => $code,
                'nombre' => $code,
                'caracteristicas' => self::DEFAULT_FEATURE_FIELDS[$code] ?? [],
                'campos_autocompletado' => self::DEFAULT_AUTOCOMPLETE_FIELDS[$code] ?? [],
            ];
        }

        return $meta;
    }

    private function decodificarArregloConfiguracion(mixed $value, array $default = []): array
    {
        if (is_array($value)) {
            return $this->normalizarArregloCadenas($value, $default);
        }

        if (is_string($value) && trim($value) !== '') {
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $this->normalizarArregloCadenas($decoded, $default);
            }
        }

        return $default;
    }

    private function normalizarArregloCadenas(array $values, array $default = []): array
    {
        $normalized = array_values(array_filter(array_map(
            fn($item) => is_string($item) ? trim($item) : '',
            $values
        )));

        return $normalized !== [] ? $normalized : $default;
    }

    private function construirOpcionesPorCategoria(?int $categoriaId): array
    {
        return [
            'subcategoria' => $this->obtenerOpciones('erp_subcategorias', $categoriaId),
            'material' => $this->obtenerOpciones('erp_material', $categoriaId),
            'marca' => $this->obtenerOpciones('erp_marcas', $categoriaId),
            'tipo' => $this->obtenerOpciones('erp_tipos', $categoriaId),
            'talla' => $this->obtenerOpciones('erp_tallas', $categoriaId),
            'color' => $this->obtenerOpciones('erp_colores', $categoriaId),
            'detallecolor' => $this->obtenerOpciones('erp_detalle_colores', $categoriaId),
            'clase' => $this->obtenerOpciones('erp_clases', $categoriaId),
            'genero' => $this->obtenerOpciones('erp_generos'),
            'presentacion' => $this->obtenerOpciones('erp_unidad_medidas'),
            'fotocromatico' => $this->obtenerOpciones('erp_fotocromaticos', $categoriaId),
            'tratamiento' => $this->obtenerOpciones('erp_tratamientos', $categoriaId),
            'diametro' => $this->obtenerOpciones('erp_diametros', $categoriaId),
            'medida' => $this->obtenerOpciones('erp_unidad_medidas'),
            'indice' => $this->obtenerOpciones('erp_indices', $categoriaId),
            'ojobifocal' => $this->obtenerOpciones('erp_ojobifocales', $categoriaId),
            'adicion' => $this->obtenerOpciones('erp_adiciones', $categoriaId),
            'modalidad' => $this->obtenerOpciones('erp_modalidades'),
            'cb' => $this->obtenerOpciones('erp_cb'),
            'o' => $this->obtenerOpciones('erp_o'),
            'modelo' => $this->obtenerOpciones('erp_modelos', $categoriaId),
            'base' => $this->obtenerOpciones('erp_poderes'),
            'colorluna' => $this->obtenerOpciones('erp_colores', $categoriaId),
            'imagen' => [],
        ];
    }

    private function obtenerOpciones(string $table, ?int $categoriaId = null): array
    {
        if (!Schema::hasTable($table)) {
            return [];
        }

        $query = DB::table($table)->select('id', 'nombre');

        if (Schema::hasColumn($table, 'codigo')) {
            $query->addSelect('codigo');
        }

        if (Schema::hasColumn($table, 'color_id')) {
            $query->addSelect('color_id');
        }

        if (Schema::hasColumn($table, 'deleted_at')) {
            $query->whereNull('deleted_at');
        }

        $categoriaColumn = $this->resolverColumnaCategoria($table);
        if ($categoriaColumn) {
            if ($categoriaId === null) {
                return [];
            }

            $query->where($categoriaColumn, $categoriaId);
        }

        return $query->orderBy('nombre')->get()
            ->map(fn($item) => [
                'id' => $item->id,
                'nombre' => $item->nombre,
                'codigo' => $item->codigo ?? null,
                'color_id' => isset($item->color_id) ? (string) $item->color_id : null,
            ])
            ->values()
            ->all();
    }

    private function resolverColumnaCategoria(string $table): ?string
    {
        if (Schema::hasColumn($table, 'categoria_id')) {
            return 'categoria_id';
        }

        if (Schema::hasColumn($table, 'erp_categoria_id')) {
            return 'erp_categoria_id';
        }

        return null;
    }

    /**
     * @return array{
     *   serie_visual: array<int,array{id:mixed,nombre:string,codigo:?string,serie_visual_id:?int,categoria_id:?int}>,
     *   subserie_visual: array<int,array{id:mixed,nombre:string,codigo:?string,serie_visual_id:?int,categoria_id:?int}>,
     *   medida_esferica: array<int,array{id:mixed,nombre:string,codigo:?string,serie_visual_id:?int,categoria_id:?int}>,
     *   medida_cilindrica: array<int,array{id:mixed,nombre:string,codigo:?string,serie_visual_id:?int,categoria_id:?int}>,
     *   adicion: array<int,array{id:mixed,nombre:string,codigo:?string,serie_visual_id:?int,categoria_id:?int}>
     * }
     */
    private function construirOpcionesCombinacion(): array
    {
        return [
            'serie_visual' => $this->obtenerOpcionesCombinacion('erp_serie_visual'),
            'subserie_visual' => $this->obtenerOpcionesCombinacion('erp_subserie_visual'),
            'medida_esferica' => $this->obtenerOpcionesCombinacion('erp_medida_esferica'),
            'medida_cilindrica' => $this->obtenerOpcionesCombinacion('erp_medida_cilindrica'),
            'adicion' => $this->obtenerOpcionesAdicion(),
        ];
    }

    /**
     * Compatibilidad: usa erp_adicion (nuevo) y cae a erp_adiciones (catalogos) si no hay datos.
     *
     * @return array<int,array{id:mixed,nombre:string,codigo:?string,serie_visual_id:?int,categoria_id:?int}>
     */
    private function obtenerOpcionesAdicion(): array
    {
        $adicionRows = $this->obtenerOpcionesCombinacion('erp_adicion');
        if ($adicionRows !== []) {
            return $adicionRows;
        }

        return $this->obtenerOpcionesCombinacion('erp_adiciones');
    }

    /**
     * @return array<int,array{id:mixed,nombre:string,codigo:?string,serie_visual_id:?int,categoria_id:?int}>
     */
    private function obtenerOpcionesCombinacion(string $table): array
    {
        if (!Schema::hasTable($table)) {
            return [];
        }

        $query = DB::table($table)->select('id', 'nombre');

        if (Schema::hasColumn($table, 'codigo')) {
            $query->addSelect('codigo');
        }

        if (Schema::hasColumn($table, 'serie_visual_id')) {
            $query->addSelect('serie_visual_id');
        } elseif (Schema::hasColumn($table, 'erp_serie_visual_id')) {
            $query->addSelect(DB::raw('erp_serie_visual_id as serie_visual_id'));
        }

        if (Schema::hasColumn($table, 'categoria_id')) {
            $query->addSelect('categoria_id');
        } elseif (Schema::hasColumn($table, 'erp_categoria_id')) {
            $query->addSelect(DB::raw('erp_categoria_id as categoria_id'));
        }

        if (Schema::hasColumn($table, 'deleted_at')) {
            $query->whereNull('deleted_at');
        }

        if (Schema::hasColumn($table, 'estado')) {
            $query->where('estado', true);
        }

        $query->orderBy('nombre');

        return $query->get()->map(
            fn($item) => [
                'id' => $item->id,
                'nombre' => (string) ($item->nombre ?? ''),
                'codigo' => isset($item->codigo) ? (string) $item->codigo : null,
                'serie_visual_id' => isset($item->serie_visual_id) && $item->serie_visual_id !== null
                    ? (int) $item->serie_visual_id
                    : null,
                'categoria_id' => isset($item->categoria_id) && $item->categoria_id !== null
                    ? (int) $item->categoria_id
                    : null,
            ]
        )->values()->all();
    }

}
