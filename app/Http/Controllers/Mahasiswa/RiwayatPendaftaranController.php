<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Pendaftaran;
use App\Models\PeriodeRekrutmen;
use App\Models\Tahapan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class RiwayatPendaftaranController extends Controller
{
    /** Menampilkan rekrutmen tertutup yang diikuti dan tidak diikuti mahasiswa. */
    public function index()
    {
        $riwayatDiikuti = Pendaftaran::query()
            ->with([
                'pilihanJabatan1.periode.organisasi',
                'pilihanJabatan2',
            ])
            ->where('nim', Auth::user()->nim)
            ->whereHas('pilihanJabatan1.periode', fn ($query) => $query->where('status_aktif', 0))
            ->latest('created_at')
            ->get();

        $periodeYangDiikuti = $riwayatDiikuti
            ->pluck('pilihanJabatan1.periode_rekrutmen_id')
            ->filter()
            ->unique()
            ->all();

        $riwayatTidakDiikuti = PeriodeRekrutmen::query()
            ->with('organisasi')
            ->where('status_aktif', 0)
            ->when(
                $periodeYangDiikuti !== [],
                fn ($query) => $query->whereNotIn('id', $periodeYangDiikuti),
            )
            ->latest('id')
            ->get();

        return view('mahasiswa.riwayat.index', compact('riwayatDiikuti', 'riwayatTidakDiikuti'));
    }

    /**
     * Menampilkan pengumuman pada rekrutmen tertutup yang tidak diikuti.
     * Tahapan seleksi dan penugasannya sengaja tidak dimuat.
     */
    public function showTahapanPengumuman(int $periode_id)
    {
        $periode = PeriodeRekrutmen::with('organisasi')->findOrFail($periode_id);

        abort_unless((int) $periode->status_aktif === 0, 404);

        $tahapans = Tahapan::query()
            ->where('periode_rekrutmen_id', $periode->id)
            ->where('jenis_tahapan', 'pengumuman')
            ->orderBy('urutan_tahapan')
            ->get()
            ->map(function (Tahapan $tahapan) {
                $tahapan->parsed_mulai = Carbon::parse($tahapan->waktu_mulai);
                $tahapan->parsed_berakhir = Carbon::parse($tahapan->waktu_berakhir);
                $tahapan->is_waktu_tunggal = $tahapan->parsed_mulai->equalTo($tahapan->parsed_berakhir);
                $tahapan->is_future = $tahapan->parsed_mulai->isFuture();

                $lampiran = is_array($tahapan->lampiran_tahapan)
                    ? $tahapan->lampiran_tahapan
                    : json_decode($tahapan->lampiran_tahapan ?? '[]', true);
                $tahapan->lampiran_pengumuman = array_values(array_filter((array) $lampiran));

                return $tahapan;
            });

        return view('mahasiswa.riwayat.pengumuman', compact('periode', 'tahapans'));
    }
}
