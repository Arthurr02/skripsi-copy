<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\DaftarAnggota;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DataOrganisasiController extends Controller
{
    public function index(Request $request): View
    {
        $sortColumns = [
            'organisasi' => 'organisasi.nama_organisasi',
            'tanggal_mulai' => 'daftar_anggota.tanggal_mulai_periode',
            'tanggal_akhir' => 'daftar_anggota.tanggal_akhir_periode',
            'waktu_kirim' => 'daftar_anggota.created_at',
        ];
        $sort = $request->string('sort')->toString();
        $direction = $request->string('direction')->lower()->value() === 'asc' ? 'asc' : 'desc';
        $sort = array_key_exists($sort, $sortColumns) ? $sort : 'waktu_kirim';
        $organisasi = $request->string('organisasi')->trim()->toString();

        $dataOrganisasi = DaftarAnggota::query()
            ->select('daftar_anggota.*')
            ->with('organisasi:id,nama_organisasi')
            ->join('organisasi', 'organisasi.id', '=', 'daftar_anggota.organisasi_id')
            ->when($organisasi !== '', fn ($query) => $query->where('organisasi.nama_organisasi', 'like', '%'.$organisasi.'%'))
            ->orderBy($sortColumns[$sort], $direction)
            ->orderByDesc('daftar_anggota.id')
            ->paginate(15)
            ->withQueryString();

        return view('dosen.data-organisasi.index', compact('dataOrganisasi', 'sort', 'direction', 'organisasi'));
    }

    public function download(DaftarAnggota $daftarAnggota): StreamedResponse
    {
        abort_unless(Storage::disk('local')->exists($daftarAnggota->file_path), 404);

        return Storage::disk('local')->download($daftarAnggota->file_path, $daftarAnggota->nama_file_asli);
    }
}
