<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // 🌟 WAJIB DITAMBAHKAN

class PeriodeRekrutmen extends Model
{
    use HasFactory;

    protected $table = 'periode_rekrutmen';

    protected $guarded = [];

    protected $casts = [
        'lampiran_banner' => 'array',
        'lampiran_pedoman' => 'array',
    ];

    public function organisasi()
    {
        return $this->belongsTo(Organisasi::class, 'organisasi_id', 'id');
    }
}