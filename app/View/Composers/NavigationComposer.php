<?php

namespace App\View\Composers;

use App\Models\Panitia;
use App\Models\PeriodeRekrutmen;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class NavigationComposer
{
    /** Supply navigation state without letting layout views query models. */
    public function compose(View $view): void
    {
        $isOrganisasi = Auth::guard('organisasi')->check();
        $isDosen = Auth::guard('dosen')->check();
        $currentUser = $isOrganisasi
            ? Auth::guard('organisasi')->user()
            : ($isDosen ? Auth::guard('dosen')->user() : Auth::guard('mahasiswa')->user());
        $isPanitia = ! $isOrganisasi
            && ! $isDosen
            && $currentUser
            && Panitia::query()->where('nim', $currentUser->nim)->exists();
        $isMahasiswaBiasa = ! $isOrganisasi && ! $isDosen && ! $isPanitia;

        $rekrutmenAktifTersedia = match (true) {
            $isOrganisasi => PeriodeRekrutmen::query()
                ->where('organisasi_id', $currentUser->id)
                ->whereIn('status_aktif', [1, 2])
                ->exists(),
            $isPanitia => Panitia::query()
                ->where('nim', $currentUser->nim)
                ->whereHas('periode', fn ($query) => $query->whereIn('status_aktif', [1, 2]))
                ->exists(),
            default => false,
        };

        $userName = $isOrganisasi
            ? ($currentUser?->nama_organisasi ?? 'Organisasi')
            : ($isDosen ? ($currentUser?->nama ?? 'Dosen') : ($currentUser?->nama_lengkap ?? 'Mahasiswa'));
        $userAvatar = $currentUser?->avatar_google;
        if (blank($userAvatar)) {
            $userAvatar = 'https://ui-avatars.com/api/?name='.urlencode($userName)
                .'&background=2563eb&color=ffffff&rounded=true&bold=true';
        }

        $view->with([
            'currentUser' => $currentUser,
            'isOrganisasi' => $isOrganisasi,
            'isDosen' => $isDosen,
            'isPanitia' => $isPanitia,
            'isMahasiswaBiasa' => $isMahasiswaBiasa,
            'dashboardRoute' => $isOrganisasi ? 'organisasi.dashboard' : ($isDosen ? 'dosen.dashboard' : ($isPanitia ? 'panitia.dashboard' : 'mahasiswa.dashboard')),
            'routePrefix' => $isOrganisasi ? 'organisasi.' : ($isDosen ? 'dosen.' : ($isPanitia ? 'panitia.' : 'mahasiswa.')),
            'rekrutmenAktifTersedia' => $rekrutmenAktifTersedia,
            'userName' => $userName,
            'userAvatar' => $userAvatar,
            'userRole' => $isOrganisasi ? 'Organisasi' : ($isDosen ? 'Dosen' : ($isPanitia ? 'Panitia Rekrutmen' : 'Mahasiswa')),
        ]);
    }
}
