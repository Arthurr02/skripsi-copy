<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\KeputusanSeleksi;
use App\Models\Pendaftaran;
use App\Models\PengumpulanTugas;
use App\Models\Tahapan;
use App\Models\Tugas;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class RekrutmenDiikutiController extends Controller
{
    /**
     * Menampilkan daftar rekrutmen yang sedang/pernah diikuti oleh mahasiswa
     */
    public function index()
    {
        $nimMahasiswa = Auth::user()->nim;

        // 1. Ambil pendaftaran pada rekrutmen yang masih berjalan.
        // Rekrutmen yang telah ditutup ditampilkan khusus pada menu Riwayat.
        $pendaftarans = Pendaftaran::query()
            ->with([
                'pilihanJabatan1.periode.organisasi',
                'pilihanJabatan2',
            ])
            ->where('nim', $nimMahasiswa)
            ->whereHas('pilihanJabatan1.periode', fn ($query) => $query->whereIn('status_aktif', [1, 2]))
            ->orderBy('created_at', 'desc')
            ->get();

        $periodeIds = $pendaftarans
            ->pluck('pilihanJabatan1.periode_rekrutmen_id')
            ->filter()
            ->unique()
            ->values();
        $tahapanAktifPerPeriode = Tahapan::query()
            ->whereIn('periode_rekrutmen_id', $periodeIds)
            ->where('waktu_mulai', '<=', now())
            ->where('waktu_berakhir', '>=', now())
            ->orderBy('urutan_tahapan')
            ->get()
            ->keyBy('periode_rekrutmen_id');

        // Siapkan data presentasi di controller; Blade hanya merender kartu.
        $rekrutmenDiikuti = $pendaftarans->map(function (Pendaftaran $pendaftaran) use ($tahapanAktifPerPeriode) {
            $jabatanUtama = $pendaftaran->pilihanJabatan1;
            $periode = $jabatanUtama->periode;
            $organisasi = $periode->organisasi;
            $avatarUrl = $organisasi?->avatar_google
                ? str_replace('http://', 'https://', $organisasi->avatar_google)
                : ($organisasi?->lampiran_logo ? asset('storage/'.$organisasi->lampiran_logo) : '');

            return (object) [
                'id' => $pendaftaran->id,
                'periode' => $periode,
                'jabatan_1' => $jabatanUtama,
                'jabatan_2' => $pendaftaran->pilihanJabatan2,
                'nama_organisasi' => $organisasi?->nama_organisasi ?? 'Organisasi',
                'avatar_url' => $avatarUrl,
                'banner_path' => $periode->lampiran_banner[0] ?? null,
                'nama_tahapan_berjalan' => $tahapanAktifPerPeriode->get($periode->id)?->nama_tahapan
                    ?? 'Menunggu / Telah Selesai',
            ];
        });

        return view('mahasiswa.diikuti.index', compact('rekrutmenDiikuti'));
    }

    /**
     * Menampilkan detail timeline tahapan berdasarkan ID Pendaftaran
     */
    public function showTahapan($id)
    {
        return $this->tampilkanTahapan((int) $id, false);
    }

    /**
     * Menampilkan tahapan dari rekrutmen yang sudah ditutup.
     * Halaman ini tetap memakai alur yang sama, tetapi hanya boleh dibuka dari
     * menu Riwayat dan seluruh penugasan bersifat baca-saja.
     */
    public function showTahapanRiwayat($id)
    {
        return $this->tampilkanTahapan((int) $id, true);
    }

    private function tampilkanTahapan(int $id, bool $isRiwayatMahasiswa)
    {
        $user = Auth::user();

        // 1. Ambil pendaftaran dengan Eager Loading
        $pendaftaran = Pendaftaran::with([
            'pilihanJabatan1.periode.organisasi',
        ])
            ->where('id', $id)
            ->where('nim', $user->nim) // Proteksi keamanan akun mahasiswa
            ->firstOrFail();

        // 2. Setup Data Header (Relasi Organisasi, Banner, dan Jabatan)
        $jabatanUtama = $pendaftaran->pilihanJabatan1;
        $periode = $jabatanUtama ? $jabatanUtama->periode : null;
        $organisasi = $periode ? $periode->organisasi : null;

        abort_unless($periode, 404);
        abort_unless(
            $isRiwayatMahasiswa
                ? (int) $periode->status_aktif === 0
                : in_array((int) $periode->status_aktif, [1, 2], true),
            404,
        );

        $namaOrganisasi = $organisasi->nama_organisasi ?? 'Organisasi';

        $avatarUrl = '';
        if ($organisasi) {
            if (! empty($organisasi->avatar_google)) {
                $avatarUrl = str_replace('http://', 'https://', $organisasi->avatar_google);
            } elseif (! empty($organisasi->lampiran_logo)) {
                $avatarUrl = asset('storage/'.$organisasi->lampiran_logo);
            }
        }

        $bannerData = $periode->lampiran_banner ?? null;
        $bannerArray = is_string($bannerData) ? json_decode($bannerData, true) : $bannerData;
        $bannerPath = is_array($bannerArray) && count($bannerArray) > 0 ? $bannerArray[0] : null;

        $namaJabatanUtama = $jabatanUtama->nama_jabatan ?? 'Jabatan Tidak Diketahui';
        $namaPosisiUtama = (! empty($jabatanUtama->nama_posisi) && $jabatanUtama->nama_posisi !== '-')
            ? $jabatanUtama->nama_posisi
            : 'Tanpa Divisi Khusus';

        // 3. Ambil dan Format Data Tahapan (Tanpa membebani Blade)
        $now = now();
        $keputusanPeserta = KeputusanSeleksi::query()
            ->with('tahapan:id,periode_rekrutmen_id,urutan_tahapan')
            ->where('pendaftaran_id', $pendaftaran->id)
            ->where('jabatan_id', $pendaftaran->jabatan_1_id)
            ->get();
        $keputusanPerTahapan = $keputusanPeserta->keyBy('tahapan_id');
        $keputusanGagal = $keputusanPeserta->where('keputusan', 'tidak_lolos');

        $tahapans = Tahapan::with([
            'tugas' => function ($query) use ($pendaftaran) {
                $query->where('jabatan_id', $pendaftaran->jabatan_1_id);
            },
        ])
            ->where('periode_rekrutmen_id', $jabatanUtama->periode_rekrutmen_id ?? 0)
            ->orderBy('urutan_tahapan', 'asc')
            ->get()
            ->map(function ($tahapan) use ($now, $keputusanPerTahapan, $keputusanGagal) {
                // Konversi dan injeksi properti waktu langsung ke objek
                $mulai = Carbon::parse($tahapan->waktu_mulai);
                $berakhir = Carbon::parse($tahapan->waktu_berakhir);

                $tahapan->parsed_mulai = $mulai;
                $tahapan->parsed_berakhir = $berakhir;
                $tahapan->is_past = $berakhir->isPast();
                $tahapan->is_active = $mulai->lte($now) && $berakhir->gte($now);
                $tahapan->is_future = $mulai->isFuture();
                $tahapan->is_waktu_tunggal = $mulai->equalTo($berakhir);

                $keputusanTahapan = $keputusanPerTahapan->get($tahapan->id);
                $tahapan->is_dinyatakan_gagal = $keputusanTahapan?->keputusan === 'tidak_lolos';
                $tahapan->dikunci_karena_tidak_lulus = ! $tahapan->is_dinyatakan_gagal
                    && $keputusanGagal->contains(fn ($keputusan) => $keputusan->tahapan
                        && $keputusan->tahapan->urutan_tahapan < $tahapan->urutan_tahapan);

                // Parsing Lampiran Pedoman
                $pedomanArray = is_string($tahapan->lampiran_tahapan) ? json_decode($tahapan->lampiran_tahapan, true) : $tahapan->lampiran_tahapan;
                $tahapan->pedoman_path = is_array($pedomanArray) && count($pedomanArray) > 0 ? $pedomanArray[0] : null;

                return $tahapan;
            });

        // 4. Ambil Tugas Dikumpulkan
        $pengumpulanTugas = PengumpulanTugas::where('pendaftaran_id', $pendaftaran->id)
            ->get()
            ->keyBy('tugas_id');
        $tugasDikumpulkan = $pengumpulanTugas->keys()->all();

        $tahapans->each(function ($tahapan) use ($pengumpulanTugas) {
            $tahapan->tugas->each(function ($tugas) use ($pengumpulanTugas) {
                $tugas->pengumpulan_mahasiswa = $pengumpulanTugas->get($tugas->id);
            });
        });

        $routeDetailTugas = $isRiwayatMahasiswa
            ? 'mahasiswa.riwayat.diikuti.tugas'
            : 'mahasiswa.rekrutmen.diikuti.tugas_detail';

        return view('mahasiswa.diikuti.daftar-tahapan', compact(
            'pendaftaran',
            'tahapans',
            'tugasDikumpulkan',
            'namaOrganisasi',
            'avatarUrl',
            'bannerPath',
            'namaJabatanUtama',
            'namaPosisiUtama',
            'isRiwayatMahasiswa',
            'routeDetailTugas',
        ));
    }

    // =========================================================================
    // FUNGSI BARU UNTUK MENANGANI PENUGASAN (FORM, FILE, & WAWANCARA)
    // =========================================================================

    /** Menampilkan halaman khusus untuk mengisi form dinamis tugas aktif. */
    public function showTugasDetail($pendaftaran_id, $tugas_id)
    {
        return $this->tampilkanDetailTugas((int) $pendaftaran_id, (int) $tugas_id, false);
    }

    /** Menampilkan detail tugas arsip melalui rute riwayat yang bersifat baca-saja. */
    public function showTugasRiwayat($pendaftaran_id, $tugas_id)
    {
        return $this->tampilkanDetailTugas((int) $pendaftaran_id, (int) $tugas_id, true);
    }

    private function tampilkanDetailTugas(int $pendaftaran_id, int $tugas_id, bool $dibukaDariRiwayat)
    {
        $user = Auth::user();

        // 1. Validasi Kepemilikan Data Pendaftaran
        $pendaftaran = Pendaftaran::with('pilihanJabatan1.periode.organisasi')
            ->where('id', $pendaftaran_id)
            ->where('nim', $user->nim)
            ->firstOrFail();

        // Tugas wajib merupakan tugas jabatan pilihan mahasiswa pada periode yang sama.
        // Hak mengerjakan divalidasi setelah waktu tahapan diketahui agar jawaban
        // lama yang telah dikirim dapat tetap dibaca dari menu Riwayat.
        $tugas = $this->temukanTugasMilikPendaftaran($pendaftaran, $tugas_id, false);

        $statusPeriode = (int) $pendaftaran->pilihanJabatan1?->periode?->status_aktif;
        abort_unless(
            $dibukaDariRiwayat ? $statusPeriode === 0 : in_array($statusPeriode, [1, 2], true),
            404,
        );

        // 3. Ambil Riwayat Pengumpulan (Jika mahasiswa sudah pernah mengisi)
        $pengumpulan = PengumpulanTugas::where('pendaftaran_id', $pendaftaran->id)
            ->where('tugas_id', $tugas->id)
            ->first();

        $waktuMulai = Carbon::parse($tugas->tahapan->waktu_mulai);
        $waktuBerakhir = Carbon::parse($tugas->tahapan->waktu_berakhir);
        $dapatDikerjakan = now()->gte($waktuMulai) && now()->lt($waktuBerakhir);
        $isRiwayatMahasiswa = $dibukaDariRiwayat;
        $routeTahapan = $isRiwayatMahasiswa
            ? 'mahasiswa.riwayat.diikuti.tahapan'
            : 'mahasiswa.rekrutmen.diikuti.tahapan';
        $routeDetailTugas = $isRiwayatMahasiswa
            ? 'mahasiswa.riwayat.diikuti.tugas'
            : 'mahasiswa.rekrutmen.diikuti.tugas_detail';

        // Instruksi, struktur form, serta lampiran tugas tidak boleh dibuka
        // melalui URL langsung sebelum waktu tahapan dimulai.
        if (now()->lt($waktuMulai)) {
            return redirect()->route($routeTahapan, $pendaftaran->id)
                ->with('error', 'Informasi penugasan belum tersedia karena tahapan belum dimulai.');
        }

        if ($dapatDikerjakan) {
            $this->pastikanPesertaMasihBerhakMengerjakan($pendaftaran, $tugas);
        }

        if (! $dapatDikerjakan && ! $pengumpulan) {
            return redirect()->route(
                $routeTahapan,
                $pendaftaran->id,
            )
                ->with('error', 'Tugas ini tidak dapat dikerjakan karena belum dibuka atau waktu pengumpulan telah berakhir.');
        }

        // 4. Proses JSON Struktur Form Dinamis yang dibuat Panitia
        $lampiranData = is_string($tugas->lampiran_tugas)
            ? json_decode($tugas->lampiran_tugas, true)
            : ($tugas->lampiran_tugas ?? []);

        $komponenForm = $lampiranData['form'] ?? [];
        $lampiranPenugasan = array_values(array_filter((array) ($lampiranData['berkas'] ?? [])));

        // 5. Proses JSON Jawaban Mahasiswa (Disesuaikan dengan DB: lampiran_jawaban)
        $jawabanSebelumnya = [];
        if ($pengumpulan && ! empty($pengumpulan->lampiran_jawaban)) {
            $parsedJawaban = is_string($pengumpulan->lampiran_jawaban)
                ? json_decode($pengumpulan->lampiran_jawaban, true)
                : $pengumpulan->lampiran_jawaban;

            // Normalisasi kunci lama (label asli, slug, maupun indeks) agar
            // satu komponen form hanya dibaca melalui satu nama bidang.
            $jawabanSebelumnya = $this->normalisasiJawabanForm(
                $komponenForm,
                $parsedJawaban['form'] ?? $parsedJawaban,
            );
        }

        // Arahkan ke file View yang baru
        return view('mahasiswa.diikuti.tugas-form', compact(
            'pendaftaran',
            'tugas',
            'komponenForm',
            'lampiranPenugasan',
            'pengumpulan',
            'jawabanSebelumnya',
            'dapatDikerjakan',
            'waktuBerakhir',
            'isRiwayatMahasiswa',
            'routeDetailTugas',
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

        $tugas = $this->temukanTugasMilikPendaftaran($pendaftaran, (int) $tugas_id);
        $this->pastikanTugasSedangDibuka($tugas);

        $pengumpulan = PengumpulanTugas::firstOrNew([
            'pendaftaran_id' => $pendaftaran->id,
            'tugas_id' => $tugas->id,
        ]);

        // Ambil data JSON lama agar tidak terhapus saat update
        $existingJawaban = [];
        if (! empty($pengumpulan->lampiran_jawaban)) {
            $existingJawaban = is_string($pengumpulan->lampiran_jawaban)
                ? json_decode($pengumpulan->lampiran_jawaban, true)
                : $pengumpulan->lampiran_jawaban;
        }

        $existingJawaban['form'] = $this->normalisasiJawabanForm(
            $this->strukturFormTugas($tugas),
            $existingJawaban['form'] ?? [],
        );

        $this->validasiJawabanTugas($request, $tugas, $existingJawaban);

        // 3. Logika Penyimpanan untuk Tugas Tipe "Form Dinamis"
        if ($request->has('jawaban_form')) {
            $existingJawaban['form'] = array_merge(
                (array) ($existingJawaban['form'] ?? []),
                $request->input('jawaban_form', []),
            );
        }

        $strukturForm = $this->strukturFormTugas($tugas);
        $jumlahBidangFile = collect($strukturForm)->where('tipe', 'file')->count();

        foreach ($strukturForm as $indeks => $item) {
            if (($item['tipe'] ?? null) !== 'file') {
                continue;
            }

            $nama = $this->namaBidangForm($item, $indeks);
            $berkasLama = (array) ($existingJawaban['form'][$nama] ?? []);
            $berkasDipertahankan = $request->has("jawaban_file_pertahankan.{$nama}")
                ? array_values(array_intersect($berkasLama, (array) $request->input("jawaban_file_pertahankan.{$nama}", [])))
                : $berkasLama;

            foreach ($this->ambilBerkasForm($request, $nama, $jumlahBidangFile === 1) as $file) {
                $berkasDipertahankan[] = $file->store('rekrutmen/jawaban-form', 'public');
            }

            foreach (array_diff($berkasLama, $berkasDipertahankan) as $berkasDihapus) {
                Storage::disk('public')->delete($berkasDihapus);
            }

            $existingJawaban['form'][$nama] = array_values(array_unique($berkasDipertahankan));
        }

        // 4. Logika Penyimpanan untuk Tugas Tipe "Upload File / Project"
        $berkasLama = (array) ($existingJawaban['berkas'] ?? []);
        $berkasDipertahankan = $request->has('berkas_pertahankan')
            ? array_values(array_intersect($berkasLama, (array) $request->input('berkas_pertahankan', [])))
            : $berkasLama;

        if ($request->hasFile('file_jawaban')) {
            try {
                foreach ($request->file('file_jawaban') as $file) {
                    $berkasDipertahankan[] = $file->store('rekrutmen/tugas', 'public');
                }
            } catch (\Throwable $exception) {
                report($exception);

                return back()
                    ->withInput()
                    ->withErrors(['file_jawaban' => 'Berkas gagal diunggah. Periksa koneksi Anda lalu coba kembali.']);
            }

        }

        foreach (array_diff($berkasLama, $berkasDipertahankan) as $berkasDihapus) {
            Storage::disk('public')->delete($berkasDihapus);
        }
        if ($tugas->tipe_jawaban_tugas !== 'form' && $tugas->tipe_tugas !== 'pengisian_form') {
            $existingJawaban['berkas'] = array_values(array_unique($berkasDipertahankan));
        }

        // 5. Eksekusi Simpan ke Kolom lampiran_jawaban
        $pengumpulan->lampiran_jawaban = $existingJawaban;
        $pengumpulan->save();

        return back()
            ->with('success', 'Jawaban penugasan Anda berhasil diterima dan tersimpan dengan baik.')
            ->with('success_type', 'tugas');
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

        $tugas = $this->temukanTugasMilikPendaftaran($pendaftaran, (int) $tugas_id);
        $this->pastikanTugasSedangDibuka($tugas);

        $pengumpulan = PengumpulanTugas::firstOrNew([
            'pendaftaran_id' => $pendaftaran->id,
            'tugas_id' => $tugas->id,
        ]);

        // Catat kehadiran dalam kolom JSON yang digunakan seluruh fitur pengumpulan.
        $pengumpulan->lampiran_jawaban = [
            'status_wawancara' => 'Hadir',
            'waktu_konfirmasi' => now()->toDateTimeString(),
        ];

        $pengumpulan->save();

        return back()
            ->with('success', 'Konfirmasi kehadiran wawancara Anda berhasil dicatat.')
            ->with('success_type', 'wawancara');
    }

    private function temukanTugasMilikPendaftaran(Pendaftaran $pendaftaran, int $tugasId, bool $periksaHakPengerjaan = true): Tugas
    {
        $jabatan = $pendaftaran->pilihanJabatan1;

        $tugas = Tugas::with('tahapan')
            ->whereKey($tugasId)
            ->where('jabatan_id', $pendaftaran->jabatan_1_id)
            ->whereHas('tahapan', fn ($query) => $query->where(
                'periode_rekrutmen_id',
                $jabatan->periode_rekrutmen_id,
            ))
            ->firstOrFail();

        if ($periksaHakPengerjaan) {
            $this->pastikanPesertaMasihBerhakMengerjakan($pendaftaran, $tugas);
        }

        return $tugas;
    }

    private function pastikanPesertaMasihBerhakMengerjakan(Pendaftaran $pendaftaran, Tugas $tugas): void
    {
        $gagalTahapanIniAtauSebelumnya = KeputusanSeleksi::query()
            ->where('pendaftaran_id', $pendaftaran->id)
            ->where('jabatan_id', $pendaftaran->jabatan_1_id)
            ->where('keputusan', 'tidak_lolos')
            ->whereHas('tahapan', fn ($query) => $query
                ->where('periode_rekrutmen_id', $tugas->tahapan->periode_rekrutmen_id)
                ->where('jenis_tahapan', 'seleksi')
                ->where('urutan_tahapan', '<=', $tugas->tahapan->urutan_tahapan))
            ->exists();

        abort_unless(! $gagalTahapanIniAtauSebelumnya, 403, 'Anda tidak dinyatakan lulus pada tahapan seleksi sebelumnya.');
    }

    private function pastikanTugasSedangDibuka(Tugas $tugas): void
    {
        $mulai = Carbon::parse($tugas->tahapan->waktu_mulai);
        $berakhir = Carbon::parse($tugas->tahapan->waktu_berakhir);

        if (now()->lt($mulai) || now()->gte($berakhir)) {
            throw ValidationException::withMessages([
                'tugas' => 'Tugas hanya dapat dikirim selama periode pengumpulan berlangsung.',
            ]);
        }
    }

    private function validasiJawabanTugas(Request $request, Tugas $tugas, array $jawabanLama = []): void
    {
        $aturan = [];
        $pesan = [];
        $struktur = $this->strukturFormTugas($tugas);

        $jumlahBidangFile = collect($struktur)->where('tipe', 'file')->count();

        foreach ($struktur as $indeks => $item) {
            $nama = $this->namaBidangForm($item, $indeks);
            $aturanBidang = ! empty($item['required']) ? ['required'] : ['nullable'];
            $tipe = $item['tipe'] ?? 'text_short';

            if ($tipe === 'file') {
                $berkasLama = (array) ($jawabanLama['form'][$nama] ?? []);
                $berkasDipertahankan = $request->has("jawaban_file_pertahankan.{$nama}")
                    ? array_intersect($berkasLama, (array) $request->input("jawaban_file_pertahankan.{$nama}", []))
                    : $berkasLama;
                $berkasMasuk = $this->ambilBerkasForm($request, $nama, $jumlahBidangFile === 1);
                if (empty($berkasDipertahankan) && empty($berkasMasuk) && ! empty($item['required'])) {
                    throw ValidationException::withMessages([
                        'jawaban_file.'.$nama => 'Berkas '.($item['label'] ?? 'jawaban').' wajib diunggah.',
                    ]);
                }

                $aturanPerBerkas = ['file', 'max:5120'];
                $format = $this->formatBerkasForm($item['allowed_formats'] ?? []);
                if ($format !== []) {
                    $aturanPerBerkas[] = 'mimes:'.implode(',', $format);
                }
                foreach ($berkasMasuk as $berkas) {
                    Validator::make(['berkas' => $berkas], ['berkas' => $aturanPerBerkas], [
                        'berkas.mimes' => 'Format berkas '.($item['label'] ?? 'jawaban').' tidak sesuai.',
                        'berkas.max' => 'Ukuran setiap berkas '.($item['label'] ?? 'jawaban').' maksimal 5 MB.',
                    ])->validate();
                }

                continue;
            }

            if ($tipe === 'checkbox') {
                $aturanBidang[] = 'array';
            } elseif ($tipe === 'number') {
                $aturanBidang[] = 'numeric';
            } elseif ($tipe === 'email') {
                $aturanBidang[] = 'email';
            } elseif ($tipe === 'date') {
                $aturanBidang[] = 'date';
            } else {
                $aturanBidang[] = 'string';
            }

            $aturan['jawaban_form.'.$nama] = $aturanBidang;
            $pesan['jawaban_form.'.$nama.'.required'] = 'Isian '.($item['label'] ?? 'tugas').' wajib diisi.';
            $pesan['jawaban_form.'.$nama.'.numeric'] = 'Isian '.($item['label'] ?? 'tugas').' harus berupa angka.';
            $pesan['jawaban_form.'.$nama.'.email'] = 'Isian '.($item['label'] ?? 'tugas').' harus berupa email yang valid.';
            $pesan['jawaban_form.'.$nama.'.date'] = 'Isian '.($item['label'] ?? 'tugas').' harus berupa tanggal yang valid.';
        }

        if ($tugas->tipe_jawaban_tugas === 'form' || $tugas->tipe_tugas === 'pengisian_form') {
            if (collect($struktur)->contains(fn ($item) => ($item['tipe'] ?? 'text_short') !== 'file')) {
                $aturan['jawaban_form'] = ['required', 'array'];
            }
        } else {
            $berkasLama = (array) ($jawabanLama['berkas'] ?? []);
            $berkasDipertahankan = $request->has('berkas_pertahankan')
                ? array_intersect($berkasLama, (array) $request->input('berkas_pertahankan', []))
                : $berkasLama;
            $aturan['file_jawaban'] = [empty($berkasDipertahankan) ? 'required' : 'nullable', 'array'];
            $aturan['file_jawaban.*'] = ['file', 'max:5120'];
            $pesan['file_jawaban.required'] = 'Silakan pilih berkas jawaban sebelum mengirim tugas.';
            $pesan['file_jawaban.*.max'] = 'Ukuran setiap berkas jawaban maksimal 5 MB.';
        }

        $request->validate($aturan, $pesan);
    }

    private function strukturFormTugas(Tugas $tugas): array
    {
        $lampiran = is_array($tugas->lampiran_tugas)
            ? $tugas->lampiran_tugas
            : (json_decode($tugas->lampiran_tugas ?? '[]', true) ?: []);

        return (array) ($lampiran['form'] ?? []);
    }

    private function namaBidangForm(array $item, int $indeks): string
    {
        if (filled($item['id'] ?? null)) {
            return $item['id'];
        }

        if (filled($item['name'] ?? null)) {
            return $item['name'];
        }

        return 'isian_'.$indeks;
    }

    /**
     * Menyatukan jawaban dari skema lama ke nama bidang kanonis.
     * Data duplikat label/slug tidak dibawa lagi saat mahasiswa menyimpan revisi.
     */
    private function normalisasiJawabanForm(array $struktur, mixed $jawaban): array
    {
        $jawaban = is_array($jawaban) ? $jawaban : [];
        $hasil = [];
        $kunciLamaTerpakai = [];

        foreach ($struktur as $indeks => $item) {
            $namaKanonis = $this->namaBidangForm($item, $indeks);
            $label = $item['label'] ?? '';
            $slugLabel = str($label)->slug('_')->toString();
            $kandidat = array_unique(array_filter([
                'isian_'.$indeks,
                'field_'.$indeks,
                $namaKanonis,
                $label,
                $slugLabel,
            ], fn ($nilai) => filled($nilai)));

            foreach ($kandidat as $kunci) {
                if (
                    ! in_array($kunci, $kunciLamaTerpakai, true) &&
                    array_key_exists($kunci, $jawaban) &&
                    filled($jawaban[$kunci]) &&
                    $this->sesuaiJenisJawaban($item, $jawaban[$kunci])
                ) {
                    $hasil[$namaKanonis] = $jawaban[$kunci];
                    $kunciLamaTerpakai[] = $kunci;
                    break;
                }
            }
        }

        return $hasil;
    }

    private function sesuaiJenisJawaban(array $item, mixed $nilai): bool
    {
        $tipe = $item['tipe'] ?? 'text_short';

        return match ($tipe) {
            'checkbox', 'file' => is_array($nilai),
            default => ! is_array($nilai),
        };
    }

    private function formatBerkasForm(array $format): array
    {
        return collect($format)->flatMap(fn ($ekstensi) => match (strtolower($ekstensi)) {
            'word' => ['doc', 'docx'],
            'excel' => ['xls', 'xlsx'],
            default => [strtolower($ekstensi)],
        })->filter()->unique()->values()->all();
    }

    /** Mengambil unggahan form dinamis, termasuk payload dari skema lama tanpa nama bidang. */
    private function ambilBerkasForm(Request $request, string $namaBidang, bool $gunakanFallback): array
    {
        $berkas = $request->file('jawaban_file.'.$namaBidang);

        if (empty($berkas) && $gunakanFallback) {
            $berkas = $request->allFiles()['jawaban_file'] ?? [];
        }

        return collect((array) $berkas)
            ->flatten()
            ->filter(fn ($file) => $file instanceof UploadedFile && $file->isValid())
            ->values()
            ->all();
    }
}
