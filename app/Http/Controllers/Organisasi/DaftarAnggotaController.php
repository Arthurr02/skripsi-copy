<?php

namespace App\Http\Controllers\Organisasi;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDaftarAnggotaRequest;
use App\Models\DaftarAnggota;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DaftarAnggotaController extends Controller
{
    public function index(): View
    {
        $riwayatUnggahan = DaftarAnggota::query()
            ->where('organisasi_id', Auth::guard('organisasi')->id())
            ->latest()
            ->paginate(10);

        return view('organisasi.daftar-anggota.index', compact('riwayatUnggahan'));
    }

    public function store(StoreDaftarAnggotaRequest $request): RedirectResponse
    {
        $file = $request->file('file_daftar_anggota');
        $path = null;

        try {
            // Private disk prevents a guessed storage URL from exposing IPKM data.
            $path = $file->store('organisasi/daftar-anggota', 'local');

            DB::transaction(function () use ($request, $file, $path): void {
                DaftarAnggota::create([
                    'organisasi_id' => Auth::guard('organisasi')->id(),
                    'file_path' => $path,
                    'nama_file_asli' => $file->getClientOriginalName(),
                    'tanggal_mulai_periode' => $request->date('tanggal_mulai_periode'),
                    'tanggal_akhir_periode' => $request->date('tanggal_akhir_periode'),
                ]);
            });
        } catch (\Throwable $exception) {
            report($exception);
            if ($path) {
                Storage::disk('local')->delete($path);
            }

            return back()
                ->withInput()
                ->withErrors(['file_daftar_anggota' => 'Berkas belum dapat disimpan. Silakan coba kembali.'])
                ->with('flash_alert', [
                    'icon' => 'error',
                    'title' => 'Unggahan gagal disimpan',
                    'text' => 'Berkas tidak tersimpan. Periksa data lalu coba kembali.',
                ]);
        }

        return redirect()
            ->route('organisasi.daftar-anggota.index')
            ->with('flash_alert', [
                'icon' => 'success',
                'title' => 'Daftar anggota berhasil diunggah',
                'text' => 'Berkas telah dikirim untuk penilaian IPKM.',
            ]);
    }

    public function download(DaftarAnggota $daftarAnggota): StreamedResponse
    {
        abort_unless($daftarAnggota->organisasi_id === Auth::guard('organisasi')->id(), 403);
        abort_unless(Storage::disk('local')->exists($daftarAnggota->file_path), 404);

        ob_end_clean();
        return Storage::disk('local')->download($daftarAnggota->file_path, $daftarAnggota->nama_file_asli);
    }
}
