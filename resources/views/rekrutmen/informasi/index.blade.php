<x-app-layout>
    <!-- Background Aksen Atas (Tetap dipertahankan untuk estetika luar) -->
    <div
        class="absolute top-0 inset-x-0 h-[400px] overflow-hidden pointer-events-none -z-10"
    >
        <div
            class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PHBhdGggZD0iTTAgMGg0MHY0MEgwVjB6bTIwIDIwaDIwdjIwSDIwaC0yMHptMCAwaC0yMHYtMjBoMjB2MjB6IiBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiIHN0cm9rZT0iI2YxZjVmOSIgc3Ryb2tlLXdpZHRoPSIxIi8+PC9zdmc+')] opacity-60"
        ></div>
        <div
            class="absolute -top-[20%] -left-[10%] w-[40%] h-[60%] rounded-full bg-gradient-to-br from-blue-300/80 to-blue-50/20 blur-[100px]"
        ></div>
        <div
            class="absolute top-[10%] right-[10%] w-[35%] h-[50%] rounded-full bg-gradient-to-bl from-indigo-200/60 to-transparent blur-[120px]"
        ></div>
    </div>

    <div class="p-4 sm:p-8 max-w-5xl mx-auto relative z-10 mt-6 sm:mt-10">
        <!-- HEADER SELARAS (Rounded-lg, Border Slate, Solid Background) -->
        <div class="rounded-lg mb-8 relative overflow-hidden">
            <div
                class="relative z-10 flex flex-col md:flex-row md:items-start justify-between gap-8"
            >
                <div>
                    <!-- Tipografi Solid Slate (Tanpa Gradien) agar seragam dengan form -->
                    <h2
                        class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-800 mb-2.5 leading-tight"
                    >
                        Update Informasi <br class="hidden sm:block" />
                        Rekrutmen
                    </h2>

                    <p class="text-sm font-normal text-slate-500 leading-relaxed">
                        Organisasi Mahasiswa
                        <span class="text-blue-600 font-bold tracking-wide"
                            >POLITEKNIK STATISTIKA STIS</span
                        >
                    </p>
                </div>

                <!-- Info Periode -->
                <div
                    class="bg-white backdrop-blur-sm border border-slate-200/80 px-6 py-4 rounded-lg shrink-0 flex flex-col md:items-start justify-betweenring-1 ring-slate-900/5 shadow-[0_2px_10px_rgb(0,0,0,0.02)]"
                >
                    <!-- Kontainer Flex untuk Titik Biru dan Teks -->
                    <div class="flex items-center justify-center gap-2 mb-1.5">
                        <span class="relative flex h-2 w-2">
                            <span
                                class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"
                            ></span>
                            <span
                                class="relative inline-flex rounded-full h-2 w-2 bg-blue-500"
                            ></span>
                        </span>
                        <p class="text-[11px] text-slate-500 font-bold uppercase tracking-widest">Periode Aktif</p>
                    </div>
                    <!-- Angka Tahun -->
                    <p class="text-2xl font-bold text-blue-600 tracking-tight">
                        {{ $periode->tahun_periode }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Konten Stepper dan Form di bawahnya... -->

        <!-- Lanjutkan dengan konten form di bawahnya... -->

        @php
            // 1. Siapkan Data Jabatan
            $jabatanResult = [];
            if (old('nama_jabatan')) {
                foreach (old('nama_jabatan') as $jName) {
                    $jabatanResult[] = ['nama' => $jName];
                }
            } elseif (isset($jabatanData) && $jabatanData->isNotEmpty()) {
                foreach ($jabatanData as $j) {
                    $jabatanResult[] = ['nama' => $j->nama_jabatan];
                }
            } else {
                $jabatanResult[] = ['nama' => ''];
            }

            // 2. Siapkan Data Tahapan & Tugas Multi-Kondisi
            $tahapanResult = [];
            if (old('tahapan')) {
                foreach (old('tahapan') as $tIndex => $t) {
                    $tugasList = [];
                    if (isset($t['tugas'])) {
                        foreach ($t['tugas'] as $tug) {
                            $tugasList[] = [
                                'nama_jabatan' => $tug['nama_jabatan'] ?? '',
                                'deskripsi_tugas' => $tug['deskripsi_tugas'] ?? '',
                                'tipe_jawaban_tugas' =>
                                    $tug['tipe_jawaban_tugas'] ?? 'file',
                                'tipe_tugas' => $tug['tipe_tugas'] ?? 'pengisian_form',
                                'format_proyek' => $tug['format_proyek'] ?? [],
                                'berkas_lama' => isset($tug['berkas_lama_json'])
                                    ? json_decode($tug['berkas_lama_json'], true)
                                    : [],
                                'skema_form' => isset($tug['skema_form_json'])
                                    ? json_decode($tug['skema_form_json'], true)
                                    : [],
                            ];
                        }
                    }

                    $tahapanResult[] = [
                        'nama_tahapan' => $t['nama_tahapan'] ?? '',
                        'deskripsi' => $t['deskripsi'] ?? '',
                        'tanggal_mulai' => $t['tanggal_mulai'] ?? '',
                        'tanggal_selesai' => $t['tanggal_selesai'] ?? '',
                        'file_lama' => $t['file_lama'] ?? '',
                        'metodeDistribusi' => $t['metode_distribusi'] ?? 'sama',
                        'tugasJabatan' => $tugasList,
                        'ada_tugas' => count($tugasList) > 0 || $tIndex === 0,
                        'is_pengumuman' =>
                            isset($t['is_pengumuman']) &&
                            ($t['is_pengumuman'] === 'true' || $t['is_pengumuman'] == 1),
                        'is_rentang_waktu' =>
                            isset($t['is_rentang_waktu']) &&
                            ($t['is_rentang_waktu'] === 'true' ||
                                $t['is_rentang_waktu'] == 1),
                    ];
                }
            } elseif (isset($tahapanData) && $tahapanData->isNotEmpty()) {
                foreach ($tahapanData as $tIndex => $t) {
                    $tugasList = [];
                    if ($t->tugas && $t->tugas->isNotEmpty()) {
                        foreach ($t->tugas as $tug) {
                            $lampiranTugasData = $tug->lampiran_tugas;
                            if (is_string($lampiranTugasData)) {
                                $lampiranTugasData =
                                    json_decode($lampiranTugasData, true) ?: [];
                            }

                            $berkasLama = $lampiranTugasData['berkas'] ?? [];
                            $skemaForm = $lampiranTugasData['form'] ?? [];

                            $formatProyek = [];
                            if (
                                in_array($tug->tipe_tugas, ['proyek', 'unggah_berkas']) &&
                                !empty($tug->tipe_jawaban_tugas)
                            ) {
                                $formatProyek = explode(',', $tug->tipe_jawaban_tugas);
                            }

                            $tugasList[] = [
                                'nama_jabatan' => $tug->nama_jabatan,
                                'deskripsi_tugas' => $tug->deskripsi_tugas,
                                'tipe_jawaban_tugas' => $tug->tipe_jawaban_tugas,
                                'tipe_tugas' => $tug->tipe_tugas ?? 'pengisian_form',
                                'format_proyek' => $formatProyek,
                                'berkas_lama' => $berkasLama,
                                'skema_form' => $skemaForm,
                            ];
                        }
                    }

                    $metodeDistribusi = 'sama';
                    if (count($tugasList) > 1) {
                        $firstTugas = $tugasList[0];
                        foreach ($tugasList as $idx => $tug) {
                            if ($idx === 0) {
                                continue;
                            }
                            if (
                                $tug['deskripsi_tugas'] !==
                                    $firstTugas['deskripsi_tugas'] ||
                                $tug['tipe_tugas'] !== $firstTugas['tipe_tugas'] ||
                                json_encode($tug['format_proyek']) !==
                                    json_encode($firstTugas['format_proyek']) ||
                                json_encode($tug['skema_form']) !==
                                    json_encode($firstTugas['skema_form'])
                            ) {
                                $metodeDistribusi = 'beda';
                                break;
                            }
                        }
                    }

                    $wMulai = $t->waktu_mulai
                        ? \Carbon\Carbon::parse($t->waktu_mulai)->format('Y-m-d\TH:i')
                        : '';
                    $wSelesai = $t->waktu_berakhir
                        ? \Carbon\Carbon::parse($t->waktu_berakhir)->format('Y-m-d\TH:i')
                        : '';

                    $isPengumuman =
                        $tIndex !== 0 && ($wMulai !== '' && $wMulai === $wSelesai);
                    $isRentang =
                        $wMulai !== '' && $wSelesai !== '' && $wMulai !== $wSelesai;

                    $tahapanResult[] = [
                        'nama_tahapan' => $t->nama_tahapan,
                        'deskripsi' => $t->deskripsi_tahapan ?? '',
                        'tanggal_mulai' => $wMulai,
                        'tanggal_selesai' => $wSelesai,
                        'file_lama' => is_array($t->lampiran_tahapan)
                            ? $t->lampiran_tahapan[0] ?? ''
                            : $t->lampiran_tahapan ?? '',
                        'metodeDistribusi' => $metodeDistribusi,
                        'tugasJabatan' => $tugasList,
                        'ada_tugas' => count($tugasList) > 0 || $tIndex === 0,
                        'is_pengumuman' => $isPengumuman,
                        'is_rentang_waktu' => $isRentang,
                    ];
                }
            } else {
                $tahapanResult[] = [
                    'nama_tahapan' => '',
                    'deskripsi' => '',
                    'tanggal_mulai' => '',
                    'tanggal_selesai' => '',
                    'file_lama' => '',
                    'metodeDistribusi' => 'sama',
                    'tugasJabatan' => [],
                    'ada_tugas' => true,
                    'is_pengumuman' => false,
                    'is_rentang_waktu' => false,
                ];
            }
        @endphp

        <div
            x-data="{
                
                tab: 1,
                errors: {},
                openFormBuilder: false,
                isWawancaraMode: false, 
                showFormSuccess: false, 
                activeTIndex: null,
                activeJIndex: null,
                activeJabatanName: '',
                currentFormSchema: [],
                activeTahapanIndex: 0,
                bannerFileName: '',
                pedomanFileName: '',
                
                // 1. STRUKTUR BARU (Grup Posisi -> Jabatan)
                // GANTI MENJADI INI (Gunakan kurung kurawal ganda biasa):
                listGroupPosisi: {{ json_encode($groupedJabatan) }},

                listTahapan: {{ json_encode($tahapanResult ?? []) }},

                init() {
                    // 2. Pantau perubahan pada struktur grup yang baru
                    this.$watch('listGroupPosisi', () => { this.autoSyncJabatan(); }, { deep: true });
                    this.autoSyncJabatan();
                },

                // 3. EMPAT FUNGSI KONTROL GRUP JABATAN
                tambahPosisi() {
                    this.listGroupPosisi.push({ posisi: '', jabatans: [{ nama: '' }] });
                },
                hapusPosisi(pIdx) {
                    this.listGroupPosisi.splice(pIdx, 1);
                },
                tambahJabatan(pIdx) {
                    this.listGroupPosisi[pIdx].jabatans.push({ nama: '' });
                },
                hapusJabatan(pIdx, jIdx) {
                    this.listGroupPosisi[pIdx].jabatans.splice(jIdx, 1);
                },

                // 4. ROMBAK TOTAL LOGIKA SINKRONISASI
                autoSyncJabatan() {
                    // Langkah A: Ekstrak semua jabatan dari dalam grup menjadi array lurus (Flat Array)
                    let flatJabatans = [];
                    this.listGroupPosisi.forEach(group => {
                        group.jabatans.forEach(jab => {
                            flatJabatans.push({
                                posisi: group.posisi,
                                nama: jab.nama
                            });
                        });
                    });

                    // Langkah B: Sinkronisasikan Flat Array ke dalam setiap Tahapan
                    this.listTahapan.forEach((tahapan) => {
                        let syncedTugas = flatJabatans.map((jabatan, indexFlat) => {
                            let existing = tahapan.tugasJabatan ? tahapan.tugasJabatan[indexFlat] : null;
                            
                            if (existing) {
                                // Pertahankan data form/berkas lama, hanya update namanya
                                existing.nama_jabatan = jabatan.nama; 
                                return existing;
                            }
                            
                            // Buat wadah tugas baru jika jabatannya baru ditambah
                            return { 
                                nama_jabatan: jabatan.nama, 
                                deskripsi_tugas: '', 
                                tipe_tugas: 'pengisian_form', 
                                format_proyek: [], 
                                berkas_lama: [], 
                                skema_form: [] 
                            };
                        });
                        tahapan.tugasJabatan = syncedTugas;
                    });
                },

                tambahTahapan() { 
                    this.listTahapan.push({ 
                        nama_tahapan: '', deskripsi: '', tanggal_mulai: '', tanggal_selesai: '', file_lama: '', 
                        metodeDistribusi: 'sama', tugasJabatan: [], ada_tugas: false, is_pengumuman: false, is_rentang_waktu: false 
                    }); 
                    this.activeTahapanIndex = this.listTahapan.length - 1;
                    this.autoSyncJabatan();
                },

                hapusTahapan(index) { 
                    this.listTahapan.splice(index, 1); 
                    if (this.activeTahapanIndex >= this.listTahapan.length) {
                        this.activeTahapanIndex = Math.max(0, this.listTahapan.length - 1);
                    }
                },

                simpanSkemaKeTugas() {
                    this.listTahapan[this.activeTIndex].tugasJabatan[this.activeJIndex].skema_form = this.currentFormSchema;
                    this.openFormBuilder = false;
                    this.showFormSuccess = true;
                    setTimeout(() => { this.showFormSuccess = false; }, 3000);
                },

                bukaBuilder(tIndex, jIndex, jabatanName, isWawancara) {
                    this.activeTIndex = tIndex;
                    this.activeJIndex = jIndex;
                    this.activeJabatanName = jabatanName;
                    this.isWawancaraMode = isWawancara; 
                    let existing = this.listTahapan[tIndex].tugasJabatan[jIndex].skema_form;
                    this.currentFormSchema = existing && existing.length > 0 ? JSON.parse(JSON.stringify(existing)) : [];
                    this.openFormBuilder = true;
                },

                validateField(fieldName, value, label) {
                    if (!value || value.toString().trim() === '') {
                        this.errors[fieldName] = ' wajib diisi!';
                    } else {
                        const isExempt = fieldName === 'slogan' || fieldName === 'deskripsi_rekrutmen' || fieldName.includes('tanggal') || fieldName.includes('nama_tahapan') || fieldName.includes('deskripsi_tahapan') || fieldName.includes('deskripsi_tugas');
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

                validateFile(fieldName, files, maxSizeMb, label, allowedExts = null) {
                    if (!files || files.length === 0) { 
                        delete this.errors[fieldName]; 
                        return; 
                    }
                    
                    const file = files[0];

                    if (file.size > maxSizeMb * 1024 * 1024) {
                        this.errors[fieldName] = `${label} tidak boleh lebih dari ${maxSizeMb} MB!`;
                        return; 
                    } 

                    if (allowedExts) {
                        const fileExt = file.name.split('.').pop().toLowerCase();
                        const allowedArray = allowedExts.split(',').map(e => e.trim().toLowerCase());
                        
                        if (!allowedArray.includes(fileExt)) {
                            this.errors[fieldName] = `Format tidak valid! ${label} hanya mendukung: ${allowedExts.toUpperCase()}`;
                            return;
                        }
                    }
                    delete this.errors[fieldName];
                },

                validateAllTimelines() {
                    let prevEnd = null;
                    for (let i = 0; i < this.listTahapan.length; i++) {
                        let t = this.listTahapan[i];
                        if (t.tanggal_mulai) {
                            if (prevEnd && new Date(t.tanggal_mulai) < new Date(prevEnd)) {
                                this.errors['tanggal_mulai_' + i] = '⚠️ Waktu mulai harus setelah tahapan sebelumnya selesai!';
                            } else {
                                if (this.errors['tanggal_mulai_' + i] && this.errors['tanggal_mulai_' + i].includes('tahapan sebelumnya')) {
                                    delete this.errors['tanggal_mulai_' + i];
                                }
                            }
                            prevEnd = (t.is_rentang_waktu && t.tanggal_selesai) ? t.tanggal_selesai : t.tanggal_mulai;
                        }
                    }
                }
            }"
        >
            <div class="mb-12 relative max-w-xl mx-auto">
                <!-- Garis Penghubung Background -->
                <div
                    class="absolute top-1/2 left-0 w-full h-1 bg-slate-200 -translate-y-1/2 rounded-full hidden sm:block"
                ></div>

                <!-- Garis Penghubung Progress (Biru) -->
                <div
                    class="absolute top-1/2 left-0 h-1 bg-blue-600 -translate-y-1/2 rounded-full transition-all duration-500 hidden sm:block"
                    :style="'width: ' + (tab - 1) * 50 + '%'"
                ></div>

                <div
                    class="relative flex flex-col sm:flex-row justify-between gap-4 sm:gap-0"
                >
                    <!-- Tab 1 -->
                    <button
                        type="button"
                        @click="
                            tab = 1;
                            window.scrollTo({ top: 0, behavior: 'smooth' });
                        "
                        class="relative flex items-center sm:flex-col sm:justify-center gap-3 sm:gap-2 group outline-none"
                    >
                        <div
                            :class="tab >= 1
                                ? 'bg-blue-600 text-white shadow-[0_0_15px_rgba(37,99,235,0.4)] border-blue-600'
                                : 'bg-white text-slate-400 border-slate-300 group-hover:border-blue-300'"
                            class="w-10 h-10 sm:w-12 sm:h-12 rounded-full border-2 flex items-center justify-center font-black text-sm sm:text-base transition-all duration-300 z-10"
                        >
                            <span x-show="tab === 1">1</span>
                            <svg x-show="tab > 1" class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <span
                            :class="tab === 1
                                ? 'text-blue-700 font-black'
                                : tab > 1
                                  ? 'text-slate-800 font-bold'
                                  : 'text-slate-500 font-bold'"
                            class="text-xs sm:text-[11px] uppercase tracking-widest sm:absolute sm:-bottom-8 sm:whitespace-nowrap transition-colors"
                        >
                            Identitas Rekrutmen
                        </span>
                    </button>

                    <!-- Tab 2 -->
                    <button
                        type="button"
                        @click="
                            tab = 2;
                            window.scrollTo({ top: 0, behavior: 'smooth' });
                        "
                        class="relative flex items-center sm:flex-col sm:justify-center gap-3 sm:gap-2 group outline-none"
                    >
                        <div
                            :class="tab >= 2
                                ? 'bg-blue-600 text-white shadow-[0_0_15px_rgba(37,99,235,0.4)] border-blue-600'
                                : 'bg-white text-slate-400 border-slate-300 group-hover:border-blue-300'"
                            class="w-10 h-10 sm:w-12 sm:h-12 rounded-full border-2 flex items-center justify-center font-black text-sm sm:text-base transition-all duration-300 z-10"
                        >
                            <span x-show="tab <= 2">2</span>
                            <svg x-show="tab > 2" class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <span
                            :class="tab === 2
                                ? 'text-blue-700 font-black'
                                : tab > 2
                                  ? 'text-slate-800 font-bold'
                                  : 'text-slate-500 font-bold'"
                            class="text-xs sm:text-[11px] uppercase tracking-widest sm:absolute sm:-bottom-8 sm:whitespace-nowrap transition-colors"
                        >
                            Formasi Jabatan
                        </span>
                    </button>

                    <!-- Tab 3 -->
                    <button
                        type="button"
                        @click="
                            tab = 3;
                            window.scrollTo({ top: 0, behavior: 'smooth' });
                        "
                        class="relative flex items-center sm:flex-col sm:justify-center gap-3 sm:gap-2 group outline-none"
                    >
                        <div
                            :class="tab === 3
                                ? 'bg-blue-600 text-white shadow-[0_0_15px_rgba(37,99,235,0.4)] border-blue-600'
                                : 'bg-white text-slate-400 border-slate-300 group-hover:border-blue-300'"
                            class="w-10 h-10 sm:w-12 sm:h-12 rounded-full border-2 flex items-center justify-center font-black text-sm sm:text-base transition-all duration-300 z-10"
                        >
                            <span>3</span>
                        </div>
                        <span
                            :class="tab === 3
                                ? 'text-blue-700 font-black'
                                : 'text-slate-500 font-bold'"
                            class="text-xs sm:text-[11px] uppercase tracking-widest sm:absolute sm:-bottom-8 sm:whitespace-nowrap transition-colors"
                        >
                            Tahapan Seleksi
                        </span>
                    </button>
                </div>
            </div>

            <!-- FORM UTAMA -->
            <form
                action="{{ route($routePrefix . 'rekrutmen.store_update', $periode->id) }}"
                method="POST"
                enctype="multipart/form-data"
                @submit="
                    if (Object.keys(errors).length > 0) {
                        $event.preventDefault();
                        Swal.fire({
                            icon: 'error',
                            title: 'Penyimpanan Tertahan',
                            text: 'Masih ada data yang belum lengkap atau formatnya salah. Silakan periksa kembali tanda peringatan merah pada formulir Anda!',
                            confirmButtonColor: '#2563eb',
                            customClass: {
                                popup: 'rounded-3xl',
                                confirmButton:
                                    'rounded-xl font-bold px-6 py-2.5',
                            },
                        });
                    }
                "
            >
                @csrf

                <div class="relative">
                    @include ('rekrutmen.informasi.tab-informasi')
                    @include ('rekrutmen.informasi.tab-jabatan')
                    @include ('rekrutmen.informasi.tab-tahapan')
                </div>
            </form>

            @include ('rekrutmen.informasi.form-builder')
        </div>
</x-app-layout>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Terapkan kustomisasi border-radius pada semua SweetAlert agar serasi dengan UI Enterprise kita
        const swalCustomClass = {
            popup: 'rounded-3xl',
            confirmButton: 'rounded-xl font-bold px-6 py-2.5 shadow-sm',
        };

        @if (session('error_server'))
        Swal.fire({
            icon: 'error',
            title: 'Sistem Terkendala',
            text: '{!!
        session(
            'error_server',
        )
    !!}',
            confirmButtonColor: '#2563eb',
            customClass: swalCustomClass,
        });
        @endif

        @if ($errors->any())
        Swal.fire({
            icon: 'warning',
            title: 'Validasi Server Gagal',
            html: `
                <div class="bg-red-50 p-4 rounded-xl border border-red-100 mt-2">
                    <ul class="text-left text-red-600 text-sm font-bold space-y-1.5">
                        @foreach ($errors->all() as $error)
                            <li class="flex items-start gap-2">
                                <span class="mt-0.5">•</span>
                                <span>{{ $error }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            `,
            confirmButtonColor: '#2563eb',
            customClass: swalCustomClass,
        });
        @endif

        @if (session('success_update'))
        Swal.fire({
            icon: 'success',
            title: 'Konfigurasi Disimpan!',
            text: '{!!
        session(
            'success_update',
        )
    !!}',
            confirmButtonColor: '#2563eb',
            customClass: swalCustomClass,
        });
        @endif
    });
</script>
