<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('umkm');
});

Route::view('/umkm', 'umkm');
