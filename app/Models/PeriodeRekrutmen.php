<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeriodeRekrutmen extends Model
{
    use HasFactory;

    protected $table = 'periode_rekrutmen';

    // Tambahkan baris ini untuk membuka izin insert data
    protected $guarded = [];
}
