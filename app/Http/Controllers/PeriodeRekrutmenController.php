<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\PeriodeRekrutmen;
use App\Models\AnggotaOrganisasi;
use App\Models\Mahasiswa;

class PeriodeRekrutmenController extends Controller
{
    // ==========================================
    // TAHAP 1: INISIASI (Pembuatan Dasar & Panitia)
    // ==========================================
    public function createInisiasi()
    {
        return view('organisasi.periode.inisiasi');
    }

    public function storeInisiasi(Request $request)
    {
        // 1. Validasi Super Ketat dengan Regex STIS
        $request->validate([
            'tahun_periode' => 'required|string',
            'email_panitia' => 'required|array|min:1',
            'email_panitia.*' => ['required', 'regex:/^\d{9}@stis\.ac\.id$/'],
        ], [
            'email_panitia.*.regex' => 'Format email ditolak server. Pastikan berisi 9 digit NIM diikuti @stis.ac.id'
        ]);

        DB::beginTransaction();
        try {
            // 2. Buat Draft Periode
            $periode = PeriodeRekrutmen::create([
                'organisasi_id' => Auth::guard('organisasi')->id(),
                'tahun_periode' => $request->tahun_periode,
                'status_aktif' => 0,
            ]);

            // 3. Olah Array Email dan Langsung Masukkan ke Tabel Anggota Organisasi
            $emails = $request->email_panitia;

            foreach ($emails as $email) {
                $email = trim($email);

                // Ekstrak NIM dari email (Contoh: 222212602@stis.ac.id dipotong menjadi 222212602)
                $nim = explode('@', $email)[0];

                // [OPSIONAL TAPI PENTING] 
                // Jika database Anda mewajibkan NIM harus terdaftar di tabel mahasiswa dulu (Foreign Key),
                // kita buatkan "Data Bayangan" sementara. Saat mereka login via Google nanti, datanya otomatis diperbarui.
                \App\Models\Mahasiswa::firstOrCreate(
                    ['nim' => $nim],
                    [
                        'email_kampus' => $email,
                        'nama_lengkap' => 'Panitia (Belum Login)' // Nama sementara
                    ]
                );

                // 4. Daftarkan sebagai panitia
                \App\Models\AnggotaOrganisasi::create([
                    'periode_rekrutmen_id' => $periode->id,
                    'nim' => $nim,
                    'jabatan' => 'panitia',
                    'panitia_rekrutmen' => 1
                ]);
            }

            DB::commit();

            // Kembali ke halaman dengan membawa sinyal untuk memunculkan Popup
            return back()->with([
                'success_inisiasi' => true,
                'periode_id' => $periode->id,
                'tahun_periode' => $periode->tahun_periode
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            // Jika database error, kirim pesan error ke layar
            return back()->with('error_server', 'Gagal menyimpan data ke database: ' . $e->getMessage());
        }
    }

    // ==========================================
    // TAHAP 2: SKEMA (Tahapan & Penugasan)
    // ==========================================
    public function createSkema($id)
    {
        $periode = PeriodeRekrutmen::findOrFail($id);
        return view('organisasi.periode.skema', compact('periode'));
    }

    public function storeSkema(Request $request, $id)
    {
        // TODO: Logika insert tahapan & penugasan akan ditambahkan di sini nanti

        $periode = PeriodeRekrutmen::findOrFail($id);
        $periode->update(['status_aktif' => 1]);

        return redirect()->route('organisasi.dashboard')->with('success', 'Skema Rekrutmen Resmi Dibuka!');
    }
}
