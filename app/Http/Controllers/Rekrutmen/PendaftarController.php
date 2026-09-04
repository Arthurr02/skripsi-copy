<?php

namespace App\Http\Controllers\Rekrutmen;

use App\Http\Controllers\Controller;
use App\Models\Jabatan;
use App\Models\Panitia;
use App\Models\Pendaftaran;
use App\Models\PeriodeRekrutmen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PendaftarController extends Controller
{
    // =================================================================
    // FUNGSI UNTUK MENAMPILKAN DAFTAR PENDAFTAR REKRUTMEN AKTIF
    // =================================================================
    public function index(Request $request)
    {
        // 🌟 1. CEK IDENTITAS LOGIN (Organisasi / Panitia)
        if (Auth::guard('organisasi')->check()) {
            $organisasiId = Auth::guard('organisasi')->id();
        } else {
            $nimPanitia = Auth::user()->nim;

            // Diperbaiki: Hapus panitia_rekrutmen karena kolom tidak ada di DB
            $kepanitiaan = Panitia::query()
                ->with('periode')
                ->where('nim', $nimPanitia)
                ->whereHas('periode', fn ($query) => $query->whereIn('status_aktif', [1, 2]))
                ->latest()
                ->first();

            if (! $kepanitiaan) {
                abort(403, 'Akses ditolak. Anda bukan panitia yang sah.');
            }

            $periodePanitia = $kepanitiaan->periode;
            abort_unless($periodePanitia, 403, 'Periode rekrutmen panitia tidak ditemukan.');
            $organisasiId = $periodePanitia->organisasi_id;
        }

        // 🌟 2. AMBIL PERIODE REKRUTMEN YANG SEDANG AKTIF / BERJALAN
        $periodeAktif = PeriodeRekrutmen::where('organisasi_id', $organisasiId)
            ->whereIn('status_aktif', [1, 2])
            ->latest()
            ->first();

        abort_unless($periodeAktif, 404);

        $listJabatan = Jabatan::query()
            ->where('periode_rekrutmen_id', $periodeAktif->id)
            ->orderBy('nama_posisi')
            ->orderBy('nama_jabatan')
            ->get();

        // 🌟 3. SUSUN KUERI DASAR PENDAFTAR
        // Diperbaiki: Karena tabel pendaftaran tidak punya periode_rekrutmen_id,
        // kita hubungkan lewat jabatan_1_id
        $query = Pendaftaran::with([
            'mahasiswa', // Relasi harus berdasarkan 'nim' (sesuai DB)
            'pilihanJabatan1',
            'pilihanJabatan2',
        ])
            ->whereHas('pilihanJabatan1', function ($q) use ($periodeAktif) {
                $q->where('periode_rekrutmen_id', $periodeAktif->id);
            })
            ->select('pendaftaran.*'); // Sesuai nama tabel di DB (bukan pendaftarans)

        $statusSeleksiTersedia = (clone $query)
            ->whereNotNull('status_seleksi')
            ->where('status_seleksi', '!=', '')
            ->distinct()
            ->orderBy('status_seleksi')
            ->pluck('status_seleksi')
            ->prepend('Menunggu Seleksi')
            ->unique()
            ->values();

        if ($request->filled('filter_jabatan')) {
            $jabatanId = (int) $request->input('filter_jabatan');
            $pilihanTipe = collect($request->input('pilihan_tipe', ['1', '2']))
                ->intersect(['1', '2'])
                ->values();

            if ($pilihanTipe->isEmpty()) {
                $pilihanTipe = collect(['1', '2']);
            }

            $query->where(function ($query) use ($jabatanId, $pilihanTipe): void {
                if ($pilihanTipe->contains('1')) {
                    $query->orWhere('jabatan_1_id', $jabatanId);
                }

                if ($pilihanTipe->contains('2')) {
                    $query->orWhere('jabatan_2_id', $jabatanId);
                }
            });
        }

        if ($request->filled('filter_status')) {
            $status = $request->string('filter_status')->toString();

            if ($status === 'Menunggu Seleksi') {
                $query->where(function ($query): void {
                    $query->whereNull('status_seleksi')
                        ->orWhere('status_seleksi', '')
                        ->orWhere('status_seleksi', 'Menunggu Seleksi');
                });
            } else {
                $query->where('status_seleksi', $status);
            }
        }

        $query->latest('pendaftaran.created_at');

        // 🌟 7. EKSEKUSI KUERI & PAGINATION
        $daftarPeserta = $query->paginate(25)->withQueryString();
        $totalPendaftar = $daftarPeserta->total();

        // 🌟 8. LEMPAR KE TAMPILAN
        $routePrefix = Auth::guard('organisasi')->check() ? 'organisasi.' : 'panitia.';

        return view('rekrutmen.pendaftar.index', compact(
            'daftarPeserta',
            'totalPendaftar',
            'periodeAktif',
            'listJabatan',
            'statusSeleksiTersedia',
            'routePrefix'
        ));
    }
}
