<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pembayaran extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'tagihan_id',
        'status', 
        'bukti',
        'metode',
        'tanggal_bayar',
        'keterangan_pembimbing',
    ];

    public function tagihan()
    {
        return $this->belongsTo(Tagihan::class, 'tagihan_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected $casts = [
        'tenggat' => 'datetime',
        'tanggal_bayar' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];


}

