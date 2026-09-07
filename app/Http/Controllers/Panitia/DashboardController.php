<?php

namespace App\Http\Controllers\Panitia;

use App\Http\Controllers\Controller;
use App\Models\Panitia;
use App\Models\Pendaftaran;
use App\Models\Tahapan;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $user = Auth::user();
        $kepanitiaanAktif = Panitia::query()
            ->with('periode')
            ->where('nim', $user->nim)
            ->whereHas('periode', fn ($query) => $query->whereIn('status_aktif', [1, 2]))
            ->latest('id')
            ->first();
        $periodeAktif = $kepanitiaanAktif?->periode;

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

        return view('panitia.dashboard', compact('user', 'periodeAktif', 'jumlahPeserta', 'tahapanSeleksiSaatIni'));
    }
}
