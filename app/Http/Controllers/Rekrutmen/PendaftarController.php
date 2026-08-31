<?php

namespace App\Http\Controllers\Rekrutmen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\PeriodeRekrutmen;
use App\Models\Pendaftaran;
use App\Models\Jabatan;
use App\Models\Panitia;

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
            $kepanitiaan = Panitia::where('nim', $nimPanitia)
                ->latest()
                ->first();

            if (!$kepanitiaan) {
                abort(403, 'Akses ditolak. Anda bukan panitia yang sah.');
            }

            $periodePanitia = PeriodeRekrutmen::find($kepanitiaan->periode_rekrutmen_id);
            $organisasiId = $periodePanitia->organisasi_id;
        }

        // 🌟 2. AMBIL PERIODE REKRUTMEN YANG SEDANG AKTIF / BERJALAN
        $periodeAktif = PeriodeRekrutmen::where('organisasi_id', $organisasiId)
            ->whereIn('status_aktif', [1, 2])
            ->latest()
            ->first();

        if (!$periodeAktif) {
            return view('rekrutmen.pendaftar.index', [
                'daftarPeserta' => collect(),
                'totalPendaftar' => 0,
                'periodeAktif' => null,
                'listJabatan' => collect()
            ]);
        }

        // Ambil daftar jabatan untuk filter Frontend
        // KODE BARU (Diperbarui dengan relasi posisi)
        // UBAH DARI INI:
// $listJabatan = Jabatan::with('posisi')
//     ->where('periode_rekrutmen_id', $periodeAktif->id)
//     ->get();

        // MENJADI SEPERTI INI:
        $listJabatan = Jabatan::where('periode_rekrutmen_id', $periodeAktif->id)->get();

        // 🌟 3. SUSUN KUERI DASAR PENDAFTAR
        // Diperbaiki: Karena tabel pendaftaran tidak punya periode_rekrutmen_id, 
        // kita hubungkan lewat jabatan_1_id
        $query = Pendaftaran::with([
            'mahasiswa', // Relasi harus berdasarkan 'nim' (sesuai DB)
            'pilihanJabatan1',
            'pilihanJabatan2'
        ])
            ->whereHas('pilihanJabatan1', function ($q) use ($periodeAktif) {
                $q->where('periode_rekrutmen_id', $periodeAktif->id);
            })
            ->select('pendaftaran.*'); // Sesuai nama tabel di DB (bukan pendaftarans)

        // 🌟 4. LOGIKA FILTER: JABATAN & CHECKBOX PILIHAN
        if ($request->filled('filter_jabatan')) {
            $jabatanId = $request->filter_jabatan;
            $tipePilihan = $request->input('pilihan_tipe', []); // Array checkbox [1, 2]

            $query->where(function ($q) use ($jabatanId, $tipePilihan) {
                // Jika user mencentang keduanya atau tidak mencentang sama sekali (default perilaku awal)
                if (empty($tipePilihan) || (in_array('1', $tipePilihan) && in_array('2', $tipePilihan))) {
                    $q->where('jabatan_1_id', $jabatanId)
                        ->orWhere('jabatan_2_id', $jabatanId);
                } else {
                    // Jika hanya centang Pilihan 1 saja
                    if (in_array('1', $tipePilihan)) {
                        $q->orWhere('jabatan_1_id', $jabatanId);
                    }
                    // Jika hanya centang Pilihan 2 saja
                    if (in_array('2', $tipePilihan)) {
                        $q->orWhere('jabatan_2_id', $jabatanId);
                    }
                }
            });
        }

        // 🌟 5. LOGIKA FILTER: STATUS SELEKSI 
        if ($request->filled('filter_status')) {
            $status = $request->filter_status;

            if ($status == 'Menunggu Seleksi') {
                $query->where(function ($q) {
                    $q->whereNull('status_seleksi')
                        ->orWhere('status_seleksi', 'Menunggu Seleksi')
                        ->orWhere('status_seleksi', '');
                });
            } else {
                $query->where('status_seleksi', $status);
            }
        }

        // 🌟 6. LOGIKA SORTING 
        if ($request->sort === 'nama') {
            // Diperbaiki: Mahasiswa menggunakan 'nim' sebagai primary key
            $query->join('mahasiswa', 'pendaftaran.nim', '=', 'mahasiswa.nim')
                ->orderBy('mahasiswa.nama_lengkap', 'asc');
        } else {
            // Default: Terbaru
            $query->latest('pendaftaran.created_at');
        }

        // 🌟 7. EKSEKUSI KUERI & PAGINATION
        $daftarPeserta = $query->paginate(25)->withQueryString();
        $totalPendaftar = $daftarPeserta->total();

        // 🌟 8. LEMPAR KE TAMPILAN
        return view('rekrutmen.pendaftar.index', compact(
            'daftarPeserta',
            'totalPendaftar',
            'periodeAktif',
            'listJabatan'
        ));
    }
}