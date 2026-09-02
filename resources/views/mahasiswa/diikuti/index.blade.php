<x-app-layout>
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
                    Rekrutmen Sedang Diikuti
                </h2>
                <p class="text-sm text-slate-500 mt-1">Pantau status seleksi dan rekrutmen organisasi yang sedang Anda ikuti.</p>
            </div>
            <a
                href="{{ route('mahasiswa.rekrutmen.index') }}"
                class="inline-flex items-center text-sm font-bold text-blue-700 bg-blue-50 hover:bg-blue-100 hover:text-blue-800 transition-colors px-4 py-2 rounded-md border border-blue-200 shrink-0 shadow-sm"
            >
                + Cari Rekrutmen Baru
            </a>
        </div>

        @if (count($rekrutmenDiikuti) === 0)
            <!-- KONDISI KOSONG -->
            <div
                class="bg-white rounded-lg border border-slate-200 p-16 flex flex-col items-center justify-center text-center shadow-sm"
            >
                <div
                    class="w-16 h-16 bg-slate-100 text-slate-400 rounded-md flex items-center justify-center mb-5 border border-slate-200"
                >
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
                <h3 class="text-xl font-bold text-slate-800 mb-1.5">
                    Belum Ada Rekrutmen yang Diikuti
                </h3>
                <p class="text-slate-500 text-sm max-w-md">Anda belum mendaftar ke organisasi kepanitiaan manapun. Silakan jelajahi daftar rekrutmen yang sedang buka.</p>
            </div>
        @else
            <!-- DAFTAR KARTU REKRUTMEN -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($rekrutmenDiikuti as $item)
                    @php
                        $organisasi = $item->organisasi;
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

                        $bannerData = $item->periode->lampiran_banner;
                        $bannerArray = is_string($bannerData)
                            ? json_decode($bannerData, true)
                            : $bannerData;
                        $bannerPath =
                            is_array($bannerArray) && count($bannerArray) > 0 ? $bannerArray[0] : null;

                        $tahapanBerjalan = \App\Models\Tahapan::where(
                            'periode_rekrutmen_id',
                            $item->periode->id,
                        )
                            ->where('waktu_mulai', '<=', now())
                            ->where('waktu_berakhir', '>=', now())
                            ->first();
                        $namaTahapan = $tahapanBerjalan
                            ? $tahapanBerjalan->nama_tahapan
                            : 'Menunggu / Telah Selesai';
                    @endphp
                    <div
                        class="group bg-white rounded-lg border border-slate-200 overflow-hidden flex flex-col hover:border-blue-400 transition-colors shadow-sm"
                    >
                        <!-- Banner Area (Dioptimalkan Tingginya menjadi h-28) -->
                        <div
                            class="h-28 relative bg-slate-900 border-b border-slate-200 shrink-0"
                        >
                            @if ($bannerPath)
                                <img
                                    src="{{ asset('storage/' . $bannerPath) }}"
                                    alt="Banner"
                                    class="absolute inset-0 w-full h-full object-cover opacity-60 mix-blend-luminosity group-hover:opacity-80 transition-opacity"
                                />
                                <div
                                    class="absolute inset-0 bg-gradient-to-t from-slate-900/80 via-transparent to-transparent"
                                ></div>
                            @else
                                <div class="absolute inset-0 opacity-20">
                                    <svg class="w-full h-full" xmlns="http://www.w3.org/2000/svg">
                                        <defs>
                                            <pattern
                                                id="grid-{{ $item->periode->id }}"
                                                width="20"
                                                height="20"
                                                patternUnits="userSpaceOnUse"
                                            >
                                                <path d="M 20 0 L 0 0 0 20" fill="none" stroke="currentColor" class="text-white" stroke-width="1" />
                                            </pattern>
                                        </defs>
                                        <rect
                                            width="100%"
                                            height="100%"
                                            fill="url(#grid-{{ $item->periode->id }})"
                                        />
                                    </svg>
                                </div>
                            @endif

                            <div class="absolute top-2.5 right-2.5">
                                <span
                                    class="bg-yellow-100 border border-yellow-200 text-yellow-700 text-[9px] font-bold px-2 py-1 rounded shadow-sm flex items-center gap-1.5 uppercase tracking-wide"
                                >
                                    <span
                                        class="w-1.5 h-1.5 bg-yellow-500 rounded-full animate-pulse"
                                    ></span>
                                    Sedang Seleksi
                                </span>
                            </div>
                        </div>

                        <!-- Card Body (Padding dikurangi & Margin Dirapatkan) -->
                        <div class="p-6 flex-1 flex flex-col relative">
                            <!-- Avatar Berada di Tengah Garis (Overlap) -->
                            <div class="absolute -top-8 left-6 pr-6">
                                <div class="flex items-end gap-3">
                                    @if (!empty($avatarUrl))
                                        <img
                                            src="{{ $avatarUrl }}"
                                            alt="Logo {{ $namaOrganisasi }}"
                                            class="w-14 h-14 rounded-full object-cover shadow-sm bg-white border border-slate-200"
                                            referrerpolicy="no-referrer"
                                            onerror="this.style.display='none'; document.getElementById('card-avatar-fallback-{{ $loop->iteration }}').style.display='flex';"
                                        />
                                    @endif
                                    <div
                                        id="card-avatar-fallback-{{ $loop->iteration }}"
                                        style="{{ !empty($avatarUrl) ? 'display: none;' : 'display: flex;' }}"
                                        class="w-14 h-14 rounded-md bg-blue-600 text-white flex items-center justify-center text-xl font-black uppercase border border-blue-700 shadow-sm select-none"
                                    >
                                        {{
                                            substr(
                                                $namaOrganisasi,
                                                0,
                                                1,
                                            )
                                        }}
                                    </div>
                                    <div class="flex min-w-0 h-7 items-center">
                                        <p class="text-[11px] font-extrabold text-blue-600 uppercase tracking-wider truncate">
                                            {{ $namaOrganisasi }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Detail Organisasi -->
                            <div class="mt-2 mb-3">
                                <h3
                                    class="text-base font-extrabold text-slate-800 leading-snug group-hover:text-blue-700 transition-colors line-clamp-2"
                                >
                                    {{
                                        $item->periode->slogan ??
                                            'Penerimaan Anggota Baru'
                                    }}
                                </h3>
                            </div>

                            <!-- Formasi Pilihan -->
                            <div
                                class="bg-slate-50 border border-slate-200 rounded-md px-3 py-1 mb-4 space-y-1"
                            >
                                <!-- Pilihan Utama -->
                                <div
                                    class="flex flex-col sm:items-start sm:justify-between"
                                >
                                    <p class="text-[9px] text-slate-400 font-bold tracking-wider shrink-0 mt-0.5">Pilihan Utama</p>
                                    <div
                                        class="flex flex-col sm:items-end w-full min-w-0"
                                    >
                                        <p class="text-[9px] font-bold text-slate-500 uppercase tracking-widest truncate max-w-full -mb-0.5">
                                            {{
                                                !empty($item->jabatan_1->nama_posisi) &&
                                                $item->jabatan_1->nama_posisi !== '-'
                                                    ? $item->jabatan_1->nama_posisi
                                                    : 'Tanpa Divisi Khusus'
                                            }}
                                        </p>
                                        <p class="text-xs font-extrabold text-slate-700 uppercase max-w-full leading-tight">
                                            {{
                                                $item->jabatan_1
                                                    ->nama_jabatan
                                            }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Pilihan Cadangan -->
                                @if ($item->jabatan_2)
                                    <div
                                        class="pt-1 border-t border-slate-200/80 flex flex-col sm:items-start sm:justify-between"
                                    >
                                        <p class="text-[9px] text-slate-400 font-bold tracking-wider shrink-0 mt-0.5">Cadangan</p>
                                        <div
                                            class="flex flex-col sm:items-end w-full min-w-0"
                                        >
                                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest truncate max-w-full -mb-0.5">
                                                {{
                                                    !empty($item->jabatan_2->nama_posisi) &&
                                                    $item->jabatan_2->nama_posisi !== '-'
                                                        ? $item->jabatan_2->nama_posisi
                                                        : 'Tanpa Divisi Khusus'
                                                }}
                                            </p>
                                            <p class="text-[9px] font-bold uppercase text-slate-600 max-w-full leading-tight">
                                                {{
                                                    $item->jabatan_2
                                                        ->nama_jabatan
                                                }}
                                            </p>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- Status Tahapan Info -->
                            <div
                                class="bg-indigo-50 border border-indigo-100 rounded-md p-2.5 mb-5 flex items-center gap-2.5"
                            >
                                <div
                                    class="w-6 h-6 rounded-md bg-indigo-100 flex items-center justify-center shrink-0"
                                >
                                    <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                </div>
                                <div
                                    class="flex flex-col overflow-hidden w-full"
                                >
                                    <span
                                        class="text-[9px] font-bold text-blue-500 uppercase tracking-wider"
                                        >Tahapan Saat Ini</span
                                    >
                                    <span
                                        class="text-[11px] font-bold text-blue-900 truncate"
                                        title="{{ $namaTahapan }}"
                                    >
                                        {{ $namaTahapan }}
                                    </span>
                                </div>
                            </div>

                            <!-- Action Button -->
                            <div class="mt-auto">
                                <a
                                    href="{{ route('mahasiswa.rekrutmen.diikuti.tahapan', $item->id) }}"
                                    class="w-full flex justify-center py-2 px-4 bg-slate-800 text-xs font-bold text-white rounded-md hover:bg-slate-900 transition-colors shadow-sm gap-1.5 items-center"
                                >
                                    Kerjakan Tugas
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
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
