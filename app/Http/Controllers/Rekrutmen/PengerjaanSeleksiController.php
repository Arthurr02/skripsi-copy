<?php

namespace App\Http\Controllers\Rekrutmen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\PeriodeRekrutmen;
use App\Models\Tahapan;
use App\Models\Jabatan;
use App\Models\Pendaftaran;
use App\Models\Panitia;
use Carbon\Carbon;

class PengerjaanSeleksiController extends Controller
{
    // =================================================================
    // FUNGSI UNTUK MENU PENGERJAAN SELEKSI (MONITORING TAHAPAN)
    // =================================================================
    public function index(Request $request)
    {
        // 🌟 CEK IDENTITAS LOGIN: Organisasi atau Panitia?
        if (Auth::guard('organisasi')->check()) {
            $organisasiId = Auth::guard('organisasi')->id();
        } else {
            // Jika yang login adalah Panitia, lacak ID Organisasinya
            $nimPanitia = Auth::user()->nim;

            $kepanitiaan = Panitia::where('nim', $nimPanitia)
                ->where('panitia_rekrutmen', 1)
                ->latest()
                ->first();

            if (!$kepanitiaan) {
                abort(403, 'Anda tidak terdaftar sebagai panitia aktif.');
            }

            // Dapatkan ID organisasi dari tabel relasi periode
            $periodePanitia = PeriodeRekrutmen::find($kepanitiaan->periode_rekrutmen_id);
            $organisasiId = $periodePanitia->organisasi_id;
        }

        $now = Carbon::now();

        // 1. Ambil periode aktif
        $periodeAktif = PeriodeRekrutmen::where('organisasi_id', $organisasiId)
            ->where('status_aktif', 1)
            ->latest()
            ->first();

        if (!$periodeAktif) {
            return view('rekrutmen.seleksi.index', [
                'listTahapan' => collect(),
                'activeTahapan' => null,
                'pesertaData' => collect(),
                'listJabatan' => collect(),
                'periodeAktif' => null
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

        // Jika tidak memilih, default ke tahapan yang 'sedang_berjalan', atau tahapan pertama
        if (!$activeTahapan) {
            $activeTahapan = $listTahapan->firstWhere('status_waktu', 'sedang_berjalan') ?? $listTahapan->first();
        }

        // Ambil daftar jabatan untuk filter dropdown
        $listJabatan = Jabatan::where('periode_rekrutmen_id', $periodeAktif->id)->get();

        // 4. Ambil data peserta & data pengumpulan tugas pada tahapan terpilih
        $pesertaData = collect();
        if ($activeTahapan) {
            $query = Pendaftaran::with([
                'mahasiswa:nim,nama_lengkap,email_kampus',
                'pilihanJabatan1:id,nama_jabatan',
                'pilihanJabatan2:id,nama_jabatan',
                'pengumpulanTugas' => function ($q) use ($activeTahapan) {
                    $q->whereHas('tugas', function ($tQ) use ($activeTahapan) {
                        $tQ->where('tahapan_id', $activeTahapan->id);
                    });
                }
            ])->whereHas('pilihanJabatan1', function ($q) use ($periodeAktif) {
                $q->where('periode_rekrutmen_id', $periodeAktif->id);
            });

            // Logika Filter Jabatan
            if ($request->filled('filter_jabatan')) {
                $jabatanId = $request->filter_jabatan;
                $query->where(function ($q) use ($jabatanId) {
                    $q->where('jabatan_1_id', $jabatanId)
                        ->orWhere('jabatan_2_id', $jabatanId);
                });
            }

            $pesertaData = $query->latest()->get();
        }

        return view('rekrutmen.seleksi.index', compact('listTahapan', 'activeTahapan', 'pesertaData', 'listJabatan', 'periodeAktif'));
    }
}