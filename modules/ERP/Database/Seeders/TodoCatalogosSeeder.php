<?php

namespace Modules\ERP\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TodoCatalogosSeeder extends Seeder
{
    public function run(): void
    {
        if (!Schema::hasTable('erp_tipo_categorias') || !Schema::hasTable('erp_categorias')) {
            $this->command?->warn('Tablas ERP de catalogos no encontradas. Ejecuta migraciones primero.');

            return;
        }

        $this->seedTipoCategorias();
        $this->seedCategorias();

        $categoriaIds = DB::table('erp_categorias')->pluck('id', 'codigo')->all();

        $this->seedSubcategorias($categoriaIds);
        $this->seedMateriales($categoriaIds);
        $this->seedTipos($categoriaIds);
        $this->seedMarcas($categoriaIds);
        $this->seedTallas($categoriaIds);
        $this->seedColores($categoriaIds);
        $this->seedDetalleColores($categoriaIds);
        $this->seedClases($categoriaIds);
        $this->seedGeneros();
        $this->seedUnidadMedidas();
        $this->seedFotocromaticos($categoriaIds);
        $this->seedTratamientos($categoriaIds);
        $this->seedIndices($categoriaIds);
        $this->seedOjoBifocales($categoriaIds);
        $this->seedDiametros($categoriaIds);
        $this->seedAdiciones($categoriaIds);
        $this->seedModalidades();
        $this->seedPoderes();
        $this->seedCb();
        $this->seedO();
        $this->seedModelos($categoriaIds);

        $this->command?->info('Seeder ERP todo_catalogos ejecutado correctamente.');
    }

    private function seedTipoCategorias(): void
    {
        $rows = [
            [
                'codigo' => 'PROD',
                'nombre' => 'PRODUCTO',
                'descripcion' => 'Catalogos para articulos de inventario',
                'estado' => 1,
            ],
            [
                'codigo' => 'SERV',
                'nombre' => 'SERVICIO',
                'descripcion' => 'Catalogos para servicios',
                'estado' => 1,
            ],
        ];

        $this->upsertByCodigo('erp_tipo_categorias', $rows);
    }

