<div
    x-show="tab === 2"
    style="display: none"
    x-transition:enter="transition ease-out duration-300 transform"
    x-transition:enter-start="opacity-0 translate-y-4"
>
    <div
        class="bg-white rounded-lg border border-slate-200 shadow-sm overflow-hidden p-6 md:p-8"
    >
        <!-- Header Tab 2 -->
        <div
            class="mb-4 border-b border-slate-200 pb-5 flex flex-col md:flex-row md:items-end justify-between gap-5"
        >
            <div class="flex-1">
                <h2
                    class="text-xl font-extrabold text-slate-800 mb-2 flex items-center gap-2"
                >
                    <div class="w-1.5 h-3.5 bg-blue-600 rounded-full"></div>
                    Formulir Daftar Jabatan:
                    <span
                        class="text-blue-700 ml-1"
                        x-text="pilihan1Name"
                    ></span>
                </h2>

                <p
                    class="text-sm font-medium text-slate-500"
                    x-text="currentTugas?.deskripsi"
                ></p>
            </div>
            <div
                class="bg-slate-50 border border-slate-200 p-4 rounded-lg shrink-0 text-left md:text-right shadow-sm w-full md:w-auto"
            >
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">Tenggat Waktu</p>
                <p class="text-sm font-extrabold text-slate-800 flex items-center md:justify-end gap-1.5">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    @if ($isWaktuTunggal && $rawMulai)
                        {{
                            $rawMulai->translatedFormat(
                                'd M Y',
                            )
                        }}
                    @elseif ($rawMulai && $rawBerakhir)
                        {{
                            $rawBerakhir->translatedFormat(
                                'd M Y',
                            )
                        }}
                    @else
                        Tanpa Batas Waktu
                    @endif
                </p>
            </div>
        </div>

        <div class="p-5 md:p-6 space-y-8">
            <!-- Instruksi Box -->
            <div class="bg-slate-50 border border-slate-200 rounded-lg p-5">
                <!-- Lampiran Template (Jika Ada) -->
                <template
                    x-if="
                        currentTugas?.berkas_template &&
                        currentTugas.berkas_template.length > 0
                    "
                >
                    <div class="flex items-center gap-5">
                        <div
                            class="size-16 flex items-center justify-center text-blue-600 shrink-0"
                        >
                            <svg class="size-full" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3">Lampiran Dokumen Pendukung:</p>
                            <div class="flex flex-wrap gap-2.5">
                                <template
                                    x-for="
                                        (berkas, bIdx) in
                                        currentTugas.berkas_template
                                    "
                                    :key="bIdx"
                                >
                                    <a
                                        :href="'/storage/' + berkas"
                                        target="_blank"
                                        class="inline-flex items-center gap-2 text-xs font-bold text-blue-700 bg-white hover:bg-blue-50 px-4 py-2 rounded-md border border-slate-300 transition-colors shadow-sm"
                                    >
                                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                        Unduh Berkas Lampiran
                                        <span x-text="bIdx + 1"></span>
                                    </a>
                                </template>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Area Dynamic Form -->
            <template x-if="currentTugas?.form && currentTugas.form.length > 0">
                <div class="space-y-6 pt-2">
                    <div class="grid grid-cols-1 gap-6">
                        <template
                            x-for="(field, fIdx) in currentTugas.form"
                            :key="fIdx"
                        >
                            <div class="space-y-2.5">
                                <label
                                    class="block text-sm font-bold text-slate-700"
                                >
                                    <span x-text="field.label"></span>
                                    <span
                                        x-show="field.required"
                                        class="text-red-500 ml-0.5"
                                        >*</span
                                    >
                                </label>

                                <p
                                    x-show="field.keterangan"
                                    x-text="field.keterangan"
                                    class="mt-1.5 text-xs font-normal text-slate-500"
                                ></p>

                                <!-- Text / Number / Email / Date -->
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
                                        :name="`dynamic_answers[${field.label}]`"
                                        :required="field.required"
                                        class="w-full rounded-md text-sm border border-slate-300 focus:border-blue-600 focus:ring-0 bg-white py-2.5 transition-colors shadow-sm"
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
                                        :name="`dynamic_answers[${field.label}]`"
                                        :required="field.required"
                                        rows="4"
                                        class="w-full rounded-md text-sm border border-slate-300 focus:border-blue-600 focus:ring-0 bg-white py-2.5 transition-colors shadow-sm resize-y"
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
                                        :name="`dynamic_answers[${field.label}]`"
                                        :required="field.required"
                                        class="w-full rounded-md text-sm border border-slate-300 focus:border-blue-600 focus:ring-0 bg-white py-2.5 transition-colors shadow-sm"
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
                                                    class="flex items-center cursor-pointer p-3.5 bg-slate-50 rounded-md border border-slate-200 hover:bg-slate-100 hover:border-slate-300 transition-colors w-full sm:w-auto min-w-[140px] shadow-sm"
                                                >
                                                    <input
                                                        :type="field.tipe"
                                                        :name="field.tipe ===
                                                        'checkbox'
                                                            ? `dynamic_answers[${field.label}][]`
                                                            : `dynamic_answers[${field.label}]`"
                                                        :value="opt"
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
                                                        class="text-sm font-bold text-slate-700"
                                                        x-text="opt"
                                                    ></span>
                                                </label>
                                            </template>
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>
                </div>
            </template>

            <!-- Area Unggah File Khusus -->
            <template
                x-if="
                    !currentTugas?.form ||
                    currentTugas.form.length === 0 ||
                    currentTugas?.tipe_tugas === 'unggah_berkas'
                "
            >
                <div class="space-y-4 pt-4 border-t border-slate-200">
                    <label
                        class="block text-base font-extrabold text-slate-800 flex items-center gap-2"
                    >
                        <div class="w-1.5 h-3.5 bg-blue-600 rounded-full"></div>
                        Unggah Berkas Persyaratan / Portofolio Kerja
                    </label>

                    <div
                        class="flex flex-col items-center justify-center p-8 border-2 border-dashed border-slate-300 rounded-lg bg-slate-50 hover:bg-slate-100 hover:border-blue-400 transition-colors"
                    >
                        <svg class="mx-auto h-12 w-12 text-slate-400 mb-3" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>

                        <label
                            for="file_berkas"
                            class="relative cursor-pointer rounded-md font-bold text-blue-600 hover:text-blue-800 text-sm"
                        >
                            <span>Klik untuk Memilih File Penugasan</span>
                            <input
                                id="file_berkas"
                                name="file_berkas"
                                type="file"
                                class="sr-only"
                                :required="!currentTugas?.form ||
                                currentTugas.form.length === 0"
                            />
                        </label>
                        <p class="text-xs font-normal text-slate-500 mt-2" x-text="currentTugas?.format_proyek && currentTugas.format_proyek.length > 0 ? 'Ekstensi: ' + currentTugas.format_proyek.join(', ') : 'Format yang didukung (Maksimal 5MB)'"></p>
                    </div>
                </div>
            </template>
        </div>

        <!-- Footer Actions -->
        <div
            class="pt-7 mt-3 border-t border-slate-200 flex flex-col-reverse sm:flex-row items-center justify-between gap-4 px-5 md:px-6"
        >
            <button
                type="button"
                @click="
                    tab = 1;
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                "
                class="text-sm font-bold text-slate-600 hover:text-slate-900 transition-colors w-full sm:w-auto text-center"
            >
                &larr; Kembali ke Pilih Formasi
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

                    if (form.reportValidity()) {
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
                class="bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm px-8 py-3 rounded-md transition-colors w-full sm:w-auto shadow-sm flex items-center justify-center gap-2"
            >
                Kirim Pendaftaran
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
            </button>
        </div>
    </div>
</div>
