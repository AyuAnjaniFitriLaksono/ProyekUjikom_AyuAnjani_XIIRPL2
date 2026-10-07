<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\KategoriController;
use App\Http\Controllers\API\AlatController;
use App\Http\Controllers\API\PeminjamanController;
use App\Http\Controllers\API\PengembalianController;
use App\Http\Controllers\API\LogAktivitasController;


// =====================================================
// PUBLIC ROUTES
// Tidak perlu token
// =====================================================

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


// =====================================================
// PROTECTED ROUTES
// Wajib membawa Bearer Token dari Sanctum
// =====================================================

Route::middleware('auth:sanctum')->group(function () {

    // =================================================
    // AUTH
    // =================================================

    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);


    // =================================================
    // ADMIN
    // =================================================

    Route::middleware('role.admin')->group(function () {

        // Kategori
        Route::apiResource('kategori', KategoriController::class);

        // Alat
        Route::apiResource('alat', AlatController::class);

        // Katalog
        Route::get('/katalog', [AlatController::class, 'katalog']);

        // User
        Route::apiResource('users', UserController::class);

        // Peminjaman
        Route::get('/peminjaman', [PeminjamanController::class, 'index']);
        Route::get('/peminjaman/{peminjaman}', [PeminjamanController::class, 'show']);
        Route::post('/peminjaman/{peminjaman}/approve', [PeminjamanController::class, 'approve']);
        Route::put('/peminjaman/{peminjaman}', [PeminjamanController::class, 'update']);
        Route::delete('/peminjaman/{peminjaman}', [PeminjamanController::class, 'destroy']);

        // Pengembalian
        Route::get('/pengembalian', [PengembalianController::class, 'index']);
        Route::get('/pengembalian/{pengembalian}', [PengembalianController::class, 'show']);
        Route::put('/pengembalian/{pengembalian}', [PengembalianController::class, 'update']);
        Route::delete('/pengembalian/{pengembalian}', [PengembalianController::class, 'destroy']);

        // Log Aktivitas
        Route::get('/log-aktivitas', [LogAktivitasController::class, 'index']);
    });


    // =================================================
    // PETUGAS
    // =================================================

    Route::middleware('role.petugas')->group(function () {

        // Menyetujui peminjaman
        Route::post('/peminjaman/{peminjaman}/approve', [PeminjamanController::class, 'approve']);

        // Memproses pengembalian
        Route::post('/pengembalian', [PengembalianController::class, 'store']);
    });


    // =================================================
    // PEMINJAM
    // =================================================

    Route::middleware('role.peminjam')->group(function () {

        // Katalog alat
        Route::get('/katalog', [AlatController::class, 'katalog']);

        // Mengajukan peminjaman
        Route::post('/peminjaman', [PeminjamanController::class, 'store']);

        // Riwayat peminjaman
        Route::get('/riwayat-pinjam', [PeminjamanController::class, 'riwayat']);
    });

});