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

    <!-- MAIN CONTAINER (Padding responsif) -->
    <div
        class="py-4 sm:py-8 px-4 sm:px-8 md:px-10 max-w-5xl mx-auto relative z-10 my-6 sm:my-10"
    >
        <!-- HEADER SELARAS -->
        <div
            class="mb-8 sm:mb-10 relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-5 text-center sm:text-left"
        >
            <div>
                <h2
                    class="text-3xl sm:text-4xl font-extrabold text-slate-800 tracking-tight leading-tight"
                >
                    Daftar Panitia
                </h2>
                <p class="text-sm text-slate-500 mt-2 leading-relaxed">Daftar panitia yang bertugas dalam kegiatan rekrutmen.</p>
            </div>

            <!-- Info Periode -->
            <div
                class="w-full sm:w-auto bg-white backdrop-blur-sm border border-slate-200/80 px-6 py-4 rounded-xl shrink-0 flex flex-col items-center sm:items-start justify-between shadow-sm"
            >
                <div class="flex items-center justify-center gap-2 mb-1.5">
                    <span class="relative flex h-2 w-2">
                        <span
                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"
                        ></span>
                        <span
                            class="relative inline-flex rounded-full h-2 w-2 bg-blue-500"
                        ></span>
                    </span>
                    <p class="text-[11px] text-slate-500 font-bold uppercase tracking-widest">Periode Aktif</p>
                </div>
                <p class="text-2xl font-extrabold text-blue-600 tracking-tight">
                    {{ $periode->tahun_periode }}
                </p>
            </div>
        </div>

        <!-- KONTEN UTAMA (Format Card & Padding disamakan) -->
        <div
            class="bg-white px-5 sm:px-10 py-6 sm:py-10 rounded-xl shadow-sm border border-slate-200 space-y-6"
        >
            <!-- Judul Seksi Tabel -->
            <div>
                <label
                    class="block text-sm font-bold text-slate-700 mb-1 tracking-wide"
                >
                    Anggota Panitia
                </label>
            </div>

            <!-- Form Penambahan Panitia -->
            <form
                id="form-tambah-panitia"
                method="POST"
                action="{{ route('organisasi.rekrutmen.panitia.store') }}"
            >
                @csrf
            </form>

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
                                    class="px-5 py-3 text-xs font-bold text-slate-600 tracking-wide border-r border-slate-200/50 w-48"
                                >
                                    NIM Mahasiswa
                                </th>
                                <th
                                    class="px-5 py-3 text-xs font-bold text-slate-600 tracking-wide border-r border-slate-200/50"
                                >
                                    Nama Lengkap
                                </th>
                                <th
                                    class="px-5 py-3 text-xs font-bold text-slate-600 tracking-wide border-r border-slate-200/50"
                                >
                                    Email Kampus
                                </th>
                                <th
                                    class="px-5 py-3 text-xs font-bold text-slate-600 tracking-wide text-center w-36"
                                >
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <!-- Baris Input Tambah Panitia -->

                            <!-- Data List Panitia -->
                            @forelse ($panitia as $anggota)
                                <tr
                                    class="hover:bg-slate-50/50 transition-colors align-middle"
                                >
                                    <td
                                        class="px-5 py-4 font-mono text-sm font-bold text-slate-700 border-slate-100"
                                    >
                                        {{ $anggota->nim }}
                                    </td>
                                    <td
                                        class="px-5 py-4 text-sm font-bold text-slate-800 border-slate-100"
                                    >
                                        {{
                                            $anggota->mahasiswa?->nama_lengkap ??
                                                '-'
                                        }}
                                    </td>
                                    <td
                                        class="px-5 py-4 text-sm font-medium text-slate-600 border-slate-100"
                                    >
                                        {{
                                            $anggota->mahasiswa?->email_kampus ??
                                                '-'
                                        }}
                                    </td>
                                    <td class="px-5 py-4 text-center">
                                        <form
                                            id="form-hapus-panitia-{{ $anggota->id }}"
                                            method="POST"
                                            action="{{ route('organisasi.rekrutmen.panitia.destroy', $anggota) }}"
                                        >
                                            @csrf
                                            @method ('DELETE')
                                            <button
                                                type="button"
                                                onclick="hapusPanitia('form-hapus-panitia-{{ $anggota->id }}')"
                                                class="w-full sm:w-auto text-xs font-bold tracking-wide text-red-700 bg-red-50 hover:bg-red-100 px-4 py-2.5 rounded-md border border-red-200 transition-colors flex justify-center items-center gap-1.5 mx-auto"
                                            >
                                                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                                Hapus
                                            </button>
                                        </form>
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
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                            </svg>
                                            <p class="font-bold text-slate-500 text-sm">Belum Ada Panitia</p>
                                            <p class="font-normal text-slate-400 text-xs mt-1">Silakan masukkan NIM panitia pada baris di atas.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                            <tr
                                class="bg-blue-50/50 border-b-2 border-blue-100/60 align-middle"
                            >
                                <td class="px-5 py-4 border-blue-100/60">
                                    <label for="nim-panitia" class="sr-only"
                                        >NIM panitia</label
                                    >
                                    <input
                                        id="nim-panitia"
                                        form="form-tambah-panitia"
                                        name="nim"
                                        value="{{ old('nim') }}"
                                        inputmode="numeric"
                                        pattern="[0-9]{9}"
                                        maxlength="9"
                                        required
                                        autocomplete="off"
                                        placeholder="Contoh: 222212602"
                                        class="w-full rounded-md border-slate-300 text-sm font-bold text-slate-800 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2.5 placeholder:font-normal placeholder:text-slate-400"
                                    />
                                </td>
                                <td
                                    colspan="2"
                                    class="px-5 py-4 text-xs font-medium text-slate-500 italic border-blue-100/60"
                                >
                                    Untuk menambahkan Panitia, masukkan NIM lalu
                                    tekan tambah di kanan.
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <button
                                        form="form-tambah-panitia"
                                        type="submit"
                                        class="w-full sm:w-auto text-xs font-bold tracking-wide text-blue-700 bg-blue-50 hover:bg-blue-100 px-4 py-2.5 rounded-md border border-blue-200 transition-colors flex justify-center items-center gap-2 shadow-sm"
                                    >
                                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                        Tambah
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Script SweetAlert (Tetap Sesuai Aslinya) -->
    <script>
        function hapusPanitia(formId) {
            const form = document.getElementById(formId);
            if (!form) return;

            window.Swal.fire({
                icon: 'warning',
                title: 'Hapus panitia ini?',
                text: 'Akun tersebut tidak lagi dapat mengakses rekrutmen saat ini sebagai panitia.',
                showCancelButton: true,
                confirmButtonText: 'Ya, hapus',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#dc2626',
            }).then((hasil) => {
                if (hasil.isConfirmed) form.submit();
            });
        }

        document.addEventListener('DOMContentLoaded', function () {
            @if ($errors->any())
            window.Swal.fire({
                icon: 'error',
                title: 'Panitia belum ditambahkan',
                text: @json ($errors->first()),
                confirmButtonColor: '#2563eb',
            });
            @elseif (session('error_server'))
            window.Swal.fire({
                icon: 'error',
                title: 'Tindakan tidak dapat dilakukan',
                text: @json (session('error_server')),
                confirmButtonColor: '#2563eb',
            });
            @elseif (session('success'))
            window.Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: @json (session('success')),
                confirmButtonColor: '#2563eb',
            });
            @endif
        });
    </script>
</x-app-layout>
