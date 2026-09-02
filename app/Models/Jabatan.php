<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Jabatan extends Model
{
    use HasFactory;

    protected $table = 'jabatan';

    protected $fillable = [
        'periode_rekrutmen_id',
        'nama_posisi',
        'nama_jabatan',
    ];

    public function periode(): BelongsTo
    {
        return $this->belongsTo(PeriodeRekrutmen::class, 'periode_rekrutmen_id', 'id');
    }

    public function pendaftaranPilihanPertama(): HasMany
    {
        return $this->hasMany(Pendaftaran::class, 'jabatan_1_id');
    }

    public function pendaftaranPilihanKedua(): HasMany
    {
        return $this->hasMany(Pendaftaran::class, 'jabatan_2_id');
    }

    public function tugas(): HasMany
    {
        return $this->hasMany(Tugas::class, 'jabatan_id');
    }
}
