<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengumpulanTugas extends Model
{
    use HasFactory;

    // Pastikan nama tabel sesuai
    protected $table = 'pengumpulan_tugas';

    // Izinkan Laravel untuk mengisi kolom-kolom ini
    protected $fillable = [
        'tugas_id',
        'pendaftaran_id',
        'lampiran_jawaban',
    ];

    // Beri tahu Laravel bahwa lampiran_jawaban adalah JSON/Array
    protected $casts = [
        'lampiran_jawaban' => 'array',
    ];

    // (Bila ada fungsi relasi seperti belongsTo, biarkan saja di bawah sini)
}