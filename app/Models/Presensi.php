<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Presensi extends Model
{
    protected $table = 'presensis'; 
    
    protected $fillable = [
        'siswa_id',
        'tanggal',
        'jam_masuk',
        'foto_masuk',
        'latitude_masuk',
        'longitude_masuk',
        'status_kehadiran', 
        'keterangan_izin',
        'bukti_izin',
        'jam_pulang',
        'jurnal_kegiatan',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jam_masuk' => 'datetime',
        'jam_keluar' => 'datetime',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    public function sekolah()
    {
        return $this->hasOneThrough(
            \App\Models\Sekolah::class,
            Siswa::class,
            'id', 
            'id',
            'siswa_id',
            'sekolah_id' 
        );
    }

    public function scopeToday($query)
    {
        return $query->whereDate('tanggal', today());
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('tanggal', now()->month)
                     ->whereYear('tanggal', now()->year);
    }

    public function getStatusKehadiranLabelAttribute()
    {
        return match($this->status_kehadiran) {
            'hadir' => 'Hadir',
            'terlambat' => 'Terlambat',
            'izin' => 'Izin',
            'sakit' => 'Sakit',
            'alpha' => 'Tanpa Keterangan',
            default => 'Tidak Diketahui'
        };
    }
}