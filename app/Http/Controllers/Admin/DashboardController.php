<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\Presensi;
use App\Models\TagihanSiswa;
use App\Models\Sekolah;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();
        $totalSiswa = Siswa::count();
        $hadirHariIni = Presensi::whereDate('tanggal', $today)
            ->where('status_kehadiran', 'hadir')
            ->count();    
        $terlambatHariIni = Presensi::whereDate('tanggal', $today)
            ->where('status_kehadiran', 'terlambat')
            ->count();
        $izinSakitHariIni = Presensi::whereDate('tanggal', $today)
            ->whereIn('status_kehadiran', ['izin', 'sakit'])
            ->count();
        $alpaHariIni = Presensi::whereDate('tanggal', $today)
            ->where('status_kehadiran', 'alpha')
            ->count();
        $rekapHadir = Presensi::whereMonth('tanggal', $startOfMonth->month)
            ->whereYear('tanggal', $startOfMonth->year)
            ->where('status_kehadiran', 'hadir')->count();           
        $rekapTerlambat = Presensi::whereMonth('tanggal', $startOfMonth->month)
            ->whereYear('tanggal', $startOfMonth->year)
            ->where('status_kehadiran', 'terlambat')->count();       
        $rekapIzin = Presensi::whereMonth('tanggal', $startOfMonth->month)
            ->whereYear('tanggal', $startOfMonth->year)
            ->whereIn('status_kehadiran', ['izin', 'sakit'])->count();
        $rekapAlpa = Presensi::whereMonth('tanggal', $startOfMonth->month)
            ->whereYear('tanggal', $startOfMonth->year)
            ->where('status_kehadiran', 'alpha')->count();
        $recentActivities = Presensi::with(['siswa.user'])
            ->whereDate('tanggal', $today)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        $statusMenunggu = 'menunggu_konfirmasi'; 
        $totalTagihan = TagihanSiswa::count();
        $tagihanLunas = TagihanSiswa::where('status', 'dibayar')
            ->whereMonth('tanggal_bayar', now()->month)
            ->count();
        $tagihanMenunggu = TagihanSiswa::where('status', $statusMenunggu)->count();
        $tagihanBelumBayar = TagihanSiswa::where('status', 'belum_bayar')->count();
        $pembayaranMenunggu = TagihanSiswa::where('status', $statusMenunggu)
            ->with(['siswa.user', 'tagihan'])
            ->orderBy('updated_at', 'desc')
            ->limit(5) 
            ->get();
        $sekolahData = Sekolah::withCount('siswa')->get();

        return view('admin.dashboard', compact(
            'totalSiswa',
            'hadirHariIni',
            'terlambatHariIni',
            'izinSakitHariIni',
            'alpaHariIni',
            'rekapHadir',
            'rekapTerlambat',
            'rekapIzin',
            'rekapAlpa',
            'recentActivities',
            'totalTagihan',
            'tagihanLunas',
            'tagihanMenunggu',
            'tagihanBelumBayar',
            'pembayaranMenunggu',
            'sekolahData'
        ));
    }
}