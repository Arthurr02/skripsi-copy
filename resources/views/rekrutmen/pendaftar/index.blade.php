<x-app-layout>
    <!-- Background Aksen Atas (Mencegah scroll horizontal) -->
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

    <!-- MAIN CONTAINER (Sama persis dengan Buka Rekrutmen) -->
    <div
        class="w-full min-w-0 py-4 sm:py-8 px-4 sm:px-8 md:px-10 max-w-5xl mx-auto relative z-10 my-6 sm:my-8 sm:mb-10"
    >
        <!-- HEADER -->
        <div
            class="mb-8 sm:mb-10 relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-5 text-center sm:text-left"
        >
            <div>
                <h2
                    class="text-3xl sm:text-4xl font-extrabold text-slate-800 tracking-tight leading-tight"
                >
                    Daftar Peserta
                </h2>
                <p class="text-sm text-slate-500 mt-2 leading-relaxed">Daftar peserta berisikan seluruh mahasiswa yang mendaftar rekrutmen dan status seleksinya.</p>
            </div>

            <!-- Info Periode -->
            <div
                class="w-full sm:w-auto bg-white backdrop-blur-sm border border-slate-200/80 px-6 py-4 rounded-xl shrink-0 flex flex-col items-center sm:items-start justify-between shadow-sm"
            >
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
                <p class="text-2xl font-extrabold text-blue-600 tracking-tight">
                    {{
                        $periodeAktif?->tahun_periode ??
                            '-'
                    }}
                </p>
            </div>
        </div>

        <!-- KONTEN UTAMA (Format Card & Padding disamakan) -->
        <div
            class="bg-white px-5 sm:px-10 py-6 sm:py-10 rounded-xl shadow-sm border border-slate-200 space-y-8"
        >
            <!-- BAGIAN 1: FILTER -->
            <div>
                <label
                    class="block text-sm font-bold text-slate-700 mb-1 tracking-wide"
                >
                    Filter Posisi dan Jabatan Peserta
                </label>
                <p class="text-xs font-normal text-slate-500 mb-4">Gunakan filter untuk mempermudah melihat daftar peserta.</p>

                <form
                    method="GET"
                    action="{{ route($routePrefix . 'rekrutmen.pendaftar') }}"
                    class="flex flex-col gap-4"
                >
                    <div
                        class="flex flex-col md:flex-row gap-4 items-start md:items-stretch"
                    >
                        <!-- Pilihan Posisi & Jabatan -->
                        <div class="w-full md:w-2/5">
                            <select
                                name="filter_jabatan"
                                onchange="this.form.submit()"
                                class="block w-full bg-slate-50 border border-slate-300 text-slate-600 text-sm font-bold focus:border-blue-600 focus:ring-0 rounded-lg py-3 px-4 transition-colors"
                            >
                                <option value="">
                                    &mdash; Semua Posisi & Jabatan &mdash;
                                </option>
                                @if (isset($listJabatan))
                                    @php
                                        $groupedJabatan = collect($listJabatan)->groupBy(function ($item) {
                                            return $item->nama_posisi ?? 'Tanpa Posisi';
                                        });
                                    @endphp
                                    @foreach ($groupedJabatan as $namaPosisi => $jabatans)
                                        <optgroup label="{{ $namaPosisi }}">
                                            @foreach ($jabatans as $jabatan)
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
                                        </optgroup>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <!-- Status Seleksi -->
                        <div class="w-full md:w-2/5">
                            <select
                                name="filter_status"
                                onchange="this.form.submit()"
                                class="block w-full bg-slate-50 border border-slate-300 text-slate-600 text-sm font-bold focus:border-blue-600 focus:ring-0 rounded-lg py-3 px-4 transition-colors"
                            >
                                <option value="">
                                    &mdash; Semua Status Seleksi &mdash;
                                </option>
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

                        <!-- Tombol Reset -->
                        <div class="w-full md:w-auto flex items-center">
                            @if (request()->anyFilled([
                                    'filter_jabatan',
                                    'filter_status',
                                    'pilihan_tipe'
                                ]))
                                <a
                                    href="{{ route($routePrefix . 'rekrutmen.pendaftar') }}"
                                    class="w-full md:w-max h-full bg-slate-100 hover:bg-red-50 text-slate-600 hover:text-red-600 text-sm font-bold px-6 py-3 rounded-lg transition-colors border border-slate-200 hover:border-red-200 flex items-center justify-center gap-2 shadow-sm"
                                    title="Hapus semua filter"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    Reset
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- Checkbox Pilihan -->
                    @if (request()->filled('filter_jabatan'))
                        <div
                            class="flex flex-col sm:flex-row sm:items-center gap-3 pt-3 border-t border-slate-100 mt-1"
                        >
                            <span
                                class="text-xs font-normal text-slate-500 mr-2"
                                >Cari Berdasarkan:</span
                            >
                            <label
                                class="inline-flex items-center gap-2 cursor-pointer group"
                            >
                                <input
                                    type="checkbox"
                                    name="pilihan_tipe[]"
                                    value="1"
                                    onchange="this.form.submit()"
                                    {{
                                        in_array(
                                            '1',
                                            request('pilihan_tipe', ['1', '2']),
                                        )
                                            ? 'checked'
                                            : ''
                                    }}
                                    class="w-4 h-4 text-blue-600 bg-white border-slate-300 rounded focus:ring-blue-500 shadow-sm"
                                />
                                <span
                                    class="text-sm text-slate-700 font-bold group-hover:text-blue-600 transition-colors"
                                    >Pilihan 1 Saja</span
                                >
                            </label>
                            <label
                                class="inline-flex items-center gap-2 cursor-pointer group"
                            >
                                <input
                                    type="checkbox"
                                    name="pilihan_tipe[]"
                                    value="2"
                                    onchange="this.form.submit()"
                                    {{
                                        in_array(
                                            '2',
                                            request('pilihan_tipe', ['1', '2']),
                                        )
                                            ? 'checked'
                                            : ''
                                    }}
                                    class="w-4 h-4 text-blue-600 bg-white border-slate-300 rounded focus:ring-blue-500 shadow-sm"
                                />
                                <span
                                    class="text-sm text-slate-700 font-bold group-hover:text-blue-600 transition-colors"
                                    >Pilihan 2 Saja</span
                                >
                            </label>
                        </div>
                    @endif
                </form>
            </div>

            <hr class="border-slate-100" />

            <!-- BAGIAN 2: STATISTIK & TABEL PESERTA -->
            <div>
                <!-- Statistik -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-5">
                    <div
                        class="bg-blue-50 border border-blue-100 py-4 px-5 rounded-xl flex items-center gap-4 shadow-sm"
                    >
                        <div
                            class="text-blue-600 rounded-lg shrink-0 bg-white p-2.5 border border-blue-200"
                        >
                            <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-[10px] font-extrabold text-blue-500/80 uppercase tracking-wider">Total Pendaftar</p>
                            <h3
                                class="text-2xl font-black text-slate-800 mt-0.5"
                            >
                                {{ $totalPendaftar }}
                                <span class="text-sm font-bold text-slate-500"
                                    >Peserta</span
                                >
                            </h3>
                        </div>
                    </div>
                </div>

                <!-- Desain Tabel Diselaraskan dengan Desain Form Panitia -->
                <div
                    class="overflow-hidden border border-slate-200 rounded-lg bg-white"
                >
                    <div class="max-w-full overflow-x-auto">
                        <table
                            class="w-full text-left border-collapse min-w-[900px]"
                        >
                            <thead>
                                <tr
                                    class="bg-slate-50 border-b border-slate-200"
                                >
                                    <th
                                        class="px-5 py-3 text-xs font-bold text-slate-600 tracking-wide text-center w-12 border-r border-slate-200/50"
                                    >
                                        No
                                    </th>
                                    <th
                                        class="px-5 py-3 text-xs font-bold text-slate-600 tracking-wide border-r border-slate-200/50"
                                    >
                                        Waktu Daftar
                                    </th>
                                    <th
                                        class="px-5 py-3 text-xs font-bold text-slate-600 tracking-wide border-r border-slate-200/50"
                                    >
                                        NIM Mahasiswa
                                    </th>
                                    <th
                                        class="px-5 py-3 text-xs font-bold text-slate-600 tracking-wide border-r border-slate-200/50"
                                    >
                                        Nama Lengkap
                                    </th>
                                    <th
                                        class="px-5 py-3 text-xs font-bold text-slate-600 tracking-wide border-r border-slate-200/50"
                                    >
                                        Pilihan 1
                                    </th>
                                    <th
                                        class="px-5 py-3 text-xs font-bold text-slate-600 tracking-wide border-r border-slate-200/50"
                                    >
                                        Pilihan 2
                                    </th>
                                    <th
                                        class="px-5 py-3 text-xs font-bold text-slate-600 tracking-wide"
                                    >
                                        Status Seleksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse ($daftarPeserta as $index => $pendaftar)
                                    <tr
                                        class="hover:bg-slate-50/50 transition-colors align-top"
                                    >
                                        <td
                                            class="px-5 py-4 text-center text-xs font-bold text-slate-400"
                                        >
                                            {{
                                                $daftarPeserta->firstItem() +
                                                    $index
                                            }}
                                        </td>
                                        <td
                                            class="px-5 py-4 text-xs font-bold text-slate-700 whitespace-nowrap"
                                        >
                                            {{
                                                \Carbon\Carbon::parse(
                                                    $pendaftar->created_at,
                                                )->translatedFormat('d M Y')
                                            }}
                                            <span
                                                class="block text-[10px] font-normal text-slate-500 mt-0.5"
                                            >
                                                {{
                                                    \Carbon\Carbon::parse(
                                                        $pendaftar->created_at,
                                                    )->format('H:i')
                                                }} WIB
                                            </span>
                                        </td>
                                        <td
                                            class="px-5 py-4 text-sm font-bold font-mono text-slate-800"
                                        >
                                            {{ $pendaftar->nim }}
                                        </td>
                                        <td
                                            class="px-5 py-4 text-sm font-semibold text-slate-600"
                                        >
                                            {{
                                                $pendaftar->mahasiswa->nama_lengkap ??
                                                    '-'
                                            }}
                                        </td>

                                        <!-- Kolom Pilihan Jabatan 1 -->
                                        <td class="px-5 py-4">
                                            @php
                                                $jab1 = $pendaftar->pilihanJabatan1;
                                                if ($jab1) {
                                                    $namaPos1 = $jab1->nama_posisi ?? '';
                                                    $namaJab1 = $jab1->nama_jabatan ?? '';
                                                    $teksJabatan1 = $namaPos1 ? "{$namaPos1} | {$namaJab1}" : $namaJab1;
                                                    $badgeClass1 = 'bg-slate-100 border-slate-300 text-slate-700';
                                                } else {
                                                    $teksJabatan1 = 'Tidak Memilih';
                                                    $badgeClass1 = 'bg-white border-dashed border-slate-300 text-slate-400';
                                                }
                                            @endphp
                                            <span
                                                class="inline-flex items-center px-3 py-1.5 rounded-md text-[11px] font-bold border shadow-sm {{ $badgeClass1 }} whitespace-nowrap"
                                            >
                                                {{ $teksJabatan1 }}
                                            </span>
                                        </td>

                                        <!-- Kolom Pilihan Jabatan 2 -->
                                        <td class="px-5 py-4">
                                            @php
                                                $jab2 = $pendaftar->pilihanJabatan2;
                                                if ($jab2) {
                                                    $namaPos2 = $jab2->nama_posisi ?? '';
                                                    $namaJab2 = $jab2->nama_jabatan ?? '';
                                                    $teksJabatan2 = $namaPos2 ? "{$namaPos2} | {$namaJab2}" : $namaJab2;
                                                    $badgeClass2 = 'bg-slate-100 border-slate-300 text-slate-700';
                                                } else {
                                                    $teksJabatan2 = 'Tidak Memilih';
                                                    $badgeClass2 = 'bg-white border-dashed border-slate-300 text-slate-400';
                                                }
                                            @endphp
                                            <span
                                                class="inline-flex items-center px-3 py-1.5 rounded-md text-[11px] font-bold border shadow-sm {{ $badgeClass2 }} whitespace-nowrap"
                                            >
                                                {{ $teksJabatan2 }}
                                            </span>
                                        </td>

                                        <!-- Status Seleksi -->
                                        <td class="px-5 py-4">
                                            @php
                                                $status = $pendaftar->status_seleksi ?? 'Menunggu Seleksi';
                                                $badgeClass = 'bg-slate-50 text-slate-600 border-slate-200';

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
                                                class="inline-flex items-center px-3 py-1.5 rounded-md text-[11px] font-bold border shadow-sm {{ $badgeClass }} whitespace-nowrap"
                                            >
                                                {{ $status }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td
                                            colspan="7"
                                            class="text-center py-20 bg-slate-50/50"
                                        >
                                            <div
                                                class="flex flex-col items-center justify-center"
                                            >
                                                <svg class="w-12 h-12 text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                                </svg>
                                                <p class="font-bold text-slate-500 text-sm">Data Tidak Ditemukan</p>
                                                <p class="font-normal text-slate-400 text-xs mt-1">Belum ada peserta yang sesuai dengan filter.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Navigasi Pagination -->
            @if (method_exists($daftarPeserta, 'hasPages') && $daftarPeserta->hasPages())
                <hr class="border-slate-100" />
                <div class="pt-2">{{ $daftarPeserta->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>
