<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tagihan extends Model
{
    protected $fillable = [
        'siswa_id',     
        'nama_tagihan', 
        'nominal', 
        'jatuh_tempo', 
        'status'
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    public function tagihanSiswas()
    {
        return $this->hasMany(TagihanSiswa::class, 'tagihan_id');
    }
}