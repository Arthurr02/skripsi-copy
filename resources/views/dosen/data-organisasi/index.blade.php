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
                Data Organisasi
            </h2>
            <p class="text-sm text-slate-500 mt-2 leading-relaxed">Data organisasi mencakup daftar file yang berisikan data identitas serta jabatan dari setiap anggota sebuah organisasi, dengan tujuan untuk penilaian IPKM nantinya.</p>
        </div>

        <!-- KONTEN TABEL DATA ORGANISASI -->
        <div
            class="bg-white px-5 sm:px-10 py-6 sm:py-10 rounded-xl shadow-sm border border-slate-200 space-y-8"
        >
            <!-- Judul Seksi Tabel & Form Filter -->
            <div>
                <label
                    class="block text-sm font-bold text-slate-700 mb-4 tracking-wide"
                >
                    Daftar Data Keanggotaan Organisasi
                </label>

                <!-- FORM FILTER -->
                <form
                    id="form-filter"
                    method="GET"
                    action="{{ url()->current() }}"
                    class="flex flex-col sm:flex-row sm:items-end gap-4"
                >
                    <div class="w-full sm:max-w-sm my-0">
                        <div class="relative">
                            <div
                                class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none"
                            >
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </div>
                            <input
                                id="organisasi"
                                name="organisasi"
                                value="{{ request('organisasi', $organisasi ?? '') }}"
                                placeholder="Cari organisasi..."
                                autocomplete="off"
                                class="block w-full pl-10 bg-slate-50 border text-slate-600 text-sm font-bold focus:border-blue-600 focus:ring-0 rounded-lg py-3 px-4 transition-colors border-slate-300 placeholder:font-normal placeholder:text-slate-400"
                            />
                        </div>
                    </div>

                    <!-- Hidden inputs untuk sinkronisasi Sort & Direction -->
                    <input
                        type="hidden"
                        name="sort"
                        id="input-sort"
                        value="{{ request('sort', $sort ?? '') }}"
                    />
                    <input
                        type="hidden"
                        name="direction"
                        id="input-direction"
                        value="{{ request('direction', $direction ?? '') }}"
                    />

                    <div class="flex items-center gap-3 w-full sm:w-auto">
                        <a
                            href="{{ route('dosen.data-organisasi.index') }}"
                            class="w-full sm:w-auto bg-slate-50 hover:bg-slate-100 text-slate-600 px-6 py-3 rounded-lg font-bold text-sm border border-slate-200 transition-colors flex items-center justify-center text-center"
                        >
                            Reset
                        </a>
                    </div>
                </form>
            </div>

            <!-- CONTAINER DATA (Tabel & Pagination yang akan di-update otomatis) -->
            <div
                id="data-container"
                class="transition-opacity duration-300 ease-in-out space-y-6"
            >
                <div
                    class="overflow-hidden border border-slate-200 rounded-lg bg-white"
                >
                    <div class="overflow-x-auto">
                        <table
                            class="w-full text-left border-collapse min-w-[900px]"
                        >
                            <thead>
                                <tr
                                    class="bg-slate-50 border-b border-slate-200"
                                >
                                    @php
                                        // Konfigurasi Spesifik Kolom Sorting
                                        $tableHeaders = [
                                            'organisasi' => [
                                                'label' => 'Organisasi',
                                                'sortable' => false,
                                                'th_class' => 'w-56',
                                                'inner_class' => '',
                                            ],
                                            'tanggal_mulai' => [
                                                'label' => 'Awal Periode',
                                                'sortable' => true,
                                                'th_class' => 'w-48',
                                                'inner_class' => '',
                                            ],
                                            'tanggal_akhir' => [
                                                'label' => 'Akhir Periode',
                                                'sortable' => true,
                                                'th_class' => 'w-48',
                                                'inner_class' => '',
                                            ],
                                            'waktu_kirim' => [
                                                'label' => 'Waktu Pengiriman',
                                                'sortable' => true,
                                                'th_class' => 'text-center w-52',
                                                'inner_class' => '',
                                            ],
                                        ];
                                    @endphp

                                    @foreach ($tableHeaders as $column => $colData)
                                        <th
                                            class="px-5 py-3 text-xs font-bold text-slate-600 tracking-wide border-r border-slate-200/50 {{ $colData['th_class'] }}"
                                        >
                                            @if ($colData['sortable'])
                                                <!-- KOLOM BISA DI-SORT -->
                                                <a
                                                    href="{{ request()->fullUrlWithQuery(['sort' => $column, 'direction' => request('sort') === $column && request('direction') === 'asc' ? 'desc' : 'asc']) }}"
                                                    class="inline-flex items-center gap-1.5 hover:text-blue-700 transition-colors w-full group {{ $colData['inner_class'] }}"
                                                    data-no-loading
                                                >
                                                    {{
                                                        $colData[
                                                            'label'
                                                        ]
                                                    }}

                                                    @if (request('sort') === $column)
                                                        <!-- Sedang Aktif Disortir -->
                                                        <span
                                                            class="flex items-center justify-center text-blue-700 rounded-sm w-4 h-4 text-[10px] font-black shrink-0"
                                                        >
                                                            {{
                                                                request('direction') === 'asc'
                                                                    ? '↑'
                                                                    : '↓'
                                                            }}
                                                        </span>
                                                    @else
                                                        <!-- Ikon Idle (Belum diklik) -->
                                                        <svg class="w-3.5 h-3.5 text-slate-300 group-hover:text-blue-500 transition-colors shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4"></path>
                                                        </svg>
                                                    @endif
                                                </a>
                                            @else
                                                <!-- KOLOM TIDAK BISA DI-SORT (Teks biasa) -->
                                                <span
                                                    class="inline-flex items-center w-full text-slate-600 {{ $colData['inner_class'] }}"
                                                >
                                                    {{
                                                        $colData[
                                                            'label'
                                                        ]
                                                    }}
                                                </span>
                                            @endif
                                        </th>
                                    @endforeach

                                    <!-- KOLOM PDF (Fix) -->
                                    <th
                                        class="px-5 py-3 text-xs font-bold text-slate-600 tracking-wide w-48"
                                    >
                                        File PDF
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse ($dataOrganisasi as $data)
                                    <tr
                                        class="hover:bg-slate-50/50 transition-colors align-middle"
                                    >
                                        <td
                                            class="px-5 py-4 text-sm font-bold text-slate-800 border-slate-100"
                                        >
                                            {{
                                                $data->organisasi
                                                    ->nama_organisasi
                                            }}
                                        </td>
                                        <td
                                            class="px-5 py-4 text-sm font-bold text-slate-800 border-slate-100"
                                        >
                                            {{
                                                $data->tanggal_mulai_periode->translatedFormat(
                                                    'd F Y',
                                                )
                                            }}
                                        </td>
                                        <td
                                            class="px-5 py-4 text-sm font-bold text-slate-800 border-slate-100"
                                        >
                                            {{
                                                $data->tanggal_akhir_periode->translatedFormat(
                                                    'd F Y',
                                                )
                                            }}
                                        </td>
                                        <td
                                            class="px-5 py-4 text-sm font-medium text-slate-600 border-slate-100"
                                        >
                                            {{
                                                $data->created_at
                                                    ->timezone(config('app.timezone'))
                                                    ->format('d/m/Y')
                                            }}<br />
                                            {{
                                                $data->created_at
                                                    ->timezone(config('app.timezone'))
                                                    ->format('H:i')
                                            }} WIB
                                        </td>
                                        <td class="px-5 py-4 border-slate-100">
                                            <a
                                                href="{{ route('dosen.data-organisasi.download', $data) }}"
                                                data-no-loading
                                                class="font-bold text-sm text-blue-700 hover:text-blue-800 hover:underline transition-colors flex items-center justify-center gap-2"
                                            >
                                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                                {{ $data->nama_file_asli }}
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td
                                            colspan="5"
                                            class="px-5 py-16 text-center bg-slate-50/50"
                                        >
                                            <div
                                                class="flex flex-col items-center justify-center"
                                            >
                                                <svg class="w-12 h-12 text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                                <p class="font-bold text-slate-500 text-sm">Belum Ada Data</p>
                                                <p class="font-normal text-slate-400 text-xs mt-1">Belum ada daftar anggota yang dikirimkan oleh organisasi.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                @if (isset($dataOrganisasi) && $dataOrganisasi->hasPages())
                    <div class="pt-2">{{ $dataOrganisasi->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>

<!-- SCRIPT UNTUK LIVE SEARCH, SORTING & PAGINATION TANPA RELOAD -->
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const form = document.getElementById('form-filter');
        const searchInput = document.getElementById('organisasi');
        const inputSort = document.getElementById('input-sort');
        const inputDirection = document.getElementById('input-direction');
        let debounceTimer;

        // Failsafe: Jika elemen tidak ada, jangan jalankan script
        if (!form || !searchInput) return;

        const fetchAndUpdateData = async (targetUrl) => {
            const container = document.getElementById('data-container');
            if (!container) return (window.location.href = targetUrl); // Fallback jika dom rusak

            // Efek visual menandakan proses load
            container.style.opacity = '0.5';
            container.style.pointerEvents = 'none';

            try {
                const response = await fetch(targetUrl, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        Accept: 'text/html',
                    },
                });

                // Jika server tidak membalas 200 OK (misal terjadi error Laravel), paksa reload halaman untuk melihat error
                if (!response.ok)
                    throw new Error(`HTTP error! status: ${response.status}`);

                const htmlText = await response.text();
                const parser = new DOMParser();
                const doc = parser.parseFromString(htmlText, 'text/html');

                const newContainer = doc.getElementById('data-container');
                if (newContainer) {
                    container.innerHTML = newContainer.innerHTML;
                    window.history.pushState({}, '', targetUrl);

                    // Sinkronisasi input sort & direction di form (agar saat di-search, form tau urutan terakhir)
                    const urlObj = new URL(targetUrl, window.location.origin);
                    if (inputSort)
                        inputSort.value = urlObj.searchParams.get('sort') || '';
                    if (inputDirection)
                        inputDirection.value =
                            urlObj.searchParams.get('direction') || '';
                } else {
                    throw new Error(
                        'Elemen #data-container tidak ditemukan pada response server.',
                    );
                }
            } catch (error) {
                console.error(
                    'Proses AJAX gagal. Melakukan navigasi ulang...',
                    error,
                );
                // Fallback krusial: mencegah stuck loading selamanya
                window.location.href = targetUrl;
            } finally {
                container.style.opacity = '1';
                container.style.pointerEvents = 'auto';
            }
        };

        // Live Search Input Handler
        searchInput.addEventListener('input', () => {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                const urlObj = new URL(form.action, window.location.origin);
                const formData = new FormData(form);

                for (const [key, value] of formData.entries()) {
                    if (value) {
                        urlObj.searchParams.set(key, value);
                    } else {
                        urlObj.searchParams.delete(key);
                    }
                }

                fetchAndUpdateData(urlObj.toString());
            }, 350);
        });

        // Hentikan submit manual form karena sudah di-handle oleh Javascript
        form.addEventListener('submit', (e) => {
            e.preventDefault();
        });

        // Event delegation untuk link Sorting & Pagination di dalam tabel
        document.addEventListener('click', (e) => {
            const link = e.target.closest('#data-container a');

            // Cegah loading pada link ber-atribut data-no-loading (seperti download PDF)
            if (
                link &&
                link.hasAttribute('href') &&
                !link.hasAttribute('data-no-loading')
            ) {
                e.preventDefault();
                fetchAndUpdateData(link.href);
            }
        });

        // Handle navigasi tombol browser (Back/Forward)
        window.addEventListener('popstate', () => {
            fetchAndUpdateData(window.location.href);
        });
    });
</script>
