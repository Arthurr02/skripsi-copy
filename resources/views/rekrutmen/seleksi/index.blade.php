<x-app-layout>
    <div class="p-8 max-w-5xl mx-auto space-y-6">
        <div
            class="mb-8 bg-white p-5 rounded-xl border border-gray-200 shadow-sm"
        >
            <div
                class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 auto-rows-fr"
            >
                @forelse ($listTahapan as $tahapan)
                    @php
                        $isActive = $activeTahapan && $activeTahapan->id === $tahapan->id;

                        // Set style warna berdasarkan status waktu instan
                        $statusStyles = [
                            'sudah_lewat' => [
                                'bg' => 'bg-emerald-50',
                                'border' => 'border-emerald-200',
                                'text' => 'text-emerald-700',
                                'label' => '✅ Selesai',
                            ],
                            'sedang_berjalan' => [
                                'bg' => 'bg-blue-50',
                                'border' => 'border-blue-300 animate-pulse',
                                'text' => 'text-blue-700',
                                'label' => '🔵 Berjalan',
                            ],
                            'belum_mulai' => [
                                'bg' => 'bg-gray-50',
                                'border' => 'border-gray-200',
                                'text' => 'text-gray-500',
                                'label' => '⏳ Belum Mulai',
                            ],
                        ][$tahapan->status_waktu];
                    @endphp
                    <a
                        href="{{ route('organisasi.rekrutmen.seleksi', ['tahapan_id' => $tahapan->id, 'filter_jabatan' => request('filter_jabatan')]) }}"
                        class="p-4 rounded-xl border transition-all text-left flex flex-col justify-between h-full hover:shadow-md {{ $isActive ? 'ring-2 ring-blue-600 bg-white border-blue-600 shadow-sm' : 'bg-white border-gray-200' }}"
                    >
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span
                                    class="text-[10px] font-extrabold uppercase tracking-wide px-2 py-0.5 rounded {{ $statusStyles['bg'] }} {{ $statusStyles['text'] }} border {{ $statusStyles['border'] }}"
                                >
                                    {{
                                        $statusStyles[
                                            'label'
                                        ]
                                    }}
                                </span>
                                <span class="text-xs font-black text-gray-300"
                                    >#{{ $tahapan->urutan_tahapan }}</span
                                >
                            </div>
                            <h4
                                class="text-sm font-bold text-gray-900 line-clamp-2 mt-1"
                            >
                                {{ $tahapan->nama_tahapan }}
                            </h4>
                        </div>

                        <p class="text-[10px] text-gray-400 font-medium mt-4">
                            {{
                                \Carbon\Carbon::parse(
                                    $tahapan->waktu_mulai,
                                )->format('d M')
                            }} - {{
                                \Carbon\Carbon::parse(
                                    $tahapan->waktu_berakhir,
                                )->format('d M Y')
                            }}
                        </p>
                    </a>
                @empty
                    <div
                        class="col-span-full p-6 text-center text-gray-400 italic bg-gray-50 rounded-lg"
                    >
                        Belum ada struktur tahapan seleksi yang dibuat.
                    </div>
                @endforelse
            </div>
        </div>

        @if ($activeTahapan)
            <div
                class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden"
            >
                <div
                    class="p-5 border-b border-gray-200 bg-gray-50/50 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4"
                >
                    <div>
                        <span
                            class="text-[10px] font-bold text-blue-600 bg-blue-100 px-2.5 py-1 rounded-md uppercase tracking-wider"
                            >Tahapan Sedang Ditinjau</span
                        >
                        <h3 class="text-lg font-black text-gray-800 mt-1.5">
                            {{ $activeTahapan->nama_tahapan }}
                        </h3>
                        <p class="text-xs text-gray-500 mt-0.5">{{ $activeTahapan->deskripsi_tahapan }}</p>
                    </div>

                    <form
                        method="GET"
                        action="{{ route('organisasi.rekrutmen.seleksi') }}"
                        class="flex items-center gap-2 min-w-[280px]"
                    >
                        <input
                            type="hidden"
                            name="tahapan_id"
                            value="{{ $activeTahapan->id }}"
                        />
                        <select
                            name="filter_jabatan"
                            class="w-full text-xs font-semibold border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2"
                        >
                            <option value="">
                                Semua Pilihan Posisi Divisi
                            </option>
                            @foreach ($listJabatan as $jab)
                                <option
                                    value="{{ $jab->id }}"
                                    {{
                                        request('filter_jabatan') == $jab->id
                                            ? 'selected'
                                            : ''
                                    }}
                                >
                                    {{ $jab->nama_jabatan }}
                                </option>
                            @endforeach
                        </select>
                        <button
                            type="submit"
                            class="bg-blue-600 text-white font-bold text-xs py-2 px-4 rounded-lg shadow-sm hover:bg-blue-700 transition-colors"
                        >
                            Filter
                        </button>
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr
                                class="bg-gray-50 border-b border-gray-200 text-[11px] font-extrabold text-gray-500 uppercase tracking-wider"
                            >
                                <th class="px-5 py-4 text-center w-12">No</th>
                                <th class="px-5 py-4 w-32">NIM</th>
                                <th class="px-5 py-4 w-48">Nama Lengkap</th>
                                <th class="px-5 py-4">
                                    Pilihan Jabatan (1 & 2)
                                </th>
                                <th class="px-5 py-4 w-44 text-center">
                                    Status Pengumpulan
                                </th>
                                <th class="px-5 py-4 w-40 text-center">
                                    Aksi Dokumen
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-gray-700">
                            @forelse ($pesertaData as $index => $peserta)
                                @php
                                    $sudahMengumpul = $peserta->pengumpulanTugas->isNotEmpty();
                                @endphp
                                <tr
                                    class="hover:bg-blue-50/20 transition-colors"
                                >
                                    <td
                                        class="px-5 py-4 text-center font-bold text-gray-400 text-xs"
                                    >
                                        {{ $index + 1 }}
                                    </td>
                                    <td
                                        class="px-5 py-4 font-mono text-xs font-bold text-gray-600"
                                    >
                                        {{ $peserta->nim }}
                                    </td>
                                    <td
                                        class="px-5 py-4 font-semibold text-gray-900 text-sm"
                                    >
                                        {{
                                            $peserta->mahasiswa->nama_lengkap ??
                                                'Tanpa Nama'
                                        }}
                                        <span
                                            class="block text-[11px] font-normal text-gray-400 mt-0.5"
                                            >{{
                                                $peserta->mahasiswa->email_kampus ??
                                                    '-'
                                            }}</span
                                        >
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="flex flex-col gap-1 w-fit">
                                            <span
                                                class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200"
                                            >
                                                1️⃣ {{
                                                    $peserta->pilihanJabatan1
                                                        ->nama_jabatan ?? '-'
                                                }}
                                            </span>
                                            <span
                                                class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-200"
                                            >
                                                2️⃣ {{
                                                    $peserta->pilihanJabatan2
                                                        ->nama_jabatan ?? '-'
                                                }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 text-center">
                                        @if ($sudahMengumpul)
                                            <span
                                                class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-200"
                                            >
                                                🟢 Sudah
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-amber-100 text-amber-800 border border-amber-200"
                                            >
                                                🟡 Belum
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 text-center">
                                        @if ($sudahMengumpul)
                                            <a
                                                href="#"
                                                class="inline-flex items-center gap-1 bg-white hover:bg-gray-100 text-gray-700 font-bold text-xs py-1.5 px-3 rounded border border-gray-300 shadow-sm transition-all"
                                            >
                                                🔍 Periksa
                                            </a>
                                        @else
                                            <button
                                                disabled
                                                class="inline-flex items-center gap-1 bg-gray-50 text-gray-300 font-bold text-xs py-1.5 px-3 rounded border border-gray-200 cursor-not-allowed"
                                            >
                                                🔒 Terkunci
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td
                                        colspan="6"
                                        class="text-center py-16 text-gray-400 bg-white"
                                    >
                                        <div
                                            class="flex flex-col items-center justify-center"
                                        >
                                            <span class="text-4xl block mb-2"
                                                >🔎</span
                                            >
                                            <p class="font-bold text-gray-700 text-sm">Tidak Ada Peserta</p>
                                            <p class="text-[11px] mt-1 text-gray-400">Belum ada mahasiswa mendaftar atau tidak ada yang sesuai dengan filter posisi.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
