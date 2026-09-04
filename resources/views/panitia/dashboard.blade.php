<x-app-layout>
    <div class="p-8 max-w-5xl mx-auto">
        {{-- Header --}}
        <div
            class="flex flex-col md:flex-row md:items-center md:justify-between mb-8 pb-6 border-b border-gray-200"
        >
            <div>
                <h2 class="text-2xl font-bold text-gray-800">
                    Dashboard Panitia Rekrutmen
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Selamat bertugas,
                    <span class="font-semibold text-gray-800"
                        >{{ auth()->user()->nama_lengkap }}!</span
                    >
                </p>
            </div>
            {{-- Jika dibutuhkan, bisa tambahkan tombol aksi di sini --}}
        </div>

        {{-- Subjudul --}}
        <div class="mb-6">
            <h3 class="text-lg text-gray-700 mb-4 flex items-center">
                <span
                    class="inline-block w-2.5 h-2.5 bg-green-500 rounded-full mr-2"
                ></span>
                Menu Pintas Rekrutmen
                <span class="text-blue-700">&nbsp;Periode Aktif</span>
            </h3>
        </div>

        {{-- Grid Menu Pintas --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <a
                href="{{ route('panitia.rekrutmen.pendaftar') }}"
                class="block bg-white p-6 rounded-lg border border-gray-200 hover:border-blue-400 hover:bg-blue-50 transition-colors group"
            >
                <span
                    class="text-sm font-semibold uppercase tracking-wide text-blue-600 mb-3 block"
                >
                    Daftar Peserta
                </span>
                <div class="flex items-start gap-4">
                    <div
                        class="p-3 bg-blue-100 text-blue-600 rounded-md shrink-0"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"
                            ></path>
                        </svg>
                    </div>
                    <div>
                        <h3
                            class="text-2xl font-bold text-gray-800 group-hover:text-blue-700"
                        >
                            0
                        </h3>
                        <p class="text-sm text-gray-500 mt-1">Total pendaftar saat ini</p>
                    </div>
                </div>
            </a>

            <a
                href="{{ route('panitia.rekrutmen.seleksi') }}"
                class="block bg-white p-6 rounded-lg border border-gray-200 hover:border-yellow-400 hover:bg-yellow-50 transition-colors group"
            >
                <span
                    class="text-sm font-semibold uppercase tracking-wide text-yellow-600 mb-3 block"
                >
                    Pengerjaan Seleksi
                </span>
                <div class="flex items-start gap-4">
                    <div
                        class="p-3 bg-yellow-100 text-yellow-600 rounded-md shrink-0"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"
                            ></path>
                        </svg>
                    </div>
                    <div>
                        <h3
                            class="text-2xl font-bold text-gray-800 group-hover:text-yellow-700"
                        >
                            Pendaftaran
                        </h3>
                        <p class="text-sm text-gray-500 mt-1">Tahapan seleksi saat ini</p>
                    </div>
                </div>
            </a>

            <a
                href="{{ route('panitia.rekrutmen.update') }}"
                class="block bg-white p-6 rounded-lg border border-gray-200 hover:border-green-400 hover:bg-green-50 transition-colors group"
            >
                <span
                    class="text-sm font-semibold uppercase tracking-wide text-green-600 mb-3 block"
                >
                    Update Informasi
                </span>
                <div class="flex items-start gap-4">
                    <div
                        class="p-3 bg-green-100 text-green-600 rounded-md shrink-0"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                            ></path>
                        </svg>
                    </div>
                    <div>
                        <h3
                            class="text-2xl font-bold text-gray-800 group-hover:text-green-700"
                        >
                            Pengaturan
                        </h3>
                        <p class="text-sm text-gray-500 mt-1">Ubah/ update informasi rekrutmen</p>
                    </div>
                </div>
            </a>
        </div>
    </div>
</x-app-layout>

{{-- SweetAlert2 --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        @if (session('success_update'))
        Swal.fire({
            icon: 'success',
            title: 'Pembaruan Berhasil!',
            text: '{{
        session(
            'success_update',
        )
    }}',
            confirmButtonColor: '#2563eb',
            timer: 4000,
        });
        @endif

        @if (session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil',
            text: @json(session('success')),
            confirmButtonColor: '#2563eb',
        });
        @endif

        @if (session('error_server'))
        Swal.fire({
            icon: 'info',
            title: 'Rekrutmen belum tersedia',
            text: @json(session('error_server')),
            confirmButtonColor: '#2563eb',
        });
        @endif
    });
</script>
