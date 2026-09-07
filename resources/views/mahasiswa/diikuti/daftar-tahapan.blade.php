@php
    $isRiwayatMahasiswa = $isRiwayatMahasiswa ?? false;
@endphp

<x-app-layout>
    <!-- Wrapper Alpine.js untuk Modal -->
    <div
        x-data="{
            tugasModalOpen: false,
            tugasAktif: null,
            tugasSudahDikumpul: false,
            tugasDapatDiedit: false,
            berkasHapusSaatRevisi: [],
            pendaftaranId: '{{ $pendaftaran->id }}',
            urlKirimTugas: '{{ route('mahasiswa.rekrutmen.diikuti.tugas_submit', ['pendaftaran' => '__PENDAFTARAN__', 'tugas' => '__TUGAS__']) }}',
            urlKehadiran: '{{ route('mahasiswa.rekrutmen.diikuti.wawancara_hadir', ['pendaftaran' => '__PENDAFTARAN__', 'tugas' => '__TUGAS__']) }}',
            urlDetailTugas: '{{ route($routeDetailTugas, ['pendaftaran' => '__PENDAFTARAN__', 'tugas' => '__TUGAS__']) }}',

            bukaModal(tugas, statusKumpul, dapatDiedit) {
                this.tugasAktif = tugas;
                this.tugasSudahDikumpul = statusKumpul;
                this.tugasDapatDiedit = dapatDiedit;
                this.berkasHapusSaatRevisi = [];
                this.tugasModalOpen = true;
                document.body.style.overflow = 'hidden';
            },
            tutupModal() {
                this.tugasModalOpen = false;
                this.berkasHapusSaatRevisi = [];
                setTimeout(() => { this.tugasAktif = null; }, 300);
                document.body.style.overflow = '';
            },
            parseBerkasLama(jsonStr) {
                try {
                    let obj = typeof jsonStr === 'string' ? JSON.parse(jsonStr) : jsonStr;
                    if(typeof obj === 'string') obj = JSON.parse(obj);
                    return obj.berkas || [];
                } catch(e) {
                    return [];
                }
            },
            berkasJawaban(tugas) {
                return tugas?.pengumpulan_mahasiswa?.lampiran_jawaban?.berkas || [];
            },
            buatUrl(template, tugasId) {
                return template
                    .replace('__PENDAFTARAN__', this.pendaftaranId)
                    .replace('__TUGAS__', tugasId);
            }
        }"
        @keydown.escape.window="tutupModal()"
    >
        <!-- Background Aksen Atas (Diselaraskan) -->
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

        <!-- MAIN CONTAINER (Padding responsif selaras) -->
        <div
            class="py-4 sm:py-8 px-4 sm:px-8 md:px-10 max-w-5xl mx-auto relative z-10 my-6 sm:my-8"
        >
            <!-- HEADER SELARAS -->
            <div
                class="mb-8 sm:mb-10 relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-5 text-center sm:text-left"
            >
                <div>
                    <h2
                        class="text-3xl sm:text-4xl font-extrabold text-slate-800 tracking-tight leading-tight"
                    >
                        {{
                            $isRiwayatMahasiswa
                                ? 'Riwayat Tahapan Seleksi'
                                : 'Jadwal Tahapan Seleksi'
                        }}<br />
                    </h2>
                    <p class="text-sm text-blue-700 font-extrabold mt-2 leading-relaxed">
                        {{ $namaOrganisasi }}
                    </p>
                    <p class="text-sm text-slate-500 leading-relaxed">
                        {{
                            $isRiwayatMahasiswa
                                ? 'Lihat kembali tahapan dan penugasan yang pernah Anda kirimkan pada rekrutmen ini.'
                                : 'Informasi tahapan seleksi rekrutmen serta penugasan yang diberikan dapat melalui halaman ini.'
                        }}
                    </p>
                </div>

                <!-- Info Periode -->
                <div
                    class="w-full sm:w-auto bg-white backdrop-blur-sm border border-slate-200/80 px-6 py-4 rounded-xl shrink-0 flex flex-col items-center sm:items-start justify-between shadow-sm"
                >
                    <div class="flex items-center justify-center gap-2 mb-1.5">
                        <span class="relative flex h-2 w-2">
                            <span
                                class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"
                            ></span>
                            <span
                                class="relative inline-flex rounded-full h-2 w-2 bg-blue-500"
                            ></span>
                        </span>
                        <p class="text-[11px] font-bold uppercase tracking-widest text-slate-500">Periode</p>
                    </div>
                    <p class="text-2xl font-extrabold tracking-tight text-blue-600">
                        {{
                            $pendaftaran->pilihanJabatan1?->periode
                                ?->tahun_periode ?? '-'
                        }}
                    </p>
                </div>
            </div>

            <!-- KONTEN UTAMA (Format Card & Padding disamakan) -->
            <div
                class="bg-white px-5 sm:px-10 py-6 sm:py-10 rounded-xl shadow-sm border border-slate-200"
            >
                <!-- Judul Card Logo / Jabatan -->
                <div
                    class="border-b border-slate-100 mb-8 pb-5 flex flex-col md:flex-row md:items-end justify-between gap-4"
                >
                    <div
                        class="flex items-center gap-4 text-center sm:text-left"
                    >
                        <div class="shrink-0">
                            @if (!empty($avatarUrl))
                                <img
                                    src="{{ $avatarUrl }}"
                                    alt="Logo"
                                    class="w-16 h-16 sm:w-20 sm:h-20 rounded-full object-contain bg-white p-0.5 border border-slate-200 shadow-sm"
                                    referrerpolicy="no-referrer"
                                    onerror="
                                        this.style.display = 'none';
                                        document.getElementById(
                                            'header-fallback-logo',
                                        ).style.display = 'flex';
                                    "
                                />
                                <div
                                    id="header-fallback-logo"
                                    style="display: none"
                                    class="w-16 h-16 sm:w-20 sm:h-20 rounded-full bg-blue-600 text-white items-center justify-center text-xl sm:text-2xl font-black uppercase border border-blue-700 shadow-sm"
                                >
                                    {{
                                        substr(
                                            $namaOrganisasi,
                                            0,
                                            1,
                                        )
                                    }}
                                </div>
                            @else
                                <div
                                    class="w-16 h-16 sm:w-20 sm:h-20 rounded-full bg-blue-600 text-white flex items-center justify-center text-xl sm:text-2xl font-black uppercase border border-blue-700 shadow-sm"
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

                        <div>
                            <p class="text-lg font-extrabold text-slate-500">{{ $namaPosisiUtama }}</p>
                            <h2
                                class="flex flex-wrap items-center gap-3 text-xl font-extrabold uppercase leading-tight tracking-tight text-slate-800 md:text-2xl"
                            >
                                <span>{{ $namaJabatanUtama }}</span>
                                <span
                                    class="inline px-2 py-0.5 bg-blue-100 text-blue-700 border border-blue-200 rounded text-[9px] font-extrabold uppercase tracking-widest shadow-sm leading-none"
                                >
                                    Pilihan Utama
                                </span>
                            </h2>
                        </div>
                    </div>
                </div>

                <!-- TIMELINE TAHAPAN -->
                <div class="relative w-full overflow-hidden">
                    @foreach ($tahapans as $index => $tahapan)
                        <div
                            class="mb-6 flex justify-between items-start w-full relative group"
                        >
                            <!-- Garis Vertikal Timeline -->
                            @if (!$loop->last)
                                <div
                                    class="absolute border-l-2 border-dashed border-slate-200 h-full ml-[1.4rem] left-0 top-12 -bottom-6"
                                ></div>
                            @endif

                            <!-- Lingkaran Status Timeline -->
                            <div
                                class="relative z-10 w-12 h-12 rounded-full shrink-0 flex items-center justify-center font-bold shadow-sm border-white border-4 {{ $tahapan->is_past ? 'bg-blue-100 text-blue-600 ring-2 ring-blue-50' : ($tahapan->is_active ? 'bg-blue-600 text-white ring-2 ring-blue-50' : 'bg-slate-100 text-slate-400 border-2 border-slate-200') }}"
                            >
                                @if ($tahapan->is_past)
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
                                @elseif ($tahapan->is_active)
                                    <span
                                        class="block w-2 h-2 bg-white rounded-full animate-pulse"
                                    ></span>
                                @else
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 0 0 2-2v-6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2Zm10-10V7a4 4 0 0 0-8 0v4h8Z" /></svg>
                                @endif
                            </div>

                            <!-- Card Konten Tahapan -->
                            <div class="w-full flex-1 mb-2 ml-5 sm:ml-7">
                                <div
                                    class="bg-white rounded-xl border transition-colors duration-300 {{ $tahapan->is_active ? 'border-blue-300 shadow-md ring-1 ring-blue-50' : 'border-slate-200 shadow-sm' }}"
                                >
                                    <!-- Header Tahapan Card -->
                                    <div
                                        class="px-4 sm:px-5 py-3 sm:py-4 border-b border-slate-100 {{ $tahapan->is_active ? 'bg-blue-50' : 'bg-slate-50' }} rounded-t-xl flex flex-col sm:flex-row sm:items-center justify-between gap-3"
                                    >
                                        <div class="flex-1">
                                            <h3
                                                class="text-lg font-extrabold {{ $tahapan->is_past ? 'text-slate-700' : 'text-slate-800' }} leading-tight flex items-center gap-2"
                                            >
                                                {{ $tahapan->urutan_tahapan }}. {{ $tahapan->nama_tahapan }}
                                                @if ($tahapan->is_active)
                                                    <span
                                                        class="px-2 py-0.5 rounded text-[9px] font-extrabold uppercase bg-blue-100 text-blue-700 tracking-wider"
                                                    >
                                                        Berlangsung
                                                    </span>
                                                @endif
                                            </h3>
                                        </div>
                                        <div
                                            class="shrink-0 text-left sm:text-right"
                                        >
                                            @if ($tahapan->is_waktu_tunggal)
                                                <p class="text-xs font-bold text-slate-600">
                                                    {{
                                                        $tahapan->parsed_mulai->translatedFormat(
                                                            'd M Y',
                                                        )
                                                    }}
                                                </p>
                                                <p class="text-[10px] font-bold text-slate-400 mt-0.5">
                                                    {{
                                                        $tahapan->parsed_mulai->format(
                                                            'H:i',
                                                        )
                                                    }} WIB
                                                </p>
                                            @else
                                                <p class="text-xs font-bold text-slate-600 flex items-center gap-2">
                                                    <span class="flex flex-col">
                                                        <span>{{
                                                            $tahapan->parsed_mulai->translatedFormat(
                                                                'd M Y',
                                                            )
                                                        }}</span>
                                                        <span
                                                            class="text-[10px] text-slate-400 mt-0.5"
                                                            >{{
                                                                $tahapan->parsed_mulai->format(
                                                                    'H:i',
                                                                )
                                                            }} WIB</span
                                                        >
                                                    </span>
                                                    <span class="text-slate-300"
                                                        >&mdash;</span
                                                    >
                                                    <span class="flex flex-col">
                                                        <span>{{
                                                            $tahapan->parsed_berakhir->translatedFormat(
                                                                'd M Y',
                                                            )
                                                        }}</span>
                                                        <span
                                                            class="text-[10px] text-slate-400 mt-0.5"
                                                            >{{
                                                                $tahapan->parsed_berakhir->format(
                                                                    'H:i',
                                                                )
                                                            }} WIB</span
                                                        >
                                                    </span>
                                                </p>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Body Tahapan Card -->
                                    <div class="p-4 sm:p-5 space-y-4">
                                        @if ($tahapan->is_future)
                                            <div
                                                class="flex items-center gap-2 rounded-md border border-slate-200 bg-slate-50 p-3"
                                            >
                                                <svg class="h-4 w-4 shrink-0 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 0 0 2-2v-6a2 2 0 0 0-2-2H6a2 2 0 0 1-2-2V7a4 4 0 0 1 8 0v4" /></svg>
                                                <p class="text-sm font-bold text-slate-500">
                                                    {{
                                                        $tahapan->jenis_tahapan === 'pengumuman'
                                                            ? 'Pengumuman akan tersedia saat tahapan dimulai.'
                                                            : 'Informasi penugasan akan tersedia saat tahapan dimulai.'
                                                    }}
                                                </p>
                                            </div>
                                        @elseif ($tahapan->is_dinyatakan_gagal)
                                            <div
                                                class="flex items-center gap-2 p-3 bg-red-50 border border-red-200 rounded-md"
                                            >
                                                <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                                <p class="text-sm font-bold text-red-700">Anda tidak dinyatakan lulus.</p>
                                            </div>
                                        @elseif ($tahapan->dikunci_karena_tidak_lulus)
                                            <button
                                                type="button"
                                                disabled
                                                class="flex w-full items-center justify-between rounded-lg border border-slate-200 bg-slate-100 px-4 py-2.5 text-xs font-bold text-slate-400 cursor-not-allowed"
                                            >
                                                <span>Tahapan terkunci</span>
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 0 0 2-2v-6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2zm10-10V7a4 4 0 0 0-8 0v4h8z"></path></svg>
                                            </button>
                                        @else
                                            @if ($tahapan->deskripsi_tahapan)
                                                <p class="text-sm text-slate-600 leading-relaxed font-medium">
                                                    {{ $tahapan->deskripsi_tahapan }}
                                                </p>
                                            @endif
                                            @if ($tahapan->pedoman_path)
                                                <a
                                                    href="{{ asset('storage/' . $tahapan->pedoman_path) }}"
                                                    target="_blank"
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white hover:bg-slate-50 border border-slate-300 text-[11px] font-bold text-slate-700 rounded-md shadow-sm transition-colors w-fit"
                                                >
                                                    <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                                    {{
                                                        $tahapan->jenis_tahapan === 'pengumuman'
                                                            ? 'Unduh Pengumuman'
                                                            : 'Unduh Panduan Tahapan'
                                                    }}
                                                </a>
                                            @endif
                                            @if ($tahapan->tugas->isNotEmpty())
                                                <div class="space-y-3 pt-2">
                                                    @foreach ($tahapan->tugas as $tugas)
                                                        @php $sudahDikumpul = in_array($tugas->id, $tugasDikumpulkan ?? []); @endphp
                                                        <div class="space-y-3">
                                                            <div
                                                                class="flex justify-between border-t border-slate-100 pt-4 items-center"
                                                            >
                                                                <div>
                                                                    <h4
                                                                        class="text-[10px] font-extrabold text-slate-600 flex items-center gap-1.5 uppercase tracking-wide"
                                                                    >
                                                                        <svg class="w-3 h-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                                                                        Tugas
                                                                    </h4>
                                                                </div>

                                                                @if ($sudahDikumpul)
                                                                    <div>
                                                                        <span
                                                                            class="px-2 py-1 rounded text-[9px] font-extrabold uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center gap-1 w-min"
                                                                        >
                                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                                                            Diserahkan
                                                                        </span>
                                                                    </div>
                                                                @elseif ($tahapan->is_past)
                                                                    <div>
                                                                        <span
                                                                            class="px-2 py-1 rounded text-[9px] font-extrabold uppercase tracking-wider bg-red-50 text-red-700 border border-red-200 flex items-center gap-1 w-max"
                                                                        >
                                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18 18 6M6 6l12 12"></path></svg>
                                                                            Tidak
                                                                            Diserahkan
                                                                        </span>
                                                                    </div>
                                                                @endif
                                                            </div>

                                                            @if ($tahapan->is_active || ($tahapan->is_past && $sudahDikumpul))
                                                                @if ($tugas->tipe_jawaban_tugas === 'form' ||
                                                                    $tugas->tipe_tugas === 'pengisian_form')
                                                                    <a
                                                                        href="{{ route($routeDetailTugas, ['pendaftaran' => $pendaftaran->id, 'tugas' => $tugas->id]) }}"
                                                                        class="w-full flex justify-center gap-2 items-center py-3 px-5 {{ $tahapan->is_past ? 'bg-blue-50 hover:bg-blue-100/80 text-blue-600 hover:text-blue-700 border border-blue-300'  : 'bg-blue-600 hover:bg-blue-700 text-white' }}  shadow-sm hover:shadow-md text-xs font-bold rounded-lg transition-all"
                                                                    >
                                                                        <span>
                                                                            {{
                                                                                $tahapan->is_past
                                                                                    ? 'Lihat
                                                                                                                                                            Tugas
                                                                                                                                                            Terkirim'
                                                                                    : ($isRiwayatMahasiswa
                                                                                        ? 'Lihat
                                                                                                                                                            Tugas
                                                                                                                                                            Terkirim'
                                                                                        : 'Kerjakan Tugas')
                                                                            }}</span
                                                                        >
                                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                                                    </a>
                                                                @else
                                                                    <button
                                                                        type="button"
                                                                        @click="bukaModal(JSON.parse(atob('{{ base64_encode(json_encode($tugas)) }}')), {{ $sudahDikumpul ? 'true' : 'false' }}, {{ $tahapan->is_active ? 'true' : 'false' }})"
                                                                        class="w-full flex justify-center gap-2 items-center py-3 px-5 {{ $tahapan->is_past ? 'bg-blue-50 hover:bg-blue-100/80 text-blue-600 hover:text-blue-700 border border-blue-300'  : 'bg-blue-600 hover:bg-blue-700 text-white' }}  shadow-sm hover:shadow-md text-xs font-bold rounded-lg transition-all"
                                                                    >
                                                                        <span>{{
                                                                            $tahapan->is_past
                                                                                ? 'Lihat Tugas Terkirim'
                                                                                : 'Kerjakan Tugas'
                                                                        }}</span>
                                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                                                    </button>
                                                                @endif
                                                            @elseif ($tahapan->is_future)
                                                                <button
                                                                    disabled
                                                                    class="w-full flex justify-between items-center py-2.5 px-4 bg-slate-50 border border-slate-200 text-slate-400 text-xs font-bold rounded-lg cursor-not-allowed shadow-sm"
                                                                >
                                                                    <span
                                                                        >Terkunci</span
                                                                    >
                                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                                                </button>
                                                            @elseif ($tahapan->is_past)
                                                                <button
                                                                    disabled
                                                                    class="w-full flex justify-between items-center py-2.5 px-4 bg-slate-100 border border-slate-200 text-slate-400 text-xs font-bold rounded-lg cursor-not-allowed shadow-sm"
                                                                >
                                                                    <span
                                                                        >Waktu
                                                                        Habis</span
                                                                    >
                                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                                </button>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- MODAL UPLOAD TUGAS (FLAT DESIGN - SELARAS) -->
        <div
            x-show="tugasModalOpen"
            style="display: none"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6"
            role="dialog"
            aria-modal="true"
        >
            <!-- Backdrop -->
            <div
                x-show="tugasModalOpen"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"
                @click="tutupModal()"
            ></div>

            <!-- Modal Panel -->
            <div
                x-show="tugasModalOpen"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative flex max-h-[85vh] w-full max-w-2xl flex-col overflow-hidden rounded-xl border border-slate-200 bg-white shadow-2xl"
                @click.stop
            >
                <!-- Header Modal -->
                <header
                    class="flex items-start justify-between border-b border-slate-100 px-6 sm:px-8 py-5 sm:py-6 bg-slate-50/50"
                >
                    <div>
                        <h2
                            class="text-xl sm:text-2xl font-extrabold text-slate-800 tracking-tight flex items-center gap-2"
                        >
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            Informasi Penugasan
                        </h2>
                        <p class="mt-1 text-xs text-slate-500 font-medium">Kerjakan tugas yang diberikan sebelum deadline.</p>
                    </div>
                    <button
                        type="button"
                        @click="tutupModal()"
                        class="rounded-lg p-2 text-slate-400 hover:bg-slate-200 hover:text-slate-700 transition-colors bg-white border border-slate-200"
                    >
                        <svg class="h-4 w-4 sm:h-5 sm:w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 18 6M6 6l12 12" /></svg>
                    </button>
                </header>

                <!-- Body Modal -->
                <div class="p-6 sm:p-8 overflow-y-auto space-y-6">
                    <!-- Deskripsi Tugas -->
                    <div>
                        <p class="text-[11px] font-extrabold text-slate-400 uppercase tracking-widest mb-2">Instruksi Tugas</p>
                        <p
                            class="text-sm text-slate-700 leading-relaxed font-medium whitespace-pre-line"
                            x-text="
                                tugasAktif?.deskripsi_tugas ||
                                'Tidak ada deskripsi khusus.'
                            "
                        ></p>
                    </div>

                    <!-- Lampiran Panitia (Jika Ada) -->
                    <template
                        x-if="
                            tugasAktif &&
                            parseBerkasLama(tugasAktif.lampiran_tugas).length >
                                0
                        "
                    >
                        <div>
                            <p class="text-[11px] font-extrabold text-slate-400 uppercase tracking-widest mb-2">Lampiran Penugasan</p>
                            <div class="flex flex-col gap-2">
                                <template
                                    x-for="
                                        (berkas, idx) in
                                        parseBerkasLama(
                                            tugasAktif.lampiran_tugas,
                                        )
                                    "
                                    :key="idx"
                                >
                                    <a
                                        :href="'/storage/' + berkas"
                                        target="_blank"
                                        class="flex items-center gap-2 px-3 py-2.5 border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors bg-white shadow-sm"
                                    >
                                        <svg class="w-4 h-4 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        <span
                                            class="text-xs font-bold text-slate-700 truncate"
                                            >Unduh File Lampiran
                                            <span x-text="idx + 1"></span
                                        ></span>
                                    </a>
                                </template>
                            </div>
                        </div>
                    </template>

                    <!-- 1. TIPE TUGAS: WAWANCARA (Hanya Tombol Kehadiran) -->
                    <template x-if="tugasAktif?.tipe_tugas === 'wawancara'">
                        <div
                            class="mt-6 border-t border-slate-100 pt-6 text-center"
                        >
                            <div
                                class="inline-flex items-center justify-center w-12 h-12 bg-blue-50 text-blue-600 rounded-full mb-3 border-2 border-blue-100"
                            >
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                            <p class="text-sm font-extrabold text-slate-800 tracking-wide">Sesi Wawancara</p>
                            <p class="text-xs text-slate-500 mt-1.5 mb-5 font-medium">Pastikan Anda mengonfirmasi kehadiran sesuai jadwal yang ditentukan panitia.</p>

                            <template x-if="!tugasSudahDikumpul">
                                <form
                                    :action="buatUrl(
                                        urlKehadiran,
                                        tugasAktif?.id,
                                    )"
                                    method="POST"
                                >
                                    @csrf
                                    <button
                                        type="submit"
                                        class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg transition-colors shadow-sm"
                                    >
                                        Konfirmasi Kehadiran
                                    </button>
                                </form>
                            </template>

                            <template x-if="tugasSudahDikumpul">
                                <div
                                    class="p-4 bg-emerald-50 border border-emerald-200 rounded-lg flex items-start gap-3 text-left"
                                >
                                    <svg class="w-5 h-5 text-emerald-600 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    <div>
                                        <p class="text-[11px] font-extrabold text-emerald-800 uppercase tracking-widest">Kehadiran Dikonfirmasi</p>
                                        <p class="text-xs font-medium text-emerald-700 mt-1">Anda sudah mengonfirmasi kehadiran untuk sesi wawancara ini.</p>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>

                    <!-- 2. TIPE TUGAS: FORM -->
                    <template
                        x-if="
                            tugasAktif?.tipe_jawaban_tugas === 'form' ||
                            tugasAktif?.tipe_tugas === 'pengisian_form'
                        "
                    >
                        <div class="mt-6 border-t border-slate-100 pt-6">
                            <a
                                :href="buatUrl(urlDetailTugas, tugasAktif?.id)"
                                class="w-full flex justify-center items-center py-3 px-5 bg-blue-600 text-white hover:bg-blue-700 shadow-sm text-xs font-bold rounded-lg transition-colors gap-2"
                            >
                                <span
                                    x-text="
                                        tugasSudahDikumpul
                                            ? 'Lihat Pengisian Form'
                                            : 'Lanjut ke Halaman Pengisian Form'
                                    "
                                ></span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </a>
                        </div>
                    </template>

                    <!-- 3. TIPE TUGAS: PROJECT / UPLOAD FILE -->
                    <template
                        x-if="
                            tugasAktif?.tipe_tugas !== 'wawancara' &&
                            tugasAktif?.tipe_jawaban_tugas !== 'form' &&
                            tugasAktif?.tipe_tugas !== 'pengisian_form'
                        "
                    >
                        <div
                            class="mt-6 border-t border-slate-100 pt-6 text-center"
                        >
                            <form
                                x-show="!tugasSudahDikumpul || tugasDapatDiedit"
                                id="form-revisi-tugas"
                                :action="buatUrl(urlKirimTugas, tugasAktif?.id)"
                                method="POST"
                                enctype="multipart/form-data"
                                class=""
                            >
                                @csrf
                                <div
                                    x-data="{
                                        berkasBaru: [],
                                        berkasTersimpan:
                                            berkasJawaban(tugasAktif),
                                        pesanBerkas: '',
                                        tetapkanBerkas(files) {
                                            const daftarBerkas = Array.from(
                                                files || [],
                                            );
                                            if (!daftarBerkas.length) return;
                                            try {
                                                const transfer =
                                                    new DataTransfer();
                                                [
                                                    ...this.berkasBaru,
                                                    ...daftarBerkas,
                                                ].forEach((berkas) =>
                                                    transfer.items.add(berkas),
                                                );
                                                this.$refs.berkasJawaban.files =
                                                    transfer.files;
                                                this.berkasBaru = Array.from(
                                                    transfer.files,
                                                ).map((berkas) => ({
                                                    file: berkas,
                                                    nama: berkas.name,
                                                    ukuran:
                                                        (
                                                            berkas.size /
                                                            1024 /
                                                            1024
                                                        ).toFixed(2) + ' MB',
                                                }));
                                            } catch (error) {
                                                this.pesanBerkas =
                                                    'Berkas tidak dapat dipilih. Gunakan tombol pilih berkas.';
                                                return;
                                            }
                                            this.pesanBerkas = '';
                                        },
                                        hapusBerkasBaru(indeks) {
                                            this.berkasBaru.splice(indeks, 1);
                                            const transfer = new DataTransfer();
                                            this.berkasBaru.forEach((item) =>
                                                transfer.items.add(item.file),
                                            );
                                            this.$refs.berkasJawaban.files =
                                                transfer.files;
                                        },
                                        hapusBerkasTersimpan(indeks) {
                                            this.berkasTersimpan.splice(
                                                indeks,
                                                1,
                                            );
                                        },
                                        kirimFormulir(
                                            jumlahBerkasTersimpan = 0,
                                        ) {
                                            if (
                                                !this.$refs.berkasJawaban.files
                                                    ?.length &&
                                                !jumlahBerkasTersimpan
                                            ) {
                                                this.pesanBerkas =
                                                    'Sisakan minimal satu berkas atau unggah berkas pengganti sebelum mengirim revisi.';
                                                return;
                                            }
                                            this.$el
                                                .closest('form')
                                                .requestSubmit();
                                        },
                                    }"
                                >
                                    <div
                                        class="mb-4 flex items-center justify-between gap-3"
                                    >
                                        <p class="text-[11px] font-extrabold uppercase tracking-widest text-slate-800" x-text="tugasSudahDikumpul ? 'Unggah Berkas Revisi' : 'Unggah Jawaban Anda'"></p>
                                        <span
                                            class="rounded bg-blue-100 px-2 py-0.5 text-[9px] font-extrabold uppercase tracking-wider text-blue-700"
                                            x-text="
                                                tugasAktif?.tipe_jawaban_tugas?.replace(
                                                    /_/g,
                                                    ' ',
                                                ) || 'Berkas'
                                            "
                                        ></span>
                                    </div>

                                    <label
                                        @dragover.prevent
                                        @drop.prevent="
                                            tetapkanBerkas(
                                                $event.dataTransfer.files,
                                            )
                                        "
                                        :class="berkasBaru.length
                                            ? 'border-emerald-300 bg-emerald-50'
                                            : 'border-slate-300 bg-slate-50 hover:border-blue-400 hover:bg-blue-50'"
                                        class="flex cursor-pointer flex-col items-center justify-center rounded-xl border-2 border-dashed p-8 text-center transition-colors"
                                    >
                                        <svg class="mb-3 h-10 w-10" :class="berkasBaru.length ? 'text-emerald-600' : 'text-slate-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 0 1-.88-7.903A5 5 0 1 1 15.9 6L16 6a5 5 0 0 1 1 9.9M15 13l-3-3m0 0-3 3m3-3v12"></path></svg>
                                        <span
                                            class="text-sm font-bold text-slate-700"
                                            x-text="
                                                berkasBaru.length
                                                    ? berkasBaru.length +
                                                      ' berkas baru siap diunggah'
                                                    : 'Tarik dan lepas satu atau beberapa berkas di sini'
                                            "
                                        ></span>
                                        <span
                                            class="mt-1.5 text-[11px] font-medium text-slate-500"
                                            x-text="
                                                berkasBaru.length
                                                    ? 'Anda dapat menambahkan berkas lagi'
                                                    : 'atau klik untuk memilih berkas (maks. 5 MB per berkas)'
                                            "
                                        ></span>
                                        <input
                                            x-ref="berkasJawaban"
                                            @change="
                                                tetapkanBerkas(
                                                    $event.target.files,
                                                )
                                            "
                                            type="file"
                                            name="file_jawaban[]"
                                            multiple
                                            class="sr-only"
                                        />
                                    </label>

                                    <div
                                        x-show="berkasBaru.length"
                                        x-cloak
                                        class="mt-4 flex items-center gap-2 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-xs font-bold text-emerald-800 shadow-sm"
                                    >
                                        <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m5 13 4 4L19 7"></path></svg>
                                        Berkas baru tersimpan dan siap diunggah.
                                    </div>

                                    <div
                                        x-show="berkasBaru.length"
                                        x-cloak
                                        class="mt-3 space-y-2"
                                    >
                                        <template
                                            x-for="
                                                (berkas, indeks) in berkasBaru
                                            "
                                            :key="berkas.nama + indeks"
                                        >
                                            <div
                                                class="flex items-center justify-between gap-3 rounded-lg border border-blue-200 bg-blue-50 px-4 py-2.5 text-xs font-bold text-blue-800 shadow-sm"
                                            >
                                                <span
                                                    class="truncate"
                                                    x-text="
                                                        berkas.nama +
                                                        ' · ' +
                                                        berkas.ukuran
                                                    "
                                                ></span>
                                                <button
                                                    type="button"
                                                    @click="
                                                        hapusBerkasBaru(indeks)
                                                    "
                                                    class="shrink-0 font-extrabold text-red-600 hover:text-red-800 transition-colors"
                                                >
                                                    Hapus
                                                </button>
                                            </div>
                                        </template>
                                    </div>

                                    <div
                                        x-show="berkasTersimpan.length"
                                        x-cloak
                                        class="mt-4 space-y-2 border-t border-slate-100 pt-4"
                                    >
                                        <p class="text-[11px] font-extrabold uppercase tracking-widest text-slate-500 mb-2">Berkas yang dipertahankan</p>
                                        <template
                                            x-for="
                                                (berkas, indeks) in
                                                berkasTersimpan
                                            "
                                            :key="berkas"
                                        >
                                            <div
                                                class="flex items-center justify-between gap-3 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-2.5 text-xs font-bold text-emerald-800 shadow-sm"
                                            >
                                                <span
                                                    class="truncate"
                                                    x-text="
                                                        berkas.split('/').pop()
                                                    "
                                                ></span>
                                                <button
                                                    type="button"
                                                    @click="
                                                        hapusBerkasTersimpan(
                                                            indeks,
                                                        )
                                                    "
                                                    class="shrink-0 font-extrabold text-red-600 hover:text-red-800 transition-colors"
                                                >
                                                    Hapus
                                                </button>
                                            </div>
                                        </template>
                                    </div>

                                    <div
                                        x-show="pesanBerkas"
                                        x-cloak
                                        class="mt-4 flex items-center gap-2 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-xs font-bold text-red-800 shadow-sm"
                                    >
                                        <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 18 6M6 6l12 12"></path></svg>
                                        <span x-text="pesanBerkas"></span>
                                    </div>

                                    <button
                                        type="button"
                                        @click="
                                            kirimFormulir(
                                                berkasJawaban(
                                                    tugasAktif,
                                                ).filter(
                                                    (berkas) =>
                                                        !berkasHapusSaatRevisi.includes(
                                                            berkas,
                                                        ),
                                                ).length,
                                            )
                                        "
                                        class="mt-6 flex w-full items-center justify-center gap-2 rounded-lg bg-blue-600 py-3 px-5 text-xs font-bold text-white shadow-sm transition-colors hover:bg-blue-700"
                                    >
                                        <span
                                            x-text="
                                                tugasSudahDikumpul
                                                    ? 'Kirim Revisi Tugas'
                                                    : 'Kirim Jawaban Tugas'
                                            "
                                        ></span>
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                    </button>
                                </div>
                            </form>

                            <template x-if="tugasSudahDikumpul">
                                <div
                                    class="p-4 bg-emerald-50 border border-emerald-200 rounded-lg shadow-sm"
                                >
                                    <p class="text-[11px] font-extrabold text-emerald-800 uppercase tracking-widest">Berkas telah diserahkan</p>
                                    <template
                                        x-if="berkasJawaban(tugasAktif).length"
                                    >
                                        <div class="mt-4 space-y-2">
                                            <template
                                                x-for="
                                                    (berkas, indeks) in
                                                    berkasJawaban(tugasAktif)
                                                "
                                                :key="indeks"
                                            >
                                                <div
                                                    class="flex items-center justify-between gap-3 rounded-lg border border-emerald-200 bg-white px-4 py-2.5 shadow-sm"
                                                >
                                                    <a
                                                        :href="'/storage/' +
                                                        berkas"
                                                        target="_blank"
                                                        class="flex items-center gap-2 text-xs font-bold text-emerald-800 hover:text-emerald-950 underline"
                                                    >
                                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0-3-3m3 3 3-3m2 8H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586a1 1 0 0 1 .707.293l5.414 5.414a1 1 0 0 1 .293.707V19a2 2 0 0 1-2 2Z" /></svg>
                                                        Lihat jawaban
                                                        <span
                                                            class="-ml-1"
                                                            x-text="indeks + 1"
                                                        ></span>
                                                    </a>
                                                    <label
                                                        class="flex shrink-0 items-center gap-2 text-[10px] font-extrabold uppercase tracking-wider"
                                                        :class="tugasDapatDiedit
                                                            ? 'cursor-pointer text-red-600'
                                                            : 'cursor-not-allowed text-slate-400'"
                                                        x-show="
                                                            tugasDapatDiedit
                                                        "
                                                    >
                                                        <input
                                                            type="checkbox"
                                                            :disabled="!tugasDapatDiedit"
                                                            @change="
                                                                berkasHapusSaatRevisi =
                                                                    $event
                                                                        .target
                                                                        .checked
                                                                        ? [
                                                                              ...berkasHapusSaatRevisi,
                                                                              berkas,
                                                                          ]
                                                                        : berkasHapusSaatRevisi.filter(
                                                                              (
                                                                                  path,
                                                                              ) =>
                                                                                  path !==
                                                                                  berkas,
                                                                          )
                                                            "
                                                            class="h-4 w-4 rounded border-slate-300 text-red-600 focus:ring-red-500"
                                                        />
                                                        Hapus
                                                    </label>
                                                </div>
                                            </template>
                                        </div>
                                    </template>

                                    <input
                                        form="form-revisi-tugas"
                                        type="hidden"
                                        name="berkas_pertahankan[]"
                                        value=""
                                    />
                                    <template
                                        x-for="
                                            berkasDipertahankan in
                                            berkasJawaban(tugasAktif).filter(
                                                (berkas) =>
                                                    !berkasHapusSaatRevisi.includes(
                                                        berkas,
                                                    ),
                                            )
                                        "
                                        :key="berkasDipertahankan"
                                    >
                                        <input
                                            form="form-revisi-tugas"
                                            type="hidden"
                                            name="berkas_pertahankan[]"
                                            :value="berkasDipertahankan"
                                        />
                                    </template>
                                    <p class="mt-4 text-[10px] font-medium text-emerald-700" x-text="tugasDapatDiedit ? 'Centang satu atau beberapa berkas yang akan dihapus, lalu tekan Kirim Revisi Tugas.' : 'Pengubahan berkas telah dikunci karena waktu pengumpulan berakhir.'"></p>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

