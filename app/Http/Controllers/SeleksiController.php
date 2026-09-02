<?php

namespace App\Http\Controllers;

use App\Models\Jabatan;
use App\Models\Pendaftaran;
use App\Models\PeriodeRekrutmen;
use App\Models\Tahapan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SeleksiController extends Controller
{
    public function index(Request $request)
    {
        $organisasiId = Auth::guard('organisasi')->id();
        $now = Carbon::now();

        // 1. Ambil periode aktif
        $periodeAktif = PeriodeRekrutmen::where('organisasi_id', $organisasiId)
            ->where('status_aktif', 1)
            ->latest()
            ->first();

        if (! $periodeAktif) {
            return view('organisasi.seleksi.index', [
                'listTahapan' => collect(),
                'activeTahapan' => null,
                'pesertaData' => collect(),
                'listJabatan' => collect(),
            ]);
        }

        // 2. Ambil semua tahapan dan hitung status waktunya
        $listTahapan = Tahapan::where('periode_rekrutmen_id', $periodeAktif->id)
            ->orderBy('urutan_tahapan', 'asc')
            ->get()
            ->map(function ($tahapan) use ($now) {
                $mulai = Carbon::parse($tahapan->waktu_mulai);
                $berakhir = Carbon::parse($tahapan->waktu_berakhir);

                if ($now->lt($mulai)) {
                    $tahapan->status_waktu = 'belum_mulai';
                } elseif ($now->gt($berakhir)) {
                    $tahapan->status_waktu = 'sudah_lewat';
                } else {
                    $tahapan->status_waktu = 'sedang_berjalan';
                }

                return $tahapan;
            });

        // 3. Tentukan tahapan mana yang sedang aktif dilihat panitia
        $activeTahapanId = $request->get('tahapan_id');
        $activeTahapan = $listTahapan->firstWhere('id', $activeTahapanId);

        // Jika tidak memilih atau id tidak valid, default ke tahapan yang 'sedang_berjalan', atau tahapan pertama
        if (! $activeTahapan) {
            $activeTahapan = $listTahapan->firstWhere('status_waktu', 'sedang_berjalan') ?? $listTahapan->first();
        }

        // Ambil daftar jabatan untuk filter dropdown
        $listJabatan = Jabatan::where('periode_rekrutmen_id', $periodeAktif->id)->get();

        // 4. Ambil data peserta & data pengumpulan tugas pada tahapan terpilih tersebut
        $pesertaData = collect();
        if ($activeTahapan) {
            $query = Pendaftaran::with([
                'mahasiswa:nim,nama_lengkap,email_kampus',
                'pilihanJabatan1:id,nama_jabatan',
                'pilihanJabatan2:id,nama_jabatan',
                // Ambil jawaban tugas hanya yang termasuk dalam tahapan aktif ini
                'pengumpulanTugas' => function ($q) use ($activeTahapan) {
                    $q->whereHas('tugas', function ($tQ) use ($activeTahapan) {
                        $tQ->where('tahapan_id', $activeTahapan->id);
                    });
                },
            ])->whereHas('pilihanJabatan1', function ($q) use ($periodeAktif) {
                $q->where('periode_rekrutmen_id', $periodeAktif->id);
            });

            // Logika Filter Posisi Jabatan tertentu (Pilihan 1 ATAU Pilihan 2)
            if ($request->filled('filter_jabatan')) {
                $jabatanId = $request->filter_jabatan;
                $query->where(function ($q) use ($jabatanId) {
                    $q->where('jabatan_1_id', $jabatanId)
                        ->orWhere('jabatan_2_id', $jabatanId);
                });
            }

            $pesertaData = $query->latest()->get();
        }

        return view('organisasi.seleksi.index', compact('listTahapan', 'activeTahapan', 'pesertaData', 'listJabatan', 'periodeAktif'));
    }
}
