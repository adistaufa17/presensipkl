<?php

namespace App\Models;

use App\Models\User;
use App\Models\Sekolah;
use App\Models\Tagihan;
use App\Models\Presensi;
use App\Models\TagihanSiswa;
use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    protected $guarded = []; 

    protected $casts = [
        'tanggal_mulai_pkl' => 'date',
        'tanggal_selesai_pkl' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function sekolah()
    {
        return $this->belongsTo(Sekolah::class, 'sekolah_id');
    }

    public function presensis()
    {
        return $this->hasMany(Presensi::class);
    }

    public function tagihanSiswas()
    {
        return $this->hasMany(TagihanSiswa::class);
    }

    public function tagihans() {
        return $this->hasMany(Tagihan::class);
    }
}
