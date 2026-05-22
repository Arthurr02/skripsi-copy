<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SeleksiController;
use App\Http\Controllers\PeriodeRekrutmenController;
use App\Http\Controllers\RekrutmenAktifController;

// ==========================================
// 1. HALAMAN DEPAN & AUTENTIKASI
// ==========================================
Route::get('/', function () {
    return view('auth.login');
})->name('login');

Route::get('/auth/google/redirect', [AuthController::class, 'redirect'])->name('google.login');
Route::get('/auth/google/callback', [AuthController::class, 'callback']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


// ==========================================
// GRUP ORGANISASI INTI (DPM/BEM)
// ==========================================
Route::middleware('auth:organisasi')->prefix('organisasi')->name('organisasi.')->group(function () {
    Route::get('/dashboard', function () {
        return view('organisasi.dashboard');
    })->name('dashboard');

    Route::prefix('periode')->name('periode.')->group(function () {
        // TAHAP 1: Pembentukan dasar periode & panitia
        Route::get('/inisiasi', [PeriodeRekrutmenController::class, 'createInisiasi'])->name('inisiasi');
        Route::post('/inisiasi', [PeriodeRekrutmenController::class, 'storeInisiasi'])->name('store_inisiasi');

        // TAHAP 2: Pengaturan tahapan dan penugasan
        Route::get('/{id}/skema', [PeriodeRekrutmenController::class, 'createSkema'])->name('skema');
        Route::post('/{id}/skema', [PeriodeRekrutmenController::class, 'storeSkema'])->name('store_skema');

    });

    Route::prefix('rekrutmen')->name('rekrutmen.')->group(function () {
        Route::get('/pendaftar', [RekrutmenAktifController::class, 'pendaftar'])->name('pendaftar');
        Route::get('/tahapan', [RekrutmenAktifController::class, 'tahapan'])->name('tahapan');
        Route::get('/update-informasi/{periode_id?}', [RekrutmenAktifController::class, 'updateInfo'])->name('update');
        Route::post('/update-informasi/{periode_id}', [RekrutmenAktifController::class, 'storeUpdateInfo'])->name('store_update');
    });


    // FITUR BERSAMA: Seleksi Peserta
    Route::get('/seleksi-peserta', [SeleksiController::class, 'index'])->name('seleksi.index');
});


// ==========================================
// GRUP PANITIA (Anggota Organisasi)
// ==========================================
Route::middleware(['auth', 'is_panitia'])->prefix('panitia')->name('panitia.')->group(function () {

    Route::get('/dashboard', function () {
        $user = auth()->user();

        // AMBIL DATA JABATAN DARI DATABASE
        $dataKepanitiaan = $user->keanggotaan->first();

        // PASTIKAN $dataKepanitiaan MASUK KE DALAM compact()
        return view('panitia.dashboard', compact('user', 'dataKepanitiaan'));
    })->name('dashboard');

    // Rute seleksi peserta
    Route::get('/seleksi-peserta', [App\Http\Controllers\SeleksiController::class, 'index'])->name('seleksi.index');
});


// ==========================================
// 4. GRUP MAHASISWA BIASA (Pendaftar)
// ==========================================
// Syarat masuk: Login biasa (auth)
Route::middleware('auth')->prefix('mahasiswa')->name('mahasiswa.')->group(function () {

    Route::get('/dashboard', function () {
        return view('mahasiswa.dashboard');
    })->name('dashboard');

    // Nanti rute Isi Formulir, Upload CV, ditaruh di sini

});


// ==========================================
// 5. BAWAAN LARAVEL BREEZE (Profile)
// ==========================================
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';