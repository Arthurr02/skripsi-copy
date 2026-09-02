<div
    x-show="tab === 1"
    x-transition:enter="transition ease-out duration-300 transform"
    x-transition:enter-start="opacity-0 translate-y-4"
    class="space-y-6"
>
    <div
        class="bg-white rounded-lg border border-slate-200 p-6 md:p-8 shadow-sm"
    >
        <div
            class="mb-8 border-b border-slate-200 pb-5 flex flex-col md:flex-row md:items-end justify-between gap-5"
        >
            <div class="flex-1">
                <h2
                    class="text-xl font-extrabold text-slate-800 mb-2 flex items-center gap-2"
                >
                    <div class="w-1.5 h-3.5 bg-blue-600 rounded-full"></div>
                    Tentukan Formasi Pilihanmu
                </h2>
                <p class="text-sm font-medium text-slate-500">{{ $deskripsiUmum }}</p>
            </div>
            <div
                class="bg-slate-50 border border-slate-200 p-4 rounded-lg shrink-0 text-left md:text-right shadow-sm w-full md:w-auto"
            >
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">Tenggat Waktu</p>
                <p class="text-sm font-extrabold text-slate-800 flex items-center md:justify-end gap-1.5">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    @if ($isWaktuTunggal && $rawMulai)
                        {{
                            $rawMulai->format(
                                'H:i | ',
                            )
                        }}
                        {{
                            $rawMulai->translatedFormat(
                                'd M Y',
                            )
                        }}
                    @elseif ($rawMulai && $rawBerakhir)
                        {{
                            $rawBerakhir->format(
                                'H:i | ',
                            )
                        }}
                        {{
                            $rawBerakhir->translatedFormat(
                                'd M Y',
                            )
                        }}
                    @else
                        Tanpa Batas Waktu
                    @endif
                </p>
            </div>
        </div>

        @if (empty($groupedJabatan))
            <div
                class="p-4 bg-red-50 text-red-700 rounded-md border border-red-200 font-bold text-center text-sm shadow-sm"
            >
                Mohon maaf, formasi pilihan jabatan belum ditentukan oleh
                panitia pelaksana.
            </div>
        @else
            <!-- PILIHAN 1 (WAJIB) -->
            <div class="mb-10 px-0 sm:px-6">
                <h3
                    class="text-sm font-extrabold text-slate-800 mb-3 flex items-center gap-2"
                >
                    Pilihan Utama <span class="text-red-500">*</span>
                </h3>

                <div
                    class="border border-slate-200 rounded-lg overflow-hidden flex flex-col md:flex-row bg-slate-50"
                >
                    <div
                        class="w-full md:w-5/12 bg-white border-b md:border-b-0 md:border-r border-slate-200 p-4"
                        style="max-height: 280px; overflow-y: auto"
                    >
                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-3">Daftar Divisi:</p>
                        <div class="space-y-1.5">
                            @foreach ($groupedJabatan as $namaPosisi => $jabatansGroup)
                                <button
                                    type="button"
                                    @click="activePosisi1 = '{{ $namaPosisi }}'"
                                    :class="activePosisi1 === '{{ $namaPosisi }}' ? 'bg-slate-800 text-white shadow-sm' : 'bg-transparent text-slate-600 hover:bg-slate-100 border-slate-200 border'"
                                    class="w-full text-left px-3.5 py-2.5 rounded-md text-xs font-bold transition-all flex justify-between items-center"
                                >
                                    {{ $namaPosisi }}
                                    <span
                                        x-show="activePosisi1 === '{{ $namaPosisi }}'"
                                        >&rarr;</span
                                    >
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <div class="w-full md:w-7/12 p-5 bg-slate-50">
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
                            <p class="text-xs font-bold text-slate-400">Pilih divisi di panel sebelah kiri.</p>
                        </div>

                        @foreach ($groupedJabatan as $namaPosisi => $jabatansGroup)
                            <div
                                x-show="activePosisi1 === '{{ $namaPosisi }}'"
                                style="display: none"
                                class="space-y-3"
                            >
                                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Pilih Jabatan:</p>
                                @foreach ($jabatansGroup as $jabatan)
                                    <label
                                        class="flex items-center p-3.5 bg-white border rounded-md cursor-pointer transition-colors shadow-sm"
                                        :class="pilihan1 === '{{ $jabatan['id'] }}' ? 'border-blue-600 bg-blue-50' : 'border-slate-200 hover:border-blue-300'"
                                    >
                                        <div
                                            class="flex items-center justify-between w-full gap-3"
                                        >
                                            <div
                                                class="flex items-center gap-3"
                                            >
                                                <div
                                                    class="w-4 h-4 rounded-full border flex items-center justify-center shrink-0 bg-white"
                                                    :class="pilihan1 === '{{ $jabatan['id'] }}' ? 'border-blue-600' : 'border-slate-300'"
                                                >
                                                    <div
                                                        class="w-2 h-2 bg-blue-600 rounded-full"
                                                        x-show="pilihan1 === '{{ $jabatan['id'] }}'"
                                                    ></div>
                                                </div>
                                                <span
                                                    class="text-xs font-bold transition-colors"
                                                    :class="pilihan1 === '{{ $jabatan['id'] }}' ? 'text-blue-800' : 'text-slate-700'"
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
            <div class="mb-6 px-0 sm:px-6">
                <h3
                    class="text-sm font-extrabold text-slate-800 mb-3 flex items-center justify-between"
                >
                    <span class="flex items-center gap-2">
                        Pilihan Alternatif (Opsional)
                    </span>
                    <button
                        type="button"
                        @click="
                            pilihan2 = '';
                            activePosisi2 = '';
                        "
                        x-show="pilihan2"
                        style="display: none"
                        class="text-[10px] font-bold uppercase tracking-wider text-red-600 hover:text-red-800 bg-red-50 hover:bg-red-100 px-2 py-1 rounded transition-colors"
                    >
                        Batal Pilih
                    </button>
                </h3>

                <div
                    class="border border-slate-200 rounded-lg overflow-hidden flex flex-col md:flex-row bg-slate-50"
                >
                    <div
                        class="w-full md:w-5/12 bg-white border-b md:border-b-0 md:border-r border-slate-200 p-4"
                        style="max-height: 280px; overflow-y: auto"
                    >
                        <div class="space-y-1.5">
                            @foreach ($groupedJabatan as $namaPosisi => $jabatansGroup)
                                <button
                                    type="button"
                                    @click="activePosisi2 = '{{ $namaPosisi }}'"
                                    :class="activePosisi2 === '{{ $namaPosisi }}' ? 'bg-slate-200 text-slate-800 shadow-sm' : 'bg-transparent text-slate-600 hover:bg-slate-100 border-slate-200 border'"
                                    class="w-full text-left px-3.5 py-2.5 rounded-md text-xs font-bold transition-all flex justify-between items-center"
                                >
                                    {{ $namaPosisi }}
                                    <span
                                        x-show="activePosisi2 === '{{ $namaPosisi }}'"
                                        >&rarr;</span
                                    >
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <div class="w-full md:w-7/12 p-5 bg-slate-50">
                        <input
                            type="hidden"
                            name="jabatan_2_id"
                            x-model="pilihan2"
                        />
                        <div
                            x-show="!activePosisi2"
                            class="h-full flex items-center justify-center text-center p-4"
                        >
                            <p class="text-xs font-bold text-slate-400">Pilih divisi di panel sebelah kiri.</p>
                        </div>

                        @foreach ($groupedJabatan as $namaPosisi => $jabatansGroup)
                            <div
                                x-show="activePosisi2 === '{{ $namaPosisi }}'"
                                style="display: none"
                                class="space-y-3"
                            >
                                @foreach ($jabatansGroup as $jabatan)
                                    <label
                                        class="flex items-center p-3.5 bg-white border rounded-md transition-colors shadow-sm"
                                        :class="pilihan1 === '{{ $jabatan['id'] }}' ? 'border-slate-100 bg-slate-50 cursor-not-allowed opacity-50' : (pilihan2 === '{{ $jabatan['id'] }}' ? 'border-slate-800 bg-slate-100 cursor-pointer' : 'border-slate-200 hover:border-slate-300 cursor-pointer')"
                                    >
                                        <div
                                            class="flex items-center justify-between w-full gap-3"
                                        >
                                            <div
                                                class="flex items-center gap-3"
                                            >
                                                <div
                                                    class="w-4 h-4 rounded-full border flex items-center justify-center shrink-0 bg-white"
                                                    :class="pilihan2 === '{{ $jabatan['id'] }}' ? 'border-slate-800' : 'border-slate-300'"
                                                >
                                                    <div
                                                        class="w-2 h-2 bg-slate-800 rounded-full"
                                                        x-show="pilihan2 === '{{ $jabatan['id'] }}'"
                                                    ></div>
                                                </div>
                                                <span
                                                    class="text-xs font-bold"
                                                    :class="pilihan1 === '{{ $jabatan['id'] }}' ? 'text-slate-400' : 'text-slate-700'"
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
                                                class="text-[9px] font-bold text-red-500 bg-red-50 px-2 py-1 rounded uppercase tracking-wide"
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
        @endif

        <div
            class="mt-8 pt-6 border-t border-slate-200 flex justify-end px-0 sm:px-6"
        >
            <button
                type="button"
                @click="lanjutKeTugas()"
                class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-md font-bold text-sm transition-colors w-full md:w-auto shadow-sm flex items-center justify-center gap-2"
            >
                Lanjut
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </button>
        </div>
    </div>
</div>
