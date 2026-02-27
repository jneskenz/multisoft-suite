<?php
require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

if (!Illuminate\Support\Facades\Schema::hasTable('erp_tickets')) {
    echo "TABLE_NOT_FOUND\n";
    exit(0);
}

$count = Illuminate\Support\Facades\DB::table('erp_tickets')->count();
echo "COUNT={$count}\n";

$rows = Illuminate\Support\Facades\DB::table('erp_tickets')->limit(20)->get();
if ($rows->isEmpty()) {
    echo "NO_ROWS\n";
    exit(0);
}

foreach ($rows as $row) {
    echo json_encode($row, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . PHP_EOL;
}
