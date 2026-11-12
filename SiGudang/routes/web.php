<?php

use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\MutasiStokController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - SiGudang – 036
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

// Protected Routes (Session Auth)
Route::middleware(['auth', 'role'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard (All Roles)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile Management (All Roles)
    Route::get('/profil', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profil', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profil/foto', [ProfileController::class, 'updatePhoto'])->name('profile.photo');

    // Laporan Inventaris (All Roles)
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/export', [LaporanController::class, 'exportCsv'])->name('laporan.export');

    // Audit Log (Manajer & Auditor only)
    Route::middleware('role:Manajer,Auditor')->group(function () {
        Route::get('/audit-log', [AuditLogController::class, 'index'])->name('audit.index');
    });

    // User Management (Manajer only)
    Route::middleware('role:Manajer')->prefix('pengguna')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
        Route::put('/{user}', [UserController::class, 'update'])->name('update');
        Route::post('/{user}/toggle', [UserController::class, 'toggleStatus'])->name('toggle');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
    });

    // Supplier Management
    // Read: All roles
    Route::get('/supplier', [SupplierController::class, 'index'])->name('supplier.index');
    Route::get('/supplier/{supplier}', [SupplierController::class, 'show'])->name('supplier.show');
    // Create, Edit, Delete: Manajer only
    Route::middleware('role:Manajer')->group(function () {
        Route::get('/supplier-baru/create', [SupplierController::class, 'create'])->name('supplier.create');
        Route::post('/supplier', [SupplierController::class, 'store'])->name('supplier.store');
        Route::get('/supplier/{supplier}/edit', [SupplierController::class, 'edit'])->name('supplier.edit');
        Route::put('/supplier/{supplier}', [SupplierController::class, 'update'])->name('supplier.update');
        Route::delete('/supplier/{supplier}', [SupplierController::class, 'destroy'])->name('supplier.destroy');
    });

    // Barang Management
    // Read: All roles
    Route::get('/barang', [BarangController::class, 'index'])->name('barang.index');
    Route::get('/barang/{barang}', [BarangController::class, 'show'])->name('barang.show');
    // Create & Edit: Manajer & Staf Gudang
    Route::middleware('role:Manajer,Staf Gudang')->group(function () {
        Route::get('/barang-baru/create', [BarangController::class, 'create'])->name('barang.create');
        Route::post('/barang', [BarangController::class, 'store'])->name('barang.store');
        Route::get('/barang/{barang}/edit', [BarangController::class, 'edit'])->name('barang.edit');
        Route::put('/barang/{barang}', [BarangController::class, 'update'])->name('barang.update');
    });
    // Delete: Manajer only
    Route::middleware('role:Manajer')->group(function () {
        Route::delete('/barang/{barang}', [BarangController::class, 'destroy'])->name('barang.destroy');
    });

    // Mutasi Stok Management
    // Read: All roles
    Route::get('/mutasi', [MutasiStokController::class, 'index'])->name('mutasi.index');
    Route::get('/mutasi/{mutasi}', [MutasiStokController::class, 'show'])->name('mutasi.show');
    // Create: Manajer & Staf Gudang
    Route::middleware('role:Manajer,Staf Gudang')->group(function () {
        Route::get('/mutasi-baru/create', [MutasiStokController::class, 'create'])->name('mutasi.create');
        Route::post('/mutasi', [MutasiStokController::class, 'store'])->name('mutasi.store');
    });
});
