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
        // 1. Inisialisasi Waktu
        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();

        // 2. Statistik Dasar
        $totalSiswa = Siswa::count();

        // 3. Statistik Presensi Hari Ini
        $hadirHariIni = Presensi::whereDate('tanggal', $today)
            ->where('status_kehadiran', 'hadir')
            ->count();
            
        $terlambatHariIni = Presensi::whereDate('tanggal', $today)
            ->where('status_kehadiran', 'telat')
            ->count();
            
        $izinSakitHariIni = Presensi::whereDate('tanggal', $today)
            ->whereIn('status_kehadiran', ['izin', 'sakit'])
            ->count();

        // Alpha Hari Ini (Data yang sudah di-generate oleh tombol admin)
        $alpaHariIni = Presensi::whereDate('tanggal', $today)
            ->where('status_kehadiran', 'alpha')
            ->count();

        // 4. Rekap Bulanan (Untuk Grafik)
        $rekapHadir = Presensi::whereMonth('tanggal', $startOfMonth->month)
            ->whereYear('tanggal', $startOfMonth->year)
            ->where('status_kehadiran', 'hadir')->count();
            
        $rekapTerlambat = Presensi::whereMonth('tanggal', $startOfMonth->month)
            ->whereYear('tanggal', $startOfMonth->year)
            ->where('status_kehadiran', 'telat')->count();
            
        $rekapIzin = Presensi::whereMonth('tanggal', $startOfMonth->month)
            ->whereYear('tanggal', $startOfMonth->year)
            ->whereIn('status_kehadiran', ['izin', 'sakit'])->count();
            
        $rekapAlpa = Presensi::whereMonth('tanggal', $startOfMonth->month)
            ->whereYear('tanggal', $startOfMonth->year)
            ->where('status_kehadiran', 'alpha')->count();

        // 5. Aktivitas Terbaru
        $recentActivities = Presensi::with(['siswa.user'])
            ->whereDate('tanggal', $today)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // 6. Statistik Pembayaran
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

        // 7. Data Sekolah
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