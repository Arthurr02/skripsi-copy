<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tugas extends Model
{
    use HasFactory;

    // KUNCI NAMA TABEL: Paksa Laravel menembak tabel 'penugasan', bukan 'penugasans'
    protected $table = 'tugas';

    protected $guarded = [];

    protected $fillable = [
        'tahapan_id',
        'jabatan_id',
        'pewawancara_id',
        'tipe_tugas',
        'tipe_jawaban_tugas',
        'deskripsi_tugas',
        'lampiran_tugas',
    ];

    protected $casts = [
        'lampiran_tugas' => 'array',
    ];

    public function jabatan(): BelongsTo
    {
        return $this->belongsTo(Jabatan::class, 'jabatan_id');
    }

    public function tahapan(): BelongsTo
    {
        return $this->belongsTo(Tahapan::class, 'tahapan_id');
    }

    public function pengumpulanTugas(): HasMany
    {
        return $this->hasMany(PengumpulanTugas::class, 'tugas_id');
    }
}
