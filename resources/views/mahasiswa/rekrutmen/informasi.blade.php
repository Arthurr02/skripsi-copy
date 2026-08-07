<x-app-layout>
    @php
        $bannerData = $rekrutmen->lampiran_banner;
        $bannerArray = is_string($bannerData)
            ? json_decode($bannerData, true)
            : $bannerData;
        $bannerPath =
            is_array($bannerArray) && count($bannerArray) > 0 ? $bannerArray[0] : null;

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
    @endphp
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
    <div class="p-4 sm:p-8 max-w-5xl mx-auto relative z-10 my-6 sm:my-10">
        <!-- Breadcrumb Navigasi -->
        <div class="mb-6">
            <a
                href="{{ route('mahasiswa.rekrutmen.index') }}"
                class="inline-flex items-center text-xs font-bold uppercase tracking-wide text-slate-500 hover:text-blue-600 transition-colors"
            >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali ke Daftar Rekrutmen
            </a>
        </div>

        <!-- HERO COVER CARD (Profesional Flat Layout) -->
        <div
            class="bg-white rounded-lg border border-slate-200 shadow-sm overflow-hidden mb-8"
        >
            <!-- 1. Banner Area -->
            <div
                class="relative w-full h-[200px] sm:h-[280px] bg-slate-100 border-b border-slate-200 overflow-hidden"
            >
                @if ($bannerPath)
                    <img
                        src="{{ asset('storage/' . $bannerPath) }}"
                        alt="Banner Rekrutmen"
                        class="w-full h-full object-cover"
                    />
                @else
                    <!-- Fallback Pattern jika tidak ada banner -->
                    <div class="absolute inset-0 opacity-10">
                        <svg class="w-full h-full" xmlns="http://www.w3.org/2000/svg">
                            <defs>
                                <pattern id="cover-grid" width="32" height="32" patternUnits="userSpaceOnUse">
                                    <path d="M 32 0 L 0 0 0 32" fill="none" stroke="currentColor" class="text-slate-900" stroke-width="1" />
                                </pattern>
                            </defs>
                            <rect width="100%" height="100%" fill="url(#cover-grid)" />
                        </svg>
                    </div>
                @endif

                <!-- Hiasan Flat Geometris Khas Desain Modern -->
                <div
                    class="absolute top-0 right-0 w-32 h-32 bg-blue-600 opacity-20 rounded-bl-full pointer-events-none"
                ></div>
                <div
                    class="absolute -bottom-10 -right-10 w-40 h-40 border-[16px] border-white opacity-20 rounded-full pointer-events-none"
                ></div>

                <!-- Pola Dot Grid (Dekorasi Tambahan) -->
                <div
                    class="absolute top-6 left-6 w-24 h-24 pointer-events-none opacity-40"
                    style="
                        background-image: radial-gradient(
                            #ffffff 2px,
                            transparent 2px
                        );
                        background-size: 12px 12px;
                    "
                ></div>
            </div>

            <!-- 2. Profil & Info Area -->
            <div class="relative px-6 sm:px-10 pb-8 sm:pb-10">
                <!-- Avatar Overlapping -->
                <div
                    class="absolute -top-12 sm:-top-16 left-6 sm:left-10 w-24 h-24 sm:w-32 sm:h-32 rounded-full border-4 border-white bg-white shadow-sm overflow-hidden z-10 flex items-center justify-center"
                >
                    @if (!empty($avatarUrl))
                        <img
                            src="{{ $avatarUrl }}"
                            alt="Logo {{ $namaOrganisasi }}"
                            class="w-full h-full object-cover"
                            referrerpolicy="no-referrer"
                            onerror="
                                this.style.display = 'none';
                                document.getElementById(
                                    'hero-avatar-fallback',
                                ).style.display = 'flex';
                            "
                        />
                    @endif
                    <div
                        id="hero-avatar-fallback"
                        style="{{ !empty($avatarUrl) ? 'display: none;' : 'display: flex;' }}"
                        class="w-full h-full bg-blue-600 text-white flex items-center justify-center text-3xl sm:text-5xl font-black uppercase select-none"
                    >
                        {{
                            substr(
                                $namaOrganisasi,
                                0,
                                1,
                            )
                        }}
                    </div>
                </div>

                <!-- Judul & Status -->
                <div
                    class="pt-16 sm:pt-20 lg:pt-6 lg:pl-[160px] flex flex-col lg:flex-row lg:items-center justify-between gap-6"
                >
                    <div>
                        <h1
                            class="text-2xl sm:text-3xl font-extrabold text-slate-800 tracking-tight leading-tight mb-1.5"
                        >
                            {{ $namaOrganisasi }}
                        </h1>
                        <p class="text-sm font-normal text-slate-500">
                            {{
                                $rekrutmen->slogan ??
                                    'Penerimaan Anggota Baru Tahun ' .
                                        \Carbon\Carbon::parse($rekrutmen->created_at)->format(
                                            'Y',
                                        )
                            }}
                        </p>
                    </div>

                    <div class="shrink-0 flex items-center">
                        <span
                            class="inline-flex items-center gap-2 px-3 py-1.5 rounded-md bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-bold uppercase tracking-wide"
                        >
                            <span class="relative flex h-2 w-2">
                                <span
                                    class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"
                                ></span>
                                <span
                                    class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"
                                ></span>
                            </span>
                            Pendaftaran Aktif
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- AREA KONTEN UTAMA (Split Grid) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8">
            <!-- KOLOM KIRI (Deskripsi & Jadwal) -->
            <div class="lg:col-span-8 space-y-6 lg:space-y-8">
                <!-- Tentang Rekrutmen -->
                <div
                    class="bg-white rounded-lg border border-slate-200 p-6 md:p-8 shadow-sm"
                >
                    <h2
                        class="text-lg font-extrabold text-slate-800 mb-4 flex items-center gap-2.5"
                    >
                        <div
                            class="w-8 h-8 rounded-md bg-blue-100 flex items-center justify-center text-blue-600"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        Tentang Rekrutmen
                    </h2>

                    <div
                        class="prose prose-slate max-w-none text-slate-600 text-sm leading-relaxed font-normal"
                    >
                        <p>{{ $rekrutmen->deskripsi }}</p>
                    </div>

                    <!-- Buku Pedoman -->
                    <div
                        class="mt-8 p-4 bg-slate-50 border border-slate-200 rounded-lg flex flex-col sm:flex-row sm:items-center justify-between gap-4"
                    >
                        <div class="flex items-center gap-3.5">
                            <div
                                class="w-10 h-10 bg-white rounded-md shadow-sm border border-slate-300 flex items-center justify-center text-blue-600 shrink-0"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-slate-800">
                                    Buku Pedoman Seleksi
                                </h3>
                                <p class="text-xs text-slate-500 font-normal mt-0.5">Panduan dan tata cara rekrutmen lengkap.</p>
                            </div>
                        </div>

                        @php
                            $pedomanPath = null;
                            if (!empty($rekrutmen->lampiran_pedoman)) {
                                $pedomanData = $rekrutmen->lampiran_pedoman;
                                $pedomanArray = is_string($pedomanData)
                                    ? json_decode($pedomanData, true)
                                    : $pedomanData;
                                $pedomanPath =
                                    is_array($pedomanArray) && count($pedomanArray) > 0
                                        ? $pedomanArray[0]
                                        : (is_string($pedomanData)
                                            ? $pedomanData
                                            : null);
                            }
                        @endphp

                        @if ($pedomanPath)
                            <a
                                href="{{ asset('storage/' . $pedomanPath) }}"
                                target="_blank"
                                class="inline-flex items-center justify-center gap-1.5 px-4 py-2 bg-white border border-slate-300 text-xs font-bold text-slate-700 rounded-md hover:border-blue-500 hover:text-blue-700 shadow-sm transition-colors whitespace-nowrap shrink-0"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                Unduh PDF
                            </a>
                        @else
                            <button
                                disabled
                                class="px-4 py-2 bg-slate-100 border border-slate-200 text-xs font-bold text-slate-400 rounded-md cursor-not-allowed whitespace-nowrap shrink-0"
                            >
                                Belum Tersedia
                            </button>
                        @endif
                    </div>
                </div>

                <!-- Jadwal Tahapan -->
                <div
                    class="bg-white rounded-lg border border-slate-200 p-6 md:p-8 shadow-sm"
                >
                    <h2
                        class="text-lg font-extrabold text-slate-800 mb-6 flex items-center gap-2.5"
                    >
                        <div
                            class="w-8 h-8 rounded-md bg-blue-100 flex items-center justify-center text-blue-600"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                        Jadwal Tahapan
                    </h2>

                    @if ($tahapans->isEmpty())
                        <div
                            class="text-center py-8 bg-slate-50 border border-slate-200 rounded-md"
                        >
                            <p class="text-sm text-slate-500 font-normal">Jadwal belum dipublikasikan.</p>
                        </div>
                    @else
                        <div
                            class="relative border-l-2 border-slate-200 ml-4 space-y-6"
                        >
                            @foreach ($tahapans as $index => $tahapan)
                                @php
                                    $mulai = \Carbon\Carbon::parse($tahapan->waktu_mulai);
                                    $berakhir = \Carbon\Carbon::parse(
                                        $tahapan->waktu_berakhir ?? $tahapan->waktu_selesai,
                                    );
                                    $isTunggal = $mulai->equalTo($berakhir);
                                @endphp
                                <div class="relative pl-8">
                                    <!-- Titik Timeline -->
                                    <div
                                        class="absolute -left-[9px] top-1.5 w-4 h-4 bg-white border-4 border-blue-500 rounded-full"
                                    ></div>

                                    <div
                                        class="bg-white rounded-md p-5 border border-slate-200 shadow-sm hover:border-blue-400 transition-colors"
                                    >
                                        <h3
                                            class="text-base font-bold text-slate-800 mb-1"
                                        >
                                            {{ $tahapan->nama_tahapan }}
                                        </h3>
                                        <p class="text-xs text-slate-500 font-normal mb-4">{{ $tahapan->deskripsi_tahapan }}</p>

                                        <div>
                                            <div
                                                class="inline-flex items-center gap-2.5 px-3 py-1.5 rounded-md bg-indigo-50 text-blue-700 border border-blue-200"
                                            >
                                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>

                                                @if ($isTunggal)
                                                    <div
                                                        class="flex items-center gap-1.5"
                                                    >
                                                        <span
                                                            class="text-[11px] font-bold uppercase tracking-wide"
                                                        >
                                                            {{
                                                                $mulai->translatedFormat(
                                                                    'd M Y',
                                                                )
                                                            }}
                                                        </span>
                                                        <span
                                                            class="text-[11px] font-bold opacity-75"
                                                        >
                                                            ({{ $mulai->format('H:i') }})
                                                        </span>
                                                    </div>
                                                @else
                                                    <div
                                                        class="flex items-center gap-1.5 flex-wrap"
                                                    >
                                                        <span
                                                            class="text-[11px] font-bold uppercase tracking-wide"
                                                        >
                                                            {{
                                                                $mulai->translatedFormat(
                                                                    'd M Y',
                                                                )
                                                            }} ({{ $mulai->format('H:i') }})
                                                        </span>
                                                        <span
                                                            class="text-[11px] font-bold px-1"
                                                            >&ndash;</span
                                                        >
                                                        <span
                                                            class="text-[11px] font-bold uppercase tracking-wide"
                                                        >
                                                            {{
                                                                $berakhir->translatedFormat(
                                                                    'd M Y',
                                                                )
                                                            }} ({{ $berakhir->format('H:i') }})
                                                        </span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- KOLOM KANAN (Aksi & Formasi) -->
            <div class="lg:col-span-4 space-y-6 lg:space-y-8">
                <div class="sticky top-6 space-y-6 lg:space-y-8">
                    <!-- Call to Action Box -->
                    <div
                        class="bg-blue-600 rounded-lg p-6 md:p-8 text-white shadow-sm border border-blue-700 relative overflow-hidden"
                    >
                        <!-- Hiasan Sudut CTA -->
                        <div
                            class="absolute top-0 right-0 w-32 h-32 bg-white opacity-5 rounded-bl-full pointer-events-none"
                        ></div>
                        <div
                            class="absolute bottom-0 left-0 w-24 h-24 bg-blue-800 opacity-20 rounded-tr-full pointer-events-none"
                        ></div>

                        <div class="relative z-10">
                            <h3
                                class="text-xl font-extrabold mb-2.5 leading-tight"
                            >
                                Bergabung Bersama <br />
                                {{ $namaOrganisasi }}
                            </h3>
                            <p class="text-blue-100 text-sm font-normal mb-8 leading-relaxed">Lengkapi persyaratan dan mulai proses pendaftaran Anda sekarang.</p>

                            <a
                                href="{{ route('mahasiswa.rekrutmen.daftar', $rekrutmen->id) }}"
                                class="w-full flex items-center justify-center gap-2 py-3.5 px-4 bg-white text-blue-700 text-sm font-bold rounded-md hover:bg-slate-50 border border-slate-200 shadow-sm transition-colors"
                            >
                                Daftar Sekarang
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </a>
                        </div>
                    </div>

                    <!-- Formasi Tersedia -->
                    <div
                        class="bg-white rounded-lg border border-slate-200 p-6 md:p-8 shadow-sm"
                    >
                        <h2
                            class="text-base font-extrabold text-slate-800 mb-4 flex items-center gap-2 border-b border-slate-200 pb-4"
                        >
                            <div
                                class="w-6 h-6 rounded-md bg-emerald-100 flex items-center justify-center text-emerald-600"
                            >
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            </div>
                            Formasi Tersedia
                        </h2>

                        @if ($jabatans->isEmpty())
                            <div
                                class="p-4 bg-slate-50 rounded-md border border-slate-200 text-center"
                            >
                                <p class="text-xs text-slate-500 font-normal">Belum ada formasi.</p>
                            </div>
                        @else
                            <ul class="space-y-3">
                                @foreach ($jabatans as $jabatan)
                                    <li
                                        class="flex items-center p-3 bg-slate-50 rounded-md border border-slate-200 hover:border-emerald-400 hover:bg-white transition-colors"
                                    >
                                        <div
                                            class="w-6 h-6 rounded-md bg-emerald-100 flex items-center justify-center text-emerald-600 shrink-0 mr-3"
                                        >
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 12a4 4 0 100-8 4 4 0 000 8zm0 2c-4.418 0-8 2.239-8 5v1h16v-1c0-2.761-3.582-5-8-5z"></path>
                                            </svg>
                                        </div>
                                        <p class="text-xs font-bold text-slate-700">
                                            {{ $jabatan->nama_jabatan }}
                                        </p>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
