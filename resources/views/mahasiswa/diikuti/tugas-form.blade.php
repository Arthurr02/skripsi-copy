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
            href="{{ route($isRiwayatMahasiswa ? 'mahasiswa.riwayat.diikuti.tahapan' : 'mahasiswa.rekrutmen.diikuti.tahapan', $pendaftaran->id) }}"
            class="inline-flex items-center gap-2 text-xs font-extrabold text-slate-500 transition-colors hover:text-blue-700 mb-6"
        >
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m15 18-6-6 6-6" />
            </svg>
            Kembali ke {{ $isRiwayatMahasiswa ? 'Riwayat Tahapan' : 'Daftar Tugas' }}
        </a>

        <!-- Header Profil/Tugas -->
        <header
            class="bg-white px-5 sm:px-10 py-6 sm:py-8 rounded-xl shadow-sm border border-slate-200"
        >
            <h1
                class="text-2xl sm:text-3xl font-extrabold text-slate-800 tracking-tight"
            >
                {{
                    Str::title(
                        str_replace('_', ' ', $tugas->tipe_tugas),
                    )
                }}
            </h1>

            <div
                class="mt-3 flex items-center gap-x-4 gap-y-2 text-xs font-bold text-slate-500 uppercase tracking-wide"
            >
                <div class="flex items-center gap-2 mb-2">
                    <span class="flex h-2 w-2">
                        <span
                            class="animate-ping absolute inline-flex h-2 w-2 rounded-full bg-red-400 opacity-75"
                        ></span>
                        <span
                            class="relative inline-flex rounded-full h-2 w-2 bg-red-500"
                        ></span>
                    </span>
                    <p class="text-[10px] font-light uppercase tracking-widest text-red-600">Deadline: <span class="text-[11px] font-bold"> {{
                            \Carbon\Carbon::parse(
                                $tugas->waktu_selesai,
                            )->translatedFormat('H:i | d F Y')
                        }} </span></p>
                </div>
            </div>
        </header>

        <!-- Form / Main Card -->
        <div
            x-data="{ isEditing: {{ $isRiwayatMahasiswa ? 'false' : ($errors->any() ? 'true' : ($pengumpulan && ! $dapatDikerjakan ? 'false' : ($pengumpulan ? 'false' : 'true'))) }} }"
            class="mt-6 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm"
        >
            <!-- Lampiran / Header Form -->
            @if (!empty($lampiranPenugasan))
                <div
                    class="border-b border-slate-100 bg-blue-50 px-5 sm:px-10 py-5 sm:py-6"
                >
                    <h2
                        class="text-sm font-extrabold text-slate-800 tracking-wide"
                    >
                        Instruksi Penugasan
                    </h2>
                    <p class="mt-1 text-xs font-medium leading-relaxed text-slate-500">{{ $tugas->deskripsi_tugas }}</p>

                    <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @foreach ($lampiranPenugasan as $indeks => $berkas)
                            <a
                                href="{{ asset('storage/' . $berkas) }}"
                                target="_blank"
                                class="w-min group flex items-center gap-3 rounded-md border border-slate-300 bg-white px-4 py-2.5 transition-colors hover:border-blue-600 hover:bg-slate-50 shadow-sm"
                            >
                                <span
                                    class="flex min-w-0 items-center gap-2 text-slate-600 group-hover:text-blue-600"
                                >
                                    <svg class="text-blue-600 h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                    <span class="truncate text-xs font-bold"
                                        >Unduh Lampiran {{ $indeks + 1 }}</span
                                    >
                                </span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @else
                <div
                    class="border-b border-slate-100 bg-slate-50 px-5 sm:px-10 py-5 sm:py-6"
                >
                    <h2
                        class="text-sm font-extrabold text-slate-800 tracking-wide"
                    >
                        Formulir Pengerjaan Tugas
                    </h2>
                    <p class="mt-1 text-xs font-medium leading-relaxed text-slate-500">Lengkapi seluruh isian yang tersedia dengan benar. Pastikan untuk menyimpan perubahan sebelum batas waktu berakhir.</p>
                </div>
            @endif

            <form
                action="{{ route('mahasiswa.rekrutmen.diikuti.tugas_submit', ['pendaftaran' => $pendaftaran->id, 'tugas' => $tugas->id]) }}"
                method="POST"
                enctype="multipart/form-data"
                class="px-5 sm:px-10 py-6 sm:py-8"
            >
                @csrf

                @if ($errors->has('tugas'))
                    <div
                        class="rounded-xl border border-red-200 bg-red-50 px-6 py-5 flex items-start gap-3 mb-8"
                    >
                        <svg class="w-5 h-5 text-red-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        <p class="text-sm font-bold text-red-800 leading-relaxed">{{
                            $errors->first(
                                'tugas',
                            )
                        }}</p>
                    </div>
                @endif

                <!-- Body Form (Daftar Isian) -->
                <div class="space-y-8">
                    @forelse ($komponenForm ?? [] as $item)
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
                        <div>
                            <!-- Label Pertanyaan -->
                            <label
                                class="block text-sm font-bold text-slate-700 mb-1 tracking-wide"
                                for="{{ $fieldName }}"
                            >
                                {{ $loop->iteration }}. {{
                                    $item['label'] ??
                                        'Pertanyaan'
                                }}
                                <template x-if="isEditing">
                                    <span>
                                        @if ($isRequired)
                                            <span class="text-red-500 ml-0.5"
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
                                    class="mb-3 text-[11px] font-medium text-slate-500"
                                >{{
                                    $item[
                                        'keterangan'
                                    ]
                                }}</p>
                            @endif

                            <!-- MODE BACA -->
                            <div x-show="!isEditing" style="display: none">
                                <div
                                    class="w-full border border-slate-200 bg-slate-50 text-slate-700 text-sm font-bold rounded-md px-4 py-3 min-h-[46px]"
                                >
                                    @if (($item['tipe'] ?? null) === 'file' && !empty($existingValue))
                                        @foreach ((array) $existingValue as $berkas)
                                            <a
                                                href="{{ asset('storage/' . $berkas) }}"
                                                target="_blank"
                                                class="mb-1.5 flex items-center underline gap-2 text-sm text-blue-600 hover:text-blue-800 font-bold transition-colors last:mb-0"
                                            >
                                                <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0-3-3m3 3 3-3m2 8H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586a1 1 0 0 1 .707.293l5.414 5.414a1 1 0 0 1 .293.707V19a2 2 0 0 1-2 2Z" /></svg>
                                                Lihat Berkas Jawaban {{ $loop->iteration }}
                                            </a>
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
                                            class="w-full border border-slate-300 focus:border-blue-600 focus:ring-0 rounded-md transition-colors text-sm font-bold py-2.5 px-3 sm:px-4 resize-none"
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
                                                class="w-full border border-slate-300 focus:border-blue-600 focus:ring-0 rounded-md transition-colors text-sm font-bold py-2.5 px-3 sm:px-4 appearance-none cursor-pointer bg-white"
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
                                            class="rounded-md border border-slate-200 bg-white p-4"
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
                                                    ? 'border-blue-400 bg-blue-50'
                                                    : 'border-slate-300 bg-slate-50 hover:border-blue-400 hover:bg-blue-50'"
                                                class="flex cursor-pointer flex-col items-center justify-center rounded-md border-2 border-dashed p-6 text-center transition-colors focus:outline-none"
                                            >
                                                <input
                                                    x-ref="input"
                                                    @change="
                                                        tampilkan(
                                                            $event.target.files,
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
                                                    class="text-sm font-bold text-slate-700"
                                                    x-text="
                                                        baru.length
                                                            ? baru.length +
                                                              ' berkas baru siap diunggah'
                                                            : 'Klik untuk memilih atau seret berkas ke sini'
                                                    "
                                                ></span>
                                                <span
                                                    class="mt-1 text-[11px] font-medium text-slate-500"
                                                    >Maks. 5 MB per berkas</span
                                                >
                                            </div>
                                            <div
                                                x-show="baru.length"
                                                x-cloak
                                                class="mt-3 space-y-1.5"
                                            >
                                                <template
                                                    x-for="(berkas, i) in baru"
                                                    :key="berkas.nama + i"
                                                >
                                                    <div
                                                        class="flex items-center justify-between gap-3 rounded-md border border-blue-200 bg-blue-50 px-3 py-2 text-xs font-bold text-blue-800"
                                                    >
                                                        <span
                                                            class="truncate"
                                                            x-text="
                                                                berkas.nama +
                                                                ' · ' +
                                                                berkas.ukuran
                                                            "
                                                        ></span>
                                                        <button
                                                            type="button"
                                                            @click="
                                                                hapusBaru(i)
                                                            "
                                                            class="shrink-0 font-extrabold text-red-600 hover:text-red-700"
                                                        >
                                                            Hapus
                                                        </button>
                                                    </div>
                                                </template>
                                            </div>
                                            <div
                                                x-show="tersimpan.length"
                                                x-cloak
                                                class="mt-3 space-y-1.5 border-t border-slate-100 pt-3"
                                            >
                                                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-500">Berkas tersimpan</p>
                                                <template
                                                    x-for="
                                                        (berkas, i) in tersimpan
                                                    "
                                                    :key="berkas"
                                                >
                                                    <div
                                                        class="flex items-center justify-between gap-3 rounded-md border border-slate-200 bg-white px-3 py-2 text-xs font-bold text-slate-700"
                                                    >
                                                        <input
                                                            type="hidden"
                                                            name="jawaban_file_pertahankan[{{ $fieldName }}][]"
                                                            :value="berkas"
                                                        />
                                                        <a
                                                            :href="'/storage/' +
                                                            berkas"
                                                            target="_blank"
                                                            class="truncate hover:text-blue-600 hover:underline transition-colors"
                                                            x-text="
                                                                berkas
                                                                    .split('/')
                                                                    .pop()
                                                            "
                                                        ></a>
                                                        <button
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
                                                    </div>
                                                </template>
                                            </div>
                                            <p class="mt-3 text-[11px] font-medium text-slate-500">{{ !empty($item['allowed_formats']) ? 'Format: ' . implode(', ', $item['allowed_formats']) . ' · ' : '' }}Berkas terhapus tidak akan disimpan.</p>
                                        </div>
                                        @break
                                    @case ('pilihan_ganda')
                                    @case ('radio')
                                        <div class="space-y-2.5 mt-2">
                                            @foreach ($item['options'] ?? [] as $opsi)
                                                <label
                                                    class="flex items-center gap-3 cursor-pointer group"
                                                >
                                                    <input
                                                        type="radio"
                                                        name="jawaban_form[{{ $fieldName }}]"
                                                        value="{{ $opsi }}"
                                                        class="w-4 h-4 text-blue-600 bg-white border-slate-300 focus:ring-blue-600 focus:ring-0 transition-colors"
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
                                                        class="text-sm font-bold text-slate-700 group-hover:text-blue-600 transition-colors"
                                                        >{{ $opsi }}</span
                                                    >
                                                </label>
                                            @endforeach
                                        </div>
                                        @break
                                    @case ('checkbox')
                                        <div class="space-y-2.5 mt-2">
                                            @foreach ($item['options'] ?? [] as $opsi)
                                                <label
                                                    class="flex items-center gap-3 cursor-pointer group"
                                                >
                                                    <input
                                                        type="checkbox"
                                                        name="jawaban_form[{{ $fieldName }}][]"
                                                        value="{{ $opsi }}"
                                                        class="w-4 h-4 text-blue-600 bg-white border-slate-300 rounded focus:ring-blue-600 focus:ring-0 transition-colors"
                                                        {{
                                                            (is_array($existingValue) &&
                                                                in_array($opsi, $existingValue)) ||
                                                            $existingValue === $opsi
                                                                ? 'checked'
                                                                : ''
                                                        }}
                                                    />
                                                    <span
                                                        class="text-sm font-bold text-slate-700 group-hover:text-blue-600 transition-colors"
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
                                            class="w-full border border-slate-300 focus:border-blue-600 focus:ring-0 rounded-md transition-colors text-sm font-bold py-2.5 px-3 sm:px-4"
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
                                <p class="text-[11px] font-bold text-red-600 mt-1.5">{{ $message }}</p>
                            @enderror
                            @error ('jawaban_file.' . $fieldName)
                                <p class="text-[11px] font-bold text-red-600 mt-1.5">{{ $message }}</p>
                            @enderror
                        </div>
                        @if (!$loop->last)
                            <hr class="border-slate-100" />
                        @endif
                    @empty
                        <input
                            type="hidden"
                            name="jawaban_form[_konfirmasi]"
                            value="1"
                        />
                        <div
                            class="rounded-xl border border-amber-200 bg-amber-50 px-6 py-5 flex items-start gap-3"
                        >
                            <svg class="w-5 h-5 text-amber-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            <p class="text-sm font-bold text-amber-800 leading-relaxed">Tidak ada isian tambahan untuk penugasan ini. Tekan tombol kirim untuk mengonfirmasi penyelesaian tugas.</p>
                        </div>
                    @endforelse
                </div>

                <!-- Area Tombol Aksi Form -->
                <div
                    class="mt-8 flex gap-3 sm:gap-5 py-2 sm:flex-row flex-col-reverse sm:items-center justify-end"
                >
                    @if ($pengumpulan && $dapatDikerjakan)
                        <button
                            type="button"
                            x-show="!isEditing"
                            @click="isEditing = true"
                            class="w-full sm:w-auto bg-slate-800 text-white px-6 py-3.5 rounded-lg font-bold text-sm hover:bg-slate-900 transition-colors flex items-center justify-center gap-2 shadow-md hover:shadow-lg"
                        >
                            Edit Jawaban
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        </button>
                        <button
                            type="button"
                            x-show="isEditing"
                            @click="isEditing = false"
                            style="display: none"
                            class="rounded-lg border border-slate-200 bg-white px-8 py-3.5 text-center text-sm items-center flex font-bold text-slate-600 transition-colors hover:bg-slate-100 w-full sm:w-auto justify-center"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            x-show="isEditing"
                            style="display: none"
                            class="w-full sm:w-auto bg-blue-600 text-white px-6 py-3.5 rounded-lg font-bold text-sm hover:bg-blue-700 transition-colors flex items-center justify-center gap-2 shadow-md hover:shadow-lg"
                        >
                            Simpan Perubahan
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                        </button>
                    @elseif (!$pengumpulan)
                        <button
                            type="submit"
                            class="w-full sm:w-auto bg-blue-600 text-white px-6 py-3.5 rounded-lg font-bold text-sm hover:bg-blue-700 transition-colors flex items-center justify-center gap-2 shadow-md hover:shadow-lg"
                        >
                            Kirim Jawaban
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                        </button>
                    @else
                        <button
                            type="button"
                            disabled
                            class="w-full sm:w-auto bg-slate-100 border border-slate-200 text-slate-400 px-6 py-3.5 rounded-lg font-bold text-sm cursor-not-allowed flex items-center justify-center gap-2"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                            Jawaban telah terkirim
                        </button>
                    @endif
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

@if (session('success'))
    <x-sweet-alert />
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            Swal.fire({
                icon: 'success',
                title: 'Penugasan berhasil dikirim',
                text: @json (session('success')),
                confirmButtonText: 'Mengerti',
                confirmButtonColor: '#2563eb',
                customClass: {
                    popup: 'rounded-xl',
                    confirmButton:
                        'rounded-lg text-[11px] font-extrabold uppercase tracking-widest px-6 py-3',
                },
            });
        });
    </script>
@endif

@if ($errors->any())
    <x-sweet-alert />
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            Swal.fire({
                icon: 'error',
                title: 'Pengiriman belum berhasil',
                html: '<p class="text-sm font-medium text-slate-500 mb-2">Periksa kembali berkas atau isian berikut:</p><ul class="space-y-1 text-left text-sm font-bold text-red-600">@foreach ($errors->all() as $error)<li>• {{
            addslashes(
                $error,
            )
        }}</li>@endforeach</ul>',
                confirmButtonText: 'Perbaiki sekarang',
                confirmButtonColor: '#2563eb',
                customClass: {
                    popup: 'rounded-xl',
                    confirmButton:
                        'rounded-lg text-[11px] font-extrabold uppercase tracking-widest px-6 py-3',
                },
            });
        });
    </script>
@endif
