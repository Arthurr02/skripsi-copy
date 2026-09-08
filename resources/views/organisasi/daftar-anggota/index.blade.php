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

    <!-- MAIN CONTAINER -->
    <div
        class="py-4 sm:py-8 px-4 sm:px-8 md:px-10 max-w-5xl mx-auto relative z-10 my-6 sm:my-8 sm:mb-10"
    >
        <!-- HEADER -->
        <div class="mb-8 sm:mb-10 relative z-10 text-center sm:text-left">
            <h2
                class="text-3xl sm:text-4xl font-extrabold text-slate-800 tracking-tight leading-tight"
            >
                Upload Daftar Anggota
            </h2>
            <p class="text-sm text-slate-500 mt-2 leading-relaxed">Lakukan upload terhadap file daftar anggota untuk dikirimkan sebagai penilaian IPKM oleh kampus nantinya.</p>
        </div>

        <!-- KONTEN FORM UPLOAD -->
        <form
            action="{{ route('organisasi.daftar-anggota.store') }}"
            method="POST"
            enctype="multipart/form-data"
            class="bg-white px-5 sm:px-10 py-6 sm:py-10 rounded-xl shadow-sm border border-slate-200"
        >
            @csrf

            <div class="space-y-8">
                <!-- Grid Tanggal -->
                <div class="grid gap-6 md:grid-cols-2">
                    <div class="my-0">
                        <label
                            for="tanggal_mulai_periode"
                            class="block text-sm font-bold text-slate-700 mb-1 tracking-wide"
                        >
                            Tanggal Awal Periode<span
                                class="text-red-500 ml-0.5"
                                >*</span
                            >
                        </label>
                        <input
                            id="tanggal_mulai_periode"
                            name="tanggal_mulai_periode"
                            type="date"
                            value="{{ old('tanggal_mulai_periode') }}"
                            required
                            class="block w-full bg-slate-50 border text-slate-600 text-sm font-bold focus:border-blue-600 focus:ring-0 rounded-lg py-3 px-4 transition-colors {{ $errors->has('tanggal_mulai_periode') ? 'border-red-500 bg-red-50 text-red-700' : 'border-slate-300' }}"
                        />
                        @error ('tanggal_mulai_periode')
                            <p class="mt-2 text-xs font-bold text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="my-0">
                        <label
                            for="tanggal_akhir_periode"
                            class="block text-sm font-bold text-slate-700 mb-1 tracking-wide"
                        >
                            Tanggal Akhir Periode<span
                                class="text-red-500 ml-0.5"
                                >*</span
                            >
                        </label>
                        <input
                            id="tanggal_akhir_periode"
                            name="tanggal_akhir_periode"
                            type="date"
                            value="{{ old('tanggal_akhir_periode') }}"
                            required
                            class="block w-full bg-slate-50 border text-slate-600 text-sm font-bold focus:border-blue-600 focus:ring-0 rounded-lg py-3 px-4 transition-colors {{ $errors->has('tanggal_akhir_periode') ? 'border-red-500 bg-red-50 text-red-700' : 'border-slate-300' }}"
                        />
                        @error ('tanggal_akhir_periode')
                            <p class="mt-2 text-xs font-bold text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Input File Drag and Drop -->
                <div
                    class="bg-slate-50 p-6 rounded-lg border border-slate-200"
                    x-data="{ fileName: '', errors: {} }"
                >
                    <label
                        class="text-sm font-bold text-slate-700 mb-1 tracking-wide flex items-center gap-2"
                    >
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        File Daftar Anggota (PDF)<span class="text-red-500 -m-1"
                            >*</span
                        >
                    </label>
                    <div class="relative group cursor-pointer mt-2">
                        <input
                            type="file"
                            name="file_daftar_anggota"
                            id="file_daftar_anggota"
                            accept=".pdf,application/pdf"
                            required
                            @change="
                                fileName =
                                    $el.files.length > 0
                                        ? $el.files[0].name
                                        : '';
                                if (
                                    $el.files.length > 0 &&
                                    $el.files[0].size > 5 * 1024 * 1024
                                ) {
                                    errors['file_daftar_anggota'] =
                                        'Ukuran file maksimal 5 MB.';
                                    fileName = '';
                                    $el.value = '';
                                } else {
                                    errors['file_daftar_anggota'] = null;
                                }
                            "
                            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                        />

                        <div
                            :class="errors['file_daftar_anggota'] || {{ $errors->has('file_daftar_anggota') ? 'true' : 'false' }}
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
                                            >Klik atau Seret Dokumen PDF</span
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

                    <!-- Validasi Alpine JS -->
                    <p
                        x-show="errors['file_daftar_anggota']"
                        x-text="errors['file_daftar_anggota']"
                        class="mt-2 text-xs text-red-500 font-bold"
                        style="display: none"
                    ></p>

                    <!-- Validasi Laravel -->
                    @error ('file_daftar_anggota')
                        <p class="mt-2 text-xs font-bold text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <hr class="border-slate-100" />

                <!-- Area Submit -->
                <div class="">
                    <button
                        type="submit"
                        class="w-full sm:w-auto sm:ml-auto bg-blue-600 text-white px-6 py-3.5 rounded-lg font-bold text-sm hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2 shadow-md hover:shadow-lg"
                    >
                        Kirimkan
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </button>
                </div>
            </div>
        </form>

        <!-- KONTEN TABEL RIWAYAT UNGGAHAN -->
        <div
            class="bg-white px-5 sm:px-10 py-6 sm:py-10 rounded-xl shadow-sm border border-slate-200 space-y-6 mt-8 sm:mt-10"
        >
            <!-- Judul Seksi Tabel -->
            <div>
                <label
                    class="block text-sm font-bold text-slate-700 mb-1 tracking-wide"
                >
                    Riwayat Unggahan
                </label>
                <p class="text-xs font-normal text-slate-500 mb-4">Daftar riwayat file anggota yang telah diunggah sebelumnya.</p>
            </div>

            <!-- Desain Tabel Diselaraskan -->
            <div
                class="overflow-hidden border border-slate-200 rounded-lg bg-white"
            >
                <div class="overflow-x-auto">
                    <table
                        class="w-full text-left border-collapse min-w-[800px]"
                    >
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200">
                                <th
                                    class="px-5 py-3 text-xs font-bold text-slate-600 tracking-wide border-r border-slate-200/50"
                                >
                                    File
                                </th>
                                <th
                                    class="px-5 py-3 text-xs font-bold text-slate-600 tracking-wide border-r border-slate-200/50 w-48"
                                >
                                    Awal Periode
                                </th>
                                <th
                                    class="px-5 py-3 text-xs font-bold text-slate-600 tracking-wide border-r border-slate-200/50 w-48"
                                >
                                    Akhir Periode
                                </th>
                                <th
                                    class="px-5 py-3 text-xs font-bold text-slate-600 tracking-wide w-56 text-center"
                                >
                                    Waktu Pengiriman
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($riwayatUnggahan as $unggahan)
                                <tr
                                    class="hover:bg-slate-50/50 transition-colors align-middle"
                                >
                                    <td class="px-5 py-4 border-slate-100">
                                        <a
                                            href="{{ route('organisasi.daftar-anggota.download', $unggahan) }}"
                                            data-no-loading
                                            class="font-bold text-sm text-blue-700 hover:text-blue-800 hover:underline transition-colors flex items-center gap-2"
                                        >
                                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                            {{ $unggahan->nama_file_asli }}
                                        </a>
                                    </td>
                                    <td
                                        class="px-5 py-4 text-sm font-bold text-slate-800 border-slate-100"
                                    >
                                        {{
                                            $unggahan->tanggal_mulai_periode->translatedFormat(
                                                'd F Y',
                                            )
                                        }}
                                    </td>
                                    <td
                                        class="px-5 py-4 text-sm font-bold text-slate-800 border-slate-100"
                                    >
                                        {{
                                            $unggahan->tanggal_akhir_periode->translatedFormat(
                                                'd F Y',
                                            )
                                        }}
                                    </td>
                                    <td
                                        class="px-5 py-4 text-sm font-medium text-slate-600 border-slate-100 text-center"
                                    >
                                        {{
                                            $unggahan->created_at
                                                ->timezone(config('app.timezone'))
                                                ->format('d/m/Y H:i')
                                        }} WIB
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td
                                        colspan="4"
                                        class="px-5 py-16 text-center bg-slate-50/50"
                                    >
                                        <div
                                            class="flex flex-col items-center justify-center"
                                        >
                                            <svg class="w-12 h-12 text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            <p class="font-bold text-slate-500 text-sm">Belum Ada Riwayat Unggahan</p>
                                            <p class="font-normal text-slate-400 text-xs mt-1">Daftar anggota yang Anda unggah akan tampil di sini.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if ($riwayatUnggahan->hasPages())
                <div class="pt-2">{{ $riwayatUnggahan->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>

@if ($errors->any())
    <script>
        document.addEventListener(
            'DOMContentLoaded',
            () => {
                window.rekrutmenAlert({
                    icon: 'warning',
                    title: 'Unggahan belum dapat dikirim',
                    text: @json ($errors->first()),
                });
            },
            { once: true },
        );
    </script>
@endif
