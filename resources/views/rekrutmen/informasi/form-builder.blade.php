<!-- BUNGKUS DENGAN TEMPLATE INI -->
<template x-teleport="body">
    <!-- MODAL BACKDROP & WRAPPER UTAMA -->
    <div
        x-show="openFormBuilder"
        class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4 sm:p-6"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        style="display: none"
    >
        <!-- MODAL CONTAINER -->
        <div
            class="bg-white rounded-xl max-w-3xl w-full shadow-sm border border-slate-200 flex flex-col max-h-[90vh]"
            @click.stop
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        >
            <!-- HEADER MODAL -->
            <div
                class="flex justify-between items-center border-b border-slate-100 px-6 sm:px-8 py-5 shrink-0 bg-slate-50/50 rounded-t-xl"
            >
                <div
                    class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-3"
                >
                    <h3
                        class="text-lg font-extrabold text-slate-800 flex items-center gap-2"
                    >
                        <svg x-show="isWawancaraMode" class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path></svg>
                        <svg x-show="!isWawancaraMode" class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002-2h2a2 2 0 002 2M9 12h6m-6 4h6m-8-4h.01M9 16h.01"></path></svg>
                        <span
                            x-text="
                                isWawancaraMode
                                    ? 'Rancang Form Wawancara'
                                    : 'Rancang Form'
                            "
                        ></span>
                    </h3>
                    <span
                        class="hidden sm:block text-slate-300 text-lg font-normal"
                        >|</span
                    >
                    <span
                        class="text-blue-700 text-[10px] font-extrabold uppercase tracking-widest bg-blue-50 px-2.5 py-1 rounded-md border border-blue-100"
                        x-text="activeJabatanName"
                    ></span>
                </div>
                <button
                    type="button"
                    @click="openFormBuilder = false"
                    class="text-slate-400 hover:text-slate-600 bg-white border border-slate-200 hover:bg-slate-100 w-8 h-8 rounded-md flex items-center justify-center transition-colors shadow-sm"
                    title="Tutup Form Builder"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <!-- ISI BUILDER (SCROLLABLE) -->
            <div
                class="overflow-y-auto p-6 sm:p-8 space-y-5 flex-1 min-h-[40vh] bg-slate-50/30 custom-scrollbar"
            >
                <template
                    x-for="(field, fIdx) in currentFormSchema"
                    :key="fIdx"
                >
                    <!-- FIELD CARD -->
                    <div
                        class="p-5 bg-white rounded-xl border border-slate-200 shadow-sm space-y-4 relative group hover:border-blue-300 hover:shadow-md transition-all w-full overflow-hidden"
                    >
                        <!-- Top Bar: Nomor & Tombol Hapus -->
                        <div class="flex justify-between items-start gap-4">
                            <div class="flex-1 flex gap-3 items-start">
                                <!-- Badge Nomor -->
                                <span
                                    class="shrink-0 flex items-center justify-center w-7 h-7 bg-slate-100 text-slate-600 border border-slate-200 text-xs font-black rounded-md mt-1"
                                    x-text="fIdx + 1"
                                ></span>

                                <!-- Input Pertanyaan & Keterangan -->
                                <div class="flex-1 space-y-3 w-full">
                                    <input
                                        type="text"
                                        x-model="field.label"
                                        placeholder="Tuliskan Pertanyaan / Aspek Penilaian..."
                                        class="w-full text-sm font-bold text-slate-800 rounded-md border-slate-300 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 bg-white py-2.5 px-3 transition-colors shadow-sm"
                                    />

                                    <div
                                        x-show="!isWawancaraMode"
                                        class="flex items-start gap-2.5"
                                    >
                                        <svg class="w-4 h-4 text-slate-400 shrink-0 mt-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        <input
                                            type="text"
                                            x-model="field.keterangan"
                                            placeholder="Keterangan pertanyaan (opsional)"
                                            class="w-full text-[11px] font-bold text-slate-600 rounded-md border-slate-200 bg-white focus:bg-white focus:border-blue-400 focus:ring-0 py-2 px-3 transition-colors"
                                        />
                                    </div>
                                </div>
                            </div>

                            <!-- Tombol Hapus (Icon) -->
                            <button
                                type="button"
                                @click="currentFormSchema.splice(fIdx, 1)"
                                class="text-red-500 bg-red-50 hover:bg-red-500 hover:text-white border border-red-100 hover:border-red-500 p-2 rounded-md transition-colors shrink-0 mt-1 shadow-sm"
                                title="Hapus Pertanyaan"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </div>

                        <!-- Pilihan Tipe Input & Wajib -->
                        <div
                            class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-between gap-4 sm:gap-5 pt-4 border-t border-slate-100 mt-2"
                        >
                            <div
                                class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-3 w-full sm:w-max"
                            >
                                <span
                                    class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest shrink-0"
                                    >Tipe Jawaban:
                                    <span
                                        x-show="isWawancaraMode"
                                        class="text-slate-700"
                                        >Teks</span
                                    >
                                </span>
                                <select
                                    x-model="field.tipe"
                                    @change="
                                        field.options = field.options || [
                                            'Opsi 1',
                                        ];
                                        field.allowed_formats =
                                            field.allowed_formats || [];
                                    "
                                    x-show="!isWawancaraMode"
                                    class="w-full sm:w-auto max-w-full text-xs font-bold text-slate-700 rounded-md border-slate-300 py-2 pl-3 pr-8 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 bg-white shadow-sm transition-colors"
                                >
                                    <optgroup
                                        label="Teks & Angka"
                                        x-show="!isWawancaraMode"
                                    >
                                        <option value="text_short">
                                            Teks Pendek
                                        </option>
                                        <option value="text_long">
                                            Teks Paragraf
                                        </option>
                                        <option value="email">Email</option>
                                        <option value="number">Angka</option>
                                    </optgroup>
                                    <optgroup
                                        label="Pilihan Bersarang"
                                        x-show="!isWawancaraMode"
                                    >
                                        <option value="select">
                                            Dropdown Select
                                        </option>
                                        <option value="radio">Radiobox</option>
                                        <option value="checkbox">
                                            Checkbox
                                        </option>
                                    </optgroup>
                                    <optgroup
                                        label="Berkas"
                                        x-show="!isWawancaraMode"
                                    >
                                        <option value="file">
                                            Upload File / Dokumen
                                        </option>
                                    </optgroup>
                                    <optgroup
                                        label="Format Wawancara"
                                        x-show="isWawancaraMode"
                                    >
                                        <option value="text_long">
                                            Teks Paragraf (Catatan Evaluasi)
                                        </option>
                                    </optgroup>
                                </select>
                            </div>

                            <label
                                class="flex items-center text-xs font-bold text-slate-700 cursor-pointer px-3 rounded-md transition-colors shrink-0 w-max"
                                x-show="!isWawancaraMode"
                            >
                                <input
                                    type="checkbox"
                                    x-model="field.required"
                                    class="rounded border-slate-300 text-blue-600 focus:ring-blue-600 mr-2.5 h-4 w-4"
                                />
                                <span
                                    x-text="
                                        isWawancaraMode
                                            ? 'Wajib Diisi Pewawancara'
                                            : 'Wajib Diisi Pendaftar'
                                    "
                                ></span>
                            </label>
                        </div>

                        <!-- Opsi Tambahan (Berdasarkan Tipe) -->

                        <!-- Jika Select/Radio/Checkbox -->
                        <div
                            x-show="
                                !isWawancaraMode &&
                                (field.tipe === 'select' ||
                                    field.tipe === 'radio' ||
                                    field.tipe === 'checkbox')
                            "
                            class=""
                            x-transition
                        >
                            <div
                                class="p-4 bg-slate-50 rounded-lg border border-slate-200 mt-1 space-y-3"
                            >
                                <p class="text-[10px] font-extrabold text-slate-500 tracking-widest flex items-center gap-2">
                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path></svg>
                                    Konfigurasi Opsi Jawaban:
                                </p>

                                <div class="space-y-2.5">
                                    <template
                                        x-for="(opt, oIdx) in field.options"
                                        :key="oIdx"
                                    >
                                        <div class="flex gap-2 items-center">
                                            <div
                                                class="w-4 h-4 rounded-full bg-white border-2 border-slate-300 shrink-0"
                                            ></div>
                                            <input
                                                type="text"
                                                x-model="field.options[oIdx]"
                                                placeholder="Tulis opsi..."
                                                class="text-xs font-bold text-slate-700 rounded-md border-slate-300 py-1.5 px-3 flex-1 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 bg-white shadow-sm"
                                            />
                                            <button
                                                type="button"
                                                @click="
                                                    field.options.splice(
                                                        oIdx,
                                                        1,
                                                    )
                                                "
                                                x-show="
                                                    field.options &&
                                                    field.options.length > 1
                                                "
                                                class="text-red-500 hover:text-white bg-white hover:bg-red-500 border border-red-200 hover:border-red-500 p-1.5 rounded-md transition-colors shrink-0 shadow-sm"
                                                title="Hapus Opsi"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                            </button>
                                        </div>
                                    </template>
                                </div>

                                <button
                                    type="button"
                                    @click="
                                        field.options = field.options || [];
                                        field.options.push('');
                                    "
                                    class="text-[10px] font-extrabold tracking-wider text-blue-700 bg-white border border-blue-200 hover:bg-blue-50 px-3 py-2 rounded-md transition-colors mt-2 flex items-center gap-1.5 w-max shadow-sm"
                                >
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                    Tambah Opsi
                                </button>
                            </div>
                        </div>

                        <!-- Jika File Upload -->
                        <div
                            x-show="!isWawancaraMode && field.tipe === 'file'"
                            class=""
                            x-transition
                        >
                            <div
                                class="p-4 bg-slate-50 rounded-lg border border-slate-200 mt-1 w-full overflow-hidden"
                            >
                                <p class="text-[10px] font-extrabold text-slate-500 tracking-widest mb-3 flex items-center gap-2">
                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                    Konfigurasi Format Jawaban:
                                </p>
                                <div class="flex flex-wrap gap-2.5">
                                    <template
                                        x-for="
                                            ext in
                                            [
                                                'pdf',
                                                'jpg',
                                                'png',
                                                'word',
                                                'excel',
                                                'zip',
                                            ]
                                        "
                                        :key="ext"
                                    >
                                        <label
                                            class="flex items-center text-[10px] font-bold bg-white border border-slate-300 px-3 py-1.5 rounded-md cursor-pointer hover:bg-blue-50 hover:border-blue-200 hover:text-blue-700 transition-colors select-none shadow-sm"
                                        >
                                            <input
                                                type="checkbox"
                                                :value="ext"
                                                x-model="field.allowed_formats"
                                                class="rounded border-slate-300 text-blue-600 focus:ring-blue-600 mr-2"
                                            />
                                            <span
                                                x-text="ext.toUpperCase()"
                                            ></span>
                                        </label>
                                    </template>
                                </div>
                                <p
                                    x-show="
                                        field.allowed_formats &&
                                        field.allowed_formats.length === 0
                                    "
                                    class="mt-3 text-[10px] font-bold text-red-600 bg-red-50 px-3 py-2 rounded-md w-max border border-red-100 flex items-center gap-1.5"
                                >
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                    Wajib mencentang minimal 1 format file.
                                </p>
                            </div>
                        </div>
                    </div>
                </template>

                <!-- State Kosong / Empty State -->
                <div
                    x-show="currentFormSchema.length === 0"
                    class="text-center py-16 px-6 bg-slate-50 rounded-xl border-2 border-dashed border-slate-300"
                >
                    <div
                        class="w-14 h-14 rounded-full bg-white border border-slate-200 flex items-center justify-center mx-auto mb-4 shadow-sm"
                    >
                        <svg x-show="isWawancaraMode" class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path></svg>
                        <svg x-show="!isWawancaraMode" class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002-2h2a2 2 0 002 2M9 12h6m-6 4h6m-8-4h.01M9 16h.01"></path></svg>
                    </div>
                    <h4
                        class="font-extrabold text-slate-700 mb-1.5 text-lg"
                        x-text="
                            isWawancaraMode
                                ? 'Lembar Evaluasi Kosong'
                                : 'Formulir Kosong'
                        "
                    ></h4>
                    <p class="text-xs font-medium text-slate-500 max-w-sm mx-auto leading-relaxed">Menambahkan pertanyaan silahkan klik tombol "Tambah Pertanyaan" di bawah.</p>
                </div>
            </div>

            <!-- FOOTER MODAL & ACTIONS -->
            <div
                class="flex flex-col sm:flex-row justify-between items-center p-6 sm:px-8 sm:py-5 border-t border-slate-100 bg-slate-50/50 shrink-0 gap-4 rounded-b-xl"
            >
                <button
                    type="button"
                    @click="
                        currentFormSchema.push({
                            tipe: isWawancaraMode ? 'text_long' : 'text_short',
                            label: '',
                            keterangan: '',
                            required: true,
                            options: [''],
                            allowed_formats: [],
                        });
                        setTimeout(() => {
                            const container =
                                document.querySelector('.custom-scrollbar');
                            container.scrollTop = container.scrollHeight;
                        }, 100);
                    "
                    class="w-full sm:w-auto bg-white text-blue-700 hover:bg-blue-50 border border-blue-200 hover:border-blue-300 px-5 py-2.5 rounded-lg font-extrabold text-xs tracking-wider transition-all shadow-sm flex items-center justify-center gap-2"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                    Tambah Pertanyaan
                </button>

                <div class="flex gap-3 w-full sm:w-auto">
                    <button
                        type="button"
                        @click="openFormBuilder = false"
                        class="flex-1 sm:flex-none bg-white hover:bg-slate-50 text-slate-700 border border-slate-300 px-6 py-2.5 rounded-lg font-bold text-sm transition-colors text-center shadow-sm"
                    >
                        Batalkan
                    </button>
                    <button
                        type="button"
                        @click="simpanSkemaKeTugas()"
                        class="flex-1 sm:flex-none bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-lg font-bold text-sm transition-all shadow-sm hover:shadow flex items-center justify-center gap-2"
                    >
                        Terapkan Form
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<!-- TOAST NOTIFICATION: Terapkan Form Berhasil -->
<div
    x-show="showFormSuccess"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 transform translate-y-4"
    x-transition:enter-end="opacity-100 transform translate-y-0"
    x-transition:leave="transition ease-in duration-300"
    x-transition:leave-start="opacity-100 transform translate-y-0"
    x-transition:leave-end="opacity-0 transform translate-y-4"
    class="fixed bottom-8 right-8 z-[9999] bg-emerald-600 text-white px-5 py-3.5 rounded-lg shadow-lg flex items-center gap-3 border border-emerald-700"
    style="display: none"
>
    <div class="bg-white rounded-md p-1 shadow-sm shrink-0">
        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
    </div>
    <div class="flex flex-col">
        <span class="text-sm font-extrabold text-white leading-tight"
            >Formulir Diterapkan!</span
        >
        <span class="text-[11px] text-emerald-100 font-medium mt-0.5"
            >Klik tombol "Simpan & Lanjutkan" di bagian bawah halaman untuk
            memfinalisasi.</span
        >
    </div>
</div>