    private function seedCategorias(): void
    {
        $tipoIds = DB::table('erp_tipo_categorias')->pluck('id', 'codigo')->all();

        $rows = [
            [
                'codigo' => 'MON',
                'nombre' => 'MONTURA',
                'descripcion' => 'Catalogo de monturas',
                'tipo_categoria_id' => $tipoIds['PROD'] ?? null,
                'campos_autocompletado' => ['categoria', 'subcategoria', 'material', 'marca', 'codigo', 'tipo', 'talla', 'color', 'detallecolor'],
                'caracteristicas' => ['material', 'marca', 'tipo', 'talla', 'color', 'detallecolor', 'clase', 'genero', 'presentacion', 'imagen'],
                'estado' => 1,
            ],
            [
                'codigo' => 'LTE',
                'nombre' => 'LENTES TERMINADOS',
                'descripcion' => 'Catalogo de lentes terminados',
                'tipo_categoria_id' => $tipoIds['PROD'] ?? null,
                'campos_autocompletado' => ['subcategoria', 'material', 'tipo', 'fotocromatico', 'tratamiento', 'diametro'],
                'caracteristicas' => ['material', 'tipo', 'marca', 'fotocromatico', 'tratamiento', 'indice', 'ojobifocal', 'adicion', 'imagen'],
                'estado' => 1,
            ],
            [
                'codigo' => 'LST',
                'nombre' => 'LENTES SEMI TERMINADOS',
                'descripcion' => 'Catalogo de lentes semi terminados',
                'tipo_categoria_id' => $tipoIds['PROD'] ?? null,
                'campos_autocompletado' => ['material', 'tipo', 'fotocromatico', 'tratamiento', 'diametro', 'medida', 'adicion'],
                'caracteristicas' => ['base', 'material', 'tipo', 'marca', 'fotocromatico', 'tratamiento', 'indice', 'ojobifocal', 'adicion', 'imagen'],
                'estado' => 1,
            ],
            [
                'codigo' => 'LCT',
                'nombre' => 'LENTES DE CONTACTO',
                'descripcion' => 'Catalogo de lentes de contacto',
                'tipo_categoria_id' => $tipoIds['PROD'] ?? null,
                'campos_autocompletado' => ['categoria', 'subcategoria', 'material', 'tipo', 'marca', 'modalidad', 'color'],
                'caracteristicas' => ['material', 'tipo', 'marca', 'modalidad', 'color', 'detallecolor', 'cb', 'o', 'clase', 'presentacion', 'imagen'],
                'estado' => 1,
            ],
            [
                'codigo' => 'SOL',
                'nombre' => 'SOLAR',
                'descripcion' => 'Catalogo de lentes solares',
                'tipo_categoria_id' => $tipoIds['PROD'] ?? null,
                'campos_autocompletado' => ['categoria', 'subcategoria', 'material', 'marca', 'codigo', 'tipo', 'talla', 'color', 'colorluna'],
                'caracteristicas' => ['material', 'marca', 'tipo', 'talla', 'color', 'colorluna', 'clase', 'genero', 'modelo', 'presentacion', 'imagen'],
                'estado' => 1,
            ],
            [
                'codigo' => 'EST',
                'nombre' => 'ESTUCHE',
                'descripcion' => 'Catalogo de estuches',
                'tipo_categoria_id' => $tipoIds['PROD'] ?? null,
                'campos_autocompletado' => ['categoria', 'subcategoria', 'material', 'modelo', 'marca', 'color'],
                'caracteristicas' => ['material', 'modelo', 'marca', 'color', 'detallecolor', 'clase', 'genero', 'presentacion', 'imagen'],
                'estado' => 1,
            ],
            [
                'codigo' => 'LIQ',
                'nombre' => 'LIQUIDOS',
                'descripcion' => 'Catalogo de liquidos',
                'tipo_categoria_id' => $tipoIds['PROD'] ?? null,
                'campos_autocompletado' => ['categoria', 'subcategoria', 'marca'],
                'caracteristicas' => ['tipo', 'marca', 'clase', 'presentacion', 'imagen'],
                'estado' => 1,
            ],
            [
                'codigo' => 'ACC',
                'nombre' => 'ACCESORIOS',
                'descripcion' => 'Catalogo de accesorios',
                'tipo_categoria_id' => $tipoIds['PROD'] ?? null,
                'campos_autocompletado' => ['subcategoria', 'modelo', 'tipo', 'marca'],
                'caracteristicas' => ['modelo', 'marca', 'tipo', 'presentacion', 'imagen'],
                'estado' => 1,
            ],
            [
                'codigo' => 'EQP',
                'nombre' => 'EQUIPOS',
                'descripcion' => 'Catalogo de equipos',
                'tipo_categoria_id' => $tipoIds['PROD'] ?? null,
                'campos_autocompletado' => ['subcategoria', 'tipo', 'modelo', 'marca'],
                'caracteristicas' => ['tipo', 'modelo', 'marca', 'presentacion', 'imagen'],
                'estado' => 1,
            ],
            [
                'codigo' => 'SER',
                'nombre' => 'SERVICIO',
                'descripcion' => 'Catalogo de servicios',
                'tipo_categoria_id' => $tipoIds['SERV'] ?? null,
                'campos_autocompletado' => ['subcategoria', 'tipo', 'modelo'],
                'caracteristicas' => ['tipo', 'modelo'],
                'estado' => 1,
            ],
        ];

        $this->upsertByCodigo('erp_categorias', $rows);
    }

