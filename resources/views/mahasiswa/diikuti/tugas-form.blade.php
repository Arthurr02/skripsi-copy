<x-app-layout>
    <!-- Background Flat Gelap (DNA Desain Bawaan) -->
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

    <!-- MAIN CONTAINER (Selaras dengan Halaman 1: px-8 md:px-10) -->
    <div
        class="py-4 sm:py-8 px-8 md:px-10 max-w-5xl mx-auto relative z-10 my-6 sm:my-10"
    >
        <!-- Navigasi Kembali (Desain diselaraskan dengan Tombol Reset Halaman 1) -->
        <nav class="mb-7">
            <a
                href="{{ route('mahasiswa.rekrutmen.diikuti.tahapan', $pendaftaran->id) }}"
                class="w-max bg-white hover:bg-slate-50 text-slate-600 hover:text-blue-600 text-xs font-bold px-4 py-2.5 rounded-lg transition-colors border border-slate-200 hover:border-blue-200 flex items-center justify-center gap-2 shadow-sm"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali ke Daftar Tugas
            </a>
        </nav>

        <!-- HEADER SELARAS -->
        <div
            class="mb-8 pb-5 relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6"
        >
            <div>
                <!-- Sub-label -->
                <!-- Tipografi Solid Slate -->
                <h2
                    class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-800 mb-2.5 leading-tight"
                >
                    {{
                        Str::title(
                            str_replace('_', ' ', $tugas->tipe_tugas),
                        )
                    }}
                </h2>
                <div class="flex items-center gap-2 mb-1.5">
                    <span class="relative flex h-2 w-2">
                        <span
                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"
                        ></span>
                        <span
                            class="relative inline-flex rounded-full h-2 w-2 bg-red-500"
                        ></span>
                    </span>
                    <p class="text-[11px] text-slate-500 font-bold uppercase tracking-widest">
                        Deadline:
                        <span class="text-slate-800 font-extrabold ml-1">
                            {{
                                \Carbon\Carbon::parse(
                                    $tugas->waktu_selesai,
                                )->translatedFormat('H:i - d F Y')
                            }}
                        </span>
                    </p>
                </div>
            </div>

            <!-- Info Kotak Status (Selaras 100% dengan Kotak "Periode Aktif" Halaman 1) -->
        </div>

        <!-- AREA KONTEN UTAMA: DIGABUNG DALAM SATU WHITE CARD BESAR (Seperti Halaman 1) -->
        <div
            x-data="{ isEditing: {{ $errors->any() ? 'true' : ($pengumpulan && ! $dapatDikerjakan ? 'false' : ($pengumpulan ? 'false' : 'true')) }} }"
            class="bg-white rounded-lg border border-slate-200 shadow-sm overflow-hidden"
        >
            <!-- 1. BAGIAN LAMPIRAN PENUGASAN (Jika ada) -->
            @if (!empty($lampiranPenugasan))
                <div
                    class="p-8 md:px-10 md:pt-10 border-b bg-blue-50 border-slate-200"
                >
                    <div class="flex items-center gap-4 mb-5">
                        <div>
                            <h3
                                class="text-lg font-extrabold text-slate-800 tracking-tight"
                            >
                                Deskripsi
                            </h3>
                            <p class="text-xs font-medium text-slate-500 mt-0.5">{{ $tugas->deskripsi_tugas }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @foreach ($lampiranPenugasan as $indeks => $berkas)
                            <a
                                href="{{ asset('storage/' . $berkas) }}"
                                target="_blank"
                                class="w-min group flex items-center gap-3 rounded-lg border border-blue-500 bg-white px-4 py-3.5 transition-colors hover:border-blue-700 hover:bg-slate-50 hover:shadow-sm"
                            >
                                <span
                                    class="flex min-w-0 items-center gap-3 text-blue-500 hover:text-blue-700"
                                >
                                    <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                    <span class="truncate text-xs font-bold"
                                        >Unduh Lampiran Penugasan</span
                                    >
                                </span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- 2. BAGIAN FORMULIR JAWABAN -->
            <div class="p-8 md:px-10 md:pb-10">
                <div class="mb-6 flex items-end gap-4">
                    <div
                        class="text-blue-600 rounded-lg shrink-0 bg-blue-50 p-2.5"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                        </svg>
                    </div>
                    <div>
                        <h3
                            class="text-lg font-extrabold text-blue-600 tracking-tight"
                        >
                            Lembar Jawaban
                        </h3>
                    </div>
                </div>

                <form
                    action="{{ route('mahasiswa.rekrutmen.diikuti.tugas_submit', ['pendaftaran' => $pendaftaran->id, 'tugas' => $tugas->id]) }}"
                    method="POST"
                    enctype="multipart/form-data"
                    class="space-y-6"
                >
                    @csrf

                    @if ($errors->has('tugas'))
                        <div
                            class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700"
                        >
                            {{
                                $errors->first(
                                    'tugas',
                                )
                            }}
                        </div>
                    @endif

                    @if (!empty($komponenForm))
                        @foreach ($komponenForm as $item)
                            @php
                                $fieldName = filled($item['id'] ?? null)
                                    ? $item['id']
                                    : (filled($item['name'] ?? null)
                                        ? $item['name']
                                        : 'isian_' . $loop->index);

                                $existingValue =
                                    $jawabanSebelumnya[$fieldName] ??
                                    ($jawabanSebelumnya[$item['label'] ?? ''] ??
                                        old('jawaban_form.' . $fieldName));

                                $isRequired = !empty($item['required']) && $item['required'] == true;
                                $inputType = in_array($item['tipe'] ?? '', ['email', 'number', 'date'], true)
                                    ? $item['tipe']
                                    : 'text';
                            @endphp
                            <div class="space-y-2">
                                <!-- Label Pertanyaan -->
                                <label
                                    class="block text-xs font-bold text-slate-700 uppercase tracking-wide"
                                >
                                    {{
                                        $item['label'] ??
                                            'Pertanyaan'
                                    }}
                                    <template x-if="isEditing">
                                        <span>
                                            @if ($isRequired)
                                                <span
                                                    class="text-red-500 ml-0.5"
                                                    >*</span
                                                >
                                            @endif
                                        </span>
                                    </template>
                                </label>

                                <!-- Keterangan -->
                                @if (!empty($item['keterangan']))
                                    <p
                                        x-show="isEditing"
                                        class="text-[10px] text-slate-500 font-medium mb-2"
                                    >{{
                                        $item[
                                            'keterangan'
                                        ]
                                    }}</p>
                                @endif

                                <!-- MODE BACA -->
                                <div x-show="!isEditing" style="display: none">
                                    <div
                                        class="w-full bg-white border border-slate-200 text-slate-400 text-sm font-bold rounded-lg px-4 py-3 min-h-[46px] shadow-sm"
                                    >
                                        @if (($item['tipe'] ?? null) === 'file' && !empty($existingValue))
                                            @foreach ((array) $existingValue as $berkas)
                                                <li
                                                    class="ml-3 text-blue-400 gap-2"
                                                >
                                                    <a
                                                        href="{{ asset('storage/' . $berkas) }}"
                                                        target="_blank"
                                                        class="mb-1.5 flex underline items-center gap-2 text-xs text-blue-400 hover:text-blue-600 hover:underline font-bold last:mb-0"
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0-3-3m3 3 3-3m2 8H7a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586a1 1 0 0 1 .707.293l5.414 5.414a1 1 0 0 1 .293.707V19a2 2 0 0 1-2 2Z" /></svg>
                                                    Lihat Berkas Tugas {{ $loop->iteration }} </a
                                                    >
                                                </li>
                                            @endforeach
                                        @else
                                            {{
                                                !empty($existingValue)
                                                    ? (is_array($existingValue)
                                                        ? implode(', ', $existingValue)
                                                        : $existingValue)
                                                    : '-'
                                            }}
                                        @endif
                                    </div>
                                </div>

                                <!-- MODE EDIT -->
                                <div x-show="isEditing" style="display: none">
                                    @switch ($item['tipe'] ?? 'text_short')
                                        @case ('text_long')
                                            <textarea
                                                id="{{ $fieldName }}"
                                                name="jawaban_form[{{ $fieldName }}]"
                                                rows="4"
                                                class="w-full border border-slate-300 bg-white text-slate-900 text-sm rounded-lg px-4 py-3.5 focus:border-blue-500 focus:ring-blue-500 transition-colors shadow-sm resize-none leading-relaxed"
                                                placeholder="Ketikkan jawaban Anda..."
                                                {{
                                                    $isRequired
                                                        ? 'required'
                                                        : ''
                                                }}
                                                >{{ $existingValue }}</textarea
                                            >
                                            @break
                                        @case ('dropdown')
                                        @case ('select')
                                            <div class="relative">
                                                <select
                                                    id="{{ $fieldName }}"
                                                    name="jawaban_form[{{ $fieldName }}]"
                                                    class="w-full border border-slate-300 bg-white text-slate-900 text-sm font-medium rounded-lg px-4 py-3.5 appearance-none focus:border-blue-500 focus:ring-blue-500 shadow-sm transition-colors cursor-pointer"
                                                    {{
                                                        $isRequired
                                                            ? 'required'
                                                            : ''
                                                    }}
                                                >
                                                    <option
                                                        value=""
                                                        disabled
                                                        {{
                                                            empty($existingValue)
                                                                ? 'selected'
                                                                : ''
                                                        }}
                                                        >-- Pilih Opsi --
                                                    </option>
                                                    @foreach ($item['options'] ?? [] as $opsi)
                                                        <option
                                                            value="{{ $opsi }}"
                                                            {{
                                                                $existingValue === $opsi
                                                                    ? 'selected'
                                                                    : ''
                                                            }}
                                                            >{{ $opsi }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <div
                                                    class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-slate-400"
                                                >
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                                </div>
                                            </div>
                                            @break
                                        @case ('file')
                                            @php $berkasTersimpan = array_values((array) $existingValue); @endphp
                                            <div
                                                x-data="{ baru: [], tersimpan: @js($berkasTersimpan), tampilkan(files) { this.baru = Array.from(files || []).map(file => ({ file, nama: file.name, ukuran: (file.size / 1048576).toFixed(2) + ' MB' })); }, terimaDrop(files) { const data = new DataTransfer(); [...this.baru.map(item => item.file), ...Array.from(files || [])].forEach(file => data.items.add(file)); this.$refs.input.files = data.files; this.tampilkan(data.files); }, hapusBaru(i) { this.baru.splice(i, 1); const data = new DataTransfer(); this.baru.forEach(item => data.items.add(item.file)); this.$refs.input.files = data.files; }, hapusTersimpan(i) { this.tersimpan.splice(i, 1); } }"
                                                class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm"
                                            >
                                                <div
                                                    role="button"
                                                    tabindex="0"
                                                    @click="$refs.input.click()"
                                                    @keydown.enter.prevent="
                                                        $refs.input.click()
                                                    "
                                                    @keydown.space.prevent="
                                                        $refs.input.click()
                                                    "
                                                    @dragover.prevent
                                                    @drop.prevent="
                                                        terimaDrop(
                                                            $event.dataTransfer
                                                                .files,
                                                        )
                                                    "
                                                    :class="baru.length
                                                        ? 'border-blue-300 bg-blue-50'
                                                        : 'border-slate-300 bg-slate-50 hover:border-blue-400 hover:bg-blue-50'"
                                                    class="flex cursor-pointer flex-col items-center justify-center rounded-lg border-2 border-dashed p-6 text-center transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                                                >
                                                    <input
                                                        x-ref="input"
                                                        @change="
                                                            tampilkan(
                                                                $event.target
                                                                    .files,
                                                            )
                                                        "
                                                        type="file"
                                                        id="{{ $fieldName }}"
                                                        name="jawaban_file[{{ $fieldName }}][]"
                                                        multiple
                                                        class="sr-only"
                                                        accept="{{ collect($item['allowed_formats'] ?? [])->flatMap(fn ($format) => $format === 'word' ? ['.doc,.docx'] : ($format === 'excel' ? ['.xls,.xlsx'] : ['.' . $format]))->implode(',') }}"
                                                        {{
                                                            $isRequired && empty($berkasTersimpan)
                                                                ? 'required'
                                                                : ''
                                                        }}
                                                    />
                                                    <svg class="mb-2 h-8 w-8" :class="baru.length ? 'text-blue-600' : 'text-slate-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 0 1-.88-7.903A5 5 0 1 1 15.9 6L16 6a5 5 0 0 1 1 9.9M15 13l-3-3m0 0-3 3m3-3v12" /></svg>
                                                    <span
                                                        class="text-xs font-bold text-slate-700"
                                                        x-text="
                                                            baru.length
                                                                ? baru.length +
                                                                  ' berkas baru siap diunggah'
                                                                : 'Klik untuk memilih atau seret berkas ke sini'
                                                        "
                                                    ></span>
                                                    <span
                                                        class="mt-1 text-[10px] text-slate-500 font-medium"
                                                        >Maks. 5 MB per
                                                        berkas</span
                                                    >
                                                </div>
                                                <div
                                                    x-show="baru.length"
                                                    x-cloak
                                                    class="mt-3 space-y-1.5"
                                                >
                                                    <template
                                                        x-for="
                                                            (berkas, i) in baru
                                                        "
                                                        :key="berkas.nama + i"
                                                        ><div
                                                            class="flex items-center justify-between gap-3 rounded-md border border-blue-200 bg-blue-50 px-3 py-2 text-xs font-bold text-blue-800"
                                                        >
                                                            <span
                                                                class="truncate"
                                                                x-text="
                                                                    berkas.nama +
                                                                    ' · ' +
                                                                    berkas.ukuran
                                                                "
                                                            ></span
                                                            ><button
                                                                type="button"
                                                                @click="
                                                                    hapusBaru(i)
                                                                "
                                                                class="shrink-0 font-extrabold text-red-600 hover:text-red-700"
                                                            >
                                                                Hapus
                                                            </button>
                                                        </div></template
                                                    >
                                                </div>
                                                <div
                                                    x-show="tersimpan.length"
                                                    x-cloak
                                                    class="mt-3 space-y-1.5 border-t border-slate-100 pt-3"
                                                >
                                                    <p class="text-[10px] font-bold uppercase tracking-wider text-slate-500">Berkas tersimpan</p>
                                                    <template
                                                        x-for="
                                                            (berkas, i) in
                                                            tersimpan
                                                        "
                                                        :key="berkas"
                                                        ><div
                                                            class="flex items-center justify-between gap-3 rounded-md border border-slate-200 bg-white px-3 py-2 text-xs font-bold text-slate-700"
                                                        >
                                                            <input
                                                                type="hidden"
                                                                name="jawaban_file_pertahankan[{{ $fieldName }}][]"
                                                                :value="berkas"
                                                            /><a
                                                                :href="'/storage/' +
                                                                berkas"
                                                                target="_blank"
                                                                class="truncate hover:text-blue-600 hover:underline transition-colors"
                                                                x-text="
                                                                    berkas
                                                                        .split(
                                                                            '/',
                                                                        )
                                                                        .pop()
                                                                "
                                                            ></a
                                                            ><button
                                                                type="button"
                                                                @click="
                                                                    hapusTersimpan(
                                                                        i,
                                                                    )
                                                                "
                                                                class="shrink-0 font-extrabold text-red-600 hover:text-red-700"
                                                            >
                                                                Hapus
                                                            </button>
                                                        </div></template
                                                    >
                                                </div>
                                                <p class="mt-3 text-[10px] text-slate-500 font-medium">{{ !empty($item['allowed_formats']) ? 'Format: ' . implode(', ', $item['allowed_formats']) . ' · ' : '' }}Berkas terhapus tidak akan disimpan.</p>
                                            </div>
                                            @error ('jawaban_file.' . $fieldName)
                                                <p class="text-xs font-semibold text-red-600 mt-1">{{ $message }}</p>
                                            @enderror
                                            @break
                                        @case ('pilihan_ganda')
                                        @case ('radio')
                                            <div class="space-y-2.5 mt-3">
                                                @foreach ($item['options'] ?? [] as $opsi)
                                                    <label
                                                        class="flex items-center gap-3 cursor-pointer group"
                                                    >
                                                        <input
                                                            type="radio"
                                                            name="jawaban_form[{{ $fieldName }}]"
                                                            value="{{ $opsi }}"
                                                            class="w-4 h-4 text-blue-600 bg-white border-slate-300 focus:ring-blue-500 shadow-sm"
                                                            {{
                                                                $existingValue === $opsi
                                                                    ? 'checked'
                                                                    : ''
                                                            }}
                                                            {{
                                                                $isRequired
                                                                    ? 'required'
                                                                    : ''
                                                            }}
                                                        />
                                                        <span
                                                            class="text-sm text-slate-700 font-medium group-hover:text-blue-600 transition-colors"
                                                            >{{ $opsi }}</span
                                                        >
                                                    </label>
                                                @endforeach
                                            </div>
                                            @break
                                        @case ('checkbox')
                                            <div class="space-y-2.5 mt-3">
                                                @foreach ($item['options'] ?? [] as $opsi)
                                                    <label
                                                        class="flex items-center gap-3 cursor-pointer group"
                                                    >
                                                        <input
                                                            type="checkbox"
                                                            name="jawaban_form[{{ $fieldName }}][]"
                                                            value="{{ $opsi }}"
                                                            class="w-4 h-4 text-blue-600 bg-white border-slate-300 rounded focus:ring-blue-500 shadow-sm"
                                                            {{
                                                                (is_array($existingValue) &&
                                                                    in_array($opsi, $existingValue)) ||
                                                                $existingValue === $opsi
                                                                    ? 'checked'
                                                                    : ''
                                                            }}
                                                        />
                                                        <span
                                                            class="text-sm text-slate-700 font-medium group-hover:text-blue-600 transition-colors"
                                                            >{{ $opsi }}</span
                                                        >
                                                    </label>
                                                @endforeach
                                            </div>
                                            @break
                                        @default
                                            <input
                                                type="{{ $inputType }}"
                                                id="{{ $fieldName }}"
                                                name="jawaban_form[{{ $fieldName }}]"
                                                value="{{ is_array($existingValue) ? implode(', ', $existingValue) : $existingValue }}"
                                                class="w-full border border-slate-300 bg-white text-slate-900 text-sm font-medium rounded-lg px-4 py-3.5 focus:border-blue-500 focus:ring-blue-500 transition-colors shadow-sm"
                                                placeholder="Ketikkan jawaban Anda..."
                                                {{
                                                    $isRequired
                                                        ? 'required'
                                                        : ''
                                                }}
                                            />
                                    @endswitch
                                </div>
                                @error ('jawaban_form.' . $fieldName)
                                    <p class="text-xs font-semibold text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        @endforeach
                    @else
                        <input
                            type="hidden"
                            name="jawaban_form[_konfirmasi]"
                            value="1"
                        />
                        <div
                            class="rounded-lg border border-blue-200 bg-blue-50 p-5 text-sm text-blue-800 font-medium flex items-start gap-3"
                        >
                            <svg class="w-5 h-5 shrink-0 mt-0.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Tidak ada isian tambahan untuk penugasan ini. Tekan
                            tombol kirim untuk mengonfirmasi penyelesaian tugas.
                        </div>
                    @endif

                    <!-- Area Submit / Edit -->
                    <div
                        class="pt-6 border-t border-slate-100 flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-3 mt-8"
                    >
                        @if ($pengumpulan && $dapatDikerjakan)
                            <button
                                type="button"
                                x-show="!isEditing"
                                @click="isEditing = true"
                                class="w-full md:w-auto px-6 py-3 bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold rounded-lg shadow-sm transition-colors flex justify-center items-center gap-2"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                Edit Jawaban
                            </button>
                            <button
                                type="button"
                                x-show="isEditing"
                                @click="isEditing = false"
                                style="display: none"
                                class="px-5 py-3 text-xs font-bold text-slate-500 hover:text-slate-800 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors"
                            >
                                Batal
                            </button>
                            <button
                                type="submit"
                                x-show="isEditing"
                                style="display: none"
                                class="w-full md:w-auto px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg shadow-sm transition-colors flex justify-center items-center gap-2"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Simpan Perubahan
                            </button>
                        @elseif (!$pengumpulan)
                            <button
                                type="submit"
                                class="w-full md:w-auto px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg shadow-sm transition-colors flex justify-center items-center gap-2"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                                Kirim Jawaban
                            </button>
                        @else
                            <div
                                class="w-full md:w-auto rounded-lg border border-emerald-200 bg-emerald-50 px-6 py-3 text-xs font-bold text-emerald-700 flex items-center justify-center gap-2"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Jawaban telah terkirim
                            </div>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

<!-- SweetAlert tetap sama -->
@if (session('success'))
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            Swal.fire({
                icon: 'success',
                title: 'Penugasan berhasil dikirim',
                text: @json (session('success')),
                confirmButtonText: 'Mengerti',
                confirmButtonColor: '#2563eb',
                customClass: {
                    popup: 'rounded-lg shadow-sm border border-slate-200 font-sans',
                    title: 'text-xl font-extrabold text-slate-800 tracking-tight',
                    htmlContainer: 'text-sm font-normal text-slate-500',
                    confirmButton: 'px-6 py-2.5 rounded-md font-bold text-sm',
                },
            });
        });
    </script>
@endif

@if ($errors->any())
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            Swal.fire({
                icon: 'error',
                title: 'Pengiriman belum berhasil',
                html: '<p class="text-sm text-slate-500">Periksa kembali berkas atau isian berikut:</p><ul class="mt-3 space-y-1 text-left text-sm font-semibold text-red-600">@foreach ($errors->all() as $error)<li>• {{
            addslashes(
                $error,
            )
        }}</li>@endforeach</ul>',
                confirmButtonText: 'Perbaiki sekarang',
                confirmButtonColor: '#2563eb',
                customClass: {
                    popup: 'rounded-lg shadow-sm border border-slate-200 font-sans',
                    title: 'text-xl font-extrabold text-slate-800 tracking-tight',
                    confirmButton: 'px-6 py-2.5 rounded-md font-bold text-sm',
                },
            });
        });
    </script>
@endif
