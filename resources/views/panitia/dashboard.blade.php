<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{
                __(
                    'Dashboard Panitia',
                )
            }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h4 class="text-lg font-bold text-gray-900">
                        Selamat Bertugas Panitia, {{ auth()->user()->nama_lengkap }}!
                    </h4>
                    <p class="text-gray-500 mt-2">
                        Jabatan Anda:
                        <span
                            class="font-semibold text-gray-800"
                            >{{ $dataKepanitiaan->jabatan }}</span
                        >
                        di Organisasi ID: {{ $dataKepanitiaan->organisasi_id }}
                    </p>
                    <p class="mt-4 text-sm text-gray-600">Di sini nanti berisi tabel pendaftar yang harus Anda seleksi.</p>
                    <div class="mt-6">
                        <a
                            href="{{ route('panitia.seleksi.index') }}"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                        >
                            Buka Halaman Seleksi
                        </a>
                    </div>
                    {{
                        __(
                            "You're logged in!",
                        )
                    }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