    private function seedSubcategorias(array $categoriaIds): void
    {
        $rows = [
            ['codigo' => 'OFT', 'nombre' => 'OFTALMICA', 'categoria_id' => $categoriaIds['MON'] ?? null, 'estado' => 1],
            ['codigo' => 'SOBR', 'nombre' => 'CON SOBRELENTE', 'categoria_id' => $categoriaIds['MON'] ?? null, 'estado' => 1],
            ['codigo' => 'LECTORES1', 'nombre' => 'LECTORES1', 'categoria_id' => $categoriaIds['MON'] ?? null, 'estado' => 1],
            ['codigo' => 'DEP', 'nombre' => 'DEPORTIVA', 'categoria_id' => $categoriaIds['MON'] ?? null, 'estado' => 1],

            ['codigo' => 'LNTS-DIG', 'nombre' => 'LENTES DIGITALES', 'categoria_id' => $categoriaIds['LTE'] ?? null, 'estado' => 1],
            ['codigo' => 'LNTS-CONV', 'nombre' => 'LENTES CONVENSIONALES', 'categoria_id' => $categoriaIds['LTE'] ?? null, 'estado' => 1],

            ['codigo' => 'MONOF', 'nombre' => 'MONOFOCAL', 'categoria_id' => $categoriaIds['LCT'] ?? null, 'estado' => 1],
            ['codigo' => 'BIF', 'nombre' => 'BIFOCAL', 'categoria_id' => $categoriaIds['LCT'] ?? null, 'estado' => 1],
            ['codigo' => 'MULTIF', 'nombre' => 'MULTIFOCAL', 'categoria_id' => $categoriaIds['LCT'] ?? null, 'estado' => 1],
            ['codigo' => 'TERAP', 'nombre' => 'TERAPEUTICO', 'categoria_id' => $categoriaIds['LCT'] ?? null, 'estado' => 1],

            ['codigo' => 'DEP', 'nombre' => 'DEPORTIVOS', 'categoria_id' => $categoriaIds['SOL'] ?? null, 'estado' => 1],
            ['codigo' => 'SEG', 'nombre' => 'SEGURIDAD', 'categoria_id' => $categoriaIds['SOL'] ?? null, 'estado' => 1],
            
            ['codigo' => 'SUB-EST-GEN', 'nombre' => 'GENERAL 6', 'categoria_id' => $categoriaIds['EST'] ?? null, 'estado' => 1],
            ['codigo' => 'SUB-LIQ-GEN', 'nombre' => 'GENERAL 7', 'categoria_id' => $categoriaIds['LIQ'] ?? null, 'estado' => 1],
            ['codigo' => 'SUB-ACC-GEN', 'nombre' => 'GENERAL 8', 'categoria_id' => $categoriaIds['ACC'] ?? null, 'estado' => 1],
            ['codigo' => 'SUB-EQP-GEN', 'nombre' => 'GENERAL 9', 'categoria_id' => $categoriaIds['EQP'] ?? null, 'estado' => 1],
            ['codigo' => 'SUB-SER-GEN', 'nombre' => 'GENERAL 10', 'categoria_id' => $categoriaIds['SER'] ?? null, 'estado' => 1],
        ];

        $this->upsertByCodigo('erp_subcategorias', $rows);
    }

