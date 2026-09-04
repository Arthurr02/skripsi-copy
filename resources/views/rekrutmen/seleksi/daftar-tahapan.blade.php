@php
    $isRiwayat = $isRiwayat ?? false;
    $urlJawaban = $isRiwayat
        ? route($routePrefix . 'riwayat.tahapan', [
            'periode_id' => $periodeAktif?->id ?? '__PERIODE__',
            'jabatan_id' => '__JABATAN__',
            'tahapan_id' => '__TAHAPAN__',
        ])
        : route($routePrefix . 'rekrutmen.seleksi.jawaban', [
            'tahapanId' => '__TAHAPAN__',
            'jabatanId' => '__JABATAN__',
        ]);
@endphp

<x-app-layout>
    <div
        x-data="{
            modalTerbuka: false,
            tahapanAktif: null,
            urlJawaban: @js($urlJawaban),
            pesertaPerTahapanJabatan: @js($pesertaPerTahapanJabatan ?? []),
            bukaTahapan(tahapan) {
                this.tahapanAktif = {
                    ...tahapan,
                    pesertaPerJabatan: this.pesertaPerTahapanJabatan[tahapan.id] || {},
                };
                this.modalTerbuka = true;
                document.body.style.overflow = 'hidden';
            },
            tutupModal() {
                this.modalTerbuka = false;
                this.tahapanAktif = null;
                document.body.style.overflow = '';
            }
        }"
        @keydown.escape.window="tutupModal()"
    >
        <!-- Background Aksen Atas (Mencegah scroll horizontal) -->
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
            class="py-4 sm:py-8 px-4 sm:px-8 md:px-10 max-w-5xl mx-auto relative z-10 my-6 sm:my-10"
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
                            $isRiwayat
                                ? 'Riwayat Tahapan Seleksi'
                                : 'Jadwal Tahapan Seleksi'
                        }}
                    </h2>
                    <p class="text-sm text-slate-500 mt-2 leading-relaxed">
                        {{
                            $isRiwayat
                                ? 'Tinjau jawaban dan keputusan peserta pada periode rekrutmen yang telah ditutup.'
                                : 'Perbarui informasi setiap tahapan atau lakukan seleksi peserta berdasarkan posisi dan jabatan.'
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
                        <p class="text-[11px] text-slate-500 font-bold uppercase tracking-widest">Periode Aktif</p>
                    </div>
                    <p class="text-2xl font-extrabold text-blue-600 tracking-tight">
                        {{
                            $periodeAktif?->tahun_periode ??
                                '-'
                        }}
                    </p>
                </div>
            </div>

            @if (!$periodeAktif)
                <!-- Empty State: Belum ada periode -->
                <div
                    class="bg-white px-5 sm:px-10 py-16 rounded-xl shadow-sm border border-dashed border-slate-300 text-center"
                >
                    <svg class="mx-auto h-12 w-12 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4m0 4h.01M5.07 19h13.86c1.54 0 2.5-1.67 1.73-3L13.73 4c-.77-1.33-2.69-1.33-3.46 0L3.34 16c-.77 1.33.19 3 1.73 3Z" />
                    </svg>
                    <p class="mt-4 text-sm font-bold text-slate-500">Belum ada periode rekrutmen yang dapat dikelola.</p>
                </div>
            @else
                <!-- KONTEN UTAMA (Format Card & Padding disamakan) -->
                <div
                    class="bg-white px-5 sm:px-10 py-6 sm:py-10 rounded-xl shadow-sm border border-slate-200"
                >
                    <!-- Judul Card & Count -->
                    <div
                        class="border-b border-slate-100 mb-8 pb-5 flex flex-col md:flex-row md:items-end justify-between gap-4"
                    >
                        <div>
                            <h2
                                class="text-xl font-extrabold text-slate-800 tracking-wide"
                            >
                                Daftar Tahapan
                            </h2>
                            <p class="mt-1 text-xs text-slate-500">
                                {{
                                    $isRiwayat
                                        ? 'Arsip ini hanya dapat dilihat. Data jawaban dan keputusan tetap tersedia untuk ditinjau.'
                                        : 'Kelola alur rekrutmen dan pantau jawaban peserta pada tiap tahapan.'
                                }}
                            </p>
                        </div>
                        <span
                            class="w-fit rounded-md border border-blue-100 bg-blue-50 px-3 py-1.5 text-[10px] font-extrabold uppercase tracking-widest text-blue-700"
                        >
                            {{ $tahapans->count() }} Tahapan
                        </span>
                    </div>

                    <!-- TIMELINE TAHAPAN -->
                    <div class="relative w-full overflow-hidden">
                        @forelse ($tahapans as $tahapan)
                            <div
                                class="mb-6 flex justify-between items-start w-full relative group"
                            >
                                <!-- Garis Vertikal -->
                                @if (!$loop->last)
                                    <div
                                        class="absolute border-l-2 border-dashed border-slate-200 h-full ml-[1.4rem] left-0 top-12 -bottom-6"
                                    ></div>
                                @endif

                                <!-- Lingkaran Status Timeline -->
                                <div
                                    class="relative z-10 w-12 h-12 rounded-full shrink-0 flex items-center justify-center font-bold shadow-sm border-white border-4 {{ $tahapan->is_past ? 'bg-emerald-500 text-white ring-2 ring-emerald-50' : ($tahapan->is_active ? 'bg-blue-600 text-white ring-2 ring-blue-50' : 'bg-slate-100 text-slate-400 border-2 border-slate-200') }}"
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
                                                    class="text-lg font-extrabold {{ $tahapan->is_past ? 'text-emerald-700' : 'text-slate-800' }} leading-tight flex items-center gap-2"
                                                >
                                                    {{ $tahapan->urutan_tahapan }}. {{ $tahapan->nama_tahapan }}
                                                    @if ($tahapan->is_active)
                                                        <span
                                                            class="px-2 py-0.5 rounded text-[9px] font-extrabold uppercase bg-blue-100 text-blue-700 tracking-wider"
                                                            >Berlangsung</span
                                                        >
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
                                                        <span
                                                            class="flex flex-col"
                                                        >
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
                                                        <span
                                                            class="text-slate-300"
                                                            >&mdash;</span
                                                        >
                                                        <span
                                                            class="flex flex-col"
                                                        >
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
                                                    <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0-3-3m3 3 3-3m2 8H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586a.707.707 0 0 1 .5.207l5.707 5.707a.707.707 0 0 1 .207.5V19a2 2 0 0 1-2 2Z" />
                                                    </svg>
                                                    Unduh Panduan
                                                </a>
                                            @endif

                                            <!-- Aksi Footer -->
                                            <div
                                                class="border-t border-slate-100 pt-4 mt-2"
                                            >
                                                <div
                                                    class="mb-4 flex items-center justify-between gap-3"
                                                >
                                                    <p class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Aksi Tahapan</p>
                                                    <span
                                                        class="rounded bg-slate-50 border border-slate-200 px-2 py-1 text-[10px] font-bold uppercase tracking-wider text-slate-500"
                                                    >
                                                        {{ $tahapan->peserta_count }} peserta
                                                    </span>
                                                </div>

                                                @if ($isRiwayat)
                                                    <button
                                                        type="button"
                                                        @click='bukaTahapan(@json(['id' => $tahapan->id, 'nama' => $tahapan->nama_tahapan]))'
                                                        class="flex w-full sm:w-auto justify-center items-center gap-2 py-3 px-5 bg-slate-800 text-white hover:bg-slate-900 shadow-sm text-xs font-bold rounded-lg transition-colors"
                                                    >
                                                        Lihat Riwayat Seleksi
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 0 2-2h2a2 2 0 0 0 2 2M9 12h6m-6 4h6m-8-4h.01M9 16h.01" />
                                                        </svg>
                                                    </button>
                                                @else
                                                    <div
                                                        class="flex flex-col sm:flex-row gap-3"
                                                    >
                                                        <button
                                                            type="button"
                                                            @click='bukaTahapan(@json(['id' => $tahapan->id, 'nama' => $tahapan->nama_tahapan]))'
                                                            class="flex-1 flex justify-center items-center gap-2 py-3 px-5 bg-blue-600 text-white hover:bg-blue-700 shadow-sm hover:shadow-md text-xs font-bold rounded-lg transition-all"
                                                        >
                                                            Lakukan Seleksi
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 0 2-2h2a2 2 0 0 0 2 2M9 12h6m-6 4h6m-8-4h.01M9 16h.01" />
                                                            </svg>
                                                        </button>
                                                        <a
                                                            href="{{ route($routePrefix . 'rekrutmen.update', ['periode_id' => $periodeAktif->id]) }}?tab=3&tahapan_id={{ $tahapan->id }}"
                                                            class="flex-1 flex justify-center items-center gap-2 py-3 px-5 border border-slate-300 bg-white text-slate-700 hover:bg-slate-50 shadow-sm text-xs font-bold rounded-lg transition-colors"
                                                        >
                                                            Update Tahapan
                                                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m16.862 4.487 1.65-1.65a2.121 2.121 0 1 1 3 3l-9.193 9.193a4.5 4.5 0 0 1-1.897 1.132L7 17l.838-3.422a4.5 4.5 0 0 1 1.132-1.897l7.892-7.194ZM19 7l-3-3M5 21h14" />
                                                            </svg>
                                                        </a>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div
                                class="rounded-xl border-2 border-dashed border-slate-300 px-6 py-16 text-center"
                            >
                                <svg class="mx-auto h-10 w-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                <p class="mt-4 text-sm font-bold text-slate-500">Tahapan belum ditambahkan pada periode ini.</p>
                                @unless ($isRiwayat)
                                    <a
                                        href="{{ route($routePrefix . 'rekrutmen.update', ['periode_id' => $periodeAktif->id]) }}?tab=3"
                                        class="mt-5 inline-flex items-center gap-2 rounded-lg bg-blue-600 px-6 py-3 text-sm font-bold text-white transition hover:bg-blue-700 shadow-md"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                        Tambah Tahapan Baru
                                    </a>
                                @endunless
                            </div>
                        @endforelse
                    </div>
                </div>
            @endif
        </div>

        <!-- MODAL PILIH JABATAN -->
        <div
            x-cloak
            x-show="modalTerbuka"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6"
            role="dialog"
            aria-modal="true"
            aria-labelledby="judul-pilih-jabatan"
        >
            <!-- Modal Backdrop -->
            <div
                x-show="modalTerbuka"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"
                @click="tutupModal()"
            ></div>

            <!-- Modal Content -->
            <section
                x-show="modalTerbuka"
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
                        <p class="text-[10px] font-extrabold uppercase tracking-widest text-blue-600 mb-1.5">Pilih Posisi & Jabatan</p>
                        <h2
                            id="judul-pilih-jabatan"
                            class="text-xl sm:text-2xl font-extrabold text-slate-800 tracking-tight"
                            x-text="tahapanAktif?.nama"
                        ></h2>
                        <p class="mt-1 text-xs text-slate-500 font-medium">
                            {{
                                $isRiwayat
                                    ? 'Pilih jabatan untuk melihat riwayat jawaban dan keputusan peserta.'
                                    : 'Pilih jabatan untuk melihat pengumpulan dan seleksi peserta.'
                            }}
                        </p>
                    </div>
                    <button
                        type="button"
                        @click="tutupModal()"
                        class="rounded-lg p-2 text-slate-400 hover:bg-slate-200 hover:text-slate-700 transition-colors bg-white border border-slate-200"
                        aria-label="Tutup"
                    >
                        <svg class="h-4 w-4 sm:h-5 sm:w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                </header>

                <!-- Body Modal (List Jabatan) -->
                <div class="overflow-y-auto p-6 sm:p-8">
                    @forelse ($listJabatan->groupBy(
                            fn($jabatan) => $jabatan->nama_posisi ?: 'Posisi Lainnya'
                        )
                        as $namaPosisi => $jabatans)
                        <section class="mb-6 last:mb-0">
                            <div class="mb-3 flex items-center gap-2.5">
                                <span
                                    class="h-2 w-2 rounded-full bg-blue-500"
                                ></span>
                                <h3
                                    class="text-[11px] font-extrabold uppercase tracking-widest text-slate-500"
                                >
                                    {{ $namaPosisi }}
                                </h3>
                            </div>
                            <div
                                class="ml-1 grid gap-3 border-l-2 border-slate-100 pl-4 sm:grid-cols-2"
                            >
                                @foreach ($jabatans as $jabatan)
                                    <a
                                        :href="urlJawaban.replace('__TAHAPAN__', tahapanAktif?.id).replace('__JABATAN__', '{{ $jabatan->id }}')"
                                        class="flex flex-col sm:flex-row sm:items-center justify-between rounded-lg border border-slate-200 bg-white px-4 py-3 sm:py-3.5 transition-all hover:border-blue-400 hover:bg-blue-50 hover:shadow-sm group gap-1 sm:gap-0"
                                    >
                                        <span
                                            class="text-sm font-bold text-slate-700 group-hover:text-blue-700 transition-colors"
                                        >
                                            {{ $jabatan->nama_jabatan }}
                                        </span>
                                        <span
                                            class="text-[10px] font-bold text-slate-400 bg-slate-50 group-hover:bg-blue-100 group-hover:text-blue-600 px-2 py-1 rounded-md transition-colors w-fit"
                                        >
                                            <span x-text="(tahapanAktif?.pesertaPerJabatan?.[{{ $jabatan->id }}] ?? 0) + ' pelamar'"></span>
                                        </span>
                                    </a>
                                @endforeach
                            </div>
                        </section>
                    @empty
                        <div class="py-10 text-center">
                            <svg class="mx-auto h-10 w-10 text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                            </svg>
                            <p class="text-sm font-bold text-slate-500">Belum ada posisi atau jabatan yang tersedia.</p>
                        </div>
                    @endforelse
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
