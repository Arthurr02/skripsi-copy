<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use App\Models\Dosen;
use App\Models\Organisasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

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

            // A Google callback always starts one fresh role session. Explicitly
            // clearing every guard prevents a stale role from sharing navigation
            // state or authorization with the newly authenticated account.
            Auth::guard('mahasiswa')->logout();
            Auth::guard('organisasi')->logout();
            Auth::guard('dosen')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // =====================
            // Login Organisasi
            // =====================
            $organisasi = Organisasi::where('email_kampus', $email)->first();

            if ($organisasi) {
                Auth::guard('organisasi')->login($organisasi);
                $request->session()->regenerate();

                return redirect()->route('organisasi.dashboard');
            }

            // Dosen hanya dapat masuk apabila alamat Google-nya sudah didaftarkan
            // oleh administrator pada tabel dosen.
            $dosen = Dosen::query()->where('email', $email)->first();
            if ($dosen) {
                // Identitas profil dosen selalu mengikuti akun Google yang
                // digunakan untuk masuk. Organisasi tidak mengikuti aturan ini
                // karena nama dan logo organisasinya dikelola secara mandiri.
                $dosen->update([
                    'google_id' => $googleUser->id,
                    'avatar_google' => $avatarUrl,
                    'nama' => filled($googleUser->name) ? $googleUser->name : $dosen->nama,
                ]);

                Auth::guard('dosen')->login($dosen);
                $request->session()->regenerate();

                return redirect()->route('dosen.dashboard');
            }

            // =====================
            // Login Mahasiswa
            // =====================
            $nim = explode('@', $email)[0];

            if (!preg_match('/^\d{9}@stis\.ac\.id$/i', $email)) {
                return redirect('/')
                    ->with('error', 'Mohon gunakan akun Google kampus @stis.ac.id.');
            }

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

    /** End every authenticated session used by the application. */
    public function logout(Request $request)
    {
        Auth::guard('mahasiswa')->logout();
        Auth::guard('organisasi')->logout();
        Auth::guard('dosen')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
