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
        <div
            class="mb-8 relative z-10 flex flex-col md:flex-row md:items-center justify-between text-center sm:text-left gap-6 border-b border-slate-200/60 pb-8"
        >
            <div>
                <h2
                    class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-800 mb-2 leading-tight"
                >
                    Dashboard Organisasi
                </h2>
                <p class="text-sm font-normal text-slate-500 leading-relaxed">
                    Selamat bertugas,
                    <span class="text-blue-600 font-bold tracking-wide"
                        >{{
                            auth()->user()
                                ->nama_organisasi
                        }}!</span
                    >
                </p>
            </div>

            <!-- Tombol Aksi Utama -->
            <div class="shrink-0">
                <a
                    href="{{ route('organisasi.buka-rekrutmen.index') }}"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3.5 rounded-lg font-bold text-sm transition-colors shadow-sm hover:shadow-md flex items-center justify-center gap-2 w-full md:w-auto"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Buka Rekrutmen Baru
                </a>
            </div>
        </div>

        <!-- MENU PINTAS & KARTU -->
        @if ($periodeAktif)
            <div class="mb-6">
                <h3
                    class="text-sm font-extrabold text-slate-800 flex items-center tracking-wide"
                >
                    <!-- Indikator Biru (Selaras dengan skema warna) -->
                    <span class="relative flex h-2.5 w-2.5 mr-3">
                        <span
                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"
                        ></span>
                        <span
                            class="relative inline-flex rounded-full h-2.5 w-2.5 bg-blue-600"
                        ></span>
                    </span>
                    Menu Pintas
                    <span class="text-blue-600 ml-1.5"
                        >| Periode Aktif {{ $periodeAktif->tahun_periode }}</span
                    >
                </h3>
            </div>
            <!-- Grid Kartu (Warna diseragamkan ke Slate dengan Hover Blue) -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-5">
                <!-- Kartu 1: Daftar Peserta -->
                <a
                    href="{{ route('organisasi.rekrutmen.pendaftar') }}"
                    class="block bg-white p-5 sm:p-6 rounded-xl border border-slate-200 hover:border-blue-300 hover:shadow-md transition-all group relative overflow-hidden"
                >
                    <div
                        class="absolute top-0 left-0 w-full h-1 bg-transparent group-hover:bg-blue-500 transition-colors"
                    ></div>
                    <div class="flex items-center gap-4">
                        <div
                            class="p-3 border rounded-lg shrink-0 bg-blue-50 border-blue-200 text-blue-600 transition-colors"
                        >
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        </div>
                        <div>
                            <span
                                class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400 block mb-1"
                                >Daftar Peserta</span
                            >
                            <h3
                                class="text-2xl font-extrabold text-slate-800 tracking-tight leading-none group-hover:text-blue-700 transition-colors"
                            >
                                {{ $jumlahPeserta }} Pendaftar
                            </h3>
                        </div>
                    </div>
                </a>

                <!-- Kartu 2: Daftar Panitia -->
                <a
                    href="{{ route('organisasi.rekrutmen.panitia') }}"
                    class="block bg-white p-5 sm:p-6 rounded-xl border border-slate-200 hover:border-blue-300 hover:shadow-md transition-all group relative overflow-hidden"
                >
                    <div
                        class="absolute top-0 left-0 w-full h-1 bg-transparent group-hover:bg-blue-500 transition-colors"
                    ></div>
                    <div class="flex items-center gap-4">
                        <div
                            class="p-3 border rounded-lg shrink-0 bg-blue-50 border-blue-200 text-blue-600 transition-colors"
                        >
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2m18 0v-2a4 4 0 00-3-3.87m-3-12.87a4 4 0 010 7.75M9 11a4 4 0 100-8 4 4 0 000 8z"></path></svg>
                        </div>
                        <div>
                            <span
                                class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400 block mb-1"
                                >Daftar Panitia</span
                            >
                            <h3
                                class="text-2xl font-extrabold text-slate-800 tracking-tight leading-none group-hover:text-blue-700 transition-colors"
                            >
                                {{ $periodeAktif->panitia_count }} Panitia
                            </h3>
                        </div>
                    </div>
                </a>

                <!-- Kartu 3: Pengerjaan Seleksi -->
                <a
                    href="{{ route('organisasi.rekrutmen.seleksi') }}"
                    class="block bg-white p-5 sm:p-6 rounded-xl border border-slate-200 hover:border-blue-300 hover:shadow-md transition-all group relative overflow-hidden"
                >
                    <div
                        class="absolute top-0 left-0 w-full h-1 bg-transparent group-hover:bg-blue-500 transition-colors"
                    ></div>
                    <div class="flex items-start gap-4">
                        <div
                            class="p-3 border rounded-lg shrink-0 bg-blue-50 border-blue-200 text-blue-600 transition-colors"
                        >
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                        </div>
                        <div>
                            <span
                                class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400 block mb-1"
                                >Pengerjaan Seleksi</span
                            >
                            <h3
                                class="text-lg font-extrabold text-slate-800 tracking-tight leading-tight mb-1 group-hover:text-blue-700 transition-colors"
                            >
                                @if ($tahapanSeleksiSaatIni)
                                    Tahap {{ $tahapanSeleksiSaatIni->urutan_tahapan }}: {{ $tahapanSeleksiSaatIni->nama_tahapan }}
                                @else
                                    Belum ada seleksi
                                @endif
                            </h3>
                            <p class="text-xs font-medium text-slate-500 mt-1">Tahapan seleksi saat ini.</p>
                        </div>
                    </div>
                </a>

                <!-- Kartu 4: Update Informasi -->
                <a
                    href="{{ route('organisasi.rekrutmen.update') }}"
                    class="block bg-white p-5 sm:p-6 rounded-xl border border-slate-200 hover:border-blue-300 hover:shadow-md transition-all group relative overflow-hidden"
                >
                    <div
                        class="absolute top-0 left-0 w-full h-1 bg-transparent group-hover:bg-blue-500 transition-colors"
                    ></div>
                    <div class="flex items-start gap-4">
                        <div
                            class="p-3 border rounded-lg shrink-0 bg-blue-50 border-blue-200 text-blue-600 transition-colors"
                        >
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        </div>
                        <div>
                            <span
                                class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400 block mb-1"
                                >Update Informasi</span
                            >
                            <h3
                                class="text-xl font-extrabold text-slate-800 tracking-tight leading-none mb-1.5 group-hover:text-blue-700 transition-colors"
                            >
                                Pengaturan
                            </h3>
                            <p class="text-xs font-medium text-slate-500 mt-1">Ubah Informasi & tahapan rekrutmen</p>
                        </div>
                    </div>
                </a>
            </div>
        @else
            <div
                class="mt-8 rounded-xl border-2 border-dashed border-slate-300 bg-white/60 p-10 text-center shadow-sm"
            >
                <div
                    class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-slate-100 text-slate-400"
                >
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M10.29 3.86l-8.12 14A2 2 0 003.88 21h16.24a2 2 0 001.71-3.14l-8.12-14a2 2 0 00-3.42 0z"></path></svg>
                </div>
                <h3 class="mt-4 text-lg font-extrabold text-slate-800">
                    Belum ada rekrutmen yang aktif
                </h3>
                <p class="mx-auto mt-2 max-w-md text-sm font-medium leading-relaxed text-slate-500">Daftar peserta, daftar panitia, pengaturan informasi, dan pengerjaan seleksi akan tersedia setelah rekrutmen dibuka.</p>
            </div>
        @endif

        <!-- Riwayat Card -->
        <div class="mt-8 pt-8 border-t border-slate-200">
            <a
                href="{{ route('organisasi.riwayat.index') }}"
                class="flex flex-col sm:flex-row sm:items-center justify-between rounded-xl border border-slate-200 bg-white p-4 sm:p-5 shadow-sm transition-all hover:border-blue-300 hover:shadow-md group"
            >
                <div class="flex items-center gap-4">
                    <div
                        class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-slate-50 border border-slate-100 text-slate-500 group-hover:bg-blue-50 group-hover:border-blue-200 group-hover:text-blue-600 transition-colors"
                    >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                    </div>
                    <div>
                        <span
                            class="block text-sm font-extrabold text-slate-800 group-hover:text-blue-700 transition-colors"
                            >Riwayat Rekrutmen</span
                        >
                        <span
                            class="mt-0.5 block text-xs font-medium text-slate-500"
                            >Arsip riwayat rekrutmen yang telah berlalu.</span
                        >
                    </div>
                </div>
                <div
                    class="hidden sm:block text-slate-300 group-hover:text-blue-500 transition-colors"
                >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </div>
            </a>
        </div>
    </div>
