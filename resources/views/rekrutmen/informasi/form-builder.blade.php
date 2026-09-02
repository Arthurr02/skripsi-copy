<!-- BUNGKUS DENGAN TEMPLATE INI -->
<template x-teleport="body">
    <div
        x-show="openFormBuilder"
        class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center bg-slate-100/60 backdrop-blur-sm p-4 sm:p-6"
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
            class="bg-white rounded-lg max-w-3xl w-full p-6 sm:p-8 shadow-sm border border-slate-200 flex flex-col max-h-[90vh]"
        >
            <!-- Seluruh isi modal Anda yang sudah kita desain tadi -->
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
                    class="bg-white rounded-lg max-w-3xl w-full p-6 sm:p-8 shadow-sm border border-slate-200 flex flex-col max-h-[90vh]"
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
                        class="flex justify-between items-center border-b border-slate-200 pb-4 mb-6 shrink-0"
                    >
                        <h3
                            class="text-xl font-extrabold text-slate-800 flex items-center gap-2.5"
                        >
                            <span
                                x-text="
                                    isWawancaraMode
                                        ? '🎙️ Evaluasi Wawancara'
                                        : '📝 Form Pendaftaran'
                                "
                            ></span>
                            <span class="text-slate-300 text-lg font-normal"
                                >|</span
                            >
                            <span
                                class="text-blue-600 text-sm font-bold bg-blue-50 px-3 py-1 rounded-md border border-blue-100"
                                x-text="activeJabatanName"
                            ></span>
                        </h3>
                        <button
                            type="button"
                            @click="openFormBuilder = false"
                            class="text-slate-400 hover:text-slate-600 bg-slate-50 hover:bg-slate-100 w-8 h-8 rounded-md flex items-center justify-center transition-colors"
                            title="Tutup Form Builder"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <!-- ISI BUILDER (SCROLLABLE) -->
                    <div
                        class="overflow-y-auto pr-2 space-y-5 flex-1 min-h-[40vh] custom-scrollbar"
                    >
                        <template
                            x-for="(field, fIdx) in currentFormSchema"
                            :key="fIdx"
                        >
                            <!-- FIELD CARD -->
                            <div
                                class="p-5 bg-slate-50 rounded-lg border border-slate-200 space-y-4 relative group hover:border-blue-300 transition-colors"
                            >
                                <!-- Top Bar: Nomor & Tombol Hapus -->
                                <div
                                    class="flex justify-between items-start gap-3"
                                >
                                    <div class="flex-1 flex gap-3 items-start">
                                        <!-- Badge Nomor -->
                                        <span
                                            class="shrink-0 flex items-center justify-center w-7 h-7 bg-slate-200 text-slate-600 text-xs font-black rounded-md mt-1"
                                            x-text="fIdx + 1"
                                        ></span>

                                        <!-- Input Pertanyaan & Keterangan -->
                                        <div class="flex-1 space-y-2.5 w-full">
                                            <input
                                                type="text"
                                                x-model="field.label"
                                                placeholder="Tuliskan Pertanyaan / Aspek Penilaian..."
                                                class="w-full text-sm font-bold text-slate-800 rounded-md border-slate-300 focus:border-blue-600 focus:ring-0 bg-white py-2.5 transition-colors"
                                            />

                                            <div
                                                x-show="!isWawancaraMode"
                                                class="flex items-center gap-2"
                                            >
                                                <span class="text-sm shrink-0"
                                                    >💡</span
                                                >
                                                <input
                                                    type="text"
                                                    x-model="field.keterangan"
                                                    placeholder="Penjelasan/keterangan pertanyaan (opsional)"
                                                    class="w-full text-[11px] font-medium text-slate-600 rounded-md border-slate-200 bg-white focus:border-blue-400 focus:ring-0 py-2 transition-colors"
                                                />
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Tombol Hapus -->
                                    <button
                                        type="button"
                                        @click="
                                            currentFormSchema.splice(fIdx, 1)
                                        "
                                        class="text-red-500 hover:text-white bg-red-50 hover:bg-red-600 font-bold text-[10px] uppercase tracking-wider px-3 py-2 rounded-md transition-colors shrink-0 mt-1"
                                    >
                                        Hapus
                                    </button>
                                </div>

                                <!-- Pilihan Tipe Input & Wajib -->
                                <div
                                    class="flex flex-wrap gap-4 items-center pl-10 pt-2 border-t border-slate-200/60 mt-3"
                                >
                                    <div
                                        class="flex items-center gap-2 w-full sm:w-max"
                                    >
                                        <span
                                            class="text-[10px] font-bold text-slate-400 uppercase tracking-widest"
                                            >Tipe Jawaban:</span
                                        >
                                        <select
                                            x-model="field.tipe"
                                            @change="
                                                field.options =
                                                    field.options || ['Opsi 1'];
                                                field.allowed_formats =
                                                    field.allowed_formats || [];
                                            "
                                            class="text-xs font-bold text-slate-700 rounded-md border-slate-300 py-2 pl-3 pr-8 focus:border-blue-600 focus:ring-0 bg-white transition-colors flex-1 sm:flex-none"
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
                                                <option value="email">
                                                    Alamat Email
                                                </option>
                                                <option value="number">
                                                    Angka (Nomor/NIM)
                                                </option>
                                            </optgroup>
                                            <optgroup
                                                label="Pilihan Bersarang"
                                                x-show="!isWawancaraMode"
                                            >
                                                <option value="select">
                                                    Dropdown (Opsi Pilih)
                                                </option>
                                                <option value="radio">
                                                    Pilihan Ganda (Radio)
                                                </option>
                                                <option value="checkbox">
                                                    Pilihan Jamak (Checkbox)
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
                                                    Teks Paragraf (Catatan
                                                    Evaluasi)
                                                </option>
                                            </optgroup>
                                        </select>
                                    </div>

                                    <label
                                        class="flex items-center text-xs font-bold text-slate-700 cursor-pointer hover:bg-slate-100 px-3 py-2 rounded-md transition-colors border border-transparent hover:border-slate-200"
                                    >
                                        <input
                                            type="checkbox"
                                            x-model="field.required"
                                            class="rounded border-slate-300 text-blue-600 focus:ring-blue-600 mr-2 h-4 w-4"
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
                                    class="pl-10 pt-4 border-t border-slate-200 mt-2 space-y-3"
                                    x-transition
                                >
                                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Konfigurasi Opsi Pilihan:</p>

                                    <div class="space-y-2">
                                        <template
                                            x-for="(opt, oIdx) in field.options"
                                            :key="oIdx"
                                        >
                                            <div
                                                class="flex gap-2 items-center"
                                            >
                                                <div
                                                    class="w-4 h-4 rounded-full bg-slate-200 border-2 border-white shadow-sm flex items-center justify-center shrink-0"
                                                ></div>
                                                <input
                                                    type="text"
                                                    x-model="
                                                        field.options[oIdx]
                                                    "
                                                    placeholder="Tulis opsi..."
                                                    class="text-xs font-medium text-slate-700 rounded-md border-slate-300 py-1.5 px-3 flex-1 focus:border-blue-600 focus:ring-0 bg-white"
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
                                                    class="text-red-500 hover:text-white bg-red-50 hover:bg-red-500 p-1.5 rounded-md transition-colors shrink-0"
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
                                        class="text-[10px] font-bold uppercase tracking-wider text-blue-700 bg-blue-100 hover:bg-blue-200 px-3 py-1.5 rounded-md transition-colors mt-2 flex items-center gap-1 w-max"
                                    >
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                        Tambah Pilihan Opsi
                                    </button>
                                </div>

                                <!-- Jika File Upload -->
                                <div
                                    x-show="
                                        !isWawancaraMode &&
                                        field.tipe === 'file'
                                    "
                                    class="pl-10 pt-4 border-t border-slate-200 mt-2"
                                    x-transition
                                >
                                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-3">Format Ekstensi File yang Diizinkan:</p>
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
                                                class="flex items-center text-[10px] font-bold bg-white border border-slate-300 px-3 py-1.5 rounded-md cursor-pointer hover:bg-slate-50 transition-colors select-none"
                                            >
                                                <input
                                                    type="checkbox"
                                                    :value="ext"
                                                    x-model="
                                                        field.allowed_formats
                                                    "
                                                    class="rounded border-slate-300 text-blue-600 focus:ring-blue-600 mr-2"
                                                />
                                                <span
                                                    x-text="ext.toUpperCase()"
                                                    class="text-slate-700"
                                                ></span>
                                            </label>
                                        </template>
                                    </div>
                                    <p
                                        x-show="
                                            field.allowed_formats &&
                                            field.allowed_formats.length === 0
                                        "
                                        class="mt-3 text-[10px] font-bold text-red-500 bg-red-50 px-2 py-1.5 rounded-md w-max border border-red-100"
                                    >⚠️ Wajib mencentang minimal 1 format file.</p>
                                </div>
                            </div>
                        </template>

                        <!-- State Kosong / Empty State -->
                        <div
                            x-show="currentFormSchema.length === 0"
                            class="text-center py-12 px-6 bg-slate-50 rounded-lg border border-dashed border-slate-300"
                        >
                            <div
                                class="w-12 h-12 rounded-full bg-slate-200 flex items-center justify-center mx-auto mb-3 text-xl shadow-sm"
                            >
                                <span
                                    x-text="isWawancaraMode ? '🎙️' : '📝'"
                                ></span>
                            </div>
                            <h4
                                class="font-bold text-slate-700 mb-1"
                                x-text="
                                    isWawancaraMode
                                        ? 'Lembar Evaluasi Kosong'
                                        : 'Formulir Kosong'
                                "
                            ></h4>
                            <p class="text-xs font-normal text-slate-500 max-w-sm mx-auto">Silakan klik tombol "Tambah Pertanyaan" di bawah untuk mulai merancang form khusus divisi ini.</p>
                        </div>
                    </div>

                    <!-- FOOTER MODAL & ACTIONS -->
                    <div
                        class="flex flex-col sm:flex-row justify-between items-center pt-5 mt-2 border-t border-slate-200 shrink-0 gap-4"
                    >
                        <button
                            type="button"
                            @click="
                                currentFormSchema.push({
                                    id: 'pertanyaan_' + Date.now(),
                                    name: 'isian_' + Date.now(),
                                    tipe: isWawancaraMode
                                        ? 'text_long'
                                        : 'text_short',
                                    label: '',
                                    keterangan: '',
                                    required: true,
                                    options: [''],
                                    allowed_formats: [],
                                });
                                setTimeout(() => {
                                    const container =
                                        document.querySelector(
                                            '.custom-scrollbar',
                                        );
                                    container.scrollTop =
                                        container.scrollHeight;
                                }, 100);
                            "
                            class="w-full sm:w-auto bg-blue-50 text-blue-700 hover:bg-blue-100 border border-blue-200 px-5 py-2.5 rounded-md font-bold text-xs uppercase tracking-wider transition-colors flex items-center justify-center gap-1.5"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                            Tambah Pertanyaan
                        </button>

                        <div class="flex gap-3 w-full sm:w-auto">
                            <button
                                type="button"
                                @click="openFormBuilder = false"
                                class="flex-1 sm:flex-none bg-slate-100 hover:bg-slate-200 text-slate-700 px-6 py-2.5 rounded-md font-bold text-sm transition-colors text-center"
                            >
                                Batalkan
                            </button>
                            <button
                                type="button"
                                @click="simpanSkemaKeTugas()"
                                class="flex-1 sm:flex-none bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-md font-bold text-sm transition-colors flex items-center justify-center gap-2"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Terapkan Form
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TOAST NOTIFICATION: Terapkan Form Berhasil -->
        </div>
    </div>
</template>

<div
    x-show="showFormSuccess"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 transform translate-y-4"
    x-transition:enter-end="opacity-100 transform translate-y-0"
    x-transition:leave="transition ease-in duration-300"
    x-transition:leave-start="opacity-100 transform translate-y-0"
    x-transition:leave-end="opacity-0 transform translate-y-4"
    class="fixed bottom-8 right-8 z-[9999] bg-emerald-600 text-white px-5 py-3.5 rounded-lg shadow-sm flex items-center gap-3 border border-emerald-700"
    style="display: none"
>
    <div class="bg-white rounded-md p-1">
        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
        </svg>
    </div>
    <div class="flex flex-col">
        <span class="text-sm font-extrabold text-white leading-tight"
            >Formulir Disimpan!</span
        >
        <span class="text-[11px] text-emerald-100 font-medium"
            >Klik tombol "Simpan Utama" di form tugas untuk memfinalisasi.</span
        >
    </div>
</div>
