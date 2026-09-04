<x-app-layout>
    <!-- Background Aksen Atas (Mencegah scroll horizontal) -->
    <div
        class="absolute top-0 inset-x-0 h-[400px] overflow-hidden pointer-events-none -z-10"
    >
        <div
            class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PHBhdGggZD0iTTAgMGg0MHY0MEgwVjB6bTIwIDIwaDIwdjIwSDIwaC0yMHptMCAwaC0yMHYtMjBoMjB2MjB6IiBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiIHN0cm9rZT0iI2YxZjVmOSIgc3Ryb2tlLXdpZHRoPSIxIi8+PC9zdmc+')] opacity-60"
        ></div>
        <div
            class="absolute -top-[20%] -left-[10%] w-[40%] h-[60%] rounded-full bg-gradient-to-br from-blue-300/80 to-blue-50/20 blur-[100px]"
        ></div>
        <div
            class="absolute top-[10%] right-[10%] w-[35%] h-[50%] rounded-full bg-gradient-to-bl from-indigo-200/60 to-transparent blur-[120px]"
        ></div>
    </div>

    <!-- MAIN CONTAINER (Padding disesuaikan untuk mobile: px-4 sm:px-8) -->
    <div
        class="py-4 sm:py-8 px-4 sm:px-8 md:px-10 max-w-5xl mx-auto relative z-10 my-6 sm:my-8 sm:mb-10"
    >
        <!-- HEADER -->
        <div class="mb-8 sm:mb-10 relative z-10 text-center sm:text-left">
            <h2
                class="text-3xl sm:text-4xl font-extrabold text-slate-800 tracking-tight leading-tight"
            >
                Buka Rekrutmen
            </h2>
            <p class="text-sm text-slate-500 mt-2 leading-relaxed">Buka rekrutmen untuk mengadakan seleksi anggota baru organisasi.</p>
        </div>

        <!-- KONTEN FORM (Padding form diperkecil di mobile: px-5 sm:px-10) -->
        <form
            action="{{ route('organisasi.buka-rekrutmen.store_inisiasi') }}"
            method="POST"
            class="bg-white px-5 sm:px-10 py-6 sm:py-10 rounded-xl shadow-sm border border-slate-200 space-y-8"
        >
            <!-- BAGIAN 1: PERIODE -->
            <div class="my-0">
                <label
                    class="block text-sm font-bold text-slate-700 mb-1 tracking-wide"
                >
                    Periode Rekrutmen
                </label>
                @php
                    $tahunSekarang = (int) date('Y');
                    $opsiPeriode = [
                        $tahunSekarang - 1 . '/' . $tahunSekarang,
                        $tahunSekarang . '/' . ($tahunSekarang + 1),
                        $tahunSekarang + 1 . '/' . ($tahunSekarang + 2),
                    ];
                @endphp
                <p class="text-xs font-normal text-slate-500 mb-3">Pilih tahun periode rekrutmen yang ingin diadakan.</p>
                <select
                    name="tahun_periode"
                    class="block w-full sm:w-2/3 md:w-1/2 bg-slate-50 border border-slate-300 text-slate-600 text-sm font-bold focus:border-blue-600 focus:ring-0 rounded-lg py-3 px-4 transition-colors"
                    required
                >
                    <option
                        value=""
                        disabled
                        {{
                            old('tahun_periode')
                                ? ''
                                : 'selected'
                        }}
                    >
                        &mdash; Pilih Tahun Periode &mdash;
                    </option>
                    @foreach ($opsiPeriode as $periodeOpsi)
                        <option
                            value="{{ $periodeOpsi }}"
                            {{
                                old('tahun_periode') == $periodeOpsi
                                    ? 'selected'
                                    : ''
                            }}
                        >
                            {{ $periodeOpsi }}
                        </option>
                    @endforeach
                </select>
            </div>

            <hr class="border-slate-100" />

            <!-- BAGIAN 2: PANITIA -->
            @php
                $oldNims = old('nim_panitia', []);
                $panitiaData = [];
                if (!empty($oldNims)) {
                    foreach ($oldNims as $nim) {
                        $panitiaData[] = ['nim' => $nim, 'pesanError' => ''];
                    }
                } else {
                    $panitiaData[] = ['nim' => '', 'pesanError' => ''];
                }
            @endphp

            <script>
                window.dataPanitiaAwal = {!!
                    json_encode(
                        $panitiaData,
                    )
                !!};
            </script>

            <div
                x-data="{
                    panitia: window.dataPanitiaAwal,
                    validasiNim(index) {
                        const nilai = this.panitia[index].nim;
                        const regexNIM = /^\d{9}$/;
                        if (nilai === '') {
                            this.panitia[index].pesanError = 'NIM wajib diisi.';
                        } else if (!regexNIM.test(nilai)) {
                            this.panitia[index].pesanError =
                                'NIM harus berupa 9 digit angka.';
                        } else {
                            this.panitia[index].pesanError = '';
                        }
                    },
                    tambahBaris() {
                        this.panitia.push({ nim: '', pesanError: '' });
                    },
                    hapusBaris(index) {
                        this.panitia.splice(index, 1);
                    },
                }"
            >
                <div class="mb-4">
                    <label
                        class="block text-sm font-bold text-slate-700 mb-1 tracking-wide"
                    >
                        Panitia Rekrutmen yang Bertugas
                    </label>
                    <p class="text-xs font-normal text-slate-500">Masukkan NIM mahasiswa yang akan bertugas sebagai panitia.</p>
                </div>

                <!-- PERUBAHAN UTAMA: MENGGANTI TABLE MENJADI FLEX/GRID -->
                <div
                    class="overflow-hidden border border-slate-200 rounded-lg bg-white divide-y divide-slate-100"
                >
                    <!-- Header (Hanya tampil di Desktop/Tablet) -->
                    <div
                        class="hidden sm:flex bg-slate-50 border-b border-slate-200 text-xs font-bold text-slate-600 tracking-wide"
                    >
                        <div class="flex-1 px-5 py-3">NIM Mahasiswa</div>
                        <div class="w-32 px-5 py-3 text-center">Aksi</div>
                    </div>

                    <!-- Body List Panitia -->
                    <template x-for="(baris, index) in panitia" :key="index">
                        <div
                            class="flex flex-col sm:flex-row sm:items-start gap-3 sm:gap-0 px-4 sm:px-0 py-4 hover:bg-slate-50/50 transition-colors"
                        >
                            <!-- Area Input NIM -->
                            <div class="flex-1 sm:px-5">
                                <label
                                    class="sm:hidden block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5"
                                    >NIM Mahasiswa</label
                                >

                                <div class="flex items-stretch w-full">
                                    <input
                                        type="text"
                                        name="nim_panitia[]"
                                        x-model="baris.nim"
                                        @blur="validasiNim(index)"
                                        @input="
                                            baris.nim = baris.nim
                                                .replace(/[^0-9]/g, '')
                                                .slice(0, 9);
                                            baris.pesanError = '';
                                        "
                                        placeholder="Contoh: 222212602"
                                        class="flex-1 min-w-0 border border-slate-300 focus:border-blue-600 focus:ring-0 rounded-l-md transition-colors text-sm font-bold py-2.5 px-3 sm:px-4"
                                        :class="baris.pesanError
                                            ? 'border-red-500 bg-red-50 text-red-700'
                                            : 'bg-white text-slate-900'"
                                        required
                                    />

                                    <span
                                        class="inline-flex shrink-0 items-center px-3 sm:px-4 rounded-r-md border border-l-0 border-slate-300 bg-slate-100 text-slate-600 text-xs sm:text-sm font-bold"
                                    >
                                        @stis
                                        .ac.id
                                    </span>
                                </div>

                                <p
                                    x-show="baris.pesanError"
                                    x-text="baris.pesanError"
                                    class="text-red-500 text-[11px] sm:text-xs mt-1.5 font-bold"
                                    style="display: none"
                                ></p>
                            </div>

                            <!-- Area Tombol Hapus (Full width di mobile, fix width di desktop) -->
                            <div
                                class="sm:w-32 sm:px-5 flex justify-end sm:justify-center mt-1 sm:mt-0 pt-1 sm:pt-0"
                            >
                                <button
                                    type="button"
                                    @click="hapusBaris(index)"
                                    x-show="panitia.length > 1"
                                    class="w-full sm:w-auto text-xs font-bold tracking-wide text-red-700 bg-red-50 hover:bg-red-100 px-4 py-2.5 rounded-md border border-red-200 transition-colors flex justify-center items-center gap-1.5"
                                >
                                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    Hapus
                                </button>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Tombol Tambah Baris -->
                <div class="mt-4">
                    <button
                        type="button"
                        @click="tambahBaris()"
                        class="w-full sm:w-auto text-xs font-bold tracking-wide text-blue-700 bg-blue-50 hover:bg-blue-100 px-5 py-3 rounded-md border border-blue-200 transition-colors flex justify-center items-center gap-2 shadow-sm"
                    >
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                        Tambah Panitia
                    </button>
                </div>
            </div>

            <hr class="border-slate-100" />

            <!-- Area Submit (w-full di mobile agar mudah ditekan) -->
            <div class="pt-2">
                <button
                    type="submit"
                    class="w-full sm:w-auto sm:ml-auto bg-blue-600 text-white px-6 py-3.5 rounded-lg font-bold text-sm hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2 shadow-md hover:shadow-lg"
                >
                    Buka Rekrutmen
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </button>
            </div>
        </form>
    </div>
</x-app-layout>

<!-- (Script SweetAlert biarkan persis seperti yang Anda buat sebelumnya) -->
