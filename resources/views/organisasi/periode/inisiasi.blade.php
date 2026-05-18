<x-app-layout>
    <div class="p-8">
        <h2 class="text-2xl font-bold mb-6 text-gray-800">Buka Rekrutmen</h2>

        <form
            action="{{ route('organisasi.periode.store_inisiasi') }}"
            method="POST"
            class="space-y-6 max-w-2xl bg-white p-6 rounded-lg shadow-sm border border-gray-200"
        >
            @csrf

            <div>
                <label class="block font-bold text-gray-700 mb-2"
                    >Periode Rekrutmen</label
                >
                @php
                    $tahunSekarang = date('Y');
                @endphp
                <select
                    name="tahun_periode"
                    class="border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm w-full md:w-1/2"
                    required
                >
                    @for ($i = 0; $i <= 5; $i++)
                        @php
                            $tahunAwal = $tahunSekarang + $i;
                            $tahunAkhir = $tahunAwal + 1;
                            $formatPeriode = $tahunAwal . '/' . $tahunAkhir;
                        @endphp
                        <option value="{{ $formatPeriode }}">
                            {{ $formatPeriode }}
                        </option>
                    @endfor
                </select>
                <p class="mt-1 text-sm text-gray-500">Pilih tahun periode rekrutmen yang ingin diadakan.</p>
            </div>

            <hr class="border-gray-200" />

            <div
                x-data="{ 
                panitia: [{ email: '', pesanError: '' }],
                
                // Fungsi validasi real-time saat kursor keluar (blur)
                validasiEmail(index) {
                    const nilai = this.panitia[index].email;
                    const regexSTIS = /^\d{9}@stis\.ac\.id$/;
                    
                    if (nilai === '') {
                        this.panitia[index].pesanError = 'Email wajib diisi.';
                    } else if (!regexSTIS.test(nilai)) {
                        this.panitia[index].pesanError = 'Format salah! Harus 9 digit angka NIM diikuti @stis.ac.id';
                    } else {
                        this.panitia[index].pesanError = ''; // Validasi sukses
                    }
                },
                
                tambahBaris() {
                    this.panitia.push({ email: '', pesanError: '' });
                },
                
                hapusBaris(index) {
                    this.panitia.splice(index, 1);
                }
            }"
            >
                <label class="block font-bold text-gray-700 mb-2"
                    >Daftar Email Panitia</label
                >
                <p class="mb-4 text-sm text-gray-500">Daftar email kampus mahasiswa yang ingin ditunjuk sebagai panitia.</p>

                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr
                            class="bg-gray-50 border-b border-t border-gray-200"
                        >
                            <th
                                class="px-4 py-2 text-sm font-semibold text-gray-600"
                            >
                                Email Kampus Mahasiswa
                            </th>
                            <th
                                class="px-4 py-2 text-sm font-semibold text-gray-600 w-24 text-center"
                            >
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <template
                            x-for="(baris, index) in panitia"
                            :key="index"
                        >
                            <tr class="border-b border-gray-100 align-top">
                                <td class="px-2 py-3">
                                    <input
                                        type="text"
                                        name="email_panitia[]"
                                        x-model="baris.email"
                                        @blur="validasiEmail(index)"
                                        @input="baris.pesanError = ''"
                                        placeholder="contoh: 222212602@stis.ac.id"
                                        class="w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm transition-colors"
                                        :class="baris.pesanError
                                            ? 'border-red-500 focus:border-red-500 focus:ring-red-500 bg-red-50'
                                            : ''"
                                        required
                                    />

                                    <p
                                        x-show="baris.pesanError"
                                        x-text="baris.pesanError"
                                        class="text-red-500 text-xs mt-1 font-medium"
                                        style="display: none"
                                    ></p>
                                </td>
                                <td class="px-2 py-3 text-center pt-5">
                                    <button
                                        type="button"
                                        @click="hapusBaris(index)"
                                        x-show="panitia.length > 1"
                                        class="text-red-500 hover:text-red-700 font-medium text-sm transition-colors"
                                    >
                                        Hapus
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>

                <div class="mt-3">
                    <button
                        type="button"
                        @click="tambahBaris()"
                        class="inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-800 transition-colors"
                    >
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Tambah Email Panitia
                    </button>
                </div>

                <div class="pt-6 flex justify-end">
                    <button
                        type="submit"
                        class="bg-blue-600 text-white px-6 py-2 rounded-md font-semibold hover:bg-blue-700 transition-colors shadow-sm disabled:opacity-50 disabled:cursor-not-allowed"
                        :disabled="panitia.some((b) => b.pesanError !== '')"
                    >
                        Buka Rekrutmen
                    </button>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // 1. TANGKAP ERROR SERVER (Jika query database gagal)
        @if (session('error_server'))
        Swal.fire({
            icon: 'error',
            title: 'Sistem Terkendala',
            text: '{!!
        session(
            'error_server',
        )
    !!}',
            confirmButtonColor: '#3b82f6',
        });
        @endif

        // 2. TANGKAP ERROR VALIDASI LARAVEL (Jika email tidak ditemukan/format salah)
        @if ($errors->any())
        Swal.fire({
            icon: 'warning',
            title: 'Validasi Gagal',
            html: `
                    <ul class="text-left text-red-500 text-sm font-medium">
                        @foreach ($errors->all() as $error)
                            <li>- {{ $error }}</li>
                        @endforeach
                    </ul>
                `,
            confirmButtonColor: '#3b82f6',
        });
        @endif

        // 3. TANGKAP SINYAL SUKSES & MUNCULKAN POPUP KEPUTUSAN
        @if (session('success_inisiasi'))
        Swal.fire({
            icon: 'success',
            title: 'Inisiasi Berhasil!',
            text: 'Periode {{
        session(
            'tahun_periode',
        )
    }} berhasil dibuat dan panitia telah terdaftar.',
            showCancelButton: true,
            confirmButtonColor: '#2563eb', // Warna biru untuk aksi utama
            cancelButtonColor: '#6b7280', // Warna abu-abu untuk aksi sekunder
            confirmButtonText: 'Lengkapi Skema Langsung',
            cancelButtonText: 'Nanti Saja (Simpan Draf)',
            allowOutsideClick: false, // Memaksa user memilih salah satu tombol
        }).then((result) => {
            if (result.isConfirmed) {
                // Arahkan ke halaman pengaturan tahapan dan penugasan
                window.location.href = '{{ route('organisasi.periode.skema', session('periode_id')) }}';
            } else {
                // Arahkan kembali ke Dashboard
                window.location.href = '{{ route('organisasi.dashboard') }}';
            }
        });
        @endif
    });
</script>
