<?php

namespace App\View\Components;

use Illuminate\Http\Request;
use Illuminate\View\Component;
use Illuminate\View\View;

class Breadcrumb extends Component
{
    /** @var array<int, array{label: string, route: ?string, parameters: array<string, mixed>}> */
    public array $items;

    public function __construct(Request $request)
    {
        $route = $request->route();
        $routeName = $route?->getName() ?? '';
        $parameter = fn (string $name) => $route?->parameter($name);

        $this->items = match ($routeName) {
            'organisasi.dashboard' => $this->trail(['Dashboard']),
            'organisasi.daftar-anggota.index' => $this->trail(['Dashboard', 'organisasi.dashboard'], ['Upload Daftar Anggota']),
            'organisasi.buka-rekrutmen.index' => $this->trail(['Dashboard', 'organisasi.dashboard'], ['Buka Rekrutmen']),
            'organisasi.rekrutmen.update' => $this->trail(['Dashboard', 'organisasi.dashboard'], ['Rekrutmen Saat Ini'], ['Update Informasi']),
            'organisasi.rekrutmen.panitia' => $this->trail(['Dashboard', 'organisasi.dashboard'], ['Rekrutmen Saat Ini'], ['Daftar Panitia']),
            'organisasi.rekrutmen.pendaftar' => $this->trail(['Dashboard', 'organisasi.dashboard'], ['Rekrutmen Saat Ini'], ['Daftar Peserta']),
            'organisasi.rekrutmen.seleksi' => $this->trail(['Dashboard', 'organisasi.dashboard'], ['Pengerjaan Seleksi']),
            'organisasi.rekrutmen.seleksi.tahapan' => $this->trail(['Dashboard', 'organisasi.dashboard'], ['Pengerjaan Seleksi', 'organisasi.rekrutmen.seleksi'], ['Tahapan Seleksi']),
            'organisasi.rekrutmen.seleksi.jawaban' => $this->trail(['Dashboard', 'organisasi.dashboard'], ['Pengerjaan Seleksi', 'organisasi.rekrutmen.seleksi'], ['Hasil Seleksi']),
            'organisasi.rekrutmen.seleksi.wawancara' => $this->trail(['Dashboard', 'organisasi.dashboard'], ['Pengerjaan Seleksi', 'organisasi.rekrutmen.seleksi'], ['Hasil Seleksi'], ['Wawancara']),
            'organisasi.riwayat.index' => $this->trail(['Dashboard', 'organisasi.dashboard'], ['Riwayat Rekrutmen']),
            'organisasi.riwayat.periode' => $this->trail(['Dashboard', 'organisasi.dashboard'], ['Riwayat Rekrutmen', 'organisasi.riwayat.index'], ['Detail Periode']),
            'organisasi.riwayat.jabatan' => $this->trail(['Dashboard', 'organisasi.dashboard'], ['Riwayat Rekrutmen', 'organisasi.riwayat.index'], ['Detail Periode', 'organisasi.riwayat.periode', ['periode_id' => $parameter('periode_id')]], ['Detail Jabatan']),
            'organisasi.riwayat.tahapan' => $this->trail(['Dashboard', 'organisasi.dashboard'], ['Riwayat Rekrutmen', 'organisasi.riwayat.index'], ['Detail Periode', 'organisasi.riwayat.periode', ['periode_id' => $parameter('periode_id')]], ['Detail Tahapan']),

            'panitia.dashboard' => $this->trail(['Dashboard']),
            'panitia.rekrutmen.update' => $this->trail(['Dashboard', 'panitia.dashboard'], ['Rekrutmen Saat Ini'], ['Update Informasi']),
            'panitia.rekrutmen.pendaftar' => $this->trail(['Dashboard', 'panitia.dashboard'], ['Rekrutmen Saat Ini'], ['Daftar Peserta']),
            'panitia.rekrutmen.seleksi' => $this->trail(['Dashboard', 'panitia.dashboard'], ['Pengerjaan Seleksi']),
            'panitia.rekrutmen.seleksi.tahapan' => $this->trail(['Dashboard', 'panitia.dashboard'], ['Pengerjaan Seleksi', 'panitia.rekrutmen.seleksi'], ['Tahapan Seleksi']),
            'panitia.rekrutmen.seleksi.jawaban' => $this->trail(['Dashboard', 'panitia.dashboard'], ['Pengerjaan Seleksi', 'panitia.rekrutmen.seleksi'], ['Hasil Seleksi']),
            'panitia.rekrutmen.seleksi.wawancara' => $this->trail(['Dashboard', 'panitia.dashboard'], ['Pengerjaan Seleksi', 'panitia.rekrutmen.seleksi'], ['Hasil Seleksi'], ['Wawancara']),
            'panitia.riwayat.index' => $this->trail(['Dashboard', 'panitia.dashboard'], ['Riwayat Rekrutmen']),
            'panitia.riwayat.periode' => $this->trail(['Dashboard', 'panitia.dashboard'], ['Riwayat Rekrutmen', 'panitia.riwayat.index'], ['Detail Periode']),
            'panitia.riwayat.jabatan' => $this->trail(['Dashboard', 'panitia.dashboard'], ['Riwayat Rekrutmen', 'panitia.riwayat.index'], ['Detail Periode', 'panitia.riwayat.periode', ['periode_id' => $parameter('periode_id')]], ['Detail Jabatan']),
            'panitia.riwayat.tahapan' => $this->trail(['Dashboard', 'panitia.dashboard'], ['Riwayat Rekrutmen', 'panitia.riwayat.index'], ['Detail Periode', 'panitia.riwayat.periode', ['periode_id' => $parameter('periode_id')]], ['Detail Tahapan']),

            'dosen.dashboard' => $this->trail(['Data Organisasi']),
            'dosen.data-organisasi.index' => $this->trail(['Data Organisasi']),

            'mahasiswa.rekrutmen.index' => $this->trail(['Daftar Rekrutmen']),
            'mahasiswa.rekrutmen.info' => $this->trail(['Daftar Rekrutmen', 'mahasiswa.rekrutmen.index'], ['Informasi Rekrutmen']),
            'mahasiswa.rekrutmen.daftar' => $this->trail(['Daftar Rekrutmen', 'mahasiswa.rekrutmen.index'], ['Pendaftaran']),
            'mahasiswa.rekrutmen.diikuti.index' => $this->trail(['Rekrutmen Diikuti']),
            'mahasiswa.rekrutmen.diikuti.tahapan' => $this->trail(['Rekrutmen Diikuti', 'mahasiswa.rekrutmen.diikuti.index'], ['Tahapan Seleksi']),
            'mahasiswa.rekrutmen.diikuti.tugas_detail' => $this->trail(['Rekrutmen Diikuti', 'mahasiswa.rekrutmen.diikuti.index'], ['Tahapan Seleksi', 'mahasiswa.rekrutmen.diikuti.tahapan', ['id' => $parameter('pendaftaran')]], ['Detail Tugas']),
            'mahasiswa.riwayat.index' => $this->trail(['Riwayat Rekrutmen']),
            'mahasiswa.riwayat.diikuti.tahapan' => $this->trail(['Riwayat Rekrutmen', 'mahasiswa.riwayat.index'], ['Tahapan Seleksi']),
            'mahasiswa.riwayat.diikuti.tugas' => $this->trail(['Riwayat Rekrutmen', 'mahasiswa.riwayat.index'], ['Tahapan Seleksi', 'mahasiswa.riwayat.diikuti.tahapan', ['id' => $parameter('pendaftaran')]], ['Detail Tugas']),
            'mahasiswa.riwayat.pengumuman' => $this->trail(['Riwayat Rekrutmen', 'mahasiswa.riwayat.index'], ['Pengumuman Rekrutmen']),
            default => $this->trail(['Halaman']),
        };
    }

    public function render(): View
    {
        return view('components.breadcrumb');
    }

    /** @param array<int, array{0: string, 1?: string, 2?: array<string, mixed>}> $segments */
    private function trail(array ...$segments): array
    {
        return array_map(
            fn (array $segment) => [
                'label' => $segment[0],
                'route' => $segment[1] ?? null,
                'parameters' => $segment[2] ?? [],
            ],
            $segments,
        );
    }
}
