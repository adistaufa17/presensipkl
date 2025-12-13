<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Presensi extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'tanggal',
        'jam_masuk',
        'jam_keluar',
        'status',
        'bukti_foto',
        'keterangan',
    ];

    public function user()
        {
            return $this->belongsTo(User::class);
        }

        /**
         * Scope: Presensi hari ini
         */
        public function scopeToday($query)
        {
            return $query->where('tanggal', now()->toDateString());
        }

        /**
         * Scope: Presensi bulan ini
         */
        public function scopeThisMonth($query)
        {
            return $query->whereMonth('tanggal', now()->month)
                        ->whereYear('tanggal', now()->year);
        }

        /**
         * Scope: Hanya status hadir
         */
        public function scopeHadir($query)
        {
            return $query->whereIn('status', ['hadir', 'terlambat']);
        }

        /**
         * Accessor: Durasi kerja (jam)
         */
        public function getDurasiKerjaAttribute()
        {
            if (!$this->jam_masuk || !$this->jam_keluar) {
                return 0;
            }

            $masuk = Carbon::parse($this->tanggal . ' ' . $this->jam_masuk);
            $keluar = Carbon::parse($this->tanggal . ' ' . $this->jam_keluar);

            return $masuk->diffInHours($keluar);
        }

        /**
         * Accessor: Status Badge Color (untuk UI)
         */
        public function getStatusColorAttribute()
        {
            return match($this->status) {
                'hadir' => 'success',
                'terlambat' => 'warning',
                'izin' => 'info',
                'sakit' => 'info',
                'alpa' => 'danger',
                default => 'secondary'
            };
        }

}