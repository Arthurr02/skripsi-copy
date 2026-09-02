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
            urlDetailTugas: '{{ route('mahasiswa.rekrutmen.diikuti.tugas_detail', ['pendaftaran' => '__PENDAFTARAN__', 'tugas' => '__TUGAS__']) }}',

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
    >
        <!-- Background Flat Gelap (Diselaraskan dengan Form Pendaftaran) -->
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

        <!-- AREA HEADER -->
        <div
            class="pt-4 sm:pt-8 px-8 md:px-11 max-w-5xl mx-auto relative z-10 mt-6 sm:mt-10"
        >
            <div
                class="mb-8 pb-5 flex flex-col md:flex-row md:items-end justify-between gap-4"
            >
                <div>
                    <h2
                        class="text-3xl font-extrabold text-slate-800 tracking-tight"
                    >
                        Jadwal Tahapan Seleksi
                        </br>
                        <span class="text-blue-600">{{ $namaOrganisasi }}</span>
                    </h2>
                </div>
            </div>
            <div
                class="p-8 md:p-10 bg-white rounded-lg border border-slate-200 shadow-sm overflow-hidden"
            >
                <div
                    class="border-b border-slate-200 mb-8 pb-6 flex flex-col md:flex-row items-end gap-5 text-center md:text-left"
                >
                    <div class="shrink-0 hidden sm:block">
                        @if (!empty($avatarUrl))
                            <!-- Gambar Utama -->
                            <img
                                src="{{ $avatarUrl }}"
                                alt="Logo"
                                class="w-20 h-20 rounded-full object-contain bg-white p-0.5 border border-slate-200 shadow-sm"
                                referrerpolicy="no-referrer"
                                onerror="
                                    this.style.display = 'none';
                                    document.getElementById(
                                        'header-fallback-logo',
                                    ).style.display = 'flex';
                                "
                            />
                            <!-- Fallback Tersembunyi -->
                            <div
                                id="header-fallback-logo"
                                style="display: none"
                                class="w-20 h-20 rounded-full bg-blue-600 text-white items-center justify-center text-2xl font-black uppercase border border-blue-700 shadow-sm"
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
                            <!-- Jika tidak ada URL sama sekali -->
                            <div
                                class="w-16 h-16 rounded-full bg-blue-600 text-white flex items-center justify-center text-2xl font-black uppercase border border-blue-700 shadow-sm"
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

                    <!-- Info Title -->
                    <div class="flex-1 mt-1 md:mt-0">
                        <p class="text-lg md:text-xl font-bold text-slate-400 uppercase tracking-widest">{{ $namaPosisiUtama }}</p>
                        <h1
                            class="text-xl md:text-2xl font-extrabold uppercase text-slate-800 tracking-tight leading-tight flex items-center"
                        >
                            {{ $namaJabatanUtama }}
                            <span
                                class="h-5 flex items-center px-2 py-0.5 bg-blue-100 text-blue-700 border border-blue-200 rounded-md text-[10px] font-bold uppercase tracking-widest shadow-sm w-max mx-auto md:mx-4"
                            >
                                Pilihan Utama
                            </span>
                        </h1>
                    </div>
                </div>
                @if ($errors->has('file_jawaban') || $errors->has('tugas'))
                    <div
                        class="mb-5 flex items-start gap-3 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800"
                    >
                        <svg class="mt-0.5 h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18 18 6M6 6l12 12"></path></svg>
                        <div>
                            <p class="font-bold">Berkas belum terkirim</p>
                            <p class="mt-0.5 text-xs text-red-700">{{
                                $errors->first('file_jawaban') ??
                                    $errors->first('tugas')
                            }}</p>
                        </div>
                    </div>
                @endif

                <!-- AREA TIMELINE -->
                <div class="relative w-full overflow-hidden">
                    @foreach ($tahapans as $index => $tahapan)
                        <div
                            class="mb-6 flex justify-between items-start w-full relative group"
                        >
                            <!-- GARIS KONEKTOR TIMELINE -->
                            @if (!$loop->last)
                                <div
                                    class="absolute border-l-4 border-slate-200 h-full ml-1.5 left-4 top-12 -bottom-6"
                                ></div>
                            @endif

                            <!-- Indikator Lingkaran Timeline -->
                            <div
                                class="relative z-10 w-12 h-12 rounded-full shrink-0 flex items-center justify-center font-bold shadow-sm border-white border-2 {{ $tahapan->is_past ? 'bg-emerald-500 text-white' : ($tahapan->is_active ? 'bg-blue-600 text-white ring-blue-50' : 'bg-slate-100 text-slate-400 border border-slate-300') }}"
                            >
                                @if ($tahapan->is_past)
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                @elseif ($tahapan->is_active)
                                    <span
                                        class="block w-2.5 h-2.5 bg-white rounded-full"
                                    ></span>
                                @else
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                @endif
                            </div>

                            <!-- Kartu Tahapan Lebih Ringkas -->
                            <div class="w-full flex-1 mb-4">
                                <div
                                    class="bg-white rounded-lg border transition-colors duration-300 {{ $tahapan->is_active ? 'border-blue-500 shadow-sm' : ($tahapan->is_past ? 'border-slate-200': 'border-slate-200') }} ml-7"
                                >
                                    <!-- Card Header -->
                                    <div
                                        class="px-3 sm:px-4 py-2 sm:py-2.5 border-b border-slate-100 {{ $tahapan->is_active ? 'bg-blue-200' : ( 'bg-slate-100') }} rounded-t-lg flex flex-col sm:flex-row sm:items-start justify-between gap-2"
                                    >
                                        <div class="flex-1">
                                            <h3
                                                class="text-xl font-extrabold {{ $tahapan->is_past ? 'text-emerald-700': 'text-blue-700' }}  leading-tight"
                                            >
                                                {{ $tahapan->urutan_tahapan }}. {{ $tahapan->nama_tahapan }}
                                            </h3>
                                        </div>

                                        <div
                                            class="shrink-0 text-left sm:text-right"
                                        >
                                            @if ($tahapan->is_waktu_tunggal)
                                                <p class="text-xs font-bold text-slate-700">{{
                                                    $tahapan->parsed_mulai->translatedFormat(
                                                        'd M Y',
                                                    )
                                                }}</p>
                                                <p class="text-xs font-bold text-slate-700">{{
                                                    $tahapan->parsed_mulai->format(
                                                        'H:i',
                                                    )
                                                }} WIB</p>
                                            @else
                                                <p class="text-xs font-bold text-slate-700 flex">
                                                    <span class="flex flex-col">
                                                        <span>{{
                                                            $tahapan->parsed_mulai->translatedFormat(
                                                                'd M Y',
                                                            )
                                                        }}</span>
                                                        <span>{{
                                                            $tahapan->parsed_mulai->format(
                                                                'H:i',
                                                            )
                                                        }}</span>
                                                    </span>
                                                    &nbsp;&nbsp;&ndash;&nbsp;&nbsp;
                                                    <span class="flex flex-col">
                                                        <span>{{
                                                            $tahapan->parsed_berakhir->translatedFormat(
                                                                'd M Y',
                                                            )
                                                        }}</span>
                                                        <span>{{
                                                            $tahapan->parsed_berakhir->format(
                                                                'H:i',
                                                            )
                                                        }}</span>
                                                    </span>
                                                </p>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Card Body (Tugas & Lampiran) -->
                                    <div class="p-3 sm:p-4 space-y-3">
                                        <!-- Deskripsi Tahapan -->
                                        @if ($tahapan->deskripsi_tahapan)
                                            <p class="text-xs text-slate-700 leading-relaxed">
                                                {{ $tahapan->deskripsi_tahapan }}
                                            </p>
                                        @endif

                                        <!-- Tombol Unduh Panduan -->
                                        @if ($tahapan->pedoman_path)
                                            <a
                                                href="{{ asset('storage/' . $tahapan->pedoman_path) }}"
                                                target="_blank"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white hover:bg-slate-50 border border-slate-300 text-[11px] font-bold text-slate-600 rounded-md shadow-sm transition-colors w-fit"
                                            >
                                                <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                                Unduh Panduan
                                            </a>
                                        @endif

                                        @if ($tahapan->tugas->isNotEmpty())
                                            <div class="space-y-3 pt-1">
                                                @foreach ($tahapan->tugas as $tugas)
                                                    @php $sudahDikumpul = in_array($tugas->id, $tugasDikumpulkan ?? []); @endphp
                                                    <div class="space-y-2">
                                                        <div
                                                            class="flex justify-between border-t border-slate-100 pt-3 items-center"
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
                                                                        class="px-1.5 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center gap-1 w-min"
                                                                    >
                                                                        <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                                                        Diserahkan
                                                                    </span>
                                                                </div>
                                                            @elseif ($tahapan->is_past)
                                                                <div>
                                                                    <span
                                                                        class="px-1.5 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider bg-red-50 text-red-700 border border-red-200 flex items-center gap-1 w-max"
                                                                    >
                                                                        <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18 18 6M6 6l12 12"></path></svg>
                                                                        Tidak
                                                                        Diserahkan
                                                                    </span>
                                                                </div>
                                                            @endif
                                                        </div>

                                                        <!-- Form dibuka langsung agar mahasiswa segera dapat mengisi jawabannya. -->
                                                        @if ($tahapan->is_active || ($tahapan->is_past && $sudahDikumpul))
                                                            @if ($tugas->tipe_jawaban_tugas === 'form' ||
                                                                $tugas->tipe_tugas === 'pengisian_form')
                                                                <a
                                                                    href="{{ route('mahasiswa.rekrutmen.diikuti.tugas_detail', ['pendaftaran' => $pendaftaran->id, 'tugas' => $tugas->id]) }}"
                                                                    class="w-full flex justify-between items-center py-2.5 px-4 {{ $tahapan->is_past ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-slate-600 hover:bg-slate-700' }} text-white shadow-sm text-xs font-bold rounded-md transition-colors"
                                                                >
                                                                    <span>{{
                                                                        $tahapan->is_past
                                                                            ? 'Lihat Tugas Terkirim'
                                                                            : 'Kerjakan Tugas'
                                                                    }}</span>
                                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                                                </a>
                                                            @else
                                                                <button
                                                                    type="button"
                                                                    @click="bukaModal(JSON.parse(atob('{{ base64_encode(json_encode($tugas)) }}')), {{ $sudahDikumpul ? 'true' : 'false' }}, {{ $tahapan->is_active ? 'true' : 'false' }})"
                                                                    class="w-full flex justify-between items-center py-2.5 px-4 {{ $tahapan->is_past ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-slate-600 hover:bg-slate-700' }} text-white shadow-sm text-xs font-bold rounded-md transition-colors"
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
                                                                class="w-full flex justify-between items-center py-2.5 px-4 bg-slate-50 border border-slate-200 text-slate-400 text-xs font-bold rounded-md cursor-not-allowed"
                                                            >
                                                                <span
                                                                    >Terkunci</span
                                                                >
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                                            </button>
                                                        @elseif ($tahapan->is_past)
                                                            <button
                                                                disabled
                                                                class="w-full flex justify-between items-center py-2.5 px-4 bg-slate-100 border border-slate-200 text-slate-400 text-xs font-bold rounded-md cursor-not-allowed"
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
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- AREA KONTEN (TIMELINE YANG DIPERKECIL) -->
        <div
            class="max-w-5xl py-4 sm:py-8 px-8 md:px-10 my-6 sm:my-10 relative z-20"
        ></div>

        <!-- MODAL UPLOAD TUGAS (FLAT DESIGN - ROUNDED LG SELARAS) -->
        <div
            x-show="tugasModalOpen"
            style="display: none"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6"
        >
            <!-- Backdrop -->
            <div
                x-show="tugasModalOpen"
                x-transition.opacity
                class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"
                @click="tutupModal()"
            ></div>

            <!-- Modal Panel -->
            <div
                x-show="tugasModalOpen"
                x-transition.translate.y.scale.95
                class="relative bg-white rounded-lg shadow-xl w-full max-w-lg max-h-[90vh] flex flex-col border border-slate-200 overflow-hidden"
            >
                <!-- Header Modal -->
                <div
                    class="px-5 py-4 border-b border-slate-200 bg-slate-50 flex items-center justify-between"
                >
                    <h3
                        class="text-sm font-extrabold text-slate-800 uppercase tracking-wide flex items-center gap-2"
                    >
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Detail Penugasan
                    </h3>
                    <button
                        type="button"
                        @click="tutupModal()"
                        class="text-slate-400 hover:text-red-500 transition-colors"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <!-- Body Modal -->
                <div class="p-5 sm:p-8 overflow-y-auto space-y-5">
                    <!-- Deskripsi Tugas (Diambil dari database TUGAS) -->
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Deskripsi Tugas</p>
                        <p
                            class="text-xs text-slate-700 leading-relaxed whitespace-pre-line"
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
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Lampiran Penugasan</p>
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
                                        class="flex items-center gap-2 px-3 py-2 border border-slate-200 rounded-md hover:bg-slate-50 transition-colors"
                                    >
                                        <svg class="w-4 h-4 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        <span
                                            class="text-xs font-bold text-slate-600 truncate"
                                        >
                                            Unduh File Lampiran
                                            <span x-text="idx + 1"></span>
                                        </span>
                                    </a>
                                </template>
                            </div>
                        </div>
                    </template>

                    <!-- =============================================================== -->
                    <!-- KONDISI TOMBOL AKSI MODAL BERDASARKAN TIPE TUGAS -->
                    <!-- =============================================================== -->

                    <!-- 1. TIPE TUGAS: WAWANCARA (Hanya Tombol Kehadiran) -->
                    <template x-if="tugasAktif?.tipe_tugas === 'wawancara'">
                        <div
                            class="mt-4 border-t border-slate-200 pt-5 text-center"
                        >
                            <div
                                class="inline-flex items-center justify-center w-10 h-10 bg-blue-100 text-blue-600 rounded-md mb-2"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                            <p class="text-xs font-bold text-slate-800">Sesi Wawancara</p>
                            <p class="text-[10px] text-slate-500 mt-1 mb-4">Pastikan Anda mengonfirmasi kehadiran sesuai jadwal yang ditentukan panitia.</p>

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
                                        class="w-full py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-md transition-colors shadow-sm"
                                    >
                                        Konfirmasi Kehadiran
                                    </button>
                                </form>
                            </template>

                            <template x-if="tugasSudahDikumpul">
                                <div
                                    class="p-3 bg-emerald-50 border border-emerald-200 rounded-md flex items-start gap-2 text-left"
                                >
                                    <svg class="w-4 h-4 text-emerald-600 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    <div>
                                        <p class="text-[10px] font-bold text-emerald-800 uppercase tracking-wider">Kehadiran Dikonfirmasi</p>
                                        <p class="text-xs text-emerald-700 mt-0.5">Anda sudah mengonfirmasi kehadiran untuk sesi wawancara ini.</p>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>

                    <!-- 2. TIPE TUGAS: FORM (Tombol Diarahkan ke Halaman Baru Form) -->
                    <template
                        x-if="
                            tugasAktif?.tipe_jawaban_tugas === 'form' ||
                            tugasAktif?.tipe_tugas === 'pengisian_form'
                        "
                    >
                        <div class="mt-4 border-t border-slate-200 pt-4">
                            <a
                                :href="buatUrl(urlDetailTugas, tugasAktif?.id)"
                                class="w-full flex justify-center items-center py-2.5 px-4 bg-blue-600 text-white hover:bg-blue-700 shadow-sm text-xs font-bold rounded-md transition-colors gap-2"
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
                        <div>
                            <form
                                x-show="!tugasSudahDikumpul || tugasDapatDiedit"
                                id="form-revisi-tugas"
                                :action="buatUrl(urlKirimTugas, tugasAktif?.id)"
                                method="POST"
                                enctype="multipart/form-data"
                                class="mt-4 border-t border-slate-200 pt-4"
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
                                        class="mb-3 flex items-center justify-between gap-3"
                                    >
                                        <p
                                            class="text-[10px] font-bold uppercase tracking-wider text-slate-800"
                                            x-text="
                                                tugasSudahDikumpul
                                                    ? 'Unggah Berkas Revisi'
                                                    : 'Unggah Jawaban Anda'
                                            "
                                        ></p>
                                        <span
                                            class="rounded bg-blue-50 px-2 py-1 text-[9px] font-bold uppercase tracking-wider text-blue-700"
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
                                        class="flex cursor-pointer flex-col items-center justify-center rounded-lg border-2 border-dashed p-6 text-center transition-colors"
                                    >
                                        <svg class="mb-2 h-8 w-8" :class="berkasBaru.length ? 'text-emerald-600' : 'text-slate-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 0 1-.88-7.903A5 5 0 1 1 15.9 6L16 6a5 5 0 0 1 1 9.9M15 13l-3-3m0 0-3 3m3-3v12"></path></svg>
                                        <span
                                            class="text-xs font-bold text-slate-700"
                                            x-text="
                                                berkasBaru.length
                                                    ? berkasBaru.length +
                                                      ' berkas baru siap diunggah'
                                                    : 'Tarik dan lepas satu atau beberapa berkas di sini'
                                            "
                                        ></span>
                                        <span
                                            class="mt-1 text-[10px] text-slate-500"
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
                                        class="mt-3 flex items-center gap-2 rounded-md border border-emerald-200 bg-emerald-50 px-3 py-2 text-xs font-semibold text-emerald-800"
                                    >
                                        <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m5 13 4 4L19 7"></path></svg>
                                        Berkas baru tersimpan dan siap diunggah.
                                    </div>

                                    <div
                                        x-show="berkasBaru.length"
                                        x-cloak
                                        class="mt-3 space-y-1.5"
                                    >
                                        <template
                                            x-for="
                                                (berkas, indeks) in berkasBaru
                                            "
                                            :key="berkas.nama + indeks"
                                        >
                                            <div
                                                class="flex items-center justify-between gap-3 rounded-md border border-blue-200 bg-blue-50 px-3 py-2 text-xs font-semibold text-blue-800"
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
                                                    class="shrink-0 font-bold text-red-600 hover:text-red-800"
                                                >
                                                    Hapus
                                                </button>
                                            </div>
                                        </template>
                                    </div>

                                    <div
                                        x-show="berkasTersimpan.length"
                                        x-cloak
                                        class="mt-3 space-y-2 border-t border-slate-100 pt-3"
                                    >
                                        <p class="text-[10px] font-bold uppercase tracking-wider text-slate-500">Berkas yang dipertahankan</p>
                                        <template
                                            x-for="
                                                (berkas, indeks) in
                                                berkasTersimpan
                                            "
                                            :key="berkas"
                                        >
                                            <div
                                                class="flex items-center justify-between gap-3 rounded-md border border-emerald-200 bg-emerald-50 px-3 py-2 text-xs font-semibold text-emerald-800"
                                            >
                                                <span
                                                    class="truncate"
                                                    x-text="
                                                        berkas.split('/').pop()
                                                    "
                                                ></span
                                                ><button
                                                    type="button"
                                                    @click="
                                                        hapusBerkasTersimpan(
                                                            indeks,
                                                        )
                                                    "
                                                    class="shrink-0 font-bold text-red-600 hover:text-red-800"
                                                >
                                                    Hapus
                                                </button>
                                            </div>
                                        </template>
                                    </div>

                                    <div
                                        x-show="pesanBerkas"
                                        x-cloak
                                        class="mt-3 flex items-center gap-2 rounded-md border border-red-200 bg-red-50 px-3 py-2 text-xs font-semibold text-red-800"
                                    >
                                        <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 18 6M6 6l12 12"></path></svg>
                                        <span x-text="pesanBerkas"></span>
                                    </div>

                                    <button
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
                                        type="button"
                                        class="mt-4 flex w-full items-center justify-center gap-2 rounded-md bg-slate-800 py-2.5 text-xs font-bold text-white shadow-sm transition-colors hover:bg-slate-900"
                                    >
                                        <span
                                            x-text="
                                                tugasSudahDikumpul
                                                    ? 'Kirim Revisi Tugas'
                                                    : 'Kirim Jawaban Tugas'
                                            "
                                        ></span>
                                    </button>
                                </div>
                            </form>
                            <template x-if="tugasSudahDikumpul">
                                <div
                                    class="mt-4 p-3 bg-emerald-50 border border-emerald-200 rounded-md"
                                >
                                    <p class="text-[10px] font-bold text-emerald-800 uppercase tracking-wider">Berkas telah diserahkan</p>
                                    <template
                                        x-if="berkasJawaban(tugasAktif).length"
                                    >
                                        <div class="mt-3 space-y-2">
                                            <template
                                                x-for="
                                                    (berkas, indeks) in
                                                    berkasJawaban(tugasAktif)
                                                "
                                                :key="indeks"
                                            >
                                                <div
                                                    class="flex items-center justify-between gap-3 rounded-md border border-emerald-200 bg-white px-3 py-2"
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
                                                            class="h-3.5 w-3.5 rounded border-slate-300 text-red-600 focus:ring-red-500"
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
                                    <p class="mt-3 text-[10px] font-medium text-emerald-700" x-text="tugasDapatDiedit ? 'Centang satu atau beberapa berkas yang akan dihapus, lalu tekan Kirim Revisi Tugas.' : 'Pengubahan berkas telah dikunci karena waktu pengumpulan berakhir.'"></p>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

@if (session('success'))
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
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
        });
    </script>
@endif
