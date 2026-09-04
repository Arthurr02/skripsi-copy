<?php

namespace App\Http\Controllers\Rekrutmen;

use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use App\Models\Panitia;
use App\Models\PeriodeRekrutmen;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PanitiaRekrutmenController extends Controller
{
    public function index(): View
    {
        $periode = $this->periodeAktif();
        $panitia = Panitia::query()
            ->with('mahasiswa:nim,nama_lengkap,email_kampus')
            ->where('periode_rekrutmen_id', $periode->id)
            ->orderBy('nim')
            ->get();

        return view('rekrutmen.panitia.index', compact('periode', 'panitia'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nim' => ['bail', 'required', 'regex:/^\d{9}$/'],
        ], [
            'nim.regex' => 'NIM harus terdiri dari tepat 9 digit angka.',
        ]);

        $periode = $this->periodeAktif();
        $mahasiswa = Mahasiswa::firstOrCreate(
            ['nim' => $data['nim']],
            [
                'email_kampus' => $data['nim'].'@stis.ac.id',
                'nama_lengkap' => 'Menunggu login Google',
            ],
        );
        $sudahTerdaftar = Panitia::query()
            ->where('periode_rekrutmen_id', $periode->id)
            ->where('nim', $mahasiswa->nim)
            ->exists();

        if ($sudahTerdaftar) {
            return back()->with('error_server', 'Mahasiswa tersebut sudah terdaftar sebagai panitia pada periode ini.');
        }

        Panitia::create([
            'periode_rekrutmen_id' => $periode->id,
            'nim' => $mahasiswa->nim,
        ]);

        return back()->with('success', $mahasiswa->nama_lengkap.' berhasil ditambahkan sebagai panitia.');
    }

    public function destroy(Panitia $panitia): RedirectResponse
    {
        $periode = $this->periodeAktif();
        abort_unless($panitia->periode_rekrutmen_id === $periode->id, 404);

        $namaPanitia = $panitia->mahasiswa?->nama_lengkap ?? $panitia->nim;
        $panitia->delete();

        return back()->with('success', $namaPanitia.' berhasil dihapus dari daftar panitia.');
    }

    private function periodeAktif(): PeriodeRekrutmen
    {
        return PeriodeRekrutmen::query()
            ->where('organisasi_id', Auth::guard('organisasi')->id())
            ->whereIn('status_aktif', [1, 2])
            ->latest('id')
            ->firstOrFail();
    }
}
