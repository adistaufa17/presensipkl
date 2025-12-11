<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Presensi;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class PresensiController extends Controller
{
    // --- FUNGSI BANTUAN (Hitung Summary) ---
    private function getRingkasanPresensi($user_id)
    {
        $bulanIni = Carbon::now()->month;
        $tahunIni = Carbon::now()->year; // Tambahkan filter tahun agar lebih spesifik
        
        $summary = [
            // Menghitung status 'hadir' DAN 'terlambat' sebagai Kehadiran
            'hadir' => Presensi::where('user_id', $user_id)
                        ->whereMonth('tanggal', $bulanIni)
                        ->whereYear('tanggal', $tahunIni)
                        ->whereIn('status', ['hadir', 'terlambat']) 
                        ->count(),

            'izin' => Presensi::where('user_id', $user_id)
                        ->whereMonth('tanggal', $bulanIni)
                        ->whereYear('tanggal', $tahunIni)
                        ->where('status', 'izin')
                        ->count(),

            'sakit' => Presensi::where('user_id', $user_id)
                        ->whereMonth('tanggal', $bulanIni)
                        ->whereYear('tanggal', $tahunIni)
                        ->where('status', 'sakit')
                        ->count(),
            
            'alpa' => Presensi::where('user_id', $user_id)
                        ->whereMonth('tanggal', $bulanIni)
                        ->whereYear('tanggal', $tahunIni)
                        ->where('status', 'alpa')
                        ->count(),
        ];

        // Total Izin + Sakit
        $summary['izin_total'] = $summary['izin'] + $summary['sakit'];
        
        return $summary;
    }

    // --- DASHBOARD SISWA (Penting untuk file dashboardSiswa.blade.php) ---
    public function dashboardSiswa()
    {
        $user_id = Auth::id();
        $today = Carbon::today();
        $tahunIni = Carbon::now()->year; // Ambil tahun saat ini

        // 1. Data Presensi Hari Ini
        $presensiHariIni = Presensi::where('user_id', $user_id)
                            ->whereDate('tanggal', $today)
                            ->first();

        // 2. Data Summary (Ringkasan Angka)
        $summary = $this->getRingkasanPresensi($user_id);

        // 3. Persentase Kehadiran (Total Global)
        $totalHariKerja = 20; 
        $persentaseHadir = ($totalHariKerja > 0) ? ($summary['hadir'] / $totalHariKerja) * 100 : 0;

        // --- BARU: LOGIKA GRAFIK BULANAN (Januari - Desember) ---
        $dataGrafik = [];

        // Loop Bulan 1 sampai 12
        for ($bulan = 1; $bulan <= 12; $bulan++) {
            
            // Hitung Jumlah Hadir + Terlambat di bulan tersebut
            $totalHadirBulanIni = Presensi::where('user_id', $user_id)
                                    ->whereYear('tanggal', $tahunIni)
                                    ->whereMonth('tanggal', $bulan)
                                    ->whereIn('status', ['hadir', 'terlambat'])
                                    ->count();

            // Hitung Tinggi Grafik (Asumsi max 25 hari kerja agar grafik penuh)
            $maxHariKerja = 25; 
            $persentase = ($totalHadirBulanIni / $maxHariKerja) * 100;
            
            // Batasi max 100% dan min 5% (biar grafik kosong tetap ada garis tipis)
            $height = max(5, min($persentase, 100)) . '%'; 

            // Tentukan Warna berdasarkan kerajinan
            // Hijau jika hadir > 15 hari, Kuning jika > 5, Sisanya Abu-abu
            if ($totalHadirBulanIni >= 20) {
                $color = 'bg-success'; // Sangat Rajin
            } elseif ($totalHadirBulanIni >= 10) {
                $color = 'bg-primary'; // Lumayan
            } elseif ($totalHadirBulanIni > 0) {
                $color = 'bg-warning'; // Kurang
            } else {
                $color = 'bg-light'; // Kosong/Belum ada data
            }

            // Nama Bulan (Jan, Feb, dst)
            $namaBulan = Carbon::createFromDate($tahunIni, $bulan, 1)->locale('id')->isoFormat('MMM');

            $dataGrafik[] = [
                'label' => $namaBulan,
                'height' => $height,
                'color' => $color,
                'total' => $totalHadirBulanIni
            ];
        }
        
        return view('pembayaran.dashboardSiswa', compact('presensiHariIni', 'summary', 'persentaseHadir', 'dataGrafik'));
    }

    // --- STORE MASUK (Dipanggil route presensi.masuk) ---
    public function storeMasuk()
    {
        $user_id = Auth::id();
        $now = Carbon::now();
        $jamMasuk = $now->format('H:i');
        $batasJamMasuk = Carbon::today()->setHour(8)->setMinute(0); 

        $cek = Presensi::where('user_id', $user_id)->whereDate('tanggal', $now->toDateString())->first();
        if ($cek) {
            return response()->json(['status' => 'error', 'message' => 'Anda sudah absen masuk hari ini!']);
        }

        $status = $now->lessThan($batasJamMasuk) ? 'hadir' : 'terlambat';
        $pesan = $now->lessThan($batasJamMasuk) ? "Anda masuk jam " . $jamMasuk : "Anda terlambat!!!";

        Presensi::create([
            'user_id' => $user_id,
            'tanggal' => $now->toDateString(),
            'jam_masuk' => $now->toTimeString(),
            'status' => $status
        ]);

        return response()->json(['status' => 'success', 'message' => $pesan]);
    }

    // --- STORE KELUAR ---
    public function storeKeluar()
    {
        $now = Carbon::now();
        $presensi = Presensi::where('user_id', Auth::id())
                    ->whereDate('tanggal', $now->toDateString())
                    ->first();

        if (!$presensi) return response()->json(['status' => 'error', 'message' => 'Anda belum absen masuk!']);
        if ($presensi->jam_keluar) return response()->json(['status' => 'error', 'message' => 'Anda sudah absen keluar!']);

        $presensi->update(['jam_keluar' => $now->toTimeString()]);
        return response()->json(['status' => 'success', 'message' => 'Hati-hati di jalan!']);
    }

  // ... (kode fungsi getRingkasanPresensi, dashboardSiswa, dll tetap sama) ...

    public function createIzin()
    {
        // Panggil fungsi summary agar tidak error jika layout membutuhkannya
        $summary = $this->getRingkasanPresensi(Auth::id());
        
        // FIX: Sesuai screenshot folder kamu, file ada di folder 'pembayaran'
        // Jadi panggil 'pembayaran.izin', BUKAN 'presensi.izin'
        return view('pembayaran.izin', compact('summary')); 
    }

    public function storeIzin(Request $request)
    {
        $request->validate([
            'kategori' => 'required',
            'keterangan' => 'required',
            'bukti' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $path = $request->file('bukti')->store('public/bukti_izin');

        Presensi::create([
            'user_id' => Auth::id(),
            'tanggal' => Carbon::now()->toDateString(),
            'status' => $request->kategori, 
            'keterangan' => $request->keterangan,
            'bukti_foto' => $path
        ]);

        // Redirect kembali ke dashboard siswa setelah berhasil
        return redirect()->route('pembayaran.dashboardsiswa')->with('success', 'Pengajuan berhasil dikirim.');
    }
}