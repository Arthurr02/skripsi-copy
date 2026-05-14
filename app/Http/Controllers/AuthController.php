<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

use App\Models\Mahasiswa;
use App\Models\Organisasi;

class AuthController extends Controller
{
    // 1. Mengarahkan user ke halaman login Google
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    // 2. Menangani balasan dari Google setelah user memilih akun
    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            $email = $googleUser->email;

            // JALUR A: Cek apakah ini akun Organisasi (DPM/BEM)
            $organisasi = Organisasi::where('email_kampus', $email)->first();
            if ($organisasi) {
                Auth::guard('organisasi')->login($organisasi);
                return redirect()->route('organisasi.dashboard');
            }

            // JALUR B & C: Jika bukan organisasi, berarti Mahasiswa (bisa Pendaftar atau Panitia)
            $nim = explode('@', $email)[0];
            $mahasiswa = Mahasiswa::updateOrCreate(
                ['nim' => $nim],
                [
                    'google_id' => $googleUser->id,
                    'email_kampus' => $email,
                    'nama_lengkap' => $googleUser->name,
                ]
            );

            Auth::guard('web')->login($mahasiswa);

            // ==========================================
            // PERBAIKAN PENGALIHAN (REDIRECT) DI SINI
            // ==========================================
            // Sistem akan mengecek pangkat mahasiswa sesaat setelah login
            if ($mahasiswa->isAnggota()) {
                return redirect()->route('panitia.dashboard');
            } else {
                return redirect()->route('mahasiswa.dashboard');
            }

        } catch (\Exception $e) {
            // Log error ke file storage/logs/laravel.log agar bisa dibaca nanti
            \Log::error($e->getMessage());
            return redirect('/')->with('error', 'Terjadi kesalahan sistem.');
        }
    }

    // 3. Fungsi untuk Logout
    public function logout()
    {
        Auth::logout();
        return redirect('/');
    }
}