    private function seedMateriales(array $categoriaIds): void
    {
        $rows = [
            ['codigo' => 'RSN', 'nombre' => 'RESINA', 'categoria_id' => $categoriaIds['MON'] ?? null, 'estado' => 1],
            ['codigo' => 'CTLS', 'nombre' => 'CRISTALES', 'categoria_id' => $categoriaIds['MON'] ?? null, 'estado' => 1],
            ['codigo' => 'ACET', 'nombre' => 'ACETATO', 'categoria_id' => $categoriaIds['MON'] ?? null, 'estado' => 1],
            ['codigo' => 'ACET-MET', 'nombre' => 'ACETATO/METAL', 'categoria_id' => $categoriaIds['MON'] ?? null, 'estado' => 1],
            ['codigo' => 'ALUM', 'nombre' => 'ALUMINIO', 'categoria_id' => $categoriaIds['MON'] ?? null, 'estado' => 1],
            ['codigo' => 'MET', 'nombre' => 'METAL', 'categoria_id' => $categoriaIds['MON'] ?? null, 'estado' => 1],
            ['codigo' => 'SIL', 'nombre' => 'SILICONA', 'categoria_id' => $categoriaIds['MON'] ?? null, 'estado' => 1],
            ['codigo' => 'FCB', 'nombre' => 'FIBRA DE CARBONO', 'categoria_id' => $categoriaIds['MON'] ?? null, 'estado' => 1],
            ['codigo' => 'POL', 'nombre' => 'POLICARBONATO', 'categoria_id' => $categoriaIds['MON'] ?? null, 'estado' => 1],
            ['codigo' => 'PLA', 'nombre' => 'PLASTICO', 'categoria_id' => $categoriaIds['MON'] ?? null, 'estado' => 1],
            ['codigo' => 'FLE', 'nombre' => 'FLEXON', 'categoria_id' => $categoriaIds['MON'] ?? null, 'estado' => 1],
            ['codigo' => 'TR90', 'nombre' => 'TR90', 'categoria_id' => $categoriaIds['MON'] ?? null, 'estado' => 1],
            ['codigo' => 'TIT', 'nombre' => 'TITANIO', 'categoria_id' => $categoriaIds['MON'] ?? null, 'estado' => 1],
            ['codigo' => 'CAY', 'nombre' => 'CAREY', 'categoria_id' => $categoriaIds['MON'] ?? null, 'estado' => 1],

            ['codigo' => 'RSN', 'nombre' => 'RESINA', 'categoria_id' => $categoriaIds['LTE'] ?? null, 'estado' => 1],
            ['codigo' => 'CTLS', 'nombre' => 'CRISTALES', 'categoria_id' => $categoriaIds['LTE'] ?? null, 'estado' => 1],
            ['codigo' => 'POL', 'nombre' => 'POLICARBONATO', 'categoria_id' => $categoriaIds['LTE'] ?? null, 'estado' => 1],

            ['codigo' => 'BLND-SIL', 'nombre' => 'BLANDOS SILICONA', 'categoria_id' => $categoriaIds['LCT'] ?? null, 'estado' => 1],
            ['codigo' => 'HID-SIL', 'nombre' => 'HIDROGEL DE SILICONA', 'categoria_id' => $categoriaIds['LCT'] ?? null, 'estado' => 1],
            ['codigo' => 'RGP', 'nombre' => 'RGP', 'categoria_id' => $categoriaIds['LCT'] ?? null, 'estado' => 1],

            ['codigo' => 'ACET', 'nombre' => 'ACETATO', 'categoria_id' => $categoriaIds['SOL'] ?? null, 'estado' => 1],
            ['codigo' => 'CAY', 'nombre' => 'CAREY', 'categoria_id' => $categoriaIds['SOL'] ?? null, 'estado' => 1],
            ['codigo' => 'MET', 'nombre' => 'METAL', 'categoria_id' => $categoriaIds['SOL'] ?? null, 'estado' => 1],
            ['codigo' => 'TR-90', 'nombre' => 'TR 90', 'categoria_id' => $categoriaIds['SOL'] ?? null, 'estado' => 1],
            ['codigo' => 'ALUM', 'nombre' => 'ALUMINIO', 'categoria_id' => $categoriaIds['SOL'] ?? null, 'estado' => 1],

            ['codigo' => 'NGN', 'nombre' => 'NINGUNO', 'categoria_id' => $categoriaIds['EST'] ?? null, 'estado' => 1],
            ['codigo' => 'ACR', 'nombre' => 'ACRILICO', 'categoria_id' => $categoriaIds['EST'] ?? null, 'estado' => 1],
            ['codigo' => 'CUER', 'nombre' => 'CUERO', 'categoria_id' => $categoriaIds['EST'] ?? null, 'estado' => 1],
            ['codigo' => 'CUERN', 'nombre' => 'CUERINA', 'categoria_id' => $categoriaIds['EST'] ?? null, 'estado' => 1],
            ['codigo' => 'TEL', 'nombre' => 'TELA', 'categoria_id' => $categoriaIds['EST'] ?? null, 'estado' => 1],
            ['codigo' => 'DUR', 'nombre' => 'DURO', 'categoria_id' => $categoriaIds['EST'] ?? null, 'estado' => 1],
        ];

        $this->upsertByCodigo('erp_material', $rows);
    }

