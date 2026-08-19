<!-- Navigasi Pintas -->
<nav
    class="inline-flex items-center gap-2 px-3 py-1.5 rounded-md bg-slate-800 border border-slate-700 text-slate-300 text-xs font-bold mb-6 hover:bg-slate-700 transition-colors shadow-sm"
>
    <a
        href="{{ route('mahasiswa.rekrutmen.info', $rekrutmen->id) }}"
        class="flex items-center gap-1.5 hover:text-white transition-colors"
    >
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        Kembali ke Detail Informasi
    </a>
</nav>

<!-- Header Profil -->
<div
    class="flex flex-col md:flex-row items-center md:items-start gap-5 text-center md:text-left mb-10"
>
    <div class="shrink-0">
        @if (!empty($avatarUrl))
            <img
                src="{{ $avatarUrl }}"
                alt="Logo"
                class="w-16 h-16 md:w-20 md:h-20 rounded-full object-cover bg-white p-0.5 border border-slate-200 shadow-sm"
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
    <div class="flex-1 mt-2 md:mt-0">
        <h1
            class="text-3xl md:text-4xl font-extrabold text-white tracking-tight leading-tight mb-1.5"
        >
            Formulir Pendaftaran
        </h1>
        <p class="text-sm font-medium text-slate-300">
            {{ $namaOrganisasi }} &bull; {{
                $rekrutmen->slogan ??
                    'Penerimaan Anggota Baru'
            }}
        </p>
    </div>
</div>
