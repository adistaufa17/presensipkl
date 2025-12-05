<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    /**
     * Kolom yang bisa diisi secara mass assignment
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'avatar',
        'phone',
        'address',
    ];

    /**
     * Kolom yang disembunyikan saat serialization
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Cast tipe data kolom
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Konstanta untuk role user
     */
    const ROLE_ADMIN = 'admin';
    const ROLE_MENTOR = 'mentor';
    const ROLE_SISWA = 'siswa';

    /**
     * Relasi ke Presence (absensi user)
     */
    public function presences()
    {
        return $this->hasMany(Presence::class);
    }

    /**
     * Relasi ke Permission (izin/sakit user)
     */
    public function permissions()
    {
        return $this->hasMany(Permission::class);
    }

    /**
     * Relasi ke Journal (jurnal user)
     */
    public function journals()
    {
        return $this->hasMany(Journal::class);
    }

    /**
     * Accessor untuk mendapatkan nama singkat (first name)
     */
    public function getFirstNameAttribute()
    {
        return explode(' ', $this->name)[0];
    }

    /**
     * Accessor untuk cek apakah user adalah admin
     */
    public function getIsAdminAttribute()
    {
        return $this->role === self::ROLE_ADMIN;
    }

    /**
     * Accessor untuk cek apakah user adalah mentor
     */
    public function getIsMentorAttribute()
    {
        return $this->role === self::ROLE_MENTOR;
    }

    /**
     * Accessor untuk cek apakah user adalah siswa
     */
    public function getIsSiswaAttribute()
    {
        return $this->role === self::ROLE_SISWA;
    }

    /**
     * Scope untuk filter user berdasarkan role
     */
    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }

    /**
     * Scope untuk filter hanya siswa
     */
    public function scopeSiswa($query)
    {
        return $query->where('role', self::ROLE_SISWA);
    }

    /**
     * Scope untuk filter hanya mentor
     */
    public function scopeMentor($query)
    {
        return $query->where('role', self::ROLE_MENTOR);
    }

    /**
     * Scope untuk filter hanya admin
     */
    public function scopeAdmin($query)
    {
        return $query->where('role', self::ROLE_ADMIN);
    }

    /**
     * Helper method untuk cek apakah user sudah absen hari ini
     */
    public function hasPresentToday()
    {
        return $this->presences()
                    ->whereDate('date', today())
                    ->exists();
    }

    /**
     * Helper method untuk mendapatkan absensi hari ini
     */
    public function todayPresence()
    {
        return $this->presences()
                    ->whereDate('date', today())
                    ->first();
    }

    /**
     * Helper method untuk mendapatkan total kehadiran
     */
    public function getTotalPresenceCount()
    {
        return $this->presences()
                    ->whereIn('status', [Presence::STATUS_HADIR, Presence::STATUS_TERLAMBAT])
                    ->count();
    }

    /**
     * Helper method untuk mendapatkan total keterlambatan
     */
    public function getTotalLateCount()
    {
        return $this->presences()
                    ->where('is_late', true)
                    ->count();
    }

    /**
     * Helper method untuk mendapatkan total izin
     */
    public function getTotalPermissionCount()
    {
        return $this->permissions()
                    ->where('status', Permission::STATUS_APPROVED)
                    ->count();
    }
}