    private function seedTipos(array $categoriaIds): void
    {
        $rows = [
            ['codigo' => 'TIP-MON-COM', 'nombre' => 'COMPLETA', 'categoria_id' => $categoriaIds['MON'] ?? null, 'estado' => 1],
            ['codigo' => 'TIP-LTE-MONO', 'nombre' => 'MONOFOCAL', 'categoria_id' => $categoriaIds['LTE'] ?? null, 'estado' => 1],
            ['codigo' => 'TIP-LCT-DIAR', 'nombre' => 'DIARIO', 'categoria_id' => $categoriaIds['LCT'] ?? null, 'estado' => 1],
            ['codigo' => 'TIP-SOL-PLAR', 'nombre' => 'POLARIZADO', 'categoria_id' => $categoriaIds['SOL'] ?? null, 'estado' => 1],
            ['codigo' => 'TIP-LIQ-MULT', 'nombre' => 'MULTIPROPOSITO', 'categoria_id' => $categoriaIds['LIQ'] ?? null, 'estado' => 1],
            ['codigo' => 'TIP-ACC-LIMP', 'nombre' => 'LIMPIEZA', 'categoria_id' => $categoriaIds['ACC'] ?? null, 'estado' => 1],
            ['codigo' => 'TIP-EQP-DIAG', 'nombre' => 'DIAGNOSTICO', 'categoria_id' => $categoriaIds['EQP'] ?? null, 'estado' => 1],
            ['codigo' => 'TIP-LST-MONO', 'nombre' => 'MONOFOCAL', 'categoria_id' => $categoriaIds['LST'] ?? null, 'estado' => 1],
            ['codigo' => 'TIP-SER-CONS', 'nombre' => 'CONSULTA', 'categoria_id' => $categoriaIds['SER'] ?? null, 'estado' => 1],
        ];

        $this->upsertByCodigo('erp_tipos', $rows);
    }

    private function seedMarcas(array $categoriaIds): void
    {
        $rows = [
            ['codigo' => 'MAR-MON-001', 'nombre' => 'MULTILENS', 'categoria_id' => $categoriaIds['MON'] ?? null, 'estado' => 1],
            ['codigo' => 'MAR-LTE-001', 'nombre' => 'ESSILOR', 'categoria_id' => $categoriaIds['LTE'] ?? null, 'estado' => 1],
            ['codigo' => 'MAR-LCT-001', 'nombre' => 'ACUVUE', 'categoria_id' => $categoriaIds['LCT'] ?? null, 'estado' => 1],
            ['codigo' => 'MAR-SOL-001', 'nombre' => 'RAYBAN', 'categoria_id' => $categoriaIds['SOL'] ?? null, 'estado' => 1],
            ['codigo' => 'MAR-EST-001', 'nombre' => 'CASEPRO', 'categoria_id' => $categoriaIds['EST'] ?? null, 'estado' => 1],
            ['codigo' => 'MAR-LIQ-001', 'nombre' => 'OPTIFREE', 'categoria_id' => $categoriaIds['LIQ'] ?? null, 'estado' => 1],
            ['codigo' => 'MAR-ACC-001', 'nombre' => 'EYECARE', 'categoria_id' => $categoriaIds['ACC'] ?? null, 'estado' => 1],
            ['codigo' => 'MAR-EQP-001', 'nombre' => 'TOPCON', 'categoria_id' => $categoriaIds['EQP'] ?? null, 'estado' => 1],
            ['codigo' => 'MAR-LST-001', 'nombre' => 'ESSILOR', 'categoria_id' => $categoriaIds['LST'] ?? null, 'estado' => 1],
        ];

        $this->upsertByCodigo('erp_marcas', $rows);
    }

    private function seedTallas(array $categoriaIds): void
    {
        $rows = [
            ['codigo' => 'TAL-MON-52', 'nombre' => '52-18-140', 'categoria_id' => $categoriaIds['MON'] ?? null, 'estado' => 1],
            ['codigo' => 'TAL-SOL-55', 'nombre' => '55-17-145', 'categoria_id' => $categoriaIds['SOL'] ?? null, 'estado' => 1],
        ];

        $this->upsertByCodigo('erp_tallas', $rows);
    }

