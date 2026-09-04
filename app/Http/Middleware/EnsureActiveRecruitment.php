<?php

namespace App\Http\Middleware;

use App\Models\Panitia;
use App\Models\PeriodeRekrutmen;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureActiveRecruitment
{
    /**
     * Pastikan setiap operasi pada rekrutmen saat ini selalu mempunyai periode
     * berstatus dibuka atau berjalan. Pemeriksaan ini juga melindungi URL yang
     * diakses langsung, bukan hanya tautan pada sidebar.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $periodeAktif = $this->periodeAktifUntukAktor();

        if ($periodeAktif) {
            return $next($request);
        }

        $ruteDashboard = Auth::guard('organisasi')->check()
            ? 'organisasi.dashboard'
            : 'panitia.dashboard';

        return redirect()
            ->route($ruteDashboard)
            ->with('error_server', 'Belum ada rekrutmen yang sedang berjalan. Buka rekrutmen terlebih dahulu.');
    }

    private function periodeAktifUntukAktor(): ?PeriodeRekrutmen
    {
        if (Auth::guard('organisasi')->check()) {
            return PeriodeRekrutmen::query()
                ->where('organisasi_id', Auth::guard('organisasi')->id())
                ->whereIn('status_aktif', [1, 2])
                ->latest('id')
                ->first();
        }

        $mahasiswa = Auth::user();
        if (! $mahasiswa) {
            return null;
        }

        $kepanitiaanAktif = Panitia::query()
            ->with('periode')
            ->where('nim', $mahasiswa->nim)
            ->whereHas('periode', fn ($query) => $query->whereIn('status_aktif', [1, 2]))
            ->latest('id')
            ->first();

        return $kepanitiaanAktif?->periode;
    }
}
