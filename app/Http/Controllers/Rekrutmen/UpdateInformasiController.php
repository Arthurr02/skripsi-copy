<?php

namespace App\Http\Controllers\Rekrutmen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

use App\Models\PeriodeRekrutmen;
use App\Models\Tahapan;
use App\Models\Tugas;
use App\Models\Jabatan;
use App\Models\Panitia;
use Carbon\Carbon;

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
            $kepanitiaan = Panitia::where('nim', $nimPanitia)
                ->where('panitia_rekrutmen', 1)
                ->latest()
                ->first();

            if (!$kepanitiaan)
                abort(403, 'Anda tidak terdaftar sebagai panitia aktif.');

            $periodePanitia = PeriodeRekrutmen::find($kepanitiaan->periode_rekrutmen_id);
            $organisasiId = $periodePanitia->organisasi_id;
        }

        if ($periode_id) {
            $periode = PeriodeRekrutmen::where('organisasi_id', $organisasiId)->findOrFail($periode_id);
        } else {
            $periode = PeriodeRekrutmen::where('organisasi_id', $organisasiId)->latest()->first();

            if (!$periode) {
                if ($isOrganisasi) {
                    return redirect()->route('organisasi.buka-rekrutmen.index')->with('error_server', 'Silakan buka rekrutmen periode baru terlebih dahulu.');
                } else {
                    return redirect()->route('panitia.dashboard')->with('error_server', 'Belum ada data periode rekrutmen yang aktif.');
                }
            }
        }

        $tahapanData = Tahapan::with('tugas')
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

                if (!isset($tempGroup[$posisi])) {
                    $tempGroup[$posisi] = [];
                }
                $tempGroup[$posisi][] = ['nama' => $jabatan->nama_jabatan];
            }

            foreach ($tempGroup as $posisi => $jabatans) {
                $groupedJabatan[] = [
                    'posisi' => $posisi,
                    'jabatans' => $jabatans
                ];
            }
        } else {
            // Jika form baru / belum ada data
            $groupedJabatan = [
                [
                    'posisi' => '',
                    'jabatans' => [['nama' => '']]
                ]
            ];
        }
        // ---------------------------------------------

        // Pastikan 'groupedJabatan' ditambahkan ke dalam compact()
        return view('rekrutmen.informasi.index', compact('periode', 'tahapanData', 'jabatanData', 'groupedJabatan', 'routePrefix'));
    }

    // 2. FUNGSI UNTUK MENYIMPAN DATA (POST)
    public function store(Request $request, $periode_id)
    {
        $request->validate([
            'slogan' => 'required|string',
            'deskripsi_rekrutmen' => 'required|string',
            'banner' => 'nullable|file|mimetypes:image/jpeg,image/png|mimes:jpg,jpeg,png|max:2048',
            'buku_pedoman' => 'nullable|file|mimetypes:application/pdf|mimes:pdf|max:5120',
            // --- VALIDASI POSISI DAN JABATAN ---
            'nama_posisi' => 'nullable|array',
            'nama_posisi.*' => 'nullable|string',
            'nama_jabatan' => 'required|array|min:1',
            'nama_jabatan.*' => 'required|string|regex:/^[a-zA-Z0-9 ]+$/',
            // -----------------------------------
            'tahapan' => 'required|array|min:1',
            'tahapan.*.nama_tahapan' => 'required|string',
            'tahapan.*.deskripsi' => 'required|string',
            'tahapan.*.tugas.*.deskripsi_tugas' => 'nullable|string',
            'tahapan_lampiran_*' => 'nullable|file|mimetypes:application/pdf|mimes:pdf|max:5120',
            'tahapan.*.tugas.*.lampiran_files.*' => 'nullable|file|mimetypes:application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document|mimes:pdf,doc,docx|max:2048',
        ]);

        DB::beginTransaction();
        try {
            $periode = PeriodeRekrutmen::findOrFail($periode_id);

            // 1. UPDATE DATA PERIODE
            $dataUpdatePeriode = [
                'slogan' => strip_tags($request->slogan),
                'deskripsi' => strip_tags($request->deskripsi_rekrutmen),
                'status_aktif' => 2
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

            // 2. KUMPULKAN DATA LAMA
            $tahapanLama = Tahapan::where('periode_rekrutmen_id', $periode->id)->get()->keyBy('urutan_tahapan');

            // ... (kode sebelumnya Langkah 1 & 2 tetap sama) ...

            // 3. PROSES JABATAN (MENYESUAIKAN DATABASE ASLI ANDA)
            Jabatan::where('periode_rekrutmen_id', $periode->id)->delete();

            $mapJabatanId = [];     // Pemetaan berdasarkan nama jabatan
            $mapJabatanIndex = [];  // Peta indeks urutan untuk memastikan tidak tertukar
            $posisiArray = $request->input('nama_posisi', []);

            foreach ($request->nama_jabatan as $index => $namaJabatan) {
                if (!empty($namaJabatan)) {
                    // Ambil posisi yang sejajar dengan index jabatannya. Jika tidak diisi, set sebagai string kosong atau nilai default
                    $namaPosisiRaw = !empty($posisiArray[$index]) ? trim($posisiArray[$index]) : '-';

                    // Simpan KEDUA data langsung ke dalam tabel jabatan sesuai skema Anda
                    $jabatanBaru = Jabatan::create([
                        'periode_rekrutmen_id' => $periode->id,
                        'nama_posisi' => $namaPosisiRaw,
                        'nama_jabatan' => trim($namaJabatan)
                    ]);

                    $mapJabatanId[trim($namaJabatan)] = $jabatanBaru->id;
                    $mapJabatanIndex[$index] = $jabatanBaru->id;
                }
            }

            // 4. RESET TAHAPAN LAMA
            $listTahapanLamaId = $tahapanLama->pluck('id');
            Tugas::whereIn('tahapan_id', $listTahapanLamaId)->delete();
            Tahapan::where('periode_rekrutmen_id', $periode->id)->delete();

            // 5. SIMPAN ULANG TAHAPAN
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

                $lampiranPathArray = $tahapanLama->has($urutan) ? ($tahapanLama->get($urutan)->lampiran_tahapan ?? []) : [];

                if ($request->hasFile("tahapan_lampiran_$tIndex")) {
                    foreach ($lampiranPathArray as $old) {
                        Storage::disk('public')->delete($old);
                    }
                    $lampiranPathArray = [$request->file("tahapan_lampiran_$tIndex")->store('rekrutmen/tahapan', 'public')];
                }

                $tahapan = Tahapan::create([
                    'periode_rekrutmen_id' => $periode->id,
                    'nama_tahapan' => strip_tags($tData['nama_tahapan']),
                    'deskripsi_tahapan' => strip_tags($tData['deskripsi']),
                    'lampiran_tahapan' => empty($lampiranPathArray) ? null : $lampiranPathArray,
                    'waktu_mulai' => $waktuMulai,
                    'waktu_berakhir' => $waktuSelesai,
                    'urutan_tahapan' => $urutan
                ]);

                // 6. PROSES TUGAS
                if (!$isPengumuman && isset($tData['tugas']) && is_array($tData['tugas'])) {
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
                            if ($jIndex !== 0)
                                continue;

                            foreach ($mapJabatanId as $namaJabatan => $jabatanIdFinal) {
                                $tipeTugas = $urutan === 1 ? 'pengisian_form' : ($tugasData['tipe_tugas'] ?? 'pengisian_form');
                                $tipeJawaban = ($tipeTugas === 'pengisian_form') ? 'form' : (($tipeTugas === 'wawancara') ? 'wawancara' : (isset($tugasData['format_proyek']) ? implode(',', $tugasData['format_proyek']) : ''));

                                Tugas::create([
                                    'tahapan_id' => $tahapan->id,
                                    'jabatan_id' => $jabatanIdFinal,
                                    'tipe_tugas' => $tipeTugas,
                                    'tipe_jawaban_tugas' => $tipeJawaban,
                                    'deskripsi_tugas' => strip_tags($tugasData['deskripsi_tugas'] ?? ''),
                                    'lampiran_tugas' => [
                                        'berkas' => $pathsTemplate,
                                        'form' => json_decode($tugasData['skema_form_json'] ?? '[]', true) ?: []
                                    ],
                                ]);
                            }
                        } else {
                            if (empty($tugasData['nama_jabatan']))
                                continue;

                            // LOGIKA BARU: Cari berdasarkan Index dahulu agar akurat, lalu fallback ke nama jabatan
                            $jabatanIdFinal = $mapJabatanIndex[$jIndex] ?? ($mapJabatanId[trim($tugasData['nama_jabatan'])] ?? null);

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

                            Tugas::create([
                                'tahapan_id' => $tahapan->id,
                                'jabatan_id' => $jabatanIdFinal,
                                'tipe_tugas' => $tipeTugas,
                                'tipe_jawaban_tugas' => $tipeJawaban,
                                'deskripsi_tugas' => strip_tags($tugasData['deskripsi_tugas'] ?? ''),
                                'lampiran_tugas' => [
                                    'berkas' => $paths,
                                    'form' => json_decode($tugasData['skema_form_json'] ?? '[]', true) ?: []
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
            if ($e instanceof ValidationException)
                throw $e;
            dd($e->getMessage(), $e->getFile(), $e->getLine());
        }
    }
}