    private function seedColores(array $categoriaIds): void
    {
        $rows = [
            ['codigo' => 'COL-MON-NEG', 'nombre' => 'NEGRO', 'categoria_id' => $categoriaIds['MON'] ?? null, 'estado' => 1],
            ['codigo' => 'COL-LCT-AZU', 'nombre' => 'AZUL', 'categoria_id' => $categoriaIds['LCT'] ?? null, 'estado' => 1],
            ['codigo' => 'COL-SOL-MAR', 'nombre' => 'MARRON', 'categoria_id' => $categoriaIds['SOL'] ?? null, 'estado' => 1],
            ['codigo' => 'COL-EST-GRI', 'nombre' => 'GRIS', 'categoria_id' => $categoriaIds['EST'] ?? null, 'estado' => 1],
        ];

        $this->upsertByCodigo('erp_colores', $rows);
    }

    private function seedDetalleColores(array $categoriaIds): void
    {
        $colorIds = DB::table('erp_colores')->pluck('id', 'codigo')->all();

        $rows = [
            ['codigo' => 'DET-MON-MATE', 'nombre' => 'NEGRO MATE', 'categoria_id' => $categoriaIds['MON'] ?? null, 'color_id' => $colorIds['COL-MON-NEG'] ?? null, 'estado' => 1],
            ['codigo' => 'DET-LCT-CLAR', 'nombre' => 'AZUL CLARO', 'categoria_id' => $categoriaIds['LCT'] ?? null, 'color_id' => $colorIds['COL-LCT-AZU'] ?? null, 'estado' => 1],
            ['codigo' => 'DET-SOL-OSCU', 'nombre' => 'MARRON OSCURO', 'categoria_id' => $categoriaIds['SOL'] ?? null, 'color_id' => $colorIds['COL-SOL-MAR'] ?? null, 'estado' => 1],
            ['codigo' => 'DET-EST-CLAR', 'nombre' => 'GRIS CLARO', 'categoria_id' => $categoriaIds['EST'] ?? null, 'color_id' => $colorIds['COL-EST-GRI'] ?? null, 'estado' => 1],
        ];

        $this->upsertByCodigo('erp_detalle_colores', $rows);
    }

    private function seedClases(array $categoriaIds): void
    {
        $rows = [
            ['codigo' => 'CLA-MON-ADU', 'nombre' => 'ADULTO', 'categoria_id' => $categoriaIds['MON'] ?? null, 'estado' => 1],
            ['codigo' => 'CLA-LCT-ADU', 'nombre' => 'ADULTO', 'categoria_id' => $categoriaIds['LCT'] ?? null, 'estado' => 1],
            ['codigo' => 'CLA-SOL-ADU', 'nombre' => 'ADULTO', 'categoria_id' => $categoriaIds['SOL'] ?? null, 'estado' => 1],
            ['codigo' => 'CLA-EST-ADU', 'nombre' => 'ADULTO', 'categoria_id' => $categoriaIds['EST'] ?? null, 'estado' => 1],
            ['codigo' => 'CLA-LIQ-UNI', 'nombre' => 'UNISEX', 'categoria_id' => $categoriaIds['LIQ'] ?? null, 'estado' => 1],
        ];

        $this->upsertByCodigo('erp_clases', $rows);
    }

    private function seedGeneros(): void
    {
        $rows = [
            ['codigo' => 'GEN-MAS', 'nombre' => 'MASCULINO', 'estado' => 1],
            ['codigo' => 'GEN-FEM', 'nombre' => 'FEMENINO', 'estado' => 1],
            ['codigo' => 'GEN-UNI', 'nombre' => 'UNISEX', 'estado' => 1],
        ];

        $this->upsertByCodigo('erp_generos', $rows);
    }

    private function seedUnidadMedidas(): void
    {
        $rows = [
            ['codigo' => 'UM-UN', 'nombre' => 'UNIDAD', 'estado' => 1],
            ['codigo' => 'UM-PAR', 'nombre' => 'PAR', 'estado' => 1],
            ['codigo' => 'UM-FRA', 'nombre' => 'FRASCO', 'estado' => 1],
        ];

        $this->upsertByCodigo('erp_unidad_medidas', $rows);
    }

