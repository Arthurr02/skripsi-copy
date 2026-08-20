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
use App\Models\Tugas; // 🌟 DITAMBAHKAN: Model Tugas
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
                        'id' => $daftar->id,
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

        // 1. Ambil pendaftaran dengan Eager Loading
        $pendaftaran = Pendaftaran::with([
            'pilihanJabatan1.periode.organisasi'
        ])
            ->where('id', $id)
            ->where('nim', $user->nim) // Proteksi keamanan akun mahasiswa
            ->firstOrFail();

        // 2. Setup Data Header (Relasi Organisasi, Banner, dan Jabatan)
        $jabatanUtama = $pendaftaran->pilihanJabatan1;
        $periode = $jabatanUtama ? $jabatanUtama->periode : null;
        $organisasi = $periode ? $periode->organisasi : null;

        $namaOrganisasi = $organisasi->nama_organisasi ?? 'Organisasi';

        $avatarUrl = '';
        if ($organisasi) {
            if (!empty($organisasi->avatar_google)) {
                $avatarUrl = str_replace('http://', 'https://', $organisasi->avatar_google);
            } elseif (!empty($organisasi->lampiran_logo)) {
                $avatarUrl = asset('storage/' . $organisasi->lampiran_logo);
            }
        }

        $bannerData = $periode->lampiran_banner ?? null;
        $bannerArray = is_string($bannerData) ? json_decode($bannerData, true) : $bannerData;
        $bannerPath = is_array($bannerArray) && count($bannerArray) > 0 ? $bannerArray[0] : null;

        $namaJabatanUtama = $jabatanUtama->nama_jabatan ?? 'Jabatan Tidak Diketahui';
        $namaPosisiUtama = (!empty($jabatanUtama->nama_posisi) && $jabatanUtama->nama_posisi !== '-')
            ? $jabatanUtama->nama_posisi
            : 'Tanpa Divisi Khusus';

        // 3. Ambil dan Format Data Tahapan (Tanpa membebani Blade)
        $now = now();
        $tahapans = Tahapan::with([
            'tugas' => function ($query) use ($pendaftaran) {
                $query->where('jabatan_id', $pendaftaran->jabatan_1_id);
            }
        ])
            ->where('periode_rekrutmen_id', $jabatanUtama->periode_rekrutmen_id ?? 0)
            ->orderBy('urutan_tahapan', 'asc')
            ->get()
            ->map(function ($tahapan) use ($now) {
                // Konversi dan injeksi properti waktu langsung ke objek
                $mulai = \Carbon\Carbon::parse($tahapan->waktu_mulai);
                $berakhir = \Carbon\Carbon::parse($tahapan->waktu_berakhir);

                $tahapan->parsed_mulai = $mulai;
                $tahapan->parsed_berakhir = $berakhir;
                $tahapan->is_past = $berakhir->isPast();
                $tahapan->is_active = $mulai->lte($now) && $berakhir->gte($now);
                $tahapan->is_future = $mulai->isFuture();
                $tahapan->is_waktu_tunggal = $mulai->equalTo($berakhir);

                // Parsing Lampiran Pedoman
                $pedomanArray = is_string($tahapan->lampiran_tahapan) ? json_decode($tahapan->lampiran_tahapan, true) : $tahapan->lampiran_tahapan;
                $tahapan->pedoman_path = is_array($pedomanArray) && count($pedomanArray) > 0 ? $pedomanArray[0] : null;

                return $tahapan;
            });

        // 4. Ambil Tugas Dikumpulkan
        $tugasDikumpulkan = PengumpulanTugas::where('pendaftaran_id', $pendaftaran->id)
            ->pluck('tugas_id')
            ->toArray();

        return view('mahasiswa.diikuti.daftar-tahapan', compact(
            'pendaftaran',
            'tahapans',
            'tugasDikumpulkan',
            'namaOrganisasi',
            'avatarUrl',
            'bannerPath',
            'namaJabatanUtama',
            'namaPosisiUtama'
        ));
    }

    // =========================================================================
    // FUNGSI BARU UNTUK MENANGANI PENUGASAN (FORM, FILE, & WAWANCARA)
    // =========================================================================

    /**
     * Menampilkan halaman khusus untuk mengisi form dinamis tugas
     */
    /**
     * Menampilkan halaman khusus untuk mengisi form dinamis tugas
     */
    /**
     * Menampilkan halaman khusus untuk mengisi form dinamis tugas
     */
    public function showTugasDetail($pendaftaran_id, $tugas_id)
    {
        $user = Auth::user();

        // 1. Validasi Kepemilikan Data Pendaftaran
        $pendaftaran = Pendaftaran::with('pilihanJabatan1.periode.organisasi')
            ->where('id', $pendaftaran_id)
            ->where('nim', $user->nim)
            ->firstOrFail();

        // 2. Ambil Data Tugas
        $tugas = Tugas::findOrFail($tugas_id);

        // 3. Ambil Riwayat Pengumpulan (Jika mahasiswa sudah pernah mengisi)
        $pengumpulan = PengumpulanTugas::where('pendaftaran_id', $pendaftaran->id)
            ->where('tugas_id', $tugas->id)
            ->first();

        // 4. Proses JSON Struktur Form Dinamis yang dibuat Panitia
        $lampiranData = is_string($tugas->lampiran_tugas)
            ? json_decode($tugas->lampiran_tugas, true)
            : ($tugas->lampiran_tugas ?? []);

        $komponenForm = $lampiranData['form'] ?? [];

        // 5. Proses JSON Jawaban Mahasiswa (Disesuaikan dengan DB: lampiran_jawaban)
        $jawabanSebelumnya = [];
        if ($pengumpulan && !empty($pengumpulan->lampiran_jawaban)) {
            $parsedJawaban = is_string($pengumpulan->lampiran_jawaban)
                ? json_decode($pengumpulan->lampiran_jawaban, true)
                : $pengumpulan->lampiran_jawaban;

            // Ambil isian yang ada di dalam kunci "form"
            $jawabanSebelumnya = $parsedJawaban['form'] ?? $parsedJawaban;
        }

        // Arahkan ke file View yang baru
        return view('mahasiswa.diikuti.tugas-form', compact(
            'pendaftaran',
            'tugas',
            'komponenForm',
            'pengumpulan',
            'jawabanSebelumnya'
        ));
    }

    /**
     * Memproses penerimaan unggahan file atau jawaban form dinamis
     */
    public function submitTugas(Request $request, $pendaftaran_id, $tugas_id)
    {
        $user = Auth::user();

        $pendaftaran = Pendaftaran::where('id', $pendaftaran_id)
            ->where('nim', $user->nim)
            ->firstOrFail();

        $tugas = Tugas::findOrFail($tugas_id);

        $pengumpulan = PengumpulanTugas::firstOrNew([
            'pendaftaran_id' => $pendaftaran->id,
            'tugas_id' => $tugas->id
        ]);

        // Ambil data JSON lama agar tidak terhapus saat update
        $existingJawaban = [];
        if (!empty($pengumpulan->lampiran_jawaban)) {
            $existingJawaban = is_string($pengumpulan->lampiran_jawaban)
                ? json_decode($pengumpulan->lampiran_jawaban, true)
                : $pengumpulan->lampiran_jawaban;
        }

        // 3. Logika Penyimpanan untuk Tugas Tipe "Form Dinamis"
        if ($request->has('jawaban_form')) {
            $existingJawaban['form'] = $request->input('jawaban_form');
        }

        // 4. Logika Penyimpanan untuk Tugas Tipe "Upload File / Project"
        if ($request->hasFile('file_jawaban')) {
            $path = $request->file('file_jawaban')->store('rekrutmen/tugas', 'public');
            $existingJawaban['berkas'] = [$path];
        }

        // 5. Eksekusi Simpan ke Kolom lampiran_jawaban
        $pengumpulan->lampiran_jawaban = json_encode($existingJawaban);
        $pengumpulan->save();

        return back()->with('success', 'Berhasil! Jawaban penugasan Anda telah disimpan.');
    }

    /**
     * Memproses konfirmasi kehadiran untuk tipe tugas Wawancara
     */
    public function konfirmasiWawancara(Request $request, $pendaftaran_id, $tugas_id)
    {
        $user = Auth::user();

        $pendaftaran = Pendaftaran::where('id', $pendaftaran_id)
            ->where('nim', $user->nim)
            ->firstOrFail();

        $tugas = Tugas::findOrFail($tugas_id);

        $pengumpulan = PengumpulanTugas::firstOrNew([
            'pendaftaran_id' => $pendaftaran->id,
            'tugas_id' => $tugas->id
        ]);

        // Catat kehadiran di dalam JSON
        $pengumpulan->jawaban_form = json_encode([
            'status_wawancara' => 'Hadir',
            'waktu_konfirmasi' => now()->toDateTimeString()
        ]);

        $pengumpulan->save();

        return back()->with('success', 'Konfirmasi kehadiran wawancara Anda berhasil dicatat!');
    }
}