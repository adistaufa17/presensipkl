<?php

namespace App\Models;

use App\Models\User;
use App\Models\Siswa;
use App\Models\Tagihan;
use Illuminate\Database\Eloquent\Model;

class TagihanSiswa extends Model
{
    protected $guarded = [];

    protected $fillable = [
        'tagihan_id', 
        'siswa_id', 
        'bulan_ke', 
        'jatuh_tempo', 
        'status', 
        'bukti_pembayaran', 
        'tanggal_bayar',
        'dikonfirmasi_oleh', 
        'catatan_admin'
    ];
    
    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    public function tagihan()
    {
        return $this->belongsTo(Tagihan::class, 'tagihan_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'dikonfirmasi_oleh');
    }
}
