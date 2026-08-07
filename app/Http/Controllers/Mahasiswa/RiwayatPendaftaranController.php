<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Pendaftaran;

class RiwayatPendaftaranController extends Controller
{
    /**
     * Menampilkan seluruh riwayat rekrutmen mahasiswa
     */
    public function index()
    {
        $user = Auth::user();

        // Mengambil semua riwayat pendaftaran dengan relasi penuh
        $riwayatPendaftaran = Pendaftaran::with([
            'jabatan_1.periode.organisasi',
            'jabatan_2'
        ])
            ->where('nim', $user->nim)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('mahasiswa.riwayat.index', compact('riwayatPendaftaran'));
    }
}