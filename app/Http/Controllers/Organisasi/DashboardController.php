<?php

namespace App\Http\Controllers\Organisasi;

use App\Http\Controllers\Controller;
use App\Models\Pendaftaran;
use App\Models\PeriodeRekrutmen;
use App\Models\Tahapan;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $periodeAktif = PeriodeRekrutmen::query()
            ->withCount('panitia')
            ->where('organisasi_id', Auth::guard('organisasi')->id())
            ->whereIn('status_aktif', [1, 2])
            ->latest('id')
            ->first();

        $jumlahPeserta = $periodeAktif
            ? Pendaftaran::query()
                ->whereHas('pilihanJabatan1', fn ($query) => $query->where('periode_rekrutmen_id', $periodeAktif->id))
                ->count()
            : 0;

        $tahapanSeleksiSaatIni = $periodeAktif
            ? Tahapan::query()
                ->where('periode_rekrutmen_id', $periodeAktif->id)
                ->seleksi()
                ->where('waktu_mulai', '<=', now())
                ->where('waktu_berakhir', '>', now())
                ->orderBy('urutan_tahapan')
                ->first()
            : null;

        return view('organisasi.dashboard', compact('periodeAktif', 'jumlahPeserta', 'tahapanSeleksiSaatIni'));
    }
}
