<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{
                    __(
                        'Seleksi Pendaftar',
                    )
                }}
            </h2>
            <span
                class="px-3 py-1 text-sm font-semibold {{ $isOrganisasi ? 'text-red-800 bg-red-100' : 'text-blue-800 bg-blue-100' }} rounded-full"
            >
                Akses: {{ $role }}
            </span>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div
                class="mb-6 bg-white overflow-hidden shadow-sm sm:rounded-lg p-4 border-l-4 {{ $isOrganisasi ? 'border-red-500' : 'border-blue-500' }}"
            >
                <p class="text-gray-700">Selamat bertugas, <span class="font-bold">{{ $namaPengguna }}</span>. Di bawah ini adalah daftar mahasiswa yang telah mengajukan pendaftaran.</p>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead
                            class="text-xs text-gray-700 uppercase bg-gray-50 border-b"
                        >
                            <tr>
                                <th scope="col" class="px-6 py-3">NIM</th>
                                <th scope="col" class="px-6 py-3">
                                    Nama Lengkap
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Pilihan Divisi
                                </th>
                                <th scope="col" class="px-6 py-3 text-center">
                                    Status Berkas
                                </th>
                                <th scope="col" class="px-6 py-3 text-center">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="bg-white border-b hover:bg-gray-50">
                                <td class="px-6 py-4 font-medium text-gray-900">
                                    222212345
                                </td>
                                <td class="px-6 py-4">Fulan Bin Fulan</td>
                                <td class="px-6 py-4">Humas</td>
                                <td class="px-6 py-4 text-center">
                                    <span
                                        class="px-2 py-1 text-xs font-semibold text-yellow-800 bg-yellow-100 rounded-full"
                                        >Menunggu Review</span
                                    >
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <a
                                        href="#"
                                        class="text-blue-600 hover:text-blue-900 font-medium mr-3"
                                        >Lihat CV</a
                                    >
                                    <a
                                        href="#"
                                        class="text-green-600 hover:text-green-900 font-medium"
                                        >Beri Nilai</a
                                    >
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
