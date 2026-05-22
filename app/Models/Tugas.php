<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'lampiran_tugas'
    ];

    // app/Models/Tugas.php
    protected $casts = [
        'lampiran_tugas' => 'array', // Otomatis decode JSON menjadi Array
    ];
}