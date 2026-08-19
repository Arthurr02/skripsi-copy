<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\PeriodeRekrutmen;
use App\Models\Tahapan;
use App\Models\Tugas;
use App\Models\Jabatan;
use App\Models\Pendaftaran;
use App\Models\PengumpulanTugas;

class DaftarRekrutmenController extends Controller
{
    public function index()
    {
        // Ambil semua rekrutmen yang sedang aktif (buka)
        $rekrutmenAktif = PeriodeRekrutmen::with('organisasi')
            ->where('status_aktif', 2)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('mahasiswa.rekrutmen.index', compact('rekrutmenAktif'));
    }

    public function info($periode_id)
    {
        $rekrutmen = PeriodeRekrutmen::with('organisasi')->findOrFail($periode_id);
        $jabatans = Jabatan::where('periode_rekrutmen_id', $periode_id)->get();
        $tahapans = Tahapan::where('periode_rekrutmen_id', $periode_id)
            ->get()
            ->sortBy('waktu_mulai');

        return view('mahasiswa.rekrutmen.informasi', compact('rekrutmen', 'jabatans', 'tahapans'));
    }

    public function kerjakanTahapanSatu($periode_id)
    {
        $rekrutmen = PeriodeRekrutmen::with('organisasi')->findOrFail($periode_id);
        $tahapanSatu = Tahapan::where('periode_rekrutmen_id', $periode_id)
            ->get()
            ->sortBy('waktu_mulai')
            ->first();

        if (!$tahapanSatu) {
            return redirect()->route('mahasiswa.rekrutmen.info', $periode_id)
                ->with('error', 'Pendaftaran belum bisa dilakukan karena panitia belum mengatur jadwal tahapan seleksi.');
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

                if (!isset($groupedJabatan[$namaPosisi])) {
                    $groupedJabatan[$namaPosisi] = [];
                }

                // Masukkan data jabatan ke dalam grup posisi yang bersangkutan
                $groupedJabatan[$namaPosisi][] = [
                    'id' => $jabatan->id,
                    'nama_jabatan' => $jabatan->nama_jabatan
                ];
            }
        }
        // -------------------------------------------

