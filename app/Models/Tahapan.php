<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tahapan extends Model
{
    use HasFactory;

    protected $table = 'tahapan';

    protected $guarded = [];

    protected $casts = [
        'lampiran_tahapan' => 'array',
    ];

    public function tugas()
    {
        return $this->hasMany(Tugas::class, 'tahapan_id');
    }

    public function keputusanSeleksi()
    {
        return $this->hasMany(KeputusanSeleksi::class, 'tahapan_id');
    }
}
