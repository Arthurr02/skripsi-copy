<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\PeriodeRekrutmen;
use App\Models\Tahapan;
use App\Models\Tugas;

class RekrutmenAktifController extends Controller
{
    // 1. FUNGSI UNTUK MENAMPILKAN HALAMAN FORM (GET)
    public function updateInfo($periode_id = null)
    {
        $organisasiId = Auth::guard('organisasi')->id();

        // 1. Cari periode aktif
        if ($periode_id) {
            $periode = PeriodeRekrutmen::where('organisasi_id', $organisasiId)->findOrFail($periode_id);
        } else {
            $periode = PeriodeRekrutmen::where('organisasi_id', $organisasiId)
                ->latest()
                ->first();

            if (!$periode) {
                return redirect()->route('organisasi.periode.inisiasi')->with('error_server', 'Silakan buka rekrutmen periode baru terlebih dahulu.');
            }
        }

        // 2. SINKRONISASI LENGKAP: Muat data tahapan beserta relasi 'tugas'-nya
        $tahapanData = Tahapan::with('tugas')
            ->where('periode_rekrutmen_id', $periode->id)
            ->orderBy('urutan_tahapan', 'asc') // Urutkan berdasarkan kolom urutan yang baru
            ->get();

        // 3. Ambil juga data jabatan khusus untuk periode ini agar bisa dimuat di Tab 1
        // (Asumsi relasi di model PeriodeRekrutmen ke Jabatan bernama 'jabatan')
        $jabatanData = \App\Models\Jabatan::where('periode_rekrutmen_id', $periode->id)->get();

        return view('organisasi.rekrutmen-saat-ini.update', compact('periode', 'tahapanData', 'jabatanData'));
    }

