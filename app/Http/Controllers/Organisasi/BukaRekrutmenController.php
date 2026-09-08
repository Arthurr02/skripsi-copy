<?php

namespace App\Http\Controllers\Organisasi;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRecruitmentRequest;
use App\Models\Mahasiswa;
use App\Models\Panitia;
use App\Models\PeriodeRekrutmen;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BukaRekrutmenController extends Controller
{
    public function index()
    {
        return view('organisasi.buka-rekrutmen.index');
    }

    public function storeInisiasi(StoreRecruitmentRequest $request)
    {
        $organisasiId = Auth::guard('organisasi')->id();

        // 2. CEK VALDASI : Apakah ada rekrutmen yang SEDANG BERJALAN AKTIF (status_aktif = 1)
        $rekrutmenAktif = PeriodeRekrutmen::where('organisasi_id', $organisasiId)
            ->whereIn('status_aktif', [1, 2])
            ->first();

        if ($rekrutmenAktif) {
            return back()
                ->withInput()
                ->with('rekrutmen_sedang_berjalan', 'Terdapat rekrutmen sedang berlangsung. Lakukan penyelesaian atau non-aktifkan terlebih dahulu.')
                ->with('flash_alert', [
                    'icon' => 'warning',
                    'title' => 'Rekrutmen masih berjalan',
                    'text' => 'Selesaikan atau tutup rekrutmen aktif sebelum membuka periode baru.',
                ]);
        }

        DB::beginTransaction();
        try {
            // 3. Buat Draft Periode
            $periode = PeriodeRekrutmen::create([
                'organisasi_id' => $organisasiId,
                'tahun_periode' => $request->tahun_periode,
                'status_aktif' => 1,
            ]);

            $nims = $request->nim_panitia;

            // 4. Looping data NIM panitia dan konversi otomatis menjadi Email
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

                Panitia::create([
                    'periode_rekrutmen_id' => $periode->id,
                    'nim' => $nim,
                ]);
            }

            DB::commit();

            return back()->with([
                'success_inisiasi' => true,
                'periode_id' => $periode->id,
                'tahun_periode' => $periode->tahun_periode,
                'flash_alert' => [
                    'icon' => 'success',
                    'title' => 'Rekrutmen berhasil dibuka',
                    'text' => 'Periode rekrutmen telah dibuat. Lengkapi informasi dan tahapan seleksi berikutnya.',
                ],
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            report($e);

            return back()->withInput()
                ->with('error_server', 'Gagal menyimpan data. Silakan coba lagi.')
                ->with('flash_alert', [
                    'icon' => 'error',
                    'title' => 'Rekrutmen belum dapat dibuka',
                    'text' => 'Data tidak dapat disimpan. Periksa kembali isian Anda lalu coba lagi.',
                ]);
        }
    }

}
