<?php

namespace App\Models;

// 1. Tambahkan baris impor ini
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

// 2. Ubah 'extends Model' menjadi 'extends Authenticatable'
class Organisasi extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'organisasi';



    // Sesuaikan primary key jika bukan 'id'
    // protected $primaryKey = 'id'; 

    protected $guarded = [];

    protected $fillable = [
        'email_kampus',
        'nama_organisasi',
        'lampiran_logo',
        'avatar_google', // <--- Daftarkan di sini
    ];

    // Jika password tidak digunakan (karena pakai Google), 
    // Laravel terkadang tetap mencari kolom password, kita biarkan kosong saja.
    public function getAuthPassword()
    {
        return null;
    }
}