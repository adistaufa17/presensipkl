<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PengaturanJamKerja extends Model
{
    use HasFactory;

    protected $table = 'pengaturan_jam_kerjas';

    protected $fillable = [
        'jam_masuk',
        'jam_pulang',
        'batas_telat',
        'is_active',
    ];

    protected $casts = [
        'jam_masuk' => 'datetime:H:i:s',
        'jam_pulang' => 'datetime:H:i:s',
        'batas_telat' => 'datetime:H:i:s',
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function isTerlambat($jamSekarang = null)
    {
        $jam = $jamSekarang ? Carbon::parse($jamSekarang) : Carbon::now();
        $batasTelat = Carbon::parse($this->batas_telat);
        
        return $jam->greaterThan($batasTelat);
    }

    public function isDalamJamKerja($jamSekarang = null)
    {
        $jam = $jamSekarang ? Carbon::parse($jamSekarang) : Carbon::now();
        $jamMasuk = Carbon::parse($this->jam_masuk);
        $jamPulang = Carbon::parse($this->jam_pulang);
        
        return $jam->between($jamMasuk, $jamPulang);
    }

    public function getStatusKehadiran($jamSekarang = null)
    {
        $jam = $jamSekarang ? Carbon::parse($jamSekarang) : Carbon::now();
        $jamMasuk = Carbon::parse($this->jam_masuk);
        $batasTelat = Carbon::parse($this->batas_telat);
        $jamPulang = Carbon::parse($this->jam_pulang);

        if ($jam->lessThan($jamMasuk) || $jam->greaterThan($jamPulang)) {
            return 'diluar_jam_kerja';
        }

        if ($jam->greaterThan($batasTelat)) {
            return 'telat';
        }

        return 'hadir';
    }

    public function getJamMasukFormatted()
    {
        return Carbon::parse($this->jam_masuk)->format('H:i');
    }

    public function getJamPulangFormatted()
    {
        return Carbon::parse($this->jam_pulang)->format('H:i');
    }

    public function getBatasTelatFormatted()
    {
        return Carbon::parse($this->batas_telat)->format('H:i');
    }

    public function getDurasiKerja()
    {
        $jamMasuk = Carbon::parse($this->jam_masuk);
        $jamPulang = Carbon::parse($this->jam_pulang);
        
        return $jamPulang->diffInHours($jamMasuk);
    }

    public function getToleransiTelat()
    {
        $jamMasuk = Carbon::parse($this->jam_masuk);
        $batasTelat = Carbon::parse($this->batas_telat);
        
        return $jamMasuk->diffInMinutes($batasTelat);
    }

    public function getJamMasukAttribute($value)
    {
        return $value ? Carbon::parse($value)->format('H:i:s') : null;
    }

    public function getJamPulangAttribute($value)
    {
        return $value ? Carbon::parse($value)->format('H:i:s') : null;
    }

    
    public function getBatasTelatAttribute($value)
    {
        return $value ? Carbon::parse($value)->format('H:i:s') : null;
    }

    public static function getActive()
    {
        return self::where('is_active', true)->first();
    }

    public function setAsActive()
    {
        self::query()->update(['is_active' => false]);
        
        $this->update(['is_active' => true]);
        
        return $this;
    }

    public function isSudahWaktuPulang($jamSekarang = null)
    {
        $jam = $jamSekarang ? Carbon::parse($jamSekarang) : Carbon::now();
        $jamPulang = Carbon::parse($this->jam_pulang);
        
        return $jam->greaterThanOrEqualTo($jamPulang);
    }

    public function getKeteranganStatus($jamSekarang = null)
    {
        $status = $this->getStatusKehadiran($jamSekarang);
        
        return match($status) {
            'hadir' => 'Tepat Waktu',
            'telat' => 'Terlambat',
            'diluar_jam_kerja' => 'Di Luar Jam Kerja',
            default => 'Status Tidak Diketahui'
        };
    }

    public static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            $jamMasuk = Carbon::parse($model->jam_masuk);
            $batasTelat = Carbon::parse($model->batas_telat);
            $jamPulang = Carbon::parse($model->jam_pulang);

            if ($jamMasuk->greaterThanOrEqualTo($batasTelat)) {
                throw new \Exception('Jam masuk harus lebih awal dari batas telat!');
            }

            if ($batasTelat->greaterThanOrEqualTo($jamPulang)) {
                throw new \Exception('Batas telat harus lebih awal dari jam pulang!');
            }
        });
    }
}