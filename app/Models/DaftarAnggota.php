<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DaftarAnggota extends Model
{
    protected $table = 'daftar_anggota';

    protected $fillable = [
        'organisasi_id',
        'file_path',
        'nama_file_asli',
        'tanggal_mulai_periode',
        'tanggal_akhir_periode',
    ];

    protected $casts = [
        'tanggal_mulai_periode' => 'date',
        'tanggal_akhir_periode' => 'date',
    ];

    public function organisasi(): BelongsTo
    {
        return $this->belongsTo(Organisasi::class, 'organisasi_id');
    }
}
