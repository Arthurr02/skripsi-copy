<!-- LANGKAH 3: ALUR TAHAPAN SELEKSI -->
<div
    x-show="tab === 3"
    x-transition:enter="transition ease-out duration-300 transform"
    x-transition:enter-start="opacity-0 translate-y-4"
    class="space-y-8"
    style="display: none"
>
    <div class="bg-white p-8 md:p-10 rounded-lg border border-slate-200">
        <!-- Header Section -->
        <div
            class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 border-b border-slate-200 pb-5 gap-5"
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
                <p class="text-sm font-normal text-slate-500 mt-2">Bangun struktur seleksi, jadwalkan waktu, dan alokasikan penugasan secara detail.</p>
            </div>
            <button
                type="button"
                @click="tambahTahapan()"
                class="text-xs font-bold uppercase tracking-wide text-blue-700 bg-blue-50 hover:bg-blue-600 hover:text-white px-5 py-3 rounded-md border border-blue-200 hover:border-blue-600 transition-colors whitespace-nowrap flex items-center gap-2"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                Tambah Tahapan Baru
            </button>
        </div>

        <!-- Navigasi Tabs Antar Tahapan -->
        <div class="flex overflow-x-auto gap-3 pb-4 custom-scrollbar">
            <template x-for="(t, idx) in listTahapan" :key="idx">
                <button
                    type="button"
                    @click="activeTahapanIndex = idx"
                    :class="activeTahapanIndex === idx
                        ? 'bg-slate-800 text-white border-transparent'
                        : 'bg-slate-50 text-slate-500 hover:bg-slate-100 hover:text-slate-800 border-slate-200'"
                    class="px-6 py-2.5 rounded-md border text-xs font-bold uppercase tracking-wide transition-colors whitespace-nowrap flex items-center gap-2.5"
                >
                    Tahapan <span x-text="idx + 1"></span>
                    <span
                        x-show="
                            Object.keys(errors).some((k) =>
                                k.includes('_' + idx),
                            )
                        "
                        class="w-2.5 h-2.5 rounded-full bg-red-500 animate-pulse"
                    ></span>
                </button>
            </template>
        </div>

        <!-- Kontainer Konten Tahapan Utama -->
        <div class="relative min-h-[400px] mt-2">
            <template x-for="(tahapan, tIndex) in listTahapan" :key="tIndex">
                <div
                    x-data="{ tStep: 1 }"
                    x-show="activeTahapanIndex === tIndex"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 translate-y-2"
                    class="p-6 md:p-8 bg-slate-50 border border-slate-200 rounded-lg"
                >
                    <!-- Header Dalam Tahapan -->
                    <div
                        class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4 border-b border-slate-200 pb-5"
                    >
                        <span
                            class="text-sm font-bold text-slate-800 uppercase tracking-wide flex items-center gap-2.5"
                        >
                            <span
                                class="w-1.5 h-6 bg-blue-600 rounded-full inline-block"
                            ></span>
                            Konfigurasi Tahap <span x-text="tIndex + 1"></span>
                        </span>
                        <button
                            type="button"
                            @click="hapusTahapan(tIndex)"
                            x-show="listTahapan.length > 1"
                            class="text-xs font-bold uppercase tracking-wide text-red-500 hover:text-white bg-red-50 hover:bg-red-500 px-4 py-2.5 rounded-md transition-colors flex items-center gap-1.5"
                        >
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            Hapus Tahapan
                        </button>
                    </div>

                    <!-- Pengendali Segmen A/B -->
                    <div
                        class="inline-flex p-1 bg-slate-200 rounded-md mb-8 w-full sm:w-auto"
                        role="group"
                    >
                        <button
                            type="button"
                            @click="tStep = 1"
                            :class="tStep === 1
                                ? 'bg-white text-blue-700 border border-slate-300'
                                : 'text-slate-600 hover:text-slate-800 border border-transparent'"
                            class="flex-1 sm:flex-none px-6 py-2 text-xs font-bold uppercase tracking-wide rounded transition-colors"
                        >
                            A. Jadwal & Info
                        </button>
                        <button
                            type="button"
                            @click="tStep = 2"
                            :class="tStep === 2
                                ? 'bg-white text-blue-700 border border-slate-300'
                                : 'text-slate-600 hover:text-slate-800 border border-transparent'"
                            class="flex-1 sm:flex-none px-6 py-2 text-xs font-bold uppercase tracking-wide rounded transition-colors"
                        >
                            B. Skema Penugasan
                        </button>
                    </div>

                    <!-- KONTEN SUB-TAHAP A -->
                    <div
                        x-show="tStep === 1"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0"
                    >
                        <div class="grid grid-cols-1 gap-6 mb-8">
                            <!-- Nama Tahapan -->
                            <div>
                                <label
                                    class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-2.5"
                                >
                                    Nama Tahapan
                                    <span class="text-red-500 ml-0.5">*</span>
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
                                        : 'border border-slate-300 focus:border-blue-600 focus:ring-0 bg-white'"
                                    class="w-full rounded-md text-sm font-bold py-3.5 transition-colors text-slate-900"
                                    placeholder="Cth: Seleksi Berkas / Ujian Tulis"
                                    required
                                />
                                <p x-show="errors['nama_tahapan_' + tIndex]" x-text="errors['nama_tahapan_' + tIndex]" class="mt-2 text-xs text-red-500 font-bold ml-1" style="display: none"></p>
                            </div>

                            <!-- Deskripsi Instruksi -->
                            <div class="md:col-span-2">
                                <label
                                    class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-2.5"
                                >
                                    Deskripsi Instruksi
                                    <span class="text-red-500 ml-0.5">*</span>
                                </label>
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
                                    :class="errors[
                                        'deskripsi_tahapan_' + tIndex
                                    ]
                                        ? 'border-red-500 bg-red-50 text-red-700 focus:ring-0 focus:border-red-500'
                                        : 'border border-slate-300 focus:border-blue-600 focus:ring-0 bg-white'"
                                    class="w-full rounded-md text-sm py-3.5 transition-colors text-slate-900"
                                    placeholder="Jelaskan secara singkat apa yang perlu disiapkan pendaftar..."
                                    required
                                />
                                <p x-show="errors['deskripsi_tahapan_' + tIndex]" x-text="errors['deskripsi_tahapan_' + tIndex]" class="mt-2 text-xs text-red-500 font-bold ml-1" style="display: none"></p>
                            </div>

                            <!-- Lampiran Panduan Khusus -->
                            <div
                                class="md:col-span-2 bg-white p-6 rounded-lg border border-slate-200"
                            >
                                <label
                                    class="flex items-center gap-2 text-xs font-bold text-slate-700 uppercase tracking-wide mb-4"
                                >
                                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                    Lampiran Panduan Khusus Tahapan (Opsional)
                                </label>
                                <input
                                    type="file"
                                    :name="`tahapan_lampiran_${tIndex}`"
                                    accept=".pdf"
                                    @change="
                                        validateFile(
                                            'tahapan_lampiran_' + tIndex,
                                            $event.target.files,
                                            5,
                                            'Lampiran tahapan',
                                        )
                                    "
                                    class="block w-full text-sm text-slate-600 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-bold file:uppercase file:tracking-wide file:bg-slate-200 file:text-slate-700 hover:file:bg-slate-300 border border-slate-300 rounded-md bg-white transition-colors cursor-pointer"
                                />
                                <div
                                    x-show="tahapan.file_lama"
                                    class="mt-4 flex items-center justify-between bg-blue-50 border border-blue-200 p-3 rounded-md"
                                    style="display: none"
                                >
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        <span
                                            class="text-xs font-bold text-blue-800"
                                            >Lampiran Tersimpan</span
                                        >
                                    </div>
                                    <a
                                        :href="tahapan.file_lama
                                            ? '/storage/' +
                                              tahapan.file_lama
                                                  .toString()
                                                  .replace(/[\[\]\x22\\]/g, '')
                                                  .trim()
                                            : '#'"
                                        target="_blank"
                                        class="text-xs font-bold uppercase tracking-wide text-blue-700 hover:text-white bg-white hover:bg-blue-600 border border-blue-300 px-3 py-1.5 rounded-md transition-colors"
                                    >
                                        Lihat
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Pengaturan Waktu -->
                        <div
                            class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8 p-6 bg-white rounded-lg border border-slate-200"
                        >
                            <div>
                                <label
                                    class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-2.5"
                                >
                                    Waktu Mulai
                                    <span class="text-red-500 ml-0.5">*</span>
                                </label>
                                <input
                                    type="datetime-local"
                                    :name="`tahapan[${tIndex}][tanggal_mulai]`"
                                    x-model="tahapan.tanggal_mulai"
                                    @change="validateAllTimelines()"
                                    class="block w-full rounded-md text-sm border border-slate-300 focus:border-blue-600 focus:ring-0 bg-white py-3 transition-colors"
                                    required
                                />
                                <p x-show="errors['tanggal_mulai_' + tIndex]" x-text="errors['tanggal_mulai_' + tIndex]" class="mt-2 text-xs text-red-500 font-bold ml-1" style="display: none"></p>
                            </div>

                            <div class="flex flex-col justify-center pt-2">
                                <label
                                    class="flex items-center gap-3 text-xs font-bold text-slate-700 uppercase tracking-wide cursor-pointer hover:text-blue-600 transition-colors group"
                                >
                                    <div class="relative flex items-center">
                                        <input
                                            type="checkbox"
                                            x-model="tahapan.is_rentang_waktu"
                                            @change="validateAllTimelines()"
                                            class="w-5 h-5 rounded border-slate-300 text-blue-600 focus:ring-0 cursor-pointer transition-colors"
                                        />
                                    </div>
                                    Gunakan Batas Waktu Akhir (Tenggat)
                                </label>

                                <div class="mt-4 h-[72px]">
                                    <template x-if="tahapan.is_rentang_waktu">
                                        <div>
                                            <input
                                                type="datetime-local"
                                                :name="`tahapan[${tIndex}][tanggal_selesai]`"
                                                x-model="
                                                    tahapan.tanggal_selesai
                                                "
                                                @change="validateAllTimelines()"
                                                class="block w-full rounded-md text-sm border border-slate-300 focus:border-blue-600 focus:ring-0 bg-white py-3 transition-colors"
                                                required
                                            />
                                            <p x-show="
                                                    errors[
                                                        'tanggal_selesai_' +
                                                            tIndex
                                                    ]
                                                " x-text="
                                                    errors[
                                                        'tanggal_selesai_' +
                                                            tIndex
                                                    ]
                                                " class="mt-2 text-xs text-red-500 font-bold ml-1"></p>
                                        </div>
                                    </template>
                                    <template x-if="!tahapan.is_rentang_waktu">
                                        <div
                                            class="p-3 bg-slate-100 rounded-md border border-slate-200 flex items-start gap-2.5"
                                        >
                                            <svg class="w-4 h-4 text-slate-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            <span
                                                class="text-xs text-slate-600 leading-relaxed"
                                            >
                                                Tahapan ini diatur dengan waktu
                                                tunggal (Tidak ada rentang
                                                tenggat).
                                            </span>
                                            <input
                                                type="hidden"
                                                :name="`tahapan[${tIndex}][tanggal_selesai]`"
                                                :value="tahapan.tanggal_mulai"
                                            />
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <!-- Tombol Lanjut ke Skema -->
                        <div
                            class="flex justify-end pt-6 border-t border-slate-200"
                        >
                            <button
                                type="button"
                                @click="tStep = 2"
                                class="bg-slate-800 text-white px-6 py-3 rounded-md font-bold text-sm hover:bg-slate-700 transition-colors flex items-center gap-2"
                            >
                                Lanjutkan Skema Tugas
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </button>
                        </div>
                    </div>

                    <!-- KONTEN SUB-TAHAP B -->
                    <div
                        x-show="tStep === 2"
                        style="display: none"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0"
                    >
                        <!-- Peringatan Tahap 1 (Wajib Form) -->
                        <div
                            x-show="tIndex === 0"
                            class="flex items-start gap-4 p-5 bg-blue-50 border border-blue-200 rounded-lg mb-8"
                            x-init="
                                $watch('tahapan.ada_tugas', (val) => {
                                    if (tIndex === 0 && !val)
                                        tahapan.ada_tugas = true;
                                });
                                tahapan.ada_tugas = true;
                            "
                        >
                            <div class="mt-0.5">
                                <input
                                    type="checkbox"
                                    checked
                                    disabled
                                    class="w-5 h-5 text-blue-600 rounded border-blue-300 bg-blue-200 cursor-not-allowed"
                                />
                            </div>
                            <div>
                                <span
                                    class="block text-sm font-bold text-blue-900"
                                    >Tahap Ini Membutuhkan Form Pendaftar</span
                                >
                                <p class="text-xs font-normal text-blue-700 mt-1">Sistem mewajibkan Tahapan 1 sebagai pintu gerbang pengumpulan data (form identitas) secara otomatis.</p>
                            </div>
                        </div>

                        <!-- Toggle Tahap Lainnya -->
                        <label
                            x-show="tIndex > 0"
                            class="flex items-start gap-4 p-5 bg-white border border-slate-200 rounded-lg mb-8 cursor-pointer hover:border-blue-500 hover:bg-blue-50 transition-colors group"
                        >
                            <div class="mt-0.5">
                                <input
                                    type="checkbox"
                                    x-model="tahapan.ada_tugas"
                                    class="w-5 h-5 text-blue-600 rounded border-slate-300 focus:ring-0 cursor-pointer"
                                />
                            </div>
                            <div>
                                <span
                                    class="block text-sm font-bold text-slate-800 group-hover:text-blue-700 transition-colors"
                                    >Tahap Ini Membutuhkan Form / Penugasan
                                    Khusus</span
                                >
                                <p class="text-xs text-slate-500 mt-1">Biarkan non-aktif jika tahap ini murni sekadar pengumuman hasil seleksi tanpa perlu aksi dari peserta.</p>
                            </div>
                        </label>

                        <template x-if="tahapan.ada_tugas">
                            <div
                                class="bg-white p-6 md:p-8 rounded-lg border border-slate-200 mb-6"
                            >
                                <!-- Header Tabel Tugas -->
                                <div
                                    class="mb-6 flex flex-col md:flex-row md:items-end justify-between gap-4 border-b border-slate-200 pb-5"
                                >
                                    <div>
                                        <h4
                                            class="text-base font-bold text-slate-800 flex items-center gap-2"
                                        >
                                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                                            Distribusi Penugasan
                                        </h4>
                                        <p class="text-xs font-normal text-slate-500 mt-1.5">Tersinkronisasi otomatis dengan Formasi Jabatan.</p>
                                    </div>

                                    <!-- Toggle Sama/Beda -->
                                    <div
                                        class="bg-slate-100 p-1 rounded-md inline-flex border border-slate-200 shrink-0"
                                    >
                                        <label
                                            class="flex items-center px-4 py-2 rounded cursor-pointer transition-colors"
                                            :class="tahapan.metodeDistribusi ===
                                            'sama'
                                                ? 'bg-white border border-slate-300'
                                                : 'hover:bg-slate-200'"
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
                                                class="text-xs font-bold uppercase tracking-wide"
                                                :class="tahapan.metodeDistribusi ===
                                                'sama'
                                                    ? 'text-blue-700'
                                                    : 'text-slate-600'"
                                                >Penugasan Seragam</span
                                            >
                                        </label>
                                        <label
                                            class="flex items-center px-4 py-2 rounded cursor-pointer transition-colors"
                                            :class="tahapan.metodeDistribusi ===
                                            'beda'
                                                ? 'bg-white border border-slate-300'
                                                : 'hover:bg-slate-200'"
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
                                                class="text-xs font-bold uppercase tracking-wide"
                                                :class="tahapan.metodeDistribusi ===
                                                'beda'
                                                    ? 'text-blue-700'
                                                    : 'text-slate-600'"
                                                >Spesifik Tiap Divisi</span
                                            >
                                        </label>
                                    </div>
                                </div>

                                <!-- Tabel Distribusi Tugas -->
                                <div
                                    class="overflow-x-auto border border-slate-200 rounded-md"
                                >
                                    <table
                                        class="w-full text-left text-sm min-w-[900px] bg-white border-collapse"
                                    >
                                        <thead
                                            class="bg-slate-100 border-b border-slate-200"
                                        >
                                            <tr>
                                                <th
                                                    class="px-5 py-4 font-bold text-slate-700 text-xs uppercase tracking-wide w-[18%]"
                                                >
                                                    Divisi / Jabatan
                                                </th>
                                                <th
                                                    class="px-5 py-4 font-bold text-slate-700 text-xs uppercase tracking-wide w-[30%]"
                                                >
                                                    Detail Instruksi
                                                </th>
                                                <th
                                                    class="px-5 py-4 font-bold text-slate-700 text-xs uppercase tracking-wide w-[22%]"
                                                >
                                                    Format & Kebutuhan
                                                </th>
                                                <th
                                                    class="px-5 py-4 font-bold text-slate-700 text-xs uppercase tracking-wide w-[30%]"
                                                >
                                                    Konfigurasi Form Lanjutan
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody
                                            class="divide-y divide-slate-200"
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
                                                    class="hover:bg-slate-50 transition-colors"
                                                >
                                                    <!-- Kolom 1: Divisi -->
                                                    <td
                                                        class="px-5 py-5 align-top"
                                                    >
                                                        <span
                                                            class="inline-block px-3 py-1.5 rounded text-xs font-bold uppercase tracking-wide"
                                                            :class="tahapan.metodeDistribusi ===
                                                            'sama'
                                                                ? 'bg-blue-100 text-blue-800'
                                                                : 'bg-slate-800 text-white'"
                                                            x-text="
                                                                tahapan.metodeDistribusi ===
                                                                'sama'
                                                                    ? 'BERLAKU UNTUK SEMUA'
                                                                    : tugas.nama_jabatan
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
                                                            class="w-full rounded-md border border-slate-300 text-sm focus:border-blue-600 focus:ring-0 bg-white transition-colors resize-none leading-relaxed"
                                                            placeholder="Ketik rincian penugasan di sini..."
                                                            :disabled="tahapan.metodeDistribusi ===
                                                                'sama' &&
                                                            jIndex !== 0"
                                                        ></textarea>
                                                    </td>

                                                    <!-- Kolom 3: Format Seleksi -->
                                                    <td
                                                        class="px-5 py-5 align-top space-y-4"
                                                    >
                                                        <template
                                                            x-if="tIndex === 0"
                                                        >
                                                            <div>
                                                                <select
                                                                    class="w-full rounded-md text-sm border border-slate-300 bg-slate-100 font-bold text-slate-500 cursor-not-allowed"
                                                                    disabled
                                                                >
                                                                    <option
                                                                        selected
                                                                    >
                                                                        📝
                                                                        PENGISIAN
                                                                        FORM
                                                                        IDENTITAS
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
                                                            x-if="tIndex > 0"
                                                        >
                                                            <select
                                                                :name="`tahapan[${tIndex}][tugas][${jIndex}][tipe_tugas]`"
                                                                x-model="
                                                                    tugas.tipe_tugas
                                                                "
                                                                class="w-full rounded-md text-sm font-bold border border-slate-300 text-slate-800 focus:border-blue-600 focus:ring-0 transition-colors"
                                                            >
                                                                <option
                                                                    value="pengisian_form"
                                                                >
                                                                    📝 Pengisian
                                                                    Form Web
                                                                </option>
                                                                <option
                                                                    value="unggah_berkas"
                                                                >
                                                                    ⚙️ Unggah
                                                                    Hasil Karya
                                                                </option>
                                                                <option
                                                                    value="wawancara"
                                                                >
                                                                    🎙️ Penilaian
                                                                    Wawancara
                                                                </option>
                                                            </select>
                                                        </template>

                                                        <!-- Checkbox Format (Bila Unggah) -->
                                                        <div
                                                            x-show="
                                                                tugas.tipe_tugas ===
                                                                'unggah_berkas'
                                                            "
                                                            class="bg-slate-50 border border-slate-200 p-3.5 rounded-md mt-3"
                                                        >
                                                            <p class="text-xs font-bold text-slate-600 uppercase tracking-wide mb-2.5">Format Diizinkan:</p>
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
                                                                        class="flex items-center text-xs font-bold bg-white border border-slate-300 px-2.5 py-1.5 rounded cursor-pointer hover:border-blue-500 hover:bg-blue-50 transition-colors"
                                                                    >
                                                                        <input
                                                                            type="checkbox"
                                                                            :value="fmt"
                                                                            x-model="
                                                                                tugas.format_proyek
                                                                            "
                                                                            :name="`tahapan[${tIndex}][tugas][${jIndex}][format_proyek][]`"
                                                                            class="w-3.5 h-3.5 mr-2 text-blue-600 border-slate-300 rounded focus:ring-0"
                                                                            :disabled="tahapan.metodeDistribusi ===
                                                                                'sama' &&
                                                                            jIndex !==
                                                                                0"
                                                                        />
                                                                        <span
                                                                            x-text="
                                                                                fmt.toUpperCase()
                                                                            "
                                                                            class="text-slate-700"
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
                                                        <!-- Template Upload Khusus -->
                                                        <div
                                                            class="mb-5 bg-slate-50 p-4 rounded-md border border-slate-200"
                                                        >
                                                            <p class="text-xs font-bold text-slate-600 uppercase tracking-wide mb-2.5 flex items-center gap-1.5">
                                                                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                                                Sediakan
                                                                Template (Bila
                                                                Ada)
                                                            </p>
                                                            <input
                                                                type="file"
                                                                :name="`tahapan[${tIndex}][tugas][${jIndex}][lampiran_files][]`"
                                                                multiple
                                                                accept=".pdf,.doc,.docx"
                                                                class="block w-full text-xs text-slate-600 file:mr-3 file:py-1.5 file:px-3 file:rounded file:border-0 file:text-xs file:font-bold file:uppercase file:bg-blue-100 file:text-blue-700 hover:file:bg-blue-200 bg-white border border-slate-300 rounded-md transition-colors cursor-pointer"
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

                                                            <!-- Terunggah -->
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
                                                                        class="text-xs font-bold text-emerald-700 block mb-2"
                                                                        >Telah
                                                                        Terunggah:</span
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
                                                                                class="flex items-center gap-2 text-xs font-bold text-slate-700 bg-white hover:bg-slate-100 px-3 py-2 rounded border border-slate-300 transition-colors w-max"
                                                                            >
                                                                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
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
                                                                        tahapan.metodeDistribusi ===
                                                                            'sama'
                                                                            ? 'Semua Jabatan'
                                                                            : tugas.nama_jabatan,
                                                                        tugas.tipe_tugas ===
                                                                            'wawancara',
                                                                    )
                                                                "
                                                                class="w-full bg-slate-800 text-white py-3 text-xs font-bold uppercase tracking-wide rounded-md hover:bg-slate-700 transition-colors flex items-center justify-center gap-2"
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
                                                                            ? 'Edit Skema (' +
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
                        </template>

                        <!-- Tombol Kembali ke A -->
                        <div
                            class="flex justify-start pt-6 border-t border-slate-200 mt-6"
                        >
                            <button
                                type="button"
                                @click="tStep = 1"
                                class="text-slate-600 hover:text-slate-900 font-bold text-sm flex items-center gap-2 transition-colors py-2 px-4 rounded-md hover:bg-slate-100 border border-transparent hover:border-slate-300"
                            >
                                &larr; Kembali ke Jadwal & Info
                            </button>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <!-- Tombol Navigasi Bawah (Final Submit) -->
    <div
        class="flex flex-col-reverse sm:flex-row justify-between items-center mt-10 gap-5 pb-10"
    >
        <button
            type="button"
            @click="
                tab = 2;
                window.scrollTo({ top: 0, behavior: 'smooth' });
            "
            class="text-sm font-bold text-slate-500 hover:text-slate-800 transition-colors flex items-center gap-2 px-4 py-2 w-full sm:w-auto justify-center"
        >
            &larr; Kembali ke Formasi
        </button>
        <button
            type="submit"
            class="bg-blue-600 text-white px-10 py-3.5 rounded-md font-bold text-sm hover:bg-blue-700 transition-colors flex items-center gap-2.5 w-full sm:w-auto justify-center"
        >
            Simpan Perubahan
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
        </button>
    </div>
</div>
