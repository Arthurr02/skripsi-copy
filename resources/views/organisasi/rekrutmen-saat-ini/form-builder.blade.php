<div
    x-show="openFormBuilder"
    class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center bg-black bg-opacity-50 p-4"
    x-transition
    style="display: none"
>
    <div
        class="bg-white rounded-lg max-w-3xl w-full p-6 shadow-xl space-y-4 max-h-[85vh] overflow-y-auto"
    >
        <div class="flex justify-between items-center border-b pb-3">
            <h3 class="text-lg font-bold text-gray-800">
                🛠️ Form Builder Instan — Jabatan:
                <span class="text-blue-600" x-text="activeJabatanName"></span>
            </h3>
            <button
                type="button"
                @click="openFormBuilder = false"
                class="text-gray-400 hover:text-gray-600 text-2xl leading-none"
            >
                &times;
            </button>
        </div>

        <div class="space-y-4">
            <template x-for="(field, fIdx) in currentFormSchema" :key="fIdx">
                <div
                    class="p-4 bg-gray-50 rounded-lg border border-gray-200 space-y-3 relative shadow-sm"
                >
                    <div class="flex gap-3 items-start">
                        <span
                            class="text-xs font-black text-gray-400 mt-2.5 bg-gray-200 px-2 py-1 rounded-full"
                            x-text="'#' + (fIdx + 1)"
                        ></span>
                        <div class="flex-1">
                            <input
                                type="text"
                                x-model="field.label"
                                placeholder="Tuliskan Pertanyaan Form..."
                                class="w-full text-sm font-semibold rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm"
                            />
                        </div>
                        <button
                            type="button"
                            @click="currentFormSchema.splice(fIdx, 1)"
                            class="text-red-500 hover:text-red-700 hover:bg-red-50 font-bold text-xs px-3 py-2 rounded transition-colors"
                        >
                            Hapus
                        </button>
                    </div>

                    <div class="flex flex-wrap gap-4 items-center pl-10">
                        <select
                            x-model="field.tipe"
                            @change="
                                field.options = field.options || ['Opsi 1'];
                                field.allowed_formats =
                                    field.allowed_formats || [];
                            "
                            class="text-xs font-medium rounded border-gray-300 py-1.5 focus:border-blue-500 focus:ring-blue-500 shadow-sm"
                        >
                            <optgroup label="Teks & Angka">
                                <option value="text_short">Teks Pendek</option>
                                <option value="text_long">Teks Paragraf</option>
                                <option value="email">Alamat Email</option>
                                <option value="number">
                                    Angka (Nomor/NIM)
                                </option>
                            </optgroup>
                            <optgroup label="Pilihan Bersarang">
                                <option value="select">
                                    Dropdown (Opsi Pilih)
                                </option>
                                <option value="radio">
                                    Pilihan Ganda (Radio)
                                </option>
                            </optgroup>
                            <optgroup label="Berkas">
                                <option value="file">
                                    Upload File / Dokumen
                                </option>
                            </optgroup>
                        </select>
                        <label
                            class="flex items-center text-xs font-semibold text-gray-700 cursor-pointer hover:bg-gray-100 px-2 py-1 rounded"
                        >
                            <input
                                type="checkbox"
                                x-model="field.required"
                                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 mr-2 h-4 w-4"
                            />
                            Wajib Diisi Pendaftar
                        </label>
                    </div>

                    <div
                        x-show="
                            field.tipe === 'select' || field.tipe === 'radio'
                        "
                        class="pl-10 pt-3 border-t border-gray-200 mt-3 space-y-2"
                        x-transition
                    >
                        <p class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">Konfigurasi Opsi Pilihan:</p>
                        <template
                            x-for="(opt, oIdx) in field.options"
                            :key="oIdx"
                        >
                            <div class="flex gap-2 items-center mb-1.5">
                                <span class="text-[10px] text-gray-400"
                                    >⚪</span
                                >
                                <input
                                    type="text"
                                    x-model="field.options[oIdx]"
                                    placeholder="Tulis opsi..."
                                    class="text-xs rounded border-gray-300 py-1.5 flex-1 focus:border-blue-500 focus:ring-blue-500"
                                />
                                <button
                                    type="button"
                                    @click="field.options.splice(oIdx, 1)"
                                    x-show="
                                        field.options &&
                                        field.options.length > 1
                                    "
                                    class="text-red-400 hover:text-red-600 bg-red-50 hover:bg-red-100 p-1.5 rounded text-[10px] font-bold transition-colors"
                                >
                                    X
                                </button>
                            </div>
                        </template>
                        <button
                            type="button"
                            @click="
                                field.options = field.options || [];
                                field.options.push('Opsi Baru');
                            "
                            class="text-[11px] text-blue-600 bg-blue-50 hover:bg-blue-100 px-2 py-1 rounded font-semibold transition-colors mt-1"
                        >
                            + Tambah Pilihan Opsi
                        </button>
                    </div>

                    <div
                        x-show="field.tipe === 'file'"
                        class="pl-10 pt-3 border-t border-gray-200 mt-3"
                        x-transition
                    >
                        <p class="text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-2">Format File yang Diizinkan:</p>
                        <div class="flex flex-wrap gap-2.5">
                            <template
                                x-for="
                                    ext in
                                    ['pdf', 'jpg', 'word', 'excel', 'zip']
                                "
                                :key="ext"
                            >
                                <label
                                    class="flex items-center text-[11px] bg-white border border-gray-200 px-3 py-1.5 rounded-md cursor-pointer hover:bg-blue-50 hover:border-blue-200 transition-colors shadow-sm"
                                >
                                    <input
                                        type="checkbox"
                                        :value="ext"
                                        x-model="field.allowed_formats"
                                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 mr-2"
                                    />
                                    <span
                                        x-text="ext.toUpperCase()"
                                        class="font-medium text-gray-700"
                                    ></span>
                                </label>
                            </template>
                        </div>
                        <p x-show="
                                field.allowed_formats &&
                                field.allowed_formats.length === 0
                            " class="mt-2 text-[10px] font-bold text-red-500">⚠️ Wajib mencentang minimal 1 format file agar pendaftar bisa mengunggah dokumen.</p>
                    </div>
                </div>
            </template>

            <div
                x-show="currentFormSchema.length === 0"
                class="text-center py-10 px-4 text-sm text-gray-500 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300"
            >
                <span class="text-3xl block mb-2">📝</span>
                <p class="font-semibold text-gray-700">Form Pendaftaran Masih Kosong</p>
                <p class="text-xs mt-1">Silakan klik "Tambah Pertanyaan" di bawah untuk mulai merancang form khusus divisi ini.</p>
            </div>
        </div>

        <div
            class="flex justify-between items-center pt-5 border-t text-sm bg-white sticky bottom-0"
        >
            <button
                type="button"
                @click="
                    currentFormSchema.push({
                        tipe: 'text_short',
                        label: '',
                        required: true,
                        options: ['Opsi 1'],
                        allowed_formats: [],
                    })
                "
                class="bg-blue-50 text-blue-700 hover:bg-blue-100 border border-blue-200 px-4 py-2.5 rounded-md font-bold transition-colors flex items-center gap-1"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                Tambah Pertanyaan
            </button>
            <div class="flex gap-3">
                <button
                    type="button"
                    @click="openFormBuilder = false"
                    class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2.5 rounded-md font-semibold transition-colors"
                >
                    Batalkan
                </button>
                <button
                    type="button"
                    @click="simpanSkemaKeTugas()"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-md font-bold shadow-sm transition-colors flex items-center gap-2"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Terapkan Form
                </button>
            </div>
        </div>
    </div>
</div>
