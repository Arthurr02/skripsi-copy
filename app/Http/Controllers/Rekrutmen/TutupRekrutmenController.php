<?php

namespace App\Http\Controllers\Rekrutmen;

use App\Http\Controllers\Controller;
use App\Models\PeriodeRekrutmen;
use App\Models\Tahapan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class TutupRekrutmenController extends Controller
{
    public function __invoke(): RedirectResponse
    {
        $periode = PeriodeRekrutmen::query()
            ->where('organisasi_id', Auth::guard('organisasi')->id())
            ->whereIn('status_aktif', [1, 2])
            ->latest('id')
            ->firstOrFail();

        $tahapanBelumBerakhir = Tahapan::query()
            ->where('periode_rekrutmen_id', $periode->id)
            ->where(function ($query) {
                $query->whereNull('waktu_berakhir')
                    ->orWhere('waktu_berakhir', '>', now());
            })
            ->orderBy('waktu_berakhir')
            ->first();

        if ($tahapanBelumBerakhir) {
            $waktuBerakhir = $tahapanBelumBerakhir->waktu_berakhir
                ? $tahapanBelumBerakhir->waktu_berakhir->format('d/m/Y H:i').' WIB'
                : 'belum ditentukan';

            return redirect()
                ->route('organisasi.rekrutmen.seleksi')
                ->with(
                    'error',
                    'Rekrutmen belum dapat ditutup. Tahapan "'.$tahapanBelumBerakhir->nama_tahapan
                        .'" baru berakhir pada '.$waktuBerakhir.'.',
                );
        }

        $periode->update(['status_aktif' => 0]);

        return redirect()
            ->route('organisasi.dashboard')
            ->with('success', 'Rekrutmen periode '.$periode->tahun_periode.' telah ditutup dan dipindahkan ke riwayat.');
    }
}
