@php
    $organisasi = $periode->organisasi;
    $namaOrganisasi = $organisasi?->nama_organisasi ?? 'Organisasi';
    $avatarUrl = !empty($organisasi?->avatar_google)
        ? str_replace('http://', 'https://', $organisasi->avatar_google)
        : (!empty($organisasi?->lampiran_logo)
            ? asset('storage/' . $organisasi->lampiran_logo)
            : null);
@endphp

<x-app-layout>
    <!-- Background Aksen Atas (Desain Selaras) -->
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
        class="py-4 sm:py-8 px-4 sm:px-8 md:px-10 max-w-5xl mx-auto relative z-10 my-6 sm:my-8"
    >
        <!-- Tombol Kembali -->
        <a
            href="{{ route('mahasiswa.riwayat.index') }}"
            class="mb-6 inline-flex items-center gap-2 text-xs font-extrabold text-slate-500 transition-colors hover:text-blue-700"
        >
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m15 18-6-6 6-6" /></svg>
            Kembali ke Riwayat Rekrutmen
        </a>

        <!-- HEADER SELARAS -->
        <div
            class="mb-8 sm:mb-10 relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-5 text-center sm:text-left"
        >
            <div>
                <h2
                    class="text-3xl sm:text-4xl font-extrabold text-slate-800 tracking-tight leading-tight"
                >
                    Tahapan Rekrutmen<br />
                </h2>
                <p class="text-sm text-slate-500 leading-relaxed mt-1">Seluruh tahapan pada rekrutmen yang telah ditutup dapat dibaca tanpa akses untuk mengerjakan atau melihat jawaban peserta.</p>
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
                    <p class="text-[11px] font-bold uppercase tracking-widest text-slate-500">Periode</p>
                </div>
                <p class="text-2xl font-extrabold tracking-tight text-blue-600">
                    {{
                        $periode->tahun_periode ??
                            '-'
                    }}
                </p>
            </div>
        </div>

        <!-- KONTEN UTAMA (Format Card & Padding disamakan) -->
        <div
            class="bg-white px-5 sm:px-10 py-6 sm:py-10 rounded-xl shadow-sm border border-slate-200"
        >
            <!-- Judul Card Logo / Jabatan -->
            <div
                class="border-b border-slate-100 mb-8 pb-5 flex flex-col md:flex-row md:items-end justify-between gap-4"
            >
                <div class="flex items-center gap-4 text-center sm:text-left">
                    <div class="shrink-0">
                        @if (!empty($avatarUrl))
                            <img
                                src="{{ $avatarUrl }}"
                                alt="Logo"
                                class="w-16 h-16 sm:w-20 sm:h-20 rounded-full object-contain bg-white p-0.5 border border-slate-200 shadow-sm"
                                referrerpolicy="no-referrer"
                                onerror="
                                    this.style.display = 'none';
                                    document.getElementById(
                                        'header-fallback-logo-arsip',
                                    ).style.display = 'flex';
                                "
                            />
                            <div
                                id="header-fallback-logo-arsip"
                                style="display: none"
                                class="w-16 h-16 sm:w-20 sm:h-20 rounded-full bg-blue-600 text-white items-center justify-center text-xl sm:text-2xl font-black uppercase border border-blue-700 shadow-sm"
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
                            <div
                                class="w-16 h-16 sm:w-20 sm:h-20 rounded-full bg-blue-600 text-white flex items-center justify-center text-xl sm:text-2xl font-black uppercase border border-blue-700 shadow-sm"
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
                        <p class="text-sm font-bold tracking-wider text-blue-600 mb-1">Arsip Rekrutmen</p>
                        <h2
                            class="flex flex-wrap items-center gap-3 text-xl font-extrabold leading-tight tracking-tight text-slate-800 md:text-2xl"
                        >
                            <span>{{ $namaOrganisasi }}</span>
                        </h2>
                    </div>
                </div>
            </div>

            <!-- TIMELINE TAHAPAN -->
            <div class="relative w-full overflow-hidden">
                @forelse ($tahapans as $tahapan)
                    <div
                        class="mb-6 flex justify-between items-start w-full relative group"
                    >
                        <!-- Garis Vertikal Timeline -->
                        @if (!$loop->last)
                            <div
                                class="absolute border-l-2 border-dashed border-slate-200 h-full ml-[1.4rem] left-0 top-12 -bottom-6"
                            ></div>
                        @endif

                        <!-- Lingkaran Status Timeline -->
                        <div
                            class="relative z-10 w-12 h-12 rounded-full shrink-0 flex items-center justify-center font-bold shadow-sm border-white border-4 bg-blue-100 text-blue-600 ring-2 ring-blue-50"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
                        </div>

                        <!-- Card Konten Tahapan -->
                        <div class="w-full flex-1 mb-2 ml-5 sm:ml-7">
                            <div
                                class="bg-white rounded-xl border border-slate-200 shadow-sm transition-colors duration-300"
                            >
                                <!-- Header Tahapan Card -->
                                <div
                                    class="px-4 sm:px-5 py-3 sm:py-4 border-b border-slate-100 bg-slate-50 rounded-t-xl flex flex-col sm:flex-row sm:items-center justify-between gap-3"
                                >
                                    <div class="flex-1">
                                        <h3
                                            class="text-lg font-extrabold text-slate-700 leading-tight flex items-center gap-2"
                                        >
                                            {{ $tahapan->urutan_tahapan }}. {{ $tahapan->nama_tahapan }}
                                        </h3>
                                    </div>
                                    <div
                                        class="shrink-0 text-left sm:text-right"
                                    >
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
                                    </div>
                                </div>

                                <!-- Body Tahapan Card -->
                                <div class="p-4 sm:p-5 space-y-4">
                                    @if ($tahapan->is_future ?? false)
                                        <div
                                            class="flex items-center gap-2 rounded-md border border-slate-200 bg-slate-50 p-3"
                                        >
                                            <svg class="h-4 w-4 shrink-0 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 0 0 2-2v-6a2 2 0 0 0-2-2H6a2 2 0 0 1-2-2V7a4 4 0 0 1 8 0v4" /></svg>
                                            <p class="text-sm font-bold text-slate-500">Informasi tahapan belum tersedia.</p>
                                        </div>
                                    @else
                                        @if ($tahapan->deskripsi_tahapan)
                                            <p class="text-sm text-slate-600 leading-relaxed font-medium">
                                                {{ $tahapan->deskripsi_tahapan }}
                                            </p>
                                        @endif
                                        @if (!empty($tahapan->lampiran_pengumuman))
                                            <div class="pt-2">
                                                @foreach ($tahapan->lampiran_pengumuman as $lampiran)
                                                    <a
                                                        href="{{ asset('storage/' . $lampiran) }}"
                                                        target="_blank"
                                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white hover:bg-slate-50 border border-slate-300 text-[11px] font-bold text-slate-700 rounded-md shadow-sm transition-colors w-fit"
                                                    >
                                                        <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                                        {{ $tahapan->jenis_tahapan === 'pengumuman' ? 'Unduh Pengumuman' : 'Unduh Pedoman Tahapan' }}
                                                    </a>
                                                @endforeach
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <!-- State Kosong yang Diselaraskan -->
                    <div
                        class="rounded-xl border border-dashed border-slate-300 bg-slate-50 px-5 py-10 text-center flex flex-col items-center justify-center"
                    >
                        <div
                            class="h-12 w-12 rounded-full bg-slate-100 flex items-center justify-center mb-3"
                        >
                            <svg class="h-6 w-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </div>
                        <p class="text-sm font-bold text-slate-700">Data Tahapan Tidak Tersedia</p>
                        <p class="mt-1 text-xs font-medium text-slate-500">Belum ada tahapan yang tersimpan pada rekrutmen ini.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
