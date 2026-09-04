@php
    $namaRute = request()->route()?->getName() ?? '';
    $petaBreadcrumb = [
        'organisasi.dashboard' => [['Dashboard', 'organisasi.dashboard']],
        'organisasi.buka-rekrutmen.index' => [['Dashboard', 'organisasi.dashboard'], ['Buka Rekrutmen', null]],
        'organisasi.rekrutmen.update' => [['Dashboard', 'organisasi.dashboard'], ['Rekrutmen Saat Ini', null], ['Update Informasi', null]],
        'organisasi.rekrutmen.panitia' => [['Dashboard', 'organisasi.dashboard'], ['Rekrutmen Saat Ini', null], ['Daftar Panitia', null]],
        'organisasi.rekrutmen.pendaftar' => [['Dashboard', 'organisasi.dashboard'], ['Rekrutmen Saat Ini', null], ['Daftar Peserta', null]],
        'organisasi.rekrutmen.seleksi' => [['Dashboard', 'organisasi.dashboard'], ['Rekrutmen Saat Ini', null], ['Pengerjaan Seleksi', null]],
        'organisasi.rekrutmen.seleksi.tahapan' => [['Dashboard', 'organisasi.dashboard'], ['Pengerjaan Seleksi', 'organisasi.rekrutmen.seleksi'], ['Tahapan Seleksi', null]],
        'organisasi.rekrutmen.seleksi.jawaban' => [['Dashboard', 'organisasi.dashboard'], ['Pengerjaan Seleksi', 'organisasi.rekrutmen.seleksi'], ['Hasil Seleksi', null]],
        'organisasi.rekrutmen.seleksi.wawancara' => [['Dashboard', 'organisasi.dashboard'], ['Pengerjaan Seleksi', 'organisasi.rekrutmen.seleksi'], ['Hasil Seleksi', null], ['Wawancara', null]],
        'organisasi.riwayat.index' => [['Dashboard', 'organisasi.dashboard'], ['Riwayat Rekrutmen', null]],
        'organisasi.riwayat.periode' => [['Dashboard', 'organisasi.dashboard'], ['Riwayat Rekrutmen', 'organisasi.riwayat.index'], ['Detail Periode', null]],
        'organisasi.riwayat.jabatan' => [['Dashboard', 'organisasi.dashboard'], ['Riwayat Rekrutmen', 'organisasi.riwayat.index'], ['Detail Jabatan', null]],
        'organisasi.riwayat.tahapan' => [['Dashboard', 'organisasi.dashboard'], ['Riwayat Rekrutmen', 'organisasi.riwayat.index'], ['Detail Tahapan', null]],
        'panitia.dashboard' => [['Dashboard', 'panitia.dashboard']],
        'panitia.rekrutmen.update' => [['Dashboard', 'panitia.dashboard'], ['Rekrutmen Saat Ini', null], ['Update Informasi', null]],
        'panitia.rekrutmen.pendaftar' => [['Dashboard', 'panitia.dashboard'], ['Rekrutmen Saat Ini', null], ['Daftar Peserta', null]],
        'panitia.rekrutmen.seleksi' => [['Dashboard', 'panitia.dashboard'], ['Rekrutmen Saat Ini', null], ['Pengerjaan Seleksi', null]],
        'panitia.rekrutmen.seleksi.tahapan' => [['Dashboard', 'panitia.dashboard'], ['Pengerjaan Seleksi', 'panitia.rekrutmen.seleksi'], ['Tahapan Seleksi', null]],
        'panitia.rekrutmen.seleksi.jawaban' => [['Dashboard', 'panitia.dashboard'], ['Pengerjaan Seleksi', 'panitia.rekrutmen.seleksi'], ['Hasil Seleksi', null]],
        'panitia.rekrutmen.seleksi.wawancara' => [['Dashboard', 'panitia.dashboard'], ['Pengerjaan Seleksi', 'panitia.rekrutmen.seleksi'], ['Hasil Seleksi', null], ['Wawancara', null]],
        'panitia.riwayat.index' => [['Dashboard', 'panitia.dashboard'], ['Riwayat Rekrutmen', null]],
        'panitia.riwayat.periode' => [['Dashboard', 'panitia.dashboard'], ['Riwayat Rekrutmen', 'panitia.riwayat.index'], ['Detail Periode', null]],
        'panitia.riwayat.jabatan' => [['Dashboard', 'panitia.dashboard'], ['Riwayat Rekrutmen', 'panitia.riwayat.index'], ['Detail Jabatan', null]],
        'panitia.riwayat.tahapan' => [['Dashboard', 'panitia.dashboard'], ['Riwayat Rekrutmen', 'panitia.riwayat.index'], ['Detail Tahapan', null]],
        'mahasiswa.rekrutmen.index' => [['Daftar Rekrutmen', null]],
        'mahasiswa.rekrutmen.info' => [['Daftar Rekrutmen', 'mahasiswa.rekrutmen.index'], ['Informasi Rekrutmen', null]],
        'mahasiswa.rekrutmen.daftar' => [['Daftar Rekrutmen', 'mahasiswa.rekrutmen.index'], ['Pendaftaran', null]],
        'mahasiswa.rekrutmen.diikuti.index' => [['Rekrutmen Diikuti', null]],
        'mahasiswa.rekrutmen.diikuti.tahapan' => [['Rekrutmen Diikuti', 'mahasiswa.rekrutmen.diikuti.index'], ['Tahapan Seleksi', null]],
        'mahasiswa.rekrutmen.diikuti.tugas_detail' => [['Rekrutmen Diikuti', 'mahasiswa.rekrutmen.diikuti.index'], ['Tahapan Seleksi', 'mahasiswa.rekrutmen.diikuti.tahapan'], ['Detail Tugas', null]],
        'mahasiswa.riwayat.index' => [['Riwayat Pendaftaran', null]],
    ];
    $items = $petaBreadcrumb[$namaRute] ?? [['Halaman', null]];
@endphp

<nav aria-label="Breadcrumb" class="flex min-w-0 items-center gap-2 overflow-x-auto whitespace-nowrap text-xs font-semibold text-slate-500">
    @foreach ($items as $index => [$label, $routeName])
        @if ($index > 0)
            <svg class="h-3.5 w-3.5 shrink-0 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 18 6-6-6-6" />
            </svg>
        @endif

        @php
            $parameterRute = $routeName === 'mahasiswa.rekrutmen.diikuti.tahapan'
                ? ['id' => request()->route('id') ?? request()->route('pendaftaran')]
                : [];
        @endphp
        @if ($routeName && Route::has($routeName))
            <a href="{{ route($routeName, $parameterRute) }}" class="rounded px-1 py-0.5 hover:bg-blue-50 hover:text-blue-700 transition-colors">
                {{ $label }}
            </a>
        @else
            <span class="{{ $loop->last ? 'text-slate-700' : '' }}">{{ $label }}</span>
        @endif
    @endforeach
</nav>
