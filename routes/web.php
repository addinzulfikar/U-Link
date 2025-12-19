<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\UmkmController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    // Kalau sudah login, halaman neon tidak ditampilkan lagi
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }

    try {
        $result = DB::select('select version()');
        $db_version = $result[0]->version;
    } catch (\Exception $e) {
        $db_version = 'Error: Could not connect to the database. '.$e->getMessage();
    }

    // Kurangi kemungkinan "Back" menampilkan neon dari cache
    return response()
        ->view('neon', ['db_version' => $db_version])
        ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
        ->header('Pragma', 'no-cache');
})->name('home');

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// Public UMKM and Product Routes
Route::get('/umkms', [UmkmController::class, 'index'])->name('umkms.index');
Route::get('/umkms/{slug}', [UmkmController::class, 'show'])->name('umkms.show');
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/umkms/{umkmSlug}/products/{productSlug}', [ProductController::class, 'show'])->name('products.show');

// Dashboard Routes
Route::middleware(['auth'])->group(function () {
    // ✅ Entry point dashboard untuk semua role
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // User Dashboard & Features
    Route::middleware('role:user')->group(function () {
        Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites.index');
        Route::post('/favorites/{umkmId}/toggle', [FavoriteController::class, 'toggle'])->name('favorites.toggle');
        Route::post('/products/{productId}/reviews', [ReviewController::class, 'store'])->name('reviews.store');
        Route::put('/reviews/{id}', [ReviewController::class, 'update'])->name('reviews.update');
        Route::delete('/reviews/{id}', [ReviewController::class, 'destroy'])->name('reviews.destroy');
    });

    // Admin Toko Dashboard & Features
    Route::middleware('role:admin_toko')->group(function () {
        Route::get('/dashboard/admin-toko', [DashboardController::class, 'adminToko'])->name('dashboard.admin-toko');

        // UMKM Management
        Route::get('/umkm/create', [UmkmController::class, 'create'])->name('umkm.create');
        Route::post('/umkm', [UmkmController::class, 'store'])->name('umkm.store');
        Route::get('/umkm/manage', [UmkmController::class, 'manage'])->name('umkm.manage');
        Route::get('/umkm/edit', [UmkmController::class, 'edit'])->name('umkm.edit');
        Route::put('/umkm', [UmkmController::class, 'update'])->name('umkm.update');

        // Product Management
        Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
        Route::post('/products', [ProductController::class, 'store'])->name('products.store');
        Route::get('/products/{id}/edit', [ProductController::class, 'edit'])->name('products.edit');
        Route::put('/products/{id}', [ProductController::class, 'update'])->name('products.update');
        Route::delete('/products/{id}', [ProductController::class, 'destroy'])->name('products.destroy');

        // Spreadsheet Analyzer
        Route::get('/spreadsheet/analyzer', [DashboardController::class, 'spreadsheetAnalyzer'])->name('spreadsheet.analyzer');
    });

    // Super Admin Dashboard & Features
    Route::middleware('role:super_admin')->group(function () {
        Route::get('/dashboard/super-admin', [DashboardController::class, 'superAdmin'])->name('dashboard.super-admin');
        Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
        Route::get('/admin/users', [AdminController::class, 'users'])->name('admin.users');
        Route::get('/admin/umkms', [AdminController::class, 'umkms'])->name('admin.umkms');
        Route::post('/admin/umkms/{id}/approve', [AdminController::class, 'approveUmkm'])->name('admin.umkms.approve');
        Route::post('/admin/umkms/{id}/reject', [AdminController::class, 'rejectUmkm'])->name('admin.umkms.reject');
        Route::get('/admin/categories', [AdminController::class, 'categories'])->name('admin.categories');
        Route::post('/admin/categories', [AdminController::class, 'storeCategory'])->name('admin.categories.store');
        Route::delete('/admin/categories/{id}', [AdminController::class, 'deleteCategory'])->name('admin.categories.destroy');
    });
});
