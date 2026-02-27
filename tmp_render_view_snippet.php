<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$controller = new Modules\ERP\Http\Controllers\CatalogoController();
$response = $controller->index();
$html = $response->render();

$needle = 'const catalogoOptions = ';
$pos = strpos($html, $needle);
if ($pos === false) {
    echo "needle not found\n";
    exit;
}
$start = max(0, $pos - 200);
$len = 2000;
echo substr($html, $start, $len);
