<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Siswa;
use App\Models\Presensi;
use Carbon\Carbon;

class GenerateAlphaHarian extends Command
{
    // Ini nama perintah yang dipanggil nanti
    protected $signature = 'presensi:generate-alpha';
    protected $description = 'Otomatis membuat status alpha untuk siswa yang belum absen';

    public function handle()
    {
        $today = Carbon::today()->toDateString();
        $siswas = Siswa::all();

        foreach ($siswas as $siswa) {
            // Cek apakah hari ini siswa sudah ada record (hadir/telat/izin)
            $sudahAda = Presensi::where('siswa_id', $siswa->id)
                                ->whereDate('tanggal', $today)
                                ->exists();

            if (!$sudahAda) {
                Presensi::create([
                    'siswa_id' => $siswa->id,
                    'tanggal' => $today,
                    'status_kehadiran' => 'alpha', // Pastikan pakai 'alpha' sesuai migration
                ]);
            }
        }

        $this->info('Status alpha berhasil dibuat untuk siswa yang tidak hadir.');
    }
}