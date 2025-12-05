<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Journal extends Model
{
    use HasFactory;

    /**
     * Kolom yang bisa diisi secara mass assignment
     */
    protected $fillable = [
        'user_id',
        'presence_id',
        'date',
        'title',
        'description',
        'photo_path',
    ];

    /**
     * Cast tipe data kolom
     */
    protected $casts = [
        'date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relasi ke User (siswa yang membuat jurnal)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke Presence (absensi terkait)
     */
    public function presence()
    {
        return $this->belongsTo(Presence::class);
    }

    /**
     * Accessor untuk mendapatkan URL foto jurnal
     */
    public function getPhotoUrlAttribute()
    {
        if ($this->photo_path) {
            return Storage::disk('public')->url($this->photo_path);
        }

        return asset('images/default-journal.png'); // Foto default jika tidak ada
    }

    /**
     * Scope untuk filter jurnal berdasarkan tanggal
     */
    public function scopeByDate($query, $date)
    {
        return $query->whereDate('date', $date);
    }

    /**
     * Scope untuk filter jurnal berdasarkan user
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Boot method untuk handle delete photo saat jurnal dihapus
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($journal) {
            if ($journal->photo_path && Storage::disk('public')->exists($journal->photo_path)) {
                Storage::disk('public')->delete($journal->photo_path);
            }
        });
    }
}