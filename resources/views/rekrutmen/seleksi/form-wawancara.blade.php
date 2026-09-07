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

    <!-- MAIN CONTAINER -->
    <div
        class="py-4 sm:py-8 px-4 sm:px-8 md:px-10 max-w-4xl mx-auto relative z-10 my-6 sm:my-10"
    >
        <!-- Tombol Kembali -->
        <a
            href="{{ route($routePrefix . 'rekrutmen.seleksi.jawaban', ['tahapanId' => $tahapan->id, 'jabatanId' => $jabatan->id]) }}"
            class="inline-flex items-center gap-2 text-xs font-extrabold text-slate-500 transition-colors hover:text-blue-700 mb-6"
        >
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m15 18-6-6 6-6" />
            </svg>
            Kembali ke Hasil Seleksi
        </a>

        <!-- Header Profil Wawancara -->
        <header
            class="bg-white px-5 sm:px-10 py-6 sm:py-8 rounded-xl shadow-sm border border-slate-200"
        >
            <div class="flex items-center gap-2 mb-2">
                <span class="flex h-2 w-2">
                    <span
                        class="animate-ping absolute inline-flex h-2 w-2 rounded-full bg-blue-400 opacity-75"
                    ></span>
                    <span
                        class="relative inline-flex rounded-full h-2 w-2 bg-blue-500"
                    ></span>
                </span>
                <p class="text-[10px] font-extrabold uppercase tracking-widest text-blue-600">Form Wawancara</p>
            </div>

            <h1
                class="text-2xl sm:text-3xl font-extrabold text-slate-800 tracking-tight"
            >
                {{
                    $pendaftaran->mahasiswa?->nama_lengkap ??
                        'Peserta'
                }}
            </h1>

            <div
                class="mt-3 flex flex-wrap items-center gap-x-4 gap-y-2 text-xs font-bold text-slate-500 uppercase tracking-wide"
            >
                <span class="flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path></svg>
                    NIM: {{ $pendaftaran->nim }}
                </span>
                <span class="text-slate-300">|</span>
                <span class="flex items-center gap-1.5 text-blue-700">
                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    {{
                        $jabatan->nama_posisi
                            ? $jabatan->nama_posisi . ' · '
                            : ''
                    }}{{ $jabatan->nama_jabatan }}
                </span>
                <span class="text-slate-300">|</span>
                <span class="flex items-center gap-1.5 text-emerald-600">
                    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    {{ $tahapan->nama_tahapan }}
                </span>
            </div>
        </header>

        <!-- Form Penilaian -->
        <form
            method="POST"
            action="{{ route($routePrefix . 'rekrutmen.seleksi.wawancara.store', ['tahapanId' => $tahapan->id, 'jabatanId' => $jabatan->id, 'tugasId' => $tugas->id, 'pendaftaranId' => $pendaftaran->id]) }}"
            class="mt-6 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm"
        >
            @csrf

            <!-- Header Form -->
            <div
                class="border-b border-slate-100 bg-slate-50 px-5 sm:px-10 py-5 sm:py-6"
            >
                <h2 class="text-sm font-extrabold text-slate-800 tracking-wide">
                    Formulir Seleksi Wawancara
                </h2>
                <p class="mt-1 text-xs font-medium leading-relaxed text-slate-500">Seleksi wawancara dilakukan dengan mewawancarai secara langsung para peserta, kemudian setiap jawaban yang disampaikan dicatat dalam formulir wawancara sesuai dengan kolom pertanyaan dan jawaban yang diberikan.</p>
            </div>

            <!-- Body Form (Daftar Pertanyaan) -->
            <div class="space-y-8 px-5 sm:px-10 py-6 sm:py-8">
                @forelse ($pertanyaan as $pertanyaanItem)
                    @php
                        $namaInput = 'jawaban[' . $pertanyaanItem['key'] . ']';
                        $nilaiSebelumnya = old(
                            'jawaban.' . $pertanyaanItem['key'],
                            $jawabanSebelumnya[$pertanyaanItem['key']] ?? null,
                        );
                    @endphp
                    <div class="space-y-8">
                        <div>
                            <label
                                class="block text-sm font-bold text-slate-700 mb-1 tracking-wide"
                                for="{{ $pertanyaanItem['key'] }}"
                            >
                                {{ $loop->iteration }}. {{ $pertanyaanItem['label'] }}
                                @if ($pertanyaanItem['required'])
                                    <span class="text-red-500 ml-0.5">*</span>
                                @endif
                            </label>

                            @if ($pertanyaanItem['keterangan'])
                                <p class="mb-3 text-[11px] font-medium text-slate-500">{{
                                    $pertanyaanItem[
                                        'keterangan'
                                    ]
                                }}</p>
                            @endif

                            <input
                                id="{{ $pertanyaanItem['key'] }}"
                                type="{{ in_array($pertanyaanItem['tipe'], ['email', 'number', 'date'], true) ? $pertanyaanItem['tipe'] : 'text' }}"
                                name="{{ $namaInput }}"
                                value="{{ is_scalar($nilaiSebelumnya) ? $nilaiSebelumnya : '' }}"
                                @required ($pertanyaanItem['required'])
                                class="w-full border border-slate-300 focus:border-blue-600 focus:ring-0 rounded-md transition-colors text-sm font-bold py-2.5 px-3 sm:px-4"
                                placeholder="Ketik jawaban di sini..."
                            />
                        </div>
                        <hr class="border-slate-100" />

                @empty
                    <div
                        class="rounded-xl border border-amber-200 bg-amber-50 px-6 py-5 flex items-start gap-3"
                    >
                        <svg class="w-5 h-5 text-amber-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        <p class="text-sm font-bold text-amber-800 leading-relaxed">Belum ada pertanyaan wawancara pada konfigurasi tugas ini. Tambahkan pertanyaan melalui Update Informasi sebelum melakukan wawancara.</p>
                    </div>
                @endforelse
                <div
                    class="flex gap-3 sm:gap-5 py-2 sm:flex-row sm:items-center justify-end"
                >
                    <a
                        href="{{ route($routePrefix . 'rekrutmen.seleksi.jawaban', ['tahapanId' => $tahapan->id, 'jabatanId' => $jabatan->id]) }}"
                        class="rounded-lg border border-slate-200 bg-white px-8 py-3.5 text-center text-sm items-center flex font-bold text-slate-600 transition-colors hover:bg-slate-100 w-full sm:w-auto justify-center"
                    >
                        Batal
                    </a>
                    <button
                        type="submit"
                        @disabled ($pertanyaan === [])
                        class="w-full sm:w-auto bg-blue-600 text-white px-6 py-3.5 rounded-lg font-bold text-sm hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2 shadow-md hover:shadow-lg"
                    >
                        Simpan Hasil Wawancara
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                    </button>
                </div>
            </div>
    </div>

    <!-- Footer Form (Aksi) -->
    </form>
    </div>
</x-app-layout>

@if ($errors->any() || session('error') || session('error_server'))
    <x-sweet-alert />
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            Swal.fire({
                icon: 'error',
                title: 'Hasil wawancara belum tersimpan',
                text: @json (session('error') ?? (session('error_server') ?? $errors->first())),
                confirmButtonText: 'Perbaiki sekarang',
                confirmButtonColor: '#2563eb', // Warna disesuaikan dengan DNA biru
                customClass: {
                    popup: 'rounded-xl',
                    confirmButton:
                        'rounded-lg text-[11px] font-extrabold uppercase tracking-widest px-6 py-3',
                },
            });
        });
    </script>
@endif
