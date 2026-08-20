<x-app-layout>
    <!-- Wrapper Alpine.js untuk Modal -->
    <div
        x-data="{
            tugasModalOpen: false,
            tugasAktif: null,
            tugasSudahDikumpul: false,
            pendaftaranId: '{{ $pendaftaran->id }}',

            bukaModal(tugas, statusKumpul) {
                this.tugasAktif = tugas;
                this.tugasSudahDikumpul = statusKumpul;
                this.tugasModalOpen = true;
                document.body.style.overflow = 'hidden';
            },
            tutupModal() {
                this.tugasModalOpen = false;
                setTimeout(() => { this.tugasAktif = null; }, 300);
                document.body.style.overflow = '';
            },
            parseBerkasLama(jsonStr) {
                try {
                    let obj = JSON.parse(jsonStr);
                    if(typeof obj === 'string') obj = JSON.parse(obj);
                    return obj.berkas || [];
                } catch(e) {
                    return [];
                }
            }
        }"
    >
        <!-- Background Flat Gelap (Diselaraskan dengan Form Pendaftaran) -->
        <div
            class="absolute top-0 inset-x-0 h-[400px] overflow-hidden pointer-events-none -z-10 bg-slate-50"
        >
            <div
                class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PHBhdGggZD0iTTAgMGg0MHY0MEgwVjB6bTIwIDIwaDIwdjIwSDIwaC0yMHptMCAwaC0yMHYtMjBoMjB2MjB6IiBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiIHN0cm9rZT0iI2YxZjVmOSIgc3Ryb2tlLXdpZHRoPSIxIi8+PC9zdmc+')] opacity-60"
            ></div>
            <div
                class="absolute -top-[20%] -left-[10%] w-[40%] h-[60%] rounded-full bg-gradient-to-br from-blue-200/80 to-blue-50/20 blur-[100px]"
            ></div>
            <div
                class="absolute top-[10%] right-[10%] w-[35%] h-[50%] rounded-full bg-gradient-to-bl from-indigo-200/60 to-transparent blur-[120px]"
            ></div>
        </div>

        <!-- AREA HEADER -->
        <div class="max-w-5xl mx-auto px-4 sm:px-8 relative z-10 pt-8 pb-3">
            <nav class="mb-6">
                <a
                    href="{{ route('mahasiswa.rekrutmen.diikuti.index') }}"
                    class="inline-flex items-center text-xs font-bold uppercase tracking-wider text-slate-300 hover:text-white transition-colors bg-slate-800 border border-slate-700 hover:bg-slate-700 px-3 py-1.5 rounded-md shadow-sm"
                >
                    <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Kembali ke Riwayat
                </a>
            </nav>

            <!-- Header Profil (Gaya Kartu Putih) -->
            <div
                class="py-6 flex flex-col md:flex-row items-center gap-5 text-center md:text-left"
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
                    <div
                        class="flex flex-col sm:flex-row sm:items-center gap-2 mb-2 justify-center md:justify-start"
                    >
                        <span
                            class="text-blue-700 text-xs font-bold uppercase tracking-widest"
                        >
                            {{ $namaOrganisasi }}
                        </span>
                    </div>

                    <p class="text-lg md:text-xl font-bold text-slate-400 uppercase tracking-widest -mb-2">{{ $namaPosisiUtama }}</p>
                    <h1
                        class="text-xl md:text-2xl font-extrabold text-slate-800 tracking-tight leading-tight flex items-center"
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
        </div>

        <!-- AREA KONTEN (TIMELINE YANG DIPERKECIL) -->
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 pb-24 relative z-20">
            <div class="p-5 sm:p-6">
                <h2
                    class="text-base font-extrabold text-slate-800 border-b border-slate-200 pb-3 mb-6 flex items-center gap-2"
                >
                    <div
                        class="w-6 h-6 rounded-md bg-blue-100 flex items-center justify-center text-blue-600"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 012-2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                    </div>
                    Jadwal Tahapan Seleksi
                </h2>

                <div class="relative w-full overflow-hidden">
                    <!-- Garis Vertikal Timeline -->
                    <div
                        class="absolute border-l-2 border-slate-200 h-full left-5 top-4"
                    ></div>

                    @foreach ($tahapans as $index => $tahapan)
                        <div
                            class="mb-6 flex justify-between items-start w-full relative"
                        >
                            <!-- Indikator Lingkaran Timeline -->
                            <div
                                class="relative z-10 w-10 h-10 rounded-full shrink-0 flex items-center justify-center font-bold shadow-sm ring-4 ring-white {{ $tahapan->is_past ? 'bg-emerald-500 text-white' : ($tahapan->is_active ? 'bg-blue-600 text-white ring-blue-50' : 'bg-slate-100 text-slate-400 border border-slate-300') }}"
                            >
                                @if ($tahapan->is_past)
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                @elseif ($tahapan->is_active)
                                    <span
                                        class="block w-2.5 h-2.5 bg-white rounded-full"
                                    ></span>
                                @else
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                @endif
                            </div>

                            <!-- Kartu Tahapan Lebih Ringkas -->
                            <div class="ml-6 w-full flex-1 mb-4">
                                <div
                                    class="bg-white rounded-lg border transition-colors duration-300 {{ $tahapan->is_active ? 'border-blue-500 shadow-sm' : 'border-slate-200' }}"
                                >
                                    <!-- Card Header -->
                                    <div
                                        class="p-3 sm:p-4 border-b border-slate-100 {{ $tahapan->is_active ? 'bg-blue-200' : 'bg-slate-100' }} rounded-t-lg flex flex-col sm:flex-row sm:items-start justify-between gap-2"
                                    >
                                        <div class="flex-1">
                                            <h3
                                                class="text-xl font-extrabold text-blue-700 leading-tight"
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
                                                        <h4
                                                            class="text-[10px] font-extrabold text-slate-600 flex items-center gap-1.5 uppercase tracking-wide border-t border-slate-100 pt-3"
                                                        >
                                                            <svg class="w-3 h-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                                                            Tugas
                                                        </h4>
                                                        @if ($sudahDikumpul)
                                                            <span
                                                                class="px-1.5 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center gap-1 w-min"
                                                            >
                                                                <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                                                Diserahkan
                                                            </span>
                                                        @endif

                                                        <!-- Tombol Aksi Lebar Penuh (SEMUA TIPE MEMBUKA MODAL POPUP) -->
                                                        @if ($tahapan->is_active)
                                                            <button
                                                                type="button"
                                                                @click="bukaModal(JSON.parse(atob('{{ base64_encode(json_encode($tugas)) }}')), {{ $sudahDikumpul ? 'true' : 'false' }})"
                                                                class="w-full flex justify-between items-center py-2.5 px-4 bg-slate-600 text-white hover:bg-slate-700 shadow-sm text-xs font-bold rounded-md transition-colors"
                                                            >
                                                                <span
                                                                    >Kerjakan
                                                                    Tugas</span
                                                                >
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                                            </button>
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
                <div class="p-5 overflow-y-auto space-y-5">
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

                    <!-- Tipe Pengumpulan Tugas -->
                    <div
                        class="bg-blue-50 border border-blue-100 rounded-md p-3"
                    >
                        <p class="text-[10px] font-bold text-blue-500 uppercase tracking-wider mb-0.5">Tipe Pengumpulan Tugas</p>
                        <p class="text-xs font-bold text-blue-800" x-text="tugasAktif?.tipe_jawaban_tugas ? tugasAktif.tipe_jawaban_tugas.replace(/_/g, ' ').toUpperCase() : tugasAktif?.tipe_tugas ? tugasAktif.tipe_tugas.replace(/_/g, ' ').toUpperCase() : 'BEBAS'"></p>
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
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Lampiran Berkas Panitia</p>
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
                                    :action="`/mahasiswa/rekrutmen/diikuti/tugas-detail/${pendaftaranId}/${tugasAktif?.id}/hadir`"
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
                                :href="`/mahasiswa/rekrutmen-diikuti/tugas-detail/${pendaftaranId}/${tugasAktif?.id}`"
                                class="w-full flex justify-center items-center py-2.5 px-4 bg-blue-600 text-white hover:bg-blue-700 shadow-sm text-xs font-bold rounded-md transition-colors gap-2"
                            >
                                <span
                                    x-text="
                                        tugasSudahDikumpul
                                            ? 'Lihat / Edit Pengisian Form'
                                            : 'Lanjut ke Halaman Pengisian Form'
                                    "
                                ></span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </a>
                        </div>
                    </template>

                    <!-- 3. TIPE TUGAS: PROJECT / UPLOAD FILE (Tombol Upload & Lihat File) -->
                    <template
                        x-if="
                            tugasAktif?.tipe_tugas !== 'wawancara' &&
                            tugasAktif?.tipe_jawaban_tugas !== 'form' &&
                            tugasAktif?.tipe_tugas !== 'pengisian_form'
                        "
                    >
                        <div>
                            <form
                                :action="`/mahasiswa/rekrutmen/diikuti/tugas-detail/${pendaftaranId}/${tugasAktif?.id}`"
                                method="POST"
                                enctype="multipart/form-data"
                                class="mt-4 border-t border-slate-200 pt-4"
                            >
                                @csrf

                                <!-- Info jika sudah pernah diserahkan / Lihat File -->
                                <template x-if="tugasSudahDikumpul">
                                    <div
                                        class="mb-4 p-3 bg-emerald-50 border border-emerald-200 rounded-md flex items-start gap-2"
                                    >
                                        <svg class="w-4 h-4 text-emerald-600 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        <div>
                                            <p class="text-[10px] font-bold text-emerald-800 uppercase tracking-wider">Berkas Telah Diserahkan</p>
                                            <p class="text-xs text-emerald-700 mt-0.5">Anda sudah berhasil mengunggah berkas untuk penugasan ini.</p>
                                        </div>
                                    </div>
                                </template>

                                <p class="text-[10px] font-bold text-slate-800 uppercase tracking-wider mb-2">
                                    <span
                                        x-text="
                                            tugasSudahDikumpul
                                                ? 'Unggah Berkas Baru / Revisi'
                                                : 'Unggah Jawaban Anda'
                                        "
                                    ></span>
                                </p>

                                <label
                                    class="flex flex-col items-center justify-center p-6 border-2 border-dashed border-slate-300 rounded-lg bg-slate-50 hover:bg-slate-100 hover:border-blue-400 transition-colors cursor-pointer group"
                                >
                                    <svg class="w-8 h-8 text-slate-400 group-hover:text-blue-500 mb-2 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                                    <span
                                        class="text-xs font-bold text-blue-600 group-hover:text-blue-700"
                                        >Klik untuk Pilih File</span
                                    >
                                    <input
                                        type="file"
                                        name="file_jawaban"
                                        class="sr-only"
                                        :required="!tugasSudahDikumpul"
                                    />
                                </label>

                                <button
                                    type="submit"
                                    class="w-full mt-4 py-2.5 bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold rounded-md transition-colors shadow-sm"
                                >
                                    <span
                                        x-text="
                                            tugasSudahDikumpul
                                                ? 'Kirim Revisi Tugas'
                                                : 'Kirim Jawaban Tugas'
                                        "
                                    ></span>
                                </button>
                            </form>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
