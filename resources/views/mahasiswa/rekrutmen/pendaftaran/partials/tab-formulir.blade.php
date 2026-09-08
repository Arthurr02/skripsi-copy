<div
    x-show="tab === 2"
    x-cloak
    x-transition:enter="transition ease-out duration-300 transform"
    x-transition:enter-start="opacity-0 translate-y-4"
    class="space-y-8"
>
    <!-- Kontainer Utama disamakan dengan Langkah 1 -->
    <div class="bg-white rounded-xl border shadow-sm border-slate-200">
        <!-- Header Section disamakan dengan Langkah 1 -->
        <div
            class="p-8 md:p-10 border-b rounded-t-xl border-slate-100 bg-slate-50 flex flex-col lg:flex-row lg:items-center justify-between gap-6"
        >
            <div class="flex-1">
                <h3
                    class="text-xl font-extrabold text-slate-800 tracking-tight flex items-center gap-3"
                >
                    <div
                        class="w-10 h-10 bg-blue-100 text-blue-600 rounded-md flex items-center justify-center shrink-0"
                    >
                        <!-- Menggunakan icon clipboard form -->
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <div
                        class="flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-2 leading-tight"
                    >
                        Formulir Daftar Formasi:
                        <span
                            class="text-blue-700"
                            x-text="
                                pilihan1Position && pilihan1Name
                                    ? pilihan1Position + ' | ' + pilihan1Name
                                    : pilihan1Name
                            "
                        ></span>
                    </div>
                </h3>

                <h2
                    class="text-sm font-extrabold text-slate-800 tracking-wide mt-2"
                >
                    Instruksi Penugasan
                </h2>
                <p
                    class="mt-1 text-xs font-medium leading-relaxed text-slate-500"
                    x-text="currentTugas?.deskripsi"
                ></p>
                <div class="mt-4 flex flex-wrap gap-3">
                    <template
                        x-for="
                            (berkas, bIdx) in
                            (currentTugas?.berkas_template || [])
                        "
                        :key="bIdx"
                    >
                        <a
                            :href="'/storage/' + berkas"
                            target="_blank"
                            class="group flex w-fit items-center gap-3 rounded-md border border-slate-300 bg-white px-4 py-2.5 transition-colors hover:border-blue-600 hover:bg-slate-50 shadow-sm"
                        >
                            <span
                                class="flex min-w-0 items-center gap-2 text-slate-600 group-hover:text-blue-600"
                            >
                                <svg class="text-blue-600 h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                <span class="truncate text-xs font-bold"
                                    >Unduh Lampiran
                                    <span x-text="bIdx + 1"></span
                                ></span>
                            </span>
                        </a>
                    </template>
                </div>
            </div>
        </div>

        <!-- Body Section padding disamakan dengan Langkah 1 -->
        <div class="flex flex-col px-5 sm:px-10 py-6 sm:py-10">
            <!-- Area Dynamic Form -->
            <template x-if="currentTugas?.form && currentTugas.form.length > 0">
                <div class="space-y-6 pt-2">
                    <div class="grid grid-cols-1 gap-6">
                        <template
                            x-for="(field, fIdx) in currentTugas.form"
                            :key="fIdx"
                        >
                            <!-- SINGLE ROOT ELEMENT UNTUK ALPINE -->
                            <div
                                class="flex flex-col w-full"
                                :data-field-key="`isian_${fIdx}`"
                            >
                                <!-- Label -->
                                <label
                                    class="block text-sm font-bold text-slate-700 mb-1 tracking-wide"
                                >
                                    <span x-text="field.label"></span
                                    ><span
                                        x-show="field.required"
                                        class="text-red-500 ml-0.5"
                                        >*</span
                                    >
                                </label>

                                <p
                                    x-show="field.keterangan"
                                    x-text="field.keterangan"
                                    class="mb-2 text-xs font-normal text-slate-500"
                                ></p>

                                <!-- Text / Number / Email / Date Input -->
                                <template
                                    x-if="
                                        [
                                            'text_short',
                                            'text',
                                            'number',
                                            'date',
                                            'email',
                                        ].includes(field.tipe)
                                    "
                                >
                                    <input
                                        :type="field.tipe === 'text_short' ||
                                        field.tipe === 'text'
                                            ? 'text'
                                            : field.tipe"
                                        :name="`dynamic_answers[isian_${fIdx}]`"
                                        :value="jawabanLama[`isian_${fIdx}`] ||
                                        ''"
                                        :required="field.required"
                                        :maxlength="['text_long', 'textarea', 'long_text'].includes(field.tipe) ? 5000 : 500"
                                        class="w-full border border-slate-300 focus:border-blue-600 focus:ring-0 rounded-md transition-colors text-sm font-bold py-2.5 px-3 sm:px-4 bg-white text-slate-800"
                                    />
                                </template>

                                <!-- Textarea -->
                                <template
                                    x-if="
                                        [
                                            'text_long',
                                            'textarea',
                                            'long_text',
                                        ].includes(field.tipe)
                                    "
                                >
                                    <textarea
                                        :name="`dynamic_answers[isian_${fIdx}]`"
                                        x-text="
                                            jawabanLama[`isian_${fIdx}`] || ''
                                        "
                                        :required="field.required"
                                        maxlength="5000"
                                        rows="4"
                                        class="w-full border border-slate-300 focus:border-blue-600 focus:ring-0 rounded-md transition-colors text-sm font-bold py-2.5 px-3 sm:px-4 bg-white text-slate-800 resize-y"
                                    ></textarea>
                                </template>

                                <!-- Dropdown / Select -->
                                <template
                                    x-if="
                                        ['dropdown', 'select'].includes(
                                            field.tipe,
                                        )
                                    "
                                >
                                    <select
                                        :name="`dynamic_answers[isian_${fIdx}]`"
                                        :value="jawabanLama[`isian_${fIdx}`] ||
                                        ''"
                                        :required="field.required"
                                        class="w-full border border-slate-300 focus:border-blue-600 focus:ring-0 rounded-md transition-colors text-sm font-bold py-2.5 px-3 sm:px-4 bg-white text-slate-800"
                                    >
                                        <option value="">
                                            -- Pilih Salah Satu --
                                        </option>
                                        <template
                                            x-for="(opt, oIdx) in field.options"
                                            :key="oIdx"
                                        >
                                            <template
                                                x-if="opt && opt.trim() !== ''"
                                            >
                                                <option
                                                    :value="opt"
                                                    x-text="opt"
                                                ></option>
                                            </template>
                                        </template>
                                    </select>
                                </template>

                                <!-- Radio / Checkbox group -->
                                <template
                                    x-if="
                                        field.tipe === 'radio' ||
                                        field.tipe === 'checkbox'
                                    "
                                >
                                    <div
                                        class="flex flex-col sm:flex-row gap-3 pt-1 flex-wrap"
                                    >
                                        <template
                                            x-for="(opt, oIdx) in field.options"
                                            :key="oIdx"
                                        >
                                            <template
                                                x-if="opt && opt.trim() !== ''"
                                            >
                                                <label
                                                    class="flex items-center cursor-pointer p-3.5 bg-white rounded-md border border-slate-300 hover:border-blue-500 hover:bg-blue-50 transition-colors w-full sm:w-auto min-w-[140px]"
                                                >
                                                    <input
                                                        :type="field.tipe"
                                                        :name="field.tipe ===
                                                        'checkbox'
                                                            ? `dynamic_answers[isian_${fIdx}][]`
                                                            : `dynamic_answers[isian_${fIdx}]`"
                                                        :value="opt"
                                                        :checked="field.tipe ===
                                                        'checkbox'
                                                            ? (
                                                                  jawabanLama[
                                                                      `isian_${fIdx}`
                                                                  ] || []
                                                              ).includes(opt)
                                                            : jawabanLama[
                                                                  `isian_${fIdx}`
                                                              ] === opt"
                                                        :required="field.tipe ===
                                                        'radio'
                                                            ? field.required
                                                            : false"
                                                        class="text-blue-600 border-slate-300 focus:ring-0 mr-3"
                                                        :class="field.tipe ===
                                                        'checkbox'
                                                            ? 'rounded'
                                                            : 'rounded-full'"
                                                    />
                                                    <span
                                                        class="text-sm font-bold text-slate-800"
                                                        x-text="opt"
                                                    ></span>
                                                </label>
                                            </template>
                                        </template>
                                    </div>
                                </template>

                                <!-- Upload File pada Form Dinamis -->
                                <template x-if="field.tipe === 'file'">
                                    <div
                                        x-data="{
                                            berkas: [],
                                            setBerkas(files) {
                                                this.berkas = Array.from(
                                                    files || [],
                                                ).map((file) => ({
                                                    file,
                                                    nama: file.name,
                                                    ukuran:
                                                        (
                                                            file.size / 1048576
                                                        ).toFixed(2) + ' MB',
                                                }));
                                            },
                                            terimaDrop(files) {
                                                const data = new DataTransfer();
                                                [
                                                    ...this.berkas.map(
                                                        (item) => item.file,
                                                    ),
                                                    ...Array.from(files || []),
                                                ].forEach((file) =>
                                                    data.items.add(file),
                                                );
                                                this.$refs.input.files =
                                                    data.files;
                                                this.setBerkas(data.files);
                                            },
                                            hapusBerkas(indeks) {
                                                this.berkas.splice(indeks, 1);
                                                const data = new DataTransfer();
                                                this.berkas.forEach((item) =>
                                                    data.items.add(item.file),
                                                );
                                                this.$refs.input.files =
                                                    data.files;
                                            },
                                        }"
                                        class="mt-1"
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
                                                window
                                                    .rekrutmenValidateDroppedFiles(
                                                        $refs.input,
                                                        $event.dataTransfer
                                                            .files,
                                                    )
                                                    .then(
                                                        (files) =>
                                                            files &&
                                                            terimaDrop(files),
                                                    )
                                            "
                                            :class="berkas.length
                                                ? 'border-blue-400 bg-blue-50'
                                                : 'border-slate-300 bg-slate-50 hover:border-blue-400 hover:bg-blue-50'"
                                            class="flex cursor-pointer flex-col items-center justify-center rounded-md border-2 border-dashed p-6 text-center transition-colors focus:outline-none sm:p-8"
                                        >
                                            <input
                                                x-ref="input"
                                                @change="
                                                    setBerkas(
                                                        $event.target.files,
                                                    )
                                                "
                                                type="file"
                                                :name="`dynamic_files[isian_${fIdx}][]`"
                                                multiple
                                                class="sr-only"
                                                :accept="((
                                                    field.allowed_formats || []
                                                ).length
                                                    ? field.allowed_formats ||
                                                      []
                                                    : ['pdf', 'doc', 'docx']
                                                )
                                                    .flatMap((format) =>
                                                        format === 'word'
                                                            ? ['.doc', '.docx']
                                                            : format === 'excel'
                                                              ? [
                                                                    '.xls',
                                                                    '.xlsx',
                                                                ]
                                                              : [
                                                                      'image',
                                                                      'gambar',
                                                                      'foto',
                                                                  ].includes(
                                                                      format.toLowerCase(),
                                                                  )
                                                                ? [
                                                                      '.jpg',
                                                                      '.jpeg',
                                                                      '.png',
                                                                      'image/jpeg',
                                                                      'image/png',
                                                                  ]
                                                                : [
                                                                      '.' +
                                                                          format,
                                                                  ],
                                                    )
                                                    .join(',')"
                                                :required="field.required &&
                                                berkas.length === 0"
                                            />
                                            <svg class="mb-2 h-8 w-8" :class="berkas.length ? 'text-blue-600' : 'text-slate-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 0 1-.88-7.903A5 5 0 1 1 15.9 6L16 6a5 5 0 0 1 1 9.9M15 13l-3-3m0 0-3 3m3-3v12" /></svg>
                                            <span
                                                class="text-sm font-bold text-slate-700"
                                                x-text="
                                                    berkas.length
                                                        ? berkas.length +
                                                          ' berkas siap diunggah'
                                                        : 'Klik untuk memilih atau seret berkas ke sini'
                                                "
                                            ></span>
                                            <span
                                                class="mt-1 text-[11px] font-medium text-slate-500"
                                                x-text="
                                                    (
                                                        field.allowed_formats ||
                                                        []
                                                    ).length
                                                        ? 'Format: ' +
                                                          field.allowed_formats
                                                              .join(', ')
                                                              .toUpperCase() +
                                                          ' · Maks. 5 MB per berkas'
                                                        : 'Maks. 5 MB per berkas'
                                                "
                                            ></span>
                                        </div>
                                        <div
                                            x-show="berkas.length"
                                            x-cloak
                                            class="mt-3 space-y-1.5"
                                        >
                                            <template
                                                x-for="(item, indeks) in berkas"
                                                :key="item.nama + indeks"
                                            >
                                                <div
                                                    class="flex items-center justify-between gap-3 rounded-md border border-blue-200 bg-blue-50 px-3 py-2 text-xs font-bold text-blue-800"
                                                >
                                                    <span
                                                        class="truncate"
                                                        x-text="
                                                            item.nama +
                                                            ' · ' +
                                                            item.ukuran
                                                        "
                                                    ></span>
                                                    <button
                                                        type="button"
                                                        @click="
                                                            hapusBerkas(indeks)
                                                        "
                                                        class="shrink-0 font-extrabold text-red-600 hover:text-red-700"
                                                    >
                                                        Hapus
                                                    </button>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </template>

                                <!-- Garis pembatas juga ditampilkan setelah pertanyaan terakhir. -->
                                <hr class="mt-8 border-slate-100" />
                            </div>
                        </template>
                    </div>
                </div>
            </template>

            <!-- Area Unggah File Khusus (Digabung dengan gaya Hover Group Langkah 1) -->
            <template
                x-if="
                    currentTugas &&
                    (!currentTugas.form ||
                        currentTugas.form.length === 0 ||
                        currentTugas?.tipe_tugas === 'unggah_berkas')
                "
            >
                <div
                    x-data="{
                        berkas: [],
                        setBerkas(files) {
                            this.berkas = Array.from(files || []).map(
                                (file) => ({
                                    file,
                                    nama: file.name,
                                    ukuran:
                                        (file.size / 1048576).toFixed(2) +
                                        ' MB',
                                }),
                            );
                        },
                        terimaDrop(files) {
                            const data = new DataTransfer();
                            [
                                ...this.berkas.map((item) => item.file),
                                ...Array.from(files || []),
                            ].forEach((file) => data.items.add(file));
                            this.$refs.input.files = data.files;
                            this.setBerkas(data.files);
                        },
                        hapusBerkas(indeks) {
                            this.berkas.splice(indeks, 1);
                            const data = new DataTransfer();
                            this.berkas.forEach((item) =>
                                data.items.add(item.file),
                            );
                            this.$refs.input.files = data.files;
                        },
                    }"
                    class="mt-4"
                >
                    <label
                        class="text-sm font-bold text-slate-700 mb-1 tracking-wide flex items-center gap-2"
                    >
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        Unggah Berkas Persyaratan / Portofolio Kerja<span
                            class="text-red-500 -ml-0.5"
                            x-show="
                                !currentTugas?.form ||
                                currentTugas.form.length === 0 ||
                                currentTugas?.tipe_tugas === 'unggah_berkas'
                            "
                            >*</span
                        >
                    </label>

                    <div
                        data-field-key="file_berkas"
                        role="button"
                        tabindex="0"
                        @click="$refs.input.click()"
                        @keydown.enter.prevent="$refs.input.click()"
                        @keydown.space.prevent="$refs.input.click()"
                        @dragover.prevent
                        @drop.prevent="
                            window
                                .rekrutmenValidateDroppedFiles(
                                    $refs.input,
                                    $event.dataTransfer.files,
                                )
                                .then((files) => files && terimaDrop(files))
                        "
                        :class="berkas.length
                            ? 'border-blue-400 bg-blue-50'
                            : 'border-slate-300 bg-slate-50 hover:border-blue-400 hover:bg-blue-50'"
                        class="mt-2 flex cursor-pointer flex-col items-center justify-center rounded-md border-2 border-dashed p-6 text-center transition-colors focus:outline-none sm:p-8"
                    >
                        <input
                            x-ref="input"
                            id="file_berkas"
                            name="file_berkas"
                            type="file"
                            class="sr-only"
                            :accept="((currentTugas?.format_proyek || []).length
                                ? currentTugas.format_proyek
                                : ['pdf', 'doc', 'docx']
                            )
                                .flatMap((format) =>
                                    format === 'word'
                                        ? ['.doc', '.docx']
                                        : format === 'excel'
                                          ? ['.xls', '.xlsx']
                                          : [
                                                  'image',
                                                  'gambar',
                                                  'foto',
                                              ].includes(
                                                  format.trim().toLowerCase(),
                                              )
                                            ? [
                                                  '.jpg',
                                                  '.jpeg',
                                                  '.png',
                                                  'image/jpeg',
                                                  'image/png',
                                              ]
                                            : ['.' + format.trim()],
                                )
                                .join(',')"
                            @change="setBerkas($event.target.files)"
                            :required="(!currentTugas?.form ||
                                currentTugas.form.length === 0 ||
                                currentTugas?.tipe_tugas === 'unggah_berkas') &&
                            berkas.length === 0"
                        />
                        <svg class="mb-2 h-8 w-8" :class="berkas.length ? 'text-blue-600' : 'text-slate-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 0 1-.88-7.903A5 5 0 1 1 15.9 6L16 6a5 5 0 0 1 1 9.9M15 13l-3-3m0 0-3 3m3-3v12" /></svg>
                        <span
                            class="text-sm font-bold text-slate-700"
                            x-text="
                                berkas.length
                                    ? berkas.length + ' berkas siap diunggah'
                                    : 'Klik untuk memilih atau seret berkas ke sini'
                            "
                        ></span>
                        <span
                            class="mt-1 text-[11px] font-medium text-slate-500"
                            >Maks. 5 MB per berkas</span
                        >
                    </div>
                    <div
                        x-show="berkas.length"
                        x-cloak
                        class="mt-3 space-y-1.5"
                    >
                        <template
                            x-for="(item, indeks) in berkas"
                            :key="item.nama + indeks"
                        >
                            <div
                                class="flex items-center justify-between gap-3 rounded-md border border-blue-200 bg-blue-50 px-3 py-2 text-xs font-bold text-blue-800"
                            >
                                <span
                                    class="truncate"
                                    x-text="item.nama + ' · ' + item.ukuran"
                                ></span>
                                <button
                                    type="button"
                                    @click="hapusBerkas(indeks)"
                                    class="shrink-0 font-extrabold text-red-600 hover:text-red-700"
                                >
                                    Hapus
                                </button>
                            </div>
                        </template>
                    </div>
                </div>
                <hr class="mt-8 border-slate-100" />
            </template>

            <!-- Footer Actions disamakan dengan gaya Button Langkah 1 -->
            <div
                class="mt-8 flex flex-col-reverse sm:flex-row justify-between items-center gap-4"
            >
                <button
                    type="button"
                    @click="
                        tab = 1;
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    "
                    class="text-sm font-bold text-slate-600 hover:text-slate-900 transition-colors w-full sm:w-auto text-center px-4 py-3"
                >
                    &larr; Kembali
                </button>

                <button
                    type="button"
                    @click="
                        let form = document.getElementById('formPendaftaran');
                        document
                            .querySelectorAll(
                                '.loader, #loader, #preloader, [class*=\'memuat\']',
                            )
                            .forEach((el) => (el.style.display = 'none'));

                        if (this.validasiFormulir() && form.reportValidity()) {
                            Swal.fire({
                                icon: 'question',
                                title: 'Konfirmasi Pendaftaran',
                                text: 'Apakah Anda yakin seluruh berkas dan isian form sudah benar? Pilihan formasi prioritas dan jawaban berkas tidak dapat diubah kembali setelah dikirimkan.',
                                showCancelButton: true,
                                confirmButtonText: 'Ya, Kirim Sekarang',
                                cancelButtonText: 'Periksa Kembali',
                                confirmButtonColor: '#2563eb',
                                cancelButtonColor: '#64748b',
                                reverseButtons: true,
                                customClass: {
                                    popup: 'rounded-lg shadow-sm border border-slate-200 font-sans p-6',
                                    title: 'text-xl font-extrabold text-slate-800',
                                    htmlContainer:
                                        'text-sm font-normal text-slate-500 mt-2',
                                    confirmButton:
                                        'px-5 py-2.5 rounded-md font-bold text-sm text-white mx-1 bg-blue-600 hover:bg-blue-700',
                                    cancelButton:
                                        'px-5 py-2.5 rounded-md font-bold text-sm text-white mx-1 bg-slate-500 hover:bg-slate-600',
                                },
                                buttonsStyling: false,
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    Swal.fire({
                                        title: 'Memproses Pengiriman...',
                                        text: 'Sedang mendaftarkan berkas Anda ke pangkalan data server.',
                                        allowOutsideClick: false,
                                        didOpen: () => {
                                            Swal.showLoading();
                                        },
                                        customClass: {
                                            popup: 'rounded-lg font-sans',
                                        },
                                    });
                                    form.submit();
                                }
                            });
                        } else {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Data Belum Lengkap',
                                text: 'Pastikan seluruh input wajib bertanda bintang merah (*) telah diisi dengan format berkas yang sesuai ketentuan.',
                                confirmButtonColor: '#2563eb',
                                customClass: {
                                    popup: 'rounded-lg border border-slate-200 shadow-sm font-sans',
                                    confirmButton:
                                        'px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm font-bold',
                                },
                            });
                        }
                    "
                    class="w-full sm:w-auto sm:ml-auto bg-blue-600 text-white px-6 py-3.5 rounded-lg font-bold text-sm hover:bg-blue-700 transition-colors flex items-center justify-center gap-2 shadow-md hover:shadow-lg"
                >
                    Kirim Pendaftaran
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </button>
            </div>
        </div>
    </div>
</div>
