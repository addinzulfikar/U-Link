<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    // Chatify registers this container binding in ChatifyServiceProvider.
    $messenger = app('ChatifyMessenger');
    echo "OK ChatifyMessenger resolved\n";
} catch (Throwable $e) {
    echo "ERROR: " . $e::class . " - " . $e->getMessage() . "\n";
    exit(1);
}
