<?php

namespace App\Providers;

use App\Models\Panitia;
use App\Models\PeriodeRekrutmen;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('layouts.sidebar', function ($view): void {
            $rekrutmenAktifTersedia = false;

            if (Auth::guard('organisasi')->check()) {
                $rekrutmenAktifTersedia = PeriodeRekrutmen::query()
                    ->where('organisasi_id', Auth::guard('organisasi')->id())
                    ->whereIn('status_aktif', [1, 2])
                    ->exists();
            } elseif (Auth::check()) {
                $rekrutmenAktifTersedia = Panitia::query()
                    ->where('nim', Auth::user()->nim)
                    ->whereHas('periode', fn ($query) => $query->whereIn('status_aktif', [1, 2]))
                    ->exists();
            }

            $view->with('rekrutmenAktifTersedia', $rekrutmenAktifTersedia);
        });
    }
}
