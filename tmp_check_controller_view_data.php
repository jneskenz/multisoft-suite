<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$controller = new Modules\ERP\Http\Controllers\CatalogoController();
$response = $controller->index();
$data = $response->getData();

$opts = $data['catalogoOptions'] ?? [];
$meta = $data['catalogoCategoryMeta'] ?? [];
$ids = $data['categoriaButtonIds'] ?? [];

echo "categoriaButtonIds:\n";
print_r($ids);

echo "catalogoCategoryMeta:\n";
print_r($meta);

echo "catalogoOptions summary:\n";
foreach ($opts as $catId => $fields) {
    $sub = isset($fields['subcategoria']) ? count($fields['subcategoria']) : 0;
    $material = isset($fields['material']) ? count($fields['material']) : 0;
    $tipo = isset($fields['tipo']) ? count($fields['tipo']) : 0;
    echo "cat {$catId} -> sub={$sub}, material={$material}, tipo={$tipo}\n";
}