@if (session('success') ||
    session('error') ||
    session('error_server') ||
    $errors->any())
    <x-sweet-alert />
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: @json (session('success_type') === 'wawancara'
                ? 'Kehadiran berhasil dikonfirmasi'
                : 'Penugasan berhasil diunggah'),
                text: @json (session('success')),
                confirmButtonText: 'Mengerti',
                confirmButtonColor: '#2563eb',
                customClass: {
                    popup: 'rounded-lg shadow-sm border border-slate-200 font-sans',
                    title: 'text-xl font-extrabold text-slate-800 tracking-tight',
                    htmlContainer: 'text-sm font-normal text-slate-500',
                    confirmButton: 'px-6 py-2.5 rounded-md font-bold text-sm',
                },
            });
            @else
            Swal.fire({
                icon: 'error',
                title: 'Pengiriman belum berhasil',
                text: @json (session('error') ?? (session('error_server') ?? $errors->first())),
                confirmButtonText: 'Perbaiki sekarang',
                confirmButtonColor: '#dc2626',
                customClass: {
                    popup: 'rounded-lg shadow-sm border border-slate-200 font-sans',
                    title: 'text-xl font-extrabold text-slate-800 tracking-tight',
                    htmlContainer: 'text-sm font-normal text-slate-500',
                    confirmButton: 'px-6 py-2.5 rounded-md font-bold text-sm',
                },
            });
            @endif
        });
    </script>
@endif
