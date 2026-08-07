<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PeriodeRekrutmen;

class EnsurePanitiaPeriodeAktif
{
    public function handle(Request $request, Closure $next)
    {
        // 1. Pastikan pengguna sudah login sebagai panitia
        if (!Auth::guard('panitia')->check()) {
            return redirect()->route('login.panitia');
        }

        $panitia = Auth::guard('panitia')->user();

        // 2. Cek apakah ada periode aktif untuk organisasi tempat panitia ini bernaung
        $periodeAktif = PeriodeRekrutmen::where('organisasi_id', $panitia->organisasi_id)
            ->where('status_aktif', 1)
            ->latest()
            ->first();

        // Jika tidak ada rekrutmen aktif, blokir akses
        if (!$periodeAktif) {
            abort(403, 'Tidak ada rekrutmen yang sedang aktif saat ini.');
        }

        // 3. Pastikan panitia ini ditugaskan khusus untuk periode yang sedang aktif tersebut
        // (Asumsi: di tabel panitia terdapat kolom periode_rekrutmen_id)
        if ($panitia->periode_rekrutmen_id !== $periodeAktif->id) {
            abort(403, 'Akses Ditolak: Anda bukan panitia yang ditugaskan untuk periode rekrutmen saat ini.');
        }

        return $next($request);
    }
}
