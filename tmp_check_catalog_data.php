<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

$cats = DB::table('erp_categorias')->select('id','codigo','nombre')->orderBy('id')->get();
echo "erp_categorias:\n";
foreach ($cats as $c) {
    echo "{$c->id} | {$c->codigo} | {$c->nombre}\n";
}

echo "\nerp_subcategorias columns:\n";
print_r(Schema::getColumnListing('erp_subcategorias'));

echo "\nerp_subcategorias sample:\n";
$subs = DB::table('erp_subcategorias')->select('id','codigo','nombre')->limit(20)->get();
foreach ($subs as $s) {
    echo "{$s->id} | {$s->codigo} | {$s->nombre}\n";
}

echo "\nerp_material columns:\n";
print_r(Schema::getColumnListing('erp_material'));

echo "\nerp_material sample:\n";
$mats = DB::table('erp_material')->select('id','codigo','nombre')->limit(20)->get();
foreach ($mats as $m) {
    echo "{$m->id} | {$m->codigo} | {$m->nombre}\n";
}
