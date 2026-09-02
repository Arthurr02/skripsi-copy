<?php

namespace App\Http\Controllers;

use App\Models\AnggotaOrganisasi;
use App\Models\Mahasiswa;
use App\Models\PeriodeRekrutmen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PeriodeRekrutmenController extends Controller
{
    // ==========================================
    // TAHAP 1: INISIASI (Pembuatan Dasar & Panitia)
    // ==========================================
    public function index()
    {
        return view('organisasi.buka-rekrutmen.index');
    }

    public function storeInisiasi(Request $request)
    {
        // 1. Validasi Input Dasar
        $request->validate([
            'tahun_periode' => 'required|string',
            'nim_panitia' => 'required|array|min:1',
            'nim_panitia.*' => ['required', 'regex:/^\d{9}$/'],
        ], [
            'nim_panitia.*.regex' => 'NIM panitia tidak valid. Pastikan berisi tepat 9 digit angka.',
        ]);

        $organisasiId = Auth::guard('organisasi')->id();

        // 2. CEK VALDASI 1: Apakah ada rekrutmen yang SEDANG BERJALAN AKTIF (status_aktif = 1)
        $rekrutmenAktif = PeriodeRekrutmen::where('organisasi_id', $organisasiId)
            ->where('status_aktif', 1)
            ->first();

        if ($rekrutmenAktif) {
            return back()->withInput()->with('rekrutmen_sedang_berjalan', 'Terdapat rekrutmen periode '.$rekrutmenAktif->tahun_periode.' yang sedang berjalan aktif. Anda harus menyelesaikan atau menonaktifkannya terlebih dahulu sebelum bisa membuka rekrutmen baru.');
        }

        // 3. CEK VALIDASI 2: Apakah tahun periode yang dipilih sudah pernah dibuat
        $periodeSama = PeriodeRekrutmen::where('organisasi_id', $organisasiId)
            ->where('tahun_periode', $request->tahun_periode)
            ->first();

        if ($periodeSama) {
            return back()->withInput()->with('periode_terdaftar', 'Rekrutmen untuk periode '.$request->tahun_periode.' sudah pernah diinisiasi oleh organisasi Anda.');
        }

        DB::beginTransaction();
        try {
            // 4. Buat Draft Periode
            $periode = PeriodeRekrutmen::create([
                'organisasi_id' => $organisasiId,
                'tahun_periode' => $request->tahun_periode,
                'status_aktif' => 0,
            ]);

            $nims = $request->nim_panitia;

            // 5. Looping data NIM panitia dan konversi otomatis menjadi Email
            foreach ($nims as $nim) {
                $nim = trim($nim);
                $emailDikonversi = $nim.'@stis.ac.id'; // Penggabungan otomatis di sisi server

                Mahasiswa::firstOrCreate(
                    ['nim' => $nim],
                    [
                        'email_kampus' => $emailDikonversi,
                        'nama_lengkap' => 'Panitia (Belum Login)',
                    ]
                );

                AnggotaOrganisasi::create([
                    'periode_rekrutmen_id' => $periode->id,
                    'nim' => $nim,
                    'jabatan' => 'Panitia Rekrutmen', // Jabatan paten
                    'panitia_rekrutmen' => 1,
                ]);
            }

            DB::commit();

            return back()->with([
                'success_inisiasi' => true,
                'periode_id' => $periode->id,
                'tahun_periode' => $periode->tahun_periode,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            report($e);

            return back()->withInput()->with('error_server', 'Gagal menyimpan data. Silakan coba lagi.');
        }
    }

    public function tahapan()
    {
        return $this->hasMany(Tahapan::class, 'periode_rekrutmen_id');
    }

    public function jabatan()
    {
        return $this->hasMany(Jabatan::class, 'periode_rekrutmen_id');
    }
}
