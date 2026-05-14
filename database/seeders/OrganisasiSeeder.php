<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Organisasi;

class OrganisasiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Data untuk DPM
        Organisasi::create([
            'email_kampus' => 'dpm@stis.ac.id',
            'nama_organisasi' => 'Dewan Perwakilan Mahasiswa (DPM)',
            'lampiran_logo' => 'ini logo dpm',
        ]);

        // Data untuk BEM
        Organisasi::create([
            'email_kampus' => 'bem@stis.ac.id',
            'nama_organisasi' => 'Badan Eksekutif Mahasiswa (BEM)',
            'lampiran_logo' => 'ini logo bem',
        ]);
    }
}
