<div
    x-show="tab === 1"
    x-transition:enter="transition ease-out duration-300 transform"
    x-transition:enter-start="opacity-0 translate-y-4"
    class="space-y-6"
>
    <!-- Main Card diselaraskan dengan DNA Card putih border-slate-200 -->

    <div class="bg-white rounded-xl border shadow-sm border-slate-200">
        <!-- Header Section -->
        <div
            class="p-8 md:p-10 border-b flex rounded-t-xl border-slate-100 bg-slate-50 justify-between items-center"
        >
            <div>
                <h3
                    class="text-xl font-extrabold text-slate-800 tracking-tight flex items-center gap-3"
                >
                    <div
                        class="w-10 h-10 bg-blue-100 text-blue-600 rounded-md flex items-center justify-center shrink-0"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    </div>
                    Pilih Formasi
                </h3>
                <p class="text-sm font-normal text-slate-500 mt-2">Tentukan pilihan posisi dan jabatan yanng sesuai dengan anda</p>
            </div>
        </div>

        @if (empty($groupedJabatan))
            <!-- Error Box diselaraskan dengan DNA Alert merah -->
            <div class="flex flex-col px-5 sm:px-10 py-6 sm:py-10 space-y-3">
                <svg class="w-5 h-5 text-red-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                <p class="text-sm font-bold text-red-800 leading-relaxed">Mohon maaf, formasi pilihan jabatan belum ditentukan oleh panitia pelaksana.</p>
            </div>
        @else
            <div class="flex flex-col px-5 sm:px-10 py-6 sm:py-10 space-y-8">
                <!-- PILIHAN 1 (WAJIB) -->
                <div class="" data-field-key="jabatan_1_id">
                    <h3
                        class="block text-sm font-bold text-slate-700 mb-2 tracking-wide"
                    >
                        Pilihan Utama<span class="text-red-500 ml-1">*</span>
                    </h3>

                    <div
                        class="border border-slate-200 rounded-xl overflow-hidden flex flex-col md:flex-row bg-slate-50 items-stretch"
                    >
                        <!-- Panel Kiri (Divisi) -->
                        <div
                            class="w-full md:w-1/2 bg-white border-b md:border-b-0 md:border-r border-slate-200 px-5 py-3"
                            style="max-height: 320px; overflow-y: auto"
                        >
                            <p class="text-[13px] font-bold text-slate-400 tracking-wide mb-1">Daftar Posisi</p>
                            <div class="space-y-2">
                                @foreach ($groupedJabatan as $namaPosisi => $jabatansGroup)
                                    <button
                                        type="button"
                                        @click="activePosisi1 = '{{ $namaPosisi }}'"
                                        :class="activePosisi1 === '{{ $namaPosisi }}' ? 'bg-blue-600 text-white shadow-sm border border-blue-600' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-200'"
                                        class="w-full text-left px-4 py-3 rounded-lg text-xs font-bold transition-all flex justify-between items-center"
                                    >
                                        {{ $namaPosisi }}
                                        <span
                                            x-show="activePosisi1 === '{{ $namaPosisi }}'"
                                            class="shrink-0"
                                        >
                                            <!-- Icon panah kanan yang lebih terlihat -->
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                            </svg>
                                        </span>
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        <!-- Panel Kanan (Jabatan) -->
                        <div
                            class="w-full md:w-1/2 px-5 py-3 bg-slate-50"
                            style="max-height: 320px; overflow-y: auto"
                        >
                            <input
                                type="hidden"
                                name="jabatan_1_id"
                                x-model="pilihan1"
                                required
                            />
                            <div
                                x-show="!activePosisi1"
                                class="h-full flex items-center justify-center text-center p-4"
                            >
                                <p class="text-xs font-bold text-slate-400">Pilih Posisi / Divisi terlebih dahulu.</p>
                            </div>

                            @foreach ($groupedJabatan as $namaPosisi => $jabatansGroup)
                                <div
                                    x-show="activePosisi1 === '{{ $namaPosisi }}'"
                                    style="display: none"
                                    class="space-y-2"
                                >
                                    <p class="text-[13px] font-bold text-slate-400 tracking-wide -mb-1">Daftar Jabatan</p>
                                    @foreach ($jabatansGroup as $jabatan)
                                        <label
                                            class="flex items-center px-4 py-3 bg-white border rounded-lg cursor-pointer transition-colors group"
                                            :class="pilihan1 === '{{ $jabatan['id'] }}' ? 'border-blue-600 bg-blue-50' : 'border-slate-200 hover:border-blue-400'"
                                        >
                                            <div
                                                class="flex items-center justify-between w-full gap-3"
                                            >
                                                <div
                                                    class="flex items-center gap-2"
                                                >
                                                    <!-- Radio Button Bulat -->
                                                    <div
                                                        class="w-4 h-4 rounded-full border flex items-center justify-center shrink-0 bg-white transition-colors"
                                                        :class="pilihan1 === '{{ $jabatan['id'] }}' ? 'border-blue-600' : 'border-slate-300 group-hover:border-blue-400'"
                                                    >
                                                        <div
                                                            class="w-2 h-2 rounded-full bg-blue-600"
                                                            x-show="pilihan1 === '{{ $jabatan['id'] }}'"
                                                        ></div>
                                                    </div>
                                                    <span
                                                        class="text-xs font-bold transition-colors"
                                                        :class="pilihan1 === '{{ $jabatan['id'] }}' ? 'text-blue-800' : 'text-slate-700 group-hover:text-blue-600'"
                                                    >
                                                        {{
                                                            $jabatan[
                                                                'nama_jabatan'
                                                            ]
                                                        }}
                                                    </span>
                                                </div>
                                            </div>
                                            <input
                                                type="radio"
                                                name="radio_pilihan1"
                                                value="{{ $jabatan['id'] }}"
                                                data-name="{{ $jabatan['nama_jabatan'] }}"
                                                data-position="{{ $namaPosisi }}"
                                                @click="pilihan1 = '{{ $jabatan['id'] }}'; if(pilihan2 === '{{ $jabatan['id'] }}') pilihan2 = ''; updatePilihan1Name();"
                                                class="hidden"
                                            />
                                        </label>
                                    @endforeach
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <!-- PILIHAN 2 (OPSIONAL) -->
                <div class="mb-6">
                    <h3
                        class="text-sm font-extrabold text-slate-700 tracking-wide mb-2 flex items-center justify-between"
                    >
                        <span class="flex items-center gap-1">
                            Pilihan Alternatif
                            <span class="text-xs font-bold text-slate-400"
                                >(Opsional)</span
                            >
                        </span>
                        <button
                            type="button"
                            @click="
                                pilihan2 = '';
                                activePosisi2 = '';
                            "
                            x-show="pilihan2"
                            style="display: none"
                            class="text-xs font-extrabold border border-red-300 hover:border-red-400 text-red-500 hover:text-red-700 bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-md transition-colors"
                        >
                            Batalkan Pilihan
                        </button>
                    </h3>

                    <div
                        class="border border-slate-200 rounded-xl overflow-hidden flex flex-col md:flex-row bg-slate-50 items-stretch"
                    >
                        <!-- Panel Kiri (Divisi Alternatif) -->
                        <div
                            class="w-full md:w-1/2 bg-white border-b md:border-b-0 md:border-r border-slate-200 px-5 py-3"
                            style="max-height: 320px; overflow-y: auto"
                        >
                            <p class="text-[13px] font-bold text-slate-400 tracking-wide mb-1">Daftar Posisi / Divisi</p>
                            <div class="space-y-2">
                                @foreach ($groupedJabatan as $namaPosisi => $jabatansGroup)
                                    <button
                                        type="button"
                                        @click="activePosisi2 = '{{ $namaPosisi }}'"
                                        :class="activePosisi2 === '{{ $namaPosisi }}' ? 'bg-blue-600 text-white shadow-sm border border-blue-600' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-200'"
                                        class="w-full text-left px-4 py-3 rounded-lg text-xs font-bold transition-all flex justify-between items-center"
                                    >
                                        {{ $namaPosisi }}
                                        <span
                                            x-show="activePosisi2 === '{{ $namaPosisi }}'"
                                            class="shrink-0"
                                        >
                                            <!-- Icon panah kanan yang lebih terlihat -->
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                            </svg>
                                        </span>
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        <!-- Panel Kanan (Jabatan Alternatif) -->
                        <div
                            class="w-full md:w-1/2 px-5 py-3 bg-slate-50"
                            style="max-height: 320px; overflow-y: auto"
                        >
                            <input
                                type="hidden"
                                name="jabatan_2_id"
                                x-model="pilihan2"
                            />
                            <div
                                x-show="!activePosisi2"
                                class="h-full flex items-center justify-center text-center p-4"
                            >
                                <p class="text-xs font-bold text-slate-400">Pilih Posisi / Divisi terlebih dahulu.</p>
                            </div>

                            @foreach ($groupedJabatan as $namaPosisi => $jabatansGroup)
                                <div
                                    x-show="activePosisi2 === '{{ $namaPosisi }}'"
                                    style="display: none"
                                    class="space-y-2"
                                >
                                    <p class="text-[13px] font-bold text-slate-400 tracking-wide -mb-1">Daftar Jabatan</p>
                                    @foreach ($jabatansGroup as $jabatan)
                                        <label
                                            class="flex items-center px-4 py-3 bg-white border rounded-lg cursor-pointer transition-colors group"
                                            :class="
                                pilihan1 === '{{ $jabatan['id'] }}'
                                    ? 'border-slate-100 bg-slate-50 cursor-not-allowed opacity-50'
                                    : (pilihan2 === '{{ $jabatan['id'] }}'
                                        ? 'border-blue-600 bg-blue-50'
                                        : 'border-slate-200 hover:border-blue-400')
                            "
                                        >
                                            <div
                                                class="flex items-center justify-between w-full gap-3"
                                            >
                                                <div
                                                    class="flex items-center gap-2"
                                                >
                                                    <!-- Radio Button Bulat -->
                                                    <div
                                                        class="w-4 h-4 rounded-full border flex items-center justify-center shrink-0 bg-white transition-colors"
                                                        :class="
                                            pilihan1 === '{{ $jabatan['id'] }}'
                                                ? 'border-slate-300'
                                                : (pilihan2 === '{{ $jabatan['id'] }}'
                                                    ? 'border-blue-600'
                                                    : 'border-slate-300 group-hover:border-blue-400')
                                        "
                                                    >
                                                        <div
                                                            class="w-2 h-2 rounded-full bg-blue-600"
                                                            x-show="pilihan2 === '{{ $jabatan['id'] }}'"
                                                        ></div>
                                                    </div>
                                                    <span
                                                        class="text-xs font-bold transition-colors"
                                                        :class="
                                            pilihan1 === '{{ $jabatan['id'] }}'
                                                ? 'text-slate-400'
                                                : (pilihan2 === '{{ $jabatan['id'] }}'
                                                    ? 'text-blue-800'
                                                    : 'text-slate-700 group-hover:text-blue-600')
                                        "
                                                    >
                                                        {{
                                                            $jabatan[
                                                                'nama_jabatan'
                                                            ]
                                                        }}
                                                    </span>
                                                </div>
                                                <span
                                                    x-show="pilihan1 === '{{ $jabatan['id'] }}'"
                                                    class="text-[9px] font-extrabold border border-red-300 hover:border-red-400 text-red-600 hover:text-red-700 bg-red-50 hover:bg-red-100 px-3 py-0.5 rounded-md transition-colors"
                                                >
                                                    Terpilih Utama
                                                </span>
                                            </div>
                                            <input
                                                type="radio"
                                                name="radio_pilihan2"
                                                value="{{ $jabatan['id'] }}"
                                                @click="if(pilihan1 !== '{{ $jabatan['id'] }}') pilihan2 = '{{ $jabatan['id'] }}'"
                                                :disabled="pilihan1 === '{{ $jabatan['id'] }}'"
                                                class="hidden"
                                            />
                                        </label>
                                    @endforeach
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <hr class="border-slate-100" />

                <!-- Area Aksi Tombol -->
                <div class="flex justify-end">
                    <button
                        type="button"
                        @click="lanjutKeTugas()"
                        class="w-full sm:w-auto bg-blue-600 text-white px-8 py-3.5 rounded-lg font-bold text-sm hover:bg-blue-700 transition-colors flex items-center justify-center gap-2 shadow-md hover:shadow-lg"
                    >
                        Lanjut Formulir Daftar
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </button>
                </div>
            </div>
        @endif
    </div>
</div>
