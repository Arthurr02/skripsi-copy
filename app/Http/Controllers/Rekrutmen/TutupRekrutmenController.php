<?php

namespace App\Http\Controllers\Rekrutmen;

use App\Http\Controllers\Controller;
use App\Models\PeriodeRekrutmen;
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

        $periode->update(['status_aktif' => 0]);

        return redirect()
            ->route('organisasi.dashboard')
            ->with('success', 'Rekrutmen periode '.$periode->tahun_periode.' telah ditutup dan dipindahkan ke riwayat.');
    }
}
