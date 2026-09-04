@php
    $readOnly = $readOnly ?? false;
    $urutanWaktu = $urutanWaktu ?? 'desc';
    $urutanWaktuBerikutnya = $urutanWaktu === 'asc' ? 'desc' : 'asc';
@endphp

<x-app-layout>
    <!-- BACKGROUND (sama persis dengan halaman Buka Rekrutmen) -->
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

    <!-- MAIN CONTAINER (sama dengan desain acuan) -->
    <div
        class="w-full min-w-0 py-4 sm:py-8 px-4 sm:px-8 md:px-10 max-w-5xl mx-auto relative z-10 my-6 sm:my-8 sm:mb-10"
    >
        <!-- LINK KEMBALI -->
        <a
            href="{{ $readOnly
                ? route($routePrefix . 'riwayat.periode', ['periode_id' => $tahapan->periode_rekrutmen_id])
                : route($routePrefix . 'rekrutmen.seleksi') }}"
            class="w-max bg-white hover:bg-slate-50 text-slate-600 hover:text-blue-600 text-xs font-bold px-4 py-2.5 rounded-lg transition-colors border border-slate-200 hover:border-blue-200 inline-flex items-center justify-center gap-2 shadow-sm"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m15 18-6-6 6-6" />
            </svg>
            {{
                $readOnly
                    ? 'Kembali ke Riwayat Tahapan'
                    : 'Kembali ke Daftar Tahapan'
            }}
        </a>

        <!-- HEADER: pola yang sama dengan Daftar Panitia dan Daftar Peserta -->
        <header
            class="mt-6 mb-8 sm:mb-10 flex flex-col md:flex-row md:items-center justify-between gap-5 text-center sm:text-left"
        >
            <div>
                <h2
                    class="mt-1 text-3xl sm:text-4xl font-extrabold text-slate-800 tracking-tight leading-tight"
                >
                    {{
                        $readOnly
                            ? 'Riwayat Seleksi Peserta: '
                            : 'Hasil Seleksi Tahapan: '
                    }} <br />{{ $tahapan->nama_tahapan }}
                </h2>
                <p class="mt-2 text-sm leading-relaxed text-blue-600 font-bold">
                    {{
                        $jabatan->nama_posisi
                            ? $jabatan->nama_posisi . ' | '
                            : ''
                    }}{{ $jabatan->nama_jabatan }}
                </p>
            </div>

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
                    <p class="text-[11px] text-slate-500 font-bold uppercase tracking-widest">Periode {{
                        $readOnly
                            ? 'Rekrutmen'
                            : 'Aktif'
                    }}</p>
                </div>
                <p class="text-2xl font-extrabold text-blue-600 tracking-tight">
                    {{
                        $jabatan->periode?->tahun_periode ??
                            '-'
                    }}
                </p>
            </div>
        </header>

        <!-- DAFTAR TUGAS -->
        <div
            class="bg-white px-5 sm:px-10 py-6 sm:py-10 rounded-xl shadow-sm border border-slate-200 space-y-6"
        >
            @forelse ($tugasDenganJawaban as $tugas)
                @if (filled($tugas->deskripsi_tugas))
                    <div>
                        <label
                            class="block text-sm font-bold text-slate-700 mb-1 tracking-wide"
                        >
                            Deskripsi Tugas
                        </label>
                        <p class="text-xs font-normal text-slate-500 mb-4">{{ $tugas->deskripsi_tugas }}</p>
                        <!-- Lampiran dari tugas (berkas dari form) -->
                        @php
                            $lampiran = $tugas->lampiran_tugas;
                            $berkasList = $lampiran['berkas'] ?? [];
                        @endphp
                        <div class="flex flex-wrap items-center gap-3">
                            @if (!empty($berkasList))
                                @foreach ($berkasList as $file)
                                    <a
                                        href="{{ asset('storage/' . $file) }}"
                                        target="_blank"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white hover:bg-slate-50 border border-slate-300 text-[11px] font-bold text-slate-700 rounded-md shadow-sm transition-colors w-fit"
                                    >
                                        <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0-3-3m3 3 3-3m2 8H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586a.707.707 0 0 1 .5.207l5.707 5.707a.707.707 0 0 1 .207.5V19a2 2 0 0 1-2 2Z" />
                                        </svg>
                                        Unduh Lampiran {{ $loop->iteration }}
                                    </a>
                                @endforeach
                            @endif
                        </div>
                    </div>
                    <hr class="border-slate-100" />

                @endif
                <div>
                    <div
                        class="flex flex-col sm:flex-row sm:items-center justify-between"
                    >
                        <div>
                            <label
                                class="block text-sm font-bold text-slate-700 mb-1 tracking-wide"
                            >
                                Hasil Penugasan Peserta
                            </label>
                            @if (!$readOnly)
                                <p class="text-xs font-normal text-slate-500 mb-4">Seleksi dapat dilakukan melalui tabel berikut.</p>
                            @endif
                        </div>
                        <!-- Tombol Export Excel -->
                        @if ($tugas->memakai_form)
                            <a
                                href="{{ $readOnly
                    ? route($routePrefix . 'riwayat.export', ['periode_id' => $tahapan->periode_rekrutmen_id, 'jabatan_id' => $jabatan->id, 'tahapan_id' => $tahapan->id, 'tugasId' => $tugas->id])
                    : route($routePrefix . 'rekrutmen.seleksi.export', ['tahapanId' => $tahapan->id, 'jabatanId' => $jabatan->id, 'tugasId' => $tugas->id]) }}"
                                data-no-loading
                                class="inline-flex justify-center items-center gap-1.5 rounded-md border border-emerald-200 bg-emerald-50 px-4 py-2.5 text-xs font-bold tracking-wide text-emerald-700 transition hover:border-emerald-600 hover:bg-emerald-600 hover:text-white"
                            >
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0-3-3m3 3 3-3m2 8H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586a.707.707 0 0 1 .5.207l5.707 5.707a.707.707 0 0 1 .207.5V19a2 2 0 0 1-2 2Z" />
                                </svg>
                                Export Excel
                            </a>
                        @endif
                    </div>
                </div>
                <section
                    x-data="{
                        urutanKeputusan: 'asc',
                        urutkanKeputusan() {
                            this.urutanKeputusan =
                                this.urutanKeputusan === 'asc' ? 'desc' : 'asc';
                            const baris = Array.from(
                                this.$refs.barisPeserta.querySelectorAll(
                                    'tr[data-keputusan]',
                                ),
                            );
                            baris.sort((a, b) => {
                                const ka = Number(a.dataset.keputusan);
                                const kb = Number(b.dataset.keputusan);
                                return this.urutanKeputusan === 'asc'
                                    ? ka - kb
                                    : kb - ka;
                            });
                            baris.forEach((el) =>
                                this.$refs.barisPeserta.appendChild(el),
                            );
                        },
                    }"
                    class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm"
                >
                    <!-- TABEL DENGAN LEBAR YANG LEBIH BESAR -->
                    <div class="overflow-x-auto">
                        <table
                            class="w-full text-left border-collapse table-fixed min-w-[1050px]"
                        >
                            <thead
                                class="bg-slate-50 border-b border-slate-200"
                            >
                                <tr>
                                    <th
                                        class="w-28 px-5 py-3 text-xs font-bold text-slate-600 tracking-wide border-r border-slate-200/50"
                                    >
                                        NIM
                                    </th>
                                    <th
                                        class="w-48 px-5 py-3 text-xs font-bold text-slate-600 tracking-wide border-r border-slate-200/50"
                                    >
                                        Nama
                                    </th>
                                    <th
                                        class="w-52 px-5 py-3 text-xs font-bold text-slate-600 tracking-wide border-r border-slate-200/50"
                                    >
                                        Pilihan 2
                                    </th>
                                    <th
                                        class="w-28 px-5 py-3 text-xs font-bold text-slate-600 tracking-wide border-r border-slate-200/50"
                                    >
                                        <a
                                            href="{{ request()->fullUrlWithQuery(['urutan_waktu' => $urutanWaktuBerikutnya]) }}"
                                            class="inline-flex items-center gap-1 hover:text-blue-700 transition"
                                            title="Balik urutan waktu"
                                        >
                                            Waktu Pengumpulan
                                            <svg
                                                class="w-3.5 h-3.5 transition-transform {{ $urutanWaktu === 'asc' ? 'rotate-180' : '' }}"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7" />
                                            </svg>
                                        </a>
                                    </th>
                                    <th
                                        class="w-80 px-5 py-3 text-xs font-bold text-slate-600 tracking-wide border-r border-slate-200/50"
                                    >
                                        Pertanyaan &amp; Hasil
                                    </th>
                                    <th
                                        class="w-28 px-5 py-3 text-xs font-bold text-slate-600 tracking-wide border-r border-slate-200/50"
                                    >
                                        <button
                                            type="button"
                                            @click="urutkanKeputusan()"
                                            class="inline-flex items-center gap-1 hover:text-blue-700 transition"
                                        >
                                            Keputusan
                                            <svg
                                                class="w-3.5 h-3.5 transition-transform"
                                                :class="urutanKeputusan ===
                                                'desc'
                                                    ? 'rotate-180'
                                                    : ''"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7" />
                                            </svg>
                                        </button>
                                    </th>
                                    @unless ($readOnly)
                                        <th
                                            class="w-40 px-5 py-3 text-xs font-bold text-slate-600 tracking-wide text-center"
                                        >
                                            Aksi
                                        </th>
                                    @endunless
                                </tr>
                            </thead>
                            <tbody
                                x-ref="barisPeserta"
                                class="divide-y divide-slate-100"
                            >
                                @forelse ($tugas->jawaban_peserta as $peserta)
                                    <tr
                                        data-waktu="{{ $peserta['dikumpulkan_pada']?->getTimestamp() ?? 0 }}"
                                        data-keputusan="{{ match ($peserta['status_seleksi']) { 'Lulus' => 1, 'Menunggu Seleksi' => 2, 'Tidak Lolos' => 3, default => 4 } }}"
                                        class="hover:bg-slate-50/50 transition-colors align-top"
                                    >
                                        <!-- NIM -->
                                        <td
                                            class="w-28 px-5 py-4 font-mono text-sm font-bold text-slate-800"
                                        >
                                            {{
                                                $peserta[
                                                    'nim'
                                                ]
                                            }}
                                        </td>

                                        <!-- Nama (lebih lebar) -->
                                        <td
                                            class="w-48 px-5 py-4 text-sm font-bold text-slate-800"
                                        >
                                            {{
                                                $peserta[
                                                    'nama'
                                                ]
                                            }}
                                        </td>

                                        <td class="w-52 px-5 py-4 text-xs font-semibold text-slate-600 break-words">
                                            {{ $peserta['pilihan_2'] }}
                                        </td>

                                        <!-- Waktu (2 baris) -->
                                        <td
                                            class="w-28 px-5 py-4 whitespace-nowrap text-xs"
                                        >
                                            @if ($peserta['dikumpulkan_pada'])
                                                <span
                                                    class="block font-bold text-slate-700"
                                                    >{{
                                                        $peserta[
                                                            'dikumpulkan_pada'
                                                        ]->translatedFormat('d M Y')
                                                    }}</span
                                                >
                                                <span
                                                    class="block text-[10px] font-normal text-slate-500 mt-0.5"
                                                    >{{
                                                        $peserta['dikumpulkan_pada']->format(
                                                            'H:i',
                                                        )
                                                    }} WIB</span
                                                >
                                            @else
                                                <span class="font-bold text-red-600">Tidak mengumpulkan</span>
                                            @endif
                                        </td>

                                        <!-- Pertanyaan & Hasil -->
                                        <td
                                            class="w-80 px-5 py-4 text-xs text-slate-700 break-words"
                                        >
                                            @if ($peserta['jawaban']['jenis'] === 'berkas')
                                                <div class="space-y-1">
                                                    @forelse ($peserta['jawaban']['isi'] as $berkas)
                                                        <a
                                                            class="block w-fit font-bold text-blue-600 hover:underline"
                                                            target="_blank"
                                                            href="{{ asset('storage/' . $berkas) }}"
                                                        >
                                                            📎 Buka berkas {{ $loop->iteration }}
                                                        </a>
                                                    @empty
                                                        <span
                                                            class="text-slate-400"
                                                            >Tidak ada
                                                            berkas.</span
                                                        >
                                                    @endforelse
                                                </div>
                                            @elseif (in_array( $peserta['jawaban']['jenis'], ['wawancara', 'form'], true ))
                                                <dl class="space-y-1.5">
                                                    @forelse ($peserta['jawaban']['isi'] as $idx => $jawaban)
                                                        <div>
                                                            <dt
                                                                class="inline font-bold text-slate-800"
                                                            >
                                                                {{ $idx + 1 }}. {{ $jawaban['label'] }}:
                                                            </dt>
                                                            <dd
                                                                class="inline break-words text-slate-600"
                                                            >
                                                                @if (is_array($jawaban['nilai']))
                                                                    @forelse ($jawaban['nilai'] as $nilai)
                                                                        @if (is_string($nilai) && str_starts_with($nilai, 'rekrutmen/'))
                                                                            <a
                                                                                class="font-bold text-blue-600 hover:underline"
                                                                                target="_blank"
                                                                                href="{{ asset('storage/' . $nilai) }}"
                                                                            >
                                                                                Buka
                                                                                berkas {{ $loop->iteration }} </a
                                                                            >{{
                                                                                $loop->last
                                                                                    ? ''
                                                                                    : ', '
                                                                            }}
                                                                        @else
                                                                            {{
                                                                                is_scalar($nilai)
                                                                                    ? $nilai
                                                                                    : '—'
                                                                            }}{{
                                                                                $loop->last
                                                                                    ? ''
                                                                                    : ', '
                                                                            }}
                                                                        @endif
                                                                    @empty
                                                                        <span
                                                                            class="text-slate-400"
                                                                            >—</span
                                                                        >
                                                                    @endforelse
                                                                @else
                                                                    {{
                                                                        filled($jawaban['nilai']) &&
                                                                        is_scalar($jawaban['nilai'])
                                                                            ? $jawaban['nilai']
                                                                            : '—'
                                                                    }}
                                                                @endif
                                                            </dd>
                                                        </div>
                                                    @empty
                                                        <span
                                                            class="text-slate-400"
                                                            >{{
                                                                $peserta['jawaban'][
                                                                    'status'
                                                                ]
                                                            }}</span
                                                        >
                                                    @endforelse
                                                </dl>
                                                @if ($peserta['jawaban']['jenis'] === 'wawancara' &&
                                                    $peserta['jawaban']['oleh'])
                                                    <p class="mt-2 font-semibold text-violet-700 text-[11px]">
                                                        Wawancara oleh {{ $peserta['jawaban']['oleh'] }}
                                                    </p>
                                                @endif
                                            @else
                                                <span class="text-slate-400"
                                                    >—</span
                                                >
                                            @endif
                                        </td>

                                        <!-- Keputusan (dengan wrap untuk "oleh ...") -->
                                        <td class="w-28 px-5 py-4">
                                            @php
                                                $statusSeleksi = $peserta['status_seleksi'];
                                                $kelasStatus = str_contains(strtolower($statusSeleksi), 'lulus')
                                                    ? 'border-emerald-200 bg-emerald-50 text-emerald-700'
                                                    : (str_contains(strtolower($statusSeleksi), 'tidak')
                                                        ? 'border-red-200 bg-red-50 text-red-700'
                                                        : 'border-slate-200 bg-slate-50 text-slate-600');
                                            @endphp
                                            <span
                                                class="inline-flex items-center px-3 py-1.5 rounded-md text-[11px] font-bold border shadow-sm {{ $kelasStatus }}"
                                            >
                                                {{ $statusSeleksi }}
                                            </span>
                                            @if ($peserta['keputusan_oleh'])
                                                <p class="mt-1 text-[10px] font-semibold text-slate-500 leading-tight break-words whitespace-normal max-w-full">
                                                    oleh {{ $peserta['keputusan_oleh'] }}
                                                </p>
                                            @endif
                                        </td>

                                        <!-- Aksi (2 tombol vertikal) -->
                                        @unless ($readOnly)
                                            <td
                                                class="w-40 px-5 py-4 whitespace-nowrap"
                                            >
                                                <div
                                                    class="flex flex-col gap-2"
                                                >
                                                    @if ($tugas->tipe_tugas === 'wawancara')
                                                        <a
                                                            href="{{ route($routePrefix . 'rekrutmen.seleksi.wawancara', ['tahapanId' => $tahapan->id, 'jabatanId' => $jabatan->id, 'tugasId' => $tugas->id, 'pendaftaranId' => $peserta['pendaftaran_id']]) }}"
                                                            class="w-full rounded-md border border-blue-200 bg-blue-50 px-3 py-2 text-xs font-bold tracking-wide text-blue-700 transition hover:bg-blue-100 text-center"
                                                        >
                                                            Lakukan Wawancara
                                                        </a>
                                                    @endif
                                                    <form
                                                        method="POST"
                                                        data-konfirmasi-keputusan
                                                        data-keputusan="Lulus"
                                                        action="{{ route($routePrefix . 'rekrutmen.seleksi.keputusan', ['tahapanId' => $tahapan->id, 'jabatanId' => $jabatan->id, 'pendaftaranId' => $peserta['pendaftaran_id']]) }}"
                                                    >
                                                        @csrf
                                                        <input
                                                            type="hidden"
                                                            name="keputusan"
                                                            value="lulus"
                                                        />
                                                        <button
                                                            type="submit"
                                                            class="w-full rounded-md border border-emerald-200 bg-emerald-50 px-3 py-2 text-xs font-bold tracking-wide text-emerald-700 transition hover:border-emerald-600 hover:bg-emerald-600 hover:text-white"
                                                        >
                                                            Luluskan
                                                        </button>
                                                    </form>
                                                    <form
                                                        method="POST"
                                                        data-konfirmasi-keputusan
                                                        data-keputusan="Tidak Lolos"
                                                        action="{{ route($routePrefix . 'rekrutmen.seleksi.keputusan', ['tahapanId' => $tahapan->id, 'jabatanId' => $jabatan->id, 'pendaftaranId' => $peserta['pendaftaran_id']]) }}"
                                                    >
                                                        @csrf
                                                        <input
                                                            type="hidden"
                                                            name="keputusan"
                                                            value="tidak_lolos"
                                                        />
                                                        <button
                                                            type="submit"
                                                            class="w-full rounded-md border border-red-200 bg-red-50 px-3 py-2 text-xs font-bold tracking-wide text-red-700 transition hover:border-red-600 hover:bg-red-600 hover:text-white"
                                                        >
                                                            Tidak Lolos
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        @endunless
                                    </tr>
                                @empty
                                    <tr>
                                        <td
                                            colspan="{{ $readOnly ? 6 : 7 }}"
                                            class="px-5 py-10 text-center text-sm text-slate-500"
                                        >
                                            Belum ada peserta pada jabatan ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>
            @empty
                <div
                    class="rounded-lg border border-dashed border-slate-300 bg-white p-12 text-center text-sm font-semibold text-slate-500"
                >
                    Belum ada tugas untuk jabatan ini pada tahapan yang dipilih.
                </div>
            @endforelse
        </div>
    </div>
    <!-- SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: @json (session('success_title', 'Data berhasil disimpan')),
                text: @json (session('success')),
                confirmButtonText: 'Mengerti',
                confirmButtonColor: '#2563eb',
            });
            @elseif (session('error') || session('error_server') || $errors->any())
            Swal.fire({
                icon: 'error',
                title: 'Proses belum berhasil',
                text: @json (session('error') ?? (session('error_server') ?? $errors->first())),
                confirmButtonText: 'Periksa kembali',
                confirmButtonColor: '#2563eb',
            });
            @endif

            document
                .querySelectorAll('form[data-konfirmasi-keputusan]')
                .forEach((form) => {
                    form.addEventListener('submit', (event) => {
                        if (form.dataset.dikonfirmasi === 'ya') return;
                        event.preventDefault();
                        event.stopPropagation();
                        Swal.fire({
                            icon:
                                form.dataset.keputusan === 'Lulus'
                                    ? 'question'
                                    : 'warning',
                            title: `Tetapkan ${form.dataset.keputusan}?`,
                            text: 'Apakah anda yakin dengan keputusan anda pada peserta ini.',
                            showCancelButton: true,
                            confirmButtonText: 'Ya, simpan',
                            cancelButtonText: 'Batal',
                            confirmButtonColor:
                                form.dataset.keputusan === 'Lulus'
                                    ? '#059669'
                                    : '#dc2626',
                        }).then((hasil) => {
                            if (hasil.isConfirmed) {
                                form.dataset.dikonfirmasi = 'ya';
                                form.submit();
                            }
                        });
                    });
                });
        });
    </script>
</x-app-layout>
