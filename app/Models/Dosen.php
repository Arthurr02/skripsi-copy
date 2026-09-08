<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Dosen extends Authenticatable
{
    protected $table = 'dosen';

    protected $fillable = ['email', 'nama', 'google_id', 'avatar_google'];

    protected $hidden = ['remember_token'];
}
