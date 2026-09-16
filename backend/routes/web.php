<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\WEB\AdminController;
use App\Http\Controllers\WEB\PetugasController;
use App\Http\Controllers\WEB\PeminjamController;
use App\Http\Controllers\WEB\AuthController;
use App\Http\Controllers\WEB\PengembalianController;


// ==================== HALAMAN AWAL ====================

Route::get('/', function () {
    return view('welcome');
});


// ==================== ADMIN ====================

Route::middleware(['auth', 'role.admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // ==================== DASHBOARD ====================

        Route::get('/dashboard', [AdminController::class, 'index'])
            ->name('dashboard');


        // ==================== PROFILE ====================

        Route::get('/profile', [AdminController::class, 'profile'])
            ->name('profile');

        Route::post('/profile/foto', [AdminController::class, 'updateFoto'])
            ->name('profile.updateFoto');

        Route::put('/profile', [AdminController::class, 'updateProfile'])
            ->name('profile.update');


        // ==================== USER ====================

        Route::get('/users', [AdminController::class, 'indexUser'])
            ->name('user.index');

        Route::get('/users/create', [AdminController::class, 'createUser'])
            ->name('user.create');

        Route::post('/users', [AdminController::class, 'storeUser'])
            ->name('user.store');

        Route::get('/users/{id}/edit', [AdminController::class, 'editUser'])
            ->name('user.edit');

        Route::put('/users/{id}', [AdminController::class, 'updateUser'])
            ->name('user.update');

        Route::delete('/users/{id}', [AdminController::class, 'destroyUser'])
            ->name('user.destroy');


        // ==================== KATEGORI ====================

        Route::get('/kategori', [AdminController::class, 'indexKategori'])
            ->name('kategori.index');

        Route::get('/kategori/create', [AdminController::class, 'createKategori'])
            ->name('kategori.create');

        Route::post('/kategori', [AdminController::class, 'storeKategori'])
            ->name('kategori.store');

        Route::get('/kategori/{id}/edit', [AdminController::class, 'editKategori'])
            ->name('kategori.edit');

        Route::put('/kategori/{id}', [AdminController::class, 'updateKategori'])
            ->name('kategori.update');

        Route::delete('/kategori/{id}', [AdminController::class, 'destroyKategori'])
            ->name('kategori.destroy');


        // ==================== ALAT ====================

        Route::get('/alat', [AdminController::class, 'indexAlat'])
            ->name('alat.index');

        Route::get('/alat/create', [AdminController::class, 'createAlat'])
            ->name('alat.create');

        Route::post('/alat', [AdminController::class, 'storeAlat'])
            ->name('alat.store');

        Route::get('/alat/{id}/edit', [AdminController::class, 'editAlat'])
            ->name('alat.edit');

        Route::put('/alat/{id}', [AdminController::class, 'updateAlat'])
            ->name('alat.update');

        Route::delete('/alat/{id}', [AdminController::class, 'destroyAlat'])
            ->name('alat.destroy');


        // ==================== PEMINJAMAN ADMIN ====================

        Route::get('/peminjaman', [AdminController::class, 'indexPeminjaman'])
            ->name('peminjaman.index');

        Route::get('/peminjaman/create', [AdminController::class, 'createPeminjaman'])
            ->name('peminjaman.create');

        Route::post('/peminjaman', [AdminController::class, 'storePeminjaman'])
            ->name('peminjaman.store');

        Route::put('/peminjaman/{id}', [AdminController::class, 'updatePeminjaman'])
            ->name('peminjaman.update');

        Route::put('/peminjaman/{id}/status', [AdminController::class, 'updateStatusPeminjaman'])
            ->name('peminjaman.updateStatus');

        Route::delete('/peminjaman/{id}', [AdminController::class, 'destroyPeminjaman'])
            ->name('peminjaman.destroy');


        // ==================== PENGEMBALIAN ====================

        Route::get('/pengembalian', [PengembalianController::class, 'index'])
            ->name('pengembalian.index');

        Route::get('/pengembalian/create/{peminjaman_id}', [PengembalianController::class, 'create'])
            ->name('pengembalian.create');

        Route::post('/pengembalian', [PengembalianController::class, 'store'])
            ->name('pengembalian.store');

        Route::get('/pengembalian/{id}/edit', [PengembalianController::class, 'edit'])
            ->name('pengembalian.edit');

        Route::put('/pengembalian/{id}', [PengembalianController::class, 'update'])
            ->name('pengembalian.update');

        Route::delete('/pengembalian/{id}', [PengembalianController::class, 'destroy'])
            ->name('pengembalian.destroy');


        // ==================== LOG AKTIVITAS ====================

        Route::get('/log-aktivitas', [AdminController::class, 'indexLogAktivitas'])
            ->name('logaktivitas.index');
    });


