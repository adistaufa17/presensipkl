<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'pembimbing_id'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
        ];
    }

    public function pembayarans()
    {
        return $this->hasMany(Pembayaran::class);
    }

    public function presensis()
    {
        return $this->hasMany(Presensi::class);
    }

    public function tagihans()
    {
        return $this->hasMany(Tagihan::class);
    }

    public function isPembimbing()
    {
        return $this->role === 'pembimbing';
    }

    public function isSiswa()
    {
        return $this->role === 'siswa';
    }

    public function scopeSiswa($query)
    {
        return $query->where('role', 'siswa');
    }

    public function scopePembimbing($query)
    {
        return $query->where('role', 'pembimbing');
    }

    public function pembimbing()
    {
        return $this->belongsTo(User::class, 'pembimbing_id');
    }

    public function siswaBimbingan()
    {
        return $this->hasMany(User::class, 'pembimbing_id');
    }

}