<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$controller = new Modules\ERP\Http\Controllers\CatalogoController();
$response = $controller->index();
$data = $response->getData();
$opts = $data['catalogoOptions'] ?? [];

foreach ($opts as $catId => $fields) {
    echo "cat {$catId}\n";
    foreach (['subcategoria','material','tipo','marca'] as $f) {
        $first = $fields[$f][0] ?? null;
        if ($first) {
            echo "  {$f}: id={$first['id']} nombre={$first['nombre']} codigo=" . ($first['codigo'] ?? 'NULL') . "\n";
        } else {
            echo "  {$f}: (sin datos)\n";
        }
    }
}
