<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tagihan extends Model
{
    protected $fillable = [
        'nama',
        'kategori',
        'nominal',
        'bulan',
        'tenggat',
        'keterangan',
    ];
}
