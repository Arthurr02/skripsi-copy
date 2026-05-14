<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{
                __(
                    'Dashboard Mahasiswa',
                )
            }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h4 class="text-lg font-bold text-gray-900">
                        Halo Pendaftar, {{ auth()->user()->nama_lengkap }}!
                    </h4>
                    <p class="text-gray-500 mt-2">Di sini nanti berisi tombol untuk memilih divisi dan melengkapi berkas pendaftaran.</p>
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
