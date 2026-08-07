<x-app-layout>
    @php
        $tahapanInfo = $tahapanSatu ?? null;
        $namaTahapan = $tahapanInfo->nama_tahapan ?? 'Pendaftaran & Seleksi Berkas';
        $deskripsiUmum =
            $tahapanInfo->deskripsi_tahapan ??
            'Silakan lengkapi formulir pendaftaran dan unggah berkas yang diminta.';

        // Parsing waktu untuk kebutuhan display penanda
        $rawMulai =
            $tahapanInfo && $tahapanInfo->waktu_mulai
                ? \Carbon\Carbon::parse($tahapanInfo->waktu_mulai)
                : null;
        $rawBerakhir =
            $tahapanInfo && $tahapanInfo->waktu_berakhir
                ? \Carbon\Carbon::parse($tahapanInfo->waktu_berakhir)
                : null;
        $isWaktuTunggal =
            $rawMulai && $rawBerakhir ? $rawMulai->equalTo($rawBerakhir) : false;

        $waktuMulaiStr = $rawMulai ? $rawMulai->translatedFormat('d M Y H:i') : '-';
        $waktuSelesaiStr = $rawBerakhir
            ? $rawBerakhir->translatedFormat('d M Y H:i')
            : 'Tanpa Tenggat';

        $tugasMapping = [];
        if ($tahapanInfo && $tahapanInfo->tugas) {
            foreach ($tahapanInfo->tugas as $tugas) {
                $lampiranData = $tugas->lampiran_tugas;
                $lampiran = is_string($lampiranData)
                    ? json_decode($lampiranData, true)
                    : $lampiranData;

                $tugasMapping[$tugas->jabatan_id] = [
                    'tipe_tugas' => $tugas->tipe_tugas ?? 'pengisian_form',
                    'deskripsi' => $tugas->deskripsi_tugas ?: $deskripsiUmum,
                    'format_proyek' => !empty($tugas->tipe_jawaban_tugas)
                        ? explode(',', $tugas->tipe_jawaban_tugas)
                        : [],
                    'berkas_template' => $lampiran['berkas'] ?? [],
                    'form' => $lampiran['form'] ?? [],
                ];
            }
        }

        $organisasi = $rekrutmen->organisasi;
        $namaOrganisasi = $organisasi->nama_organisasi ?? 'Organisasi';

        $avatarUrl = '';
        if ($organisasi) {
            if (!empty($organisasi->avatar_google)) {
                $avatarUrl = str_replace(
                    'http://',
                    'https://',
                    $organisasi->avatar_google,
                );
            } elseif (!empty($organisasi->lampiran_logo)) {
                $avatarUrl = asset('storage/' . $organisasi->lampiran_logo);
            }
        }

        $bannerData = $rekrutmen->lampiran_banner;
        $bannerArray = is_string($bannerData)
            ? json_decode($bannerData, true)
            : $bannerData;
        $bannerPath =
            is_array($bannerArray) && count($bannerArray) > 0 ? $bannerArray[0] : null;

        $groupedJabatan = [];
        if (isset($jabatans)) {
            foreach ($jabatans as $jabatan) {
                $namaPosisi =
                    empty($jabatan->nama_posisi) || $jabatan->nama_posisi === '-'
                        ? 'Tanpa Divisi Khusus'
                        : $jabatan->nama_posisi;
                if (!isset($groupedJabatan[$namaPosisi])) {
                    $groupedJabatan[$namaPosisi] = [];
                }
                $groupedJabatan[$namaPosisi][] = [
                    'id' => $jabatan->id,
                    'nama_jabatan' => $jabatan->nama_jabatan,
                ];
            }
        }
    @endphp

    <!-- Menyimpan JSON dengan aman untuk Alpine.js -->
    <script>
        window.tugasMappingData = {!!
            json_encode(
                $tugasMapping,
            )
        !!};
    </script>

    <!-- Background Aksen Atas -->
    <div
        class="absolute top-0 inset-x-0 h-[320px] sm:h-[400px] overflow-hidden pointer-events-none -z-10 bg-slate-900"
    >
        @if ($bannerPath)
            <img
                src="{{ asset('storage/' . $bannerPath) }}"
                alt="Banner"
                class="absolute inset-0 w-full h-full object-cover opacity-20 mix-blend-luminosity"
            />
        @else
            <div class="absolute inset-0 opacity-10">
                <svg class="w-full h-full" xmlns="http://www.w3.org/2000/svg">
                    <defs>
                        <pattern id="form-grid" width="32" height="32" patternUnits="userSpaceOnUse">
                            <path d="M 32 0 L 0 0 0 32" fill="none" stroke="currentColor" class="text-white" stroke-width="1" />
                        </pattern>
                    </defs>
                    <rect width="100%" height="100%" fill="url(#form-grid)" />
                </svg>
            </div>
        @endif
        <div
            class="absolute -top-[20%] -left-[10%] w-[40%] h-[60%] rounded-full bg-blue-600/20 blur-[100px]"
        ></div>
        <div
            class="absolute top-[10%] right-[10%] w-[35%] h-[50%] rounded-full bg-indigo-600/20 blur-[120px]"
        ></div>
        <div
            class="absolute inset-0 bg-gradient-to-t from-slate-50 via-slate-900/60 to-transparent"
        ></div>
    </div>

    <div
        x-data="{ 
            tab: 1,
            pilihan1: '{{ old('jabatan_1_id') }}',
            pilihan2: '{{ old('jabatan_2_id') }}',
            pilihan1Name: '',
            activePosisi1: '',
            activePosisi2: '',
            tugasMap: window.tugasMappingData,
            currentTugas: null,
            
            init() {
                this.$watch('pilihan1', (value) => {
                    if (this.pilihan2 === value) {
                        this.pilihan2 = '';
                    }
                    this.updatePilihan1Name();
                });
                if(this.pilihan1) this.updatePilihan1Name();
            },

            updatePilihan1Name() {
                if(!this.pilihan1) return;
                // Timeout memastikan DOM telah ter-render agar querySelector menemukan opsi yang tepat
                setTimeout(() => {
                    const selectedRadio = document.querySelector(`input[name='radio_pilihan1'][value='${this.pilihan1}']`);
                    if(selectedRadio) {
                        this.pilihan1Name = selectedRadio.dataset.name;
                    }
                }, 50);
            },
            
            lanjutKeTugas() {
                if (!this.pilihan1) {
                    Swal.fire({ 
                        icon: 'warning', 
                        title: 'Pilih Formasi Utama', 
                        text: 'Pilihan 1 (Prioritas Utama) wajib diisi sebelum melanjutkan.', 
                        confirmButtonColor: '#2563eb',
                        customClass: { popup: 'rounded-lg border border-slate-200 shadow-sm font-sans', confirmButton: 'px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm font-bold' }
                    });
                    return;
                }

                if (this.pilihan2 && this.pilihan1 === this.pilihan2) {
                    Swal.fire({ 
                        icon: 'error', 
                        title: 'Pilihan Ganda', 
                        text: 'Pilihan 1 dan Pilihan 2 tidak boleh sama. Pilih formasi alternatif yang berbeda.', 
                        confirmButtonColor: '#2563eb',
                        customClass: { popup: 'rounded-lg border border-slate-200 shadow-sm font-sans', confirmButton: 'px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm font-bold' }
                    });
                    return;
                }
                
                this.updatePilihan1Name();
                
                this.currentTugas = this.tugasMap[this.pilihan1] || {
                    tipe_tugas: 'pengisian_form', deskripsi: '{{ $deskripsiUmum }}', format_proyek: [], berkas_template: [], form: []
                };

                this.tab = 2;
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        }"
        class="relative z-10 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 w-full pt-10 pb-24"
    >
        <!-- Navigasi Pintas -->
        <nav
            class="inline-flex items-center gap-2 px-3 py-1.5 rounded-md bg-slate-800 border border-slate-700 text-slate-300 text-xs font-bold mb-6 hover:bg-slate-700 transition-colors shadow-sm"
        >
            <a
                href="{{ route('mahasiswa.rekrutmen.info', $rekrutmen->id) }}"
                class="flex items-center gap-1.5 hover:text-white transition-colors"
            >
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali ke Detail
            </a>
        </nav>

        <!-- Header Profil -->
        <div
            class="flex flex-col md:flex-row items-center md:items-start gap-5 text-center md:text-left mb-10"
        >
            <div class="shrink-0">
                @if (!empty($avatarUrl))
                    <img
                        src="{{ $avatarUrl }}"
                        alt="Logo"
                        class="w-16 h-16 md:w-20 md:h-20 rounded-full object-cover bg-white p-0.5 border border-slate-200 shadow-sm"
                    />
                @else
                    <div
                        class="w-16 h-16 md:w-20 md:h-20 rounded-full bg-blue-600 text-white flex items-center justify-center text-2xl font-black uppercase border border-blue-700 shadow-sm"
                    >
                        {{
                            substr(
                                $namaOrganisasi,
                                0,
                                1,
                            )
                        }}
                    </div>
                @endif
            </div>
            <div class="flex-1 mt-2 md:mt-0">
                <h1
                    class="text-3xl md:text-4xl font-extrabold text-white tracking-tight leading-tight mb-1.5"
                >
                    Formulir Pendaftaran
                </h1>
                <p class="text-sm font-medium text-slate-300">
                    {{ $namaOrganisasi }} &bull; {{
                        $rekrutmen->slogan ??
                            'Penerimaan Anggota Baru'
                    }}
                </p>
            </div>
        </div>

        <!-- PENGENDALI TAB (STEPPER FLAT DESIGN) -->
        <div class="max-w-2xl mx-auto relative z-30 mb-8 mt-4">
            <div
                class="bg-white rounded-lg border border-slate-200 p-2 shadow-sm flex flex-col sm:flex-row items-center justify-center gap-2 font-bold text-xs"
            >
                <button
                    type="button"
                    @click="
                        tab = 1;
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    "
                    :class="tab === 1
                        ? 'bg-blue-600 text-white border-blue-700 shadow-sm'
                        : 'text-slate-600 hover:bg-slate-50 border-transparent'"
                    class="w-full sm:flex-1 px-5 py-2.5 rounded-md transition-colors flex items-center justify-center gap-2.5 border"
                >
                    <span
                        class="w-5 h-5 rounded-md flex items-center justify-center text-[10px] border"
                        :class="tab === 1
                            ? 'border-white bg-white/20'
                            : 'border-slate-300'"
                        >1</span
                    >
                    Pilih Formasi
                </button>
                <span class="text-slate-300 font-normal hidden sm:block"
                    >&mdash;</span
                >
                <button
                    type="button"
                    @click="lanjutKeTugas()"
                    :class="tab === 2
                        ? 'bg-blue-600 text-white border-blue-700 shadow-sm'
                        : 'text-slate-600 hover:bg-slate-50 border-transparent'"
                    class="w-full sm:flex-1 px-5 py-2.5 rounded-md transition-colors flex items-center justify-center gap-2.5 border"
                >
                    <span
                        class="w-5 h-5 rounded-md flex items-center justify-center text-[10px] border"
                        :class="tab === 2
                            ? 'border-white bg-white/20'
                            : 'border-slate-300'"
                        >2</span
                    >
                    Lembar Tugas
                </button>
            </div>
        </div>

        <!-- AREA FORMULIR -->
        <form
            id="formPendaftaran"
            action="{{ route('mahasiswa.rekrutmen.submit', $rekrutmen->id) }}"
            method="POST"
            enctype="multipart/form-data"
        >
            @csrf

            <!-- TAB 1: PILIHAN FORMASI -->
            <div
                x-show="tab === 1"
                x-transition:enter="transition ease-out duration-300 transform"
                x-transition:enter-start="opacity-0 translate-y-4"
                class="space-y-6"
            >
                <div
                    class="bg-white rounded-lg border border-slate-200 p-6 md:p-8 shadow-sm"
                >
                    <div
                        class="mb-8 border-b border-slate-200 pb-5 flex flex-col md:flex-row md:items-end justify-between gap-5"
                    >
                        <div class="flex-1">
                            <div
                                class="inline-flex items-center gap-2 bg-slate-100 text-slate-700 px-3 py-1 rounded-md text-[10px] font-bold uppercase tracking-widest mb-3 border border-slate-200"
                            >
                                <span
                                    class="w-1.5 h-1.5 rounded-full bg-blue-500"
                                ></span>
                                Tahap Utama
                            </div>
                            <h2
                                class="text-xl font-extrabold text-slate-800 mb-2"
                            >
                                {{ $namaTahapan }}
                            </h2>
                            <p class="text-sm font-medium text-slate-500">{{ $deskripsiUmum }}</p>
                        </div>
                        <div
                            class="bg-slate-50 border border-slate-200 p-4 rounded-lg shrink-0 text-left md:text-right shadow-sm w-full md:w-auto"
                        >
                            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">Tenggat Waktu</p>
                            <p class="text-sm font-extrabold text-slate-800 flex items-center md:justify-end gap-1.5">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                @if ($isWaktuTunggal && $rawMulai)
                                    {{
                                        $rawMulai->translatedFormat(
                                            'd M Y',
                                        )
                                    }}
                                @elseif ($rawMulai && $rawBerakhir)
                                    {{
                                        $rawBerakhir->translatedFormat(
                                            'd M Y',
                                        )
                                    }}
                                @else
                                    Tanpa Batas Waktu
                                @endif
                            </p>
                        </div>
                    </div>

                    @if (empty($groupedJabatan))
                        <div
                            class="p-4 bg-red-50 text-red-700 rounded-md border border-red-200 font-bold text-center text-sm shadow-sm"
                        >
                            Mohon maaf, formasi pilihan jabatan belum ditentukan
                            oleh panitia pelaksana.
                        </div>
                    @else
                        <!-- PILIHAN 1 (WAJIB) -->
                        <div class="mb-10">
                            <h3
                                class="text-sm font-extrabold text-slate-800 mb-3 flex items-center gap-2"
                            >
                                <div
                                    class="w-1.5 h-3.5 bg-blue-600 rounded-full"
                                ></div>
                                Pilihan Utama (Wajib)
                            </h3>

                            <div
                                class="border border-slate-200 rounded-lg overflow-hidden flex flex-col md:flex-row bg-slate-50"
                            >
                                <!-- Kiri: Divisi -->
                                <div
                                    class="w-full md:w-5/12 bg-white border-b md:border-b-0 md:border-r border-slate-200 p-4"
                                    style="max-height: 280px; overflow-y: auto"
                                >
                                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-3">Daftar Divisi:</p>
                                    <div class="space-y-1.5">
                                        @foreach ($groupedJabatan as $namaPosisi => $jabatansGroup)
                                            <button
                                                type="button"
                                                @click="activePosisi1 = '{{ $namaPosisi }}'"
                                                :class="activePosisi1 === '{{ $namaPosisi }}' ? 'bg-slate-800 text-white shadow-sm' : 'bg-transparent text-slate-600 hover:bg-slate-100'"
                                                class="w-full text-left px-3.5 py-2.5 rounded-md text-xs font-bold transition-all flex justify-between items-center"
                                            >
                                                {{ $namaPosisi }}
                                                <span
                                                    x-show="activePosisi1 === '{{ $namaPosisi }}'"
                                                    >&rarr;</span
                                                >
                                            </button>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- Kanan: Jabatan -->
                                <div class="w-full md:w-7/12 p-5 bg-slate-50">
                                    <input
                                        type="hidden"
                                        name="jabatan_1_id"
                                        x-model="pilihan1"
                                        required
                                    />

                                    <div
                                        x-show="!activePosisi1"
                                        class="h-full flex items-center justify-center text-center p-4"
                                    >
                                        <p class="text-xs font-bold text-slate-400">Pilih divisi di panel sebelah kiri.</p>
                                    </div>

                                    @foreach ($groupedJabatan as $namaPosisi => $jabatansGroup)
                                        <div
                                            x-show="activePosisi1 === '{{ $namaPosisi }}'"
                                            style="display: none"
                                            class="space-y-3"
                                        >
                                            <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Pilih Jabatan:</p>

                                            @foreach ($jabatansGroup as $jabatan)
                                                <label
                                                    class="flex items-center p-3.5 bg-white border rounded-md cursor-pointer transition-colors shadow-sm"
                                                    :class="pilihan1 === '{{ $jabatan['id'] }}' ? 'border-blue-600 bg-blue-50' : 'border-slate-200 hover:border-blue-300'"
                                                >
                                                    <div
                                                        class="flex items-center justify-between w-full gap-3"
                                                    >
                                                        <div
                                                            class="flex items-center gap-3"
                                                        >
                                                            <div
                                                                class="w-4 h-4 rounded-full border flex items-center justify-center shrink-0 bg-white"
                                                                :class="pilihan1 === '{{ $jabatan['id'] }}' ? 'border-blue-600' : 'border-slate-300'"
                                                            >
                                                                <div
                                                                    class="w-2 h-2 bg-blue-600 rounded-full"
                                                                    x-show="pilihan1 === '{{ $jabatan['id'] }}'"
                                                                ></div>
                                                            </div>
                                                            <span
                                                                class="text-xs font-bold transition-colors"
                                                                :class="pilihan1 === '{{ $jabatan['id'] }}' ? 'text-blue-800' : 'text-slate-700'"
                                                            >
                                                                {{
                                                                    $jabatan[
                                                                        'nama_jabatan'
                                                                    ]
                                                                }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <input
                                                        type="radio"
                                                        name="radio_pilihan1"
                                                        value="{{ $jabatan['id'] }}"
                                                        data-name="{{ $jabatan['nama_jabatan'] }}"
                                                        @click="pilihan1 = '{{ $jabatan['id'] }}'; if(pilihan2 === '{{ $jabatan['id'] }}') pilihan2 = ''; updatePilihan1Name();"
                                                        class="hidden"
                                                    />
                                                </label>
                                            @endforeach
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <!-- PILIHAN 2 (OPSIONAL) -->
                        <div class="mb-6">
                            <h3
                                class="text-sm font-extrabold text-slate-800 mb-3 flex items-center justify-between"
                            >
                                <span class="flex items-center gap-2">
                                    <div
                                        class="w-1.5 h-3.5 bg-slate-300 rounded-full"
                                    ></div>
                                    Pilihan Alternatif (Opsional)
                                </span>
                                <button
                                    type="button"
                                    @click="
                                        pilihan2 = '';
                                        activePosisi2 = '';
                                    "
                                    x-show="pilihan2"
                                    class="text-[10px] font-bold uppercase tracking-wider text-red-600 hover:text-red-800 bg-red-50 hover:bg-red-100 px-2 py-1 rounded transition-colors"
                                    style="display: none"
                                >
                                    Batal Pilih
                                </button>
                            </h3>

                            <div
                                class="border border-slate-200 rounded-lg overflow-hidden flex flex-col md:flex-row bg-slate-50"
                            >
                                <!-- Kiri: Divisi -->
                                <div
                                    class="w-full md:w-5/12 bg-white border-b md:border-b-0 md:border-r border-slate-200 p-4"
                                    style="max-height: 280px; overflow-y: auto"
                                >
                                    <div class="space-y-1.5">
                                        @foreach ($groupedJabatan as $namaPosisi => $jabatansGroup)
                                            <button
                                                type="button"
                                                @click="activePosisi2 = '{{ $namaPosisi }}'"
                                                :class="activePosisi2 === '{{ $namaPosisi }}' ? 'bg-slate-200 text-slate-800 shadow-sm' : 'bg-transparent text-slate-600 hover:bg-slate-100'"
                                                class="w-full text-left px-3.5 py-2.5 rounded-md text-xs font-bold transition-all flex justify-between items-center"
                                            >
                                                {{ $namaPosisi }}
                                                <span
                                                    x-show="activePosisi2 === '{{ $namaPosisi }}'"
                                                    >&rarr;</span
                                                >
                                            </button>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- Kanan: Jabatan -->
                                <div class="w-full md:w-7/12 p-5 bg-slate-50">
                                    <input
                                        type="hidden"
                                        name="jabatan_2_id"
                                        x-model="pilihan2"
                                    />

                                    <div
                                        x-show="!activePosisi2"
                                        class="h-full flex items-center justify-center text-center p-4"
                                    >
                                        <p class="text-xs font-bold text-slate-400">Pilih divisi di panel sebelah kiri.</p>
                                    </div>

                                    @foreach ($groupedJabatan as $namaPosisi => $jabatansGroup)
                                        <div
                                            x-show="activePosisi2 === '{{ $namaPosisi }}'"
                                            style="display: none"
                                            class="space-y-3"
                                        >
                                            @foreach ($jabatansGroup as $jabatan)
                                                <label
                                                    class="flex items-center p-3.5 bg-white border rounded-md transition-colors shadow-sm"
                                                    :class="pilihan1 === '{{ $jabatan['id'] }}' ? 'border-slate-100 bg-slate-50 cursor-not-allowed opacity-50' : (pilihan2 === '{{ $jabatan['id'] }}' ? 'border-slate-800 bg-slate-100 cursor-pointer' : 'border-slate-200 hover:border-slate-300 cursor-pointer')"
                                                >
                                                    <div
                                                        class="flex items-center justify-between w-full gap-3"
                                                    >
                                                        <div
                                                            class="flex items-center gap-3"
                                                        >
                                                            <div
                                                                class="w-4 h-4 rounded-full border flex items-center justify-center shrink-0 bg-white"
                                                                :class="pilihan2 === '{{ $jabatan['id'] }}' ? 'border-slate-800' : 'border-slate-300'"
                                                            >
                                                                <div
                                                                    class="w-2 h-2 bg-slate-800 rounded-full"
                                                                    x-show="pilihan2 === '{{ $jabatan['id'] }}'"
                                                                ></div>
                                                            </div>
                                                            <span
                                                                class="text-xs font-bold"
                                                                :class="pilihan1 === '{{ $jabatan['id'] }}' ? 'text-slate-400' : 'text-slate-700'"
                                                            >
                                                                {{
                                                                    $jabatan[
                                                                        'nama_jabatan'
                                                                    ]
                                                                }}
                                                            </span>
                                                        </div>
                                                        <span
                                                            x-show="pilihan1 === '{{ $jabatan['id'] }}'"
                                                            class="text-[9px] font-bold text-red-500 bg-red-50 px-2 py-1 rounded uppercase tracking-wide"
                                                        >
                                                            Terpilih Utama
                                                        </span>
                                                    </div>
                                                    <input
                                                        type="radio"
                                                        name="radio_pilihan2"
                                                        value="{{ $jabatan['id'] }}"
                                                        @click="if(pilihan1 !== '{{ $jabatan['id'] }}') pilihan2 = '{{ $jabatan['id'] }}'"
                                                        :disabled="pilihan1 === '{{ $jabatan['id'] }}'"
                                                        class="hidden"
                                                    />
                                                </label>
                                            @endforeach
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                    <div
                        class="mt-8 pt-6 border-t border-slate-200 flex justify-end"
                    >
                        <button
                            type="button"
                            @click="lanjutKeTugas()"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-md font-bold text-sm transition-colors w-full md:w-auto shadow-sm"
                        >
                            Lanjut Isi Lembar Penugasan &rarr;
                        </button>
                    </div>
                </div>
            </div>

            <!-- TAB 2: LEMBAR TUGAS -->
            <div
                x-show="tab === 2"
                style="display: none"
                x-transition:enter="transition ease-out duration-300 transform"
                x-transition:enter-start="opacity-0 translate-y-4"
            >
                <div
                    class="bg-white rounded-lg border border-slate-200 shadow-sm overflow-hidden"
                >
                    <!-- Header Tab 2 -->
                    <div
                        class="px-6 py-5 border-b border-slate-200 bg-slate-50 flex items-center justify-between"
                    >
                        <h2
                            class="text-sm font-bold text-slate-800 uppercase tracking-wide flex items-center gap-2"
                        >
                            <div
                                class="w-1.5 h-3.5 bg-blue-600 rounded-full"
                            ></div>
                            Lembar Kerja Penugasan:
                            <span
                                class="text-blue-700 ml-1"
                                x-text="pilihan1Name"
                            ></span>
                        </h2>
                    </div>

                    <div class="p-6 md:p-8 space-y-8">
                        <!-- Instruksi Box -->
                        <div
                            class="bg-slate-50 border border-slate-200 rounded-lg p-5"
                        >
                            <h4
                                class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3"
                            >
                                Instruksi & Kriteria Penugasan
                            </h4>
                            <p
                                class="text-sm text-slate-700 font-normal whitespace-pre-line leading-relaxed"
                                x-text="currentTugas?.deskripsi"
                            ></p>

                            <!-- Lampiran Template (Jika Ada) -->
                            <template
                                x-if="
                                    currentTugas?.berkas_template &&
                                    currentTugas.berkas_template.length > 0
                                "
                            >
                                <div
                                    class="mt-6 pt-5 border-t border-slate-200"
                                >
                                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3">Lampiran Dokumen Pendukung:</p>
                                    <div class="flex flex-wrap gap-2.5">
                                        <template
                                            x-for="
                                                (berkas, bIdx) in
                                                currentTugas.berkas_template
                                            "
                                            :key="bIdx"
                                        >
                                            <a
                                                :href="'/storage/' + berkas"
                                                target="_blank"
                                                class="inline-flex items-center gap-2 text-xs font-bold text-blue-700 bg-white hover:bg-blue-50 px-4 py-2 rounded-md border border-slate-300 transition-colors shadow-sm"
                                            >
                                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                                Unduh Berkas Lampiran
                                                <span x-text="bIdx + 1"></span>
                                            </a>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <!-- Area Dynamic Form -->
                        <template
                            x-if="
                                currentTugas?.form &&
                                currentTugas.form.length > 0
                            "
                        >
                            <div class="space-y-6 pt-2">
                                <h3
                                    class="text-base font-extrabold text-slate-800 border-b border-slate-200 pb-3 flex items-center gap-2"
                                >
                                    <div
                                        class="w-1.5 h-3.5 bg-blue-600 rounded-full"
                                    ></div>
                                    Formulir Isian Formasi
                                </h3>

                                <div class="grid grid-cols-1 gap-6">
                                    <template
                                        x-for="
                                            (field, fIdx) in currentTugas.form
                                        "
                                        :key="fIdx"
                                    >
                                        <div class="space-y-0.5">
                                            <label
                                                class="block text-sm font-bold text-slate-700"
                                            >
                                                <span
                                                    x-text="field.label"
                                                ></span>
                                                <span
                                                    x-show="field.required"
                                                    class="text-red-500 ml-0.5"
                                                    >*</span
                                                >
                                            </label>
                                            <p
                                                x-show="field.keterangan"
                                                x-text="field.keterangan"
                                                class="pb-2 text-xs font-normal text-slate-500"
                                            ></p>
                                            <!-- Text / Number / Email / Date -->
                                            <template
                                                x-if="
                                                    [
                                                        'text_short',
                                                        'text',
                                                        'number',
                                                        'date',
                                                        'email',
                                                    ].includes(field.tipe)
                                                "
                                            >
                                                <input
                                                    :type="field.tipe ===
                                                        'text_short' ||
                                                    field.tipe === 'text'
                                                        ? 'text'
                                                        : field.tipe"
                                                    :name="`dynamic_answers[${field.label}]`"
                                                    :required="field.required"
                                                    class="w-full rounded-md text-sm border border-slate-300 focus:border-blue-600 focus:ring-0 bg-white py-2.5 transition-colors shadow-sm"
                                                />
                                            </template>

                                            <!-- Textarea -->
                                            <template
                                                x-if="
                                                    [
                                                        'text_long',
                                                        'textarea',
                                                        'long_text',
                                                    ].includes(field.tipe)
                                                "
                                            >
                                                <textarea
                                                    :name="`dynamic_answers[${field.label}]`"
                                                    :required="field.required"
                                                    rows="4"
                                                    class="w-full rounded-md text-sm border border-slate-300 focus:border-blue-600 focus:ring-0 bg-white py-2.5 transition-colors shadow-sm resize-y"
                                                ></textarea>
                                            </template>

                                            <!-- Dropdown / Select -->
                                            <template
                                                x-if="
                                                    [
                                                        'dropdown',
                                                        'select',
                                                    ].includes(field.tipe)
                                                "
                                            >
                                                <select
                                                    :name="`dynamic_answers[${field.label}]`"
                                                    :required="field.required"
                                                    class="w-full rounded-md text-sm border border-slate-300 focus:border-blue-600 focus:ring-0 bg-white py-2.5 transition-colors shadow-sm"
                                                >
                                                    <option value="">
                                                        -- Pilih Salah Satu --
                                                    </option>
                                                    <template
                                                        x-for="
                                                            (opt, oIdx) in
                                                            field.options
                                                        "
                                                        :key="oIdx"
                                                    >
                                                        <template
                                                            x-if="
                                                                opt &&
                                                                opt.trim() !==
                                                                    ''
                                                            "
                                                        >
                                                            <option
                                                                :value="opt"
                                                                x-text="opt"
                                                            ></option>
                                                        </template>
                                                    </template>
                                                </select>
                                            </template>

                                            <!-- Radio / Checkbox group -->
                                            <template
                                                x-if="
                                                    field.tipe === 'radio' ||
                                                    field.tipe === 'checkbox'
                                                "
                                            >
                                                <div
                                                    class="flex flex-col sm:flex-row gap-3 pt-1 flex-wrap"
                                                >
                                                    <template
                                                        x-for="
                                                            (opt, oIdx) in
                                                            field.options
                                                        "
                                                        :key="oIdx"
                                                    >
                                                        <template
                                                            x-if="
                                                                opt &&
                                                                opt.trim() !==
                                                                    ''
                                                            "
                                                        >
                                                            <label
                                                                class="flex items-center cursor-pointer p-3.5 bg-slate-50 rounded-md border border-slate-200 hover:bg-slate-100 hover:border-slate-300 transition-colors w-full sm:w-auto min-w-[140px] shadow-sm"
                                                            >
                                                                <input
                                                                    :type="field.tipe"
                                                                    :name="field.tipe ===
                                                                    'checkbox'
                                                                        ? `dynamic_answers[${field.label}][]`
                                                                        : `dynamic_answers[${field.label}]`"
                                                                    :value="opt"
                                                                    :required="field.tipe ===
                                                                    'radio'
                                                                        ? field.required
                                                                        : false"
                                                                    class="text-blue-600 border-slate-300 focus:ring-0 mr-3"
                                                                    :class="field.tipe ===
                                                                    'checkbox'
                                                                        ? 'rounded'
                                                                        : 'rounded-full'"
                                                                />
                                                                <span
                                                                    class="text-sm font-bold text-slate-700"
                                                                    x-text="opt"
                                                                ></span>
                                                            </label>
                                                        </template>
                                                    </template>
                                                </div>
                                            </template>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>

                        <!-- Area Unggah File Khusus -->
                        <template
                            x-if="
                                !currentTugas?.form ||
                                currentTugas.form.length === 0 ||
                                currentTugas?.tipe_tugas === 'unggah_berkas'
                            "
                        >
                            <div
                                class="space-y-4 pt-4 border-t border-slate-200"
                            >
                                <label
                                    class="block text-base font-extrabold text-slate-800 flex items-center gap-2"
                                >
                                    <div
                                        class="w-1.5 h-3.5 bg-blue-600 rounded-full"
                                    ></div>
                                    Unggah Berkas Persyaratan / Portofolio Kerja
                                </label>

                                <div
                                    class="flex flex-col items-center justify-center p-8 border-2 border-dashed border-slate-300 rounded-lg bg-slate-50 hover:bg-slate-100 hover:border-blue-400 transition-colors"
                                >
                                    <svg class="mx-auto h-12 w-12 text-slate-400 mb-3" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>

                                    <label
                                        for="file_berkas"
                                        class="relative cursor-pointer rounded-md font-bold text-blue-600 hover:text-blue-800 text-sm"
                                    >
                                        <span
                                            >Klik untuk Memilih File
                                            Penugasan</span
                                        >
                                        <input
                                            id="file_berkas"
                                            name="file_berkas"
                                            type="file"
                                            class="sr-only"
                                            :required="!currentTugas?.form ||
                                            currentTugas.form.length === 0"
                                        />
                                    </label>
                                    <p class="text-xs font-normal text-slate-500 mt-2" x-text="currentTugas?.format_proyek && currentTugas.format_proyek.length > 0 ? 'Ekstensi: ' + currentTugas.format_proyek.join(', ') : 'Format yang didukung (Maksimal 5MB)'"></p>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Footer Actions -->
                    <div
                        class="px-6 md:px-8 py-5 border-t border-slate-200 bg-slate-50 flex flex-col-reverse sm:flex-row items-center justify-between gap-4"
                    >
                        <button
                            type="button"
                            @click="
                                tab = 1;
                                window.scrollTo({ top: 0, behavior: 'smooth' });
                            "
                            class="text-sm font-bold text-slate-600 hover:text-slate-900 transition-colors w-full sm:w-auto text-center"
                        >
                            &larr; Kembali ke Formasi
                        </button>

                        <button
                            type="button"
                            @click="
                                let form =
                                    document.getElementById('formPendaftaran');
                                document
                                    .querySelectorAll(
                                        '.loader, #loader, #preloader, [class*=\'memuat\']',
                                    )
                                    .forEach(
                                        (el) => (el.style.display = 'none'),
                                    );

                                if (form.reportValidity()) {
                                    Swal.fire({
                                        icon: 'question',
                                        title: 'Konfirmasi Pendaftaran',
                                        text: 'Apakah Anda yakin seluruh berkas dan isian form sudah benar? Pilihan formasi prioritas dan jawaban berkas tidak dapat diubah kembali setelah dikirimkan.',
                                        showCancelButton: true,
                                        confirmButtonText: 'Ya, Kirim Sekarang',
                                        cancelButtonText: 'Periksa Kembali',
                                        confirmButtonColor: '#2563eb',
                                        cancelButtonColor: '#64748b',
                                        reverseButtons: true,
                                        customClass: {
                                            popup: 'rounded-lg shadow-sm border border-slate-200 font-sans p-6',
                                            title: 'text-xl font-extrabold text-slate-800',
                                            htmlContainer:
                                                'text-sm font-normal text-slate-500 mt-2',
                                            confirmButton:
                                                'px-5 py-2.5 rounded-md font-bold text-sm text-white mx-1 bg-blue-600 hover:bg-blue-700',
                                            cancelButton:
                                                'px-5 py-2.5 rounded-md font-bold text-sm text-white mx-1 bg-slate-500 hover:bg-slate-600',
                                        },
                                        buttonsStyling: false,
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            Swal.fire({
                                                title: 'Memproses Pengiriman...',
                                                text: 'Sedang mendaftarkan berkas Anda ke pangkalan data server.',
                                                allowOutsideClick: false,
                                                didOpen: () => {
                                                    Swal.showLoading();
                                                },
                                                customClass: {
                                                    popup: 'rounded-lg font-sans',
                                                },
                                            });
                                            form.submit();
                                        }
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'warning',
                                        title: 'Data Belum Lengkap',
                                        text: 'Pastikan seluruh input wajib bertanda bintang merah (*) telah diisi dengan format berkas yang sesuai ketentuan.',
                                        confirmButtonColor: '#2563eb',
                                        customClass: {
                                            popup: 'rounded-lg border border-slate-200 shadow-sm font-sans',
                                            confirmButton:
                                                'px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm font-bold',
                                        },
                                    });
                                }
                            "
                            class="bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm px-8 py-3 rounded-md transition-colors w-full sm:w-auto shadow-sm"
                        >
                            Kirim & Finalisasi
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const premiumSwal = Swal.mixin({
            customClass: {
                popup: 'rounded-lg shadow-sm border border-slate-200 font-sans p-6',
                title: 'text-xl font-extrabold text-slate-800 tracking-tight',
                htmlContainer: 'text-sm font-normal text-slate-500 mt-2',
                confirmButton:
                    'px-6 py-2.5 rounded-md font-bold text-sm bg-blue-600 hover:bg-blue-700 text-white transition-colors',
            },
            buttonsStyling: false,
        });

        @if ($errors->any() || session('error_server'))
        setTimeout(() => {
            document
                .querySelectorAll('.loader, #loader, #preloader, [class*="memuat"]')
                .forEach((el) => (el.style.display = 'none'));
            document.body.classList.remove('overflow-hidden');
        }, 100);
        @endif

        @if ($errors->any())
        premiumSwal.fire({
            icon: 'error',
            title: 'Pendaftaran Tertunda',
            html: `
                <div class="text-sm text-red-600 text-left mt-2">
                    <p class="font-bold mb-2">Server menolak data karena kesalahan validasi:</p>
                    <ul class="list-disc pl-5 font-medium space-y-1 text-xs text-red-700">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            `,
            customClass: {
                popup: 'rounded-lg shadow-sm border border-slate-200 font-sans p-6',
                title: 'text-xl font-extrabold text-slate-800 tracking-tight',
                confirmButton:
                    'px-6 py-2.5 rounded-md font-bold text-sm bg-red-600 hover:bg-red-700 text-white transition-colors',
            },
        });
        @endif

        @if (session('error_server'))
        premiumSwal.fire({
            icon: 'error',
            title: 'Gagal Memproses',
            text: '{{
        session(
            'error_server',
        )
    }}',
            customClass: {
                popup: 'rounded-lg shadow-sm border border-slate-200 font-sans p-6',
                title: 'text-xl font-extrabold text-slate-800 tracking-tight',
                htmlContainer: 'text-sm font-normal text-slate-500 mt-2',
                confirmButton:
                    'px-6 py-2.5 rounded-md font-bold text-sm bg-red-600 hover:bg-red-700 text-white transition-colors',
            },
        });
        @endif
    });
</script>
