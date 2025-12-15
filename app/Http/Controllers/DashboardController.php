<?php

namespace App\Http\Controllers;

use App\Models\Presensi;
use App\Models\User;
use App\Models\Tagihan;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Pembayaran;


class DashboardController extends Controller
{
    public function adminPembimbing()
    {
        $today = now()->toDateString();
        $currentMonth = now()->month;
        $currentYear = now()->year;
        
        $user = auth()->user();

        // ==============================
        // FILTER KHUSUS PEMBIMBING
        // ==============================
        if ($user->role === 'pembimbing') {

            // 🔑 AMBIL SISWA BIMBINGAN
            $pembimbing = auth()->user();
            $siswaIds = $pembimbing->siswaBimbingan()->pluck('id');

            // 1. Total siswa bimbingan
            $totalSiswa = $pembimbing->siswaBimbingan()->count();

            // 2. Hadir hari ini
            $hadirHariIni = Presensi::where('tanggal', $today)
            ->whereIn('user_id', $siswaIds)
            ->whereIn('status', ['hadir', 'terlambat'])
            ->count();

            $terlambatHariIni = Presensi::where('tanggal', $today)
            ->whereIn('user_id', $siswaIds)
            ->where('status', 'terlambat')
            ->count();
           
            $izinSakitHariIni = Presensi::where('tanggal', $today)
            ->whereIn('user_id', $siswaIds)
            ->whereIn('status', ['izin', 'sakit'])
            ->count();

            $rekapHadir = Presensi::whereIn('user_id', $siswaIds)
                ->whereMonth('tanggal', $currentMonth)
                ->whereYear('tanggal', $currentYear)
                ->where('status', 'hadir')
                ->count();

            $rekapTerlambat = Presensi::whereIn('user_id', $siswaIds)
                ->whereMonth('tanggal', $currentMonth)
                ->whereYear('tanggal', $currentYear)
                ->where('status', 'terlambat')
                ->count();

            $rekapIzin = Presensi::whereIn('user_id', $siswaIds)
                ->whereMonth('tanggal', $currentMonth)
                ->whereYear('tanggal', $currentYear)
                ->whereIn('status', ['izin', 'sakit'])
                ->count();

            $rekapAlpa = Presensi::whereIn('user_id', $siswaIds)
                ->whereMonth('tanggal', $currentMonth)
                ->whereYear('tanggal', $currentYear)
                ->where('status', 'alpa')
                ->count();

            $recentActivities = Presensi::where('tanggal', $today)
            ->whereIn('user_id', $siswaIds)
            ->with('user')
            ->orderBy('updated_at', 'desc')
            ->limit(20)
            ->get();

            $totalTagihan = Pembayaran::whereIn('user_id', $siswaIds)->count();
            $belumBayar   = Pembayaran::whereIn('user_id', $siswaIds)->where('status', 'belum_bayar')->count();
            $pending      = Pembayaran::whereIn('user_id', $siswaIds)->where('status', 'pending')->count();
            $diterima     = Pembayaran::whereIn('user_id', $siswaIds)->where('status', 'diterima')->count();
            $ditolak      = Pembayaran::whereIn('user_id', $siswaIds)->where('status', 'ditolak')->count();
            $totalNominalDiterima = Pembayaran::whereIn('user_id', $siswaIds)
                                            ->where('status', 'diterima')
                                            ->sum('nominal');
            $pendingPayments = Pembayaran::with('user')
                ->whereIn('user_id', $siswaIds)
                ->where('status', 'pending')
                ->orderBy('tanggal_bayar', 'desc')
                ->take(10)
                ->get();

        }
        else {
            // fallback admin (boleh nanti kita rapihin)
            abort(403);
        }

        return view('pembimbing.dashboard', compact(
            'totalSiswa',
            'hadirHariIni',
            'terlambatHariIni',
            'izinSakitHariIni',
            'rekapHadir',
            'rekapTerlambat',
            'rekapIzin',
            'rekapAlpa',
            'recentActivities',
            'totalTagihan',
            'belumBayar',
            'pending',
            'diterima',
            'ditolak',
            'totalNominalDiterima',
            'pendingPayments'
        ));
    }

    public function siswa()
    {
        $userId = auth()->id();

        // Ambil semua tagihan
        $semuaTagihan = \App\Models\Tagihan::orderBy('tenggat', 'asc')->get();

        // Filter hanya yang belum dibayar oleh user ini
        $tagihanBelumBayar = $semuaTagihan->filter(function($tagihan) use ($userId) {
            // Cek apakah user sudah bayar tagihan ini
            $pembayaran = \App\Models\Pembayaran::where('user_id', $userId)
                ->where('tagihan_id', $tagihan->id)
                ->whereIn('status', ['diterima', 'pending'])
                ->first();
            
            return !$pembayaran; // Return true jika belum ada pembayaran
        })->take(3);
        
        // Ambil data presensi untuk kalender
        $presensiData = \App\Models\Presensi::where('user_id', auth()->id())
            ->whereYear('tanggal', date('Y'))
            ->get()
            ->keyBy('tanggal')
            ->map(function($p) {
                return [
                    'status' => $p->status, 
                    'jam_masuk' => $p->jam_masuk, 
                    'jam_keluar' => $p->jam_keluar
                ];
            });
        
        return view('siswa.dashboard', compact('tagihanBelumBayar', 'presensiData'));
    }

    public function getRealtimeData()
    {
        $today = now()->toDateString();
        $pembimbing = auth()->user();
        $siswaIds = $pembimbing->siswaBimbingan()->pluck('id');

        return response()->json([
            'hadir' => Presensi::where('tanggal', $today)
            ->whereIn('user_id', $siswaIds)
            ->whereIn('status', ['hadir', 'terlambat'])
            ->count(),
            'terlambat' => Presensi::where('tanggal', $today)
                ->where('status', 'terlambat')
                ->count(),
            'izin_sakit' => Presensi::where('tanggal', $today)
                ->whereIn('status', ['izin', 'sakit'])
                ->count(),
            'recent' => Presensi::where('tanggal', $today)
                ->with('user')
                ->orderBy('updated_at', 'desc')
                ->limit(5)
                ->get()
        ]);
    }
}