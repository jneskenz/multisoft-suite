<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo get_class(app(Modules\Core\Contracts\PatientDirectoryContract::class)) . PHP_EOL;
