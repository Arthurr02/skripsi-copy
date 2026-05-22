<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jabatan extends Model
{
    use HasFactory;

    // KUNCI NAMA TABEL: Paksa Laravel menembak 'jabatan', bukan 'jabatans'
    protected $table = 'jabatan';

    // Izinkan semua kolom diisi (Mass Assignment)
    protected $guarded = [];

    // Relasi balik ke Periode jika dibutuhkan
    public function periode()
    {
        return $this->belongsTo(PeriodeRekrutmen::class, 'periode_rekrutmen_id');
    }
}