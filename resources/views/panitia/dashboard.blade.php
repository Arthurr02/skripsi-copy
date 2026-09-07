<x-app-layout>
    <div
        class="absolute inset-x-0 top-0 -z-10 h-[400px] overflow-hidden pointer-events-none"
    >
        <div
            class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZyI+PHBhdGggZD0iTTAgMGg0MHY0MEgwVjB6bTIwIDIwaDIwdjIwSDIwaC0yMHptMCAwaC0yMHYtMjBoMjB2MjB6IiBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiIHN0cm9rZT0iI2YxZjVmOSIgc3Ryb2tlLXdpZHRoPSIxIi8+PC9zdmc+')] opacity-60"
        ></div>
        <div
            class="absolute -left-[10%] -top-[20%] h-[60%] w-[40%] rounded-full bg-gradient-to-br from-blue-300/80 to-blue-50/20 blur-[100px]"
        ></div>
        <div
            class="absolute right-[10%] top-[10%] h-[50%] w-[35%] rounded-full bg-gradient-to-bl from-indigo-200/60 to-transparent blur-[120px]"
        ></div>
    </div>

    <div class="relative z-10 mx-auto my-6 max-w-5xl p-4 sm:my-10 sm:p-8">
        <div
            class="mb-8 flex flex-col justify-between gap-6 border-b border-slate-200/60 pb-8 text-center sm:text-left md:flex-row md:items-center"
        >
            <div>
                <h2
                    class="mb-2 text-3xl font-extrabold leading-tight tracking-tight text-slate-800 sm:text-4xl"
                >
                    Dashboard Panitia
                </h2>
                <p class="text-sm font-normal leading-relaxed text-slate-500">
                    Selamat bertugas,
                    <span class="font-bold tracking-wide text-blue-600"
                        >{{ $user->nama_lengkap }}!</span
                    >
                </p>
            </div>
        </div>

        @if ($periodeAktif)
            <div class="mb-6">
                <h3
                    class="flex items-center text-sm font-extrabold tracking-wide text-slate-800"
                >
                    <span class="relative mr-3 flex h-2.5 w-2.5">
                        <span
                            class="absolute inline-flex h-full w-full animate-ping rounded-full bg-blue-400 opacity-75"
                        ></span>
                        <span
                            class="relative inline-flex h-2.5 w-2.5 rounded-full bg-blue-600"
                        ></span>
                    </span>
                    Menu Pintas
                    <span class="ml-1.5 text-blue-600"
                        >| Periode Aktif {{ $periodeAktif->tahun_periode }}</span
                    >
                </h3>
            </div>
            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                <a
                    href="{{ route('panitia.rekrutmen.pendaftar') }}"
                    class="group relative block overflow-hidden rounded-xl border border-slate-200 bg-white p-5 transition-all hover:border-blue-300 hover:shadow-md sm:p-6"
                >
                    <div
                        class="absolute left-0 top-0 h-1 w-full bg-transparent transition-colors group-hover:bg-blue-500"
                    ></div>
                    <div class="flex items-center gap-4">
                        <div
                            class="shrink-0 rounded-lg border border-blue-200 bg-blue-50 p-3 text-blue-600"
                        >
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        </div>
                        <div>
                            <span
                                class="mb-1 block text-[10px] font-extrabold uppercase tracking-widest text-slate-400"
                                >Daftar Peserta</span
                            >
                            <h3
                                class="text-2xl font-extrabold leading-none tracking-tight text-slate-800 transition-colors group-hover:text-blue-700"
                            >
                                {{ $jumlahPeserta }} Pendaftar
                            </h3>
                        </div>
                    </div>
                </a>

                <a
                    href="{{ route('panitia.rekrutmen.seleksi') }}"
                    class="group relative block overflow-hidden rounded-xl border border-slate-200 bg-white p-5 transition-all hover:border-blue-300 hover:shadow-md sm:p-6"
                >
                    <div
                        class="absolute left-0 top-0 h-1 w-full bg-transparent transition-colors group-hover:bg-blue-500"
                    ></div>
                    <div class="flex items-start gap-4">
                        <div
                            class="shrink-0 rounded-lg border border-blue-200 bg-blue-50 p-3 text-blue-600"
                        >
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2zm0 0V9a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v10m-6 0a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2m0 0V5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-2a2 2 0 0 1-2-2z"></path></svg>
                        </div>
                        <div>
                            <span
                                class="mb-1 block text-[10px] font-extrabold uppercase tracking-widest text-slate-400"
                                >Pengerjaan Seleksi</span
                            >
                            <h3
                                class="mb-1 text-lg font-extrabold leading-tight tracking-tight text-slate-800 transition-colors group-hover:text-blue-700"
                            >
                                @if ($tahapanSeleksiSaatIni)
                                    Tahap {{ $tahapanSeleksiSaatIni->urutan_tahapan }}: {{ $tahapanSeleksiSaatIni->nama_tahapan }}
                                @else
                                    Belum ada seleksi berlangsung
                                @endif
                            </h3>
                            <p class="mt-1 text-xs font-medium text-slate-500">Tahapan seleksi saat ini.</p>
                        </div>
                    </div>
                </a>

                <a
                    href="{{ route('panitia.rekrutmen.update') }}"
                    class="group relative block overflow-hidden rounded-xl border border-slate-200 bg-white p-5 transition-all hover:border-blue-300 hover:shadow-md sm:p-6 md:col-span-2"
                >
                    <div
                        class="absolute left-0 top-0 h-1 w-full bg-transparent transition-colors group-hover:bg-blue-500"
                    ></div>
                    <div class="flex items-start gap-4">
                        <div
                            class="shrink-0 rounded-lg border border-blue-200 bg-blue-50 p-3 text-blue-600"
                        >
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 0 0-2 2v11a2 2 0 0 0 2 2h11a2 2 0 0 0 2-2v-5m-1.414-9.414a2 2 0 1 1 2.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        </div>
                        <div>
                            <span
                                class="mb-1 block text-[10px] font-extrabold uppercase tracking-widest text-slate-400"
                                >Update Informasi</span
                            >
                            <h3
                                class="mb-1.5 text-xl font-extrabold leading-none tracking-tight text-slate-800 transition-colors group-hover:text-blue-700"
                            >
                                Pengaturan
                            </h3>
                            <p class="mt-1 text-xs font-medium text-slate-500">Ubah informasi dan tahapan rekrutmen aktif.</p>
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
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M10.29 3.86l-8.12 14A2 2 0 0 0 3.88 21h16.24a2 2 0 0 0 1.71-3.14l-8.12-14a2 2 0 0 0-3.42 0z"></path></svg>
                </div>
                <h3 class="mt-4 text-lg font-extrabold text-slate-800">
                    Belum ada rekrutmen yang aktif
                </h3>
                <p class="mx-auto mt-2 max-w-md text-sm font-medium leading-relaxed text-slate-500">Menu pengelolaan peserta, informasi, dan pengerjaan seleksi tersedia ketika Anda terdaftar pada rekrutmen aktif.</p>
            </div>
        @endif

        <div class="mt-8 border-t border-slate-200 pt-8">
            <a
                href="{{ route('panitia.riwayat.index') }}"
                class="group flex flex-col justify-between gap-4 rounded-xl border border-slate-200 bg-white p-4 shadow-sm transition-all hover:border-blue-300 hover:shadow-md sm:flex-row sm:items-center sm:p-5"
            >
                <div class="flex items-center gap-4">
                    <div
                        class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg border border-slate-100 bg-slate-50 text-slate-500 transition-colors group-hover:border-blue-200 group-hover:bg-blue-50 group-hover:text-blue-600"
                    >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 1 0 0-4h14a2 2 0 1 0 0 4M5 8v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8m-9 4h4"></path></svg>
                    </div>
                    <div>
                        <span
                            class="block text-sm font-extrabold text-slate-800 transition-colors group-hover:text-blue-700"
                            >Riwayat Rekrutmen</span
                        >
                        <span
                            class="mt-0.5 block text-xs font-medium text-slate-500"
                            >Arsip rekrutmen yang telah berlalu.</span
                        >
                    </div>
                </div>
                <svg class="hidden h-5 w-5 text-slate-300 transition-colors group-hover:text-blue-500 sm:block" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 5 7 7-7 7"></path></svg>
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
            text: @json (session('success_update')),
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
