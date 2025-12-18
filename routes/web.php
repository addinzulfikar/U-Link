<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    try {
        $result = DB::select('select version()');
        $db_version = $result[0]->version;
    } catch (\Exception $e) {
        $db_version = 'Error: Could not connect to the database. ' . $e->getMessage();
    }

    return view('neon', ['db_version' => $db_version]);
});
