<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; // BARIS INI YANG TADI TERLEWAT
use Illuminate\Database\Eloquent\Model;

class Panitia extends Model
{
    use HasFactory;

    // 1. Beri tahu Laravel nama tabel aslinya (tanpa 's')
    protected $table = 'panitia';

    // 2. Izinkan semua kolom diisi
    protected $guarded = [];
}
