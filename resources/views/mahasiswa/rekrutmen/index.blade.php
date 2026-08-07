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
                <h2
                    class="text-3xl sm:text-4xl font-extrabold text-slate-800 tracking-tight mb-3"
                >
                    Daftar Rekrutmen
                </h2>
                <p class="text-sm md:text-base text-slate-500 max-w-2xl leading-relaxed mx-auto md:mx-0">Temukan dan ikuti rekrutmen kepanitiaan atau organisasi tingkat kampus yang sedang berlangsung saat ini.</p>
            </div>
        </div>

        @if ($rekrutmenAktif->isEmpty())
            <!-- Kondisi Kosong (Empty State) -->
            <div
                class="bg-white rounded-lg border border-slate-200 p-16 flex flex-col items-center justify-center text-center shadow-sm"
            >
                <div class="p-5 bg-blue-50 text-blue-600 rounded-full mb-5">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-slate-800 mb-2">
                    Belum Ada Rekrutmen
                </h3>
                <p class="text-slate-500 text-sm max-w-md">Saat ini tidak ada organisasi yang sedang membuka pendaftaran. Silakan periksa kembali halaman ini secara berkala.</p>
            </div>
        @else
            <!-- Daftar Rekrutmen (Grid Cards) -->
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
                        class="group bg-white rounded-lg border border-slate-200 overflow-hidden flex flex-col hover:border-blue-500 transition-colors shadow-sm"
                    >
                        <!-- Banner Area -->
                        <div
                            class="relative h-40 bg-slate-100 border-b border-slate-200 overflow-hidden"
                        >
                            @if ($bannerPath)
                                <img
                                    src="{{ asset('storage/' . $bannerPath) }}"
                                    alt="Banner Rekrutmen"
                                    class="absolute inset-0 w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
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
                                                <path d="M 24 0 L 0 0 0 24" fill="none" stroke="currentColor" class="text-slate-900" stroke-width="1" />
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

                        <!-- Card Body -->
                        <div class="p-6 flex-1 flex flex-col bg-white relative">
                            <!-- Avatar Overlapping -->
                            <div class="absolute -top-8 left-6">
                                @if (!empty($avatarUrl))
                                    <img
                                        src="{{ $avatarUrl }}"
                                        alt="Logo {{ $namaOrganisasi }}"
                                        class="w-14 h-14 rounded-full object-cover shadow-sm border border-slate-200 bg-white"
                                        referrerpolicy="no-referrer"
                                        onerror="this.style.display='none'; document.getElementById('card-avatar-fallback-{{ $rekrutmen->id }}').style.display='flex';"
                                    />
                                @endif
                                <div
                                    id="card-avatar-fallback-{{ $rekrutmen->id }}"
                                    style="{{ !empty($avatarUrl) ? 'display: none;' : 'display: flex;' }}"
                                    class="w-14 h-14 rounded-md bg-blue-600 text-white flex items-center justify-center text-xl font-black uppercase shadow-sm border border-blue-700 select-none"
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

                            <!-- Texts -->
                            <div class="mt-2 mb-3">
                                <span
                                    class="block text-[11px] font-bold text-blue-600 uppercase tracking-wider mb-8 truncate"
                                >
                                    {{ $namaOrganisasi }}
                                </span>
                                <h3
                                    class="text-lg font-extrabold text-slate-800 leading-tight group-hover:text-blue-700 transition-colors"
                                >
                                    {{
                                        $rekrutmen->slogan ??
                                            'Penerimaan Anggota Baru Tahun ' .
                                                \Carbon\Carbon::parse($rekrutmen->created_at)->format(
                                                    'Y',
                                                )
                                    }}
                                </h3>
                            </div>

                            <p class="text-sm text-slate-500 line-clamp-3 mb-6 flex-1 leading-relaxed">
                                {{
                                    $rekrutmen->deskripsi ??
                                        'Mari bergabung bersama kami untuk mengembangkan potensi dan berkontribusi secara nyata.'
                                }}
                            </p>

                            <!-- Actions -->
                            <div class="flex flex-col gap-2.5 mt-auto">
                                <a
                                    href="{{ route('mahasiswa.rekrutmen.info', $rekrutmen->id) }}"
                                    class="w-full flex items-center justify-center py-2.5 px-4 bg-white border border-slate-300 text-sm font-bold text-slate-700 rounded-md hover:bg-slate-50 hover:text-blue-600 hover:border-blue-400 transition-colors"
                                >
                                    Lihat Detail Info
                                </a>
                                <a
                                    href="{{ route('mahasiswa.rekrutmen.daftar', $rekrutmen->id) }}"
                                    class="w-full flex items-center justify-center py-2.5 px-4 bg-blue-600 text-sm font-bold text-white rounded-md hover:bg-blue-700 transition-colors"
                                >
                                    Mulai Pendaftaran
                                    <svg class="w-4 h-4 ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
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
        // Konfigurasi Standar Premium Flat Design untuk SweetAlert
        const premiumSwal = Swal.mixin({
            customClass: {
                popup: 'rounded-lg shadow-sm border border-slate-200 font-sans',
                title: 'text-xl font-extrabold text-slate-800 tracking-tight',
                htmlContainer: 'text-sm font-normal text-slate-500',
                confirmButton:
                    'px-6 py-2.5 rounded-md font-bold text-sm bg-blue-600 hover:bg-blue-700 text-white transition-colors',
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
                popup: 'rounded-lg shadow-sm border border-slate-200 font-sans',
                title: 'text-xl font-extrabold text-slate-800 tracking-tight',
                htmlContainer: 'text-sm font-normal text-slate-500',
                confirmButton:
                    'px-6 py-2.5 rounded-md font-bold text-sm bg-red-600 hover:bg-red-700 text-white transition-colors',
            },
        });
        @endif
    });
</script>
