<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; // BARIS INI YANG TADI TERLEWAT
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Panitia extends Model
{
    use HasFactory;

    // 1. Beri tahu Laravel nama tabel aslinya (tanpa 's')
    protected $table = 'panitia';

    // 2. Izinkan semua kolom diisi
    protected $guarded = [];

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class, 'nim', 'nim');
    }

    public function periode(): BelongsTo
    {
        return $this->belongsTo(PeriodeRekrutmen::class, 'periode_rekrutmen_id');
    }
}
