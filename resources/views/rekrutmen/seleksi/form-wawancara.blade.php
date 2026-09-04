<x-app-layout>
    <div class="mx-auto my-8 max-w-4xl px-4 sm:px-8">
        <a
            href="{{ route($routePrefix . 'rekrutmen.seleksi.jawaban', ['tahapanId' => $tahapan->id, 'jabatanId' => $jabatan->id]) }}"
            class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-500 transition hover:text-blue-700"
        >
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m15 18-6-6 6-6" />
            </svg>
            Kembali ke Hasil Seleksi
        </a>

        <header class="mt-5 rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-[10px] font-extrabold uppercase tracking-[0.18em] text-violet-600">
                Form Wawancara
            </p>
            <h1 class="mt-2 text-2xl font-extrabold text-slate-800">
                {{ $pendaftaran->mahasiswa?->nama_lengkap ?? 'Peserta' }}
            </h1>
            <div class="mt-2 flex flex-wrap gap-x-4 gap-y-1 text-sm text-slate-500">
                <span>NIM: {{ $pendaftaran->nim }}</span>
                <span>{{ $jabatan->nama_posisi ? $jabatan->nama_posisi . ' · ' : '' }}{{ $jabatan->nama_jabatan }}</span>
                <span>{{ $tahapan->nama_tahapan }}</span>
            </div>
        </header>

        <form
            method="POST"
            action="{{ route($routePrefix . 'rekrutmen.seleksi.wawancara.store', ['tahapanId' => $tahapan->id, 'jabatanId' => $jabatan->id, 'tugasId' => $tugas->id, 'pendaftaranId' => $pendaftaran->id]) }}"
            class="mt-5 overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm"
        >
            @csrf

            <div class="border-b border-slate-100 bg-slate-50 px-6 py-5">
                <h2 class="text-sm font-extrabold text-slate-800">Catatan dan Penilaian Pewawancara</h2>
                <p class="mt-1 text-xs leading-relaxed text-slate-500">
                    Pertanyaan di bawah berasal dari konfigurasi wawancara pada tahapan ini. Menyimpan formulir tidak mengubah konfirmasi kehadiran peserta.
                </p>
            </div>

            <div class="space-y-6 p-6">
                @forelse ($pertanyaan as $pertanyaanItem)
                    @php
                        $namaInput = 'jawaban[' . $pertanyaanItem['key'] . ']';
                        $nilaiSebelumnya = old('jawaban.' . $pertanyaanItem['key'], $jawabanSebelumnya[$pertanyaanItem['key']] ?? null);
                    @endphp
                    <div>
                        <label class="block text-sm font-bold text-slate-700" for="{{ $pertanyaanItem['key'] }}">
                            {{ $loop->iteration }}. {{ $pertanyaanItem['label'] }}
                            @if ($pertanyaanItem['required'])
                                <span class="text-red-500">*</span>
                            @endif
                        </label>
                        @if ($pertanyaanItem['keterangan'])
                            <p class="mt-1 text-xs text-slate-500">{{ $pertanyaanItem['keterangan'] }}</p>
                        @endif

                        @if ($pertanyaanItem['tipe'] === 'select')
                            <select
                                id="{{ $pertanyaanItem['key'] }}"
                                name="{{ $namaInput }}"
                                class="mt-2 block w-full rounded-md border-slate-300 text-sm text-slate-700 shadow-sm focus:border-violet-500 focus:ring-violet-500"
                                @required($pertanyaanItem['required'])
                            >
                                <option value="">Pilih jawaban</option>
                                @foreach ($pertanyaanItem['options'] as $opsi)
                                    <option value="{{ $opsi }}" @selected($nilaiSebelumnya === $opsi)>{{ $opsi }}</option>
                                @endforeach
                            </select>
                        @elseif ($pertanyaanItem['tipe'] === 'radio')
                            <div class="mt-2 space-y-2">
                                @foreach ($pertanyaanItem['options'] as $opsi)
                                    <label class="flex items-center gap-2 text-sm text-slate-700">
                                        <input type="radio" name="{{ $namaInput }}" value="{{ $opsi }}" @checked($nilaiSebelumnya === $opsi) @required($pertanyaanItem['required']) class="border-slate-300 text-violet-600 focus:ring-violet-500">
                                        {{ $opsi }}
                                    </label>
                                @endforeach
                            </div>
                        @elseif ($pertanyaanItem['tipe'] === 'checkbox')
                            <div class="mt-2 space-y-2">
                                @foreach ($pertanyaanItem['options'] as $opsi)
                                    <label class="flex items-center gap-2 text-sm text-slate-700">
                                        <input type="checkbox" name="{{ $namaInput }}[]" value="{{ $opsi }}" @checked(in_array($opsi, (array) $nilaiSebelumnya, true)) class="rounded border-slate-300 text-violet-600 focus:ring-violet-500">
                                        {{ $opsi }}
                                    </label>
                                @endforeach
                            </div>
                        @elseif ($pertanyaanItem['tipe'] === 'text_long')
                            <textarea
                                id="{{ $pertanyaanItem['key'] }}"
                                name="{{ $namaInput }}"
                                rows="5"
                                @required($pertanyaanItem['required'])
                                class="mt-2 block w-full rounded-md border-slate-300 text-sm text-slate-700 shadow-sm focus:border-violet-500 focus:ring-violet-500"
                            >{{ $nilaiSebelumnya }}</textarea>
                        @else
                            <input
                                id="{{ $pertanyaanItem['key'] }}"
                                type="{{ in_array($pertanyaanItem['tipe'], ['email', 'number', 'date'], true) ? $pertanyaanItem['tipe'] : 'text' }}"
                                name="{{ $namaInput }}"
                                value="{{ is_scalar($nilaiSebelumnya) ? $nilaiSebelumnya : '' }}"
                                @required($pertanyaanItem['required'])
                                class="mt-2 block w-full rounded-md border-slate-300 text-sm text-slate-700 shadow-sm focus:border-violet-500 focus:ring-violet-500"
                            >
                        @endif

                    </div>
                @empty
                    <div class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-4 text-sm text-amber-800">
                        Belum ada pertanyaan wawancara pada konfigurasi tugas ini. Tambahkan pertanyaan melalui Update Informasi sebelum melakukan wawancara.
                    </div>
                @endforelse
            </div>

            <div class="flex flex-col-reverse gap-3 border-t border-slate-100 bg-slate-50 px-6 py-4 sm:flex-row sm:items-center sm:justify-end">
                <a
                    href="{{ route($routePrefix . 'rekrutmen.seleksi.jawaban', ['tahapanId' => $tahapan->id, 'jabatanId' => $jabatan->id]) }}"
                    class="rounded-md border border-slate-300 bg-white px-4 py-2.5 text-center text-xs font-bold text-slate-600 transition hover:bg-slate-100"
                >
                    Batal
                </a>
                <button
                    type="submit"
                    @disabled($pertanyaan === [])
                    class="rounded-md bg-violet-600 px-4 py-2.5 text-xs font-bold text-white shadow-sm transition hover:bg-violet-700 disabled:cursor-not-allowed disabled:bg-slate-300"
                >
                    Simpan Hasil Wawancara
                </button>
            </div>
        </form>
    </div>
</x-app-layout>

@if ($errors->any() || session('error') || session('error_server'))
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            Swal.fire({
                icon: 'error',
                title: 'Hasil wawancara belum tersimpan',
                text: @json(session('error') ?? session('error_server') ?? $errors->first()),
                confirmButtonText: 'Perbaiki sekarang',
                confirmButtonColor: '#7c3aed',
            });
        });
    </script>
@endif
