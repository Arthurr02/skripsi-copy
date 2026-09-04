<?php

namespace App\Http\Controllers\Rekrutmen;

use App\Http\Controllers\Controller;
use App\Models\Jabatan;
use App\Models\KeputusanSeleksi;
use App\Models\Panitia;
use App\Models\Pendaftaran;
use App\Models\PengumpulanTugas;
use App\Models\PeriodeRekrutmen;
use App\Models\Tahapan;
use App\Models\Tugas;
use App\Services\Recruitment\SimpleXlsxExport;
use App\Services\Recruitment\TahapanPesertaCounter;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class PengerjaanSeleksiController extends Controller
{
    public function index()
    {
        ['organisasiId' => $organisasiId, 'routePrefix' => $routePrefix] = $this->konteksAkses();

        $periodeAktif = PeriodeRekrutmen::where('organisasi_id', $organisasiId)
            ->whereIn('status_aktif', [1, 2])
            ->latest()
            ->first();

        $tahapans = collect();
        $listJabatan = collect();
        if ($periodeAktif) {
            $now = Carbon::now();

            $listJabatan = Jabatan::query()
                ->where('periode_rekrutmen_id', $periodeAktif->id)
                ->withCount([
                    'pendaftaranPilihanPertama as pendaftaran1_count',
                    'pendaftaranPilihanKedua as pendaftaran2_count',
                ])
                ->orderBy('nama_posisi')
                ->orderBy('nama_jabatan')
                ->get();

            $tahapans = Tahapan::query()
                ->where('periode_rekrutmen_id', $periodeAktif->id)
                ->withCount('tugas')
                ->orderBy('urutan_tahapan')
                ->orderBy('waktu_mulai')
                ->get()
                ->map(function (Tahapan $tahapan) use ($now) {
                    $tahapan->parsed_mulai = Carbon::parse($tahapan->waktu_mulai);
                    $tahapan->parsed_berakhir = Carbon::parse($tahapan->waktu_berakhir);
                    $tahapan->is_past = $now->gt($tahapan->parsed_berakhir);
                    $tahapan->is_active = $now->between(
                        $tahapan->parsed_mulai,
                        $tahapan->parsed_berakhir,
                    );
                    $tahapan->is_future = $now->lt($tahapan->parsed_mulai);
                    $tahapan->is_waktu_tunggal = $tahapan->parsed_mulai->equalTo(
                        $tahapan->parsed_berakhir,
                    );

                    $lampiran = is_array($tahapan->lampiran_tahapan)
                        ? $tahapan->lampiran_tahapan
                        : (json_decode($tahapan->lampiran_tahapan ?? '[]', true) ?: []);
                    $tahapan->pedoman_path = $lampiran[0] ?? null;

                    return $tahapan;
                });

            $tahapans = app(TahapanPesertaCounter::class)
                ->tambahkanJumlahPeserta($tahapans, $periodeAktif->id);

            $pesertaPerTahapanJabatan = $tahapans
                ->mapWithKeys(fn (Tahapan $tahapan) => [$tahapan->id => $tahapan->peserta_per_jabatan]);
        }

        return view('rekrutmen.seleksi.daftar-tahapan', [
            'periodeAktif' => $periodeAktif,
            'tahapans' => $tahapans,
            'listJabatan' => $listJabatan,
            'routePrefix' => $routePrefix,
            'pesertaPerTahapanJabatan' => $pesertaPerTahapanJabatan ?? [],
        ]);
    }

    public function tahapanJabatan(int $jabatanId)
    {
        ['organisasiId' => $organisasiId, 'routePrefix' => $routePrefix] = $this->konteksAkses();

        $jabatan = Jabatan::with('periode')->findOrFail($jabatanId);
        $periodeAktif = $jabatan->periode;

        if (! $periodeAktif || $periodeAktif->organisasi_id !== $organisasiId) {
            abort(403, 'Akses ditolak.');
        }

        $now = Carbon::now();

        $pendaftarans = $this->pendaftaransUntukJabatan($jabatan);

        $totalPelamar = $pendaftarans->count();
        $pendaftaranIds = $pendaftarans->pluck('id');

        $tahapans = Tahapan::with([
            'tugas' => fn ($query) => $query->where('jabatan_id', $jabatanId)->with([
                'pengumpulanTugas' => fn ($query) => $query->whereIn('pendaftaran_id', $pendaftaranIds),
            ]),
        ])
            ->where('periode_rekrutmen_id', $periodeAktif->id)
            ->orderBy('urutan_tahapan', 'asc')
            ->get()
            ->map(function ($tahapan) use ($now, $jabatan) {
                $tahapan->parsed_mulai = Carbon::parse($tahapan->waktu_mulai);
                $tahapan->parsed_berakhir = Carbon::parse($tahapan->waktu_berakhir);

                $tahapan->is_past = $now->gt($tahapan->parsed_berakhir);
                $tahapan->is_future = $now->lt($tahapan->parsed_mulai);
                $tahapan->is_active = $now->between($tahapan->parsed_mulai, $tahapan->parsed_berakhir);
                $tahapan->is_waktu_tunggal = $tahapan->parsed_mulai->equalTo($tahapan->parsed_berakhir);

                $pesertaTahapan = $this->pendaftaransUntukJabatan($jabatan, $tahapan);

                $tahapan->tugas->each(function ($tugas) use ($pesertaTahapan) {
                    $pesertaJawaban = [];
                    $jumlahPengumpul = 0;

                    foreach ($pesertaTahapan as $pendaftaran) {
                        $pengumpulan = $tugas->pengumpulanTugas->where('pendaftaran_id', $pendaftaran->id)->first();
                        $sudahKumpul = $pengumpulan !== null;

                        if ($sudahKumpul) {
                            $jumlahPengumpul++;
                        }

                        $pesertaJawaban[] = [
                            'id' => $pendaftaran->id,
                            'nama' => $pendaftaran->mahasiswa->nama_lengkap ?? 'Peserta Anonim',
                            'sudah_kumpul' => $sudahKumpul,
                        ];
                    }

                    $tugas->jumlah_pengumpul = $jumlahPengumpul;
                    $tugas->peserta_jawaban = $pesertaJawaban;
                });

                return $tahapan;
            });

        return view('rekrutmen.seleksi.index', [
            'jabatan' => $jabatan,
            'namaRekrutmen' => $periodeAktif->nama_rekrutmen,
            'namaJabatan' => $jabatan->nama_jabatan,
            'totalPelamar' => $totalPelamar,
            'tahapans' => $tahapans,
            'routePrefix' => $routePrefix,
        ]);
    }

    /** Menampilkan jawaban seluruh peserta untuk satu tahapan dan satu jabatan. */
    public function jawabanTahapanJabatan(int $tahapanId, int $jabatanId)
    {
        ['tahapan' => $tahapan, 'jabatan' => $jabatan, 'routePrefix' => $routePrefix] = $this->konteksTahapanJabatan(
            $tahapanId,
            $jabatanId,
        );

        return $this->tampilkanJawabanTahapan($tahapan, $jabatan, $routePrefix);
    }

    /** Menampilkan hasil tahap dari periode tertutup dalam mode baca saja. */
    public function riwayatTahapanJabatan(int $periode_id, int $jabatan_id, int $tahapan_id)
    {
        ['tahapan' => $tahapan, 'jabatan' => $jabatan, 'routePrefix' => $routePrefix] = $this->konteksTahapanJabatan(
            $tahapan_id,
            $jabatan_id,
        );

        abort_unless(
            $tahapan->periode_rekrutmen_id === $periode_id
                && $jabatan->periode_rekrutmen_id === $periode_id
                && (int) $jabatan->periode?->status_aktif === 0,
            Response::HTTP_NOT_FOUND,
            'Riwayat tahapan tidak ditemukan.',
        );

        return $this->tampilkanJawabanTahapan($tahapan, $jabatan, $routePrefix, true);
    }

    private function tampilkanJawabanTahapan(Tahapan $tahapan, Jabatan $jabatan, string $routePrefix, bool $readOnly = false)
    {
        $pendaftarans = $this->pendaftaransUntukJabatan($jabatan, $tahapan);
        $keputusanPeserta = $this->keputusanTahapanPeserta($tahapan, $jabatan, $pendaftarans);
        $urutanWaktu = request('urutan_waktu') === 'asc' ? 'asc' : 'desc';

        $tugasDenganJawaban = $tahapan->tugas()
            ->where('jabatan_id', $jabatan->id)
            ->orderBy('id')
            ->get()
            ->map(function (Tugas $tugas) use ($pendaftarans, $keputusanPeserta, $urutanWaktu) {
                $tugas->jawaban_peserta = $this->jawabanPesertaUntukTugas(
                    $tugas,
                    $pendaftarans,
                    $keputusanPeserta,
                    $urutanWaktu,
                );
                $tugas->memakai_form = $this->memakaiForm($tugas);

                return $tugas;
            });

        return view('rekrutmen.seleksi.jawaban-peserta', compact(
            'tahapan',
            'jabatan',
            'tugasDenganJawaban',
            'routePrefix',
            'readOnly',
            'urutanWaktu',
        ));
    }

    /** Menyimpan keputusan seleksi peserta dari tabel tahapan. */
    public function simpanKeputusan(Request $request, int $tahapanId, int $jabatanId, int $pendaftaranId)
    {
        ['tahapan' => $tahapan, 'jabatan' => $jabatan] = $this->konteksTahapanJabatan($tahapanId, $jabatanId);
        $pendaftaran = $this->pendaftaranUntukJabatan($pendaftaranId, $jabatan, $tahapan);
        $data = $request->validate([
            'keputusan' => ['required', Rule::in(['lulus', 'tidak_lolos'])],
        ]);

        $aktor = $this->aktorSaatIni($jabatan);
        KeputusanSeleksi::updateOrCreate(
            [
                'tahapan_id' => $tahapan->id,
                'jabatan_id' => $jabatan->id,
                'pendaftaran_id' => $pendaftaran->id,
            ],
            [
                'keputusan' => $data['keputusan'],
                'aktor_tipe' => $aktor['tipe'],
                'aktor_akun_id' => $aktor['akun_id'],
                'aktor_nama' => $aktor['nama'],
                'diputuskan_pada' => now(),
            ],
        );

        $pendaftaran->status_seleksi = $data['keputusan'] === 'lulus'
            ? 'Lulus Tahap '.$tahapan->urutan_tahapan
            : 'Tidak Lolos';
        $pendaftaran->save();

        return back()
            ->with('success', 'Keputusan seleksi untuk '.$pendaftaran->mahasiswa?->nama_lengkap.' berhasil disimpan.')
            ->with('success_title', 'Keputusan seleksi disimpan');
    }

    /** Mengunduh jawaban form atau wawancara dalam format XLSX. */
    public function exportJawabanExcel(int $tahapanId, int $jabatanId, int $tugasId, SimpleXlsxExport $xlsxExport)
    {
        ['tahapan' => $tahapan, 'jabatan' => $jabatan] = $this->konteksTahapanJabatan($tahapanId, $jabatanId);
        $tugas = $this->tugasUntukTahapanJabatan($tugasId, $tahapan, $jabatan);

        return $this->unduhJawabanExcel($tahapan, $jabatan, $tugas, $xlsxExport);
    }

    /** Mengunduh jawaban form atau wawancara dari rekrutmen yang telah ditutup. */
    public function exportJawabanExcelRiwayat(
        int $periode_id,
        int $jabatan_id,
        int $tahapan_id,
        int $tugasId,
        SimpleXlsxExport $xlsxExport,
    ) {
        ['tahapan' => $tahapan, 'jabatan' => $jabatan] = $this->konteksTahapanJabatan($tahapan_id, $jabatan_id);
        abort_unless(
            $tahapan->periode_rekrutmen_id === $periode_id
                && $jabatan->periode_rekrutmen_id === $periode_id
                && (int) $jabatan->periode?->status_aktif === 0,
            Response::HTTP_NOT_FOUND,
            'Riwayat tugas tidak ditemukan.',
        );

        $tugas = $this->tugasUntukTahapanJabatan($tugasId, $tahapan, $jabatan);

        return $this->unduhJawabanExcel($tahapan, $jabatan, $tugas, $xlsxExport);
    }

    private function unduhJawabanExcel(Tahapan $tahapan, Jabatan $jabatan, Tugas $tugas, SimpleXlsxExport $xlsxExport)
    {
        abort_unless($this->memakaiForm($tugas), Response::HTTP_NOT_FOUND, 'Ekspor Excel hanya tersedia untuk penugasan form atau wawancara.');

        $pertanyaan = $this->pertanyaanTugas($tugas);
        $pendaftarans = $this->pendaftaransUntukJabatan($jabatan, $tahapan);
        $peserta = $this->jawabanPesertaUntukTugas(
            $tugas,
            $pendaftarans,
            $this->keputusanTahapanPeserta($tahapan, $jabatan, $pendaftarans),
        );
        $headers = array_merge(
            ['NIM', 'Nama', 'Waktu Pengumpulan', 'Status Pengumpulan', 'Keputusan Seleksi', 'Diputuskan Oleh'],
            array_column($pertanyaan, 'label'),
        );

        $rows = $peserta->map(function (array $peserta) use ($pertanyaan) {
            $jawabanPerKunci = collect($peserta['jawaban']['isi'] ?? [])->keyBy('key');
            $jawabanForm = array_map(function (array $kolom) use ($jawabanPerKunci) {
                return $this->nilaiUntukEkspor($jawabanPerKunci->get($kolom['key'])['nilai'] ?? null);
            }, $pertanyaan);

            return array_merge([
                $peserta['nim'],
                $peserta['nama'],
                $peserta['dikumpulkan_pada'],
                $peserta['jawaban']['status'],
                $peserta['status_seleksi'],
                $peserta['keputusan_oleh'] ?? '',
            ], $jawabanForm);
        })->all();

        $xlsx = $xlsxExport->buat($headers, $rows, [2]);
        $fileName = 'hasil-'.str($tahapan->nama_tahapan.'-'.$jabatan->nama_jabatan)->slug('-').'.xlsx';

        return response($xlsx, Response::HTTP_OK, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="'.$fileName.'"',
            'Content-Length' => (string) strlen($xlsx),
            'Cache-Control' => 'no-store, private',
        ]);
    }

    /** Menampilkan formulir catatan pewawancara untuk satu peserta. */
    public function formWawancara(int $tahapanId, int $jabatanId, int $tugasId, int $pendaftaranId)
    {
        ['tahapan' => $tahapan, 'jabatan' => $jabatan, 'routePrefix' => $routePrefix] = $this->konteksTahapanJabatan(
            $tahapanId,
            $jabatanId,
        );
        $tugas = $this->tugasUntukTahapanJabatan($tugasId, $tahapan, $jabatan);
        abort_unless($tugas->tipe_tugas === 'wawancara', Response::HTTP_NOT_FOUND, 'Tugas ini bukan sesi wawancara.');

        $pendaftaran = $this->pendaftaranUntukJabatan($pendaftaranId, $jabatan, $tahapan);
        $pengumpulan = PengumpulanTugas::query()
            ->where('tugas_id', $tugas->id)
            ->where('pendaftaran_id', $pendaftaran->id)
            ->latest('updated_at')
            ->first();
        $dataPengumpulan = (array) ($pengumpulan?->lampiran_jawaban ?? []);
        $jawabanSebelumnya = (array) ($dataPengumpulan['jawaban_wawancara']['form'] ?? []);
        $pertanyaan = $this->pertanyaanTugas($tugas);

        return view('rekrutmen.seleksi.form-wawancara', compact(
            'tahapan',
            'jabatan',
            'tugas',
            'pendaftaran',
            'pertanyaan',
            'jawabanSebelumnya',
            'routePrefix',
        ));
    }

    /** Menyimpan jawaban pewawancara tanpa menimpa konfirmasi kehadiran peserta. */
    public function simpanWawancara(Request $request, int $tahapanId, int $jabatanId, int $tugasId, int $pendaftaranId)
    {
        ['tahapan' => $tahapan, 'jabatan' => $jabatan, 'routePrefix' => $routePrefix] = $this->konteksTahapanJabatan(
            $tahapanId,
            $jabatanId,
        );
        $tugas = $this->tugasUntukTahapanJabatan($tugasId, $tahapan, $jabatan);
        abort_unless($tugas->tipe_tugas === 'wawancara', Response::HTTP_NOT_FOUND, 'Tugas ini bukan sesi wawancara.');

        $pendaftaran = $this->pendaftaranUntukJabatan($pendaftaranId, $jabatan, $tahapan);
        $pertanyaan = $this->pertanyaanTugas($tugas);
        $jawaban = $this->validasiJawabanWawancara($request, $pertanyaan);
        $pengumpulan = PengumpulanTugas::query()
            ->where('tugas_id', $tugas->id)
            ->where('pendaftaran_id', $pendaftaran->id)
            ->latest('updated_at')
            ->first() ?? new PengumpulanTugas([
                'tugas_id' => $tugas->id,
                'pendaftaran_id' => $pendaftaran->id,
            ]);
        $dataPengumpulan = (array) ($pengumpulan->lampiran_jawaban ?? []);
        $dataPengumpulan['jawaban_wawancara'] = [
            'form' => $jawaban,
            'dikerjakan_pada' => now()->toDateTimeString(),
        ];
        $aktor = $this->aktorSaatIni($jabatan);
        $pengumpulan->lampiran_jawaban = $dataPengumpulan;
        $pengumpulan->pewawancara_id = $aktor['tipe'] === 'Panitia' ? $aktor['akun_id'] : null;
        $pengumpulan->pewawancara_tipe = $aktor['tipe'];
        $pengumpulan->pewawancara_akun_id = $aktor['akun_id'];
        $pengumpulan->pewawancara_nama = $aktor['nama'];
        $pengumpulan->save();

        return redirect()->route($routePrefix.'rekrutmen.seleksi.jawaban', [
            'tahapanId' => $tahapan->id,
            'jabatanId' => $jabatan->id,
        ])
            ->with('success', 'Catatan wawancara peserta berhasil disimpan.')
            ->with('success_title', 'Hasil wawancara disimpan');
    }

    /**
     * @return array{organisasiId: int, routePrefix: string, tahapan: Tahapan, jabatan: Jabatan}
     */
    private function konteksTahapanJabatan(int $tahapanId, int $jabatanId): array
    {
        ['organisasiId' => $organisasiId, 'routePrefix' => $routePrefix] = $this->konteksAkses();

        $jabatan = Jabatan::with('periode')->findOrFail($jabatanId);
        $tahapan = Tahapan::findOrFail($tahapanId);

        abort_unless(
            $jabatan->periode &&
            $jabatan->periode->organisasi_id === $organisasiId &&
            $tahapan->periode_rekrutmen_id === $jabatan->periode_rekrutmen_id,
            Response::HTTP_FORBIDDEN,
            'Tahapan atau jabatan tidak berada pada rekrutmen Anda.'
        );

        return compact('organisasiId', 'routePrefix', 'tahapan', 'jabatan');
    }

    private function tugasUntukTahapanJabatan(int $tugasId, Tahapan $tahapan, Jabatan $jabatan): Tugas
    {
        return Tugas::query()
            ->whereKey($tugasId)
            ->where('tahapan_id', $tahapan->id)
            ->where('jabatan_id', $jabatan->id)
            ->firstOrFail();
    }

    /** @return Collection<int, Pendaftaran> */
    private function pendaftaransUntukJabatan(Jabatan $jabatan, ?Tahapan $tahapan = null): Collection
    {
        $query = Pendaftaran::with([
            'mahasiswa:nim,nama_lengkap',
            'pilihanJabatan2:id,nama_posisi,nama_jabatan',
        ])
            ->where('jabatan_1_id', $jabatan->id)
            ->orderBy('id');

        if ($tahapanSebelumnya = $this->tahapanSebelumnya($tahapan)) {
            $pendaftaranLulus = KeputusanSeleksi::query()
                ->where('tahapan_id', $tahapanSebelumnya->id)
                ->where('jabatan_id', $jabatan->id)
                ->where('keputusan', 'lulus')
                ->pluck('pendaftaran_id');

            $query->whereIn('id', $pendaftaranLulus);
        }

        return $query->get();
    }

    private function pendaftaranUntukJabatan(int $pendaftaranId, Jabatan $jabatan, ?Tahapan $tahapan = null): Pendaftaran
    {
        $pendaftaran = Pendaftaran::with([
            'mahasiswa:nim,nama_lengkap',
            'pilihanJabatan2:id,nama_posisi,nama_jabatan',
        ])
            ->whereKey($pendaftaranId)
            ->where('jabatan_1_id', $jabatan->id)
            ->firstOrFail();

        if ($tahapan && ! $this->pesertaLulusTahapSebelumnya($pendaftaran, $jabatan, $tahapan)) {
            abort(Response::HTTP_NOT_FOUND, 'Peserta tidak dapat diproses pada tahapan ini.');
        }

        return $pendaftaran;
    }

    /** @return Collection<int, array<string, mixed>> */
    private function jawabanPesertaUntukTugas(
        Tugas $tugas,
        Collection $pendaftarans,
        ?Collection $keputusanPeserta = null,
        string $urutanWaktu = 'desc',
    ): Collection {
        $keputusanPeserta ??= collect();
        $pengumpulanPeserta = PengumpulanTugas::query()
            ->where('tugas_id', $tugas->id)
            ->whereIn('pendaftaran_id', $pendaftarans->pluck('id'))
            ->latest('updated_at')
            ->get()
            ->unique('pendaftaran_id')
            ->keyBy('pendaftaran_id');

        return $pendaftarans->map(function (Pendaftaran $pendaftaran) use ($pengumpulanPeserta, $tugas, $keputusanPeserta) {
            $pengumpulan = $pengumpulanPeserta->get($pendaftaran->id);
            $keputusan = $keputusanPeserta->get($pendaftaran->id);

            return [
                'pendaftaran_id' => $pendaftaran->id,
                'nama' => $pendaftaran->mahasiswa?->nama_lengkap ?? 'Peserta',
                'nim' => $pendaftaran->nim,
                'pilihan_2' => $pendaftaran->pilihanJabatan2
                    ? $pendaftaran->pilihanJabatan2->nama_posisi.' | '.$pendaftaran->pilihanJabatan2->nama_jabatan
                    : 'Tidak memilih',
                'dikumpulkan_pada' => $pengumpulan?->updated_at,
                'status_seleksi' => match ($keputusan?->keputusan) {
                    'lulus' => 'Lulus',
                    'tidak_lolos' => 'Tidak Lolos',
                    default => 'Menunggu Seleksi',
                },
                'keputusan_oleh' => $keputusan?->aktor_nama,
                'diputuskan_pada' => $keputusan?->diputuskan_pada,
                'jawaban' => $this->ringkasJawaban($pengumpulan, $tugas),
            ];
        })->sort(function (array $pesertaA, array $pesertaB) use ($urutanWaktu) {
            $waktuA = $pesertaA['dikumpulkan_pada']?->getTimestamp() ?? 0;
            $waktuB = $pesertaB['dikumpulkan_pada']?->getTimestamp() ?? 0;

            // Peserta yang belum mengumpulkan selalu ditempatkan setelah peserta yang sudah mengumpulkan.
            if ($waktuA === 0 || $waktuB === 0) {
                return $waktuA === $waktuB ? 0 : ($waktuA === 0 ? 1 : -1);
            }

            return $urutanWaktu === 'asc'
                ? $waktuA <=> $waktuB
                : $waktuB <=> $waktuA;
        })
            ->values();
    }

    /** @return Collection<int, KeputusanSeleksi> */
    private function keputusanTahapanPeserta(Tahapan $tahapan, Jabatan $jabatan, Collection $pendaftarans): Collection
    {
        if ($pendaftarans->isEmpty()) {
            return collect();
        }

        return KeputusanSeleksi::query()
            ->where('tahapan_id', $tahapan->id)
            ->where('jabatan_id', $jabatan->id)
            ->whereIn('pendaftaran_id', $pendaftarans->pluck('id'))
            ->get()
            ->keyBy('pendaftaran_id');
    }

    private function tahapanSebelumnya(?Tahapan $tahapan): ?Tahapan
    {
        if (! $tahapan) {
            return null;
        }

        return Tahapan::query()
            ->where('periode_rekrutmen_id', $tahapan->periode_rekrutmen_id)
            ->where('urutan_tahapan', '<', $tahapan->urutan_tahapan)
            ->orderByDesc('urutan_tahapan')
            ->first();
    }

    private function pesertaLulusTahapSebelumnya(Pendaftaran $pendaftaran, Jabatan $jabatan, Tahapan $tahapan): bool
    {
        $tahapanSebelumnya = $this->tahapanSebelumnya($tahapan);

        if (! $tahapanSebelumnya) {
            return true;
        }

        return KeputusanSeleksi::query()
            ->where('pendaftaran_id', $pendaftaran->id)
            ->where('jabatan_id', $jabatan->id)
            ->where('tahapan_id', $tahapanSebelumnya->id)
            ->where('keputusan', 'lulus')
            ->exists();
    }

    private function memakaiForm(Tugas $tugas): bool
    {
        return $tugas->tipe_tugas === 'wawancara'
            || $tugas->tipe_tugas === 'pengisian_form'
            || $tugas->tipe_jawaban_tugas === 'form';
    }

    /**
     * @return array<int, array{key: string, label: string, tipe: string, options: array<int, string>, required: bool, keterangan: string, kandidat_kunci: array<int, string>}>
     */
    private function pertanyaanTugas(Tugas $tugas): array
    {
        $lampiran = is_array($tugas->lampiran_tugas)
            ? $tugas->lampiran_tugas
            : (json_decode($tugas->lampiran_tugas ?? '[]', true) ?: []);
        $schema = (array) ($lampiran['form'] ?? []);
        $jumlahLabel = [];

        return collect($schema)
            ->filter(fn ($field) => is_array($field))
            ->values()
            ->map(function (array $field, int $indeks) use (&$jumlahLabel) {
                $labelDasar = trim((string) ($field['label'] ?? '')) ?: 'Pertanyaan '.($indeks + 1);
                $jumlahLabel[$labelDasar] = ($jumlahLabel[$labelDasar] ?? 0) + 1;
                $label = $jumlahLabel[$labelDasar] === 1
                    ? $labelDasar
                    : $labelDasar.' ('.$jumlahLabel[$labelDasar].')';
                $key = (string) ($field['id'] ?? $field['name'] ?? 'isian_'.$indeks);

                return [
                    'key' => $key,
                    'label' => $label,
                    'tipe' => (string) ($field['tipe'] ?? 'text_long'),
                    'options' => array_values(array_filter((array) ($field['options'] ?? []), fn ($option) => filled($option))),
                    'required' => (bool) ($field['required'] ?? false),
                    'keterangan' => trim((string) ($field['keterangan'] ?? '')),
                    'kandidat_kunci' => array_values(array_unique(array_filter([
                        $field['id'] ?? null,
                        $field['name'] ?? null,
                        'isian_'.$indeks,
                        'field_'.$indeks,
                        $labelDasar,
                        str($labelDasar)->slug('_')->toString(),
                    ], fn ($kunci) => filled($kunci)))),
                ];
            })
            ->all();
    }

    /** @return array<int, array{key: string, label: string, nilai: mixed, tipe: string}> */
    private function formatJawabanForm(array $jawaban, Tugas $tugas): array
    {
        $pertanyaan = $this->pertanyaanTugas($tugas);

        if ($pertanyaan === []) {
            return collect($jawaban)->map(fn ($nilai, $label) => [
                'key' => (string) $label,
                'label' => str_replace('_', ' ', (string) $label),
                'nilai' => $nilai,
                'tipe' => 'text_long',
            ])->values()->all();
        }

        return collect($pertanyaan)->map(function (array $pertanyaan) use ($jawaban) {
            $nilai = null;
            foreach ($pertanyaan['kandidat_kunci'] as $kunci) {
                if (array_key_exists($kunci, $jawaban)) {
                    $nilai = $jawaban[$kunci];
                    break;
                }
            }

            return [
                'key' => $pertanyaan['key'],
                'label' => $pertanyaan['label'],
                'nilai' => $nilai,
                'tipe' => $pertanyaan['tipe'],
            ];
        })->all();
    }

    /** @return array<string, mixed> */
    private function validasiJawabanWawancara(Request $request, array $pertanyaan): array
    {
        $aturan = [];

        foreach ($pertanyaan as $pertanyaanItem) {
            $nama = 'jawaban.'.$pertanyaanItem['key'];
            $aturanBidang = [$pertanyaanItem['required'] ? 'required' : 'nullable'];

            if ($pertanyaanItem['tipe'] === 'checkbox') {
                $aturan[$nama] = [...$aturanBidang, 'array'];
                $aturan[$nama.'.*'] = ['string', 'max:1000'];

                continue;
            }

            $aturanBidang[] = match ($pertanyaanItem['tipe']) {
                'email' => 'email',
                'number' => 'numeric',
                'date' => 'date',
                default => 'string',
            };
            $aturanBidang[] = 'max:5000';

            if (in_array($pertanyaanItem['tipe'], ['select', 'radio'], true) && $pertanyaanItem['options'] !== []) {
                $aturanBidang[] = Rule::in($pertanyaanItem['options']);
            }

            $aturan[$nama] = $aturanBidang;
        }

        $tervalidasi = $request->validate($aturan);
        $jawaban = [];

        foreach ($pertanyaan as $pertanyaanItem) {
            $jawaban[$pertanyaanItem['key']] = $tervalidasi['jawaban'][$pertanyaanItem['key']] ?? null;
        }

        return $jawaban;
    }

    /** @return array{tipe: string, akun_id: ?int, nama: string} */
    private function aktorSaatIni(Jabatan $jabatan): array
    {
        if (Auth::guard('organisasi')->check()) {
            $organisasi = Auth::guard('organisasi')->user();

            return [
                'tipe' => 'Organisasi',
                'akun_id' => $organisasi?->id,
                'nama' => $organisasi?->nama_organisasi ?? 'Organisasi',
            ];
        }

        $panitia = Panitia::query()
            ->where('nim', Auth::user()->nim)
            ->where('periode_rekrutmen_id', $jabatan->periode_rekrutmen_id)
            ->latest()
            ->first();

        abort_unless($panitia, Response::HTTP_FORBIDDEN, 'Akun panitia tidak terdaftar pada periode ini.');

        return [
            'tipe' => 'Panitia',
            'akun_id' => $panitia->id,
            'nama' => Auth::user()?->nama_lengkap ?? 'Panitia',
        ];
    }

    private function nilaiUntukEkspor(mixed $nilai): string
    {
        if (is_array($nilai)) {
            return collect($nilai)->map(fn ($item) => $this->nilaiUntukEkspor($item))->implode(', ');
        }

        if (is_string($nilai) && str_starts_with($nilai, 'rekrutmen/')) {
            return asset('storage/'.$nilai);
        }

        return is_scalar($nilai) ? (string) $nilai : '';
    }

    private function konteksAkses(): array
    {
        if (Auth::guard('organisasi')->check()) {
            return ['organisasiId' => Auth::guard('organisasi')->id(), 'routePrefix' => 'organisasi.'];
        }

        $kepanitiaan = Panitia::where('nim', Auth::user()->nim)->latest()->first();
        abort_unless($kepanitiaan, Response::HTTP_FORBIDDEN, 'Anda tidak terdaftar sebagai panitia.');

        $periode = PeriodeRekrutmen::find($kepanitiaan->periode_rekrutmen_id);
        abort_unless($periode, Response::HTTP_FORBIDDEN, 'Periode rekrutmen panitia tidak ditemukan.');

        return ['organisasiId' => $periode->organisasi_id, 'routePrefix' => 'panitia.'];
    }

    private function ringkasJawaban(?PengumpulanTugas $pengumpulan, Tugas $tugas): array
    {
        if (! $pengumpulan) {
            return ['status' => 'Belum dikumpulkan', 'jenis' => 'kosong', 'isi' => [], 'oleh' => null];
        }

        $data = is_array($pengumpulan->lampiran_jawaban) ? $pengumpulan->lampiran_jawaban : (json_decode($pengumpulan->lampiran_jawaban ?? '[]', true) ?: []);

        if ($tugas->tipe_tugas === 'wawancara') {
            $jawabanWawancara = (array) ($data['jawaban_wawancara']['form'] ?? []);
            $sudahWawancara = array_key_exists('jawaban_wawancara', $data);
            $status = $sudahWawancara
                ? 'Wawancara selesai'
                : (! empty($data['kehadiran']) || ! empty($data['status_wawancara'])
                    ? 'Hadir dikonfirmasi'
                    : 'Menunggu wawancara');

            return [
                'status' => $status,
                'jenis' => 'wawancara',
                'isi' => $this->formatJawabanForm($jawabanWawancara, $tugas),
                'oleh' => $pengumpulan->pewawancara_nama,
            ];
        }

        if (! empty($data['berkas'])) {
            return ['status' => 'Terkirim', 'jenis' => 'berkas', 'isi' => (array) $data['berkas'], 'oleh' => null];
        }

        if ($this->memakaiForm($tugas)) {
            return [
                'status' => 'Terkirim',
                'jenis' => 'form',
                'isi' => $this->formatJawabanForm((array) ($data['form'] ?? $data), $tugas),
                'oleh' => null,
            ];
        }

        return ['status' => 'Terkirim', 'jenis' => 'lainnya', 'isi' => [], 'oleh' => null];
    }
}
