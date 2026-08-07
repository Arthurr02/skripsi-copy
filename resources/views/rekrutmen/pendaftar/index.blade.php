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
                    Daftar Pendaftar <br class="hidden sm:block" />
                    Calon Anggota
                </h2>

                <p class="text-sm font-normal text-slate-500 leading-relaxed">Memantau seluruh mahasiswa yang mengirimkan berkas pendaftaran pada periode aktif.</p>
            </div>

            <!-- Info Periode -->
            @if ($periodeAktif)
                <div
                    class="bg-white backdrop-blur-sm border border-slate-200/80 px-6 py-4 rounded-lg shrink-0 flex flex-col md:items-start justify-betweenring-1 ring-slate-900/5 shadow-[0_2px_10px_rgb(0,0,0,0.02)]"
                >
                    <!-- Kontainer Flex untuk Titik Biru dan Teks -->
                    <div class="flex items-center justify-center gap-2 mb-1.5">
                        <span class="relative flex h-2 w-2">
                            <span
                                class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"
                            ></span>
                            <span
                                class="relative inline-flex rounded-full h-2 w-2 bg-blue-500"
                            ></span>
                        </span>
                        <p class="text-[11px] text-slate-500 font-bold uppercase tracking-widest">Periode Aktif</p>
                    </div>
                    <!-- Angka Tahun -->
                    <p class="text-2xl font-bold text-blue-600 tracking-tight">
                        {{ $periodeAktif->tahun_periode }}
                    </p>
                </div>
            @endif
        </div>

        <!-- Kartu Statistik -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-6">
            <div
                class="bg-white p-5 rounded-lg border border-slate-200 shadow-sm flex items-center gap-4"
            >
                <div class="p-3 bg-blue-100 text-blue-600 rounded-lg shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Pendaftar Masuk</p>
                    <h3 class="text-2xl font-black text-slate-800 mt-0.5">
                        {{ $totalPendaftar }} Orang
                    </h3>
                </div>
            </div>
        </div>

        <!-- Tabel & Filter Container -->
        <div
            class="bg-white rounded-lg border border-slate-200 shadow-sm overflow-hidden"
        >
            <div
                class="p-5 border-b border-slate-200 bg-slate-50/80 flex flex-col sm:flex-row justify-between sm:items-center gap-4"
            >
                <h4
                    class="text-xs font-extrabold text-slate-700 uppercase tracking-wider"
                >
                    Manajemen Data Pendaftar
                </h4>
            </div>

            <!-- Area Filter -->
            <div class="p-5 bg-white border-b border-slate-100">
                <form
                    method="GET"
                    action="{{ route('organisasi.rekrutmen.pendaftar') }}"
                    class="flex flex-wrap md:flex-nowrap gap-4 items-end"
                >
                    <div class="w-full md:w-1/4">
                        <label
                            class="block text-[10px] font-bold text-slate-500 uppercase mb-1.5"
                            >Pilihan Jabatan</label
                        >
                        <select
                            name="filter_jabatan"
                            class="w-full text-xs font-semibold border-slate-300 text-slate-700 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2.5 transition-colors"
                        >
                            <option value="">Semua Jabatan</option>
                            @if (isset($listJabatan))
                                @foreach ($listJabatan as $jabatan)
                                    <option
                                        value="{{ $jabatan->id }}"
                                        {{
                                            request('filter_jabatan') == $jabatan->id
                                                ? 'selected'
                                                : ''
                                        }}
                                    >
                                        {{ $jabatan->nama_jabatan }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <div class="w-full md:w-1/4">
                        <label
                            class="block text-[10px] font-bold text-slate-500 uppercase mb-1.5"
                            >Status Seleksi</label
                        >
                        <select
                            name="filter_status"
                            class="w-full text-xs font-semibold border-slate-300 text-slate-700 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2.5 transition-colors"
                        >
                            <option value="">Semua Status</option>
                            <option
                                value="Menunggu Seleksi"
                                {{
                                    request('filter_status') ==
                                    'Menunggu Seleksi'
                                        ? 'selected'
                                        : ''
                                }}
                                >Menunggu Seleksi
                            </option>
                            <option
                                value="Lulus Tahap 1"
                                {{
                                    request('filter_status') ==
                                    'Lulus Tahap 1'
                                        ? 'selected'
                                        : ''
                                }}
                                >Lulus Tahap 1
                            </option>
                            <option
                                value="Lulus Tahap 2"
                                {{
                                    request('filter_status') ==
                                    'Lulus Tahap 2'
                                        ? 'selected'
                                        : ''
                                }}
                                >Lulus Tahap 2
                            </option>
                            <option
                                value="Tidak Lolos"
                                {{
                                    request('filter_status') == 'Tidak Lolos'
                                        ? 'selected'
                                        : ''
                                }}
                                >Tidak Lolos
                            </option>
                        </select>
                    </div>

                    <div class="w-full md:w-1/4">
                        <label
                            class="block text-[10px] font-bold text-slate-500 uppercase mb-1.5"
                            >Urutkan Berdasarkan</label
                        >
                        <select
                            name="sort"
                            class="w-full text-xs font-semibold border-slate-300 text-slate-700 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2.5 transition-colors"
                        >
                            <option
                                value="terbaru"
                                {{
                                    request('sort') == 'terbaru'
                                        ? 'selected'
                                        : ''
                                }}
                                >Waktu Pendaftaran (Terbaru)
                            </option>
                            <option
                                value="nama"
                                {{
                                    request('sort') == 'nama'
                                        ? 'selected'
                                        : ''
                                }}
                                >Nama Pendaftar (A-Z)
                            </option>
                        </select>
                    </div>

                    <div class="w-full md:w-auto flex items-center gap-2">
                        <button
                            type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold py-2.5 px-6 rounded-lg shadow-sm transition-colors w-full md:w-auto whitespace-nowrap"
                        >
                            Terapkan
                        </button>

                        @if (request()->anyFilled([ 'filter_jabatan', 'filter_status', 'sort' ]))
                            <a
                                href="{{ route('organisasi.rekrutmen.pendaftar') }}"
                                class="bg-slate-100 hover:bg-red-50 text-slate-600 hover:text-red-600 text-xs font-bold py-2.5 px-4 rounded-lg transition-colors whitespace-nowrap border border-slate-200 hover:border-red-200"
                            >
                                ✖ Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Tabel Data -->
            <div class="overflow-x-auto">
                <table
                    class="w-full text-left border-collapse text-sm min-w-[900px]"
                >
                    <thead>
                        <tr
                            class="bg-slate-50 border-b border-slate-200 text-[11px] font-extrabold text-slate-500 uppercase tracking-wider"
                        >
                            <th class="px-5 py-4 text-center w-12">No</th>
                            <th class="px-5 py-4">Waktu Daftar</th>
                            <th class="px-5 py-4">NIM</th>
                            <th class="px-5 py-4">Nama Lengkap</th>
                            <th class="px-5 py-4">Pilihan Jabatan 1</th>
                            <th class="px-5 py-4">Pilihan Jabatan 2</th>
                            <th class="px-5 py-4">Status Seleksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse ($daftarPeserta as $index => $pendaftar)
                            <tr
                                class="hover:bg-blue-50/50 transition-colors cursor-pointer"
                            >
                                <td
                                    class="px-5 py-4 text-center font-bold text-slate-400 text-xs"
                                >
                                    {{
                                        $daftarPeserta->firstItem() +
                                            $index
                                    }}
                                </td>
                                <td
                                    class="px-5 py-4 text-xs font-bold text-slate-600 whitespace-nowrap"
                                >
                                    {{
                                        \Carbon\Carbon::parse(
                                            $pendaftar->created_at,
                                        )->translatedFormat('d M Y')
                                    }}
                                    <span
                                        class="block text-[10px] font-medium text-slate-400 mt-0.5"
                                    >
                                        {{
                                            \Carbon\Carbon::parse(
                                                $pendaftar->created_at,
                                            )->format('H:i')
                                        }} WIB
                                    </span>
                                </td>
                                <td
                                    class="px-5 py-4 font-mono text-xs font-bold text-slate-600"
                                >
                                    {{ $pendaftar->nim }}
                                </td>
                                <td
                                    class="px-5 py-4 font-semibold text-slate-900 text-sm"
                                >
                                    {{
                                        $pendaftar->mahasiswa->nama_lengkap ??
                                            '-'
                                    }}
                                </td>
                                <td class="px-5 py-4">
                                    <span
                                        class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-200 shadow-sm"
                                    >
                                        {{
                                            $pendaftar->pilihanJabatan1
                                                ->nama_jabatan ?? 'Tidak Memilih'
                                        }}
                                    </span>
                                </td>
                                <td class="px-5 py-4">
                                    <span
                                        class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200 shadow-sm"
                                    >
                                        {{
                                            $pendaftar->pilihanJabatan2
                                                ->nama_jabatan ?? 'Tidak Memilih'
                                        }}
                                    </span>
                                </td>
                                <td class="px-5 py-4">
                                    @php
                                        $status = $pendaftar->status_seleksi ?? 'Menunggu Seleksi';
                                        $badgeClass = 'bg-slate-100 text-slate-600 border-slate-200';

                                        if (
                                            stripos($status, 'lulus') !== false ||
                                            stripos($status, 'lolos') !== false
                                        ) {
                                            $badgeClass = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                                        } elseif (
                                            stripos($status, 'tidak') !== false ||
                                            stripos($status, 'gagal') !== false
                                        ) {
                                            $badgeClass = 'bg-red-50 text-red-700 border-red-200';
                                        } elseif (stripos($status, 'menunggu') !== false) {
                                            $badgeClass = 'bg-yellow-50 text-yellow-700 border-yellow-200';
                                        }
                                    @endphp
                                    <span
                                        class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-bold border shadow-sm {{ $badgeClass }}"
                                    >
                                        {{ $status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td
                                    colspan="7"
                                    class="text-center py-16 text-slate-400 bg-white"
                                >
                                    <div
                                        class="flex flex-col items-center justify-center"
                                    >
                                        <span
                                            class="text-6xl block text-slate-300"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24">
                                                <path d="M0 0h24v24H0z" fill="none" />
                                                <path fill="currentColor" d="M15.5 14h-.79l-.28-.27a6.5 6.5 0 0 0 1.48-5.34c-.47-2.78-2.79-5-5.59-5.34a6.505 6.505 0 0 0-7.27 7.27c.34 2.8 2.56 5.12 5.34 5.59a6.5 6.5 0 0 0 5.34-1.48l.27.28v.79l4.25 4.25c.41.41 1.08.41 1.49 0s.41-1.08 0-1.49zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5S14 7.01 14 9.5S11.99 14 9.5 14" />
                                            </svg>
                                        </span>
                                        <p class="font-bold text-slate-500 text-sm mt-3">Data Tidak Ditemukan</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Navigasi Pagination -->
            @if ($daftarPeserta->hasPages())
                <div class="p-4 border-t border-slate-100 bg-slate-50/80">
                    {{ $daftarPeserta->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
