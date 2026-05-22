<x-app-layout>
    @if ($errors->any())
        <div
            class="mb-6 p-4 bg-red-50 border border-red-200 text-red-600 rounded-md text-sm"
        >
            <p class="font-bold mb-1">Terjadi kesalahan validasi input:</p>
            <ul class="list-disc pl-5 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('error_server'))
        <div
            class="mb-6 p-4 bg-red-100 border border-red-300 text-red-800 rounded-md text-sm font-semibold"
        >
            ❌ {{ session('error_server') }}
        </div>
    @endif

    <div class="p-8">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-800">
                Konfigurasi & Skema Alur Rekrutmen
            </h2>
            <p class="text-sm text-gray-500 mt-1">Periode Aktif: <span class="font-bold text-gray-700">{{ $periode->tahun_periode }}</span></p>
        </div>

        @php
            // =========================================================================
            // PERSIAPAN DATA ALPINE.JS (MENCEGAH RESET SAAT VALIDASI ERROR)
            // =========================================================================

            // 1. Siapkan Data Jabatan
            $jabatanResult = [];
            if (old('nama_jabatan')) {
                foreach (old('nama_jabatan') as $jName) {
                    $jabatanResult[] = ['nama' => $jName];
                }
            } elseif ($jabatanData && $jabatanData->isNotEmpty()) {
                foreach ($jabatanData as $j) {
                    $jabatanResult[] = ['nama' => $j->nama_jabatan];
                }
            } else {
                $jabatanResult[] = ['nama' => ''];
            }

            // 2. Siapkan Data Tahapan & Tugas
            $tahapanResult = [];
            if (old('tahapan')) {
                foreach (old('tahapan') as $tIndex => $t) {
                    $dbT = isset($tahapanData[$tIndex]) ? $tahapanData[$tIndex] : null;
                    $fileLama = '';
                    if ($dbT) {
                        $fileLama = is_array($dbT->lampiran_tahapan)
                            ? $dbT->lampiran_tahapan[0] ?? ''
                            : $dbT->lampiran_tahapan ?? '';
                    }

                    $tugasList = [];
                    if (isset($t['tugas'])) {
                        foreach ($t['tugas'] as $tug) {
                            $tugasList[] = [
                                'nama_jabatan' => $tug['nama_jabatan'] ?? '',
                                'deskripsi_tugas' => $tug['deskripsi_tugas'] ?? '',
                                'tipe_jawaban_tugas' =>
                                    $tug['tipe_jawaban_tugas'] ?? 'file',
                                'lampiran_tugas' => isset($tug['lampiran_tugas'])
                                    ? (is_string($tug['lampiran_tugas'])
                                        ? json_decode($tug['lampiran_tugas'], true)
                                        : $tug['lampiran_tugas'])
                                    : [],
                            ];
                        }
                    }

                    $tahapanResult[] = [
                        'nama_tahapan' => $t['nama_tahapan'] ?? '',
                        'deskripsi' => $t['deskripsi'] ?? '',
                        'tanggal_mulai' => $t['tanggal_mulai'] ?? '',
                        'tanggal_selesai' => $t['tanggal_selesai'] ?? '',
                        'file_lama' => $fileLama,
                        'metodeDistribusi' => $t['metodeDistribusi'] ?? 'sama', // Default ke "sama" saat error validasi
                        'tugasJabatan' => $tugasList,
                    ];
                }
            } elseif ($tahapanData && $tahapanData->isNotEmpty()) {
                foreach ($tahapanData as $t) {
                    $tugasList = [];
                    if ($t->tugas && $t->tugas->isNotEmpty()) {
                        foreach ($t->tugas as $tug) {
                            // Ambil data lampiran berkas/skema form dari database
                            $lampiranTugasData = $tug->lampiran_tugas;
                            if (is_string($lampiranTugasData)) {
                                $lampiranTugasData =
                                    json_decode($lampiranTugasData, true) ?: [];
                            }

                            $tugasList[] = [
                                'nama_jabatan' => $tug->nama_jabatan,
                                'deskripsi_tugas' => $tug->deskripsi_tugas,
                                'tipe_jawaban_tugas' => $tug->tipe_jawaban_tugas,
                                // WAJIB DIMASUKKAN AGAR ALPINE.JS MENGETAHUI DATA LAMA
                                'lampiran_tugas' => $lampiranTugasData ?: [],
                            ];
                        }
                    }
                    $tahapanResult[] = [
                        'nama_tahapan' => $t->nama_tahapan,
                        'deskripsi' => $t->deskripsi_tahapan ?? '',
                        'tanggal_mulai' => $t->waktu_mulai
                            ? \Carbon\Carbon::parse($t->waktu_mulai)->format('Y-m-d\TH:i')
                            : '',
                        'tanggal_selesai' => $t->waktu_berakhir
                            ? \Carbon\Carbon::parse($t->waktu_berakhir)->format(
                                'Y-m-d\TH:i',
                            )
                            : '',
                        'file_lama' => is_array($t->lampiran_tahapan)
                            ? $t->lampiran_tahapan[0] ?? ''
                            : $t->lampiran_tahapan ?? '',
                        'metodeDistribusi' => 'sama',
                        'tugasJabatan' => $tugasList,
                    ];
                }
            } else {
                $tahapanResult[] = [
                    'nama_tahapan' => '',
                    'deskripsi' => '',
                    'tanggal_mulai' => '',
                    'tanggal_selesai' => '',
                    'file_lama' => '',
                    'metodeDistribusi' => 'sama', // Default murni data kosong
                    'tugasJabatan' => [],
                ];
            }
        @endphp

        <div
            x-data="{
                tab: 1,
                errors: {},

                openFormBuilder: false,
                activeTIndex: null,
                activeJIndex: null,
                activeJabatanName: '',
                currentFormSchema: [],

                // Fungsi untuk membuka jepretan modal popup Form Builder
                bukaBuilder(tIndex, jIndex, jabatanName) {
                    this.activeTIndex = tIndex;
                    this.activeJIndex = jIndex;
                    this.activeJabatanName = jabatanName;
                    
                    // Ambil skema yang tersimpan di baris tugas terkait
                    let existing = this.listTahapan[tIndex].tugasJabatan[jIndex].lampiran_tugas;
                    
                    // Jaring Pengaman XSS/Reset: Jika data lama terbaca sebagai string, konversi ke Array
                    if (typeof existing === 'string') {
                        try { 
                            existing = JSON.parse(existing); 
                        } catch(e) { 
                            existing = []; 
                        }
                    }
                    
                    // Salinkan data lama ke dalam form builder modal
                    this.currentFormSchema = existing && existing.length > 0 ? JSON.parse(JSON.stringify(existing)) : [];
                    this.openFormBuilder = true;
                },

                // Fungsi untuk menyimpan kembali skema ke state Alpine utama sebelum disubmit
                simpanSkemaKeTugas() {
                    this.listTahapan[this.activeTIndex].tugasJabatan[this.activeJIndex].lampiran_tugas = this.currentFormSchema;
                    this.openFormBuilder = false;
                },

                // VALIDATOR UTAMA (ON BLUR)
                validateField(fieldName, value, label) {
                    if (!value || value.toString().trim() === '') {
                        this.errors[fieldName] = label + ' wajib diisi!';
                    } else {
                        const isExempt = fieldName === 'slogan' || fieldName === 'deskripsi_rekrutmen' || fieldName.includes('tanggal');
                        
                        if (!isExempt) {
                            const alphaNumericSpace = /^[a-zA-Z0-9 ]+$/;
                            if (!alphaNumericSpace.test(value.toString().trim())) {
                                this.errors[fieldName] = label + ' hanya boleh berisi huruf, angka, dan spasi!';
                                return;
                            }
                        }
                        delete this.errors[fieldName];
                    }
                },

                // VALIDATOR UKURAN BERKAS (ON CHANGE)
                validateFile(fieldName, files, maxSizeMb, label) {
                    if (!files || files.length === 0) {
                        delete this.errors[fieldName];
                        return;
                    }
                    const file = files[0];
                    const maxSizeBytes = maxSizeMb * 1024 * 1024;

                    if (file.size > maxSizeBytes) {
                        this.errors[fieldName] = `${label} tidak boleh lebih dari ${maxSizeMb} MB!`;
                    } else {
                        delete this.errors[fieldName];
                    }
                },
                
                listJabatan: {{ json_encode($jabatanResult) }},
                listTahapan: {{ json_encode($tahapanResult) }},

                tambahJabatan() { this.listJabatan.push({ nama: '' }); },
                hapusJabatan(index) { this.listJabatan.splice(index, 1); },
                
                tambahTahapan() { 
                    this.listTahapan.push({ nama_tahapan: '', deskripsi: '', tanggal_mulai: '', tanggal_selesai: '', file_lama: '', metodeDistribusi: 'sama', tugasJabatan: [] }); 
                },
                hapusTahapan(index) { this.listTahapan.splice(index, 1); },

                syncTugasKeJabatan(tahapanIndex) {
                    this.listTahapan[tahapanIndex].tugasJabatan = this.listJabatan.map(j => {
                        return { nama_jabatan: j.nama, deskripsi_tugas: '', tipe_jawaban_tugas: 'file', lampiran_tugas: [] };
                    });
                }
            }"
            class="max-w-5xl"
        >
            <div
                class="flex border-b border-gray-200 mb-6 bg-white rounded-t-lg"
            >
                <button
                    type="button"
                    @click="tab = 1"
                    :class="tab === 1
                        ? 'border-blue-600 text-blue-600 font-bold bg-blue-50/50'
                        : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="w-1/2 py-4 px-1 text-center border-b-2 font-medium text-sm transition-all focus:outline-none flex items-center justify-center gap-2"
                >
                    <span
                        class="inline-flex items-center justify-center w-5 h-5 text-xs font-bold rounded-full"
                        :class="tab === 1
                            ? 'bg-blue-600 text-white'
                            : 'bg-gray-200 text-gray-600'"
                        >1</span
                    >
                    Informasi Umum & Jabatan
                </button>
                <button
                    type="button"
                    @click="tab = 2"
                    :class="tab === 2
                        ? 'border-blue-600 text-blue-600 font-bold bg-blue-50/50'
                        : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="w-1/2 py-4 px-1 text-center border-b-2 font-medium text-sm transition-all focus:outline-none flex items-center justify-center gap-2"
                >
                    <span
                        class="inline-flex items-center justify-center w-5 h-5 text-xs font-bold rounded-full"
                        :class="tab === 2
                            ? 'bg-blue-600 text-white'
                            : 'bg-gray-200 text-gray-600'"
                        >2</span
                    >
                    Alur Tahapan Seleksi & Tugas
                </button>
            </div>

            <form
                action="{{ route('organisasi.rekrutmen.store_update', $periode->id) }}"
                method="POST"
                enctype="multipart/form-data"
                class="space-y-6"
            >
                @csrf
                @include ('organisasi.rekrutmen-saat-ini.partials.tab-informasi')

                @include ('organisasi.rekrutmen-saat-ini.partials.tab-tahapan')
            </form>
            @include ('organisasi.rekrutmen-saat-ini.form-builder')
        </div>
    </div>
</x-app-layout>
