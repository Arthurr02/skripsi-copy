@php
    // Logika untuk mengelompokkan data jabatan dari Database agar sesuai dengan format UI
    $groupedJabatan = [];

    if (isset($jabatanData) && count($jabatanData) > 0) {
        $tempGroup = [];
        foreach ($jabatanData as $jabatan) {
            // Ambil posisi, ubah tanda '-' (default database) menjadi string kosong
            $posisi =
                $jabatan->nama_posisi === '-' || empty($jabatan->nama_posisi)
                    ? ''
                    : $jabatan->nama_posisi;

            if (!isset($tempGroup[$posisi])) {
                $tempGroup[$posisi] = [];
            }
            $tempGroup[$posisi][] = [
                'id' => $jabatan->id,
                'nama' => $jabatan->nama_jabatan,
            ];
        }

        foreach ($tempGroup as $posisi => $jabatans) {
            $groupedJabatan[] = [
                'posisi' => $posisi,
                'jabatans' => $jabatans,
            ];
        }
    } else {
        // Jika form baru / belum ada data di database
        $groupedJabatan = [
            [
                'posisi' => '',
                'jabatans' => [['nama' => '']],
            ],
        ];
    }
@endphp

<!-- LANGKAH 2: FORMASI JABATAN -->
<div
    x-show="tab === 2"
    x-transition:enter="transition ease-out duration-300 transform"
    x-transition:enter-start="opacity-0 translate-y-4"
    class="space-y-8"
    style="display: none"
