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
    // 1. FUNGSI UNTUK MENAMPILKAN HALAMAN PILIH JABATAN / POSISI
    // Akses: /organisasi/rekrutmen/seleksi
    // =================================================================
    public function index(Request $request)
    {
        // Cek Identitas Login Organisasi
        if (Auth::guard('organisasi')->check()) {
            $organisasiId = Auth::guard('organisasi')->id();
        } else {
            $nimPanitia = Auth::user()->nim;
            $kepanitiaan = Panitia::where('nim', $nimPanitia)
                ->where('panitia_rekrutmen', 1)
                ->latest()
                ->first();

            if (!$kepanitiaan) {
                abort(403, 'Anda tidak terdaftar sebagai panitia aktif.');
            }
            $periodePanitia = PeriodeRekrutmen::find($kepanitiaan->periode_rekrutmen_id);
            $organisasiId = $periodePanitia->organisasi_id;
        }

        // Ambil periode aktif
        $periodeAktif = PeriodeRekrutmen::where('organisasi_id', $organisasiId)
            ->where('status_aktif', 1) // Pastikan kolom ini sesuai dengan database Anda (1 atau 'Aktif')
            ->latest()
            ->first();

        // Ambil daftar jabatan (posisi) untuk dipilih panitia
        $listJabatan = collect();
        if ($periodeAktif) {
            $listJabatan = Jabatan::withCount(['pendaftaran1', 'pendaftaran2'])
                ->where('periode_rekrutmen_id', $periodeAktif->id)
                ->get();
        }

        return view('rekrutmen.seleksi.daftar-posisi', compact('periodeAktif', 'listJabatan'));
    }

    // =================================================================
    // FUNGSI UNTUK MENU PENGERJAAN SELEKSI BERDASARKAN JABATAN
    // Rute yang disarankan: Route::get('/seleksi/jabatan/{jabatan_id}', ... )
    // =================================================================
    public function tahapanJabatan(Request $request, $jabatan_id)
    {
        // 🌟 1. CEK IDENTITAS LOGIN: Organisasi atau Panitia?
        if (Auth::guard('organisasi')->check()) {
            $organisasiId = Auth::guard('organisasi')->id();
        } else {
            $nimPanitia = Auth::user()->nim;
            $kepanitiaan = Panitia::where('nim', $nimPanitia)
                ->where('panitia_rekrutmen', 1)
                ->latest()
                ->first();

            if (!$kepanitiaan) {
                abort(403, 'Anda tidak terdaftar sebagai panitia aktif.');
            }
            $periodePanitia = PeriodeRekrutmen::find($kepanitiaan->periode_rekrutmen_id);
            $organisasiId = $periodePanitia->organisasi_id;
        }

        // 🌟 2. AMBIL DATA JABATAN & PERIODE
        $jabatan = Jabatan::with('periode')->findOrFail($jabatan_id);
        $periodeAktif = $jabatan->periode;

        // Validasi keamanan (Opsional: pastikan jabatan ini milik organisasi yang sedang login)
        if ($periodeAktif->organisasi_id != $organisasiId) {
            abort(403, 'Akses ditolak.');
        }

        $now = Carbon::now();

        // 🌟 3. AMBIL DATA PELAMAR UNTUK JABATAN INI
        $pendaftarans = Pendaftaran::with(['mahasiswa:nim,nama_lengkap'])
            ->where('periode_rekrutmen_id', $periodeAktif->id)
            ->where(function ($q) use ($jabatan_id) {
                $q->where('jabatan_1_id', $jabatan_id)
                    ->orWhere('jabatan_2_id', $jabatan_id);
            })
            ->get();

        $totalPelamar = $pendaftarans->count();

        // 🌟 4. AMBIL TAHAPAN & TUGAS, LALU RAKIT DATA UNTUK VIEW & MODAL ALPINE.JS
        $tahapans = Tahapan::with(['tugas.pengumpulanTugas'])
            ->where('periode_rekrutmen_id', $periodeAktif->id)
            ->orderBy('urutan_tahapan', 'asc')
            ->get()
            ->map(function ($tahapan) use ($now, $pendaftarans) {
                // Formatting Waktu (Sesuai kebutuhan View)
                $tahapan->parsed_mulai = Carbon::parse($tahapan->waktu_mulai);
                $tahapan->parsed_berakhir = Carbon::parse($tahapan->waktu_berakhir);

                $tahapan->is_past = $now->gt($tahapan->parsed_berakhir);
                $tahapan->is_future = $now->lt($tahapan->parsed_mulai);
                $tahapan->is_active = $now->between($tahapan->parsed_mulai, $tahapan->parsed_berakhir);
                $tahapan->is_waktu_tunggal = $tahapan->parsed_mulai->isSameDay($tahapan->parsed_berakhir);

                // Memetakan Tugas dan Menginjeksi Data Jawaban Peserta
                $tahapan->tugas->map(function ($tugas) use ($pendaftarans) {
                    $pesertaJawaban = [];
                    $jumlahPengumpul = 0;

                    foreach ($pendaftarans as $pendaftaran) {
                        // Cek apakah pendaftar ini sudah mengumpulkan tugas ini
                        $pengumpulan = $tugas->pengumpulanTugas->where('pendaftaran_id', $pendaftaran->id)->first();
                        $sudahKumpul = $pengumpulan ? true : false;

                        if ($sudahKumpul) {
                            $jumlahPengumpul++;
                        }

                        // Format array json untuk dibaca Alpine.js di Modal View
                        $pesertaJawaban[] = [
                            'id' => $pendaftaran->id,
                            'nama' => $pendaftaran->mahasiswa->nama_lengkap ?? 'Peserta Anonim',
                            'sudah_kumpul' => $sudahKumpul,
                        ];
                    }

                    // Tempelkan data ke dalam objek $tugas
                    $tugas->jumlah_pengumpul = $jumlahPengumpul;
                    $tugas->peserta_jawaban = $pesertaJawaban;

                    return $tugas;
                });

                return $tahapan;
            });

        // 🌟 5. LEMPAR SEMUA DATA KE VIEW
        return view('rekrutmen.seleksi.index', [
            'jabatan' => $jabatan,
            'namaRekrutmen' => $periodeAktif->nama_rekrutmen,
            'namaJabatan' => $jabatan->nama_jabatan,
            'totalPelamar' => $totalPelamar,
            'tahapans' => $tahapans
        ]);
    }
}