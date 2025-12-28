<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tagihan extends Model
{
    // Hapus siswa_id dari sini! Master tagihan tidak butuh siswa_id
    protected $fillable = [
        'siswa_id', 
        'nama_tagihan', 
        'nominal', 
        'jatuh_tempo', 
        'status'
    ];

    public function tagihanSiswas()
    {
        return $this->hasMany(TagihanSiswa::class, 'tagihan_id');
    }
}