<x-app-layout>
    <!-- Wrapper Alpine.js untuk Modal -->
    <div
        x-data="{
            tugasModalOpen: false,
            tugasAktif: null,
            // Simulasi data peserta yang mengumpulkan (Nanti diisi dari relasi database)
            listPeserta: [], 
            jabatanId: '{{ $jabatan->id ?? '' }}',

            bukaModal(tugas, peserta) {
                this.tugasAktif = tugas;
                this.listPeserta = peserta || [];
                this.tugasModalOpen = true;
                document.body.style.overflow = 'hidden';
            },
            tutupModal() {
                this.tugasModalOpen = false;
                setTimeout(() => { 
                    this.tugasAktif = null; 
                    this.listPeserta = [];
                }, 300);
                document.body.style.overflow = '';
            },
            parseBerkasLama(jsonStr) {
                try {
                    let obj = JSON.parse(jsonStr);
                    if(typeof obj === 'string') obj = JSON.parse(obj);
                    return obj.berkas || [];
                } catch(e) {
                    return [];
                }
            }
        }"
    >
        <!-- Background Flat Gelap (Diselaraskan 100% dengan Role Mahasiswa) -->
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
        <div class="max-w-5xl mx-auto px-4 sm:px-8 relative z-10 pt-8 pb-3">
            <nav class="mb-6">
                <!-- Tombol kembali disesuaikan ke halaman daftar posisi -->
                <a
                    {{-- href="{{ route('organisasi.rekrutmen.posisi.index') }}" --}}
                    class="inline-flex items-center text-xs font-bold uppercase tracking-wider text-slate-300 hover:text-white transition-colors bg-slate-800 border border-slate-700 hover:bg-slate-700 px-3 py-1.5 rounded-md shadow-sm"
                >
                    <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Kembali ke Daftar Posisi
                </a>
            </nav>

            <!-- Header Profil (Gaya Kartu Putih - Disesuaikan untuk Konteks Manajemen Posisi) -->
            <div
                class="py-6 flex flex-col md:flex-row items-center gap-5 text-center md:text-left"
            >
                <div class="shrink-0 hidden sm:block">
                    <!-- Ikon Manajemen Posisi -->
                    <div
                        class="w-20 h-20 rounded-full bg-white border border-slate-200 shadow-sm flex items-center justify-center text-blue-600 p-0.5"
                    >
                        <div
                            class="w-full h-full bg-blue-50 rounded-full flex items-center justify-center"
                        >
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        </div>
                    </div>
                </div>

                <!-- Info Title -->
                <div class="flex-1 mt-1 md:mt-0">
                    <div
                        class="flex flex-col sm:flex-row sm:items-center gap-2 mb-2 justify-center md:justify-start"
                    >
                        <span
                            class="text-blue-700 text-xs font-bold uppercase tracking-widest"
                        >
                            Manajemen Seleksi
                        </span>
                    </div>

                    <p class="text-lg md:text-xl font-bold text-slate-400 uppercase tracking-widest -mb-2">{{
                        $namaRekrutmen ??
                            'Oprec Pengurus BEM'
                    }}</p>
                    <h1
                        class="text-xl md:text-2xl font-extrabold text-slate-800 tracking-tight leading-tight flex items-center"
                    >
                        {{
                            $namaJabatan ??
                                'Staff Ahli Ristek'
                        }}
                        <span
                            class="h-5 flex items-center px-2 py-0.5 bg-indigo-100 text-indigo-700 border border-indigo-200 rounded-md text-[10px] font-bold uppercase tracking-widest shadow-sm w-max mx-auto md:mx-4"
                        >
                            {{ $totalPelamar ?? '0' }} Pelamar
                        </span>
                    </h1>
                </div>
            </div>
        </div>

        <!-- AREA KONTEN (TIMELINE YANG DIPERKECIL) -->
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 pb-24 relative z-20">
            <div class="p-5 sm:p-6">
                <h2
                    class="text-base font-extrabold text-slate-800 border-b border-slate-200 pb-3 mb-6 flex items-center gap-2"
                >
                    <div
                        class="w-6 h-6 rounded-md bg-blue-100 flex items-center justify-center text-blue-600"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 012-2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                    </div>
                    Pantau Tahapan Seleksi
                </h2>

                <div class="relative w-full overflow-hidden">
                    <!-- Garis Vertikal Timeline -->
                    <div
                        class="absolute border-l-2 border-slate-200 h-full left-5 top-4"
                    ></div>

                    @foreach ($tahapans as $index => $tahapan)
                        <div
                            class="mb-6 flex justify-between items-start w-full relative"
                        >
                            <!-- Indikator Lingkaran Timeline -->
                            <div
                                class="relative z-10 w-10 h-10 rounded-full shrink-0 flex items-center justify-center font-bold shadow-sm ring-4 ring-white {{ $tahapan->is_past ? 'bg-emerald-500 text-white' : ($tahapan->is_active ? 'bg-blue-600 text-white ring-blue-50' : 'bg-slate-100 text-slate-400 border border-slate-300') }}"
                            >
                                @if ($tahapan->is_past)
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                @elseif ($tahapan->is_active)
                                    <span
                                        class="block w-2.5 h-2.5 bg-white rounded-full"
                                    ></span>
                                @else
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                @endif
                            </div>

                            <!-- Kartu Tahapan Lebih Ringkas -->
                            <div class="ml-6 w-full flex-1 mb-4">
                                <div
                                    class="bg-white rounded-lg border transition-colors duration-300 {{ $tahapan->is_active ? 'border-blue-500 shadow-sm' : 'border-slate-200' }}"
                                >
                                    <!-- Card Header -->
                                    <div
                                        class="p-3 sm:p-4 border-b border-slate-100 {{ $tahapan->is_active ? 'bg-blue-200' : 'bg-slate-100' }} rounded-t-lg flex flex-col sm:flex-row sm:items-start justify-between gap-2"
                                    >
                                        <div class="flex-1">
                                            <h3
                                                class="text-xl font-extrabold text-blue-700 leading-tight"
                                            >
                                                {{ $tahapan->urutan_tahapan }}. {{ $tahapan->nama_tahapan }}
                                            </h3>
                                        </div>

                                        <div
                                            class="shrink-0 text-left sm:text-right"
                                        >
                                            @if ($tahapan->is_waktu_tunggal)
                                                <p class="text-xs font-bold text-slate-700">{{
                                                    $tahapan->parsed_mulai->translatedFormat(
                                                        'd M Y',
                                                    )
                                                }}</p>
                                                <p class="text-xs font-bold text-slate-700">{{
                                                    $tahapan->parsed_mulai->format(
                                                        'H:i',
                                                    )
                                                }} WIB</p>
                                            @else
                                                <p class="text-xs font-bold text-slate-700 flex">
                                                    <span class="flex flex-col">
                                                        <span>{{
                                                            $tahapan->parsed_mulai->translatedFormat(
                                                                'd M Y',
                                                            )
                                                        }}</span>
                                                        <span>{{
                                                            $tahapan->parsed_mulai->format(
                                                                'H:i',
                                                            )
                                                        }}</span>
                                                    </span>
                                                    &nbsp;&nbsp;&ndash;&nbsp;&nbsp;
                                                    <span class="flex flex-col">
                                                        <span>{{
                                                            $tahapan->parsed_berakhir->translatedFormat(
                                                                'd M Y',
                                                            )
                                                        }}</span>
                                                        <span>{{
                                                            $tahapan->parsed_berakhir->format(
                                                                'H:i',
                                                            )
                                                        }}</span>
                                                    </span>
                                                </p>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Card Body (Tugas & Lampiran) -->
                                    <div class="p-3 sm:p-4 space-y-3">
                                        <!-- Deskripsi Tahapan -->
                                        @if ($tahapan->deskripsi_tahapan)
                                            <p class="text-xs text-slate-700 leading-relaxed">
                                                {{ $tahapan->deskripsi_tahapan }}
                                            </p>
                                        @endif

                                        @if ($tahapan->tugas->isNotEmpty())
                                            <div class="space-y-3 pt-1">
                                                @foreach ($tahapan->tugas as $tugas)
                                                    <div class="space-y-2">
                                                        <div
                                                            class="flex justify-between items-center border-t border-slate-100 pt-3"
                                                        >
                                                            <h4
                                                                class="text-[10px] font-extrabold text-slate-600 flex items-center gap-1.5 uppercase tracking-wide"
                                                            >
                                                                <svg class="w-3 h-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                                                                Tugas Panitia
                                                            </h4>

                                                            <!-- Label Jumlah Pengumpul -->
                                                            <span
                                                                class="px-1.5 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider bg-slate-100 text-slate-500 border border-slate-200 flex items-center gap-1"
                                                            >
                                                                <!-- Sesuaikan variabel perhitungan ini dengan backend Anda --> {{
                                                                    $tugas->jumlah_pengumpul ??
                                                                        0
                                                                }} / {{ $totalPelamar ?? 0 }} Mengumpulkan
                                                            </span>
                                                        </div>

                                                        <!-- Tombol Aksi Panitia (Buka Modal List Peserta) -->
                                                        <button
                                                            type="button"
                                                            @click="bukaModal(
                                                                JSON.parse(atob('{{ base64_encode(json_encode($tugas)) }}')),
                                                                // Pass array peserta dari backend (disimulasikan di bawah)
                                                                JSON.parse(atob('{{ base64_encode(json_encode($tugas->peserta_jawaban ?? [])) }}'))
                                                            )"
                                                            class="w-full flex justify-between items-center py-2.5 px-4 bg-slate-600 text-white hover:bg-slate-700 shadow-sm text-xs font-bold rounded-md transition-colors"
                                                        >
                                                            <span
                                                                >Lihat Jawaban
                                                                Peserta</span
                                                            >
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                            </svg>
                                                        </button>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- MODAL LIST JAWABAN PESERTA (FLAT DESIGN - PANITIA) -->
        <div
            x-show="tugasModalOpen"
            style="display: none"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6"
        >
            <!-- Backdrop -->
            <div
                x-show="tugasModalOpen"
                x-transition.opacity
                class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"
                @click="tutupModal()"
            ></div>

            <!-- Modal Panel -->
            <div
                x-show="tugasModalOpen"
                x-transition.translate.y.scale.95
                class="relative bg-white rounded-lg shadow-xl w-full max-w-2xl max-h-[90vh] flex flex-col border border-slate-200 overflow-hidden"
            >
                <!-- Header Modal -->
                <div
                    class="px-5 py-4 border-b border-slate-200 bg-slate-50 flex items-center justify-between"
                >
                    <h3
                        class="text-sm font-extrabold text-slate-800 uppercase tracking-wide flex items-center gap-2"
                    >
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Daftar Pengumpulan Peserta
                    </h3>
                    <button
                        type="button"
                        @click="tutupModal()"
                        class="text-slate-400 hover:text-red-500 transition-colors"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <!-- Body Modal (Scrollable) -->
                <div class="p-5 overflow-y-auto space-y-5">
                    <!-- Kotak Info Tugas Ringkas -->
                    <div
                        class="bg-blue-50 border border-blue-100 rounded-md p-3 flex justify-between items-center"
                    >
                        <div>
                            <p class="text-[10px] font-bold text-blue-500 uppercase tracking-wider mb-0.5">Tugas</p>
                            <p
                                class="text-xs font-bold text-blue-800"
                                x-text="
                                    tugasAktif?.tipe_tugas
                                        ? tugasAktif.tipe_tugas
                                              .replace(/_/g, ' ')
                                              .toUpperCase()
                                        : 'N/A'
                                "
                            ></p>
                        </div>
                        <div class="text-right">
                            <p class="text-[10px] font-bold text-blue-500 uppercase tracking-wider mb-0.5">Format</p>
                            <p class="text-xs font-bold text-blue-800" x-text="tugasAktif?.tipe_jawaban_tugas ? tugasAktif.tipe_jawaban_tugas.replace(/_/g, ' ').toUpperCase() : 'BEBAS'"></p>
                        </div>
                    </div>

                    <!-- List Peserta -->
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-3">Peserta yang Menjalani Tahap Ini</p>

                        <!-- State Kosong jika belum ada peserta -->
                        <template x-if="listPeserta.length === 0">
                            <div
                                class="py-8 text-center border-2 border-dashed border-slate-200 rounded-lg"
                            >
                                <svg class="w-8 h-8 text-slate-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                <p class="text-xs font-bold text-slate-400">Belum ada data peserta.</p>
                            </div>
                        </template>

                        <!-- Looping Data Peserta (Disusun Rapi Flat Design) -->
                        <div class="space-y-2">
                            <template
                                x-for="(peserta, index) in listPeserta"
                                :key="index"
                            >
                                <div
                                    class="flex items-center justify-between p-3 border border-slate-200 rounded-lg hover:border-blue-300 hover:shadow-sm transition-all bg-white group"
                                >
                                    <div class="flex items-center gap-3">
                                        <!-- Avatar Inisial -->
                                        <div
                                            class="w-8 h-8 rounded-full bg-slate-100 border border-slate-200 flex items-center justify-center text-[10px] font-extrabold text-slate-500 uppercase"
                                        >
                                            <span
                                                x-text="
                                                    peserta.nama.substring(0, 2)
                                                "
                                            ></span>
                                        </div>
                                        <div>
                                            <p
                                                class="text-xs font-extrabold text-slate-800"
                                                x-text="peserta.nama"
                                            ></p>
                                            <!-- Status Pengumpulan -->
                                            <template
                                                x-if="peserta.sudah_kumpul"
                                            >
                                                <p class="text-[9px] font-bold text-emerald-600 uppercase tracking-wide flex items-center gap-1 mt-0.5">
                                                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                                    Sudah Mengumpulkan
                                                </p>
                                            </template>
                                            <template
                                                x-if="!peserta.sudah_kumpul"
                                            >
                                                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wide flex items-center gap-1 mt-0.5">
                                                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                    Belum Kumpul
                                                </p>
                                            </template>
                                        </div>
                                    </div>

                                    <!-- Tombol Lihat/Nilai (Hanya aktif jika sudah kumpul) -->
                                    <template x-if="peserta.sudah_kumpul">
                                        <a
                                            :href="`/organisasi/rekrutmen/tugas/${tugasAktif?.id}/peserta/${peserta.id}`"
                                            class="shrink-0 px-3 py-1.5 bg-blue-50 text-blue-700 hover:bg-blue-600 hover:text-white border border-blue-200 hover:border-blue-600 text-[10px] font-bold uppercase tracking-wider rounded transition-colors"
                                        >
                                            Buka Jawaban
                                        </a>
                                    </template>
                                    <template x-if="!peserta.sudah_kumpul">
                                        <span
                                            class="shrink-0 px-3 py-1.5 bg-slate-50 text-slate-400 border border-slate-100 text-[10px] font-bold uppercase tracking-wider rounded cursor-not-allowed"
                                        >
                                            Menunggu
                                        </span>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Footer Modal (Jika perlu aksi bulk) -->
                <div
                    class="px-5 py-3 border-t border-slate-200 bg-slate-50 text-right"
                >
                    <a
                        :href="`/organisasi/rekrutmen/tugas/${tugasAktif?.id}/semua-jawaban`"
                        class="inline-flex justify-center items-center py-2 px-4 bg-slate-800 text-white hover:bg-slate-900 shadow-sm text-xs font-bold rounded-md transition-colors"
                    >
                        Buka Halaman Penilaian Lengkap
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
