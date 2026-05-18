<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RekrutmenAktifController extends Controller
{
    public function index()
    {
        return view('organisasi.rekrutmen-saat-ini.index');
    }

    public function pendaftar()
    {
        return view('organisasi.rekrutmen-saat-ini.pendaftar'); // Buat view kosongnya nanti
    }

    public function tahapan()
    {
        return view('organisasi.rekrutmen-saat-ini.tahapan'); // Buat view kosongnya nanti
    }

    public function updateInfo()
    {
        return view('organisasi.rekrutmen-saat-ini.update'); // Buat view kosongnya nanti
    }
}
