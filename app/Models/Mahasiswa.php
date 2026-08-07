<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mahasiswa extends Authenticatable
{
    use HasFactory;

    protected $table = 'mahasiswa';
    protected $primaryKey = 'nim';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $guarded = [];

    protected $fillable = [
        'nim',
        'google_id',
        'email_kampus',
        'nama_lengkap',
        'avatar_google',
    ];


    public function kepanitiaan()
    {
        // Parameter: (NamaModelTujuan, NamaKolomDiTabelTujuan, NamaKolomDiTabelIni)
        return $this->hasMany(Panitia::class, 'nim', 'nim');
    }

    /**
     * Cek apakah mahasiswa ini memiliki minimal 1 jabatan panitia/anggota
     */
    public function isPanitia()
    {
        return $this->kepanitiaan()->exists();
    }
}