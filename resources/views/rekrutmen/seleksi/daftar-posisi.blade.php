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
    </div>

    <div class="max-w-5xl mx-auto px-4 sm:px-8 relative z-10 pt-12 pb-24">
        <!-- Header -->
        <div class="mb-10 text-center md:text-left">
            <span
                class="text-blue-700 text-xs font-bold uppercase tracking-widest bg-blue-100 px-3 py-1 rounded-md"
                >Manajemen Seleksi</span
            >
            <h1
                class="text-2xl md:text-3xl font-extrabold text-slate-800 tracking-tight leading-tight mt-4"
            >
                Pilih Posisi / Jabatan
            </h1>
            <p class="text-slate-500 text-sm mt-2 font-medium">Pilih jabatan di bawah ini untuk melihat dan mengelola tugas peserta pada tahapan seleksi.</p>
        </div>

        @if (!$periodeAktif)
            <!-- State Kosong Jika Tidak Ada Rekrutmen Aktif -->
            <div
                class="py-12 text-center border-2 border-dashed border-slate-300 rounded-xl bg-white/50 backdrop-blur-sm"
            >
                <svg class="w-12 h-12 text-slate-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                <h3 class="text-lg font-bold text-slate-700">
                    Belum Ada Rekrutmen Aktif
                </h3>
                <p class="text-sm text-slate-500 mt-1">Buka periode rekrutmen terlebih dahulu untuk memulai penyeleksian.</p>
            </div>
        @else
            <!-- Grid Daftar Jabatan -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                @forelse ($listJabatan as $jabatan)
                    <div
                        class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm hover:shadow-md hover:border-blue-300 transition-all group flex flex-col h-full"
                    >
                        <div class="flex-1">
                            <div
                                class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center mb-4 group-hover:bg-blue-600 group-hover:text-white transition-colors"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            </div>
                            <h3
                                class="text-lg font-extrabold text-slate-800 leading-tight mb-1"
                            >
                                {{ $jabatan->nama_jabatan }}
                            </h3>
                            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-4">
                                {{
                                    $jabatan->pendaftaran1_count +
                                        $jabatan->pendaftaran2_count
                                }} Total Pelamar
                            </p>
                        </div>

                        <a
                            href="{{ route('organisasi.rekrutmen.seleksi.tahapan', $jabatan->id) }}"
                            class="w-full mt-4 flex items-center justify-between py-2.5 px-4 bg-slate-50 border border-slate-200 group-hover:bg-blue-50 group-hover:border-blue-200 group-hover:text-blue-700 text-slate-600 text-xs font-bold rounded-lg transition-colors"
                        >
                            <span>Kelola Tahapan</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </a>
                    </div>
                @empty
                    <div
                        class="col-span-full py-8 text-center text-slate-500 text-sm font-medium"
                    >
                        Belum ada jabatan yang ditambahkan pada periode ini.
                    </div>
                @endforelse
            </div>
        @endif
    </div>
</x-app-layout>
