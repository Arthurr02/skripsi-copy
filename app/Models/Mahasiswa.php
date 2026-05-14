<?php

namespace App\Models;

// Tambahkan baris ini
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

// Ubah kata 'Model' menjadi 'Authenticatable'
class Mahasiswa extends Authenticatable
{
    use HasFactory;

    protected $table = 'mahasiswa';
    // Karena primary key kita 'nim' (bukan 'id') dan bertipe string, tambahkan ini:
    protected $primaryKey = 'nim';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $guarded = [];

    public function keanggotaan()
    {
        // Parameter: (NamaModelTujuan, NamaKolomDiTabelTujuan, NamaKolomDiTabelIni)
        return $this->hasMany(AnggotaOrganisasi::class, 'nim', 'nim');
    }

    /**
     * Cek apakah mahasiswa ini memiliki minimal 1 jabatan panitia/anggota
     */
    public function isAnggota()
    {
        return $this->keanggotaan()->exists();
    }
}