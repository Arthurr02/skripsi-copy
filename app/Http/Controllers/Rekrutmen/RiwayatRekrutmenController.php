<?php

namespace App\Http\Controllers\Rekrutmen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\PeriodeRekrutmen;
use App\Models\Jabatan;
use App\Models\Tahapan;
use App\Models\Panitia;

class RiwayatRekrutmenController extends Controller
{
    // Bantuan untuk mengecek identitas & ID Organisasi
    private function getOrganisasiData()
    {
        if (Auth::guard('organisasi')->check()) {
            return [
                'organisasiId' => Auth::guard('organisasi')->id(),
                'prefix' => 'organisasi.'
            ];
        } else {
            $nimPanitia = Auth::user()->nim;
            $kepanitiaan = Panitia::where('nim', $nimPanitia)
                ->where('panitia_rekrutmen', 1)
                ->latest()
                ->first();

            if (!$kepanitiaan)
                abort(403, 'Anda tidak memiliki akses arsip.');

            $periodePanitia = PeriodeRekrutmen::find($kepanitiaan->periode_rekrutmen_id);
            return [
                'organisasiId' => $periodePanitia->organisasi_id,
                'prefix' => 'panitia.'
            ];
        }
    }

    // 1. LEVEL 1: MENAMPILKAN FOLDER PERIODE YANG SUDAH BERLALU
    public function index()
    {
        $authData = $this->getOrganisasiData();

        // Ambil semua periode yang status_aktif = 0 (sudah tutup/berlalu)
        $riwayatPeriode = PeriodeRekrutmen::where('organisasi_id', $authData['organisasiId'])
            ->where('status_aktif', 0)
            ->orderBy('created_at', 'desc')
            ->get();

        $routePrefix = $authData['prefix'];

        return view('rekrutmen.riwayat.index', compact('riwayatPeriode', 'routePrefix'));
    }

    // 2. LEVEL 2: FOLDER MENU (PEDOMAN & JABATAN)
    public function showPeriode($periode_id)
    {
        // Akan kita kerjakan di Fase 2
    }

    // 3. LEVEL 3: FOLDER TAHAPAN PER JABATAN
    public function showJabatan($periode_id, $jabatan_id)
    {
        // Akan kita kerjakan di Fase 3
    }

    // 4. LEVEL 4: BERKAS PENDAFTAR
    public function showTahapan($periode_id, $jabatan_id, $tahapan_id)
    {
        // Akan kita kerjakan di Fase 4
    }
}