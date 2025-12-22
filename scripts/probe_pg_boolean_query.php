<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Product;
use App\Models\Umkm;

try {
    $rows = Product::query()
        ->whereHas('umkm', function ($q) {
            $q->where('status', Umkm::STATUS_APPROVED);
        })
        ->whereRaw('is_active is true')
        ->latest()
        ->take(1)
        ->get();

    echo "OK rows=" . $rows->count() . PHP_EOL;
} catch (Throwable $e) {
    echo "ERROR: " . $e->getMessage() . PHP_EOL;
    exit(1);
}
