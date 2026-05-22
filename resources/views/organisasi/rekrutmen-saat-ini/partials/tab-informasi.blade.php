<div x-show="tab === 1" x-transition.opacity class="space-y-6">
    <div
        class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm space-y-4"
    >
        <h3
            class="text-lg font-bold text-gray-800 border-b border-gray-100 pb-2"
        >
            1. Informasi Umum & Branding
        </h3>

        <div class="grid grid-cols-1 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1"
                    >Slogan (Judul Utama)</label
                >
                <input
                    type="text"
                    name="slogan"
                    value="{{ old('slogan', $periode->slogan) }}"
                    @blur="validateField('slogan', $el.value, 'Slogan')"
                    :class="errors['slogan']
                        ? 'border-red-500 focus:border-red-500 focus:ring-red-500'
                        : 'border-gray-300 focus:border-blue-500 focus:ring-blue-500'"
                    class="w-full rounded-md shadow-sm"
                    placeholder="Contoh: Terpilih untuk Mengabdi"
                    required
                />
                <p
                    x-show="errors['slogan']"
                    x-text="errors['slogan']"
                    class="mt-1 text-xs text-red-600 font-medium"
                ></p>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1"
                    >Deskripsi (Subjudul)</label
                >
                <input
                    type="text"
                    name="deskripsi_rekrutmen"
                    value="{{ old('deskripsi_rekrutmen', $periode->deskripsi) }}"
                    @blur="
                        validateField(
                            'deskripsi_rekrutmen',
                            $el.value,
                            'Deskripsi',
                        )
                    "
                    :class="errors['deskripsi_rekrutmen']
                        ? 'border-red-500 focus:border-red-500 focus:ring-red-500'
                        : 'border-gray-300 focus:border-blue-500 focus:ring-blue-500'"
                    class="w-full rounded-md shadow-sm"
                    placeholder="Contoh: Bergabunglah bersama divisi kreatif BEM"
                    required
                />
                <p
                    x-show="errors['deskripsi_rekrutmen']"
                    x-text="errors['deskripsi_rekrutmen']"
                    class="mt-1 text-xs text-red-600 font-medium"
                ></p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700"
                    >Banner Rekrutmen (Gambar)</label
                >
                <input
                    type="file"
                    name="banner"
                    accept="image/*"
                    @change="
                        validateFile('banner', $el.files, 2, 'Banner rekrutmen')
                    "
                    :class="errors['banner']
                        ? 'border-red-500 focus:border-red-500 focus:ring-red-500'
                        : 'border-gray-300'"
                    class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 border rounded-md"
                />
                <p class="mt-1 text-[11px] text-gray-400">*Format: Gambar (JPG/PNG), Maksimal 2 MB</p>
                <p
                    x-show="errors['banner']"
                    x-text="errors['banner']"
                    class="mt-1 text-xs text-red-600 font-medium"
                ></p>

                @if ($periode->lampiran_banner)
                    @php
                        $rawBanner = is_array($periode->lampiran_banner)
                            ? $periode->lampiran_banner[0] ?? ''
                            : $periode->lampiran_banner;
                        $bannerPath = str_replace(['[', ']', '"', '\\', ' '], '', $rawBanner);
                    @endphp
                    <div
                        class="mt-2 flex items-center space-x-2 text-xs text-green-600"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span>Dokumen terunggah: </span>
                        <a
                            href="{{ asset('storage/' . $bannerPath) }}"
                            target="_blank"
                            class="underline font-semibold hover:text-green-700"
                            >Buka Dokumen</a
                        >
                    </div>
                @endif
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700"
                    >Buku Pedoman Rekrutmen (PDF)</label
                >
                <input
                    type="file"
                    name="buku_pedoman"
                    accept=".pdf"
                    @change="
                        validateFile(
                            'buku_pedoman',
                            $el.files,
                            5,
                            'Buku pedoman',
                        )
                    "
                    :class="errors['buku_pedoman']
                        ? 'border-red-500 focus:border-red-500 focus:ring-red-500'
                        : 'border-gray-300'"
                    class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 border rounded-md"
                />
                <p class="mt-1 text-[11px] text-gray-400">*Format: PDF, Maksimal 5 MB</p>
                <p
                    x-show="errors['buku_pedoman']"
                    x-text="errors['buku_pedoman']"
                    class="mt-1 text-xs text-red-600 font-medium"
                ></p>
                @if ($periode->lampiran_pedoman)
                    @php
                        $rawPedoman = is_array($periode->lampiran_pedoman)
                            ? $periode->lampiran_pedoman[0] ?? ''
                            : $periode->lampiran_pedoman;
                        $pedomanPath = str_replace(['[', ']', '"', '\\', ' '], '', $rawPedoman);
                    @endphp
                    <div
                        class="mt-2 flex items-center space-x-2 text-xs text-green-600"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span>Dokumen Terunggah: </span>
                        <a
                            href="{{ asset('storage/' . $pedomanPath) }}"
                            target="_blank"
                            class="underline font-semibold hover:text-green-700"
                            >Buka Dokumen</a
                        >
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm">
        <div
            class="flex items-center justify-between mb-4 border-b border-gray-100 pb-2"
        >
            <h3 class="text-lg font-bold text-gray-800">
                2. Form Jabatan yang Dibuka
            </h3>
            <button
                type="button"
                @click="tambahJabatan()"
                class="text-sm font-semibold text-blue-600 hover:text-blue-800"
            >
                + Tambah Jabatan
            </button>
        </div>
        <div class="overflow-y-auto max-h-72 border border-gray-100 rounded-md">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr
                        class="bg-gray-50 border-b border-gray-200 sticky top-0"
                    >
                        <th
                            class="px-4 py-2 text-sm font-semibold text-gray-600"
                        >
                            Nama Jabatan / Divisi
                        </th>
                        <th
                            class="px-4 py-2 text-sm font-semibold text-gray-600 w-24 text-center"
                        >
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <template
                        x-for="(jabatan, index) in listJabatan"
                        :key="index"
                    >
                        <tr class="border-b border-gray-100">
                            <td class="px-2 py-3">
                                <input
                                    type="text"
                                    name="nama_jabatan[]"
                                    x-model="jabatan.nama"
                                    @blur="
                                        validateField(
                                            'nama_jabatan_' + index,
                                            jabatan.nama,
                                            'Nama jabatan',
                                        )
                                    "
                                    :class="errors['nama_jabatan_' + index]
                                        ? 'border-red-500 focus:border-red-500 focus:ring-red-500'
                                        : 'border-gray-300 focus:border-blue-500 focus:ring-blue-500'"
                                    class="w-full rounded-md shadow-sm text-sm"
                                    placeholder="Contoh: Anggota Seksi Acara"
                                    required
                                />
                                <p
                                    x-show="errors['nama_jabatan_' + index]"
                                    x-text="errors['nama_jabatan_' + index]"
                                    class="mt-1 text-xs text-red-600 font-medium"
                                ></p>
                            </td>
                            <td class="px-2 py-3 text-center w-24">
                                <button
                                    type="button"
                                    @click="hapusJabatan(index)"
                                    x-show="listJabatan.length > 1"
                                    class="text-red-500 hover:text-red-700 text-sm font-medium"
                                >
                                    Hapus
                                </button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    <div class="flex justify-end pt-2">
        <button
            type="button"
            @click="tab = 2"
            class="bg-gray-800 text-white px-6 py-2 rounded-md font-semibold text-sm hover:bg-gray-900 transition-colors shadow-sm flex items-center gap-1"
        >
            Lanjut ke Tahapan Seleksi
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
        </button>
    </div>
</div>
