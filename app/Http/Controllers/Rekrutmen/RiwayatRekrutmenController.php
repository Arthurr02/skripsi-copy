<?php

namespace App\Http\Controllers\Rekrutmen;

use App\Http\Controllers\Controller;
use App\Models\Jabatan;
use App\Models\Panitia;
use App\Models\PeriodeRekrutmen;
use App\Models\Tahapan;
use App\Services\Recruitment\TahapanPesertaCounter;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class RiwayatRekrutmenController extends Controller
{
    // Bantuan untuk mengecek identitas & ID Organisasi
    private function getOrganisasiData()
    {
        if (Auth::guard('organisasi')->check()) {
            return [
                'organisasiId' => Auth::guard('organisasi')->id(),
                'prefix' => 'organisasi.',
            ];
        } else {
            $nimPanitia = Auth::user()->nim;
            $kepanitiaan = Panitia::where('nim', $nimPanitia)
                ->latest()
                ->first();

            if (! $kepanitiaan) {
                abort(403, 'Anda tidak memiliki akses arsip.');
            }

            $periodePanitia = PeriodeRekrutmen::find($kepanitiaan->periode_rekrutmen_id);
            abort_unless($periodePanitia, 403, 'Periode kepanitiaan tidak ditemukan.');

            return [
                'organisasiId' => $periodePanitia->organisasi_id,
                'prefix' => 'panitia.',
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

    // 2. Detail periode tertutup dalam mode baca saja.
    public function showPeriode($periode_id)
    {
        $authData = $this->getOrganisasiData();
        $periodeAktif = PeriodeRekrutmen::query()
            ->whereKey($periode_id)
            ->where('organisasi_id', $authData['organisasiId'])
            ->where('status_aktif', 0)
            ->firstOrFail();

        $now = Carbon::now();
        $tahapans = Tahapan::query()
            ->where('periode_rekrutmen_id', $periodeAktif->id)
            ->withCount('tugas')
            ->orderBy('urutan_tahapan')
            ->orderBy('waktu_mulai')
            ->get()
                ->map(function (Tahapan $tahapan) use ($now) {
                $tahapan->parsed_mulai = Carbon::parse($tahapan->waktu_mulai);
                $tahapan->parsed_berakhir = Carbon::parse($tahapan->waktu_berakhir);
                $tahapan->is_past = $now->gt($tahapan->parsed_berakhir);
                $tahapan->is_active = $now->between($tahapan->parsed_mulai, $tahapan->parsed_berakhir);
                $tahapan->is_future = $now->lt($tahapan->parsed_mulai);
                $tahapan->is_waktu_tunggal = $tahapan->parsed_mulai->equalTo($tahapan->parsed_berakhir);

                $lampiran = is_array($tahapan->lampiran_tahapan)
                    ? $tahapan->lampiran_tahapan
                    : (json_decode($tahapan->lampiran_tahapan ?? '[]', true) ?: []);
                $tahapan->pedoman_path = $lampiran[0] ?? null;

                    return $tahapan;
                });

        $tahapans = app(TahapanPesertaCounter::class)
            ->tambahkanJumlahPeserta($tahapans, $periodeAktif->id);

        $pesertaPerTahapanJabatan = $tahapans
            ->mapWithKeys(fn (Tahapan $tahapan) => [$tahapan->id => $tahapan->peserta_per_jabatan]);

        $listJabatan = Jabatan::query()
            ->where('periode_rekrutmen_id', $periodeAktif->id)
            ->withCount([
                'pendaftaranPilihanPertama as pendaftaran1_count',
                'pendaftaranPilihanKedua as pendaftaran2_count',
            ])
            ->orderBy('nama_posisi')
            ->orderBy('nama_jabatan')
            ->get();

        return view('rekrutmen.seleksi.daftar-tahapan', [
            'periodeAktif' => $periodeAktif,
            'tahapans' => $tahapans,
            'listJabatan' => $listJabatan,
            'routePrefix' => $authData['prefix'],
            'isRiwayat' => true,
            'pesertaPerTahapanJabatan' => $pesertaPerTahapanJabatan,
        ]);
    }

    // 3. LEVEL 3: FOLDER TAHAPAN PER JABATAN
    public function showJabatan($periode_id, $jabatan_id)
    {
        $authData = $this->getOrganisasiData();
        $periode = PeriodeRekrutmen::query()
            ->whereKey($periode_id)
            ->where('organisasi_id', $authData['organisasiId'])
            ->where('status_aktif', 0)
            ->firstOrFail();

        Jabatan::query()
            ->whereKey($jabatan_id)
            ->where('periode_rekrutmen_id', $periode->id)
            ->firstOrFail();

        return redirect()->route($authData['prefix'].'riwayat.periode', $periode->id);
    }

    // 4. LEVEL 4: BERKAS PENDAFTAR
    public function showTahapan($periode_id, $jabatan_id, $tahapan_id)
    {
        // Akan kita kerjakan di Fase 4
    }
}
