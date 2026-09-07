<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Jabatan;
use App\Models\Pendaftaran;
use App\Models\PengumpulanTugas;
use App\Models\PeriodeRekrutmen;
use App\Models\Tahapan;
use App\Models\Tugas;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class DaftarRekrutmenController extends Controller
{
    public function index()
    {
        // Ambil semua rekrutmen yang sedang aktif (buka)
        $rekrutmenAktif = PeriodeRekrutmen::with('organisasi')
            ->where('status_aktif', 2)
            ->orderBy('created_at', 'desc')
            ->get();

        $tahapanPendaftaranPerPeriode = Tahapan::query()
            ->whereIn('periode_rekrutmen_id', $rekrutmenAktif->pluck('id'))
            ->seleksi()
            ->orderBy('urutan_tahapan')
            ->get()
            ->groupBy('periode_rekrutmen_id')
            ->map(fn ($tahapans) => $tahapans->first());

        $rekrutmenAktif->each(function (PeriodeRekrutmen $rekrutmen) use ($tahapanPendaftaranPerPeriode) {
            $tahapanPendaftaran = $tahapanPendaftaranPerPeriode->get($rekrutmen->id);

            $rekrutmen->tahapan_pendaftaran = $tahapanPendaftaran;
            $rekrutmen->pendaftaran_terbuka = $tahapanPendaftaran
                && now()->gte($tahapanPendaftaran->waktu_mulai)
                && now()->lt($tahapanPendaftaran->waktu_berakhir);
            $rekrutmen->pendaftaran_sudah_berakhir = $tahapanPendaftaran
                && now()->gte($tahapanPendaftaran->waktu_berakhir);
        });

        $jabatanIdsTerdaftar = Pendaftaran::query()
            ->where('nim', Auth::user()->nim)
            ->get(['jabatan_1_id', 'jabatan_2_id'])
            ->flatMap(fn (Pendaftaran $pendaftaran) => [$pendaftaran->jabatan_1_id, $pendaftaran->jabatan_2_id])
            ->filter()
            ->unique()
            ->values();

        $periodeTerdaftar = Jabatan::whereIn('id', $jabatanIdsTerdaftar)
            ->pluck('periode_rekrutmen_id')
            ->unique()
            ->flip();

        return view('mahasiswa.rekrutmen.index', compact('rekrutmenAktif', 'periodeTerdaftar'));
    }

    public function info($periode_id)
    {
        $rekrutmen = PeriodeRekrutmen::with('organisasi')->findOrFail($periode_id);
        $jabatans = Jabatan::where('periode_rekrutmen_id', $periode_id)->get();
        $tahapans = Tahapan::where('periode_rekrutmen_id', $periode_id)
            ->get()
            ->sortBy('waktu_mulai');
        $tahapanPendaftaran = $tahapans
            ->where('jenis_tahapan', 'seleksi')
            ->sortBy('urutan_tahapan')
            ->first();
        $pendaftaranTerbuka = (int) $rekrutmen->status_aktif === 2
            && $tahapanPendaftaran
            && now()->gte($tahapanPendaftaran->waktu_mulai)
            && now()->lt($tahapanPendaftaran->waktu_berakhir);
        $pendaftaranSudahBerakhir = $tahapanPendaftaran
            && now()->gte($tahapanPendaftaran->waktu_berakhir);
        $sudahTerdaftar = $this->sudahTerdaftarPadaPeriode((int) $periode_id, Auth::user()->nim);

        return view('mahasiswa.rekrutmen.informasi', compact(
            'rekrutmen',
            'jabatans',
            'tahapans',
            'pendaftaranTerbuka',
            'pendaftaranSudahBerakhir',
            'sudahTerdaftar',
        ));
    }

    public function kerjakanTahapanSatu($periode_id)
    {
        $rekrutmen = PeriodeRekrutmen::with('organisasi')->findOrFail($periode_id);

        if ((int) $rekrutmen->status_aktif !== 2) {
            return redirect()->route('mahasiswa.rekrutmen.index')
                ->with('error_server', 'Pendaftaran untuk rekrutmen ini sudah tidak tersedia.');
        }

        if ($this->sudahTerdaftarPadaPeriode((int) $periode_id, Auth::user()->nim)) {
            return redirect()->route('mahasiswa.rekrutmen.index')
                ->with('error_server', 'Anda telah mendaftarkan diri pada rekrutmen ini.');
        }
        $tahapanSatu = Tahapan::where('periode_rekrutmen_id', $periode_id)
            ->seleksi()
            ->orderBy('urutan_tahapan')
            ->first();

        if (! $tahapanSatu) {
            return redirect()->route('mahasiswa.rekrutmen.info', $periode_id)
                ->with('error', 'Pendaftaran belum bisa dilakukan karena panitia belum mengatur jadwal tahapan seleksi.');
        }

        if (now()->lt($tahapanSatu->waktu_mulai)) {
            return redirect()->route('mahasiswa.rekrutmen.info', $periode_id)
                ->with('error', 'Pendaftaran belum dapat dilakukan karena tahapan pertama belum dimulai.');
        }

        if (now()->gte($tahapanSatu->waktu_berakhir)) {
            return redirect()->route('mahasiswa.rekrutmen.index')
                ->with('error_server', 'Pendaftaran telah ditutup karena batas waktu tahapan pertama sudah berakhir.');
        }

        $jabatans = Jabatan::where('periode_rekrutmen_id', $periode_id)->get();

        // --- LOGIKA GROUPING DIPINDAHKAN KE SINI ---
        $groupedJabatan = [];
        if ($jabatans->count() > 0) {
            foreach ($jabatans as $jabatan) {
                // Jika nama_posisi kosong atau berisi '-', set menjadi 'Tanpa Divisi Khusus'
                $namaPosisi = (empty($jabatan->nama_posisi) || $jabatan->nama_posisi === '-')
                    ? 'Tanpa Divisi Khusus'
                    : $jabatan->nama_posisi;

                if (! isset($groupedJabatan[$namaPosisi])) {
                    $groupedJabatan[$namaPosisi] = [];
                }

                // Masukkan data jabatan ke dalam grup posisi yang bersangkutan
                $groupedJabatan[$namaPosisi][] = [
                    'id' => $jabatan->id,
                    'nama_jabatan' => $jabatan->nama_jabatan,
                ];
            }
        }
        // -------------------------------------------

        // Lempar $groupedJabatan (bukan hanya $jabatans mentah) ke tampilan
        return view('mahasiswa.rekrutmen.pendaftaran.index', compact('rekrutmen', 'tahapanSatu', 'jabatans', 'groupedJabatan'));
    }

    public function submitPendaftaran(Request $request, $periode_id)
    {
        $rekrutmen = PeriodeRekrutmen::findOrFail($periode_id);
        $tahapanSatu = Tahapan::query()
            ->where('periode_rekrutmen_id', $periode_id)
            ->seleksi()
            ->orderBy('urutan_tahapan')
            ->first();

        if ((int) $rekrutmen->status_aktif !== 2 || ! $tahapanSatu) {
            return redirect()->route('mahasiswa.rekrutmen.index')
                ->with('error_server', 'Pendaftaran untuk rekrutmen ini sudah tidak tersedia.');
        }

        if (now()->lt($tahapanSatu->waktu_mulai)) {
            return redirect()->route('mahasiswa.rekrutmen.info', $periode_id)
                ->with('error', 'Pendaftaran belum dapat dilakukan karena tahapan pertama belum dimulai.');
        }

        if (now()->gte($tahapanSatu->waktu_berakhir)) {
            return redirect()->route('mahasiswa.rekrutmen.index')
                ->with('error_server', 'Pendaftaran telah ditutup karena batas waktu tahapan pertama sudah berakhir.');
        }

        // 1. Aturan Validasi Dasar Struktural
        $rules = [
            'jabatan_1_id' => 'required|integer',
            'jabatan_2_id' => 'nullable|integer|different:jabatan_1_id',
            'dynamic_answers' => 'nullable|array',
            'file_berkas' => ['nullable', 'file', 'max:5120'],
        ];

        $customMessages = [
            'jabatan_1_id.required' => 'Formasi prioritas utama (Pilihan 1) wajib ditentukan.',
            'jabatan_2_id.different' => 'Formasi Pilihan 2 tidak boleh identik dengan Pilihan 1.',
            'file_berkas.max' => 'Berkas lampiran melebihi kapasitas batas maksimal 5 MB.',
        ];

        // 2. Pastikan formasi yang dipilih benar-benar berada pada periode ini.
        $request->validate($rules, $customMessages);

        $jabatanUtama = Jabatan::query()
            ->where('periode_rekrutmen_id', $periode_id)
            ->find($request->integer('jabatan_1_id'));
        $jabatanCadangan = $request->filled('jabatan_2_id')
            ? Jabatan::query()
                ->where('periode_rekrutmen_id', $periode_id)
                ->find($request->integer('jabatan_2_id'))
            : null;

        if (! $jabatanUtama || ($request->filled('jabatan_2_id') && ! $jabatanCadangan)) {
            throw ValidationException::withMessages([
                'jabatan_1_id' => 'Formasi yang dipilih tidak tersedia pada periode rekrutmen ini.',
            ]);
        }

        // 3. Baca dan validasi form dinamis milik formasi pilihan utama.
        $tugas = Tugas::query()
            ->where('tahapan_id', $tahapanSatu->id)
            ->where('jabatan_id', $jabatanUtama->id)
            ->first();
        $skemaForm = $this->strukturFormPendaftaran($tugas);

        foreach ($skemaForm as $indeks => $field) {
            $namaBidang = $this->namaBidangPendaftaran($field, $indeks);
            $tipeInput = $field['tipe'] ?? 'text_short';

            if ($tipeInput === 'file') {
                continue;
            }

            $fieldRules = ! empty($field['required']) ? ['required'] : ['nullable'];
            $fieldRules[] = match ($tipeInput) {
                'number' => 'numeric',
                'date' => 'date',
                'email' => 'email',
                'checkbox' => 'array',
                default => 'string',
            };
            $rules["dynamic_answers.{$namaBidang}"] = $fieldRules;
            $customMessages["dynamic_answers.{$namaBidang}.required"] = "Kolom isian '{$field['label']}' wajib Anda lengkapi.";
        }

        $perluBerkasUtama = $tugas
            && ($skemaForm === [] || $tugas->tipe_tugas === 'unggah_berkas');
        $rules['file_berkas'][0] = $perluBerkasUtama ? 'required' : 'nullable';

        // 4. Jalankan validasi teks/form lalu validasi unggahan tiap pertanyaan file.
        $request->validate($rules, $customMessages);

        $berkasFormMasuk = [];
        foreach ($skemaForm as $indeks => $field) {
            if (($field['tipe'] ?? null) !== 'file') {
                continue;
            }

            $namaBidang = $this->namaBidangPendaftaran($field, $indeks);
            $berkas = $this->ambilBerkasPendaftaran($request, $namaBidang);

            if ($berkas === [] && ! empty($field['required'])) {
                throw ValidationException::withMessages([
                    "dynamic_files.{$namaBidang}" => "Berkas {$field['label']} wajib diunggah.",
                ]);
            }

            $this->validasiBerkasPendaftaran($berkas, $field);
            $berkasFormMasuk[$namaBidang] = $berkas;
        }

        $nimMahasiswa = Auth::user()->nim;

        // 5. Barikade Pendaftaran Ganda
        // Cek apakah nim ini sudah mendaftar di jabatan yang masuk dalam periode ini.
        if ($this->sudahTerdaftarPadaPeriode((int) $periode_id, $nimMahasiswa)) {
            return back()->with('error_server', 'Pendaftaran gagal. Anda sudah terdaftar di sistem rekrutmen organisasi ini.');
        }

        $berkasTersimpan = [];
        DB::beginTransaction();
        try {
            // 6. Simpan Data ke Tabel `pendaftaran`
            $pendaftaran = Pendaftaran::create([
                'nim' => $nimMahasiswa,
                'jabatan_1_id' => $jabatanUtama->id,
                'jabatan_2_id' => $jabatanCadangan?->id,
            ]);

            // 7. Proses unggahan berkas umum dan berkas dari setiap pertanyaan form.
            $pathBerkas = null;
            if ($request->hasFile('file_berkas')) {
                $pathBerkas = $request->file('file_berkas')->store('rekrutmen/pendaftar/berkas', 'public');
                $berkasTersimpan[] = $pathBerkas;
            }

            $jawabanForm = (array) $request->input('dynamic_answers', []);
            foreach ($berkasFormMasuk as $namaBidang => $berkas) {
                $jawabanForm[$namaBidang] = [];
                foreach ($berkas as $file) {
                    $path = $file->store('rekrutmen/jawaban-form', 'public');
                    $jawabanForm[$namaBidang][] = $path;
                    $berkasTersimpan[] = $path;
                }
            }

            // 8. Kemas jawaban dengan format yang sama seperti pengerjaan seleksi.
            $lampiranJawaban = [];
            if ($jawabanForm !== []) {
                $lampiranJawaban['form'] = $jawabanForm;
            }
            if ($pathBerkas) {
                $lampiranJawaban['berkas'] = [$pathBerkas];
            }

            // 9. Simpan jawaban pendaftaran pada tugas tahap pertama.
            if ($tugas && ! empty($lampiranJawaban)) {
                PengumpulanTugas::create([
                    'tugas_id' => $tugas->id,
                    'pendaftaran_id' => $pendaftaran->id,
                    'lampiran_jawaban' => $lampiranJawaban,
                ]);
            }

            DB::commit();

            return redirect()->route('mahasiswa.rekrutmen.index')
                ->with('success', 'Selamat! Pendaftaran berkas dan pengisian formulir Anda berhasil dikirim ke server.');

        } catch (\Throwable $e) {
            DB::rollBack();
            foreach ($berkasTersimpan as $berkas) {
                Storage::disk('public')->delete($berkas);
            }

            report($e);

            return back()->withInput()->with('error_server', 'Terjadi kendala internal saat menyimpan pendaftaran. Silakan coba lagi.');
        }
    }

    // MENU 2: REKRUTMEN YANG SEDANG DIIKUTI
    public function diikuti()
    {
        // Menampilkan rekrutmen yang sudah didaftar
    }

    public function detailDiikuti($periode_id)
    {
        // Menampilkan timeline lanjutan dan tugas tahapan berikutnya
    }

    private function sudahTerdaftarPadaPeriode(int $periodeId, string $nim): bool
    {
        $jabatanIds = Jabatan::where('periode_rekrutmen_id', $periodeId)->pluck('id');

        return Pendaftaran::where('nim', $nim)
            ->where(function ($query) use ($jabatanIds) {
                $query->whereIn('jabatan_1_id', $jabatanIds)
                    ->orWhereIn('jabatan_2_id', $jabatanIds);
            })
            ->exists();
    }

    private function strukturFormPendaftaran(?Tugas $tugas): array
    {
        if (! $tugas) {
            return [];
        }

        $lampiran = is_array($tugas->lampiran_tugas)
            ? $tugas->lampiran_tugas
            : (json_decode($tugas->lampiran_tugas ?? '[]', true) ?: []);

        return (array) ($lampiran['form'] ?? []);
    }

    private function namaBidangPendaftaran(array $field, int $indeks): string
    {
        return 'isian_'.$indeks;
    }

    /** @return array<int, UploadedFile> */
    private function ambilBerkasPendaftaran(Request $request, string $namaBidang): array
    {
        return collect((array) (($request->allFiles()['dynamic_files'][$namaBidang] ?? [])))
            ->flatten()
            ->filter(fn ($file) => $file instanceof UploadedFile && $file->isValid())
            ->values()
            ->all();
    }

    private function validasiBerkasPendaftaran(array $berkas, array $field): void
    {
        $aturan = ['file', 'max:5120'];
        $format = collect($field['allowed_formats'] ?? [])
            ->flatMap(fn ($ekstensi) => match (strtolower($ekstensi)) {
                'word' => ['doc', 'docx'],
                'excel' => ['xls', 'xlsx'],
                default => [strtolower($ekstensi)],
            })
            ->filter()
            ->unique()
            ->values()
            ->all();

        if ($format !== []) {
            $aturan[] = 'mimes:'.implode(',', $format);
        }

        foreach ($berkas as $file) {
            Validator::make(['berkas' => $file], ['berkas' => $aturan], [
                'berkas.mimes' => 'Format berkas '.($field['label'] ?? 'jawaban').' tidak sesuai.',
                'berkas.max' => 'Ukuran setiap berkas '.($field['label'] ?? 'jawaban').' maksimal 5 MB.',
            ])->validate();
        }
    }
}
