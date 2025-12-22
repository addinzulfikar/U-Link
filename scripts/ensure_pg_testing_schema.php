<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    if (DB::connection()->getDriverName() !== 'pgsql') {
        echo "SKIP: driver is not pgsql\n";
        exit(0);
    }

    DB::statement('CREATE SCHEMA IF NOT EXISTS testing');
    echo "OK: schema testing ensured\n";
} catch (Throwable $e) {
    echo "ERROR: " . $e::class . " - " . $e->getMessage() . "\n";
    exit(1);
}
