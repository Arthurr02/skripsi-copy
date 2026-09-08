<x-app-layout>
    <!-- Background Aksen Atas -->
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

    <div
        class="py-4 sm:py-8 px-8 md:px-10 max-w-5xl mx-auto relative z-10 my-6 sm:my-10"
    >
        <!-- HEADER SELARAS -->
        <div
            class="mb-8 border-b border-slate-200 pb-5 flex flex-col md:flex-row md:items-end justify-between gap-4"
        >
            <div>
                <h2
                    class="text-3xl font-extrabold text-slate-800 tracking-tight"
                >
                    Daftar Rekrutmen
                </h2>
                <p class="text-sm text-slate-500 mt-1">Daftarkan dirimu menjadi bagian dari sebuah organisasi sekarang!</p>
            </div>
        </div>
        @if ($rekrutmenAktif->isEmpty())
            <!-- Kondisi Kosong (Empty State) -->
            <div
                class="bg-white rounded-2xl border border-slate-200 p-12 flex flex-col items-center justify-center text-center shadow-sm"
            >
                <div class="p-4 bg-blue-50 text-blue-600 rounded-full mb-4">
                    <svg class="mx-auto mb-4 h-12 w-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 13.5h3.86a2.25 2.25 0 0 1 2.012 1.244l.256.512a2.25 2.25 0 0 0 2.013 1.244h3.218a2.25 2.25 0 0 0 2.013-1.244l.256-.512a2.25 2.25 0 0 1 2.013-1.244h3.859m-19.5.338V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18v-4.162c0-.224-.034-.447-.1-.661L19.8 6.808a2.25 2.25 0 0 0-2.15-1.558H6.35a2.25 2.25 0 0 0-2.15 1.558L2.35 13.177a2.25 2.25 0 0 0-.1.661Z" />
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
                        $sudahTerdaftar = $periodeTerdaftar->has($rekrutmen->id);
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
                                            <p class="text-[13px] font-extrabold text-blue-600 tracking-wide truncate">
                                                {{ $namaOrganisasi }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Detail Organisasi -->
                                <div class="mt-8 mb-3">
                                    <h3
                                        class="text-base font-extrabold text-slate-800 leading-snug transition-colors line-clamp-2"
                                    >
                                        {{
                                            $rekrutmen->slogan ??
                                                'Penerimaan Anggota Baru Tahun ' .
                                                    \Carbon\Carbon::parse($rekrutmen->created_at)->format(
                                                        'Y',
                                                    )
                                        }}
                                    </h3>
                                    <p class="mt-1 text-[11px] font-bold text-blue-600">Periode {{ $rekrutmen->tahun_periode }}</p>
                                </div>

                                <!-- Deskripsi karena tidak ada jabatan -->
                                <p class="mb-2 line-clamp-2 text-xs leading-relaxed font-bold text-slate-500">
                                    {{
                                        $rekrutmen->deskripsi ?:
                                            'Informasi rekrutmen yang telah ditutup dapat dilihat melalui halaman detail.'
                                    }}
                                </p>

                                <!-- 3. Tombol Aksi Sejajar (Side-by-Side: Menghemat Ruang Vertikal) -->
                                <div
                                    class="grid grid-cols-2 gap-2 pt-3 border-t border-slate-100"
                                >
                                    <a
                                        href="{{ route('mahasiswa.rekrutmen.info', $rekrutmen->id) }}"
                                        class="w-full flex justify-center py-2 px-4 border border-slate-300 bg-white text-xs font-bold text-slate-700 rounded-md hover:bg-slate-50 hover:text-blue-700 transition-colors shadow-sm gap-1.5 items-center"
                                    >
                                        Detail Info
                                    </a>
                                    @if ($sudahTerdaftar)
                                        <button
                                            type="button"
                                            data-status-pendaftaran="sudah-terdaftar"
                                            class="flex items-center justify-center py-2 px-3 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-lg transition-colors text-center gap-1 shadow-sm"
                                        >
                                            Terdaftar
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                        </button>
                                    @elseif ($rekrutmen->pendaftaran_terbuka)
                                        <a
                                            href="{{ route('mahasiswa.rekrutmen.daftar', $rekrutmen->id) }}"
                                            class="flex items-center justify-center py-2 px-3 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg transition-colors text-center gap-1 shadow-sm"
                                        >
                                            Daftar
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                        </a>
                                    @else
                                        <button
                                            type="button"
                                            disabled
                                            class="flex cursor-not-allowed items-center justify-center gap-1 rounded-lg border border-slate-200 bg-slate-100 px-3 py-2 text-center text-xs font-bold text-slate-400"
                                        >
                                            {{ $rekrutmen->pendaftaran_sudah_berakhir ? 'Pendaftaran Ditutup' : 'Pendaftaran Belum Dibuka' }}
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 0 0 5.636 5.636m12.728 12.728A9 9 0 0 1 5.636 5.636m12.728 12.728L5.636 5.636" />
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>

                @endforeach
            </div>
        @endif
    </div>
</x-app-layout>

<x-sweet-alert />
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
            title: 'Gagal Melakukan Pendaftaran',
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

        document
            .querySelectorAll('[data-status-pendaftaran="sudah-terdaftar"]')
            .forEach(function (button) {
                button.addEventListener('click', function () {
                    premiumSwal.fire({
                        icon: 'info',
                        title: 'Pendaftaran Sudah Tercatat',
                        text: 'Anda telah mendaftarkan diri pada rekrutmen ini.',
                    });
                });
            });
    });
</script>
