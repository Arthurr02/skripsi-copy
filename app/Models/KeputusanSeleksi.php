<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KeputusanSeleksi extends Model
{
    use HasFactory;

    protected $table = 'keputusan_seleksi';

    protected $guarded = [];

    protected $casts = [
        'diputuskan_pada' => 'datetime',
    ];

    public function tahapan(): BelongsTo
    {
        return $this->belongsTo(Tahapan::class, 'tahapan_id');
    }

    public function jabatan(): BelongsTo
    {
        return $this->belongsTo(Jabatan::class, 'jabatan_id');
    }

    public function pendaftaran(): BelongsTo
    {
        return $this->belongsTo(Pendaftaran::class, 'pendaftaran_id');
    }
}
