<?php

namespace App\Http\Controllers;

use App\Models\Presensi;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PresensiController extends Controller
{
    /**
     * Absen Masuk
     */
    public function storeMasuk()
    {
        // ✅ Set timezone
        $now = Carbon::now('Asia/Jakarta');
        $today = $now->toDateString();
        $userId = auth()->id();

        // Cek apakah sudah absen hari ini
        $presensi = Presensi::where('user_id', $userId)
            ->where('tanggal', $today)
            ->first();

        if ($presensi) {
            return back()->with('error', 'Anda sudah absen masuk hari ini!');
        }

        // Tentukan status berdasarkan jam masuk
        $jamMasuk = $now;
        $batasWaktu = Carbon::parse($today . ' 08:00:00', 'Asia/Jakarta');
        
        $status = $jamMasuk->greaterThan($batasWaktu) ? 'terlambat' : 'hadir';

        // Simpan presensi
        Presensi::create([
            'user_id' => $userId,
            'tanggal' => $today,
            'jam_masuk' => $jamMasuk->format('H:i:s'),
            'status' => $status,
        ]);

        $message = $status == 'terlambat' 
            ? 'Presensi masuk berhasil. Anda terlambat!' 
            : 'Presensi masuk berhasil!';

        return back()->with('success', $message);
    }

    /**
     * Absen Keluar
     */
    public function storeKeluar()
    {
        // ✅ Set timezone
        $now = Carbon::now('Asia/Jakarta');
        $today = $now->toDateString();
        $userId = auth()->id();

        // Cari presensi hari ini
        $presensi = Presensi::where('user_id', $userId)
            ->where('tanggal', $today)
            ->first();

        // Validasi: Belum absen masuk
        if (!$presensi || !$presensi->jam_masuk) {
            return back()->with('error', 'Anda belum absen masuk hari ini!');
        }

        // Validasi: Sudah absen keluar
        if ($presensi->jam_keluar) {
            return back()->with('error', 'Anda sudah absen keluar hari ini!');
        }

        // Validasi: Cek waktu minimal (6 jam kerja)
        $jamMasuk = Carbon::parse($today . ' ' . $presensi->jam_masuk, 'Asia/Jakarta');
        $jamKeluar = $now;
        $durasi = $jamMasuk->diffInHours($jamKeluar);

        if ($durasi < 6) {
            return back()->with('error', 'Anda belum bisa absen keluar! Minimal 6 jam kerja.');
        }

        // Update jam keluar
        $presensi->update([
            'jam_keluar' => $jamKeluar->format('H:i:s'),
        ]);

        return back()->with('success', 'Presensi keluar berhasil! Hati-hati di jalan.');
    }

    /**
     * Form Pengajuan Izin/Sakit
     */
    public function createIzin()
    {
        return view('izin');
    }

    /**
     * ✅ FIXED: Simpan Pengajuan Izin/Sakit dengan Logika yang Benar
     */
    public function storeIzin(Request $request)
    {
        // Validasi input
        $request->validate([
            'tanggal' => 'required|date', // ✅ Hapus after_or_equal agar bisa izin kemarin
            'status' => 'required|in:izin,sakit',
            'keterangan' => 'nullable|string|max:500',
        ], [
            'tanggal.required' => 'Tanggal harus diisi',
            'tanggal.date' => 'Format tanggal tidak valid',
            'status.required' => 'Status harus dipilih',
            'status.in' => 'Status hanya boleh Izin atau Sakit',
        ]);

        $userId = auth()->id();
        $tanggal = $request->tanggal;
        $tanggalCarbon = Carbon::parse($tanggal);
        $today = Carbon::now('Asia/Jakarta')->toDateString();

        // ✅ LOGIKA BARU: Cek apakah tanggal di masa depan (lebih dari hari ini)
        if ($tanggalCarbon->greaterThan(Carbon::now('Asia/Jakarta'))) {
            return back()
                ->withInput()
                ->with('error', 'Tidak bisa mengajukan izin untuk tanggal yang belum terjadi!');
        }

        // ✅ LOGIKA: Cek apakah sudah ada presensi HADIR di tanggal tersebut
        $presensiExist = Presensi::where('user_id', $userId)
            ->where('tanggal', $tanggal)
            ->whereIn('status', ['hadir', 'terlambat']) // Cek hanya status hadir/terlambat
            ->first();

        if ($presensiExist) {
            return back()
                ->withInput()
                ->with('error', 'Anda sudah absen HADIR di tanggal tersebut! Tidak bisa mengajukan izin.');
        }

        // ✅ LOGIKA: Jika sudah ada izin/sakit, update saja
        $presensiIzin = Presensi::where('user_id', $userId)
            ->where('tanggal', $tanggal)
            ->whereIn('status', ['izin', 'sakit'])
            ->first();

        if ($presensiIzin) {
            // Update izin yang sudah ada
            $presensiIzin->update([
                'status' => $request->status,
                'keterangan' => $request->keterangan,
            ]);

            return redirect()
                ->route('dashboard')
                ->with('success', 'Pengajuan ' . strtoupper($request->status) . ' berhasil diperbarui!');
        }

        // ✅ Simpan pengajuan izin/sakit baru
        Presensi::create([
            'user_id' => $userId,
            'tanggal' => $tanggal,
            'status' => $request->status,
            'keterangan' => $request->keterangan,
            'jam_masuk' => null,
            'jam_keluar' => null,
        ]);

        return redirect()
            ->route('dashboard')
            ->with('success', 'Pengajuan ' . strtoupper($request->status) . ' berhasil diajukan!');
    }

    /**
     * ✅ Halaman Riwayat Presensi (untuk link "See more")
     */
    public function riwayat()
    {
        $now = Carbon::now('Asia/Jakarta');
        $bulanSekarang = $now->month;
        $tahunSekarang = $now->year;

        // Ambil presensi user ini, urutkan terbaru
        $presensi = Presensi::where('user_id', auth()->id())
            ->orderBy('tanggal', 'desc')
            ->paginate(20);

        // Statistik bulan ini
        $statsBulanIni = [
            'hadir' => Presensi::where('user_id', auth()->id())
                ->whereMonth('tanggal', $bulanSekarang)
                ->whereYear('tanggal', $tahunSekarang)
                ->where('status', 'hadir')
                ->count(),
            'terlambat' => Presensi::where('user_id', auth()->id())
                ->whereMonth('tanggal', $bulanSekarang)
                ->whereYear('tanggal', $tahunSekarang)
                ->where('status', 'terlambat')
                ->count(),
            'izin' => Presensi::where('user_id', auth()->id())
                ->whereMonth('tanggal', $bulanSekarang)
                ->whereYear('tanggal', $tahunSekarang)
                ->whereIn('status', ['izin', 'sakit'])
                ->count(),
            'alpa' => Presensi::where('user_id', auth()->id())
                ->whereMonth('tanggal', $bulanSekarang)
                ->whereYear('tanggal', $tahunSekarang)
                ->where('status', 'alpa')
                ->count(),
        ];

        return view('presensi.riwayat', compact('presensi', 'statsBulanIni'));
    }

    /**
     * ✅ Rekap Presensi (untuk pembimbing/admin)
     */
    public function rekap(Request $request)
    {
        $now = Carbon::now('Asia/Jakarta');
        $bulan = $request->input('bulan', $now->month);
        $tahun = $request->input('tahun', $now->year);

        $presensi = Presensi::whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->with('user')
            ->orderBy('tanggal', 'desc')
            ->get();

        $statsBulanIni = [
            'hadir' => Presensi::whereMonth('tanggal', $bulan)
                                ->whereYear('tanggal', $tahun)
                                ->where('status', 'hadir')
                                ->count(),
            'terlambat' => Presensi::whereMonth('tanggal', $bulan)
                                    ->whereYear('tanggal', $tahun)
                                    ->where('status', 'terlambat')
                                    ->count(),
            'izin' => Presensi::whereMonth('tanggal', $bulan)
                                ->whereYear('tanggal', $tahun)
                                ->whereIn('status', ['izin','sakit'])
                                ->count(),
            'alpa' => Presensi::whereMonth('tanggal', $bulan)
                                ->whereYear('tanggal', $tahun)
                                ->where('status', 'alpa')
                                ->count(),
        ];

       return view('presensi.riwayat', compact('presensi', 'statsBulanIni'));

    }

}