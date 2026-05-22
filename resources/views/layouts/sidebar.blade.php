@php
    if (Auth::guard('organisasi')->check()) {
        $dashboardRoute = 'organisasi.dashboard';
    } else {
        $user = Auth::user();
        $dashboardRoute = $user->isAnggota()
            ? 'panitia.dashboard'
            : 'mahasiswa.dashboard';
    }
@endphp

<aside
    class="fixed inset-y-0 left-0 w-64 bg-white border-r border-gray-200 z-50 hidden md:flex md:flex-col"
>
    <div class="flex items-center justify-center h-16 border-b border-gray-200">
        <a
            href="{{ route($dashboardRoute) }}"
            class="text-xl font-bold text-gray-800"
        >
            Sistem Rekrutmen
        </a>
    </div>

    <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
        <a
            href="{{ route($dashboardRoute) }}"
            class="flex items-center px-4 py-3 rounded-lg font-medium transition-colors 
                  {{ request()->routeIs($dashboardRoute) ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}"
        >
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
            </svg>
            Dashboard
        </a>

        @if (Auth::guard('organisasi')->check())
            <div class="pt-4 pb-2">
                <p class="px-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Manajemen</p>
            </div>
            <a
                href="{{ route('organisasi.periode.inisiasi') }}"
                class="flex items-center px-4 py-3 rounded-lg font-medium transition-colors 
                      {{ request()->routeIs('organisasi.periode.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}"
            >
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                </svg>
                Buka Rekrutmen
            </a>
            <div
                x-data="{ openRekrutmen: {{ request()->routeIs('organisasi.rekrutmen.*') ? 'true' : 'false' }} }"
                class="space-y-1"
            >
                <button
                    @click="openRekrutmen = !openRekrutmen"
                    class="w-full flex items-center justify-between px-4 py-3 rounded-lg font-medium transition-colors focus:outline-none text-gray-600 hover:bg-gray-100 hover:text-gray-900"
                >
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        Rekrutmen Saat Ini
                    </div>
                    <svg :class="{
                            'rotate-180': openRekrutmen,
                        }" class="w-4 h-4 transition-transform duration-200 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>

                <div
                    x-show="openRekrutmen"
                    x-transition
                    class="pl-11 pr-4 space-y-1 mt-1 pb-2"
                >
                    <a
                        href="{{ route('organisasi.rekrutmen.update') }}"
                        class="block py-2 text-sm rounded-md transition-colors
                          {{ request()->routeIs('organisasi.rekrutmen.update') ? 'text-blue-600 font-bold' : 'text-gray-500 hover:text-gray-900' }}"
                    >
                        Update Informasi
                    </a>
                    <a
                        href="{{ route('organisasi.rekrutmen.pendaftar') }}"
                        class="block py-2 text-sm rounded-md transition-colors
                              {{ request()->routeIs('organisasi.rekrutmen.pendaftar') ? 'text-blue-600 font-bold' : 'text-gray-500 hover:text-gray-900' }}"
                    >
                        Pendaftar
                    </a>
                    <a
                        href="{{ route('organisasi.rekrutmen.tahapan') }}"
                        class="block py-2 text-sm rounded-md transition-colors
                              {{ request()->routeIs('organisasi.rekrutmen.tahapan') ? 'text-blue-600 font-bold' : 'text-gray-500 hover:text-gray-900' }}"
                    >
                        Pengerjaan Seleksi
                    </a>
                </div>
            </div>
        @endif
    </nav>
</aside>
