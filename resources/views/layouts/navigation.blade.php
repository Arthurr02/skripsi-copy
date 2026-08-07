@php
    // Menentukan identitas pengguna yang sedang login
    if (Auth::guard('organisasi')->check()) {
        $user = Auth::guard('organisasi')->user();
        $userName = $user->nama_organisasi;
        $userAvatar = $user->avatar_google;
        $userRole = 'Organisasi';
    } else {
        $user = Auth::user();
        $userName = $user->nama_lengkap;
        $userAvatar = $user->avatar_google;
        $userRole = $user->isPanitia() ? 'Panitia Rekrutmen' : 'Mahasiswa';
    }

    // Sistem Fallback Otomatis UI-Avatars
    if (empty($userAvatar)) {
        $urlName = urlencode($userName);
        $userAvatar = "https://ui-avatars.com/api/?name={$urlName}&background=2563eb&color=ffffff&rounded=true&bold=true";
    }
@endphp

<nav
    x-data="{ open: false }"
    class="bg-white border-b border-slate-200 shadow-sm sticky top-0 z-50"
>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-end h-16">
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="group flex items-center gap-3 p-1.5">
                            <div
                                class="flex flex-col text-right justify-center"
                            >
                                <span
                                    class="font-bold text-slate-700 text-sm truncate leading-tight transition-colors"
                                >
                                    {{ $userName }}
                                </span>
                                <span
                                    class="text-[10px] font-extrabold text-blue-600 uppercase tracking-widest mt-0.5 drop-shadow-sm"
                                >
                                    {{ $userRole }}
                                </span>
                            </div>

                            <div class="relative">
                                <img
                                    src="{{ str_replace('http://', 'https://', $userAvatar) }}"
                                    alt="Profil {{ $userName }}"
                                    class="w-9 h-9 rounded-full object-cover border-2 border-slate-200 shadow-sm group-hover:border-blue-200 transition-colors bg-white"
                                    referrerpolicy="no-referrer"
                                    onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($userName) }}&background=2563eb&color=ffffff&rounded=true&bold=true';"
                                />
                                <div
                                    class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-green-500 border-2 border-white rounded-full"
                                ></div>
                            </div>

                            <div
                                class="text-slate-400 group-hover:text-slate-600 transition-colors pe-1"
                            >
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link
                                :href="route('logout')"
                                class="font-bold text-red-600 hover:text-red-700 hover:bg-red-50 flex items-center gap-2 transition-colors py-2.5"
                                onclick="
                                    event.preventDefault();
                                    this.closest('form').submit();
                                "
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                </svg>
                                {{ __('Keluar') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>
        </div>
    </div>
</nav>
