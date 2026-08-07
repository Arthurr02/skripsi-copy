<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// Impor Semua Model yang Diperlukan
use App\Models\Pendaftaran;
use App\Models\Jabatan;
use App\Models\PeriodeRekrutmen;
use App\Models\Organisasi;
use App\Models\Tahapan;
use App\Models\PengumpulanTugas;

class RekrutmenDiikutiController extends Controller
{
    /**
     * Menampilkan daftar rekrutmen yang sedang/pernah diikuti oleh mahasiswa
     */
    public function index()
    {
        $nimMahasiswa = Auth::user()->nim;

        // 1. Ambil semua riwayat pendaftaran mahasiswa ini
        $pendaftarans = Pendaftaran::where('nim', $nimMahasiswa)
            ->orderBy('created_at', 'desc')
            ->get();

        // 2. Petakan relasi secara manual
        $rekrutmenDiikuti = [];

        foreach ($pendaftarans as $daftar) {
            $jabatan1 = Jabatan::find($daftar->jabatan_1_id);
            $jabatan2 = $daftar->jabatan_2_id
                ? Jabatan::find($daftar->jabatan_2_id)
                : null;

            if ($jabatan1) {
                $periode = PeriodeRekrutmen::find($jabatan1->periode_rekrutmen_id);

                if ($periode) {
                    $organisasi = Organisasi::find($periode->organisasi_id);

                    // Kemas ke dalam object stdClass untuk dikirim ke view
                    $rekrutmenDiikuti[] = (object) [
                        'id' => $daftar->id, // 🌟 KUNCI: Dipastikan menggunakan 'id' agar cocok dengan $item->id di Blade
                        'periode' => $periode,
                        'organisasi' => $organisasi,
                        'jabatan_1' => $jabatan1,
                        'jabatan_2' => $jabatan2,
                        'tanggal_daftar' => $daftar->created_at,
                    ];
                }
            }
        }

        return view('mahasiswa.diikuti.index', compact('rekrutmenDiikuti'));
    }

    /**
     * Menampilkan detail timeline tahapan berdasarkan ID Pendaftaran
     */
    public function showTahapan($id)
    {
        $user = Auth::user();

        // 1. Ambil pendaftaran dengan Eager Loading menggunakan NAMA FUNGSI RELASI
        $pendaftaran = Pendaftaran::with([
            'pilihanJabatan1.periode.organisasi' // Pastikan relasinya bernama 'periode' di model Jabatan
        ])
            ->where('id', $id)
            ->where('nim', $user->nim) // Proteksi keamanan akun mahasiswa
            ->firstOrFail();

        // 2. Ambil tahapan sekaligus filter tugas HANYA untuk formasi prioritas utama
        $tahapans = Tahapan::with([
            'tugas' => function ($query) use ($pendaftaran) {
                $query->where('jabatan_id', $pendaftaran->jabatan_1_id); // Di dalam query builder, kita tetap pakai nama kolom DB
            }
        ])
            ->where('periode_rekrutmen_id', $pendaftaran->pilihanJabatan1->periode_rekrutmen_id) // Pakai 'pilihanJabatan1', BUKAN 'jabatan_1'
            ->orderBy('urutan_tahapan', 'asc')
            ->get();

        // 3. Ambil array ID tugas yang sudah dikerjakan untuk efisiensi Blade
        $tugasDikumpulkan = PengumpulanTugas::where('pendaftaran_id', $pendaftaran->id)
            ->pluck('tugas_id')
            ->toArray();

        return view('mahasiswa.diikuti.daftar-tahapan', compact(
            'pendaftaran',
            'tahapans',
            'tugasDikumpulkan'
        ));
    }
}