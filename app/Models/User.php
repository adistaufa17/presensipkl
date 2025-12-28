<?php

namespace App\Models;

use App\Models\Siswa;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $fillable = [
        'nama_lengkap', 'email', 'password', 'role',
    ];

    public function siswa()
    {
       return $this->hasOne(Siswa::class, 'user_id');
    }
}
