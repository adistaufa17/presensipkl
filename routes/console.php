<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Models\Siswa;
use App\Models\Presensi;
use Carbon\Carbon;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(function () {
    $today = Carbon::today();
    $siswas = Siswa::all();

    foreach ($siswas as $siswa) {
        $exists = Presensi::where('siswa_id', $siswa->id)
            ->whereDate('tanggal', $today)
            ->exists();

        if (!$exists) {
            Presensi::create([
                'siswa_id' => $siswa->id,
                'tanggal' => $today,
                'status_kehadiran' => 'alpha', 
                'jam_masuk' => null,
            ]);
        }
    }
})->dailyAt('08:01'); 