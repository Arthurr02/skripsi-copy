<x-app-layout>
    <!-- Background Flat Gelap (DNA Desain Bawaan) -->
    <div
        class="absolute top-0 inset-x-0 h-[400px] overflow-hidden pointer-events-none -z-10 bg-slate-50"
    >
        <div
            class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PHBhdGggZD0iTTAgMGg0MHY0MEgwVjB6bTIwIDIwaDIwdjIwSDIwaC0yMHptMCAwaC0yMHYtMjBoMjB2MjB6IiBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiIHN0cm9rZT0iI2YxZjVmOSIgc3Ryb2tlLXdpZHRoPSIxIi8+PC9zdmc+')] opacity-60"
        ></div>
        <div
            class="absolute -top-[20%] -left-[10%] w-[40%] h-[60%] rounded-full bg-gradient-to-br from-blue-200/80 to-blue-50/20 blur-[100px]"
        ></div>
        <div
            class="absolute top-[10%] right-[10%] w-[35%] h-[50%] rounded-full bg-gradient-to-bl from-indigo-200/60 to-transparent blur-[120px]"
        ></div>
    </div>

    <!-- AREA HEADER -->
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 pt-8 pb-4">
        <nav class="mb-6">
            <a
                href="{{ route('mahasiswa.rekrutmen.diikuti.tahapan', $pendaftaran->id) }}"
                class="inline-flex items-center text-xs font-bold uppercase tracking-wider text-slate-300 hover:text-white transition-colors bg-slate-800 border border-slate-700 hover:bg-slate-700 px-3 py-1.5 rounded-md shadow-sm"
            >
                <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali ke Daftar Tugas
            </a>
        </nav>

        <!-- Judul Halaman -->
        <div>
            <div class="flex items-center gap-2 mb-2">
                <span
                    class="px-2 py-0.5 bg-blue-100 text-blue-700 border border-blue-200 rounded-md text-[10px] font-bold uppercase tracking-widest shadow-sm"
                >
                    Formulir Penugasan
                </span>
            </div>
            <h1
                class="text-2xl md:text-3xl font-extrabold text-slate-800 tracking-tight leading-tight"
            >
                {{
                    Str::title(
                        str_replace('_', ' ', $tugas->tipe_tugas),
                    )
                }}
            </h1>
        </div>
    </div>

    <!-- AREA KONTEN FORMULIR (DENGAN ALPINE.JS) -->
    <!-- Jika sudah ada pengumpulan, isEditing = false (mode baca). Jika belum, isEditing = true (mode isi form) -->
    <div
        x-data="{ isEditing: {{ $pengumpulan ? 'false' : 'true' }} }"
        class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 pb-24 relative z-20 mt-4"
    >
        <div
            class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-hidden"
        >
            <!-- Body Formulir -->
            <form
                action="{{ route('mahasiswa.rekrutmen.diikuti.tugas_submit', ['pendaftaran' => $pendaftaran->id, 'tugas' => $tugas->id]) }}"
                method="POST"
                class="p-6 space-y-6"
            >
                @csrf

                @if (!empty($komponenForm))
                    @foreach ($komponenForm as $item)
                        @php
                            // PASTIKAN pembacaan nama sesuai dengan bagaimana disubmit (biasanya label atau property name)
                            $fieldName =
                                $item['name'] ?? Str::slug($item['label'] ?? 'field_' . $loop->index, '_');

                            // Pencarian yang lebih toleran jika nama field-nya tersimpan berbeda sedikit
                            $existingValue =
                                $jawabanSebelumnya[$fieldName] ??
                                ($jawabanSebelumnya[$item['label'] ?? ''] ?? old($fieldName));

                            $isRequired = !empty($item['required']) && $item['required'] == true;
                        @endphp
                        <div class="space-y-1.5">
                            <!-- Label Pertanyaan -->
                            <label
                                class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider"
                            >
                                {{
                                    $item['label'] ??
                                        'Pertanyaan'
                                }}
                                <template x-if="isEditing">
                                    <span>
                                        @if ($isRequired)
                                            <span class="text-red-500 ml-0.5"
                                                >*</span
                                            >
                                        @endif
                                    </span>
                                </template>
                            </label>

                            <!-- Keterangan (Hanya muncul saat mode edit) -->
                            @if (!empty($item['keterangan']))
                                <p
                                    x-show="isEditing"
                                    class="text-[10px] text-slate-400 font-medium mb-2"
                                >{{
                                    $item[
                                        'keterangan'
                                    ]
                                }}</p>
                            @endif

                            <!-- MODE BACA (TAMPILKAN JAWABAN SAJA) -->
                            <div x-show="!isEditing" style="display: none">
                                <div
                                    class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-sm font-bold rounded-md px-4 py-3 min-h-[46px]"
                                >
                                    {{
                                        !empty($existingValue)
                                            ? (is_array($existingValue)
                                                ? implode(', ', $existingValue)
                                                : $existingValue)
                                            : '-'
                                    }}
                                </div>
                            </div>

                            <!-- MODE EDIT (TAMPILKAN INPUT FORM) -->
                            <div x-show="isEditing" style="display: none">
                                @switch ($item['tipe'] ?? 'text_short')
                                    @case ('text_long')
                                        <textarea
                                            id="{{ $fieldName }}"
                                            name="jawaban_form[{{ $fieldName }}]"
                                            rows="4"
                                            class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-sm rounded-md px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-colors"
                                            placeholder="Ketikkan jawaban Anda..."
                                            {{
                                                $isRequired
                                                    ? 'required'
                                                    : ''
                                            }}
                                            >{{ $existingValue }}</textarea
                                        >
                                        @break
                                    @case ('dropdown')
                                        <div class="relative">
                                            <select
                                                id="{{ $fieldName }}"
                                                name="jawaban_form[{{ $fieldName }}]"
                                                class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-sm rounded-md px-4 py-3 appearance-none focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-colors"
                                                {{
                                                    $isRequired
                                                        ? 'required'
                                                        : ''
                                                }}
                                            >
                                                <option
                                                    value=""
                                                    disabled
                                                    {{
                                                        empty($existingValue)
                                                            ? 'selected'
                                                            : ''
                                                    }}
                                                    >-- Pilih Opsi --
                                                </option>
                                                @foreach ($item['options'] ?? [] as $opsi)
                                                    <option
                                                        value="{{ $opsi }}"
                                                        {{
                                                            $existingValue === $opsi
                                                                ? 'selected'
                                                                : ''
                                                        }}
                                                    >
                                                        {{ $opsi }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div
                                                class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-slate-400"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                            </div>
                                        </div>
                                        @break
                                    @case ('pilihan_ganda')
                                    @case ('radio')
                                        <div class="space-y-2 mt-2">
                                            @foreach ($item['options'] ?? [] as $opsi)
                                                <label
                                                    class="flex items-center gap-3 cursor-pointer group"
                                                >
                                                    <input
                                                        type="radio"
                                                        name="jawaban_form[{{ $fieldName }}]"
                                                        value="{{ $opsi }}"
                                                        class="w-4 h-4 text-blue-600 bg-slate-50 border-slate-300 focus:ring-blue-500"
                                                        {{
                                                            $existingValue === $opsi
                                                                ? 'checked'
                                                                : ''
                                                        }}
                                                        {{
                                                            $isRequired
                                                                ? 'required'
                                                                : ''
                                                        }}
                                                    />
                                                    <span
                                                        class="text-sm text-slate-700 font-medium group-hover:text-blue-700 transition-colors"
                                                        >{{ $opsi }}</span
                                                    >
                                                </label>
                                            @endforeach
                                        </div>
                                        @break
                                    @case ('checkbox')
                                        <div class="space-y-2 mt-2">
                                            @foreach ($item['options'] ?? [] as $opsi)
                                                <label
                                                    class="flex items-center gap-3 cursor-pointer group"
                                                >
                                                    <input
                                                        type="checkbox"
                                                        name="jawaban_form[{{ $fieldName }}][]"
                                                        value="{{ $opsi }}"
                                                        class="w-4 h-4 text-blue-600 bg-slate-50 border-slate-300 rounded focus:ring-blue-500"
                                                        {{
                                                            (is_array($existingValue) &&
                                                                in_array($opsi, $existingValue)) ||
                                                            $existingValue === $opsi
                                                                ? 'checked'
                                                                : ''
                                                        }}
                                                    />
                                                    <span
                                                        class="text-sm text-slate-700 font-medium group-hover:text-blue-700 transition-colors"
                                                        >{{ $opsi }}</span
                                                    >
                                                </label>
                                            @endforeach
                                        </div>
                                        @break
                                    @default
                                        <input
                                            type="text"
                                            id="{{ $fieldName }}"
                                            name="jawaban_form[{{ $fieldName }}]"
                                            value="{{ is_array($existingValue) ? implode(', ', $existingValue) : $existingValue }}"
                                            class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-sm rounded-md px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-colors"
                                            placeholder="Ketikkan jawaban Anda..."
                                            {{
                                                $isRequired
                                                    ? 'required'
                                                    : ''
                                            }}
                                        />
                                @endswitch
                            </div>
                        </div>
                    @endforeach
                @endif

                <!-- Area Submit / Edit -->
                @if (!empty($komponenForm))
                    <div
                        class="pt-6 border-t border-slate-100 flex items-center justify-end gap-3 mt-8"
                    >
                        <!-- Logika Jika Sudah Pernah Mengirim Form -->
                        @if ($pengumpulan)
                            <!-- Tombol Edit (Hanya Muncul di Mode Baca) -->
                            <button
                                type="button"
                                x-show="!isEditing"
                                @click="isEditing = true"
                                class="w-full md:w-auto px-6 py-2.5 bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold rounded-md shadow-sm transition-colors flex justify-center items-center gap-2"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                Edit Jawaban
                            </button>
                            <!-- Tombol Batal Edit (Muncul di Mode Edit) -->
                            <button
                                type="button"
                                x-show="isEditing"
                                @click="isEditing = false"
                                style="display: none"
                                class="px-5 py-2.5 text-xs font-bold text-slate-500 hover:text-slate-800 transition-colors"
                            >
                                Batal
                            </button>
                            <!-- Tombol Simpan Perubahan (Muncul di Mode Edit) -->
                            <button
                                type="submit"
                                x-show="isEditing"
                                style="display: none"
                                class="w-full md:w-auto px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-md shadow-sm transition-colors flex justify-center items-center gap-2"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Simpan Perubahan
                            </button>
                            <!-- Logika Jika Form Baru Pertama Kali Diisi -->
                        @else
                            <button
                                type="submit"
                                class="w-full md:w-auto px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-md shadow-sm transition-colors flex justify-center items-center gap-2"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                                Kirim Jawaban
                            </button>
                        @endif
                    </div>
                @endif
            </form>
        </div>
    </div>

    <!-- Integrasi SweetAlert -->
    @if (session()->has('success'))
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: '{{
                session(
                    'success',
                )
            }}',
                    confirmButtonColor: '#2563eb', // Menyelaraskan dengan bg-blue-600 Tailwind
                    confirmButtonText: 'Tutup',
                    customClass: {
                        popup: 'rounded-lg border border-slate-200 shadow-xl',
                    },
                });
            });
        </script>
    @endif
</x-app-layout>
