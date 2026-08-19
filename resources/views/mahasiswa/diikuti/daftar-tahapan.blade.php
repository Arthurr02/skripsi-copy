<x-app-layout>
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
            class="py-6 flex flex-col md:flex-row items-center md:items-start gap-5 text-center md:text-left"
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
                    <!-- Fallback Tersembunyi (Akan muncul jika gambar di atas gagal dimuat) -->
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
                        class="px-2 py-0.5 bg-blue-100 text-blue-700 border border-blue-200 rounded-md text-[10px] font-bold uppercase tracking-widest shadow-sm w-max mx-auto md:mx-0"
                    >
                        Pilihan Utama
                    </span>
                    <span
                        class="text-slate-500 text-xs font-bold uppercase tracking-widest"
                    >
                        {{ $namaOrganisasi }}
                    </span>
                </div>

                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">{{ $namaPosisiUtama }}</p>
                <h1
                    class="text-2xl md:text-3xl font-extrabold text-slate-800 tracking-tight leading-tight"
                >
                    {{ $namaJabatanUtama }}
                </h1>
            </div>
        </div>
    </div>

    <!-- AREA KONTEN (TIMELINE YANG DIPERKECIL) -->
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 pb-24 relative z-20">
        <div
            class="bg-white rounded-lg shadow-sm border border-slate-200 p-5 sm:p-6"
        >
            <h2
                class="text-base font-extrabold text-slate-800 border-b border-slate-200 pb-3 mb-6 flex items-center gap-2"
            >
                <div
                    class="w-6 h-6 rounded-md bg-blue-100 flex items-center justify-center text-blue-600"
                >
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
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
                            class="relative z-10 w-10 h-10 rounded-full shrink-0 flex items-center justify-center font-bold shadow-sm ring-4 ring-white 
                            {{ $tahapan->is_past ? 'bg-emerald-500 text-white' : ($tahapan->is_active ? 'bg-blue-600 text-white ring-blue-50' : 'bg-slate-100 text-slate-400 border border-slate-300') }}"
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
                        <div class="ml-4 w-full flex-1">
                            <div
                                class="bg-white rounded-lg border transition-colors duration-300 {{ $tahapan->is_active ? 'border-blue-500 shadow-sm' : 'border-slate-200' }}"
                            >
                                <!-- Card Header -->
                                <div
                                    class="p-3 sm:p-4 border-b border-slate-100 {{ $tahapan->is_active ? 'bg-blue-200' : 'bg-slate-100' }} rounded-t-lg flex flex-col sm:flex-row sm:items-start justify-between gap-2"
                                >
                                    <div class="flex-1">
                                        <div
                                            class="flex items-center gap-2 mb-1"
                                        >
                                            <span
                                                class="text-[9px] font-black tracking-widest uppercase {{ $tahapan->is_active ? 'text-blue-600' : 'text-slate-400' }}"
                                            >
                                                Tahap {{ $tahapan->urutan_tahapan }}
                                            </span>
                                            @if ($tahapan->is_active)
                                                <span
                                                    class="px-1.5 py-0.5 rounded text-[8px] font-bold uppercase tracking-wider bg-blue-100 text-blue-700 border border-blue-200"
                                                >
                                                    Sedang Berjalan
                                                </span>
                                            @endif
                                        </div>
                                        <h3
                                            class="text-sm font-extrabold text-slate-800 leading-tight"
                                        >
                                            {{ $tahapan->nama_tahapan }}
                                        </h3>
                                        @if ($tahapan->deskripsi_tahapan)
                                            <p class="text-xs text-slate-500 mt-1 leading-relaxed line-clamp-2">
                                                {{ $tahapan->deskripsi_tahapan }}
                                            </p>
                                        @endif
                                    </div>

                                    <div
                                        class="shrink-0 text-left sm:text-right"
                                    >
                                        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Waktu</p>
                                        @if ($tahapan->is_waktu_tunggal)
                                            <p class="text-xs font-bold text-slate-700">{{
                                                $tahapan->parsed_mulai->translatedFormat(
                                                    'd M Y',
                                                )
                                            }}</p>
                                            <p class="text-[10px] font-bold text-slate-500">{{
                                                $tahapan->parsed_mulai->format(
                                                    'H:i',
                                                )
                                            }} WIB</p>
                                        @else
                                            <p class="text-xs font-bold text-slate-700">{{
                                                $tahapan->parsed_mulai->translatedFormat(
                                                    'd M',
                                                )
                                            }} - {{
                                                $tahapan->parsed_berakhir->translatedFormat(
                                                    'd M Y',
                                                )
                                            }}</p>
                                            <p class="text-[10px] font-bold text-slate-500">{{
                                                $tahapan->parsed_mulai->format(
                                                    'H:i',
                                                )
                                            }} - {{
                                                $tahapan->parsed_berakhir->format(
                                                    'H:i',
                                                )
                                            }}</p>
                                        @endif
                                    </div>
                                </div>

                                <!-- Card Body (Tugas & Lampiran) -->
                                <div class="p-3 sm:p-4 space-y-3">
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
                                        <div class="space-y-2">
                                            <h4
                                                class="text-[10px] font-extrabold text-slate-600 flex items-center gap-1.5 uppercase tracking-wide border-t border-slate-100 pt-3"
                                            >
                                                <svg class="w-3 h-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                                                Tugas
                                            </h4>

                                            <div class="space-y-2">
                                                @foreach ($tahapan->tugas as $tugas)
                                                    @php $sudahDikumpul = in_array($tugas->id, $tugasDikumpulkan ?? []); @endphp
                                                    <div
                                                        class="bg-white border {{ $sudahDikumpul ? 'border-emerald-300 bg-emerald-50/30' : 'border-slate-200' }} rounded-md p-3 flex flex-col sm:flex-row gap-3 items-start sm:items-center justify-between"
                                                    >
                                                        <div
                                                            class="flex-1 min-w-0"
                                                        >
                                                            <div
                                                                class="flex flex-wrap items-center gap-2 mb-1"
                                                            >
                                                                <span
                                                                    class="px-1.5 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider border {{ $tugas->tipe_jawaban_tugas == 'form' ? 'bg-purple-50 text-purple-700 border-purple-200' : 'bg-orange-50 text-orange-700 border-orange-200' }}"
                                                                >
                                                                    {{
                                                                        str_replace(
                                                                            '_',
                                                                            ' ',
                                                                            $tugas->tipe_jawaban_tugas,
                                                                        )
                                                                    }}
                                                                </span>
                                                                @if ($sudahDikumpul)
                                                                    <span
                                                                        class="px-1.5 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center gap-1"
                                                                    >
                                                                        <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                                                        Diserahkan
                                                                    </span>
                                                                @endif
                                                            </div>
                                                            <h5
                                                                class="text-xs font-extrabold text-slate-800 truncate"
                                                            >
                                                                {{
                                                                    Str::title(
                                                                        str_replace('_', ' ', $tugas->tipe_tugas),
                                                                    )
                                                                }}
                                                            </h5>
                                                        </div>

                                                        <div
                                                            class="w-full sm:w-auto shrink-0 flex gap-2"
                                                        >
                                                            @if ($tahapan->is_active)
                                                                @if ($sudahDikumpul)
                                                                    <button
                                                                        disabled
                                                                        class="w-full sm:w-auto px-4 py-1.5 bg-slate-100 border border-slate-200 text-slate-500 text-[10px] font-bold rounded cursor-not-allowed text-center"
                                                                    >
                                                                        Selesai
                                                                    </button>
                                                                @else
                                                                    <a
                                                                        href="{{ route('mahasiswa.rekrutmen.diikuti.tugas_detail', ['pendaftaran' => $pendaftaran->id, 'tugas' => $tugas->id]) }}"
                                                                        class="w-full sm:w-auto flex justify-center py-1.5 px-4 bg-blue-600 text-[10px] font-bold text-white rounded hover:bg-blue-700 transition-colors shadow-sm gap-1.5 items-center"
                                                                    >
                                                                        Kerjakan
                                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                                                    </a>
                                                                @endif
                                                            @elseif ($tahapan->is_future)
                                                                <button
                                                                    disabled
                                                                    class="w-full sm:w-auto px-4 py-1.5 bg-slate-50 border border-slate-200 text-slate-400 text-[10px] font-bold rounded cursor-not-allowed text-center flex items-center justify-center gap-1"
                                                                >
                                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                                                    Terkunci
                                                                </button>
                                                            @elseif ($tahapan->is_past)
                                                                <button
                                                                    disabled
                                                                    class="w-full sm:w-auto px-4 py-1.5 bg-slate-100 border border-slate-200 text-slate-400 text-[10px] font-bold rounded cursor-not-allowed text-center"
                                                                >
                                                                    Waktu Habis
                                                                </button>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
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
</x-app-layout>