    // 2. FUNGSI UNTUK MENYIMPAN DATA (POST)
    public function storeUpdateInfo(Request $request, $periode_id)
    {
        $request->validate([
            'slogan' => 'required|string',
            'deskripsi_rekrutmen' => 'required|string',
            'banner' => 'nullable|image|max:2048',
            'buku_pedoman' => 'nullable|mimes:pdf|max:5120',

            // VALIDASI BACKEND: Wajib diisi, dan hanya boleh huruf, angka, spasi (alpha_num + spasi)
            'nama_jabatan' => 'required|array|min:1',
            'nama_jabatan.*' => 'required|string|regex:/^[a-zA-Z0-9 ]+$/',

            'tahapan' => 'required|array|min:1',
            'tahapan.*.nama_tahapan' => 'required|string|regex:/^[a-zA-Z0-9 ]+$/',
            'tahapan.*.deskripsi' => 'required|string|regex:/^[a-zA-Z0-9 ]+$/',
            'tahapan.*.tugas.*.deskripsi_tugas' => 'required|string|regex:/^[a-zA-Z0-9 ]+$/',
            'tahapan_lampiran_*' => 'nullable|mimes:pdf|max:5120',
        ]);

        DB::beginTransaction();
        try {
            $periode = PeriodeRekrutmen::findOrFail($periode_id);

            // 1. UPDATE DATA PERIODE
            $dataUpdatePeriode = [
                'slogan' => strip_tags($request->slogan),
                'deskripsi' => strip_tags($request->deskripsi_rekrutmen),
                'status_aktif' => 1
            ];

            // Penanganan file Banner & Pedoman (Sama dengan sebelumnya)
            if ($request->hasFile('banner')) {
                $pathBanner = $request->file('banner')->store('rekrutmen/banner', 'public');
                $dataUpdatePeriode['lampiran_banner'] = json_encode([$pathBanner]);
            }
            if ($request->hasFile('buku_pedoman')) {
                $pathPedoman = $request->file('buku_pedoman')->store('rekrutmen/pedoman', 'public');
                $dataUpdatePeriode['lampiran_pedoman'] = json_encode([$pathPedoman]);
            }
            $periode->update($dataUpdatePeriode);

            // 2. KUMPULKAN DATA LAMA SEBELUM DIHAPUS (PENTING!)
            $tahapanLama = Tahapan::where('periode_rekrutmen_id', $periode->id)->get()->keyBy('urutan_tahapan');

            // 3. PROSES JABATAN
            \App\Models\Jabatan::where('periode_rekrutmen_id', $periode->id)->delete();
            $mapJabatanId = [];
            foreach ($request->nama_jabatan as $namaJabatan) {
                if (!empty($namaJabatan)) {
                    $jabatanBaru = \App\Models\Jabatan::create([
                        'periode_rekrutmen_id' => $periode->id,
                        'nama_jabatan' => trim($namaJabatan)
                    ]);
                    $mapJabatanId[trim($namaJabatan)] = $jabatanBaru->id;
                }
            }

            // 4. RESET ALUR TAHAPAN & TUGAS LAMA
            $listTahapanLamaId = $tahapanLama->pluck('id');
            Tugas::whereIn('tahapan_id', $listTahapanLamaId)->delete();
            Tahapan::where('periode_rekrutmen_id', $periode->id)->delete();

            // 5. SIMPAN ULANG TAHAPAN
            foreach ($request->tahapan as $tIndex => $tData) {
                $urutan = $tIndex + 1;

                // Menangani File Tahapan (Seperti sebelumnya)
                $lampiranPath = $tahapanLama->has($urutan) ? $tahapanLama->get($urutan)->getRawOriginal('lampiran_tahapan') : null;
                if ($request->hasFile("tahapan_lampiran_$tIndex")) {
                    $pathAsli = $request->file("tahapan_lampiran_$tIndex")->store('rekrutmen/tahapan', 'public');
                    $lampiranPath = json_encode([$pathAsli]);
                }

                $tahapan = Tahapan::create([
                    'periode_rekrutmen_id' => $periode->id,
                    'nama_tahapan' => strip_tags($tData['nama_tahapan']),
                    'deskripsi_tahapan' => strip_tags($tData['deskripsi']),
                    'lampiran_tahapan' => $lampiranPath,
                    'waktu_mulai' => $tData['tanggal_mulai'],
                    'waktu_berakhir' => $tData['tanggal_selesai'],
                    'urutan_tahapan' => $urutan
                ]);

                // PROSES TUGAS DENGAN DEFENSIVE PROGRAMMING
                // Di dalam loop foreach ($request->tahapan as $tIndex => $tData)
// ... setelah membuat $tahapan ...

                if (isset($tData['tugas'])) {
                    foreach ($tData['tugas'] as $jIndex => $tugasData) {

                        // 1. Ambil Jabatan ID
                        $namaJabatan = trim($tugasData['nama_jabatan']);
                        $jabatanId = $mapJabatanId[$namaJabatan] ?? null;

                        // 2. Tangani Upload Multiple Files
                        $inputKey = "tahapan.{$tIndex}.tugas.{$jIndex}.lampiran";
                        $paths = [];
                        if ($request->hasFile($inputKey)) {
                            foreach ($request->file($inputKey) as $file) {
                                $paths[] = $file->store('rekrutmen/tugas', 'public');
                            }
                        }

                        // 3. Simpan ke Tugas
                        Tugas::create([
                            'tahapan_id' => $tahapan->id,
                            'jabatan_id' => $jabatanId,
                            'tipe_tugas' => 'seleksi',
                            'tipe_jawaban_tugas' => $tugasData['tipe_jawaban_tugas'] ?? 'file',
                            'deskripsi_tugas' => strip_tags($tugasData['deskripsi_tugas']),
                            'lampiran_tugas' => json_encode($paths), // Kolom JSON
                        ]);
                    }
                }
            }

            DB::commit();
            return redirect()->route('organisasi.dashboard')->with('success_update', 'Berhasil!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error_server', 'Gagal: ' . $e->getMessage());
        }
    }
}