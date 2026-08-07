<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // 🌟 WAJIB DITAMBAHKAN

class Jabatan extends Model
{
    use HasFactory;

    // KUNCI NAMA TABEL: Paksa Laravel menembak 'jabatan', bukan 'jabatans'
    protected $table = 'jabatan';

    protected $fillable = [
        'periode_rekrutmen_id',
        'nama_posisi',   // <-- Pastikan baris ini ada
        'nama_jabatan',
    ];

    // Izinkan semua kolom diisi (Mass Assignment)
    protected $guarded = [];

    // Relasi balik ke Periode jika dibutuhkan
    public function periode()
    {
        return $this->belongsTo(PeriodeRekrutmen::class, 'periode_rekrutmen_id', 'id');
    }
}