// ==================== PETUGAS ====================
// Bisa diakses oleh ADMIN dan PETUGAS

Route::middleware(['auth', 'role.petugas_admin'])
    ->prefix('petugas')
    ->name('petugas.')
    ->group(function () {

        // ==================== PROFILE ====================

        Route::get('/profile', [PetugasController::class, 'profile'])
            ->name('profile');

        Route::post('/profile/foto', [PetugasController::class, 'updateFoto'])
            ->name('profile.updateFoto');

        Route::put('/profile', [PetugasController::class, 'updateProfile'])
            ->name('profile.update');


        // ==================== PEMINJAMAN ====================

        Route::get('/peminjaman', [PetugasController::class, 'indexPeminjaman'])
            ->name('peminjaman.index');

        // Setujui peminjaman
        Route::post('/peminjaman/{id}/setujui', [PetugasController::class, 'setujuiPeminjaman'])
            ->name('peminjaman.setujui');

        // Tolak peminjaman
        Route::post('/peminjaman/{id}/tolak', [PetugasController::class, 'tolakPeminjaman'])
            ->name('peminjaman.tolak');


        // ==================== PENGEMBALIAN ====================

        Route::get('/pengembalian', [PetugasController::class, 'indexPengembalian'])
            ->name('pengembalian.index');

        Route::post('/pengembalian/{id}', [PetugasController::class, 'prosesPengembalian'])
            ->name('pengembalian.proses');

        // Tolak pengembalian
        Route::post('/pengembalian/{id}/tolak', [PetugasController::class, 'tolakPengembalian'])
            ->name('pengembalian.tolak');


        // ==================== LAPORAN ====================

        Route::get('/laporan', [PetugasController::class, 'laporan'])
            ->name('laporan.index');

        Route::get('/laporan/cetak', [PetugasController::class, 'cetakLaporan'])
            ->name('laporan.cetak');
    });


// ==================== PEMINJAM ====================

Route::middleware(['auth', 'role.peminjam'])
    ->prefix('peminjam')
    ->name('peminjam.')
    ->group(function () {

        // ==================== PROFILE ====================

        Route::get('/profile', [PeminjamController::class, 'profile'])
            ->name('profile');

        Route::post('/profile/foto', [PeminjamController::class, 'updateFoto'])
            ->name('profile.updateFoto');

        Route::put('/profile', [PeminjamController::class, 'updateProfile'])
            ->name('profile.update');


        // ==================== KATALOG ====================

        Route::get('/katalog', [PeminjamController::class, 'katalogAlat'])
            ->name('katalog');


        // ==================== AJUKAN PEMINJAMAN ====================

        Route::post('/peminjaman/ajukan', [PeminjamController::class, 'ajukanPeminjaman'])
            ->name('peminjaman.ajukan');


        // ==================== RIWAYAT PEMINJAMAN ====================

        Route::get('/riwayat', [PeminjamController::class, 'riwayatPeminjaman'])
            ->name('riwayat');
    });


// ==================== LOGIN ====================

Route::middleware('guest')->group(function () {

    Route::get('/login', [AuthController::class, 'showLoginForm'])
        ->name('login');

    Route::post('/login', [AuthController::class, 'login']);
});


// ==================== LOGOUT ====================

Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');
