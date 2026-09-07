<x-app-layout>
    @php
        $tahapanInfo = $tahapanSatu ?? null;
        $namaTahapan = $tahapanInfo->nama_tahapan ?? 'Pendaftaran & Seleksi Berkas';
        $deskripsiUmum =
            $tahapanInfo->deskripsi_tahapan ??
            'Silakan lengkapi formulir pendaftaran dan unggah berkas yang diminta.';

        $rawMulai =
            $tahapanInfo && $tahapanInfo->waktu_mulai
                ? \Carbon\Carbon::parse($tahapanInfo->waktu_mulai)
                : null;
        $rawBerakhir =
            $tahapanInfo && $tahapanInfo->waktu_berakhir
                ? \Carbon\Carbon::parse($tahapanInfo->waktu_berakhir)
                : null;
        $isWaktuTunggal =
            $rawMulai && $rawBerakhir ? $rawMulai->equalTo($rawBerakhir) : false;

        $tugasMapping = [];
        if ($tahapanInfo && $tahapanInfo->tugas) {
            foreach ($tahapanInfo->tugas as $tugas) {
                $lampiranData = $tugas->lampiran_tugas;
                $lampiran = is_string($lampiranData)
                    ? json_decode($lampiranData, true)
                    : $lampiranData;

                $tugasMapping[$tugas->jabatan_id] = [
                    'tipe_tugas' => $tugas->tipe_tugas ?? 'pengisian_form',
                    'deskripsi' => $tugas->deskripsi_tugas ?: $deskripsiUmum,
                    'format_proyek' => !empty($tugas->tipe_jawaban_tugas)
                        ? explode(',', $tugas->tipe_jawaban_tugas)
                        : [],
                    'berkas_template' => $lampiran['berkas'] ?? [],
                    'form' => $lampiran['form'] ?? [],
                ];
            }
        }

        $organisasi = $rekrutmen->organisasi;
        $namaOrganisasi = $organisasi->nama_organisasi ?? 'Organisasi';

        $avatarUrl = '';
        if ($organisasi) {
            if (!empty($organisasi->avatar_google)) {
                $avatarUrl = str_replace(
                    'http://',
                    'https://',
                    $organisasi->avatar_google,
                );
            } elseif (!empty($organisasi->lampiran_logo)) {
                $avatarUrl = asset('storage/' . $organisasi->lampiran_logo);
            }
        }

        $bannerData = $rekrutmen->lampiran_banner;
        $bannerArray = is_string($bannerData)
            ? json_decode($bannerData, true)
            : $bannerData;
        $bannerPath =
            is_array($bannerArray) && count($bannerArray) > 0 ? $bannerArray[0] : null;

        $groupedJabatan = [];
        if (isset($jabatans)) {
            foreach ($jabatans as $jabatan) {
                $namaPosisi =
                    empty($jabatan->nama_posisi) || $jabatan->nama_posisi === '-'
                        ? 'Tanpa Divisi Khusus'
                        : $jabatan->nama_posisi;
                if (!isset($groupedJabatan[$namaPosisi])) {
                    $groupedJabatan[$namaPosisi] = [];
                }
                $groupedJabatan[$namaPosisi][] = [
                    'id' => $jabatan->id,
                    'nama_jabatan' => $jabatan->nama_jabatan,
                ];
            }
        }
    @endphp

    <!-- Menyimpan JSON dengan aman untuk Alpine.js -->
    <script>
        window.tugasMappingData = {!!
            json_encode(
                $tugasMapping,
            )
        !!};
    </script>

    <!-- Background -->
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

    <!-- Alpine.js State Management -->
    <div
        x-data="{ 
            tab: 1,
            pilihan1: '{{ old('jabatan_1_id') }}',
            pilihan2: '{{ old('jabatan_2_id') }}',
            pilihan1Name: '',
            pilihan1Position: '',
            activePosisi1: '',
            activePosisi2: '',
            tugasMap: window.tugasMappingData,
            currentTugas: null,
            
            init() {
                this.$watch('pilihan1', (value) => {
                    if (this.pilihan2 === value) {
                        this.pilihan2 = '';
                    }
                    this.updatePilihan1Name();
                });
                if(this.pilihan1) this.updatePilihan1Name();
            },

            updatePilihan1Name() {
                if(!this.pilihan1) {
                    this.pilihan1Name = '';
                    this.pilihan1Position = '';
                    return;
                }
                setTimeout(() => {
                    const selectedRadio = document.querySelector(`input[name='radio_pilihan1'][value='${this.pilihan1}']`);
                    if(selectedRadio) {
                        this.pilihan1Name = selectedRadio.dataset.name;
                        this.pilihan1Position = selectedRadio.dataset.position;
                    }
                }, 50);
            },
            
            lanjutKeTugas() {
                if (!this.pilihan1) {
                    Swal.fire({ 
                        icon: 'warning', 
                        title: 'Pilih Formasi Utama', 
                        text: 'Pilihan 1 (Prioritas Utama) wajib diisi sebelum melanjutkan.', 
                        confirmButtonColor: '#2563eb',
                        customClass: { popup: 'rounded-lg border border-slate-200 shadow-sm font-sans', confirmButton: 'px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm font-bold' }
                    });
                    return;
                }

                if (this.pilihan2 && this.pilihan1 === this.pilihan2) {
                    Swal.fire({ 
                        icon: 'error', 
                        title: 'Pilihan Ganda', 
                        text: 'Pilihan 1 dan Pilihan 2 tidak boleh sama. Pilih formasi alternatif yang berbeda.', 
                        confirmButtonColor: '#2563eb',
                        customClass: { popup: 'rounded-lg border border-slate-200 shadow-sm font-sans', confirmButton: 'px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm font-bold' }
                    });
                    return;
                }
                
                this.updatePilihan1Name();
                
                this.currentTugas = this.tugasMap[this.pilihan1] || null;

                this.tab = 2;
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        }"
        class="py-4 sm:py-8 px-4 sm:px-8 md:px-10 max-w-5xl mx-auto relative z-10 my-6 sm:my-10 space-y-8"
    >
        @include ('mahasiswa.rekrutmen.pendaftaran.partials.header')
        @include ('mahasiswa.rekrutmen.pendaftaran.partials.stepper')

        <!-- AREA FORMULIR -->
        <form
            id="formPendaftaran"
            action="{{ route('mahasiswa.rekrutmen.submit', $rekrutmen->id) }}"
            method="POST"
            enctype="multipart/form-data"
        >
            @csrf

            <div class="relative">
                @include ('mahasiswa.rekrutmen.pendaftaran.partials.tab-formasi')
                @include ('mahasiswa.rekrutmen.pendaftaran.partials.tab-formulir')
            </div>
        </form>
    </div>

    @include ('mahasiswa.rekrutmen.pendaftaran.partials.scripts')
</x-app-layout>
