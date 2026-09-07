<div>
    <!-- Navigasi Pintas -->
    <a
        href="{{ route('mahasiswa.rekrutmen.info', $rekrutmen->id) }}"
        class="inline-flex items-center gap-2 text-xs font-extrabold text-slate-500 transition-colors hover:text-blue-700 mb-6"
    >
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m15 18-6-6 6-6" />
        </svg>
        Kembali ke Detail Informasi
    </a>
    <!-- Header Profil -->
    <div class="flex flex-col md:flex-row items-center justify-between mb-5">
        <div
            class="shrink-0 flex flex-col sm:flex-row items-center gap-5 sm:text-left"
        >
            <div class="shrink-0">
                @if (!empty($avatarUrl))
                    <img
                        src="{{ $avatarUrl }}"
                        alt="Logo"
                        class="w-16 h-16 md:w-20 md:h-20 rounded-full object-cover bg-white border-2 border-slate-200 shadow-sm"
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

            <div>
                <h2
                    class="text-3xl sm:text-4xl font-extrabold text-slate-800 tracking-tight mb-2.5 leading-tight"
                >
                    Formulir Pendaftaran
                </h2>
                <p class="text-sm font-extrabold text-blue-700 leading-relaxed">
                    {{ $namaOrganisasi }}
                </p>
            </div>
        </div>
        <!-- Kotak Tenggat Waktu diselaraskan dengan DNA info-box -->
        <div
            class="bg-white backdrop-blur-sm border border-slate-200/80 px-6 py-4 rounded-lg shrink-0 text-left md:text-right shadow-sm w-full md:w-auto"
        >
            <div class="flex items-center gap-2">
                <span class="flex h-2 w-2">
                    <span
                        class="animate-ping absolute inline-flex h-2 w-2 rounded-full bg-red-400 opacity-75"
                    ></span>
                    <span
                        class="relative inline-flex rounded-full h-2 w-2 bg-red-500"
                    ></span>
                </span>
                <p class="text-[11px] font-bold uppercase tracking-widest text-slate-500">Deadline:</p>
            </div>
            <p class="font-extrabold text-[11px] uppercase tracking-tight text-red-600">
                <span class="text-lg">
                    {{
                        $rawBerakhir->translatedFormat(
                            'H:i | ',
                        )
                    }}
                </span>
                <span class="text-slate-500">
                    {{
                        $rawBerakhir->translatedFormat(
                            'd F Y',
                        )
                    }}
                </span>
            </p>
        </div>
    </div>
</div>
