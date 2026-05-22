<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tahapan extends Model
{
    use HasFactory;

    protected $table = 'tahapan';
    protected $guarded = [];

    // OTOMATIS CASTING: Mengubah JSON database menjadi array PHP secara instan
    protected $casts = [
        'lampiran_tahapan' => 'array',
    ];

    public function tugas()
    {
        return $this->hasMany(Tugas::class, 'tahapan_id');
    }
}