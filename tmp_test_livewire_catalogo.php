<?php
require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$component = new Modules\ERP\Livewire\CatalogoModalManager();
$component->mount(
    [
        '101' => [
            'subcategoria' => [
                ['id' => 1, 'nombre' => 'GENERAL', 'codigo' => 'SUB-MON-GEN'],
            ],
            'material' => [
                ['id' => 11, 'nombre' => 'ACETATO', 'codigo' => 'MAT-MON-ACET'],
            ],
        ],
    ],
    [
        '101' => [
            'codigo' => 'MON',
            'nombre' => 'MONTURA',
            'caracteristicas' => ['material'],
            'campos_autocompletado' => ['categoria', 'subcategoria', 'material'],
        ],
    ],
    ['101' => 1]
);

$component->openModal(101, 1, 'MONTURA', 'erp_catalogos');
echo "Inicial: {$component->descripcion}\n";

$component->subcategoria = '1';
$component->updated('subcategoria', '1');
echo "Subcategoria: {$component->descripcion}\n";

$component->values['material'] = '11';
$component->updated('values.material', '11');
echo "Material: {$component->descripcion}\n";

