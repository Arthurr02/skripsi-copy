<x-app-layout>
    @php
        // 1. Variabel Tahapan Info
        $tahapanInfo = $tahapanSatu ?? null;
        $namaTahapan = $tahapanInfo->nama_tahapan ?? 'Pendaftaran & Seleksi Berkas';
        $deskripsiUmum =
            $tahapanInfo->deskripsi_tahapan ??
            'Silakan lengkapi formulir pendaftaran dan unggah berkas yang diminta.';

        // 2. Logika Parsing Waktu
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

        // 3. Logika Relasi Organisasi & Banner
        $periode = $pendaftaran->periode ?? null;
        $organisasi = $periode->organisasi ?? null;
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

        $bannerData = $periode->lampiran_banner ?? null;
        $bannerArray = is_string($bannerData)
            ? json_decode($bannerData, true)
            : $bannerData;
        $bannerPath =
            is_array($bannerArray) && count($bannerArray) > 0 ? $bannerArray[0] : null;

        // 4. LOGIKA FAIL-SAFE: Mengambil Nama Jabatan
        // Jika relasi terputus di controller, kita ambil manual via ID
        $namaJabatanUtama = 'Jabatan Tidak Diketahui';

        if (!empty($pendaftaran->jabatan_1->nama_jabatan)) {
            $namaJabatanUtama = $pendaftaran->jabatan_1->nama_jabatan;
        } elseif (!empty($pendaftaran->jabatan_1_id)) {
            $jabatanBackup = \App\Models\Jabatan::find($pendaftaran->jabatan_1_id);
            if ($jabatanBackup) {
                $namaJabatanUtama = $jabatanBackup->nama_jabatan;
            }
        }
    @endphp

    <!-- Background Aksen Atas -->
    <div
        class="absolute top-0 inset-x-0 h-[300px] bg-slate-900 -z-10 overflow-hidden"
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
                        <pattern id="grid-pattern" width="32" height="32" patternUnits="userSpaceOnUse">
                            <path d="M 32 0 L 0 0 0 32" fill="none" stroke="currentColor" class="text-slate-100" stroke-width="1" />
                        </pattern>
                    </defs>
                    <rect width="100%" height="100%" fill="url(#grid-pattern)" />
                </svg>
            </div>
        @endif
        <div
            class="absolute inset-0 bg-gradient-to-t from-slate-50 via-blue-800/60 to-transparent"
        ></div>
    </div>

    <!-- AREA HEADER -->
    <div
        class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 pt-10 pb-16"
    >
        <nav class="mb-8">
            <a
                href="{{ route('mahasiswa.rekrutmen.diikuti.index') }}"
                class="inline-flex items-center text-xs font-bold uppercase tracking-wider text-slate-300 hover:text-white transition-colors bg-slate-800 hover:bg-slate-700 px-3 py-1.5 rounded-md border border-slate-700 shadow-sm"
            >
                <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali ke Riwayat
            </a>
        </nav>

        <div
            class="flex flex-col md:flex-row md:items-center justify-between gap-6"
        >
            <div class="flex items-start gap-4">
                <!-- Avatar Organisasi -->
                <div class="shrink-0 hidden sm:block">
                    @if (!empty($avatarUrl))
                        <img
                            src="{{ $avatarUrl }}"
                            alt="Logo"
                            class="w-16 h-16 rounded-md object-cover bg-white p-1 border border-slate-200 shadow-sm"
                        />
                    @else
                        <div
                            class="w-16 h-16 rounded-md bg-blue-600 text-white flex items-center justify-center text-2xl font-black uppercase border border-blue-700 shadow-sm"
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
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <span
                            class="px-2 py-0.5 bg-blue-100 text-blue-700 border border-blue-200 rounded-md text-[10px] font-bold uppercase tracking-widest shadow-sm"
                        >
                            Pilihan Utama
                        </span>
                        <span class="text-slate-300 text-sm font-bold">
                            {{ $namaOrganisasi }}
                        </span>
                    </div>

                    <!-- Menampilkan Jabatan dengan Variabel yang Aman -->
                    <h1
                        class="text-3xl md:text-4xl font-extrabold text-white tracking-tight leading-tight mb-1"
                    >
                        {{ $namaJabatanUtama }}
                    </h1>
                </div>
            </div>
        </div>
    </div>

    <!-- AREA KONTEN (TIMELINE) -->
    <div
        class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 -mt-8 pb-24 relative z-20"
    >
        <div
            class="bg-white rounded-lg shadow-sm border border-slate-200 p-6 sm:p-8"
        >
            <h2
                class="text-lg font-extrabold text-slate-800 border-b border-slate-200 pb-4 mb-8 flex items-center gap-2.5"
            >
                <div
                    class="w-8 h-8 rounded-md bg-blue-100 flex items-center justify-center text-blue-600"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                </div>
                Perjalanan Seleksi Anda
            </h2>

            <!-- TIMELINE CONTAINER -->
            <div class="relative w-full overflow-hidden">
                <!-- Garis Vertikal Timeline -->
                <div
                    class="absolute border-l-2 border-slate-200 h-full left-5 top-4"
                ></div>

                @php
                    $now = now();
                @endphp

                @foreach ($tahapans as $index => $tahapan)
                    @php
                        // Logika Penentuan Status Tahapan
                        $waktuMulai = \Carbon\Carbon::parse($tahapan->waktu_mulai);
                        $waktuBerakhir = \Carbon\Carbon::parse($tahapan->waktu_berakhir);

                        $isPast = $waktuBerakhir->isPast();
                        $isActive = $waktuMulai->lte($now) && $waktuBerakhir->gte($now);
                        $isFuture = $waktuMulai->isFuture();

                        $isWaktuTunggal = $waktuMulai->equalTo($waktuBerakhir);

                        $pedomanArray = is_string($tahapan->lampiran_tahapan)
                            ? json_decode($tahapan->lampiran_tahapan, true)
                            : $tahapan->lampiran_tahapan;
                        $pedomanPath =
                            is_array($pedomanArray) && count($pedomanArray) > 0
                                ? $pedomanArray[0]
                                : null;

                        $tugasJabatan = $tahapan->tugas;
                    @endphp
                    <div
                        class="mb-10 flex justify-between items-start w-full relative"
                    >
                        <!-- Indikator Lingkaran Timeline -->
                        <div
                            class="relative z-10 w-10 h-10 rounded-full shrink-0 flex items-center justify-center font-bold shadow-sm ring-4 ring-white 
                            {{ $isPast ? 'bg-emerald-500 text-white' : ($isActive ? 'bg-blue-600 text-white ring-blue-50' : 'bg-slate-100 text-slate-400 border border-slate-300') }}"
                        >
                            @if ($isPast)
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                            @elseif ($isActive)
                                <span
                                    class="block w-2.5 h-2.5 bg-white rounded-full"
                                ></span>
                            @else
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            @endif
                        </div>

                        <!-- Kartu Tahapan -->
                        <div class="ml-6 w-full flex-1">
                            <div
                                class="bg-white rounded-lg border transition-colors duration-300 {{ $isActive ? 'border-blue-300 shadow-md' : 'border-slate-200 shadow-sm' }}"
                            >
                                <!-- Card Header -->
                                <div
                                    class="p-5 sm:p-6 border-b border-slate-100 {{ $isActive ? 'bg-blue-50/50' : 'bg-slate-50' }} rounded-t-lg flex flex-col md:flex-row md:items-start justify-between gap-4"
                                >
                                    <div class="flex-1">
                                        <div
                                            class="flex items-center gap-2 mb-2"
                                        >
                                            <span
                                                class="text-[10px] font-black tracking-widest uppercase {{ $isActive ? 'text-blue-600' : 'text-slate-400' }}"
                                            >
                                                Tahap {{ $tahapan->urutan_tahapan }}
                                            </span>
                                            @if ($isActive)
                                                <span
                                                    class="px-2 py-0.5 rounded-md text-[9px] font-bold uppercase tracking-wider bg-blue-100 text-blue-700 border border-blue-200"
                                                >
                                                    Sedang Berjalan
                                                </span>
                                            @endif
                                        </div>
                                        <h3
                                            class="text-lg font-extrabold text-slate-800 leading-tight"
                                        >
                                            {{ $tahapan->nama_tahapan }}
                                        </h3>
                                        <p class="text-sm text-slate-600 mt-1.5 leading-relaxed">
                                            {{
                                                $tahapan->deskripsi_tahapan ??
                                                    'Tidak ada deskripsi untuk tahapan ini.'
                                            }}
                                        </p>
                                    </div>

                                    <div
                                        class="shrink-0 text-left md:text-right bg-white md:bg-transparent border md:border-none border-slate-200 p-3 md:p-0 rounded-md"
                                    >
                                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Periode Pelaksanaan</p>
                                        @if ($isWaktuTunggal)
                                            <p class="text-sm font-bold text-slate-800">
                                                {{
                                                    $waktuMulai->translatedFormat(
                                                        'd M Y',
                                                    )
                                                }}
                                            </p>
                                            <p class="text-[11px] font-bold text-slate-500 mt-0.5">
                                                {{ $waktuMulai->format('H:i') }} WIB
                                            </p>
                                        @else
                                            <p class="text-sm font-bold text-slate-800">
                                                {{
                                                    $waktuMulai->translatedFormat(
                                                        'd M',
                                                    )
                                                }} - {{
                                                    $waktuBerakhir->translatedFormat(
                                                        'd M Y',
                                                    )
                                                }}
                                            </p>
                                            <p class="text-[11px] font-bold text-slate-500 mt-0.5">
                                                {{ $waktuMulai->format('H:i') }} - {{
                                                    $waktuBerakhir->format(
                                                        'H:i',
                                                    )
                                                }} WIB
                                            </p>
                                        @endif
                                    </div>
                                </div>

                                <!-- Card Body (Tugas & Lampiran) -->
                                <div class="p-5 sm:p-6 space-y-6">
                                    @if ($pedomanPath)
                                        <a
                                            href="{{ asset('storage/' . $pedomanPath) }}"
                                            target="_blank"
                                            class="inline-flex items-center gap-2 px-4 py-2 bg-white hover:bg-slate-50 border border-slate-300 text-xs font-bold text-slate-700 rounded-md shadow-sm transition-colors w-fit"
                                        >
                                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                            Unduh Lampiran Tahapan
                                        </a>
                                    @endif

                                    @if ($tugasJabatan->isNotEmpty())
                                        <div class="space-y-4">
                                            <h4
                                                class="text-xs font-extrabold text-slate-800 flex items-center gap-2 uppercase tracking-wide"
                                            >
                                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                                                Daftar Penugasan
                                            </h4>

                                            <div class="space-y-3">
                                                @foreach ($tugasJabatan as $tugas)
                                                    @php
                                                        $sudahDikumpul = in_array($tugas->id, $tugasDikumpulkan ?? []);
                                                    @endphp
                                                    <div
                                                        class="bg-white border {{ $sudahDikumpul ? 'border-emerald-300' : 'border-slate-200' }} rounded-md p-4 sm:p-5 flex flex-col lg:flex-row gap-5 items-start lg:items-center justify-between shadow-sm"
                                                    >
                                                        <div class="flex-1">
                                                            <div
                                                                class="flex flex-wrap items-center gap-2 mb-2"
                                                            >
                                                                <span
                                                                    class="px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider border {{ $tugas->tipe_jawaban_tugas == 'form' ? 'bg-purple-50 text-purple-700 border-purple-200' : 'bg-orange-50 text-orange-700 border-orange-200' }}"
                                                                >
                                                                    Tipe: {{
                                                                        str_replace(
                                                                            '_',
                                                                            ' ',
                                                                            $tugas->tipe_jawaban_tugas,
                                                                        )
                                                                    }}
                                                                </span>
                                                                @if ($sudahDikumpul)
                                                                    <span
                                                                        class="px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center gap-1"
                                                                    >
                                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                                                        Telah
                                                                        Diserahkan
                                                                    </span>
                                                                @endif
                                                            </div>
                                                            <h5
                                                                class="text-sm font-extrabold text-slate-800 mb-1"
                                                            >
                                                                {{
                                                                    Str::title(
                                                                        str_replace('_', ' ', $tugas->tipe_tugas),
                                                                    )
                                                                }}
                                                            </h5>
                                                            <p class="text-xs text-slate-500 leading-relaxed">
                                                                {{
                                                                    $tugas->deskripsi_tugas ??
                                                                        'Ikuti instruksi yang tertera pada pedoman atau lampiran.'
                                                                }}
                                                            </p>
                                                        </div>

                                                        <div
                                                            class="w-full lg:w-auto shrink-0 flex gap-2"
                                                        >
                                                            @if ($isActive)
                                                                @if ($sudahDikumpul)
                                                                    <a
                                                                        {{-- href="{{ route('mahasiswa.rekrutmen.diikuti.tugas_detail', ['pendaftaran' => $pendaftaran->id, 'tugas' => $tugas->id]) }}" --}}
                                                                        class="w-full lg:w-auto px-5 py-2.5 bg-white border border-slate-300 text-slate-700 text-xs font-bold rounded-md hover:bg-slate-50 transition-colors text-center"
                                                                    >
                                                                        Lihat
                                                                        Jawaban
                                                                    </a>
                                                                @else
                                                                    <a
                                                                        href="{{ route('mahasiswa.rekrutmen.diikuti.tugas_detail', ['pendaftaran' => $pendaftaran->id, 'tugas' => $tugas->id]) }}"
                                                                        class="w-full lg:w-auto flex justify-center py-2.5 px-6 bg-blue-600 text-xs font-bold text-white rounded-md hover:bg-blue-700 transition-colors shadow-sm gap-2 items-center"
                                                                    >
                                                                        Kerjakan
                                                                        Tugas
                                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                                                    </a>
                                                                @endif
                                                            @elseif ($isFuture)
                                                                <button
                                                                    disabled
                                                                    class="w-full lg:w-auto px-5 py-2.5 bg-slate-100 border border-slate-200 text-slate-400 text-xs font-bold rounded-md cursor-not-allowed text-center flex items-center justify-center gap-1.5"
                                                                >
                                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                                                    Terkunci
                                                                </button>
                                                            @elseif ($isPast && !$sudahDikumpul)
                                                                <button
                                                                    disabled
                                                                    class="w-full lg:w-auto px-5 py-2.5 bg-red-50 text-red-600 border border-red-200 text-xs font-bold rounded-md cursor-not-allowed text-center"
                                                                >
                                                                    Waktu Habis
                                                                </button>
                                                            @elseif ($isPast && $sudahDikumpul)
                                                                <button
                                                                    disabled
                                                                    class="w-full lg:w-auto px-5 py-2.5 bg-slate-50 border border-slate-200 text-slate-500 text-xs font-bold rounded-md cursor-not-allowed text-center"
                                                                >
                                                                    Selesai
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
