<x-app-layout>
    <div class="bg-slate-900 pt-16 pb-24 relative overflow-hidden">
        <div class="absolute inset-0 opacity-10">
            <svg class="w-full h-full" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <pattern id="grid-riwayat" width="32" height="32" patternUnits="userSpaceOnUse">
                        <path d="M 32 0 L 0 0 0 32" fill="none" stroke="white" stroke-width="1" />
                    </pattern>
                </defs>
                <rect width="100%" height="100%" fill="url(#grid-riwayat)" />
            </svg>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <h1
                class="text-3xl md:text-4xl font-extrabold text-white tracking-tight mb-2"
            >
                Riwayat Rekrutmen
            </h1>
            <p class="text-slate-400 font-medium max-w-2xl">Arsip seluruh organisasi dan kepanitiaan yang pernah Anda lamar beserta status akhir kelulusannya.</p>
        </div>
    </div>

    <div
        class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-12 pb-24 relative z-20"
    >
        <div
            class="bg-white rounded-3xl shadow-xl border border-slate-200 overflow-hidden"
        >
            @if ($riwayatPendaftaran->isEmpty())
                <div class="p-16 text-center flex flex-col items-center">
                    <div
                        class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center text-slate-400 mb-4"
                    >
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800">
                        Belum Ada Riwayat
                    </h3>
                    <p class="text-slate-500 text-sm mt-1">Anda belum pernah mendaftar rekrutmen apa pun.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr
                                class="bg-slate-50/80 border-b border-slate-200 text-slate-500 text-[11px] uppercase tracking-wider font-extrabold"
                            >
                                <th class="p-5">Organisasi & Periode</th>
                                <th class="p-5">Pilihan Utama</th>
                                <th class="p-5 hidden md:table-cell">
                                    Pilihan Alternatif
                                </th>
                                <th class="p-5">Tanggal Daftar</th>
                                <th class="p-5 text-right">Status Akhir</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($riwayatPendaftaran as $item)
                                @php
                                    $organisasi = $item->jabatan_1->periode->organisasi;
                                    $periode = $item->jabatan_1->periode;
                                    $namaOrganisasi = $organisasi->nama_organisasi ?? 'Organisasi';

                                    // Fallback Avatar Organisasi
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

                                    // Anda bisa mengganti logika ini dengan kolom status asli di database Anda (misal: $item->status_lulus)
                                    $status = 'Menunggu';
                                @endphp
                                <tr
                                    class="hover:bg-slate-50/50 transition-colors group"
                                >
                                    <td class="p-5">
                                        <div class="flex items-center gap-4">
                                            <div class="relative shrink-0">
                                                @if (!empty($avatarUrl))
                                                    <img
                                                        src="{{ $avatarUrl }}"
                                                        alt="Logo"
                                                        class="w-10 h-10 rounded-full object-cover shadow-sm bg-white border border-slate-200"
                                                        referrerpolicy="no-referrer"
                                                        onerror="this.style.display='none'; document.getElementById('riwayat-avatar-{{ $loop->iteration }}').style.display='flex';"
                                                    />
                                                @endif
                                                <div
                                                    id="riwayat-avatar-{{ $loop->iteration }}"
                                                    style="{{ !empty($avatarUrl) ? 'display: none;' : 'display: flex;' }}"
                                                    class="w-10 h-10 rounded-full bg-slate-800 text-white flex items-center justify-center text-sm font-black uppercase shadow-sm select-none"
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
                                            <div>
                                                <p class="text-sm font-extrabold text-slate-800">{{ $namaOrganisasi }}</p>
                                                <p class="text-xs font-semibold text-slate-500 mt-0.5">
                                                    Periode {{ $periode->tahun_periode }}
                                                </p>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="p-5">
                                        <span
                                            class="inline-flex px-2.5 py-1 bg-blue-50 text-blue-700 text-xs font-bold rounded-md border border-blue-100"
                                        >
                                            {{
                                                $item->jabatan_1->nama_jabatan ??
                                                    '-'
                                            }}
                                        </span>
                                    </td>

                                    <td class="p-5 hidden md:table-cell">
                                        @if ($item->jabatan_2)
                                            <span
                                                class="inline-flex px-2.5 py-1 bg-slate-50 text-slate-600 text-xs font-semibold rounded-md border border-slate-200"
                                            >
                                                {{
                                                    $item->jabatan_2
                                                        ->nama_jabatan
                                                }}
                                            </span>
                                        @else
                                            <span class="text-slate-400 text-sm"
                                                >-</span
                                            >
                                        @endif
                                    </td>

                                    <td class="p-5">
                                        <p class="text-sm font-semibold text-slate-700">{{
                                            $item->created_at->translatedFormat(
                                                'd M Y',
                                            )
                                        }}</p>
                                        <p class="text-xs text-slate-400 mt-0.5">{{
                                            $item->created_at->format(
                                                'H:i',
                                            )
                                        }} WIB</p>
                                    </td>

                                    <td class="p-5 text-right">
                                        @if ($status === 'Lolos')
                                            <span
                                                class="inline-flex items-center gap-1.5 px-3 py-1 bg-green-100 text-green-700 border border-green-200 text-xs font-bold uppercase tracking-wider rounded-full"
                                            >
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                                Lolos
                                            </span>
                                        @elseif ($status === 'Tidak Lolos')
                                            <span
                                                class="inline-flex items-center gap-1.5 px-3 py-1 bg-red-50 text-red-600 border border-red-200 text-xs font-bold uppercase tracking-wider rounded-full"
                                            >
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                Tidak Lolos
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center gap-1.5 px-3 py-1 bg-yellow-100 text-yellow-800 border border-yellow-200 text-xs font-bold uppercase tracking-wider rounded-full"
                                            >
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                Sedang Berjalan
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
