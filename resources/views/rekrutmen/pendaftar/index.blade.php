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

    <div
        class="py-4 sm:py-8 px-8 md:px-10 max-w-5xl mx-auto relative z-10 my-6 sm:my-10"
    >
        <!-- HEADER SELARAS -->
        <div
            class="mb-8 pb-5 relative z-10 flex flex-col md:flex-row md:items-center justify-between"
        >
            <div>
                <!-- Tipografi Solid Slate -->
                <h2
                    class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-800 mb-2.5 leading-tight"
                >
                    Daftar Peserta
                </h2>

                <p class="text-sm font-normal text-slate-500 leading-relaxed">Memantau seluruh mahasiswa yang mengirimkan berkas pendaftaran pada periode aktif.</p>
            </div>

            <!-- Info Periode -->
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
                    {{ $periodeAktif?->tahun_periode ?? '-' }}
                </p>
            </div>
        </div>

        <!-- Tabel & Filter Container -->
        <div
            class="p-8 md:p-10 bg-white rounded-lg border border-slate-200 shadow-sm overflow-hidden"
        >
            <!-- Area Filter (Nested Select, Checkbox Pilihan, & Auto-Submit) -->
            <div class="bg-white border-slate-100">
                <form
                    method="GET"
                    action="{{ route($routePrefix . 'rekrutmen.pendaftar') }}"
                    class="flex flex-col gap-4"
                >
                    <div class="flex flex-col md:flex-row gap-4 items-end">
                        <!-- 1. Nested Filter Posisi & Jabatan -->
                        <div class="w-full md:w-2/5">
                            <label
                                class="block text-[10px] font-bold text-slate-500 uppercase mb-1.5"
                            >
                                Pilihan Posisi & Jabatan
                            </label>
                            <select
                                name="filter_jabatan"
                                onchange="this.form.submit()"
                                class="w-full text-xs font-semibold border-slate-300 text-slate-700 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2.5 transition-colors cursor-pointer"
                            >
                                <option value="">Semua Posisi & Jabatan</option>
                                @if (isset($listJabatan))
                                    @php
                                        $groupedJabatan = collect($listJabatan)->groupBy(function ($item) {
                                            return $item->nama_posisi ?? 'Tanpa Posisi';
                                        });
                                    @endphp
                                    @foreach ($groupedJabatan as $namaPosisi => $jabatans)
                                        <!-- Menggunakan ikon profil orang pada label optgroup -->
                                        <optgroup label="👤  {{ $namaPosisi }}">
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

                        <!-- 2. Filter Status Seleksi -->
                        <div class="w-full md:w-2/5">
                            <label
                                class="block text-[10px] font-bold text-slate-500 uppercase mb-1.5"
                            >
                                Status Seleksi
                            </label>
                            <select
                                name="filter_status"
                                onchange="this.form.submit()"
                                class="w-full text-xs font-semibold border-slate-300 text-slate-700 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2.5 transition-colors cursor-pointer"
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

                        <!-- 3. Tombol Reset -->
                        <div
                            class="w-full md:w-auto h-[38px] flex items-center"
                        >
                            @if (request()->anyFilled([
                                    'filter_jabatan',
                                    'filter_status',
                                    'pilihan_tipe'
                                ]))
                                <a
                                    href="{{ route($routePrefix . 'rekrutmen.pendaftar') }}"
                                    class="w-full md:w-max h-full bg-slate-100 hover:bg-red-50 text-slate-600 hover:text-red-600 text-xs font-bold px-5 rounded-lg transition-colors border border-slate-200 hover:border-red-200 flex items-center justify-center gap-1.5"
                                    title="Hapus semua filter"
                                >
                                    ✖ Reset
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- Baris Tambahan: Checkbox Pilihan 1 / Pilihan 2 -->
                    @if (request()->filled('filter_jabatan'))
                        <div
                            class="flex items-center gap-6 pt-2 border-t border-slate-100 text-xs text-slate-600 font-medium"
                        >
                            <span
                                class="text-[10px] font-bold text-slate-400 uppercase tracking-wider"
                                >Cari Berdasarkan:</span
                            >

                            <label
                                class="inline-flex items-center gap-2 cursor-pointer hover:text-blue-600 transition-colors"
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
                                    class="rounded border-slate-300 text-blue-600 shadow-sm focus:ring-blue-500 w-4 h-4"
                                />
                                <span>Pilihan 1 Saja</span>
                            </label>

                            <label
                                class="inline-flex items-center gap-2 cursor-pointer hover:text-blue-600 transition-colors"
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
                                    class="rounded border-slate-300 text-blue-600 shadow-sm focus:ring-blue-500 w-4 h-4"
                                />
                                <span>Pilihan 2 Saja</span>
                            </label>
                        </div>
                    @endif
                </form>
            </div>

            <!-- Kartu Statistik -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 py-6">
                <div
                    class="bg-blue-50 py-3 px-5 rounded-lg flex items-center gap-4"
                >
                    <div class="text-blue-600 rounded-lg shrink-0">
                        <svg class="size-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Pendaftar Masuk</p>
                        <h3 class="text-2xl font-black text-slate-800 mt-0.5">
                            {{ $totalPendaftar }} Peserta
                        </h3>
                    </div>
                </div>
            </div>

            <!-- Tabel Data -->
            <div class="overflow-x-auto">
                <table
                    class="w-full text-left border-collapse text-sm min-w-[900px]"
                >
                    <thead>
                        <tr
                            class="bg-gray-50 border border-slate-200 text-[11px] font-extrabold text-slate-500 uppercase tracking-wider"
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
                                class="hover:bg-blue-50/50 transition-colors border"
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

                                <!-- Kolom Pilihan Jabatan 1 (Format: Posisi | Jabatan) -->
                                <td class="px-5 py-4">
                                    @php
                                        $jab1 = $pendaftar->pilihanJabatan1;
                                        if ($jab1) {
                                            $namaPos1 = $jab1->nama_posisi ?? '';
                                            $namaJab1 = $jab1->nama_jabatan ?? '';
                                            $teksJabatan1 = $namaPos1 ? "{$namaPos1} | {$namaJab1}" : $namaJab1;
                                            $badgeClass1 = 'bg-blue-50 text-blue-700 border-blue-200';
                                        } else {
                                            $teksJabatan1 = 'Tidak Memilih';
                                            $badgeClass1 = 'bg-yellow-50 text-yellow-700 border-yellow-200';
                                        }
                                    @endphp
                                    <span
                                        class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-bold border shadow-sm {{ $badgeClass1 }}"
                                    >
                                        {{ $teksJabatan1 }}
                                    </span>
                                </td>

                                <!-- Kolom Pilihan Jabatan 2 (Format: Posisi | Jabatan) -->
                                <td class="px-5 py-4">
                                    @php
                                        $jab2 = $pendaftar->pilihanJabatan2;
                                        if ($jab2) {
                                            $namaPos2 = $jab2->nama_posisi ?? '';
                                            $namaJab2 = $jab2->nama_jabatan ?? '';
                                            $teksJabatan2 = $namaPos2 ? "{$namaPos2} | {$namaJab2}" : $namaJab2;
                                            $badgeClass2 = 'bg-indigo-50 text-indigo-700 border-indigo-200';
                                        } else {
                                            $teksJabatan2 = 'Tidak Memilih';
                                            $badgeClass2 = 'bg-yellow-50 text-yellow-700 border-yellow-200';
                                        }
                                    @endphp
                                    <span
                                        class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-bold border shadow-sm {{ $badgeClass2 }}"
                                    >
                                        {{ $teksJabatan2 }}
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
            @if (method_exists($daftarPeserta, 'hasPages') && $daftarPeserta->hasPages())
                <div class="p-4 border-t border-slate-100 bg-slate-50/80">
                    {{ $daftarPeserta->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
