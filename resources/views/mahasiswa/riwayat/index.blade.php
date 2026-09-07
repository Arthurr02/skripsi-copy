<x-app-layout>
    <!-- BACKGROUND SELARAS -->
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

    <!-- MAIN CONTAINER SELARAS -->
    <main
        class="py-4 sm:py-8 px-8 md:px-10 max-w-5xl mx-auto relative z-10 my-6 sm:my-10"
    >
        <!-- HEADER SELARAS -->
        <div
            class="mb-8 border-b border-slate-200 pb-5 flex flex-col md:flex-row md:items-end justify-between gap-4"
        >
            <div>
                <h2
                    class="text-3xl font-extrabold text-slate-800 tracking-tight"
                >
                    Riwayat Rekrutmen
                </h2>
                <p class="text-sm text-slate-500 mt-1">Daftar riwayat rekrutmen yang telah berlalu.</p>
            </div>
        </div>

        @php
            $adaRiwayat =
                $riwayatDiikuti->isNotEmpty() || $riwayatTidakDiikuti->isNotEmpty();
        @endphp

        @unless ($adaRiwayat)
            <!-- KONDISI KOSONG (SELURUH HALAMAN) -->
            <div
                class="bg-white rounded-lg border border-slate-200 p-16 flex flex-col items-center justify-center text-center shadow-sm"
            >
                <div
                    class="w-16 h-16 bg-slate-100 text-slate-400 rounded-md flex items-center justify-center mb-5 border border-slate-200"
                >
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0 1 18 0z" /></svg>
                </div>
                <h3 class="text-xl font-bold text-slate-800 mb-1.5">
                    Belum Ada Riwayat Rekrutmen
                </h3>
                <p class="text-slate-500 text-sm max-w-md">Rekrutmen yang telah ditutup akan tampil di halaman ini.</p>
            </div>
        @else
            <!-- SECTION: REKRUTMEN DIIKUTI -->
            <section class="mb-10">
                <div
                    class="bg-white px-5 sm:px-10 py-6 sm:py-10 rounded-xl shadow-sm border border-slate-200 space-y-8"
                >
                    <!-- Judul Section -->
                    <div
                        class="flex flex-col justify-between gap-3 sm:flex-row sm:items-end"
                    >
                        <div>
                            <h2
                                class="flex items-center text-xl font-extrabold tracking-tight text-slate-700"
                            >
                                <!-- Ikon Jam dengan panah melingkar (Riwayat) -->
                                <svg class="mr-2 h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Rekrutmen yang Pernah Diikuti
                            </h2>
                        </div>
                        <span
                            class="w-fit rounded-md border border-blue-200 bg-blue-100 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider text-blue-700 shadow-sm"
                        >
                            {{ $riwayatDiikuti->count() }} Rekrutmen
                        </span>
                    </div>
                    <hr class="border-slate-100" />

                    @if ($riwayatDiikuti->isEmpty())
                        <!-- KONDISI KOSONG (PER SECTION) -->
                        <div
                            class="rounded-lg border border-dashed border-slate-300 bg-white px-5 py-8 text-sm font-bold text-slate-500 text-center"
                        >
                            <svg class="mx-auto mb-4 h-12 w-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 13.5h3.86a2.25 2.25 0 0 1 2.012 1.244l.256.512a2.25 2.25 0 0 0 2.013 1.244h3.218a2.25 2.25 0 0 0 2.013-1.244l.256-.512a2.25 2.25 0 0 1 2.013-1.244h3.859m-19.5.338V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18v-4.162c0-.224-.034-.447-.1-.661L19.8 6.808a2.25 2.25 0 0 0-2.15-1.558H6.35a2.25 2.25 0 0 0-2.15 1.558L2.35 13.177a2.25 2.25 0 0 0-.1.661Z" />
                            </svg>
                            Anda belum pernah mengikuti rekrutmen.
                        </div>
                    @else
                        <div
                            class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3"
                        >
                            @foreach ($riwayatDiikuti as $pendaftaran)
                                @php
                                    $jabatanUtama = $pendaftaran->pilihanJabatan1;
                                    $periode = $jabatanUtama?->periode;
                                    $organisasi = $periode?->organisasi;
                                    $namaOrganisasi = $organisasi?->nama_organisasi ?? 'Organisasi';
                                    $avatarUrl = !empty($organisasi?->avatar_google)
                                        ? str_replace('http://', 'https://', $organisasi->avatar_google)
                                        : (!empty($organisasi?->lampiran_logo)
                                            ? asset('storage/' . $organisasi->lampiran_logo)
                                            : null);
                                    $banner = is_array($periode?->lampiran_banner)
                                        ? $periode->lampiran_banner
                                        : json_decode($periode?->lampiran_banner ?? '[]', true);
                                    $bannerPath = $banner[0] ?? null;
                                @endphp
                                <!-- DAFTAR KARTU REKRUTMEN -->
                                <div
                                    class="group bg-white rounded-lg border border-slate-200 overflow-hidden flex flex-col hover:border-blue-400 transition-colors shadow-sm"
                                >
                                    <!-- Banner Area -->
                                    <div
                                        class="h-28 relative border-b border-slate-200 overflow-hidden shrink-0"
                                    >
                                        @if ($bannerPath)
                                            <img
                                                src="{{ asset('storage/' . $bannerPath) }}"
                                                alt="Banner {{ $namaOrganisasi }}"
                                                class="absolute inset-0 w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                                            />
                                            <div class="absolute inset-0"></div>
                                        @else
                                            <div
                                                class="absolute inset-0 bg-gradient-to-br from-blue-600 to-indigo-700"
                                            ></div>
                                            <div
                                                class="absolute inset-0 opacity-20"
                                            >
                                                <svg class="w-full h-full" xmlns="http://www.w3.org/2000/svg">
                                                    <defs>
                                                        <pattern
                                                            id="grid-{{ $pendaftaran->id }}"
                                                            width="20"
                                                            height="20"
                                                            patternUnits="userSpaceOnUse"
                                                        >
                                                            <path d="M 20 0 L 0 0 0 20" fill="none" stroke="currentColor" class="text-white" stroke-width="1" />
                                                        </pattern>
                                                    </defs>
                                                    <rect
                                                        width="100%"
                                                        height="100%"
                                                        fill="url(#grid-{{ $pendaftaran->id }})"
                                                    />
                                                </svg>
                                            </div>
                                        @endif

                                        <!-- Badge Status Ditutup -->
                                        <div class="absolute top-2.5 right-2.5">
                                            <span
                                                class="bg-slate-100 border border-slate-200 text-slate-600 text-[9px] font-bold px-2 py-1 rounded shadow-sm flex items-center gap-1.5 tracking-wide"
                                            >
                                                <span
                                                    class="w-1.5 h-1.5 bg-slate-400 rounded-full"
                                                ></span>
                                                Periode {{
                                                    $periode?->tahun_periode ??
                                                        '-'
                                                }}
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Card Body -->
                                    <div
                                        class="p-6 flex-1 flex flex-col relative"
                                    >
                                        <!-- Avatar Area -->
                                        <div
                                            class="absolute -top-8 left-6 pr-6"
                                        >
                                            <div class="flex items-end gap-3">
                                                @if (!empty($avatarUrl))
                                                    <img
                                                        src="{{ $avatarUrl }}"
                                                        alt="Logo {{ $namaOrganisasi }}"
                                                        class="w-14 h-14 rounded-full object-cover shadow-sm bg-white border border-slate-200"
                                                        referrerpolicy="no-referrer"
                                                        onerror="this.style.display='none'; document.getElementById('card-avatar-fallback-d-{{ $loop->iteration }}').style.display='flex';"
                                                    />
                                                @endif
                                                <div
                                                    id="card-avatar-fallback-d-{{ $loop->iteration }}"
                                                    style="{{ !empty($avatarUrl) ? 'display: none;' : 'display: flex;' }}"
                                                    class="w-14 h-14 rounded-md bg-blue-600 text-white flex items-center justify-center text-xl font-black uppercase border border-blue-700 shadow-sm select-none"
                                                >
                                                    {{
                                                        substr(
                                                            $namaOrganisasi,
                                                            0,
                                                            1,
                                                        )
                                                    }}
                                                </div>
                                                <div
                                                    class="flex min-w-0 h-7 items-center"
                                                >
                                                    <p class="text-[13px] font-extrabold text-blue-600 tracking-wide truncate">
                                                        {{ $namaOrganisasi }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Detail Organisasi -->
                                        <div class="mt-2 mb-3">
                                            <h3
                                                class="text-base font-extrabold text-slate-800 leading-snug transition-colors line-clamp-2"
                                            >
                                                {{
                                                    $periode?->slogan ??
                                                        'Penerimaan Anggota Baru'
                                                }}
                                            </h3>
                                            <p class="mt-1 text-[11px] font-bold text-slate-500">Terdaftar {{
                                                $pendaftaran->created_at?->translatedFormat(
                                                    'd M Y',
                                                ) ?? '-'
                                            }}</p>
                                        </div>

                                        <!-- Formasi Pilihan -->
                                        <div
                                            class="bg-slate-50 border border-slate-200 rounded-md px-3 py-1 mb-4 space-y-1"
                                        >
                                            <!-- Pilihan Utama -->
                                            <div
                                                class="flex flex-col sm:items-start sm:justify-between"
                                            >
                                                <p class="text-[9px] text-slate-400 font-bold tracking-wider shrink-0 mt-0.5">Pilihan Utama</p>
                                                <div
                                                    class="flex flex-col sm:items-end w-full min-w-0"
                                                >
                                                    <p class="text-[11px] font-bold text-slate-500 truncate max-w-full -mb-0.5">
                                                        {{
                                                            !empty($jabatanUtama?->nama_posisi) &&
                                                            $jabatanUtama->nama_posisi !== '-'
                                                                ? $jabatanUtama->nama_posisi
                                                                : 'Tanpa Divisi Khusus'
                                                        }}
                                                    </p>
                                                    <p class="text-[11px] font-extrabold text-slate-700 uppercase max-w-full leading-tight">
                                                        {{
                                                            $jabatanUtama?->nama_jabatan ??
                                                                '-'
                                                        }}
                                                    </p>
                                                </div>
                                            </div>

                                            <!-- Pilihan Cadangan -->
                                            @if ($pendaftaran->pilihanJabatan2)
                                                <div
                                                    class="pt-1 border-t border-slate-200/80 flex flex-col sm:items-start sm:justify-between"
                                                >
                                                    <p class="text-[9px] text-slate-400 font-bold tracking-wider shrink-0 mt-0.5">Cadangan</p>
                                                    <div
                                                        class="flex flex-col sm:items-end w-full min-w-0"
                                                    >
                                                        <p class="text-[11px] font-bold text-slate-500 truncate max-w-full -mb-0.5">
                                                            {{
                                                                !empty(
                                                                    $pendaftaran->pilihanJabatan2->nama_posisi
                                                                ) && $pendaftaran->pilihanJabatan2->nama_posisi !== '-'
                                                                    ? $pendaftaran->pilihanJabatan2->nama_posisi
                                                                    : 'Tanpa Divisi Khusus'
                                                            }}
                                                        </p>
                                                        <p class="text-[11px] font-extrabold uppercase text-slate-600 max-w-full leading-tight">
                                                            {{
                                                                $pendaftaran->pilihanJabatan2
                                                                    ->nama_jabatan
                                                            }}
                                                        </p>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>

                                        <!-- Action Buttons -->

                                        <div class="mt-auto">
                                            <a
                                                href="{{ route('mahasiswa.riwayat.diikuti.tahapan', $pendaftaran->id) }}"
                                                class="w-full flex justify-center py-2 px-4 bg-blue-600 text-xs font-bold text-white rounded-md hover:bg-blue-700 transition-colors shadow-sm gap-1.5 items-center"
                                            >
                                                Lihat Penugasan Terkirim
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0-7 7m7-7H3" /></svg>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </section>
            <!-- SECTION: REKRUTMEN TIDAK DIIKUTI -->
            <section>
                <!-- Judul Section -->
                <div
                    class="bg-white px-5 sm:px-10 py-6 sm:py-10 rounded-xl shadow-sm border border-slate-200 space-y-8"
                >
                    <div
                        class="flex flex-col justify-between gap-3 sm:flex-row sm:items-end"
                    >
                        <div>
                            <h2
                                class="flex items-center text-xl font-extrabold tracking-tight text-slate-700"
                            >
                                <!-- Ikon Kalender dengan garis tengah (Kadaluwarsa) -->
                                <svg class="mr-2 h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 15h6" />
                                </svg>
                                Rekrutmen yang Tidak Diikuti
                            </h2>
                        </div>
                        <span
                            class="w-fit rounded-md border border-blue-200 bg-blue-100 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider text-blue-700 shadow-sm"
                        >
                            {{ $riwayatTidakDiikuti->count() }} Rekrutmen
                        </span>
                    </div>
                    <hr class="border-slate-100" />

                    @if ($riwayatTidakDiikuti->isEmpty())
                        <!-- KONDISI KOSONG (PER SECTION) -->
                        <div
                            class="rounded-lg border border-dashed border-slate-300 bg-white px-5 py-8 text-sm font-bold text-slate-500 text-center"
                        >
                            <svg class="mx-auto mb-4 h-12 w-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 13.5h3.86a2.25 2.25 0 0 1 2.012 1.244l.256.512a2.25 2.25 0 0 0 2.013 1.244h3.218a2.25 2.25 0 0 0 2.013-1.244l.256-.512a2.25 2.25 0 0 1 2.013-1.244h3.859m-19.5.338V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18v-4.162c0-.224-.034-.447-.1-.661L19.8 6.808a2.25 2.25 0 0 0-2.15-1.558H6.35a2.25 2.25 0 0 0-2.15 1.558L2.35 13.177a2.25 2.25 0 0 0-.1.661Z" />
                            </svg>
                            Belum terdapat rekrutmen yang pernah diadakan.
                        </div>
                    @else
                        <div
                            class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3"
                        >
                            @foreach ($riwayatTidakDiikuti as $periode)
                                @php
                                    $organisasi = $periode->organisasi;
                                    $namaOrganisasi = $organisasi?->nama_organisasi ?? 'Organisasi';
                                    $avatarUrl = !empty($organisasi?->avatar_google)
                                        ? str_replace('http://', 'https://', $organisasi->avatar_google)
                                        : (!empty($organisasi?->lampiran_logo)
                                            ? asset('storage/' . $organisasi->lampiran_logo)
                                            : null);
                                    $banner = is_array($periode->lampiran_banner)
                                        ? $periode->lampiran_banner
                                        : json_decode($periode->lampiran_banner ?? '[]', true);
                                    $bannerPath = $banner[0] ?? null;
                                @endphp
                                <!-- DAFTAR KARTU REKRUTMEN -->
                                <div
                                    class="group bg-white rounded-lg border border-slate-200 overflow-hidden flex flex-col hover:border-blue-400 transition-colors shadow-sm"
                                >
                                    <!-- Banner Area -->
                                    <div
                                        class="h-28 relative border-b border-slate-200 overflow-hidden shrink-0"
                                    >
                                        @if ($bannerPath)
                                            <img
                                                src="{{ asset('storage/' . $bannerPath) }}"
                                                alt="Banner {{ $namaOrganisasi }}"
                                                class="absolute inset-0 w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                                            />
                                            <div class="absolute inset-0"></div>
                                        @else
                                            <div
                                                class="absolute inset-0 bg-gradient-to-br from-slate-600 to-slate-800"
                                            ></div>
                                            <div
                                                class="absolute inset-0 opacity-20"
                                            >
                                                <svg class="w-full h-full" xmlns="http://www.w3.org/2000/svg">
                                                    <defs>
                                                        <pattern
                                                            id="grid-terbuka-{{ $periode->id }}"
                                                            width="20"
                                                            height="20"
                                                            patternUnits="userSpaceOnUse"
                                                        >
                                                            <path d="M 20 0 L 0 0 0 20" fill="none" stroke="currentColor" class="text-white" stroke-width="1" />
                                                        </pattern>
                                                    </defs>
                                                    <rect
                                                        width="100%"
                                                        height="100%"
                                                        fill="url(#grid-terbuka-{{ $periode->id }})"
                                                    />
                                                </svg>
                                            </div>
                                        @endif

                                        <!-- Badge Status Ditutup -->
                                        <div class="absolute top-2.5 right-2.5">
                                            <span
                                                class="bg-slate-100 border border-slate-200 text-slate-600 text-[9px] font-bold px-2 py-1 rounded shadow-sm flex items-center gap-1.5 tracking-wide"
                                            >
                                                <span
                                                    class="w-1.5 h-1.5 bg-slate-400 rounded-full"
                                                ></span>
                                                Periode {{
                                                    $periode->tahun_periode ??
                                                        '-'
                                                }}
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Card Body -->
                                    <div
                                        class="p-6 flex-1 flex flex-col relative"
                                    >
                                        <!-- Avatar Area -->
                                        <div
                                            class="absolute -top-8 left-6 pr-6"
                                        >
                                            <div class="flex items-end gap-3">
                                                @if (!empty($avatarUrl))
                                                    <img
                                                        src="{{ $avatarUrl }}"
                                                        alt="Logo {{ $namaOrganisasi }}"
                                                        class="w-14 h-14 rounded-full object-cover shadow-sm bg-white border border-slate-200"
                                                        referrerpolicy="no-referrer"
                                                        onerror="this.style.display='none'; document.getElementById('card-avatar-fallback-t-{{ $loop->iteration }}').style.display='flex';"
                                                    />
                                                @endif
                                                <div
                                                    id="card-avatar-fallback-t-{{ $loop->iteration }}"
                                                    style="{{ !empty($avatarUrl) ? 'display: none;' : 'display: flex;' }}"
                                                    class="w-14 h-14 rounded-full bg-slate-600 border-slate-200 text-white flex items-center justify-center text-xl font-black uppercase border shadow-sm select-none"
                                                >
                                                    {{
                                                        substr(
                                                            $namaOrganisasi,
                                                            0,
                                                            1,
                                                        )
                                                    }}
                                                </div>
                                                <div
                                                    class="flex min-w-0 h-7 items-center"
                                                >
                                                    <p class="text-[13px] font-extrabold text-blue-600 tracking-wide truncate">
                                                        {{ $namaOrganisasi }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Detail Organisasi -->
                                        <div class="mt-2 mb-3">
                                            <h3
                                                class="text-base font-extrabold text-slate-800 leading-snug transition-colors line-clamp-2"
                                            >
                                                {{
                                                    $periode->slogan ??
                                                        'Penerimaan Anggota Baru'
                                                }}
                                            </h3>
                                        </div>

                                        <!-- Deskripsi karena tidak ada jabatan -->
                                        <p class="mb-4 line-clamp-3 text-xs leading-relaxed font-bold text-slate-500">
                                            {{
                                                $periode->deskripsi ?:
                                                    'Informasi rekrutmen yang telah ditutup dapat dilihat melalui halaman detail.'
                                            }}
                                        </p>

                                        <!-- Action Buttons -->
                                        <div
                                            class="mt-auto grid grid-cols-1 sm:grid-cols-2 gap-2"
                                        >
                                            <a
                                                href="{{ route('mahasiswa.rekrutmen.info', $periode->id) }}"
                                                class="w-full flex justify-center py-2 px-4 border border-slate-300 bg-white text-xs font-bold text-slate-700 rounded-md hover:bg-slate-50 hover:text-blue-700 transition-colors shadow-sm gap-1.5 items-center"
                                            >
                                                Detail Info
                                            </a>
                                            <a
                                                href="{{ route('mahasiswa.riwayat.pengumuman', $periode->id) }}"
                                                class="w-full flex justify-center py-2 px-4 bg-slate-800 text-xs font-bold text-white rounded-md hover:bg-slate-900 transition-colors shadow-sm gap-1.5 items-center"
                                            >
                                                Lihat Tahapan yang Telah Berlangsung
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </section>
        @endunless
    </main>
</x-app-layout>
