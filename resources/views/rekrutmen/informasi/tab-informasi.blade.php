<!-- LANGKAH 1: IDENTITAS REKRUTMEN -->
<div
    x-show="tab === 1"
    x-transition:enter="transition ease-out duration-300 transform"
    x-transition:enter-start="opacity-0 translate-y-4"
    class="space-y-8"
>
    <div class="bg-white rounded-xl border shadow-sm border-slate-200">
        <!-- Header Section -->
        <div
            class="p-8 md:p-10 border-b rounded-t-xl border-slate-100 bg-slate-50"
        >
            <h3
                class="text-xl font-extrabold text-slate-800 tracking-tight flex items-center gap-3"
            >
                <div
                    class="w-10 h-10 bg-blue-100 text-blue-600 rounded-md flex items-center justify-center shrink-0"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                </div>
                Informasi Rekrutmen
            </h3>
            <p class="text-sm font-normal text-slate-500 mt-2">Informasi rekrutmen yang akan ditampilkan pada pada halaman pendaftaran.</p>
        </div>

        <div class="flex flex-col px-5 sm:px-10 py-6 sm:py-10 space-y-8">
            <!-- BAGIAN 1: PERIODE -->
            <div class="">
                <label
                    class="block text-sm font-bold text-slate-700 mb-1 tracking-wide"
                >
                    Headline Rekrutmen<span class="text-red-500 ml-0.5">*</span>
                </label>
                <p
                    x-show="errors['slogan']"
                    x-text="errors['slogan']"
                    class="mt-2 text-xs text-red-500 font-bold"
                    style="display: none"
                ></p>
                <input
                    type="text"
                    name="slogan"
                    value="{{ old('slogan', $periode->slogan) }}"
                    @blur="validateField('slogan', $el.value, 'Slogan')"
                    :class="errors['slogan']
                        ? 'border-red-500 bg-red-50 text-red-700 focus:ring-0 focus:border-red-500'
                        : 'border-slate-300 focus:border-blue-600 focus:ring-0 bg-white text-slate-800'"
                    class="w-full border border-slate-300 focus:border-blue-600 focus:ring-0 rounded-md transition-colors text-sm font-bold py-2.5 px-3 sm:px-4"
                    placeholder="Contoh: Daftarkan Dirimu Menjadi Bagian dari Anggota Organisasi Sekarang!"
                    required
                />
            </div>

            <!-- Deskripsi Input -->
            <div>
                <label
                    class="block text-sm font-bold text-slate-700 mb-1 tracking-wide"
                >
                    Deskripsi Ringkas Rekrutmen<span class="text-red-500 ml-0.5"
                        >*</span
                    >
                </label>
                <textarea
                    name="deskripsi_rekrutmen"
                    rows="3"
                    @blur="
                        validateField(
                            'deskripsi_rekrutmen',
                            $el.value,
                            'Deskripsi',
                        )
                    "
                    :class="errors['deskripsi_rekrutmen']
                        ? 'border-red-500 bg-red-50 text-red-700 focus:ring-0 focus:border-red-500'
                        : 'border-slate-300 focus:border-blue-600 focus:ring-0 bg-white text-slate-800'"
                    class="w-full border border-slate-300 focus:border-blue-600 focus:ring-0 rounded-md transition-colors text-sm font-bold py-2.5 px-3 sm:px-4"
                    placeholder="Contoh: Organisasi adalah tempat untuk mengembangkan berbagai soft skill terbaik"
                    required
                    >{{
                        old(
                            'deskripsi_rekrutmen',
                            $periode->deskripsi,
                        )
                    }}</textarea
                >
                <p
                    x-show="errors['deskripsi_rekrutmen']"
                    x-text="errors['deskripsi_rekrutmen']"
                    class="mt-2 text-xs text-red-500 font-bold"
                    style="display: none"
                ></p>
            </div>
            <hr class="border-slate-100" />
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 pt-2">
                <!-- Banner Upload -->
                <div class="bg-slate-50 p-6 rounded-lg border border-slate-200">
                    <label
                        class="text-sm font-bold text-slate-700 mb-1 tracking-wide flex items-center gap-2"
                    >
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        Banner<span class="text-red-500 -m-1">*</span>
                    </label>
                    <div class="relative group cursor-pointer">
                        <input
                            type="file"
                            name="banner"
                            id="banner_upload"
                            accept=".jpg,.jpeg,.png,image/jpeg,image/png"
                            @change="
                                validateFile(
                                    'banner',
                                    $el.files,
                                    2,
                                    'Banner rekrutmen',
                                    'jpg,jpeg,png',
                                );
                                bannerFileName =
                                    $el.files.length > 0
                                        ? $el.files[0].name
                                        : '';
                            "
                            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                        />

                        <div
                            :class="errors['banner']
                                ? 'border-red-500 bg-red-50'
                                : bannerFileName
                                  ? 'border-emerald-500 bg-emerald-50'
                                  : 'border-slate-300 bg-white group-hover:border-blue-500 group-hover:bg-blue-50'"
                            class="border-2 border-dashed rounded-md p-5 flex items-center justify-center transition-colors"
                        >
                            <div class="text-center w-full">
                                <!-- Kondisi Belum Ada File -->
                                <template x-if="!bannerFileName">
                                    <div>
                                        <span
                                            class="text-sm font-bold text-blue-600 group-hover:text-blue-700"
                                            >Klik atau Seret Gambar</span
                                        >
                                        <p class="mt-1 text-xs text-slate-500">Maks 2 MB (JPG/PNG)</p>
                                    </div>
                                </template>

                                <!-- Kondisi Sudah Ada File -->
                                <template x-if="bannerFileName">
                                    <div
                                        class="flex flex-col items-center justify-center"
                                    >
                                        <svg class="w-6 h-6 text-emerald-600 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        <span
                                            class="text-sm font-bold text-emerald-800 truncate max-w-full px-2"
                                            x-text="bannerFileName"
                                        ></span>
                                        <p class="mt-1 text-xs text-emerald-600">Siap Diunggah</p>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                    <p
                        x-show="errors['banner']"
                        x-text="errors['banner']"
                        class="mt-2 text-xs text-red-500 font-bold"
                        style="display: none"
                    ></p>

                    @if ($periode->lampiran_banner)
                        @php
                            $rawBanner = is_array($periode->lampiran_banner)
                                ? $periode->lampiran_banner[0] ?? ''
                                : $periode->lampiran_banner;
                            $bannerPath = str_replace(['[', ']', '"', '\\', ' '], '', $rawBanner);
                        @endphp
                        <div
                            class="mt-4 flex items-center justify-between bg-blue-50 border border-blue-200 p-3 rounded-md"
                        >
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span class="text-xs font-bold text-blue-800"
                                    >Data Sebelumnya Tersimpan</span
                                >
                            </div>
                            <a
                                href="{{ asset('storage/' . $bannerPath) }}"
                                target="_blank"
                                class="text-xs font-bold text-blue-700 hover:text-white hover:bg-blue-600 bg-white border border-blue-300 px-3 py-1.5 rounded-md transition-colors"
                            >
                                Lihat
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Buku Pedoman Upload -->
                <div class="bg-slate-50 p-6 rounded-lg border border-slate-200">
                    <label
                        class="text-sm font-bold text-slate-700 mb-1 tracking-wide flex items-center gap-2"
                    >
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                        Buku Pedoman<span class="text-red-500 -ml-1">*</span>
                    </label>
                    <div class="relative group cursor-pointer">
                        <input
                            type="file"
                            name="buku_pedoman"
                            id="pedoman_upload"
                            accept=".pdf"
                            @change="
                                validateFile(
                                    'buku_pedoman',
                                    $el.files,
                                    5,
                                    'Buku pedoman',
                                );
                                pedomanFileName =
                                    $el.files.length > 0
                                        ? $el.files[0].name
                                        : '';
                            "
                            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                        />

                        <div
                            :class="errors['buku_pedoman']
                                ? 'border-red-500 bg-red-50'
                                : pedomanFileName
                                  ? 'border-emerald-500 bg-emerald-50'
                                  : 'border-slate-300 bg-white group-hover:border-blue-500 group-hover:bg-blue-50'"
                            class="border-2 border-dashed rounded-md p-5 flex items-center justify-center transition-colors"
                        >
                            <div class="text-center w-full">
                                <!-- Kondisi Belum Ada File -->
                                <template x-if="!pedomanFileName">
                                    <div>
                                        <span
                                            class="text-sm font-bold text-blue-600 group-hover:text-blue-700"
                                            >Klik atau Seret PDF</span
                                        >
                                        <p class="mt-1 text-xs text-slate-500">Maks 5 MB (PDF)</p>
                                    </div>
                                </template>

                                <!-- Kondisi Sudah Ada File -->
                                <template x-if="pedomanFileName">
                                    <div
                                        class="flex flex-col items-center justify-center"
                                    >
                                        <svg class="w-6 h-6 text-emerald-600 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        <span
                                            class="text-sm font-bold text-emerald-800 truncate max-w-full px-2"
                                            x-text="pedomanFileName"
                                        ></span>
                                        <p class="mt-1 text-xs text-emerald-600">Siap Diunggah</p>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                    <p
                        x-show="errors['buku_pedoman']"
                        x-text="errors['buku_pedoman']"
                        class="mt-2 text-xs text-red-500 font-bold"
                        style="display: none"
                    ></p>

                    @if ($periode->lampiran_pedoman)
                        @php
                            $rawPedoman = is_array($periode->lampiran_pedoman)
                                ? $periode->lampiran_pedoman[0] ?? ''
                                : $periode->lampiran_pedoman;
                            $pedomanPath = str_replace(['[', ']', '"', '\\', ' '], '', $rawPedoman);
                        @endphp
                        <div
                            class="mt-4 flex items-center justify-between bg-blue-50 border border-blue-200 p-3 rounded-md"
                        >
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span class="text-xs font-bold text-blue-800"
                                    >Data Sebelumnya Tersimpan</span
                                >
                            </div>
                            <a
                                href="{{ asset('storage/' . $pedomanPath) }}"
                                target="_blank"
                                class="text-xs font-bold text-blue-700 hover:text-white hover:bg-blue-600 bg-white border border-blue-300 px-3 py-1.5 rounded-md transition-colors"
                            >
                                Lihat
                            </a>
                        </div>
                    @endif
                </div>
            </div>
            <hr class="border-slate-100" />
            <div
                class="flex flex-col-reverse sm:flex-row justify-end items-center gap-4"
            >
                <button
                    type="button"
                    @click="
                        tab = 2;
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    "
                    class="w-full sm:w-auto sm:ml-auto bg-blue-600 text-white px-6 py-3.5 rounded-lg font-bold text-sm hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2 shadow-md hover:shadow-lg"
                >
                    Lanjut Formasi Jabatan
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </button>
            </div>
        </div>
    </div>
</div>
