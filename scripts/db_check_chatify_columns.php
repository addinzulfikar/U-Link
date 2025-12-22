<?php

// Quick DB sanity check for migrations vs actual schema.

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

$driver = DB::connection()->getDriverName();
$dbName = method_exists(DB::connection(), 'getDatabaseName') ? DB::connection()->getDatabaseName() : null;

echo "DB_DRIVER={$driver}\n";
echo "DB_NAME={$dbName}\n";

try {
    $meta = DB::selectOne("select current_schema() as current_schema, current_setting('search_path') as search_path");
    echo "CURRENT_SCHEMA={$meta->current_schema}\n";
    echo "SEARCH_PATH={$meta->search_path}\n";
} catch (Throwable $e) {
    echo "Failed to read schema/search_path: {$e->getMessage()}\n";
}

echo "\nusers.hasColumn(active_status): ";
var_export(Schema::hasColumn('users', 'active_status'));
echo "\nusers.hasColumn(avatar): ";
var_export(Schema::hasColumn('users', 'avatar'));
echo "\nusers.hasColumn(dark_mode): ";
var_export(Schema::hasColumn('users', 'dark_mode'));
echo "\nusers.hasColumn(messenger_color): ";
var_export(Schema::hasColumn('users', 'messenger_color'));
echo "\n\n";

try {
    $cols = Schema::getColumnListing('users');
    echo "USERS_COLS (Schema::getColumnListing):\n";
    echo implode(', ', $cols) . "\n\n";
} catch (Throwable $e) {
    echo "Failed Schema::getColumnListing(users): {$e->getMessage()}\n\n";
}

try {
    $cols = DB::select("select column_name, data_type, column_default from information_schema.columns where table_schema = 'public' and table_name = 'users' order by ordinal_position");
    echo "USERS_COLS (information_schema.public.users):\n";
    foreach ($cols as $c) {
        echo "- {$c->column_name} ({$c->data_type}) default={$c->column_default}\n";
    }
    echo "\n";
} catch (Throwable $e) {
    echo "Failed information_schema query: {$e->getMessage()}\n\n";
}

try {
    $others = DB::select("select table_schema from information_schema.tables where table_name = 'users' order by table_schema");
    echo "\nSCHEMAS HAVING table users:\n";
    foreach ($others as $o) {
        echo "- {$o->table_schema}\n";
    }
    echo "\n";
} catch (Throwable $e) {
    echo "Failed users schema lookup: {$e->getMessage()}\n\n";
}

try {
    $chatTables = DB::select("select table_schema, table_name from information_schema.tables where table_name in ('ch_messages','ch_favorites') order by table_schema, table_name");
    echo "CHATIFY TABLES (where they exist):\n";
    foreach ($chatTables as $t) {
        echo "- {$t->table_schema}.{$t->table_name}\n";
    }
    echo "\n";
} catch (Throwable $e) {
    echo "Failed chat tables lookup: {$e->getMessage()}\n\n";
}

try {
    $rows = DB::select("select migration, batch from migrations where migration like '2025_12_22_999999_%' order by migration");
    echo "MIGRATIONS (2025_12_22_999999_*):\n";
    foreach ($rows as $r) {
        echo "- {$r->migration} (batch {$r->batch})\n";
    }
    echo "\n";
} catch (Throwable $e) {
    echo "Failed migrations query: {$e->getMessage()}\n\n";
}
