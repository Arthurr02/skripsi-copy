<x-app-layout>
    <!-- Background Aksen Atas -->
    <div
        class="absolute top-0 inset-x-0 h-[400px] overflow-hidden pointer-events-none -z-10 bg-slate-50"
    >
        <div
            class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PHBhdGggZD0iTTAgMGg0MHY0MEgwVjB6bTIwIDIwaDIwdjIwSDIwaC0yMHptMCAwaC0yMHYtMjBoMjB2MjB6IiBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiIHN0cm9rZT0iI2YxZjVmOSIgc3Ryb2tlLXdpZHRoPSIxIi8+PC9zdmc+')] opacity-60"
        ></div>
        <div
            class="absolute -top-[20%] -left-[10%] w-[40%] h-[60%] rounded-full bg-gradient-to-br from-blue-200/80 to-blue-50/20 blur-[100px]"
        ></div>
        <div
            class="absolute top-[10%] right-[10%] w-[35%] h-[50%] rounded-full bg-gradient-to-bl from-indigo-200/60 to-transparent blur-[120px]"
        ></div>
    </div>

    <div class="p-4 sm:p-8 max-w-5xl mx-auto relative z-10 my-6 sm:my-10 pb-24">
        <!-- HEADER SELARAS -->
        <div
            class="mb-8 relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6"
        >
            <div>
                <h2
                    class="text-3xl sm:text-4xl font-extrabold text-slate-800 tracking-tight mb-2"
                >
                    Daftar Rekrutmen
                </h2>
                <p class="text-sm md:text-base text-slate-500 max-w-2xl leading-relaxed">Temukan dan ikuti rekrutmen kepanitiaan atau organisasi tingkat kampus yang sedang berlangsung saat ini.</p>
            </div>
        </div>

        @if ($rekrutmenAktif->isEmpty())
            <!-- Kondisi Kosong (Empty State) -->
            <div
                class="bg-white rounded-2xl border border-slate-200 p-12 flex flex-col items-center justify-center text-center shadow-sm"
            >
                <div class="p-4 bg-blue-50 text-blue-600 rounded-full mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-slate-800 mb-1">
                    Belum Ada Rekrutmen
                </h3>
                <p class="text-slate-500 text-sm max-w-md">Saat ini tidak ada organisasi yang sedang membuka pendaftaran. Silakan periksa kembali halaman ini secara berkala.</p>
            </div>
        @else
            <!-- Daftar Rekrutmen (Grid Cards Ergonomis) -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($rekrutmenAktif as $rekrutmen)
                    @php
                        // Logika Identitas & URL
                        $organisasi = $rekrutmen->organisasi;
                        $namaOrganisasi = $organisasi->nama_organisasi ?? 'Organisasi';

                        $avatarUrl = '';
                        if ($organisasi) {
                            if (!empty($organisasi->avatar_google)) {
                                $avatarUrl = str_replace(
                                    'http://',
                                    'https://',
                                    $organisasi->avatar_google,
                                );
                            } elseif (!empty($organisasi->lampiran_logo)) {
                                $avatarUrl = asset('storage/' . $organisasi->lampiran_logo);
                            }
                        }

                        $bannerData = $rekrutmen->lampiran_banner;
                        $bannerArray = is_string($bannerData)
                            ? json_decode($bannerData, true)
                            : $bannerData;
                        $bannerPath =
                            is_array($bannerArray) && count($bannerArray) > 0 ? $bannerArray[0] : null;
                    @endphp
                    <div
                        class="group bg-white rounded-lg border border-slate-200 overflow-hidden flex flex-col hover:border-blue-500 transition-all duration-300 shadow-sm hover:shadow-md"
                    >
                        <!-- 1. Banner Area (Proporsi Ringkas h-32) -->
                        <div
                            class="relative h-32 bg-slate-900 overflow-hidden shrink-0"
                        >
                            @if ($bannerPath)
                                <img
                                    src="{{ asset('storage/' . $bannerPath) }}"
                                    alt="Banner Rekrutmen"
                                    class="absolute inset-0 w-full h-full object-cover opacity-90 transition-transform duration-500 group-hover:scale-105"
                                />
                            @else
                                <div class="absolute inset-0 opacity-20">
                                    <svg class="w-full h-full" xmlns="http://www.w3.org/2000/svg">
                                        <defs>
                                            <pattern
                                                id="card-grid-{{ $rekrutmen->id }}"
                                                width="24"
                                                height="24"
                                                patternUnits="userSpaceOnUse"
                                            >
                                                <path d="M 24 0 L 0 0 0 24" fill="none" stroke="currentColor" class="text-white" stroke-width="1" />
                                            </pattern>
                                        </defs>
                                        <rect
                                            width="100%"
                                            height="100%"
                                            fill="url(#card-grid-{{ $rekrutmen->id }})"
                                        />
                                    </svg>
                                </div>
                            @endif
                        </div>

                        <!-- 2. Card Body (Tata Letak Padat & Nyaman) -->
                        <div
                            class="pb-5 px-5 flex-1 flex flex-col justify-between bg-white"
                        >
                            <div class="relative">
                                <!-- Baris Identitas Organisasi: Logo + Nama (1 Baris Sejajar) -->
                                <div
                                    class="flex items-center gap-3 mb-3.5 absolute -top-1/2 h-full"
                                >
                                    <div class="flex items-end gap-3">
                                        <div class="shrink-0">
                                            @if (!empty($avatarUrl))
                                                <img
                                                    src="{{ $avatarUrl }}"
                                                    alt="Logo {{ $namaOrganisasi }}"
                                                    class="w-14 h-14 rounded-full object-cover border border-slate-200 bg-white shadow-sm"
                                                    referrerpolicy="no-referrer"
                                                    onerror="this.style.display='none'; document.getElementById('card-avatar-fallback-{{ $rekrutmen->id }}').style.display='flex';"
                                                />
                                            @endif
                                            <div
                                                id="card-avatar-fallback-{{ $rekrutmen->id }}"
                                                style="{{ !empty($avatarUrl) ? 'display: none;' : 'display: flex;' }}"
                                                class="w-14 h-14 rounded-lg bg-blue-600 text-white flex items-center justify-center text-xs font-black uppercase shadow-sm select-none"
                                            >
                                                {{
                                                    substr(
                                                        $namaOrganisasi,
                                                        0,
                                                        1,
                                                    )
                                                }}
                                            </div>
                                        </div>
                                        <div
                                            class="flex min-w-0 h-7 items-center"
                                        >
                                            <p class="text-[11px] font-extrabold text-blue-600 uppercase tracking-wider truncate">
                                                {{ $namaOrganisasi }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Judul Slogan Rekrutmen -->
                                <h3
                                    class="pt-8 text-base font-extrabold text-slate-800 leading-snug group-hover:text-blue-600 transition-colors my-2 line-clamp-2"
                                >
                                    {{
                                        $rekrutmen->slogan ??
                                            'Penerimaan Anggota Baru Tahun ' .
                                                \Carbon\Carbon::parse($rekrutmen->created_at)->format(
                                                    'Y',
                                                )
                                    }}
                                </h3>

                                <!-- Deskripsi Ringkas (Max 2 Baris) -->
                                <p class="text-xs text-slate-500 leading-relaxed line-clamp-2 mb-4">
                                    {{
                                        $rekrutmen->deskripsi ??
                                            'Mari bergabung bersama kami untuk mengembangkan potensi dan berkontribusi secara nyata.'
                                    }}
                                </p>
                            </div>

                            <!-- 3. Tombol Aksi Sejajar (Side-by-Side: Menghemat Ruang Vertikal) -->
                            <div
                                class="grid grid-cols-2 gap-2 pt-3 border-t border-slate-100 mt-auto"
                            >
                                <a
                                    href="{{ route('mahasiswa.rekrutmen.info', $rekrutmen->id) }}"
                                    class="flex items-center justify-center py-2 px-3 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-lg transition-colors text-center"
                                >
                                    Detail Info
                                </a>
                                <a
                                    href="{{ route('mahasiswa.rekrutmen.daftar', $rekrutmen->id) }}"
                                    class="flex items-center justify-center py-2 px-3 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg transition-colors text-center gap-1 shadow-sm"
                                >
                                    Daftar
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-app-layout>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const premiumSwal = Swal.mixin({
            customClass: {
                popup: 'rounded-2xl shadow-sm border border-slate-200 font-sans p-6',
                title: 'text-xl font-extrabold text-slate-800 tracking-tight',
                htmlContainer: 'text-sm font-normal text-slate-500',
                confirmButton:
                    'px-6 py-2.5 rounded-xl font-bold text-sm bg-blue-600 hover:bg-blue-700 text-white transition-colors',
            },
            buttonsStyling: false,
        });

        @if (session('success'))
        premiumSwal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: '{{
        session(
            'success',
        )
    }}',
        });
        @endif

        @if (session('error_server'))
        premiumSwal.fire({
            icon: 'error',
            title: 'Akses Ditolak',
            text: '{{
        session(
            'error_server',
        )
    }}',
            customClass: {
                popup: 'rounded-2xl shadow-sm border border-slate-200 font-sans p-6',
                title: 'text-xl font-extrabold text-slate-800 tracking-tight',
                htmlContainer: 'text-sm font-normal text-slate-500',
                confirmButton:
                    'px-6 py-2.5 rounded-xl font-bold text-sm bg-red-600 hover:bg-red-700 text-white transition-colors',
            },
        });
        @endif
    });
</script>
