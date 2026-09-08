<!-- LANGKAH 3: ALUR TAHAPAN SELEKSI -->
<div
    id="tahapan-seleksi"
    x-show="tab === 3"
    x-transition:enter="transition ease-out duration-300 transform"
    x-transition:enter-start="opacity-0 translate-y-4"
    class="space-y-8"
    style="display: none"
>
    <div class="bg-white rounded-xl border shadow-sm border-slate-200">
        <!-- Header Section -->
        <div
            class="p-8 md:p-10 border-b rounded-t-xl border-slate-100 bg-slate-50 flex flex-col md:flex-row md:items-center justify-between gap-4"
        >
            <div>
                <h3
                    class="text-xl font-extrabold text-slate-800 tracking-tight flex items-center gap-3"
                >
                    <div
                        class="w-10 h-10 bg-blue-100 text-blue-600 rounded-md flex items-center justify-center shrink-0"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                    </div>
                    Alur Tahapan Seleksi
                </h3>
                <p class="text-sm font-normal text-slate-500 mt-2">Tentukan alur tahapan beserta seleksinya.</p>
            </div>

            <button
                type="button"
                @click="tambahTahapan()"
                class="text-sm font-bold text-blue-700 bg-blue-50 hover:bg-blue-600 hover:text-white px-5 py-2.5 rounded-md border border-blue-200 hover:border-blue-600 transition-colors flex items-center justify-center gap-2 shrink-0"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                Tambah Tahapan
            </button>
        </div>

        <div class="flex flex-col px-5 sm:px-10 py-6 sm:py-10 space-y-8">
            <!-- Navigasi Tabs Antar Tahapan -->
            <div class="flex overflow-x-auto gap-3 pb-2 custom-scrollbar">
                <template x-for="(t, idx) in listTahapan" :key="idx">
                    <button
                        type="button"
                        @click="activeTahapanIndex = idx"
                        :class="activeTahapanIndex === idx
                            ? 'bg-slate-800 text-white shadow-md'
                            : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                        class="px-5 py-2.5 rounded-md text-sm font-bold transition-all whitespace-nowrap flex items-center gap-2"
                    >
                        Tahapan <span x-text="idx + 1"></span>
                        <span
                            x-show="
                                Object.keys(errors).some((k) =>
                                    k.includes('_' + idx),
                                )
                            "
                            class="w-2.5 h-2.5 rounded-full bg-red-500 animate-pulse shadow-[0_0_8px_rgba(239,68,68,0.6)]"
                        ></span>
                    </button>
                </template>
            </div>

            <!-- Kontainer Konten Tahapan Utama -->
            <div class="relative min-h-[400px]">
                <template
                    x-for="(tahapan, tIndex) in listTahapan"
                    :key="tIndex"
                >
                    <div
                        x-show="activeTahapanIndex === tIndex"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-y-2"
                        class="p-6 md:p-8 bg-white border border-slate-200 rounded-xl shadow-sm"
                    >
                        <input
                            type="hidden"
                            :name="`tahapan[${tIndex}][id]`"
                            x-model="tahapan.id"
                        />

                        <!-- Header Dalam Tahapan -->
                        <div
                            class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4 border-b border-slate-100 pb-4"
                        >
                            <span
                                class="text-lg font-extrabold text-slate-800 tracking-wide flex items-center gap-3"
                            >
                                <span
                                    class="w-1.5 h-6 bg-blue-600 rounded-full inline-block"
                                ></span>
                                Konfigurasi Tahapan<span
                                    class="-ml-1"
                                    x-text="tIndex + 1"
                                ></span>
                            </span>
                            <button
                                type="button"
                                @click="hapusTahapan(tIndex)"
                                x-show="listTahapan.length > 1 && tIndex > 0"
                                class="text-xs font-bold text-red-500 hover:text-red-700 bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-md transition-colors flex items-center gap-1.5"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                Hapus Tahapan
                            </button>
                        </div>

                        <!-- Jenis Tahapan -->
                        <div
                            class="mb-8 p-6 bg-slate-50 rounded-xl border border-slate-200"
                        >
                            <label
                                class="block text-sm font-bold text-slate-700 mb-1 tracking-wide"
                            >
                                Jenis Tahapan
                                <span class="text-red-500 ml-0.5">*</span>
                            </label>

                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                               <label
                                    class="flex items-start gap-3 rounded-lg border p-4 transition-colors"
                                    :class="tIndex === 0
                                        ? 'cursor-not-allowed border-slate-200 bg-slate-100 text-slate-400 opacity-60'
                                        : (tahapan.jenis_tahapan === 'pengumuman'
                                            ? 'cursor-pointer border-blue-500 ring-1 ring-blue-500 bg-blue-50/30'
                                            : 'cursor-pointer border-slate-200 bg-white hover:border-blue-300')"
                                >
                                    <input
                                        type="radio"
                                        :name="`tahapan[${tIndex}][jenis_tahapan]`"
                                        value="pengumuman"
                                        x-model="tahapan.jenis_tahapan"
                                        @change="aturJenisTahapan(tahapan)"
                                        :disabled="tIndex === 0"
                                        class="mt-0.5 h-4 w-4 border-slate-300 text-blue-600 focus:ring-blue-600"
                                        required
                                    />
                                    <span>
                                        <span
                                            class="block text-sm font-bold text-slate-800"
                                            >Tahapan Pengumuman /
                                            Pemberitahuan</span
                                        >
                                        <span
                                            class="mt-1 block text-xs font-normal text-slate-500"
                                            >Tahapan yang hanya berisikan
                                            informasi pengumaman / pemberitahuan
                                            untuk peserta.</span
                                        >
                                    </span>
                                </label>
                                <label
                                    class="flex cursor-pointer items-start gap-3 rounded-lg border p-4 transition-colors bg-white hover:border-blue-300"
                                    :class="tahapan.jenis_tahapan === 'seleksi'
                                        ? 'border-blue-500 ring-1 ring-blue-500 bg-blue-50/30'
                                        : 'border-slate-200'"
                                >
                                    <input
                                        type="radio"
                                        value="seleksi"
                                        :name="tIndex === 0
                                            ? null
                                            : `tahapan[${tIndex}][jenis_tahapan]`"
                                        x-model="tahapan.jenis_tahapan"
                                        @change="aturJenisTahapan(tahapan)"
                                        :disabled="tIndex === 0"
                                        class="mt-0.5 h-4 w-4 border-slate-300 text-blue-600 focus:ring-blue-600"
                                        required
                                    />
                                    <span>
                                        <span
                                            class="block text-sm font-bold text-slate-800"
                                            >Tahapan Pendaftaran / Seleksi</span
                                        >
                                        <span
                                            class="mt-1 block text-xs font-normal text-slate-500"
                                            >Tahapan yang berisikan pelaksanaan
                                            seleksi (dapat berupa pengisian
                                            form, pengumpulan tugas, ataupun
                                            wawancara).</span
                                        >
                                    </span>
                                </label>
                                <template x-if="tIndex === 0">
                                    <input
                                        type="hidden"
                                        name="tahapan[0][jenis_tahapan]"
                                        value="seleksi"
                                    />
                                </template>
                            </div>
                            <p
                                x-show="errors['jenis_tahapan_' + tIndex]"
                                x-text="errors['jenis_tahapan_' + tIndex]"
                                class="mt-2 text-xs font-bold text-red-600"
                                style="display: none"
                            ></p>
                        </div>

                        <!-- Detail Tahapan -->
                        <div
                            x-show="tahapan.jenis_tahapan"
                            style="display: none"
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0"
                        >
                            <div class="grid grid-cols-1 gap-6 mb-8">
                                <!-- Nama Tahapan -->
                                <div>
                                    <label
                                        class="block text-sm font-bold text-slate-700 mb-1 tracking-wide"
                                    >
                                        Nama Tahapan<span
                                            class="text-red-500 ml-0.5"
                                            >*</span
                                        >
                                    </label>
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
                                            ? 'border-red-500 bg-red-50 text-red-700 focus:ring-0 focus:border-red-500'
                                            : 'border-slate-300 focus:border-blue-600 focus:ring-0 bg-white text-slate-800'"
                                        class="w-full border rounded-md transition-colors text-sm font-bold py-2.5 px-3 sm:px-4"
                                        placeholder="Contoh: Pendaftaran Peserta"
                                        required
                                    />
                                    <p
                                        x-show="
                                            errors['nama_tahapan_' + tIndex]
                                        "
                                        x-text="
                                            errors['nama_tahapan_' + tIndex]
                                        "
                                        class="mt-2 text-xs text-red-500 font-bold style='display:none;'"
                                    ></p>
                                </div>

                                <!-- Deskripsi Instruksi -->
                                <div>
                                    <label
                                        class="block text-sm font-bold text-slate-700 mb-1 tracking-wide"
                                    >
                                       Deskripsi Tahapan<span
                                            class="ml-1 text-xs font-bold text-slate-400"
                                            >(Opsional)</span
                                       >
                                    </label>
                                    <textarea
                                       :name="`tahapan[${tIndex}][deskripsi]`"
                                       x-model="tahapan.deskripsi"
                                       rows="3"
                                       :class="errors[
                                            'deskripsi_tahapan_' + tIndex
                                        ]
                                            ? 'border-red-500 bg-red-50 text-red-700 focus:ring-0 focus:border-red-500'
                                            : 'border-slate-300 focus:border-blue-600 focus:ring-0 bg-white text-slate-800'"
                                        class="w-full border rounded-md transition-colors text-sm font-bold py-2.5 px-3 sm:px-4"
                                        placeholder="Contoh: Peserta diharapkan membuat CV dengan template yang telah disediakan."
                                    ></textarea>
                                    <p x-show="errors['deskripsi_tahapan_' + tIndex]" x-text="errors['deskripsi_tahapan_' + tIndex]" class="mt-2 text-xs text-red-500 font-bold style='display:none;'"></p>
                                </div>

                                <!-- Waktu Pengumuman -->
                                <div
                                    x-show="
                                        tahapan.jenis_tahapan === 'pengumuman'
                                    "
                                    class="mb-3"
                                >
                                    <label
                                        class="block text-sm font-bold text-slate-700 mb-1 tracking-wide"
                                    >
                                        Waktu Pengumuman<span
                                            class="text-red-500 ml-0.5"
                                            >*</span
                                        >
                                    </label>
                                    <input
                                        type="datetime-local"
                                        :name="`tahapan[${tIndex}][waktu_pengumuman]`"
                                        x-model="tahapan.waktu_pengumuman"
                                        @change="validateAllTimelines()"
                                        :required="tahapan.jenis_tahapan ===
                                        'pengumuman'"
                                        :class="errors[
                                            'waktu_pengumuman_' + tIndex
                                        ]
                                            ? 'border-red-500 bg-red-50'
                                            : 'border-slate-300 focus:border-blue-600 focus:ring-0 bg-white'"
                                        class="w-full border rounded-md transition-colors text-sm font-bold py-2.5 px-3 sm:px-4 text-slate-800"
                                    />
                                    <p x-show="errors['waktu_pengumuman_' + tIndex]" x-text="errors['waktu_pengumuman_' + tIndex]" class="mt-2 text-xs text-red-500 font-bold style='display:none;'"></p>
                                </div>

                                <!-- Waktu Seleksi -->
                                <div
                                    x-show="tahapan.jenis_tahapan === 'seleksi'"
                                    class="mb-3 grid grid-cols-1 md:grid-cols-2 gap-6"
                                >
                                    <div>
                                        <label
                                            class="block text-sm font-bold text-slate-700 mb-1 tracking-wide"
                                        >
                                            Waktu Mulai<span
                                                class="text-red-500 ml-0.5"
                                                >*</span
                                            >
                                        </label>
                                        <input
                                            type="datetime-local"
                                            :name="`tahapan[${tIndex}][tanggal_mulai]`"
                                            x-model="tahapan.tanggal_mulai"
                                            @change="validateAllTimelines()"
                                            :required="tahapan.jenis_tahapan ===
                                            'seleksi'"
                                            :class="errors[
                                                'tanggal_mulai_' + tIndex
                                            ]
                                                ? 'border-red-500 bg-red-50'
                                                : 'border-slate-300 focus:border-blue-600 focus:ring-0 bg-white'"
                                            class="w-full border rounded-md transition-colors text-sm font-bold py-2.5 px-3 sm:px-4 text-slate-800"
                                        />
                                        <p
                                            x-show="
                                                errors[
                                                    'tanggal_mulai_' + tIndex
                                                ]
                                            "
                                            x-text="
                                                errors[
                                                    'tanggal_mulai_' + tIndex
                                                ]
                                            "
                                            class="mt-2 text-xs text-red-500 font-bold style='display:none;'"
                                        ></p>
                                    </div>
                                    <div>
                                        <label
                                            class="block text-sm font-bold text-slate-700 mb-1 tracking-wide"
                                        >
                                            Waktu Akhir<span
                                                class="text-red-500 ml-0.5"
                                                >*</span
                                            >
                                        </label>
                                        <input
                                            type="datetime-local"
                                            :name="`tahapan[${tIndex}][tanggal_selesai]`"
                                            x-model="tahapan.tanggal_selesai"
                                            @change="validateAllTimelines()"
                                            :required="tahapan.jenis_tahapan ===
                                            'seleksi'"
                                            :class="errors[
                                                'tanggal_selesai_' + tIndex
                                            ]
                                                ? 'border-red-500 bg-red-50'
                                                : 'border-slate-300 focus:border-blue-600 focus:ring-0 bg-white'"
                                            class="w-full border rounded-md transition-colors text-sm font-bold py-2.5 px-3 sm:px-4 text-slate-800"
                                        />
                                        <p x-show="errors['tanggal_selesai_' + tIndex]" x-text="errors['tanggal_selesai_' + tIndex]" class="mt-2 text-xs text-red-500 font-bold style='display:none;'"></p>
                                    </div>
                                </div>

                                <!-- Lampiran Panduan Khusus -->
                                <div
                                    class="bg-slate-50 p-6 rounded-lg border border-slate-200"
                                >
                                    <label
                                        class="text-sm font-bold text-slate-700 mb-2 tracking-wide flex items-center gap-2"
                                    >
                                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                        </svg>
                                        <span
                                            x-text="
                                                tahapan.jenis_tahapan ===
                                                'pengumuman'
                                                    ? 'Lampiran Pengumuman'
                                                    : 'Buku Pedoman Khusus Tahapan'
                                            "
                                        ></span>
                                        <span
                                            class="text-xs font-bold text-slate-400"
                                            >(Opsional)</span
                                        >
                                    </label>

                                    <!-- x-data memastikan state nama file terisolasi per-tahapan agar tidak bentrok -->
                                    <div
                                        x-data="{ fileName: '' }"
                                        class="relative group cursor-pointer mt-1"
                                    >
                                        <input
                                           type="file"
                                           :name="`tahapan_lampiran_${tIndex}`"
                                            :data-lampiran-tahapan="tIndex"
                                            accept=".pdf,application/pdf"
                                            @change="
                                                validateFile(
                                                    'tahapan_lampiran_' +
                                                        tIndex,
                                                    $event.target.files,
                                                    5,
                                                   tahapan.jenis_tahapan ===
                                                       'pengumuman'
                                                       ? 'Lampiran pengumuman'
                                                       : 'Lampiran tahapan',
                                                    'pdf',
                                               );
                                                fileName =
                                                    $event.target.files.length >
                                                    0
                                                        ? $event.target.files[0]
                                                              .name
                                                        : '';
                                            "
                                            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                                        />

                                        <div
                                            :class="errors[
                                                'tahapan_lampiran_' + tIndex
                                            ]
                                                ? 'border-red-500 bg-red-50'
                                                : fileName
                                                  ? 'border-emerald-500 bg-emerald-50'
                                                  : 'border-slate-300 bg-white group-hover:border-blue-500 group-hover:bg-blue-50'"
                                            class="border-2 border-dashed rounded-md p-5 flex items-center justify-center transition-colors"
                                        >
                                            <div class="text-center w-full">
                                                <!-- Kondisi Belum Ada File -->
                                                <template x-if="!fileName">
                                                    <div>
                                                        <span
                                                            class="text-sm font-bold text-blue-600 group-hover:text-blue-700"
                                                            >Klik atau Seret
                                                            PDF</span
                                                       >
                                                        <p class="mt-1 text-xs text-slate-500">Maks 5 MB (PDF)</p>
                                                    </div>
                                                </template>

                                                <!-- Kondisi Sudah Ada File -->
                                                <template x-if="fileName">
                                                    <div
                                                        class="flex flex-col items-center justify-center"
                                                    >
                                                        <svg class="w-6 h-6 text-emerald-600 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                        <span
                                                            class="text-sm font-bold text-emerald-800 truncate max-w-full px-2"
                                                            x-text="fileName"
                                                        ></span>
                                                        <p class="mt-1 text-xs text-emerald-600">Siap Diunggah</p>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Pesan Error Validasi -->
                                    <p
                                        x-show="
                                            errors['tahapan_lampiran_' + tIndex]
                                        "
                                        x-text="
                                            errors['tahapan_lampiran_' + tIndex]
                                        "
                                        class="mt-2 text-xs text-red-500 font-bold"
                                        style="display: none"
                                    ></p>

                                    <!-- File Lama / Data Sebelumnya Tersimpan -->
                                    <div
                                        x-show="tahapan.file_lama"
                                        class="mt-4 flex items-center justify-between bg-blue-50 border border-blue-200 p-3 rounded-md"
                                        style="display: none"
                                    >
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            <span
                                                class="text-xs font-bold text-blue-800"
                                                >Data Sebelumnya Tersimpan</span
                                            >
                                        </div>
                                        <a
                                            :href="tahapan.file_lama
                                                ? '/storage/' +
                                                  tahapan.file_lama
                                                      .toString()
                                                      .replace(
                                                          /[\[\]\x22\\]/g,
                                                          '',
                                                      )
                                                      .trim()
                                                : '#'"
                                            target="_blank"
                                            class="text-xs font-bold text-blue-700 hover:text-white hover:bg-blue-600 bg-white border border-blue-300 px-3 py-1.5 rounded-md transition-colors"
                                        >
                                            Lihat
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <hr
                                x-show="tahapan.jenis_tahapan === 'seleksi'"
                                class="border-slate-100 my-8"
                            />

                            <!-- Tabel Distribusi Penugasan -->
                            <div
                                x-show="tahapan.jenis_tahapan === 'seleksi'"
                                class="mb-2"
                            >
                                <div
                                    class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-4"
                                >
                                    <div>
                                        <h4
                                            class="text-base font-bold text-slate-800 tracking-wide flex items-center gap-2"
                                        >
                                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                                            Penugasan Seleksi
                                        </h4>
                                        <p class="text-sm font-normal text-slate-500 mt-1">Pilih terlebih dulu apakah penugasan yang kamu tetapkan untuk semua jabatan, atau spesifik setiap jabatan. Barulah tetapkan penugasan melalui tabel di bawah ini.</p>
                                    </div>

                                    <!-- Toggle Sama/Beda -->
                                    <div
                                        class="bg-slate-100 p-1 rounded-md inline-flex border border-slate-200 shrink-0"
                                    >
                                        <label
                                            class="flex items-center px-4 py-2 rounded cursor-pointer transition-all"
                                            :class="tahapan.metodeDistribusi ===
                                            'sama'
                                                ? 'bg-white shadow-sm border border-slate-200/60'
                                                : 'hover:bg-slate-200/70'"
                                        >
                                            <input
                                                type="radio"
                                                :name="`tahapan[${tIndex}][metode_distribusi]`"
                                                x-model="
                                                    tahapan.metodeDistribusi
                                                "
                                                value="sama"
                                                class="hidden"
                                            />
                                            <span
                                                class="text-sm font-bold transition-colors"
                                                :class="tahapan.metodeDistribusi ===
                                                'sama'
                                                    ? 'text-blue-700'
                                                    : 'text-slate-500'"
                                                >Penugasan Keseluruhan</span
                                            >
                                        </label>
                                        <label
                                            class="flex items-center px-4 py-2 rounded cursor-pointer transition-all"
                                            :class="tahapan.metodeDistribusi ===
                                            'beda'
                                                ? 'bg-white shadow-sm border border-slate-200/60'
                                                : 'hover:bg-slate-200/70'"
                                        >
                                            <input
                                                type="radio"
                                                :name="`tahapan[${tIndex}][metode_distribusi]`"
                                                x-model="
                                                    tahapan.metodeDistribusi
                                                "
                                                value="beda"
                                                class="hidden"
                                            />
                                            <span
                                                class="text-sm font-bold transition-colors"
                                                :class="tahapan.metodeDistribusi ===
                                                'beda'
                                                    ? 'text-blue-700'
                                                    : 'text-slate-500'"
                                                >Penugasan Spesifik</span
                                            >
                                        </label>
                                    </div>
                                </div>

                                <div
                                    class="overflow-x-auto border border-slate-200 rounded-md"
                                >
                                    <table
                                        class="w-full text-left bg-white border-collapse min-w-[900px]"
                                    >
                                        <thead
                                            class="bg-slate-50 border-b border-slate-200"
                                        >
                                            <tr>
                                                <th
                                                    class="px-5 py-4 text-xs font-bold text-slate-700 tracking-wide w-[18%]"
                                                >
                                                    Posisi / Jabatan
                                                </th>
                                                <th
                                                    class="px-5 py-4 text-xs font-bold text-slate-700 tracking-wide w-[30%]"
                                                >
                                                    Instruksi Tugas
                                                </th>
                                                <th
                                                    class="px-5 py-4 text-xs font-bold text-slate-700 tracking-wide w-[22%]"
                                                >
                                                    Jenis Seleksi
                                                </th>
                                                <th
                                                    class="px-5 py-4 text-xs font-bold text-slate-700 tracking-wide w-[30%]"
                                                >
                                                    Konfigurasi Seleksi
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody
                                            class="divide-y divide-slate-100"
                                        >
                                            <template
                                                x-for="
                                                    (tugas, jIndex) in
                                                    tahapan.tugasJabatan
                                                "
                                                :key="jIndex"
                                            >
                                                <tr
                                                    x-show="
                                                        tahapan.metodeDistribusi ===
                                                            'beda' ||
                                                        jIndex === 0
                                                    "
                                                    class="hover:bg-slate-50/50 transition-colors"
                                                >
                                                    <!-- Kolom 1: Posisi / Jabatan -->
                                                    <td
                                                        class="px-5 py-5 align-top"
                                                    >
                                                        <span
                                                            class="inline-block px-3 py-1.5 rounded-md text-xs font-bold"
                                                            :class="'bg-blue-100 text-blue-800'"
                                                            x-text="
                                                                labelPenugasan(
                                                                    tahapan,
                                                                    tugas,
                                                                )
                                                            "
                                                        ></span>
                                                        <input
                                                            type="hidden"
                                                            :name="`tahapan[${tIndex}][tugas][${jIndex}][nama_jabatan]`"
                                                            x-model="
                                                                tugas.nama_jabatan
                                                            "
                                                            :disabled="tahapan.metodeDistribusi ===
                                                                'sama' &&
                                                            jIndex !== 0"
                                                        />
                                                        <input
                                                            type="hidden"
                                                            :name="`tahapan[${tIndex}][tugas][${jIndex}][jabatan_id]`"
                                                            x-model="
                                                                tugas.jabatan_id
                                                            "
                                                            :disabled="tahapan.metodeDistribusi ===
                                                                'sama' &&
                                                            jIndex !== 0"
                                                        />
                                                        <input
                                                            type="hidden"
                                                            :name="`tahapan[${tIndex}][tugas][${jIndex}][id]`"
                                                            x-model="tugas.id"
                                                        />
                                                    </td>

                                                    <!-- Kolom 2: Instruksi -->
                                                    <td
                                                        class="px-5 py-5 align-top"
                                                    >
                                                        <textarea
                                                            :name="`tahapan[${tIndex}][tugas][${jIndex}][deskripsi_tugas]`"
                                                            x-model="
                                                                tugas.deskripsi_tugas
                                                            "
                                                            rows="4"
                                                            class="w-full border border-slate-300 focus:border-blue-600 focus:ring-0 rounded-md transition-colors text-sm font-bold py-2.5 px-3 bg-white text-slate-800 resize-none"
                                                            placeholder="Ketik instruksi penugasan disini jika ada"
                                                            :disabled="tahapan.metodeDistribusi ===
                                                                'sama' &&
                                                            jIndex !== 0"
                                                        ></textarea>
                                                    </td>

                                                    <!-- Kolom 3: Format Seleksi -->
                                                    <td
                                                        class="px-5 py-5 align-top"
                                                    >
                                                        <!-- Select Input -->
                                                        <template
                                                            x-if="
                                                                isTahapSeleksiPertama(
                                                                    tIndex,
                                                                )
                                                            "
                                                        >
                                                            <div>
                                                                <select
                                                                    class="w-full border border-slate-300 bg-slate-100 rounded-md text-xs font-bold py-2.5 px-3 text-slate-500 cursor-not-allowed"
                                                                    disabled
                                                                >
                                                                    <option
                                                                        selected
                                                                    >
                                                                        📝
                                                                        Pengisian
                                                                        Form
                                                                    </option>
                                                                </select>
                                                                <input
                                                                    type="hidden"
                                                                    :name="`tahapan[${tIndex}][tugas][${jIndex}][tipe_tugas]`"
                                                                    value="pengisian_form"
                                                                />
                                                            </div>
                                                        </template>
                                                        <template
                                                            x-if="
                                                                !isTahapSeleksiPertama(
                                                                    tIndex,
                                                                )
                                                            "
                                                        >
                                                            <select
                                                                :name="`tahapan[${tIndex}][tugas][${jIndex}][tipe_tugas]`"
                                                                x-model="
                                                                    tugas.tipe_tugas
                                                                "
                                                                class="w-full border border-slate-300 focus:border-blue-600 focus:ring-0 rounded-md transition-colors text-sm font-bold py-2.5 px-3 bg-white text-slate-800"
                                                            >
                                                                <option
                                                                    value="pengisian_form"
                                                                >
                                                                    📝 Pengisian
                                                                    Form
                                                                </option>
                                                                <option
                                                                    value="unggah_berkas"
                                                                >
                                                                    ⚙️ Upload
                                                                    Berkas
                                                                </option>
                                                                <option
                                                                    value="wawancara"
                                                                >
                                                                    🎙️ Wawancara
                                                                </option>
                                                            </select>
                                                        </template>

                                                        <!-- Checkbox Format (Bila Unggah) -->
                                                        <div
                                                            x-show="
                                                                tugas.tipe_tugas ===
                                                                'unggah_berkas'
                                                            "
                                                            class="bg-slate-50 border border-slate-200 p-4 rounded-md mt-3"
                                                        >
                                                            <p class="text-xs font-bold text-slate-700 mb-2">Format Diizinkan:</p>
                                                            <div
                                                                class="flex flex-wrap gap-2"
                                                            >
                                                                <template
                                                                    x-for="
                                                                        fmt in
                                                                        [
                                                                            'pdf',
                                                                            'word',
                                                                            'excel',
                                                                            'jpg',
                                                                            'png',
                                                                            'zip',
                                                                        ]
                                                                    "
                                                                    :key="fmt"
                                                                >
                                                                    <label
                                                                        class="flex items-center text-xs font-bold bg-white border border-slate-300 px-3 py-1.5 rounded-md cursor-pointer hover:border-blue-400 hover:bg-blue-50 transition-colors"
                                                                    >
                                                                        <input
                                                                            type="checkbox"
                                                                            :value="fmt"
                                                                            x-model="
                                                                                tugas.format_proyek
                                                                            "
                                                                            :name="`tahapan[${tIndex}][tugas][${jIndex}][format_proyek][]`"
                                                                            class="w-3.5 h-3.5 mr-2 text-blue-600 border-slate-300 rounded focus:ring-0 transition-colors"
                                                                            :disabled="tahapan.metodeDistribusi ===
                                                                                'sama' &&
                                                                            jIndex !==
                                                                                0"
                                                                        />
                                                                        <span
                                                                            x-text="
                                                                                fmt.toUpperCase()
                                                                            "
                                                                            class="text-slate-800"
                                                                        ></span>
                                                                    </label>
                                                                </template>
                                                            </div>
                                                        </div>
                                                    </td>

                                                    <!-- Kolom 4: Konfigurasi Lanjutan -->
                                                    <td
                                                        class="px-5 py-5 align-top"
                                                    >
                                                        <div
                                                            class="mb-4 bg-slate-50 p-4 rounded-md border border-slate-200"
                                                        >
                                                            <p class="text-xs font-bold text-slate-700 mb-2 flex items-center gap-1.5">
                                                                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                                                Sediakan
                                                                Lampiran
                                                                (Opsional)
                                                            </p>
                                                            <input
                                                                type="file"
                                                               :name="`tahapan[${tIndex}][tugas][${jIndex}][lampiran_files][]`"
                                                               multiple
                                                                accept=".pdf,.doc,.docx,.xls,.xlsx,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
                                                                class="block w-full text-sm text-slate-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 bg-white border border-slate-300 rounded-md transition-colors cursor-pointer focus:outline-none"
                                                                :disabled="tahapan.metodeDistribusi ===
                                                                    'sama' &&
                                                                jIndex !== 0"
                                                            />
                                                            <input
                                                                type="hidden"
                                                                :name="`tahapan[${tIndex}][tugas][${jIndex}][berkas_lama_json]`"
                                                                :value="JSON.stringify(
                                                                    tugas.berkas_lama,
                                                                )"
                                                                :disabled="tahapan.metodeDistribusi ===
                                                                    'sama' &&
                                                                jIndex !== 0"
                                                            />

                                                            <template
                                                                x-if="
                                                                    tugas.berkas_lama &&
                                                                    tugas
                                                                        .berkas_lama
                                                                        .length >
                                                                        0
                                                                "
                                                            >
                                                                <div
                                                                    class="mt-4 pt-4 border-t border-slate-200"
                                                                >
                                                                    <span
                                                                        class="text-xs font-bold text-blue-800 block mb-2.5"
                                                                        >Data
                                                                        Sebelumnya
                                                                        Tersimpan:</span
                                                                    >
                                                                    <div
                                                                        class="flex flex-col gap-2"
                                                                    >
                                                                        <template
                                                                            x-for="
                                                                                (berkas,
                                                                                bIdx) in
                                                                                tugas.berkas_lama
                                                                            "
                                                                            :key="bIdx"
                                                                        >
                                                                            <a
                                                                                :href="'/storage/' +
                                                                                berkas
                                                                                    .replace(
                                                                                        /[\[\]\x22\\]/g,
                                                                                        '',
                                                                                    )
                                                                                    .trim()"
                                                                                target="_blank"
                                                                                class="flex items-center gap-2 text-xs font-bold text-blue-700 hover:text-white hover:bg-blue-600 bg-white px-3 py-2 rounded-md border border-blue-300 transition-colors w-max"
                                                                            >
                                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                                                                                Lampiran
                                                                                <span
                                                                                    x-text="
                                                                                        bIdx +
                                                                                        1
                                                                                    "
                                                                                ></span>
                                                                            </a>
                                                                        </template>
                                                                    </div>
                                                                </div>
                                                            </template>
                                                        </div>

                                                        <!-- Tombol Builder -->
                                                        <div
                                                            x-show="
                                                                [
                                                                    'pengisian_form',
                                                                    'wawancara',
                                                                ].includes(
                                                                    tugas.tipe_tugas,
                                                                )
                                                            "
                                                        >
                                                            <input
                                                                type="hidden"
                                                                :name="`tahapan[${tIndex}][tugas][${jIndex}][skema_form_json]`"
                                                                :value="JSON.stringify(
                                                                    tugas.skema_form,
                                                                )"
                                                                :disabled="tahapan.metodeDistribusi ===
                                                                    'sama' &&
                                                                jIndex !== 0"
                                                            />
                                                            <button
                                                                type="button"
                                                                @click="
                                                                    bukaBuilder(
                                                                        tIndex,
                                                                        jIndex,
                                                                        labelPenugasan(
                                                                            tahapan,
                                                                            tugas,
                                                                        ),
                                                                        tugas.tipe_tugas ===
                                                                            'wawancara',
                                                                    )
                                                                "
                                                                class="w-full text-sm font-bold text-blue-700 bg-blue-50 hover:bg-blue-600 hover:text-white px-4 py-2.5 rounded-md border border-blue-200 hover:border-blue-600 transition-colors flex items-center justify-center gap-2 shrink-0"
                                                                :disabled="tahapan.metodeDistribusi ===
                                                                    'sama' &&
                                                                jIndex !== 0"
                                                            >
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                                                                <span
                                                                    x-text="
                                                                        tugas.skema_form &&
                                                                        tugas
                                                                            .skema_form
                                                                            .length >
                                                                            0
                                                                            ? 'Rancang Form (' +
                                                                              tugas
                                                                                  .skema_form
                                                                                  .length +
                                                                              ' Baris)'
                                                                            : tugas.tipe_tugas ===
                                                                                'wawancara'
                                                                              ? 'Rancang Penilaian'
                                                                              : 'Rancang Form'
                                                                    "
                                                                ></span>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <hr class="border-slate-100" />

            <!-- Tombol Navigasi Bawah (Final Submit) -->
            <div
                class="flex flex-col-reverse sm:flex-row justify-between items-center gap-4"
            >
                <button
                    type="button"
                    @click="
                        tab = 2;
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    "
                    class="text-sm font-bold text-slate-500 hover:text-slate-800 transition-colors flex items-center justify-center gap-2 px-4 py-2 w-full sm:w-auto"
                >
                    &larr; Kembali
                </button>
                <button
                    type="submit"
                    class="w-full sm:w-auto bg-blue-600 text-white px-6 py-3.5 rounded-lg font-bold text-sm hover:bg-blue-700 transition-colors flex items-center justify-center gap-2 shadow-md hover:shadow-lg"
                >
                    Simpan Perubahan
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                </button>
            </div>
        </div>
    </div>
</div>
