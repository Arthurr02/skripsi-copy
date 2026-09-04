<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // 🌟 WAJIB DITAMBAHKAN
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pendaftaran extends Model
{
    use HasFactory;

    // Pastikan mengarah ke tabel yang tepat
    protected $table = 'pendaftaran';

    // 🌟 INI KUNCI UTAMANYA: Mengizinkan Laravel mengisi kolom-kolom ini
    protected $fillable = [
        'nim',
        'jabatan_1_id',
        'jabatan_2_id',
    ];

    /**
     * Relasi ke tabel Mahasiswa
     */
    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class, 'nim', 'nim');
    }

    /**
     * Relasi ke tabel Jabatan (Pilihan 1)
     */
    public function pilihanJabatan1()
    {
        return $this->belongsTo(Jabatan::class, 'jabatan_1_id', 'id');
    }

    /**
     * RELASI KE TABEL JABATAN (PILIHAN 2)
     */
    public function pilihanJabatan2()
    {
        return $this->belongsTo(Jabatan::class, 'jabatan_2_id', 'id');
    }

    public function keputusanSeleksi(): HasMany
    {
        return $this->hasMany(KeputusanSeleksi::class, 'pendaftaran_id');
    }
}
