<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tagihan extends Model
{
    protected $fillable = [
        'pembimbing_id',
        'nama',
        'kategori',
        'nominal',
        'bulan',
        'tenggat',
        'keterangan',
    ];

    protected $casts = [
        'tenggat' => 'date',
        'tanggal_bayar' => 'datetime',
    ];

    /**
     * Relasi: Tagihan milik User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope: Tagihan belum bayar
     */
    public function scopeBelumBayar($query)
    {
        return $query->where('status', 'belum_bayar');
    }

    /**
     * Scope: Tagihan pending
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope: Tagihan sudah diterima
     */
    public function scopeDiterima($query)
    {
        return $query->where('status', 'diterima');
    }

    /**
     * Scope: Tagihan telat (lewat tenggat)
     */
    public function scopeTelat($query)
    {
        return $query->where('status', 'belum_bayar')
                     ->where('tenggat', '<', now());
    }

    /**
     * Accessor: Check apakah tagihan telat
     */
    public function isTelat()
    {
        return $this->status === 'belum_bayar' && $this->tenggat < now();
    }

    public function pembayarans()
    {
        return $this->hasMany(Pembayaran::class, 'tagihan_id');
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'belum_bayar' => 'danger',
            'pending' => 'warning',
            'diterima' => 'success',
            default => 'secondary'
        };
    }
}