    private function seedFotocromaticos(array $categoriaIds): void
    {
        $rows = [
            ['codigo' => 'FOTO-LTE-SI', 'nombre' => 'SI', 'categoria_id' => $categoriaIds['LTE'] ?? null, 'estado' => 1],
            ['codigo' => 'FOTO-LST-SI', 'nombre' => 'SI', 'categoria_id' => $categoriaIds['LST'] ?? null, 'estado' => 1],
        ];

        $this->upsertByCodigo('erp_fotocromaticos', $rows);
    }

    private function seedTratamientos(array $categoriaIds): void
    {
        $rows = [
            ['codigo' => 'TRA-LTE-AR', 'nombre' => 'ANTIREFLEJO', 'categoria_id' => $categoriaIds['LTE'] ?? null],
            ['codigo' => 'TRA-LST-AR', 'nombre' => 'ANTIREFLEJO', 'categoria_id' => $categoriaIds['LST'] ?? null],
        ];

        $this->upsertByCodigo('erp_tratamientos', $rows);
    }

    private function seedIndices(array $categoriaIds): void
    {
        $rows = [
            ['codigo' => 'IND-LTE-150', 'nombre' => '1.50', 'categoria_id' => $categoriaIds['LTE'] ?? null, 'estado' => 1],
            ['codigo' => 'IND-LST-150', 'nombre' => '1.50', 'categoria_id' => $categoriaIds['LST'] ?? null, 'estado' => 1],
        ];

        $this->upsertByCodigo('erp_indices', $rows);
    }

    private function seedOjoBifocales(array $categoriaIds): void
    {
        $rows = [
            ['codigo' => 'OBJ-LTE-NO', 'nombre' => 'NO', 'categoria_id' => $categoriaIds['LTE'] ?? null, 'estado' => 1],
            ['codigo' => 'OBJ-LST-NO', 'nombre' => 'NO', 'categoria_id' => $categoriaIds['LST'] ?? null, 'estado' => 1],
        ];

        $this->upsertByCodigo('erp_ojobifocales', $rows);
    }

    private function seedDiametros(array $categoriaIds): void
    {
        $rows = [
            ['codigo' => 'DIA-LTE-65', 'nombre' => '65', 'categoria_id' => $categoriaIds['LTE'] ?? null, 'estado' => 1],
            ['codigo' => 'DIA-LST-65', 'nombre' => '65', 'categoria_id' => $categoriaIds['LST'] ?? null, 'estado' => 1],
        ];

        $this->upsertByCodigo('erp_diametros', $rows);
    }

    private function seedAdiciones(array $categoriaIds): void
    {
        $rows = [
            ['codigo' => 'ADI-LTE-100', 'nombre' => '+1.00', 'categoria_id' => $categoriaIds['LCT'] ?? null, 'estado' => 1],
            ['codigo' => 'ADI-LST-100', 'nombre' => '+1.00', 'categoria_id' => $categoriaIds['LST'] ?? null, 'estado' => 1],
        ];

        $this->upsertByCodigo('erp_adiciones', $rows);
    }

    private function seedModalidades(): void
    {
        $rows = [
            ['codigo' => 'MOD-MEN', 'nombre' => 'MENSUAL', 'estado' => 1],
            ['codigo' => 'MOD-TRI', 'nombre' => 'TRIMESTRAL', 'estado' => 1],
        ];

        $this->upsertByCodigo('erp_modalidades', $rows);
    }

    private function seedPoderes(): void
    {
        $rows = [
            ['codigo' => 'POD-050', 'nombre' => '-0.50', 'estado' => 1],
            ['codigo' => 'POD-100', 'nombre' => '-1.00', 'estado' => 1],
            ['codigo' => 'POD-150', 'nombre' => '-1.50', 'estado' => 1],
        ];

        $this->upsertByCodigo('erp_poderes', $rows);
    }

    private function seedCb(): void
    {
        $rows = [
            ['codigo' => 'CB-84', 'nombre' => '8.4', 'estado' => 1],
            ['codigo' => 'CB-86', 'nombre' => '8.6', 'estado' => 1],
        ];

        $this->upsertByCodigo('erp_cb', $rows);
    }

