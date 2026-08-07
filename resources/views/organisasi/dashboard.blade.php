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
            class="mb-10 relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-8"
        >
            <div>
                <!-- Tipografi Solid Slate -->
                <h2
                    class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-800 mb-2.5 leading-tight"
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
            <div class="shrink-0 flex flex-col md:items-end">
                <a
                    href="{{ route('organisasi.buka-rekrutmen.index') }}"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3.5 rounded-lg font-bold text-sm transition-colors shadow-sm flex items-center justify-center gap-2 w-full md:w-auto"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Buka Rekrutmen Baru
                </a>
            </div>
        </div>

        <!-- MENU PINTAS & KARTU -->
        <div class="mb-6 mt-8">
            <h3
                class="text-lg font-extrabold text-slate-800 mb-4 flex items-center tracking-tight"
            >
                <!-- Indikator hijau dengan animasi ping agar selaras dengan desain titik biru -->
                <span class="relative flex h-2.5 w-2.5 mr-2.5">
                    <span
                        class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"
                    ></span>
                    <span
                        class="relative inline-flex rounded-full h-2.5 w-2.5 bg-green-500"
                    ></span>
                </span>
                Menu Pintas Rekrutmen
                <span class="text-blue-600 ml-1.5">Periode Aktif</span>
            </h3>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Kartu 1: Daftar Peserta -->
            <a
                href="{{ route('organisasi.rekrutmen.pendaftar') }}"
                class="block bg-white p-6 rounded-2xl border border-slate-200 hover:border-blue-500 hover:bg-blue-50 transition-colors shadow-sm group"
            >
                <span
                    class="text-xs font-bold uppercase tracking-wider text-blue-600 mb-4 block"
                >
                    Daftar Peserta
                </span>
                <div class="flex items-start gap-4">
                    <div
                        class="p-3 bg-blue-100 text-blue-600 rounded-lg shrink-0"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3
                            class="text-2xl font-black text-slate-800 group-hover:text-blue-700 tracking-tight"
                        >
                            0
                        </h3>
                        <p class="text-xs font-medium text-slate-500 mt-1">Total pendaftar saat ini</p>
                    </div>
                </div>
            </a>

            <!-- Kartu 2: Pengerjaan Seleksi -->
            <a
                href="{{ route('organisasi.rekrutmen.seleksi') }}"
                class="block bg-white p-6 rounded-2xl border border-slate-200 hover:border-yellow-500 hover:bg-yellow-50 transition-colors shadow-sm group"
            >
                <span
                    class="text-xs font-bold uppercase tracking-wider text-yellow-600 mb-4 block"
                >
                    Pengerjaan Seleksi
                </span>
                <div class="flex items-start gap-4">
                    <div
                        class="p-3 bg-yellow-100 text-yellow-600 rounded-lg shrink-0"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3
                            class="text-2xl font-black text-slate-800 group-hover:text-yellow-700 tracking-tight"
                        >
                            Pendaftaran
                        </h3>
                        <p class="text-xs font-medium text-slate-500 mt-1">Tahapan seleksi saat ini</p>
                    </div>
                </div>
            </a>

            <!-- Kartu 3: Update Informasi -->
            <a
                href="{{ route('organisasi.rekrutmen.update') }}"
                class="block bg-white p-6 rounded-2xl border border-slate-200 hover:border-green-500 hover:bg-green-50 transition-colors shadow-sm group"
            >
                <span
                    class="text-xs font-bold uppercase tracking-wider text-green-600 mb-4 block"
                >
                    Update Informasi
                </span>
                <div class="flex items-start gap-4">
                    <div
                        class="p-3 bg-green-100 text-green-600 rounded-lg shrink-0"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3
                            class="text-2xl font-black text-slate-800 group-hover:text-green-700 tracking-tight"
                        >
                            Pengaturan
                        </h3>
                        <p class="text-xs font-medium text-slate-500 mt-1">Ubah informasi rekrutmen</p>
                    </div>
                </div>
            </a>
        </div>
    </div>
</x-app-layout>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
                popup: 'rounded-lg border border-slate-200',
                title: 'font-extrabold text-slate-800 tracking-tight',
                confirmButton: 'font-bold rounded-md',
            },
        });
        @endif
    });
</script>
