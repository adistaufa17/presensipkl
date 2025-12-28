<?php

namespace App\Models;

use App\Models\Siswa;
use Illuminate\Database\Eloquent\Model;

class Sekolah extends Model
{

    protected $fillable = ['nama_sekolah'];

    public function siswas()
    {
        return $this->hasMany(Siswa::class, 'sekolah_id');
    }

    public function siswa() {
        return $this->hasMany(Siswa::class, 'sekolah_id');
    }
}
