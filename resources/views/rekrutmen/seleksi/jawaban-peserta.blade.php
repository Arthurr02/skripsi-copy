<x-app-layout>
    <div class="mx-auto my-8 max-w-7xl px-4 sm:px-8">
        <a
            href="{{ route($routePrefix . 'rekrutmen.seleksi') }}"
            class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-500 transition hover:text-blue-700"
        >
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m15 18-6-6 6-6" />
            </svg>
            Kembali ke Daftar Tahapan
        </a>

        <header class="mt-5 rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-[10px] font-extrabold uppercase tracking-[0.18em] text-blue-600">
                Hasil Seleksi Peserta
            </p>
            <h1 class="mt-2 text-2xl font-extrabold text-slate-800">
                {{ $tahapan->nama_tahapan }}
            </h1>
            <p class="mt-1 text-sm text-slate-500">
                {{ $jabatan->nama_posisi ? $jabatan->nama_posisi . ' · ' : '' }}{{ $jabatan->nama_jabatan }}
            </p>
        </header>

        @if (session('success'))
            <div class="mt-5 flex items-start gap-3 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                <svg class="mt-0.5 h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="m5 13 4 4L19 7" />
                </svg>
                <p class="font-semibold">{{ session('success') }}</p>
            </div>
        @endif

        <div class="mt-5 space-y-5">
            @forelse ($tugasDenganJawaban as $tugas)
                <section
                    x-data="{
                        urutanWaktu: 'desc',
                        urutkanWaktu() {
                            this.urutanWaktu = this.urutanWaktu === 'desc' ? 'asc' : 'desc';

                            const baris = Array.from(
                                this.$refs.barisPeserta.querySelectorAll('tr[data-waktu]'),
                            );

                            baris.sort((barisA, barisB) => {
                                const waktuA = Number(barisA.dataset.waktu);
                                const waktuB = Number(barisB.dataset.waktu);

                                // Peserta yang belum mengumpulkan selalu berada setelah peserta yang sudah mengumpulkan.
                                if (waktuA === 0 || waktuB === 0) {
                                    return waktuA === waktuB ? 0 : (waktuA === 0 ? 1 : -1);
                                }

                                return this.urutanWaktu === 'asc'
                                    ? waktuA - waktuB
                                    : waktuB - waktuA;
                            });

                            baris.forEach((baris) => this.$refs.barisPeserta.appendChild(baris));
                        }
                    }"
                    class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm"
                >
                    <header class="flex flex-col gap-3 border-b border-slate-100 bg-slate-50 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">
                                {{ str_replace('_', ' ', $tugas->tipe_tugas) }}
                            </p>
                            <h2 class="mt-1 text-sm font-extrabold text-slate-800">
                                {{ $tugas->deskripsi_tugas ?: 'Hasil penugasan peserta' }}
                            </h2>
                        </div>
                        @if ($tugas->memakai_form)
                            <a
                                href="{{ route($routePrefix . 'rekrutmen.seleksi.export', ['tahapanId' => $tahapan->id, 'jabatanId' => $jabatan->id, 'tugasId' => $tugas->id]) }}"
                                class="inline-flex w-fit items-center gap-1.5 rounded-md border border-emerald-200 bg-emerald-50 px-3 py-2 text-[10px] font-bold uppercase tracking-wide text-emerald-700 transition hover:border-emerald-600 hover:bg-emerald-600 hover:text-white"
                            >
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0-3-3m3 3 3-3m2 8H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586a.707.707 0 0 1 .5.207l5.707 5.707a.707.707 0 0 1 .207.5V19a2 2 0 0 1-2 2Z" />
                                </svg>
                                Export Excel
                            </a>
                        @endif
                    </header>

                    <div class="overflow-x-auto">
                        <table class="min-w-full text-left text-sm">
                            <thead
                                class="border-b border-slate-100 text-[10px] font-extrabold uppercase tracking-wider text-slate-400"
                            >
                                <tr>
                                    <th scope="col" class="px-5 py-3">NIM</th>
                                    <th scope="col" class="px-5 py-3">Nama</th>
                                    <th scope="col" class="px-5 py-3">
                                        <button
                                            type="button"
                                            @click="urutkanWaktu()"
                                            class="inline-flex items-center gap-1 text-left transition hover:text-blue-700"
                                            title="Klik untuk membalik urutan waktu pengumpulan"
                                        >
                                            Waktu Pengumpulan
                                            <svg
                                                class="h-3.5 w-3.5 transition-transform"
                                                :class="urutanWaktu === 'asc' ? 'rotate-180' : ''"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7" />
                                            </svg>
                                        </button>
                                    </th>
                                    <th scope="col" class="px-5 py-3">Pertanyaan &amp; Hasil Tugas</th>
                                    <th scope="col" class="px-5 py-3">Keputusan</th>
                                    <th scope="col" class="px-5 py-3">Aksi</th>
                                </tr>
                            </thead>
                            <tbody x-ref="barisPeserta" class="divide-y divide-slate-100">
                                @forelse ($tugas->jawaban_peserta as $peserta)
                                    <tr
                                        data-waktu="{{ $peserta['dikumpulkan_pada']?->getTimestamp() ?? 0 }}"
                                        class="align-top"
                                    >
                                        <td class="whitespace-nowrap px-5 py-4 font-mono text-xs font-bold text-slate-600">
                                            {{ $peserta['nim'] }}
                                        </td>
                                        <td class="whitespace-nowrap px-5 py-4 font-bold text-slate-800">
                                            {{ $peserta['nama'] }}
                                        </td>
                                        <td class="whitespace-nowrap px-5 py-4 text-xs">
                                            @if ($peserta['dikumpulkan_pada'])
                                                <time class="font-medium text-slate-600" datetime="{{ $peserta['dikumpulkan_pada']->toIso8601String() }}">
                                                    {{ $peserta['dikumpulkan_pada']->translatedFormat('d M Y, H:i') }} WIB
                                                </time>
                                            @else
                                                <span class="font-bold text-red-600">
                                                    Tidak mengumpulkan
                                                </span>
                                            @endif
                                        </td>
                                        <td class="max-w-xl px-5 py-4 text-xs text-slate-600">
                                            @if ($peserta['jawaban']['jenis'] === 'berkas')
                                                <div class="space-y-1">
                                                    @forelse ($peserta['jawaban']['isi'] as $berkas)
                                                        <a
                                                            class="block w-fit font-bold text-blue-600 hover:underline"
                                                            target="_blank"
                                                            href="{{ asset('storage/' . $berkas) }}"
                                                        >
                                                            Buka berkas {{ $loop->iteration }}
                                                        </a>
                                                    @empty
                                                        <span class="text-slate-400">Tidak ada berkas yang dapat dibuka.</span>
                                                    @endforelse
                                                </div>
                                            @elseif (in_array($peserta['jawaban']['jenis'], ['wawancara', 'form'], true))
                                                <dl class="space-y-1.5">
                                                    @forelse ($peserta['jawaban']['isi'] as $jawaban)
                                                        <div>
                                                            <dt class="inline font-bold capitalize text-slate-500">
                                                                {{ $jawaban['label'] }}:
                                                            </dt>
                                                            <dd class="inline break-words">
                                                                @if (is_array($jawaban['nilai']))
                                                                    @forelse ($jawaban['nilai'] as $nilai)
                                                                        @if (is_string($nilai) && str_starts_with($nilai, 'rekrutmen/'))
                                                                            <a class="font-bold text-blue-600 hover:underline" target="_blank" href="{{ asset('storage/' . $nilai) }}">
                                                                                Buka berkas {{ $loop->iteration }}
                                                                            </a>{{ $loop->last ? '' : ', ' }}
                                                                        @else
                                                                            {{ is_scalar($nilai) ? $nilai : '—' }}{{ $loop->last ? '' : ', ' }}
                                                                        @endif
                                                                    @empty
                                                                        <span class="text-slate-400">—</span>
                                                                    @endforelse
                                                                @else
                                                                    {{ filled($jawaban['nilai']) && is_scalar($jawaban['nilai']) ? $jawaban['nilai'] : '—' }}
                                                                @endif
                                                            </dd>
                                                        </div>
                                                    @empty
                                                        <span class="text-slate-400">{{ $peserta['jawaban']['status'] }}</span>
                                                    @endforelse
                                                </dl>
                                            @else
                                                <span class="font-medium text-slate-400">—</span>
                                            @endif
                                        </td>
                                        <td class="whitespace-nowrap px-5 py-4 text-xs">
                                            @php
                                                $statusSeleksi = $peserta['status_seleksi'];
                                                $kelasStatus = str_contains(strtolower($statusSeleksi), 'lulus')
                                                    ? 'border-emerald-200 bg-emerald-50 text-emerald-700'
                                                    : (str_contains(strtolower($statusSeleksi), 'tidak')
                                                        ? 'border-red-200 bg-red-50 text-red-700'
                                                        : 'border-slate-200 bg-slate-50 text-slate-600');
                                            @endphp
                                            <span class="inline-flex rounded-md border px-2 py-1 text-[10px] font-bold {{ $kelasStatus }}">
                                                {{ $statusSeleksi }}
                                            </span>
                                        </td>
                                        <td class="whitespace-nowrap px-5 py-4">
                                            <div class="flex min-w-[10rem] flex-col gap-2">
                                                @if ($tugas->tipe_tugas === 'wawancara')
                                                    <a
                                                        href="{{ route($routePrefix . 'rekrutmen.seleksi.wawancara', ['tahapanId' => $tahapan->id, 'jabatanId' => $jabatan->id, 'tugasId' => $tugas->id, 'pendaftaranId' => $peserta['pendaftaran_id']]) }}"
                                                        class="inline-flex items-center justify-center rounded-md border border-violet-200 bg-violet-50 px-3 py-1.5 text-[10px] font-bold uppercase tracking-wide text-violet-700 transition hover:border-violet-600 hover:bg-violet-600 hover:text-white"
                                                    >
                                                        Lakukan Wawancara
                                                    </a>
                                                @endif
                                                <div class="grid grid-cols-2 gap-2">
                                                    <form method="POST" action="{{ route($routePrefix . 'rekrutmen.seleksi.keputusan', ['tahapanId' => $tahapan->id, 'jabatanId' => $jabatan->id, 'pendaftaranId' => $peserta['pendaftaran_id']]) }}">
                                                        @csrf
                                                        <input type="hidden" name="keputusan" value="lulus">
                                                        <button type="submit" class="w-full rounded-md border border-emerald-200 bg-emerald-50 px-2 py-1.5 text-[10px] font-bold uppercase tracking-wide text-emerald-700 transition hover:border-emerald-600 hover:bg-emerald-600 hover:text-white">
                                                            Luluskan
                                                        </button>
                                                    </form>
                                                    <form method="POST" action="{{ route($routePrefix . 'rekrutmen.seleksi.keputusan', ['tahapanId' => $tahapan->id, 'jabatanId' => $jabatan->id, 'pendaftaranId' => $peserta['pendaftaran_id']]) }}">
                                                        @csrf
                                                        <input type="hidden" name="keputusan" value="tidak_lolos">
                                                        <button type="submit" class="w-full rounded-md border border-red-200 bg-red-50 px-2 py-1.5 text-[10px] font-bold uppercase tracking-wide text-red-700 transition hover:border-red-600 hover:bg-red-600 hover:text-white">
                                                            Tidak Lolos
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-5 py-8 text-center text-sm text-slate-500">
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
</x-app-layout>
