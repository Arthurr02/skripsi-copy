<x-app-layout>
    <!-- Background Aksen Atas (Mencegah scroll horizontal dengan inset-x-0) -->
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

    <div class="p-4 sm:p-8 max-w-5xl mx-auto relative z-10 my-6 sm:my-10">
        <!-- HEADER -->
        <div class="mb-10 relative z-10">
            <h2
                class="text-3xl sm:text-4xl font-extrabold text-slate-800 tracking-tight leading-tight"
            >
                Buka Rekrutmen
            </h2>
            <p class="text-sm text-slate-500 mt-2 leading-relaxed">Pembukaan rekrutmen dilakukan untuk membuka penerimaan anggota baru organisasi yang melalui berbagai tahapan seleksi.</p>
        </div>

        <!-- KONTEN FORM -->
        <form
            action="{{ route('organisasi.buka-rekrutmen.store_inisiasi') }}"
            method="POST"
            class="bg-white px-8 sm:px-10 pb-8 sm:pb-10 rounded-lg shadow-sm border border-slate-200 space-y-8"
        >
            @csrf

            <!-- BAGIAN 1: PERIODE -->
            <div>
                <label
                    class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wide"
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
                <p class="text-xs font-normal text-slate-500 mb-5">Pilih tahun ajaran kepengurusan dari rekrutmen ini.</p>
                <select
                    name="tahun_periode"
                    class="block w-full md:w-1/2 bg-slate-50 border border-slate-300 text-slate-700 text-sm font-bold focus:border-blue-600 focus:ring-0 rounded-md py-3 transition-colors"
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
                        -- Pilih Tahun Periode --
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

            <hr class="border-slate-200" />

            <!-- BAGIAN 2: PANITIA -->
            @php
                $oldNims = old('nim_panitia', []);
                $panitiaData = [];

                // Jika ada data old (misal setelah gagal validasi), gunakan data tersebut
                if (!empty($oldNims)) {
                    foreach ($oldNims as $nim) {
                        $panitiaData[] = ['nim' => $nim, 'pesanError' => ''];
                    }
                } else {
                    // Jika halaman baru dimuat, sediakan 1 baris kosong default
                    $panitiaData[] = ['nim' => '', 'pesanError' => ''];
                }
            @endphp

            <!-- Data JSON diamankan di dalam JS agar tidak merusak struktur HTML -->
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
                <div class="mb-5">
                    <label
                        class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wide"
                    >
                        Kepanitiaan Rekrutmen
                    </label>
                    <p class="text-xs font-normal text-slate-500">Masukkan NIM mahasiswa yang bertugas sebagai panitia rekrutmen.</p>
                </div>

                <div class="overflow-hidden border border-slate-200 rounded-md">
                    <table class="w-full text-left border-collapse bg-white">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200">
                                <th
                                    class="px-5 py-3 text-xs font-bold text-slate-600 uppercase tracking-wide"
                                >
                                    NIM Mahasiswa
                                </th>
                                <th
                                    class="px-5 py-3 text-xs font-bold text-slate-600 uppercase tracking-wide w-32 text-center"
                                >
                                    Tindakan
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <template
                                x-for="(baris, index) in panitia"
                                :key="index"
                            >
                                <tr
                                    class="hover:bg-slate-50/50 transition-colors align-top"
                                >
                                    <td class="px-5 py-4">
                                        <div class="flex items-center">
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
                                                class="w-full sm:w-2/3 border border-slate-300 focus:border-blue-600 focus:ring-0 rounded-l-md transition-colors text-sm font-bold py-2.5"
                                                :class="baris.pesanError
                                                    ? 'border-red-500 bg-red-50 text-red-700'
                                                    : 'bg-white text-slate-900'"
                                                required
                                            />
                                            <span
                                                class="inline-flex items-center px-4 py-2.5 rounded-r-md border border-l-0 border-slate-300 bg-slate-100 text-slate-600 text-sm font-bold"
                                            >
                                                @stis
                                                .ac.id
                                            </span>
                                        </div>
                                        <p
                                            x-show="baris.pesanError"
                                            x-text="baris.pesanError"
                                            class="text-red-500 text-xs mt-2 font-bold"
                                            style="display: none"
                                        ></p>
                                    </td>
                                    <td class="px-5 py-4 text-center">
                                        <button
                                            type="button"
                                            @click="hapusBaris(index)"
                                            x-show="panitia.length > 1"
                                            class="text-red-500 hover:text-red-700 hover:bg-red-50 px-3 py-2 rounded-md text-xs font-bold transition-colors mt-1"
                                        >
                                            Hapus
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <div class="mt-5">
                    <button
                        type="button"
                        @click="tambahBaris()"
                        class="text-xs font-bold uppercase tracking-wide text-blue-700 bg-blue-50 hover:bg-blue-100 px-4 py-2.5 rounded-md border border-blue-200 transition-colors inline-flex items-center gap-1.5"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                        Tambah Panitia
                    </button>
                </div>

                <div
                    class="pt-8 mt-6 border-t border-slate-200 flex justify-end"
                >
                    <button
                        type="submit"
                        class="bg-blue-600 text-white px-8 py-3.5 rounded-md font-bold text-sm hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
                    >
                        Buka Rekrutmen
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </button>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        @if (session('error_server'))
        Swal.fire({
            icon: 'error',
            title: 'Sistem Terkendala',
            text: '{!!
        session(
            'error_server',
        )
    !!}',
            confirmButtonColor: '#2563eb',
        });
        @endif

        @if ($errors->any())
        Swal.fire({
            icon: 'warning',
            title: 'Validasi Gagal',
            html: `
                <p class="text-left text-red-500 text-sm font-bold">
                    @foreach ($errors->all() as $error)
                        {{ $error }}<br>
                    @endforeach
                </p>
            `,
            confirmButtonColor: '#2563eb',
        });
        @endif

        @if (session('success_inisiasi'))
        Swal.fire({
            icon: 'success',
            title: 'Rekrutmen Dibuka!',
            text: 'Periode {{
        session(
            'tahun_periode',
        )
    }}',
            showCancelButton: true,
            confirmButtonColor: '#2563eb',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Lanjutkan',
            cancelButtonText: 'Kembali Dashboard',
            allowOutsideClick: false,
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '{{ route('organisasi.rekrutmen.update', session('periode_id') ?? 1) }}';
            } else {
                window.location.href = '{{ route('organisasi.dashboard') }}';
            }
        });
        @endif
    });

    @if (session('rekrutmen_sedang_berjalan'))
    Swal.fire({
        icon: 'warning',
        title: 'Rekrutmen Sedang Berjalan',
        text: '{!!
        session(
            'rekrutmen_sedang_berjalan',
        )
    !!}',
        confirmButtonColor: '#dc2626',
    });
    @endif
</script>
