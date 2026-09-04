<?php

namespace App\Http\Controllers\Rekrutmen;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateRecruitmentInformationRequest;
use App\Models\Jabatan;
use App\Models\Panitia;
use App\Models\PeriodeRekrutmen;
use App\Models\Tahapan;
use App\Models\Tugas;
use App\Services\Recruitment\PositionSynchronizer;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class UpdateInformasiController extends Controller
{
    // 1. FUNGSI UNTUK MENAMPILKAN HALAMAN FORM (GET)
    public function index($periode_id = null)
    {
        $isOrganisasi = Auth::guard('organisasi')->check();

        if ($isOrganisasi) {
            $organisasiId = Auth::guard('organisasi')->id();
        } else {
            $nimPanitia = Auth::user()->nim;
            $kepanitiaan = Panitia::query()
                ->with('periode')
                ->where('nim', $nimPanitia)
                ->whereHas('periode', fn ($query) => $query->whereIn('status_aktif', [1, 2]))
                ->latest()
                ->first();

            if (! $kepanitiaan) {
                abort(403, 'Anda tidak terdaftar sebagai panitia aktif.');
            }

            $periodePanitia = $kepanitiaan->periode;
            $organisasiId = $periodePanitia->organisasi_id;
        }

        if ($periode_id) {
            $periode = PeriodeRekrutmen::where('organisasi_id', $organisasiId)
                ->whereIn('status_aktif', [1, 2])
                ->findOrFail($periode_id);
        } else {
            $periode = PeriodeRekrutmen::where('organisasi_id', $organisasiId)
                ->whereIn('status_aktif', [1, 2])
                ->latest()
                ->first();

            if (! $periode) {
                if ($isOrganisasi) {
                    return redirect()->route('organisasi.buka-rekrutmen.index')->with('error_server', 'Silakan buka rekrutmen periode baru terlebih dahulu.');
                } else {
                    return redirect()->route('panitia.dashboard')->with('error_server', 'Belum ada data periode rekrutmen yang aktif.');
                }
            }
        }

        $tahapanData = Tahapan::with('tugas.jabatan')
            ->where('periode_rekrutmen_id', $periode->id)
            ->orderBy('urutan_tahapan', 'asc')
            ->get()
            ->map(function ($t) {
                $waktuMulai = Carbon::parse($t->waktu_mulai)->format('Y-m-d\TH:i');
                $waktuSelesai = Carbon::parse($t->waktu_berakhir)->format('Y-m-d\TH:i');

                $t->waktu_mulai = $waktuMulai;
                $t->waktu_berakhir = $waktuSelesai;
                $t->is_pengumuman = ($waktuMulai === $waktuSelesai);

                return $t;
            });

        // ... (kode di atasnya tetap sama) ...

        $jabatanData = Jabatan::where('periode_rekrutmen_id', $periode->id)->get();
        $routePrefix = $isOrganisasi ? 'organisasi.' : 'panitia.';

        // --- 🌟 TAMBAHKAN LOGIKA GROUPING DI SINI ---
        $groupedJabatan = [];
        if ($jabatanData->count() > 0) {
            $tempGroup = [];
            foreach ($jabatanData as $jabatan) {
                // Ubah tanda '-' menjadi string kosong agar cantik di UI
                $posisi = ($jabatan->nama_posisi === '-' || empty($jabatan->nama_posisi)) ? '' : $jabatan->nama_posisi;

                if (! isset($tempGroup[$posisi])) {
                    $tempGroup[$posisi] = [];
                }
                $tempGroup[$posisi][] = [
                    'id' => $jabatan->id,
                    'nama' => $jabatan->nama_jabatan,
                ];
            }

            foreach ($tempGroup as $posisi => $jabatans) {
                $groupedJabatan[] = [
                    'posisi' => $posisi,
                    'jabatans' => $jabatans,
                ];
            }
        } else {
            // Jika form baru / belum ada data
            $groupedJabatan = [
                [
                    'posisi' => '',
                    'jabatans' => [['nama' => '']],
                ],
            ];
        }
        // ---------------------------------------------

        // Pastikan 'groupedJabatan' ditambahkan ke dalam compact()
        return view('rekrutmen.informasi.index', compact('periode', 'tahapanData', 'jabatanData', 'groupedJabatan', 'routePrefix'));
    }

    // 2. FUNGSI UNTUK MENYIMPAN DATA (POST)
    public function store(UpdateRecruitmentInformationRequest $request, int $periode_id, PositionSynchronizer $positionSynchronizer)
    {
        DB::beginTransaction();
        try {
            $periode = PeriodeRekrutmen::findOrFail($periode_id);

            // 1. UPDATE DATA PERIODE
            $dataUpdatePeriode = [
                'slogan' => strip_tags($request->slogan),
                'deskripsi' => strip_tags($request->deskripsi_rekrutmen),
                'status_aktif' => 2,
            ];

            if ($request->hasFile('banner')) {
                $oldBanners = $periode->lampiran_banner ?? [];
                foreach ($oldBanners as $old) {
                    Storage::disk('public')->delete($old);
                }
                $dataUpdatePeriode['lampiran_banner'] = [$request->file('banner')->store('rekrutmen/banner', 'public')];
            }

            if ($request->hasFile('buku_pedoman')) {
                $oldPedoman = $periode->lampiran_pedoman ?? [];
                foreach ($oldPedoman as $old) {
                    Storage::disk('public')->delete($old);
                }
                $dataUpdatePeriode['lampiran_pedoman'] = [$request->file('buku_pedoman')->store('rekrutmen/pedoman', 'public')];
            }

            $periode->update($dataUpdatePeriode);

            // Jangan menghapus dan membuat ulang tahapan/tugas. Pengumpulan tugas
            // memiliki foreign key cascade ke tugas, sehingga pola tersebut berisiko
            // menghapus jawaban mahasiswa secara permanen.
            $tahapanLama = Tahapan::where('periode_rekrutmen_id', $periode->id)->get()->keyBy('id');

            // ... (kode sebelumnya Langkah 1 & 2 tetap sama) ...

            $mapJabatanIndex = $positionSynchronizer->synchronize(
                $periode,
                $request->input('nama_posisi', []),
                $request->input('nama_jabatan'),
                $request->input('jabatan_ids', []),
            );

            $idTahapanDikirim = collect($request->input('tahapan', []))
                ->pluck('id')
                ->filter()
                ->map(fn ($id) => (int) $id);
            $tahapanDihapus = $tahapanLama->except($idTahapanDikirim->all());

            // Tahapan yang telah memiliki jawaban tidak boleh dihapus dari UI.
            // Ini menjadi pengaman server-side bila JavaScript atau request dimanipulasi.
            $tugasDenganJawaban = Tugas::query()
                ->whereIn('tahapan_id', $tahapanDihapus->pluck('id'))
                ->whereHas('pengumpulanTugas')
                ->exists();

            if ($tugasDenganJawaban) {
                throw ValidationException::withMessages([
                    'tahapan' => 'Tahapan yang sudah memiliki jawaban peserta tidak dapat dihapus. Pertahankan tahapan tersebut agar riwayat peserta tetap aman.',
                ]);
            }

            // Hanya tahapan tanpa satu pun jawaban yang aman untuk dihapus.
            if ($tahapanDihapus->isNotEmpty()) {
                Tugas::whereIn('tahapan_id', $tahapanDihapus->pluck('id'))->delete();
                Tahapan::whereKey($tahapanDihapus->pluck('id'))->delete();
            }

            // Perbarui data berdasarkan ID agar tugas dan jawaban yang telah
            // dikumpulkan tetap memakai rekam yang sama.
            $waktuAkhirSebelumnya = null;

            foreach ($request->tahapan as $tIndex => $tData) {
                $urutan = $tIndex + 1;
                $isPengumuman = isset($tData['is_pengumuman']) && ($tData['is_pengumuman'] === 'true' || $tData['is_pengumuman'] === '1');
                $waktuMulai = $tData['tanggal_mulai'];
                $waktuSelesai = $isPengumuman ? $waktuMulai : $tData['tanggal_selesai'];

                if ($waktuAkhirSebelumnya && $waktuMulai < $waktuAkhirSebelumnya) {
                    throw ValidationException::withMessages(["tahapan.{$tIndex}.tanggal_mulai" => "Waktu mulai Tahapan ke-{$urutan} tidak boleh mendahului waktu selesai tahapan sebelumnya!"]);
                }
                $waktuAkhirSebelumnya = $waktuSelesai;

                $tahapanId = $tData['id'] ?? null;
                if ($tahapanId && ! $tahapanLama->has((int) $tahapanId)) {
                    throw ValidationException::withMessages(["tahapan.{$tIndex}.id" => 'Tahapan tidak termasuk dalam periode rekrutmen ini.']);
                }

                $tahapanLamaSaatIni = $tahapanId ? $tahapanLama->get((int) $tahapanId) : null;
                $lampiranPathArray = $tahapanLamaSaatIni?->lampiran_tahapan ?? [];

                if ($request->hasFile("tahapan_lampiran_$tIndex")) {
                    foreach ($lampiranPathArray as $old) {
                        Storage::disk('public')->delete($old);
                    }
                    $lampiranPathArray = [$request->file("tahapan_lampiran_$tIndex")->store('rekrutmen/tahapan', 'public')];
                }

                $atributTahapan = [
                    'periode_rekrutmen_id' => $periode->id,
                    'nama_tahapan' => strip_tags($tData['nama_tahapan']),
                    'deskripsi_tahapan' => strip_tags($tData['deskripsi']),
                    'lampiran_tahapan' => empty($lampiranPathArray) ? null : $lampiranPathArray,
                    'waktu_mulai' => $waktuMulai,
                    'waktu_berakhir' => $waktuSelesai,
                    'urutan_tahapan' => $urutan,
                ];

                $tahapan = $tahapanLamaSaatIni
                    ? tap($tahapanLamaSaatIni)->update($atributTahapan)
                    : Tahapan::create($atributTahapan);

                // 6. PROSES TUGAS
                if (! $isPengumuman && isset($tData['tugas']) && is_array($tData['tugas'])) {
                    $metodeDistribusi = $tData['metode_distribusi'] ?? 'sama';
                    $pathsTemplate = [];

                    if ($metodeDistribusi === 'sama' && isset($tData['tugas'][0])) {
                        $pathsTemplateRaw = json_decode($tData['tugas'][0]['berkas_lama_json'] ?? '[]', true);
                        while (is_string($pathsTemplateRaw)) {
                            $pathsTemplateRaw = json_decode($pathsTemplateRaw, true);
                        }
                        $pathsTemplate = is_array($pathsTemplateRaw) ? $pathsTemplateRaw : [];

                        $inputKey = "tahapan.{$tIndex}.tugas.0.lampiran_files";
                        if ($request->hasFile($inputKey)) {
                            foreach ($pathsTemplate as $oldFile) {
                                Storage::disk('public')->delete($oldFile);
                            }
                            $pathsTemplate = [];
                            foreach ($request->file($inputKey) as $file) {
                                $pathsTemplate[] = $file->store('rekrutmen/tugas', 'public');
                            }
                        }
                    }

                    foreach ($tData['tugas'] as $jIndex => $tugasData) {
                        if ($metodeDistribusi === 'sama') {
                            if ($jIndex !== 0) {
                                continue;
                            }

                            foreach ($mapJabatanIndex as $jabatanIdFinal) {
                                $tipeTugas = $urutan === 1 ? 'pengisian_form' : ($tugasData['tipe_tugas'] ?? 'pengisian_form');
                                $tipeJawaban = ($tipeTugas === 'pengisian_form') ? 'form' : (($tipeTugas === 'wawancara') ? 'wawancara' : (isset($tugasData['format_proyek']) ? implode(',', $tugasData['format_proyek']) : ''));

                                Tugas::updateOrCreate([
                                    'tahapan_id' => $tahapan->id,
                                    'jabatan_id' => $jabatanIdFinal,
                                ], [
                                    'tahapan_id' => $tahapan->id,
                                    'jabatan_id' => $jabatanIdFinal,
                                    'tipe_tugas' => $tipeTugas,
                                    'tipe_jawaban_tugas' => $tipeJawaban,
                                    'deskripsi_tugas' => strip_tags($tugasData['deskripsi_tugas'] ?? ''),
                                    'lampiran_tugas' => [
                                        'berkas' => $pathsTemplate,
                                        'form' => json_decode($tugasData['skema_form_json'] ?? '[]', true) ?: [],
                                    ],
                                ]);
                            }
                        } else {
                            if (empty($tugasData['nama_jabatan'])) {
                                continue;
                            }

                            $jabatanIdFinal = $tugasData['jabatan_id'] ?? ($mapJabatanIndex[$jIndex] ?? null);
                            if (! $jabatanIdFinal) {
                                continue;
                            }
                            $pathsRaw = json_decode($tugasData['berkas_lama_json'] ?? '[]', true);
                            while (is_string($pathsRaw)) {
                                $pathsRaw = json_decode($pathsRaw, true);
                            }
                            $paths = is_array($pathsRaw) ? $pathsRaw : [];

                            $inputKey = "tahapan.{$tIndex}.tugas.{$jIndex}.lampiran_files";
                            if ($request->hasFile($inputKey)) {
                                foreach ($paths as $oldFile) {
                                    Storage::disk('public')->delete($oldFile);
                                }
                                $paths = [];
                                foreach ($request->file($inputKey) as $file) {
                                    $paths[] = $file->store('rekrutmen/tugas', 'public');
                                }
                            }

                            $tipeTugas = $urutan === 1 ? 'pengisian_form' : ($tugasData['tipe_tugas'] ?? 'pengisian_form');
                            $tipeJawaban = ($tipeTugas === 'pengisian_form') ? 'form' : (($tipeTugas === 'wawancara') ? 'wawancara' : (isset($tugasData['format_proyek']) ? implode(',', $tugasData['format_proyek']) : ''));

                            Tugas::updateOrCreate([
                                'tahapan_id' => $tahapan->id,
                                'jabatan_id' => $jabatanIdFinal,
                            ], [
                                'tahapan_id' => $tahapan->id,
                                'jabatan_id' => $jabatanIdFinal,
                                'tipe_tugas' => $tipeTugas,
                                'tipe_jawaban_tugas' => $tipeJawaban,
                                'deskripsi_tugas' => strip_tags($tugasData['deskripsi_tugas'] ?? ''),
                                'lampiran_tugas' => [
                                    'berkas' => $paths,
                                    'form' => json_decode($tugasData['skema_form_json'] ?? '[]', true) ?: [],
                                ],
                            ]);
                        }
                    }
                }
            }

            DB::commit();

            if (Auth::guard('organisasi')->check()) {
                return redirect()->route('organisasi.dashboard')->with('success_update', 'Pengaturan Berhasil Disimpan!');
            } else {
                return redirect()->route('panitia.dashboard')->with('success_update', 'Pengaturan Berhasil Disimpan!');
            }

        } catch (\Exception $e) {
            DB::rollBack();
            if ($e instanceof ValidationException) {
                throw $e;
            }
            report($e);

            return back()->withInput()->with('error_server', 'Gagal menyimpan perubahan. Silakan coba lagi.');
        }
    }
}
