<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;

$users = User::query()->with('umkm')->orderBy('id')->get();

echo 'TOTAL_USERS=' . $users->count() . PHP_EOL;

echo PHP_EOL . "USERS:" . PHP_EOL;
foreach ($users as $u) {
    $ownsUmkmId = $u->umkm?->id;
    echo "- id={$u->id} name=\"{$u->name}\" role={$u->role} umkm_id=" . ($u->umkm_id ?? 'NULL') . " owns_umkm_id=" . ($ownsUmkmId ?? 'NULL') . PHP_EOL;
}

echo PHP_EOL . "ALLOWED CONTACTS:" . PHP_EOL;
foreach ($users as $u) {
    $allowed = $u->getAllowedChatUsers();
    $ids = $allowed->pluck('id')->values()->all();
    echo "- user_id={$u->id} role={$u->role} allowed_count=" . count($ids) . " allowed_ids=" . json_encode($ids) . PHP_EOL;
}
