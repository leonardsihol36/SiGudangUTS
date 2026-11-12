<?php

use App\Http\Controllers\Api\ApiAuthController;
use App\Http\Controllers\Api\ApiBarangController;
use App\Http\Controllers\Api\ApiIdentitasController;
use App\Http\Controllers\Api\ApiMutasiController;
use App\Http\Controllers\Api\ApiSupplierController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| REST API Routes - SiGudang – 036
| Prefix: /api/
|--------------------------------------------------------------------------
*/

// Public Authentication Endpoint
Route::post('/login', [ApiAuthController::class, 'login']);

// Protected Endpoints (Bearer Token Sanctum)
Route::middleware('auth:sanctum')->group(function () {
    // Auth Session / Token Management
    Route::post('/logout', [ApiAuthController::class, 'logout']);
    Route::get('/me', [ApiAuthController::class, 'me']);

    // Identitas Pembuat Aplikasi (WAJIB)
    Route::get('/identitas', [ApiIdentitasController::class, 'index']);

    // Barang Endpoints
    Route::get('/barang', [ApiBarangController::class, 'index']);
    Route::get('/barang/{id}', [ApiBarangController::class, 'show']);
    Route::post('/barang', [ApiBarangController::class, 'store']);
    Route::put('/barang/{id}', [ApiBarangController::class, 'update']);
    Route::patch('/barang/{id}', [ApiBarangController::class, 'update']);
    Route::delete('/barang/{id}', [ApiBarangController::class, 'destroy']);

    // Supplier Endpoints
    Route::get('/supplier', [ApiSupplierController::class, 'index']);

    // Mutasi Stok Endpoints
    Route::get('/mutasi', [ApiMutasiController::class, 'index']);
    Route::post('/mutasi', [ApiMutasiController::class, 'store']);
});
