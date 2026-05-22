<div x-show="tab === 2" x-transition.opacity class="space-y-6">
    <div
        class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm space-y-6"
    >
        <div
            class="flex items-center justify-between border-b border-gray-100 pb-2"
        >
            <h3 class="text-lg font-bold text-gray-800">
                3. Informasi Tahapan Seleksi & Distribusi Tugas
            </h3>
            <button
                type="button"
                @click="tambahTahapan()"
                class="text-sm font-semibold text-blue-600 hover:text-blue-800"
            >
                + Tambah Tahapan Baru
            </button>
        </div>

        <div class="space-y-6">
            <template x-for="(tahapan, tIndex) in listTahapan" :key="tIndex">
                <div
                    class="p-5 bg-gray-50 rounded-lg border border-gray-200 relative"
                >
                    <div
                        class="flex justify-between items-center mb-4 pb-2 border-b border-gray-200"
                    >
                        <span
                            class="text-sm font-bold text-blue-600 uppercase tracking-wider"
                        >
                            Tahapan Seleksi #<span x-text="tIndex + 1"></span>
                        </span>
                        <button
                            type="button"
                            @click="hapusTahapan(tIndex)"
                            x-show="listTahapan.length > 1"
                            class="text-xs text-red-500 hover:text-red-700 font-semibold"
                        >
                            Hapus Tahapan ini
                        </button>
                    </div>

                    <div class="grid grid-cols-1 gap-4 mb-4">
                        <div>
                            <label
                                class="block text-xs font-semibold text-gray-600 mb-1"
                                >Nama Tahapan</label
                            >
                            <input
                                type="text"
                                :name="`tahapan[${tIndex}][nama_tahapan]`"
                                x-model="tahapan.nama_tahapan"
                                @blur="
                                    validateField(
                                        'nama_tahapan_' + tIndex,
                                        tahapan.nama_tahapan,
                                        'Nama tahapan',
                                    )
                                "
                                :class="errors['nama_tahapan_' + tIndex]
                                    ? 'border-red-500 focus:border-red-500 focus:ring-red-500'
                                    : 'border-gray-300 focus:border-blue-500 focus:ring-blue-500'"
                                class="w-full rounded-md shadow-sm text-sm"
                                placeholder="Contoh: Seleksi Berkas / Ujian Tulis"
                                required
                            />
                            <p
                                x-show="errors['nama_tahapan_' + tIndex]"
                                x-text="errors['nama_tahapan_' + tIndex]"
                                class="mt-1 text-[11px] text-red-600 font-medium"
                            ></p>
                        </div>

                        <div class="md:col-span-2">
                            <label
                                class="block text-xs font-semibold text-gray-600 mb-1"
                                >Deskripsi Ringkas Tahapan</label
                            >
                            <input
                                type="text"
                                :name="`tahapan[${tIndex}][deskripsi]`"
                                x-model="tahapan.deskripsi"
                                @blur="
                                    validateField(
                                        'deskripsi_tahapan_' + tIndex,
                                        tahapan.deskripsi,
                                        'Deskripsi tahapan',
                                    )
                                "
                                :class="errors['deskripsi_tahapan_' + tIndex]
                                    ? 'border-red-500 focus:border-red-500 focus:ring-red-500'
                                    : 'border-gray-300 focus:border-blue-500 focus:ring-blue-500'"
                                class="w-full rounded-md shadow-sm text-sm"
                                placeholder="Berikan instruksi dasar mengenai tahapan seleksi ini"
                                required
                            />
                            <p
                                x-show="errors['deskripsi_tahapan_' + tIndex]"
                                x-text="errors['deskripsi_tahapan_' + tIndex]"
                                class="mt-1 text-[11px] text-red-600 font-medium"
                            ></p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label
                                class="block text-xs font-semibold text-gray-600 mb-1.5 tracking-wide"
                                >Waktu Mulai Tahapan</label
                            >
                            <div class="relative rounded-md shadow-sm">
                                <div
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-gray-400"
                                >
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                </div>
                                <input
                                    type="datetime-local"
                                    :name="`tahapan[${tIndex}][tanggal_mulai]`"
                                    x-model="tahapan.tanggal_mulai"
                                    @blur="
                                        validateField(
                                            'tanggal_mulai_' + tIndex,
                                            tahapan.tanggal_mulai,
                                            'Waktu mulai',
                                        )
                                    "
                                    :class="errors['tanggal_mulai_' + tIndex]
                                        ? 'border-red-500 focus:border-red-500 focus:ring-red-500 bg-red-50/30'
                                        : 'border-gray-300 focus:border-blue-500 focus:ring-blue-500 bg-white'"
                                    class="block w-full pr-10 rounded-md text-sm transition-colors duration-200 ease-in-out py-2.5 shadow-sm font-medium text-gray-700"
                                    required
                                />
                            </div>
                            <p
                                x-show="errors['tanggal_mulai_' + tIndex]"
                                x-text="errors['tanggal_mulai_' + tIndex]"
                                class="mt-1.5 text-[11px] text-red-600 font-medium"
                            ></p>
                        </div>
                        <div>
                            <label
                                class="block text-xs font-semibold text-gray-600 mb-1.5 tracking-wide"
                                >Waktu Berakhir Tahapan</label
                            >
                            <div class="relative rounded-md shadow-sm">
                                <div
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-gray-400"
                                >
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                                <input
                                    type="datetime-local"
                                    :name="`tahapan[${tIndex}][tanggal_selesai]`"
                                    x-model="tahapan.tanggal_selesai"
                                    @blur="
                                        validateField(
                                            'tanggal_selesai_' + tIndex,
                                            tahapan.tanggal_selesai,
                                            'Waktu berakhir',
                                        )
                                    "
                                    :class="errors['tanggal_selesai_' + tIndex]
                                        ? 'border-red-500 focus:border-red-500 focus:ring-red-500 bg-red-50/30'
                                        : 'border-gray-300 focus:border-blue-500 focus:ring-blue-500 bg-white'"
                                    class="block w-full pr-10 rounded-md text-sm transition-colors duration-200 ease-in-out py-2.5 shadow-sm font-medium text-gray-700"
                                    required
                                />
                            </div>
                            <p
                                x-show="errors['tanggal_selesai_' + tIndex]"
                                x-text="errors['tanggal_selesai_' + tIndex]"
                                class="mt-1.5 text-[11px] text-red-600 font-medium"
                            ></p>
                        </div>
                    </div>

                    <div class="bg-white p-4 rounded-md border border-gray-200">
                        <div class="flex items-center justify-between mb-3">
                            <h4
                                class="text-xs font-bold text-gray-700 uppercase"
                            >
                                Distribusi Beban Tugas Per Jabatan
                            </h4>
                            <button
                                type="button"
                                @click="syncTugasKeJabatan(tIndex)"
                                class="text-[11px] bg-blue-50 text-blue-600 hover:bg-blue-100 px-2 py-1 rounded font-bold border border-blue-200"
                            >
                                🔄 Sinkronisasi Jabatan Terbaru
                            </button>
                        </div>
                        <p class="text-[11px] text-gray-400 mb-3">*Tekan tombol sinkronisasi di atas jika Anda baru saja mengubah daftar Jabatan di Tab 1.</p>

                        <div
                            class="mb-4 p-3 bg-gray-50 rounded border border-gray-200"
                        >
                            <label
                                class="block text-xs font-bold text-gray-700 uppercase mb-2"
                                >Metode Distribusi Tugas:</label
                            >
                            <div class="flex items-center gap-6">
                                <label
                                    class="flex items-center text-sm cursor-pointer"
                                >
                                    <input
                                        type="radio"
                                        x-model="tahapan.metodeDistribusi"
                                        value="sama"
                                        class="text-blue-600 focus:ring-blue-500"
                                    />
                                    <span
                                        class="ml-2 text-xs font-medium text-gray-700"
                                        >Tugas Seragam untuk Semua Jabatan</span
                                    >
                                </label>
                                <label
                                    class="flex items-center text-sm cursor-pointer"
                                >
                                    <input
                                        type="radio"
                                        x-model="tahapan.metodeDistribusi"
                                        value="beda"
                                        class="text-blue-600 focus:ring-blue-500"
                                    />
                                    <span
                                        class="ml-2 text-xs font-medium text-gray-700"
                                        >Spesifik per Jabatan</span
                                    >
                                </label>
                            </div>
                        </div>

                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="bg-gray-50 border-b border-gray-200">
                                    <th
                                        class="px-3 py-2 font-bold text-gray-600 w-1/6"
                                    >
                                        Jabatan
                                    </th>
                                    <th
                                        class="px-3 py-2 font-bold text-gray-600 w-1/3"
                                    >
                                        Deskripsi Instruksi Tugas
                                    </th>
                                    <th
                                        class="px-3 py-2 font-bold text-gray-600 w-1/6"
                                    >
                                        Tipe Jawaban
                                    </th>
                                    <th
                                        class="px-3 py-2 font-bold text-gray-600 w-1/3"
                                    >
                                        Lampiran Berkasan (PDF/Word)
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <template
                                    x-for="
                                        (tugas, jIndex) in tahapan.tugasJabatan
                                    "
                                    :key="jIndex"
                                >
                                    <tr
                                        x-show="
                                            tahapan.metodeDistribusi ===
                                                'beda' || jIndex === 0
                                        "
                                        class="border-b border-gray-100"
                                    >
                                        <td
                                            class="px-3 py-2 font-medium text-gray-700"
                                        >
                                            <span
                                                x-text="
                                                    tahapan.metodeDistribusi ===
                                                    'sama'
                                                        ? 'Semua Jabatan'
                                                        : tugas.nama_jabatan ||
                                                          'Belum diisi di Tab 1'
                                                "
                                            ></span>
                                            <input
                                                type="hidden"
                                                :name="`tahapan[${tIndex}][tugas][${jIndex}][nama_jabatan]`"
                                                x-model="tugas.nama_jabatan"
                                                :disabled="tahapan.metodeDistribusi ===
                                                    'sama' && jIndex !== 0"
                                            />
                                        </td>

                                        <td class="px-2 py-2">
                                            <input
                                                type="text"
                                                :name="`tahapan[${tIndex}][tugas][${jIndex}][deskripsi_tugas]`"
                                                x-model="tugas.deskripsi_tugas"
                                                @blur="
                                                    validateField(
                                                        'deskripsi_tugas_' +
                                                            tIndex +
                                                            '_' +
                                                            jIndex,
                                                        tugas.deskripsi_tugas,
                                                        'Instruksi tugas',
                                                    )
                                                "
                                                :class="errors[
                                                    'deskripsi_tugas_' +
                                                        tIndex +
                                                        '_' +
                                                        jIndex
                                                ]
                                                    ? 'border-red-500 focus:border-red-500 focus:ring-red-500'
                                                    : 'border-gray-200 focus:border-blue-500 focus:ring-blue-500'"
                                                class="w-full rounded text-xs"
                                                placeholder="Misal: Buatlah essay 300 kata..."
                                                :required="tahapan.metodeDistribusi ===
                                                    'beda' || jIndex === 0"
                                                :disabled="tahapan.metodeDistribusi ===
                                                    'sama' && jIndex !== 0"
                                            />
                                            <p x-show="errors['deskripsi_tugas_' + tIndex + '_' + jIndex]" x-text="errors['deskripsi_tugas_' + tIndex + '_' + jIndex]" class="mt-0.5 text-[10px] text-red-600 font-medium"></p>
                                        </td>

                                        <td class="px-2 py-2">
                                            <select
                                                :name="`tahapan[${tIndex}][tugas][${jIndex}][tipe_jawaban_tugas]`"
                                                x-model="
                                                    tugas.tipe_jawaban_tugas
                                                "
                                                class="w-full border-gray-200 focus:border-blue-500 focus:ring-blue-500 rounded text-xs"
                                                :disabled="tahapan.metodeDistribusi ===
                                                    'sama' && jIndex !== 0"
                                            >
                                                <option value="file">
                                                    Upload File (PDF/Image)
                                                </option>
                                                <option value="link">
                                                    Tautan / Link URL
                                                </option>
                                                <option value="text">
                                                    Input Teks Langsung (Form
                                                    Builder)
                                                </option>
                                            </select>

                                            <input
                                                type="hidden"
                                                :name="`tahapan[${tIndex}][tugas][${jIndex}][skema_form_json]`"
                                                :value="typeof tugas.lampiran_tugas ===
                                                'object'
                                                    ? JSON.stringify(
                                                          tugas.lampiran_tugas,
                                                      )
                                                    : tugas.lampiran_tugas"
                                            />

                                            <div
                                                x-show="
                                                    tugas.tipe_jawaban_tugas ===
                                                    'text'
                                                "
                                                class="mt-1"
                                            >
                                                <button
                                                    type="button"
                                                    @click="
                                                        bukaBuilder(
                                                            tIndex,
                                                            jIndex,
                                                            tahapan.metodeDistribusi ===
                                                                'sama'
                                                                ? 'Semua Jabatan'
                                                                : tugas.nama_jabatan,
                                                        )
                                                    "
                                                    class="w-full text-center bg-amber-50 text-amber-700 hover:bg-amber-100 border border-amber-200 rounded py-1 text-[11px] font-bold transition"
                                                >
                                                    ⚙️
                                                    <span
                                                        x-text="
                                                            tugas.lampiran_tugas &&
                                                            tugas.lampiran_tugas
                                                                .length > 0
                                                                ? 'Edit Desain Form (' +
                                                                  tugas
                                                                      .lampiran_tugas
                                                                      .length +
                                                                  ' Field)'
                                                                : 'Buat Struktur Form'
                                                        "
                                                    ></span>
                                                </button>
                                            </div>
                                        </td>

                                        <td class="px-2 py-2">
                                            <input
                                                type="file"
                                                :name="`tahapan[${tIndex}][tugas][${jIndex}][lampiran][]`"
                                                multiple
                                                accept=".pdf,.doc,.docx"
                                                @change="
                                                    validateFile(
                                                        'lampiran_tugas_' +
                                                            tIndex +
                                                            '_' +
                                                            jIndex,
                                                        $el.files,
                                                        2,
                                                        'Lampiran tugas',
                                                    )
                                                "
                                                :class="errors[
                                                    'lampiran_tugas_' +
                                                        tIndex +
                                                        '_' +
                                                        jIndex
                                                ]
                                                    ? 'border-red-500 focus:border-red-500 focus:ring-red-500'
                                                    : 'border-gray-300'"
                                                class="block w-full text-xs text-gray-500 file:mr-2 file:py-1 file:px-2 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 border rounded"
                                                :disabled="tahapan.metodeDistribusi ===
                                                    'sama' && jIndex !== 0"
                                            />
                                            <p class="mt-0.5 text-[10px] text-gray-400">*Format: PDF/Word, Maksimal 2 MB</p>

                                            <div
                                                x-show="
                                                    tugas.lampiran_tugas &&
                                                    tugas.lampiran_tugas
                                                        .length > 0
                                                "
                                                class="mt-1.5 flex flex-wrap gap-1.5"
                                            >
                                                <template
                                                    x-for="
                                                        (file, fIndex) in
                                                        tugas.lampiran_tugas
                                                    "
                                                    :key="fIndex"
                                                >
                                                    <a
                                                        :href="'/storage/' +
                                                        file"
                                                        target="_blank"
                                                        class="inline-flex items-center gap-1 bg-green-50 text-green-700 border border-green-200 rounded px-1.5 py-0.5 text-[10px] font-semibold hover:bg-green-100 transition-colors"
                                                    >
                                                        Buka
                                                        <span
                                                            x-text="fIndex + 1"
                                                        ></span>
                                                    </a>
                                                </template>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <div class="flex justify-between items-center pt-2">
        <button
            type="button"
            @click="tab = 1"
            class="text-sm font-semibold text-gray-600 hover:text-gray-900 flex items-center gap-1"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            Kembali ke Tab 1
        </button>
        <button
            type="submit"
            class="bg-blue-600 text-white px-8 py-3 rounded-md font-bold hover:bg-blue-700 transition-colors shadow-sm"
        >
            Simpan & Publikasikan Informasi Rekrutmen
        </button>
    </div>
</div>
