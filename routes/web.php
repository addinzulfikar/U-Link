<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
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

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// Dashboard Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware('role:user')
        ->name('dashboard.user');
    
    Route::get('/dashboard/admin-toko', [DashboardController::class, 'adminToko'])
        ->middleware('role:admin_toko')
        ->name('dashboard.admin-toko');
    
    Route::get('/dashboard/super-admin', [DashboardController::class, 'superAdmin'])
        ->middleware('role:super_admin')
        ->name('dashboard.super-admin');
});