>
    <div class="bg-white p-8 md:p-10 rounded-lg border border-slate-200">
        <!-- Header Section -->
        <div class="mb-8 pb-5 border-b border-slate-200">
            <h3
                class="text-xl font-extrabold text-slate-800 tracking-tight flex items-center gap-3"
            >
                <div
                    class="w-10 h-10 bg-blue-100 text-blue-600 rounded-md flex items-center justify-center shrink-0"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                </div>
                Formasi Jabatan Tersedia
            </h3>
            <p class="text-sm font-normal text-slate-500 mt-2">Daftarkan posisi/divisi di sisi kiri, dan masukkan spesifik jabatannya di sisi kanan.</p>
        </div>

        <!-- Container List Posisi (Desain Split-Pane Kiri-Kanan) -->
        <div class="space-y-6">
            <template x-for="(group, pIdx) in listGroupPosisi" :key="pIdx">
                <div
                    class="border border-slate-200 rounded-md overflow-hidden bg-white flex flex-col md:flex-row relative group/card hover:border-blue-400 transition-colors"
                >
                    <!-- KIRI: Area Posisi/Divisi (Parent) -->
                    <div
                        class="w-full md:w-5/12 bg-slate-50 p-6 md:p-7 border-b md:border-b-0 md:border-r border-slate-200 flex flex-col justify-between"
                    >
                        <div>
                            <label
                                class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-2.5"
                            >
                                Nama Posisi / Divisi
                            </label>
                            <input
                                type="text"
                                x-model="group.posisi"
                                class="w-full rounded-md text-sm font-bold py-3.5 border border-slate-300 focus:border-blue-600 focus:ring-0 bg-white transition-colors text-slate-900"
                                placeholder="Contoh: Badan Pengurus Harian"
                            />
                        </div>

                        <!-- Tombol Hapus Posisi (Hanya Muncul Jika Lebih Dari 1) -->
                        <div class="mt-6">
                            <button
                                type="button"
                                @click="hapusPosisi(pIdx)"
                                x-show="listGroupPosisi.length > 1"
                                class="text-xs font-bold text-red-500 hover:text-red-700 transition-colors inline-flex items-center gap-1.5"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                Hapus Posisi
                            </button>
                        </div>
                    </div>

                    <!-- KANAN: Area Daftar Jabatan (Children) -->
                    <div class="w-full md:w-7/12 p-6 md:p-7 bg-white">
                        <label
                            class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-4 border-b border-slate-200 pb-3"
                        >
                            Nama Jabatan
                        </label>

                        <div class="space-y-3.5">
                            <template
                                x-for="(jabatan, jIdx) in group.jabatans"
                                :key="jIdx"
                            >
                                <div class="flex items-start gap-3">
                                    <div class="flex-1">
                                        <!-- Hidden Input untuk sinkronisasi Database -->
                                        <input
                                            type="hidden"
                                            name="nama_posisi[]"
                                            :value="group.posisi"
                                        />

                                        <input
                                            type="hidden"
                                            name="jabatan_ids[]"
                                            :value="jabatan.id || ''"
                                        />

                                        <input
                                            type="text"
                                            name="nama_jabatan[]"
                                            x-model="jabatan.nama"
                                            @blur="
                                                validateField(
                                                    'nama_jabatan_' +
                                                        pIdx +
                                                        '_' +
                                                        jIdx,
                                                    jabatan.nama,
                                                    'Nama jabatan',
                                                )
                                            "
                                            :class="errors[
                                                'nama_jabatan_' +
                                                    pIdx +
                                                    '_' +
                                                    jIdx
                                            ]
                                                ? 'border-red-500 bg-red-50 text-red-700 focus:ring-0 focus:border-red-500'
                                                : 'border border-slate-300 focus:border-blue-600 focus:ring-0 bg-white text-slate-900'"
                                            class="w-full rounded-md text-sm font-bold py-3.5 transition-colors"
                                            placeholder="Contoh: Staff Content Creator"
                                            required
                                        />
                                        <p x-show="errors['nama_jabatan_' + pIdx + '_' + jIdx]" x-text="errors['nama_jabatan_' + pIdx + '_' + jIdx]" class="mt-2 text-xs text-red-500 font-bold ml-1" style="display: none"></p>
                                    </div>

                                    <button
                                        type="button"
                                        @click="hapusJabatan(pIdx, jIdx)"
                                        x-show="group.jabatans.length > 1"
                                        class="text-red-500 hover:text-red-700 bg-red-50 hover:bg-red-100 p-3 rounded-md transition-colors shrink-0 mt-0.5"
                                        title="Hapus Jabatan"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </button>
                                </div>
                            </template>
                        </div>

                        <!-- Tombol Tambah Jabatan -->
                        <div class="mt-5">
                            <button
                                type="button"
                                @click="tambahJabatan(pIdx)"
                                class="text-xs font-bold uppercase tracking-wide text-blue-700 bg-blue-50 hover:bg-blue-100 border border-blue-200 px-4 py-2.5 rounded-md transition-colors inline-flex items-center gap-2"
                            >
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                Tambah Jabatan
                            </button>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <!-- Tombol Tambah Posisi -->
        <div class="mt-8 pt-6 border-t border-slate-200 flex justify-start">
            <button
                type="button"
                @click="tambahPosisi()"
                class="text-xs font-bold uppercase tracking-wide text-blue-700 bg-blue-50 hover:bg-blue-600 hover:text-white px-6 py-3 rounded-md border border-blue-200 hover:border-blue-600 transition-colors flex items-center gap-2"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                Tambah Posisi / Divisi
            </button>
        </div>
    </div>

    <!-- Tombol Navigasi Bawah -->
    <div
        class="flex flex-col-reverse sm:flex-row justify-between items-center mt-8 pb-10 gap-4"
    >
        <button
            type="button"
            @click="
                tab = 1;
                window.scrollTo({ top: 0, behavior: 'smooth' });
            "
            class="text-sm font-bold text-slate-500 hover:text-slate-800 transition-colors flex items-center gap-2 px-4 py-2 w-full sm:w-auto justify-center"
        >
            &larr; Kembali
        </button>
        <button
            type="button"
            @click="
                tab = 3;
                window.scrollTo({ top: 0, behavior: 'smooth' });
            "
            class="bg-slate-800 text-white px-8 py-3.5 rounded-md font-bold text-sm hover:bg-blue-600 transition-colors flex items-center gap-2 w-full sm:w-auto justify-center"
        >
            Lanjut Tahapan Seleksi
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
        </button>
    </div>
</div>
