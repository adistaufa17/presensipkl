<?php

namespace App\Http\Controllers;

use App\Models\Presensi;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class PresensiController extends Controller
{
    /**
     * Absen Masuk dengan Foto Selfie
     */
    public function storeMasuk(Request $request)
    {
        $request->validate([
            'foto_masuk' => 'required|string',
        ], [
            'foto_masuk.required' => 'Foto selfie wajib diambil untuk absen masuk!',
        ]);

        $now = Carbon::now('Asia/Jakarta');
        $today = $now->toDateString();
        $userId = auth()->id();

        // ❗ CEK SUDAH ABSEN
        $presensi = Presensi::where('user_id', $userId)
            ->where('tanggal', $today)
            ->first();

        if ($presensi) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah absen masuk hari ini!'
            ], 422);
        }

        // ✅ SIMPAN FOTO
        $image = str_replace('data:image/png;base64,', '', $request->foto_masuk);
        $image = str_replace(' ', '+', $image);
        $imageName = 'presensi_' . $userId . '_' . time() . '.png';

        Storage::disk('public')->put(
            'foto_presensi/' . $imageName,
            base64_decode($image)
        );

        // ⏰ STATUS
        $batasWaktu = Carbon::parse($today . ' 08:00:00', 'Asia/Jakarta');
        $status = $now->greaterThan($batasWaktu) ? 'terlambat' : 'hadir';

        Presensi::create([
            'user_id' => $userId,
            'tanggal' => $today,
            'jam_masuk' => $now->format('H:i:s'),
            'foto_masuk' => 'foto_presensi/' . $imageName,
            'status' => $status,
        ]);

        return response()->json([
            'success' => true,
            'message' => $status === 'terlambat'
                ? 'Presensi masuk berhasil. Anda terlambat!'
                : 'Presensi masuk berhasil!'
        ]);
    }


    /**
     * Absen Keluar dengan Jurnal Kegiatan
     */
    public function storeKeluar(Request $request)
    {
        $request->validate([
            'jurnal_kegiatan' => 'required|string|min:50|max:1000',
        ], [
            'jurnal_kegiatan.required' => 'Jurnal kegiatan wajib diisi!',
            'jurnal_kegiatan.min' => 'Jurnal kegiatan minimal 50 karakter.',
            'jurnal_kegiatan.max' => 'Jurnal kegiatan maksimal 1000 karakter.',
        ]);

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

        // Update jam keluar dan jurnal
        $presensi->update([
            'jam_keluar' => $jamKeluar->format('H:i:s'),
            'jurnal_kegiatan' => $request->jurnal_kegiatan,
        ]);

        return back()->with('success', 'Presensi keluar berhasil! Jurnal kegiatan telah tersimpan. Hati-hati di jalan.');
    }

    /**
     * Form Pengajuan Izin/Sakit
     */
    public function createIzin()
    {
        return view('siswa/izin');
    }

    /**
     * Simpan Pengajuan Izin/Sakit
     */
    public function storeIzin(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
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

        if ($tanggalCarbon->greaterThan(Carbon::now('Asia/Jakarta'))) {
            return back()
                ->withInput()
                ->with('error', 'Tidak bisa mengajukan izin untuk tanggal yang belum terjadi!');
        }

        $presensiExist = Presensi::where('user_id', $userId)
            ->where('tanggal', $tanggal)
            ->whereIn('status', ['hadir', 'terlambat'])
            ->first();

        if ($presensiExist) {
            return back()
                ->withInput()
                ->with('error', 'Anda sudah absen HADIR di tanggal tersebut! Tidak bisa mengajukan izin.');
        }

        $presensiIzin = Presensi::where('user_id', $userId)
            ->where('tanggal', $tanggal)
            ->whereIn('status', ['izin', 'sakit'])
            ->first();

        if ($presensiIzin) {
            $presensiIzin->update([
                'status' => $request->status,
                'keterangan' => $request->keterangan,
            ]);

            return redirect()
                ->route('dashboard')
                ->with('success', 'Pengajuan ' . strtoupper($request->status) . ' berhasil diperbarui!');
        }

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
     * Halaman Riwayat Presensi (Siswa)
     */
    public function riwayat()
    {
        $now = Carbon::now('Asia/Jakarta');
        $bulanSekarang = $now->month;
        $tahunSekarang = $now->year;

        $presensi = Presensi::where('user_id', auth()->id())
            ->orderBy('tanggal', 'desc')
            ->paginate(20);

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
     * Rekap Presensi untuk Pembimbing/Admin
     */
    public function rekap(Request $request)
    {
        $now = Carbon::now('Asia/Jakarta');
        $bulan = $request->input('bulan', $now->month);
        $tahun = $request->input('tahun', $now->year);
        $siswaId = $request->input('siswa_id');

        $query = Presensi::whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->with('user');

        if ($siswaId) {
            $query->where('user_id', $siswaId);
        }

        $presensi = $query->orderBy('tanggal', 'desc')->paginate(20);

        $statsBulanIni = [
            'hadir' => Presensi::whereMonth('tanggal', $bulan)
                                ->whereYear('tanggal', $tahun)
                                ->where('status', 'hadir')
                                ->when($siswaId, fn($q) => $q->where('user_id', $siswaId))
                                ->count(),
            'terlambat' => Presensi::whereMonth('tanggal', $bulan)
                                    ->whereYear('tanggal', $tahun)
                                    ->where('status', 'terlambat')
                                    ->when($siswaId, fn($q) => $q->where('user_id', $siswaId))
                                    ->count(),
            'izin' => Presensi::whereMonth('tanggal', $bulan)
                                ->whereYear('tanggal', $tahun)
                                ->whereIn('status', ['izin','sakit'])
                                ->when($siswaId, fn($q) => $q->where('user_id', $siswaId))
                                ->count(),
            'alpa' => Presensi::whereMonth('tanggal', $bulan)
                                ->whereYear('tanggal', $tahun)
                                ->where('status', 'alpa')
                                ->when($siswaId, fn($q) => $q->where('user_id', $siswaId))
                                ->count(),
        ];

        $siswaList = \App\Models\User::where('role', 'siswa')->get();

       return view('presensi.rekap', compact('presensi', 'statsBulanIni', 'siswaList', 'bulan', 'tahun', 'siswaId'));
    }

    /**
     * Detail Presensi Individual (untuk Pembimbing)
     */
    public function detail($id)
    {
        $presensi = Presensi::with('user')->findOrFail($id);
        return view('presensi.detail', compact('presensi'));
    }
}