<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;

$tables = ['erp_subcategorias','erp_material','erp_tipos','erp_marcas','erp_tallas','erp_colores','erp_detalle_colores','erp_clases','erp_modelos','erp_fotocromaticos','erp_tratamientos','erp_indices','erp_ojobifocales','erp_adiciones','erp_diametros'];
foreach ($tables as $t) {
    $col = null;
    if (Schema::hasColumn($t, 'categoria_id')) {
        $col = 'categoria_id';
    } elseif (Schema::hasColumn($t, 'erp_categoria_id')) {
        $col = 'erp_categoria_id';
    }
    echo "{$t} => " . ($col ?? 'none') . "\n";
}
