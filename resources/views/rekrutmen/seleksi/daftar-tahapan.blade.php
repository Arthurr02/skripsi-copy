<x-app-layout>
    <div
        x-data="{
            modalTerbuka: false,
            tahapanAktif: null,
            urlJawaban: @js(route($routePrefix . 'rekrutmen.seleksi.jawaban', ['tahapanId' => '__TAHAPAN__', 'jabatanId' => '__JABATAN__'])),
            bukaTahapan(tahapan) {
                this.tahapanAktif = tahapan;
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
        <div
            class="absolute top-0 inset-x-0 h-[400px] overflow-hidden pointer-events-none -z-10"
        >
            <div
                class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZyI+PHBhdGggZD0iTTAgMGg0MHY0MEgwVjB6bTIwIDIwaDIwdjIwSDIwaC0yMHptMCAwaC0yMHYtMjBoMjB2MjB6IiBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiIHN0cm9rZT0iI2YxZjVmOSIgc3Ryb2tlLXdpZHRoPSIxIi8+PC9zdmc+')] opacity-60"
            ></div>
            <div
                class="absolute -top-[20%] -left-[10%] w-[40%] h-[60%] rounded-full bg-gradient-to-br from-blue-300/80 to-blue-50/20 blur-[100px]"
            ></div>
            <div
                class="absolute top-[10%] right-[10%] w-[35%] h-[50%] rounded-full bg-gradient-to-bl from-indigo-200/60 to-transparent blur-[120px]"
            ></div>
        </div>

        <div
            class="pt-4 sm:pt-8 px-8 md:px-11 max-w-5xl mx-auto relative z-10 mt-6 sm:mt-10"
        >
            <div
                class="mb-8 pb-5 flex flex-col md:flex-row md:items-end justify-between gap-4"
            >
                <div>
                    <p
                        class="text-xs font-bold uppercase tracking-[0.18em] text-blue-600"
                    >
                        Pengelolaan Seleksi
                    </p>
                    <h1
                        class="mt-2 text-3xl font-extrabold text-slate-800 tracking-tight"
                    >
                        Jadwal Tahapan Seleksi
                    </h1>
                    <p class="mt-2 text-sm text-slate-500">
                        Perbarui informasi setiap tahapan atau lakukan seleksi peserta berdasarkan posisi dan jabatan.
                    </p>
                </div>
                <div
                    class="rounded-lg border border-slate-200 bg-white px-5 py-3 shadow-sm"
                >
                    <p
                        class="text-[10px] font-bold uppercase tracking-widest text-slate-400"
                    >
                        Periode Rekrutmen
                    </p>
                    <p class="mt-1 text-sm font-extrabold text-slate-700">
                        {{ $periodeAktif?->nama_rekrutmen ?? 'Belum aktif' }}
                    </p>
                </div>
            </div>

            @if (! $periodeAktif)
                <div
                    class="rounded-lg border border-dashed border-slate-300 bg-white px-6 py-14 text-center shadow-sm"
                >
                    <svg
                        class="mx-auto h-10 w-10 text-slate-300"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4m0 4h.01M5.07 19h13.86c1.54 0 2.5-1.67 1.73-3L13.73 4c-.77-1.33-2.69-1.33-3.46 0L3.34 16c-.77 1.33.19 3 1.73 3Z" />
                    </svg>
                    <p class="mt-3 text-sm font-bold text-slate-600">
                        Belum ada periode rekrutmen yang dapat dikelola.
                    </p>
                </div>
            @else
                <div
                    class="p-6 md:p-10 bg-white rounded-lg border border-slate-200 shadow-sm overflow-hidden"
                >
                    <div
                        class="border-b border-slate-200 mb-8 pb-6 flex flex-col md:flex-row md:items-end justify-between gap-4"
                    >
                        <div>
                            <h2 class="text-xl font-extrabold text-slate-800">
                                Daftar Tahapan
                            </h2>
                            <p class="mt-1 text-sm text-slate-500">
                                Kelola alur rekrutmen dan pantau jawaban peserta pada tiap tahapan.
                            </p>
                        </div>
                        <span
                            class="w-fit rounded-md border border-blue-100 bg-blue-50 px-3 py-1.5 text-[10px] font-extrabold uppercase tracking-wider text-blue-700"
                        >
                            {{ $tahapans->count() }} Tahapan
                        </span>
                    </div>

                    <div class="relative w-full overflow-hidden">
                        @forelse ($tahapans as $tahapan)
                            <div
                                class="mb-6 flex justify-between items-start w-full relative group"
                            >
                                @if (! $loop->last)
                                    <div
                                        class="absolute border-l-4 border-slate-200 h-full ml-1.5 left-4 top-12 -bottom-6"
                                    ></div>
                                @endif

                                <div
                                    class="relative z-10 w-12 h-12 rounded-full shrink-0 flex items-center justify-center font-bold shadow-sm border-white border-2 {{ $tahapan->is_past ? 'bg-emerald-500 text-white' : ($tahapan->is_active ? 'bg-blue-600 text-white ring-blue-50' : 'bg-slate-100 text-slate-400 border border-slate-300') }}"
                                >
                                    @if ($tahapan->is_past)
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                        </svg>
                                    @elseif ($tahapan->is_active)
                                        <span class="block w-2.5 h-2.5 bg-white rounded-full"></span>
                                    @else
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 0 0 2-2v-6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2Zm10-10V7a4 4 0 0 0-8 0v4h8Z" />
                                        </svg>
                                    @endif
                                </div>

                                <div class="w-full flex-1 mb-4">
                                    <div
                                        class="bg-white rounded-lg border transition-colors duration-300 {{ $tahapan->is_active ? 'border-blue-500 shadow-sm' : 'border-slate-200' }} ml-7"
                                    >
                                        <div
                                            class="px-3 sm:px-4 py-2 sm:py-2.5 border-b border-slate-100 {{ $tahapan->is_active ? 'bg-blue-200' : 'bg-slate-100' }} rounded-t-lg flex flex-col sm:flex-row sm:items-start justify-between gap-2"
                                        >
                                            <div class="flex-1">
                                                <h3 class="text-xl font-extrabold {{ $tahapan->is_past ? 'text-emerald-700' : 'text-blue-700' }} leading-tight">
                                                    {{ $tahapan->urutan_tahapan }}. {{ $tahapan->nama_tahapan }}
                                                </h3>
                                            </div>

                                            <div class="shrink-0 text-left sm:text-right">
                                                @if ($tahapan->is_waktu_tunggal)
                                                    <p class="text-xs font-bold text-slate-700">
                                                        {{ $tahapan->parsed_mulai->translatedFormat('d M Y') }}
                                                    </p>
                                                    <p class="text-xs font-bold text-slate-700">
                                                        {{ $tahapan->parsed_mulai->format('H:i') }} WIB
                                                    </p>
                                                @else
                                                    <p class="text-xs font-bold text-slate-700 flex">
                                                        <span class="flex flex-col">
                                                            <span>{{ $tahapan->parsed_mulai->translatedFormat('d M Y') }}</span>
                                                            <span>{{ $tahapan->parsed_mulai->format('H:i') }}</span>
                                                        </span>
                                                        &nbsp;&nbsp;&ndash;&nbsp;&nbsp;
                                                        <span class="flex flex-col">
                                                            <span>{{ $tahapan->parsed_berakhir->translatedFormat('d M Y') }}</span>
                                                            <span>{{ $tahapan->parsed_berakhir->format('H:i') }}</span>
                                                        </span>
                                                    </p>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="p-3 sm:p-4 space-y-3">
                                            @if ($tahapan->deskripsi_tahapan)
                                                <p class="text-xs text-slate-700 leading-relaxed">
                                                    {{ $tahapan->deskripsi_tahapan }}
                                                </p>
                                            @endif

                                            @if ($tahapan->pedoman_path)
                                                <a
                                                    href="{{ asset('storage/' . $tahapan->pedoman_path) }}"
                                                    target="_blank"
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white hover:bg-slate-50 border border-slate-300 text-[11px] font-bold text-slate-600 rounded-md shadow-sm transition-colors w-fit"
                                                >
                                                    <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0-3-3m3 3 3-3m2 8H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586a.707.707 0 0 1 .5.207l5.707 5.707a.707.707 0 0 1 .207.5V19a2 2 0 0 1-2 2Z" />
                                                    </svg>
                                                    Unduh Panduan
                                                </a>
                                            @endif

                                            <div class="border-t border-slate-100 pt-3">
                                                <div class="mb-3 flex items-center justify-between gap-3">
                                                    <p class="text-[10px] font-extrabold uppercase tracking-wide text-slate-600">
                                                        Aksi Tahapan
                                                    </p>
                                                    <span class="rounded border border-slate-200 bg-slate-50 px-2 py-1 text-[9px] font-bold uppercase tracking-wider text-slate-500">
                                                        {{ $tahapan->tugas_count }} tugas
                                                    </span>
                                                </div>
                                                <div class="grid gap-2 sm:grid-cols-2">
                                                    <a
                                                        href="{{ route($routePrefix . 'rekrutmen.update', ['periode_id' => $periodeAktif->id]) }}?tab=3&tahapan_id={{ $tahapan->id }}"
                                                        class="flex justify-between items-center py-2.5 px-4 border border-blue-200 bg-blue-50 text-blue-700 hover:bg-blue-600 hover:text-white hover:border-blue-600 shadow-sm text-xs font-bold rounded-md transition-colors"
                                                    >
                                                        <span>Update Tahapan</span>
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m16.862 4.487 1.65-1.65a2.121 2.121 0 1 1 3 3l-9.193 9.193a4.5 4.5 0 0 1-1.897 1.132L7 17l.838-3.422a4.5 4.5 0 0 1 1.132-1.897l7.892-7.194ZM19 7l-3-3M5 21h14" />
                                                        </svg>
                                                    </a>
                                                    <button
                                                        type="button"
                                                        @click='bukaTahapan(@json(['id' => $tahapan->id, 'nama' => $tahapan->nama_tahapan]))'
                                                        class="flex justify-between items-center py-2.5 px-4 bg-slate-600 text-white hover:bg-slate-700 shadow-sm text-xs font-bold rounded-md transition-colors"
                                                    >
                                                        <span>Lakukan Seleksi</span>
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 0 2-2h2a2 2 0 0 0 2 2M9 12h6m-6 4h6m-8-4h.01M9 16h.01" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="rounded-lg border-2 border-dashed border-slate-200 px-6 py-12 text-center">
                                <p class="text-sm font-bold text-slate-500">
                                    Tahapan belum ditambahkan pada periode ini.
                                </p>
                                <a
                                    href="{{ route($routePrefix . 'rekrutmen.update', ['periode_id' => $periodeAktif->id]) }}?tab=3"
                                    class="mt-4 inline-flex rounded-md bg-blue-600 px-4 py-2 text-xs font-bold text-white transition hover:bg-blue-700"
                                >
                                    Tambah Tahapan
                                </a>
                            </div>
                        @endforelse
                    </div>
                </div>
            @endif
        </div>

        <div
            x-cloak
            x-show="modalTerbuka"
            class="fixed inset-0 z-50 flex items-center justify-center p-4"
            role="dialog"
            aria-modal="true"
            aria-labelledby="judul-pilih-jabatan"
        >
            <div
                class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"
                @click="tutupModal()"
            ></div>
            <section
                class="relative flex max-h-[85vh] w-full max-w-2xl flex-col overflow-hidden rounded-lg border border-slate-200 bg-white shadow-xl"
                @click.stop
            >
                <header class="flex items-start justify-between border-b border-slate-100 px-6 py-5">
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-blue-600">
                            Pilih Posisi dan Jabatan
                        </p>
                        <h2 id="judul-pilih-jabatan" class="mt-1 text-lg font-extrabold text-slate-800" x-text="tahapanAktif?.nama"></h2>
                        <p class="mt-1 text-xs text-slate-500">
                            Pilih jabatan untuk melihat pengumpulan peserta pada tahapan ini.
                        </p>
                    </div>
                    <button
                        type="button"
                        @click="tutupModal()"
                        class="rounded-md p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-700"
                        aria-label="Tutup"
                    >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                </header>
                <div class="overflow-y-auto p-6">
                    @forelse ($listJabatan->groupBy(fn ($jabatan) => $jabatan->nama_posisi ?: 'Posisi Lainnya') as $namaPosisi => $jabatans)
                        <section class="mb-5 last:mb-0">
                            <div class="mb-2 flex items-center gap-2">
                                <span class="h-2 w-2 rounded-full bg-blue-500"></span>
                                <h3 class="text-xs font-extrabold uppercase tracking-wider text-slate-500">
                                    {{ $namaPosisi }}
                                </h3>
                            </div>
                            <div class="ml-1 grid gap-2 border-l-2 border-blue-100 pl-4 sm:grid-cols-2">
                                @foreach ($jabatans as $jabatan)
                                    <a
                                        :href="urlJawaban.replace('__TAHAPAN__', tahapanAktif.id).replace('__JABATAN__', '{{ $jabatan->id }}')"
                                        class="flex items-center justify-between rounded-lg border border-slate-200 px-4 py-3 text-sm font-bold text-slate-700 transition hover:border-blue-300 hover:bg-blue-50 hover:text-blue-700"
                                    >
                                        <span>{{ $jabatan->nama_jabatan }}</span>
                                        <span class="text-[10px] font-semibold text-slate-400">
                                            {{ $jabatan->pendaftaran1_count + $jabatan->pendaftaran2_count }} pelamar
                                        </span>
                                    </a>
                                @endforeach
                            </div>
                        </section>
                    @empty
                        <p class="py-6 text-center text-sm text-slate-500">
                            Belum ada posisi atau jabatan yang tersedia.
                        </p>
                    @endforelse
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
