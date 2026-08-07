@php
    // Tentukan Identitas Login
    $isOrganisasi = Auth::guard('organisasi')->check();
    $isPanitia = false;
    $isMahasiswaBiasa = false;

    // Ambil data user yang sedang aktif untuk User Context
    $currentUser = Auth::user() ?? Auth::guard('organisasi')->user();

    // Tentukan Rute Dashboard dan Prefix Rute Dinamis
    if ($isOrganisasi) {
        $dashboardRoute = 'organisasi.dashboard';
        $routePrefix = 'organisasi.';
    } else {
        $isPanitia = $currentUser ? $currentUser->isPanitia() : false;
        $isMahasiswaBiasa = !$isPanitia;

        $dashboardRoute = $isPanitia ? 'panitia.dashboard' : 'mahasiswa.dashboard';
        $routePrefix = $isPanitia ? 'panitia.' : 'mahasiswa.';
    }
@endphp

<aside
    class="fixed inset-y-0 left-0 w-64 bg-white border-r border-slate-200 z-50 hidden md:flex md:flex-col shadow-[4px_0_24px_rgba(0,0,0,0.02)]"
>
    <div
        class="flex items-center px-6 h-16 border-b border-slate-100 bg-slate-50/50 shrink-0"
    >
        <a
            href="{{ route($dashboardRoute) }}"
            class="text-xl font-extrabold text-slate-800 tracking-tight flex items-center gap-2.5 hover:opacity-80 transition-opacity"
        >
            Sistem Rekrutmen
        </a>
    </div>

    <nav
        class="flex-1 py-5 space-y-1 overflow-y-auto font-medium custom-scrollbar"
    >
        @if ($isOrganisasi || $isPanitia)
            <div class="px-3">
                <a
                    href="{{ route($dashboardRoute) }}"
                    class="flex items-center px-3 py-2.5 rounded-lg transition-all duration-200 border-l-[3px] 
                    {{ request()->routeIs($dashboardRoute) ? 'bg-blue-50 text-blue-700 border-blue-600 font-bold' : 'border-transparent text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}"
                >
                    <svg
                        class="w-5 h-5 mr-3 {{ request()->routeIs($dashboardRoute) ? 'text-blue-600' : 'text-slate-400' }}"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                    </svg>
                    Dashboard
                </a>
            </div>
        @endif

        @if ($isMahasiswaBiasa && !$isOrganisasi)
            <div class="px-3 space-y-1">
                <a
                    href="{{ route('mahasiswa.rekrutmen.index') }}"
                    class="flex items-center px-3 py-2.5 rounded-lg transition-all duration-200 border-l-[3px]
                    {{ request()->routeIs('mahasiswa.rekrutmen.index', 'mahasiswa.rekrutmen.info', 'mahasiswa.rekrutmen.daftar') ? 'bg-blue-50 text-blue-700 border-blue-600 font-bold' : 'border-transparent text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}"
                >
                    <svg
                        class="w-5 h-5 mr-3 {{ request()->routeIs('mahasiswa.rekrutmen.index', 'mahasiswa.rekrutmen.info', 'mahasiswa.rekrutmen.daftar') ? 'text-blue-600' : 'text-slate-400' }}"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path>
                    </svg>
                    Daftar Rekrutmen
                </a>

                <a
                    href="{{ route('mahasiswa.rekrutmen.diikuti.index') }}"
                    class="flex items-center px-3 py-2.5 rounded-lg transition-all duration-200 border-l-[3px]
                    {{ request()->routeIs('mahasiswa.rekrutmen.diikuti*') ? 'bg-blue-50 text-blue-700 border-blue-600 font-bold' : 'border-transparent text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}"
                >
                    <svg
                        class="w-5 h-5 mr-3 {{ request()->routeIs('mahasiswa.rekrutmen.diikuti*') ? 'text-blue-600' : 'text-slate-400' }}"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                    </svg>
                    Rekrutmen Diikuti
                </a>

                <a
                    href="{{ route('mahasiswa.riwayat.index') }}"
                    class="flex items-center px-3 py-2.5 rounded-lg transition-all duration-200 border-l-[3px]
                    {{ request()->routeIs('mahasiswa.riwayat*') ? 'bg-blue-50 text-blue-700 border-blue-600 font-bold' : 'border-transparent text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}"
                >
                    <svg
                        class="w-5 h-5 mr-3 {{ request()->routeIs('mahasiswa.riwayat*') ? 'text-blue-600' : 'text-slate-400' }}"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Riwayat Pendaftaran
                </a>
            </div>
        @endif

        @if ($isOrganisasi)
            <div class="pt-5 pb-1.5 px-6">
                <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Manajemen Rekrutmen</p>
            </div>
            <div class="px-3">
                <a
                    href="{{ route('organisasi.buka-rekrutmen.index') }}"
                    class="flex items-center px-3 py-2.5 rounded-lg transition-all duration-200 border-l-[3px]
                    {{ request()->routeIs('organisasi.buka-rekrutmen.*') ? 'bg-blue-50 text-blue-700 border-blue-600 font-bold' : 'border-transparent text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}"
                >
                    <svg
                        class="w-5 h-5 mr-3 {{ request()->routeIs('organisasi.buka-rekrutmen.*') ? 'text-blue-600' : 'text-slate-400' }}"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path>
                    </svg>
                    Buka Rekrutmen
                </a>
            </div>
        @endif

        @if ($isOrganisasi || $isPanitia)
            @if (!$isOrganisasi)
                <div class="pt-5 pb-1.5 px-6">
                    <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Manajemen Rekrutmen</p>
                </div>
            @endif
            <div
                x-data="{ openRekrutmen: {{ request()->routeIs($routePrefix . 'rekrutmen.*') ? 'false' : 'true' }} }"
                class="px-3 space-y-1"
            >
                <button
                    @click="openRekrutmen = !openRekrutmen"
                    :aria-expanded="openRekrutmen.toString()"
                    class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg transition-all duration-200 focus:outline-none border-l-[3px]
                    {{ request()->routeIs($routePrefix . 'rekrutmen.*') ? 'border-transparent text-slate-900 bg-slate-50/80' : 'border-transparent text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}"
                >
                    <div class="flex items-center">
                        <svg
                            class="w-5 h-5 mr-3 {{ request()->routeIs($routePrefix . 'rekrutmen.*') ? 'text-blue-500' : 'text-slate-400' }}"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <span
                            class="{{ request()->routeIs($routePrefix . 'rekrutmen.*') ? 'font-bold' : '' }}"
                            >Rekrutmen Saat Ini</span
                        >
                    </div>
                    <svg
                        class="w-4 h-4 transition-transform duration-300 text-slate-400"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>

                <div
                    x-show="true"
                    x-collapse
                    class="ml-[1.35rem] pl-4 border-l-2 border-slate-100 space-y-1 mt-1"
                >
                    <a
                        href="{{ route($routePrefix . 'rekrutmen.update') }}"
                        class="block py-2 px-3 text-[13px] rounded-lg transition-colors {{ request()->routeIs($routePrefix . 'rekrutmen.update') ? 'text-blue-700 bg-blue-50/80 font-bold shadow-sm' : 'text-slate-500 hover:text-slate-900 hover:bg-slate-50' }}"
                    >
                        Update Informasi
                    </a>
                    <a
                        href="{{ route($routePrefix . 'rekrutmen.pendaftar') }}"
                        class="block py-2 px-3 text-[13px] rounded-lg transition-colors {{ request()->routeIs($routePrefix . 'rekrutmen.pendaftar') ? 'text-blue-700 bg-blue-50/80 font-bold shadow-sm' : 'text-slate-500 hover:text-slate-900 hover:bg-slate-50' }}"
                    >
                        Daftar Peserta
                    </a>
                    <a
                        href="{{ route($routePrefix . 'rekrutmen.seleksi') }}"
                        class="block py-2 px-3 text-[13px] rounded-lg transition-colors {{ request()->routeIs($routePrefix . 'rekrutmen.seleksi') ? 'text-blue-700 bg-blue-50/80 font-bold shadow-sm' : 'text-slate-500 hover:text-slate-900 hover:bg-slate-50' }}"
                    >
                        Pengerjaan Seleksi
                    </a>
                </div>
            </div>
            <div class="pt-5 pb-1.5 px-6">
                <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Arsip Data</p>
            </div>
            <div class="px-3">
                <a
                    href="{{ route($routePrefix . 'riwayat.index') }}"
                    class="flex items-center px-3 py-2.5 rounded-lg transition-all duration-200 border-l-[3px]
                    {{ request()->routeIs($routePrefix . 'riwayat.*') ? 'bg-blue-50 text-blue-700 border-blue-600 font-bold' : 'border-transparent text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}"
                >
                    <svg
                        class="w-5 h-5 mr-3 {{ request()->routeIs($routePrefix . 'riwayat.*') ? 'text-blue-600' : 'text-slate-400' }}"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                    </svg>
                    Riwayat Rekrutmen
                </a>
            </div>
        @endif
    </nav>

    <div class="border-t border-slate-200 bg-slate-50/30 p-4 shrink-0">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button
                type="submit"
                class="w-full flex items-center justify-center px-4 py-2.5 rounded-lg text-sm font-bold transition-all text-slate-600 bg-white border border-slate-200 shadow-sm hover:bg-red-50 hover:text-red-700 hover:border-red-200 group"
            >
                <svg class="w-4 h-4 mr-2 text-slate-400 group-hover:text-red-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                </svg>
                Keluar
            </button>
        </form>
    </div>
</aside>

<style>
    /* Custom CSS untuk menyembunyikan scrollbar default tapi tetap bisa di-scroll (Premium Look) */
    .custom-scrollbar::-webkit-scrollbar {
        width: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background-color: #cbd5e1; /* slate-300 */
        border-radius: 10px;
    }
    .custom-scrollbar:hover::-webkit-scrollbar-thumb {
        background-color: #94a3b8; /* slate-400 */
    }
</style>