    private function seedO(): void
    {
        $rows = [
            ['codigo' => 'O-142', 'nombre' => '14.2', 'estado' => 1],
            ['codigo' => 'O-145', 'nombre' => '14.5', 'estado' => 1],
        ];

        $this->upsertByCodigo('erp_o', $rows);
    }

    private function seedModelos(array $categoriaIds): void
    {
        $rows = [
            ['codigo' => 'MOD-SOL-AVI', 'nombre' => 'AVIADOR', 'categoria_id' => $categoriaIds['SOL'] ?? null, 'estado' => 1],
            ['codigo' => 'MOD-EST-RIG', 'nombre' => 'RIGIDO', 'categoria_id' => $categoriaIds['EST'] ?? null, 'estado' => 1],
            ['codigo' => 'MOD-ACC-PAQ', 'nombre' => 'PAQUETE', 'categoria_id' => $categoriaIds['ACC'] ?? null, 'estado' => 1],
            ['codigo' => 'MOD-EQP-AUT', 'nombre' => 'AUTOMATICO', 'categoria_id' => $categoriaIds['EQP'] ?? null, 'estado' => 1],
            ['codigo' => 'MOD-SER-BAS', 'nombre' => 'BASICO', 'categoria_id' => $categoriaIds['SER'] ?? null, 'estado' => 1],
        ];

        $this->upsertByCodigo('erp_modelos', $rows);
    }

    private function upsertByCodigo(string $table, array $rows): void
    {
        if (!Schema::hasTable($table)) {
            $this->command?->warn("Tabla {$table} no encontrada. Seeder omitido para esta tabla.");

            return;
        }

        $now = now();

        foreach ($rows as $row) {
            $row = $this->normalizeColumns($table, $row);

            $current = DB::table($table)->where('codigo', $row['codigo'])->first();

            if ($current) {
                DB::table($table)
                    ->where('id', $current->id)
                    ->update(array_merge($row, [
                        'deleted_at' => null,
                        'updated_at' => $now,
                    ]));
            } else {
                DB::table($table)->insert(array_merge($row, [
                    'deleted_at' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]));
            }
        }
    }

    private function normalizeColumns(string $table, array $row): array
    {
        $row = $this->moveColumnIfNeeded($table, $row, ['tipo_categoria_id', 'erp_tipo_categoria_id']);
        $row = $this->moveColumnIfNeeded($table, $row, ['categoria_id', 'erp_categoria_id']);
        $row = $this->moveColumnIfNeeded($table, $row, ['color_id', 'erp_color_id']);
        $row = $this->normalizeJsonColumn($table, $row, 'campos_autocompletado');
        $row = $this->normalizeJsonColumn($table, $row, 'caracteristicas');

        return $row;
    }

    private function normalizeJsonColumn(string $table, array $row, string $column): array
    {
        if (!array_key_exists($column, $row)) {
            return $row;
        }

        if (!Schema::hasColumn($table, $column)) {
            unset($row[$column]);

            return $row;
        }

        $value = $row[$column];
        if (is_array($value)) {
            $row[$column] = json_encode(array_values($value), JSON_UNESCAPED_UNICODE);
        } elseif ($value !== null && !is_string($value)) {
            $row[$column] = json_encode($value, JSON_UNESCAPED_UNICODE);
        }

        return $row;
    }

    private function moveColumnIfNeeded(string $table, array $row, array $candidates): array
    {
        $targetColumn = $this->resolveColumn($table, $candidates);

        if (!$targetColumn) {
            foreach ($candidates as $candidate) {
                unset($row[$candidate]);
            }

            return $row;
        }

        $valueSet = false;
        $value = null;

        foreach ($candidates as $candidate) {
            if (array_key_exists($candidate, $row)) {
                $value = $row[$candidate];
                $valueSet = true;
                break;
            }
        }

        foreach ($candidates as $candidate) {
            unset($row[$candidate]);
        }

        if ($valueSet) {
            $row[$targetColumn] = $value;
        }

        return $row;
    }

    private function resolveColumn(string $table, array $candidates): ?string
    {
        foreach ($candidates as $candidate) {
            if (Schema::hasColumn($table, $candidate)) {
                return $candidate;
            }
        }

        return null;
    }
}