        // Lempar $groupedJabatan (bukan hanya $jabatans mentah) ke tampilan
        return view('mahasiswa.rekrutmen.pendaftaran.index', compact('rekrutmen', 'tahapanSatu', 'jabatans', 'groupedJabatan'));
    }

    public function submitPendaftaran(Request $request, $periode_id)
    {
        // 1. Aturan Validasi Dasar Struktural
        $rules = [
            'jabatan_1_id' => 'required|exists:jabatan,id',
            'jabatan_2_id' => 'nullable|exists:jabatan,id|different:jabatan_1_id',
            'dynamic_answers' => 'nullable|array',
            'file_berkas' => 'nullable|file|max:5120',
        ];

        $customMessages = [
            'jabatan_1_id.required' => 'Formasi prioritas utama (Pilihan 1) wajib ditentukan.',
            'jabatan_2_id.different' => 'Formasi Pilihan 2 tidak boleh identik dengan Pilihan 1.',
            'file_berkas.max' => 'Berkas lampiran melebihi kapasitas batas maksimal 5 MB.',
        ];

        // 2. Membaca Validasi Dinamis Berdasarkan Skema Form Builder Panitia
        $tahapanSatu = Tahapan::where('periode_rekrutmen_id', $periode_id)
            ->orderBy('urutan_tahapan', 'asc')
            ->first();

        $tugas = null;
        if ($tahapanSatu) {
            $tugas = Tugas::where('tahapan_id', $tahapanSatu->id)
                ->where('jabatan_id', $request->jabatan_1_id)
                ->first();

            if ($tugas && isset($tugas->lampiran_tugas['form'])) {
                $skemaForm = $tugas->lampiran_tugas['form'];

                foreach ($skemaForm as $field) {
                    $label = $field['label'] ?? null;
                    if (!$label)
                        continue;

                    $fieldRules = [];
                    if (!empty($field['required'])) {
                        $fieldRules[] = 'required';
                    } else {
                        $fieldRules[] = 'nullable';
                    }

                    $tipeInput = $field['tipe'] ?? '';
                    if ($tipeInput === 'number') {
                        $fieldRules[] = 'numeric';
                    } elseif ($tipeInput === 'date') {
                        $fieldRules[] = 'date';
                    } elseif ($tipeInput === 'email') {
                        $fieldRules[] = 'email';
                    } else {
                        $fieldRules[] = 'string';
                    }

                    // Dinamis Validation Rule Array (Contoh: "dynamic_answers.No HP" => "required|numeric")
                    $rules["dynamic_answers.{$label}"] = implode('|', $fieldRules);
                    $customMessages["dynamic_answers.{$label}.required"] = "Kolom isian '{$label}' wajib Anda lengkapi.";
                    $customMessages["dynamic_answers.{$label}.numeric"] = "Isian untuk '{$label}' harus berupa angka murni.";
                    $customMessages["dynamic_answers.{$label}.date"] = "Format tanggal pada '{$label}' tidak valid.";
                    $customMessages["dynamic_answers.{$label}.email"] = "Alamat email pada '{$label}' tidak valid.";
                }
            }
        }

        // 3. Jalankan Validasi
        $request->validate($rules, $customMessages);

        $nimMahasiswa = Auth::user()->nim;

        // 4. Barikade Pendaftaran Ganda
        // Cek apakah nim ini sudah mendaftar di jabatan yang masuk dalam periode ini.
        $jabatanIdsPeriodeIni = Jabatan::where('periode_rekrutmen_id', $periode_id)->pluck('id');

        $sudahMendaftar = Pendaftaran::where('nim', $nimMahasiswa)
            ->whereIn('jabatan_1_id', $jabatanIdsPeriodeIni)
            ->exists();

        if ($sudahMendaftar) {
            return back()->with('error_server', 'Pendaftaran gagal. Anda sudah terdaftar di sistem rekrutmen organisasi ini.');
        }

        DB::beginTransaction();
        try {
            // 5. Simpan Data ke Tabel `pendaftaran`
            $pendaftaran = Pendaftaran::create([
                'nim' => $nimMahasiswa,
                'jabatan_1_id' => $request->jabatan_1_id,
                'jabatan_2_id' => $request->jabatan_2_id,
            ]);

            // 6. Proses Unggah Berkas Persyaratan
            $pathBerkas = null;
            if ($request->hasFile('file_berkas')) {
                $pathBerkas = $request->file('file_berkas')->store('rekrutmen/pendaftar/berkas', 'public');
            }

            // 7. Kemas Jawaban Kuesioner ke format Array untuk disimpan ke `pengumpulan_tugas`
            $lampiranJawaban = [];

            // Masukkan jawaban form dinamis
            if ($request->has('dynamic_answers') && is_array($request->dynamic_answers)) {
                $lampiranJawaban['form'] = $request->dynamic_answers;
            }

            // Masukkan path berkas yang diunggah
            if ($pathBerkas) {
                $lampiranJawaban['berkas'] = $pathBerkas;
            }

            // 8. Simpan ke Tabel `pengumpulan_tugas` jika ada tugas pada tahap 1 (Meski kosong, tetap buat relasinya jika diminta)
            if ($tugas && !empty($lampiranJawaban)) {
                PengumpulanTugas::create([
                    'tugas_id' => $tugas->id,
                    'pendaftaran_id' => $pendaftaran->id,
                    'lampiran_jawaban' => json_encode($lampiranJawaban) // Konversi ke JSON karena di database format JSON/Text
                ]);
            }

            DB::commit();

            return redirect()->route('mahasiswa.rekrutmen.index')
                ->with('success', 'Selamat! Pendaftaran berkas dan pengisian formulir Anda berhasil dikirim ke server.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error_server', 'Terjadi kendala internal database: ' . $e->getMessage() . ' (Baris: ' . $e->getLine() . ')');
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
}