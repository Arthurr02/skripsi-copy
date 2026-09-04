<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// 🌟 WAJIB DITAMBAHKAN

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

    public function jabatan()
    {
        return $this->hasMany(Jabatan::class, 'periode_rekrutmen_id');
    }

    public function panitia()
    {
        return $this->hasMany(Panitia::class, 'periode_rekrutmen_id');
    }
}
