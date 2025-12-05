<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Permission extends Model
{
    use HasFactory;

    /**
     * Kolom yang bisa diisi secara mass assignment
     */
    protected $fillable = [
        'user_id',
        'date',
        'type',
        'reason',
        'proof_path',
        'status',
        'note',
        'verified_at',
    ];

    /**
     * Cast tipe data kolom
     */
    protected $casts = [
        'date' => 'date',
        'verified_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Konstanta untuk tipe izin
     */
    const TYPE_IZIN = 'izin';
    const TYPE_SAKIT = 'sakit';

    /**
     * Konstanta untuk status izin
     */
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    /**
     * Relasi ke User (siswa yang mengajukan izin)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Accessor untuk mendapatkan URL bukti izin
     */
    public function getProofUrlAttribute()
    {
        if ($this->proof_path) {
            return Storage::disk('public')->url($this->proof_path);
        }

        return null;
    }

    /**
     * Accessor untuk mendapatkan label status (untuk display)
     */
    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            self::STATUS_PENDING => 'Menunggu',
            self::STATUS_APPROVED => 'Disetujui',
            self::STATUS_REJECTED => 'Ditolak',
            default => 'Tidak Diketahui',
        };
    }

    /**
     * Accessor untuk mendapatkan badge class berdasarkan status
     */
    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            self::STATUS_PENDING => 'warning',
            self::STATUS_APPROVED => 'success',
            self::STATUS_REJECTED => 'danger',
            default => 'secondary',
        };
    }

    /**
     * Accessor untuk mendapatkan label tipe izin
     */
    public function getTypeLabelAttribute()
    {
        return match($this->type) {
            self::TYPE_IZIN => 'Izin',
            self::TYPE_SAKIT => 'Sakit',
            default => 'Tidak Diketahui',
        };
    }

    /**
     * Scope untuk filter izin pending
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope untuk filter izin approved
     */
    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    /**
     * Scope untuk filter izin rejected
     */
    public function scopeRejected($query)
    {
        return $query->where('status', self::STATUS_REJECTED);
    }

    /**
     * Scope untuk filter izin berdasarkan tanggal
     */
    public function scopeByDate($query, $date)
    {
        return $query->whereDate('date', $date);
    }

    /**
     * Scope untuk filter izin berdasarkan user
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope untuk filter berdasarkan tipe
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Boot method untuk handle delete proof saat permission dihapus
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($permission) {
            if ($permission->proof_path && Storage::disk('public')->exists($permission->proof_path)) {
                Storage::disk('public')->delete($permission->proof_path);
            }
        });
    }
}