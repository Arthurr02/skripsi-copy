<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AuthController;

use App\Http\Controllers\Rekrutmen\UpdateInformasiController;
use App\Http\Controllers\Rekrutmen\PendaftarController;
use App\Http\Controllers\Rekrutmen\PengerjaanSeleksiController;
use App\Http\Controllers\Rekrutmen\RiwayatRekrutmenController;

use App\Http\Controllers\Organisasi\BukaRekrutmenController;

use App\Http\Controllers\Mahasiswa\DaftarRekrutmenController;
use App\Http\Controllers\Mahasiswa\RekrutmenDiikutiController;
use App\Http\Controllers\Mahasiswa\RiwayatPendaftaranController;

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
// 2. GRUP ORGANISASI INTI (DPM/BEM)
// ==========================================
Route::middleware('auth:organisasi')->prefix('organisasi')->name('organisasi.')->group(function () {
    Route::get('/dashboard', function () {
        return view('organisasi.dashboard');
    })->name('dashboard');

    Route::prefix('buka-rekrutmen')->name('buka-rekrutmen.')->group(function () {
        Route::get('/', [BukaRekrutmenController::class, 'index'])->name('index');
        Route::post('/inisiasi', [BukaRekrutmenController::class, 'storeInisiasi'])->name('store_inisiasi');
    });

    Route::prefix('rekrutmen')->name('rekrutmen.')->group(function () {
        Route::get('/pendaftar', [PendaftarController::class, 'index'])->name('pendaftar');
        Route::get('/seleksi', [PengerjaanSeleksiController::class, 'index'])->name('seleksi');
        Route::get('/informasi/{periode_id?}', [UpdateInformasiController::class, 'index'])->name('update');
        Route::post('/informasi/{periode_id}', [UpdateInformasiController::class, 'store'])->name('store_update');
    });

    Route::prefix('riwayat')->name('riwayat.')->group(function () {
        Route::get('/', [RiwayatRekrutmenController::class, 'index'])->name('index');
        Route::get('/{periode_id}', [RiwayatRekrutmenController::class, 'showPeriode'])->name('periode');
        Route::get('/{periode_id}/jabatan/{jabatan_id}', [RiwayatRekrutmenController::class, 'showJabatan'])->name('jabatan');
        Route::get('/{periode_id}/jabatan/{jabatan_id}/tahapan/{tahapan_id}', [RiwayatRekrutmenController::class, 'showTahapan'])->name('tahapan');
    });
});


// ==========================================
// 3. GRUP PANITIA (Anggota Organisasi)
// ==========================================
Route::middleware(['auth:mahasiswa', 'is_panitia'])->prefix('panitia')->name('panitia.')->group(function () {

    Route::get('/dashboard', function () {
        $user = auth()->user();
        // $dataKepanitiaan = $user->keanggotaan->first();
        return view('panitia.dashboard', compact(
            'user',
            //'dataKepanitiaan'
        ));
    })->name('dashboard');

    Route::prefix('rekrutmen')->name('rekrutmen.')->group(function () {
        Route::get('/pendaftar', [PendaftarController::class, 'index'])->name('pendaftar');
        Route::get('/seleksi', [PengerjaanSeleksiController::class, 'index'])->name('seleksi');
        Route::get('/informasi/{periode_id?}', [UpdateInformasiController::class, 'index'])->name('update');
        Route::post('/informasi/{periode_id}', [UpdateInformasiController::class, 'store'])->name('store_update');
    });

    Route::prefix('riwayat')->name('riwayat.')->group(function () {
        Route::get('/', [RiwayatRekrutmenController::class, 'index'])->name('index');
        Route::get('/{periode_id}', [RiwayatRekrutmenController::class, 'showPeriode'])->name('periode');
        Route::get('/{periode_id}/jabatan/{jabatan_id}', [RiwayatRekrutmenController::class, 'showJabatan'])->name('jabatan');
        Route::get('/{periode_id}/jabatan/{jabatan_id}/tahapan/{tahapan_id}', [RiwayatRekrutmenController::class, 'showTahapan'])->name('tahapan');
    });
});


// ==========================================
// 4. GRUP MAHASISWA BIASA (Pendaftar) - FIXED
// ==========================================
Route::middleware('auth')->prefix('mahasiswa')->name('mahasiswa.')->group(function () {

    Route::get('/dashboard', function () {
        return redirect()->route('mahasiswa.rekrutmen.index');
    })->name('dashboard');

    Route::prefix('rekrutmen')->name('rekrutmen.')->group(function () {
        Route::get('/', [DaftarRekrutmenController::class, 'index'])->name('index');
        Route::get('/{periode_id}/info', [DaftarRekrutmenController::class, 'info'])->name('info');
        Route::get('/{periode_id}/daftar', [DaftarRekrutmenController::class, 'kerjakanTahapanSatu'])->name('daftar');
        Route::post('/{periode_id}/daftar', [DaftarRekrutmenController::class, 'submitPendaftaran'])->name('submit');

    });

    // Menu 2: Rekrutmen Sedang Diikuti
    Route::prefix('/rekrutmen-diikuti')->name('rekrutmen.diikuti.')->group(function () {
        Route::get('/', [RekrutmenDiikutiController::class, 'index'])->name('index');
        Route::get('/tahapan/{id}', [RekrutmenDiikutiController::class, 'showTahapan'])->name('tahapan');
        Route::get('/tugas-detail/{pendaftaran}/{tugas}', [RekrutmenDiikutiController::class, 'showTugasDetail'])->name('tugas_detail');
        Route::post('/tugas-detail/{pendaftaran}/{tugas}', [RekrutmenDiikutiController::class, 'submitTugas'])->name('tugas_submit');
        Route::post('/tugas-detail/{pendaftaran}/{tugas}/hadir', [RekrutmenDiikutiController::class, 'konfirmasiWawancara'])->name('wawancara_hadir');
    });

    // Menu 3: Riwayat Rekrutmen
    Route::prefix('riwayat')->name('riwayat.')->group(function () {
        Route::get('/', [RiwayatPendaftaranController::class, 'index'])->name('index');
    });

});




require __DIR__ . '/auth.php';