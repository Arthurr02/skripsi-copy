<x-app-layout>
    <!-- Background Aksen Atas (Mencegah scroll horizontal dengan inset-x-0) -->
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
        <!-- HEADER SELARAS -->
        <div class="mb-10 relative z-10">
            <h2
                class="text-3xl sm:text-4xl font-extrabold text-slate-800 tracking-tight leading-tight"
            >
                Arsip Riwayat Rekrutmen
            </h2>
            <p class="text-sm text-slate-500 mt-2 leading-relaxed">Kumpulan data rekrutmen dari periode-periode sebelumnya.</p>
        </div>

        @if ($riwayatPeriode->isEmpty())
            <!-- Kondisi Kosong (Empty State) -->
            <div
                class="bg-white rounded-2xl shadow-sm border border-slate-200 p-16 flex flex-col items-center justify-center text-center"
            >
                <div class="p-5 bg-slate-50 rounded-full mb-5">
                    <svg class="w-12 h-12 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1M5 19h14a2 2 0 002-2v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-slate-700">
                    Belum Ada Arsip
                </h3>
                <p class="text-slate-500 text-sm mt-1.5 max-w-sm">Data riwayat rekrutmen yang telah selesai periodenya akan muncul secara otomatis di sini.</p>
            </div>
        @else
            <!-- Daftar Folder Arsip -->
            <div
                class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5"
            >
                @foreach ($riwayatPeriode as $periode)
                    <a
                        href="{{ route($routePrefix . 'riwayat.periode', $periode->id) }}"
                        class="group block bg-white rounded-xl border border-slate-200 p-5 hover:border-blue-500 hover:bg-blue-50 transition-colors cursor-pointer shadow-sm"
                    >
                        <div class="flex flex-col gap-4">
                            <!-- Ikon Folder -->
                            <div
                                class="text-blue-200 group-hover:text-blue-500 transition-colors w-max"
                            >
                                <svg class="w-10 h-10" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M10 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2h-8l-2-2z"></path>
                                </svg>
                            </div>

                            <!-- Informasi Folder -->
                            <div>
                                <h3
                                    class="text-sm font-bold text-slate-800 group-hover:text-blue-700 line-clamp-2"
                                >
                                    Periode {{ $periode->tahun_periode }}
                                </h3>
                                <p class="text-[10px] text-slate-400 font-bold mt-1.5 uppercase tracking-widest group-hover:text-blue-500/70 transition-colors">
                                    {{
                                        \Carbon\Carbon::parse(
                                            $periode->created_at,
                                        )->translatedFormat('d M Y')
                                    }}
                                </p>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</x-app-layout>
