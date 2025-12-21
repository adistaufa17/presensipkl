<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Presensi;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        // 1. DATA KARTU STATISTIK
        $totalSiswa = User::where('role', 'siswa')->count(); 
        $hadirHariIni = Presensi::whereDate('tanggal', $today)->where('status', 'hadir')->count();
        $terlambatHariIni = Presensi::whereDate('tanggal', $today)->where('status', 'terlambat')->count();
        $izinSakitHariIni = Presensi::whereDate('tanggal', $today)->whereIn('status', ['izin', 'sakit'])->count();

        // 2. DATA GRAFIK
        $bulanIni = Carbon::now()->month;
        $rekapHadir = Presensi::whereMonth('tanggal', $bulanIni)->where('status', 'hadir')->count();
        $rekapTerlambat = Presensi::whereMonth('tanggal', $bulanIni)->where('status', 'terlambat')->count();
        $rekapIzin = Presensi::whereMonth('tanggal', $bulanIni)->whereIn('status', ['izin', 'sakit'])->count();
        $rekapAlpa = Presensi::whereMonth('tanggal', $bulanIni)->where('status', 'alpa')->count();

        // 3. AKTIVITAS TERBARU (GABUNGAN MONITORING)
        // Mengambil 10 aktivitas presensi TERBARU khusus HARI INI
        $recentActivities = Presensi::with('user') 
                            ->whereDate('tanggal', $today)
                            ->orderBy('updated_at', 'desc') // Paling baru (masuk/pulang) ada di atas
                            ->take(10)
                            ->get();

        return view('admin.dashboard', compact(
            'totalSiswa', 'hadirHariIni', 'terlambatHariIni', 'izinSakitHariIni',
            'rekapHadir', 'rekapTerlambat', 'rekapIzin', 'rekapAlpa',
            'recentActivities'
        ));
    }
}