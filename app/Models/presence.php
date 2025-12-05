<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Presence extends Model
{
    use HasFactory;

    /**
     * Kolom yang bisa diisi secara mass assignment
     */
    protected $fillable = [
        'user_id',
        'date',
        'time_in',
        'time_out',
        'status',
        'is_late',
    ];

    /**
     * Cast tipe data kolom
     */
    protected $casts = [
        'date' => 'date',
        'is_late' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Konstanta untuk status kehadiran
     */
    const STATUS_HADIR = 'hadir';
    const STATUS_TERLAMBAT = 'terlambat';
    const STATUS_IZIN = 'izin';
    const STATUS_SAKIT = 'sakit';
    const STATUS_ALPA = 'alpa';

    /**
     * Relasi ke User (siswa yang melakukan absensi)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke Journal (jurnal terkait absensi ini)
     */
    public function journal()
    {
        return $this->hasOne(Journal::class);
    }

    /**
     * Accessor untuk mendapatkan label status
     */
    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            self::STATUS_HADIR => 'Hadir',
            self::STATUS_TERLAMBAT => 'Terlambat',
            self::STATUS_IZIN => 'Izin',
            self::STATUS_SAKIT => 'Sakit',
            self::STATUS_ALPA => 'Alpa',
            default => 'Tidak Diketahui',
        };
    }

    /**
     * Accessor untuk mendapatkan badge class berdasarkan status
     */
    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            self::STATUS_HADIR => 'success',
            self::STATUS_TERLAMBAT => 'warning',
            self::STATUS_IZIN => 'info',
            self::STATUS_SAKIT => 'secondary',
            self::STATUS_ALPA => 'danger',
            default => 'dark',
        };
    }

    /**
     * Accessor untuk mendapatkan durasi kerja (dari check-in sampai check-out)
     */
    public function getWorkDurationAttribute()
    {
        if (!$this->time_in || !$this->time_out) {
            return null;
        }

        $timeIn = Carbon::createFromTimeString($this->time_in);
        $timeOut = Carbon::createFromTimeString($this->time_out);

        $diff = $timeIn->diff($timeOut);
        
        return sprintf('%d jam %d menit', $diff->h, $diff->i);
    }

    /**
     * Accessor untuk cek apakah sudah check-in
     */
    public function getHasCheckedInAttribute()
    {
        return !is_null($this->time_in);
    }

    /**
     * Accessor untuk cek apakah sudah check-out
     */
    public function getHasCheckedOutAttribute()
    {
        return !is_null($this->time_out);
    }

    /**
     * Accessor untuk cek apakah jurnal sudah dibuat
     */
    public function getHasJournalAttribute()
    {
        return $this->journal()->exists();
    }

    /**
     * Scope untuk filter absensi hari ini
     */
    public function scopeToday($query)
    {
        return $query->whereDate('date', Carbon::today());
    }

    /**
     * Scope untuk filter absensi berdasarkan tanggal
     */
    public function scopeByDate($query, $date)
    {
        return $query->whereDate('date', $date);
    }

    /**
     * Scope untuk filter absensi berdasarkan user
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope untuk filter absensi yang terlambat
     */
    public function scopeLate($query)
    {
        return $query->where('is_late', true);
    }

    /**
     * Scope untuk filter absensi berdasarkan status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope untuk filter absensi yang belum check-out
     */
    public function scopeNotCheckedOut($query)
    {
        return $query->whereNull('time_out');
    }

    /**
     * Scope untuk filter absensi yang sudah lengkap (ada check-in dan check-out)
     */
    public function scopeComplete($query)
    {
        return $query->whereNotNull('time_in')
                     ->whereNotNull('time_out');
    }
}