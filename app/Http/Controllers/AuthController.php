<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
    public function callback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            $email = $googleUser->email;
            $avatarUrl = $googleUser->avatar;

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // =====================
            // Login Organisasi
            // =====================
            $organisasi = Organisasi::where('email_kampus', $email)->first();

            if ($organisasi) {
                $organisasi->update([
                    'avatar_google' => $avatarUrl,
                ]);

                Auth::guard('organisasi')->login($organisasi);
                $request->session()->regenerate();
                return redirect()->route('organisasi.dashboard');
            }

            // =====================
            // Login Mahasiswa
            // =====================
            $nim = explode('@', $email)[0];

            $mahasiswa = Mahasiswa::updateOrCreate(
                ['nim' => $nim],
                [
                    'google_id' => $googleUser->id,
                    'email_kampus' => $email,
                    'nama_lengkap' => $googleUser->name,
                    'avatar_google' => $avatarUrl,
                ]
            );

            Auth::guard('mahasiswa')->login($mahasiswa);

            // Regenerate session setelah login
            $request->session()->regenerate();

            if ($mahasiswa->isPanitia()) {
                return redirect()->route('panitia.dashboard');
            }

            return redirect()->route('mahasiswa.dashboard');

        } catch (\Exception $e) {
            \Log::error($e);
            return redirect('/')
                ->with('error', 'Mohon gunakan akun email kampus STIS');
        }
    }
}