</x-app-layout>

<x-sweet-alert />

<script>
    document.addEventListener('DOMContentLoaded', function () {
        @if (session('success_update'))
        Swal.fire({
            icon: 'success',
            title: 'Pembaruan Berhasil!',
            text: '{!!
        session(
            'success_update',
        )
    !!}',
            confirmButtonColor: '#2563eb',
            timer: 4000,
            customClass: {
                popup: 'rounded-xl border border-slate-200 shadow-xl',
                title: 'font-extrabold text-slate-800 tracking-tight',
                confirmButton: 'font-bold rounded-md px-6 py-2.5 text-sm',
            },
        });
        @endif

        @if (session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil',
            text: @json (session('success')),
            confirmButtonColor: '#2563eb',
            customClass: {
                popup: 'rounded-xl border border-slate-200 shadow-xl',
                title: 'font-extrabold text-slate-800 tracking-tight',
                confirmButton: 'font-bold rounded-md px-6 py-2.5 text-sm',
            },
        });
        @endif

        @if (session('error_server'))
        Swal.fire({
            icon: 'info',
            title: 'Informasi',
            text: @json (session('error_server')),
            confirmButtonColor: '#2563eb',
            customClass: {
                popup: 'rounded-xl border border-slate-200 shadow-xl',
                title: 'font-extrabold text-slate-800 tracking-tight',
                confirmButton: 'font-bold rounded-md px-6 py-2.5 text-sm',
            },
        });
        @endif
    });
</script>
