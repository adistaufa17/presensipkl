<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Tagihan;
use App\Models\Presensi;
use App\Models\Pembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PembayaranController extends Controller
{
    /**
     * ===== ROUTES UNTUK SISWA =====
     */

    /**
     * Index Pembayaran Siswa
     * Route: /pembayaran/siswa
     */
    public function index()
    {
        $payments = Pembayaran::where('user_id', Auth::id())
            ->orderBy('bulan', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('pembayaran.siswa_index', compact('payments'));
    }

    /**
     * FIXED: Method Bayar dengan Upload File
     * Route: POST /pembayaran/bayar
     */
    public function bayar(Request $request)
    {
        // Validasi input dasar
        $request->validate([
            'id' => 'required|exists:pembayarans,id',
            'metode' => 'required|in:cash,transfer',
        ]);

        // Ambil data pembayaran
        $pembayaran = Pembayaran::findOrFail($request->id);

        // SECURITY: Cek apakah pembayaran ini milik user yang login
        if ($pembayaran->user_id !== Auth::id()) {
            return back()->with('error', 'Anda tidak memiliki akses ke pembayaran ini!');
        }

        // Cek apakah pembayaran sudah diterima
        if ($pembayaran->status === 'diterima') {
            return back()->with('error', 'Pembayaran ini sudah diterima dan tidak bisa diubah!');
        }

        // Inisialisasi bukti path
        $buktiPath = $pembayaran->bukti;

        // ⚠️ FIX: Jika metode transfer, WAJIB upload bukti
        if ($request->metode === 'transfer') {
            $request->validate([
                'bukti' => 'required|image|mimes:jpg,jpeg,png|max:2048'
            ], [
                'bukti.required' => 'Bukti transfer wajib diupload!',
                'bukti.image' => 'File harus berupa gambar!',
                'bukti.mimes' => 'Format gambar harus JPG, JPEG, atau PNG!',
                'bukti.max' => 'Ukuran gambar maksimal 2MB!'
            ]);

            // Hapus bukti lama jika ada
            if ($pembayaran->bukti && Storage::disk('public')->exists($pembayaran->bukti)) {
                Storage::disk('public')->delete($pembayaran->bukti);
            }

            // ⚠️ FIX: Upload bukti baru dengan nama unik
            $file = $request->file('bukti');
            $fileName = 'bukti_' . time() . '_' . Auth::id() . '.' . $file->getClientOriginalExtension();
            $buktiPath = $file->storeAs('bukti_pembayaran', $fileName, 'public');
        }

        // Jika metode cash, boleh tidak upload bukti (opsional)
        if ($request->metode === 'cash' && $request->hasFile('bukti')) {
            $request->validate([
                'bukti' => 'image|mimes:jpg,jpeg,png|max:2048'
            ]);

            // Hapus bukti lama jika ada
            if ($pembayaran->bukti && Storage::disk('public')->exists($pembayaran->bukti)) {
                Storage::disk('public')->delete($pembayaran->bukti);
            }

            // Upload bukti
            $file = $request->file('bukti');
            $fileName = 'bukti_' . time() . '_' . Auth::id() . '.' . $file->getClientOriginalExtension();
            $buktiPath = $file->storeAs('bukti_pembayaran', $fileName, 'public');
        }

        // Update data pembayaran
        $pembayaran->update([
            'metode' => $request->metode,
            'bukti' => $buktiPath,
            'status' => 'pending',
            'tanggal_bayar' => now(),
        ]);

        return back()->with('success', 'Pembayaran berhasil dikirim! Menunggu konfirmasi dari pembimbing.');
    }

    /**
     * Dashboard Siswa (jika digunakan)
     */
    public function dashboardSiswa()
    {
        $userId = auth()->id();
        $today = Carbon::today();

        $presensiHariIni = Presensi::where('user_id', $userId)
            ->whereDate('tanggal', $today)
            ->first();

        $summary = [
            'hadir' => Presensi::where('user_id', $userId)->where('status', 'hadir')->count(),
            'izin_total' => Presensi::where('user_id', $userId)->whereIn('status', ['izin', 'sakit'])->count(),
            'alpa' => Presensi::where('user_id', $userId)->where('status', 'alpa')->count(),
        ];

        $totalHari = Presensi::where('user_id', $userId)->count();
        $persentaseHadir = $totalHari > 0 ? ($summary['hadir'] / $totalHari) * 100 : 0;

        // Grafik Jan–Des
        $dataGrafik = [];
        for ($bulan = 1; $bulan <= 12; $bulan++) {
            $total = Presensi::where('user_id', $userId)
                ->whereMonth('tanggal', $bulan)
                ->where('status', 'hadir')
                ->count();

            $dataGrafik[] = [
                'label' => Carbon::create()->month($bulan)->translatedFormat('M'),
                'total' => $total,
                'height' => min(100, $total * 5) . '%',
                'color' => $total > 20 ? 'bg-success' : ($total >= 10 ? 'bg-primary' : 'bg-warning'),
            ];
        }

        $tagihanBelumBayar = Pembayaran::where('user_id', $userId)
            ->where('status', 'belum_bayar')
            ->orderBy('tenggat', 'asc')
            ->take(3)
            ->get();

        return view('pembayaran.dashboardSiswa', compact(
            'presensiHariIni',
            'summary',
            'dataGrafik',
            'persentaseHadir',
            'tagihanBelumBayar'
        ));
    }

    /**
     * ===== ROUTES UNTUK PEMBIMBING =====
     */

    /**
     * Dashboard Pembimbing
     */
    public function dashboard()
    {
        // ⚠️ FIX: Jika tidak ada relasi siswaBimbingan, ambil semua siswa
        $siswaIds = User::where('role', 'siswa')->pluck('id');

        // Atau jika ada relasi siswaBimbingan:
        // $pembimbing = auth()->user();
        // $siswaIds = $pembimbing->siswaBimbingan()->pluck('id');

        $totalTagihan = Pembayaran::whereIn('user_id', $siswaIds)->count();
        $belumBayar = Pembayaran::whereIn('user_id', $siswaIds)->where('status', 'belum_bayar')->count();
        $pending = Pembayaran::whereIn('user_id', $siswaIds)->where('status', 'pending')->count();
        $diterima = Pembayaran::whereIn('user_id', $siswaIds)->where('status', 'diterima')->count();
        $ditolak = Pembayaran::whereIn('user_id', $siswaIds)->where('status', 'ditolak')->count();

        // Total nominal yang sudah diterima
        $totalNominalDiterima = Pembayaran::whereIn('user_id', $siswaIds)
            ->where('status', 'diterima')
            ->sum('nominal');

        // Pembayaran pending terbaru
        $pendingPayments = Pembayaran::with('user')
            ->whereIn('user_id', $siswaIds)
            ->where('status', 'pending')
            ->orderBy('tanggal_bayar', 'desc')
            ->take(10)
            ->get();

        // Siswa yang paling banyak telat bayar
        $siswaTelat = Pembayaran::with('user')
            ->whereIn('user_id', $siswaIds)
            ->where('status', 'belum_bayar')
            ->whereDate('tenggat', '<', now())
            ->get()
            ->groupBy('user_id')
            ->map(function ($items) {
                return [
                    'user' => $items->first()->user,
                    'jumlah' => $items->count()
                ];
            })
            ->sortByDesc('jumlah')
            ->take(5);

        return view('pembimbing.dashboard', compact(
            'totalTagihan',
            'belumBayar',
            'pending',
            'diterima',
            'ditolak',
            'totalNominalDiterima',
            'pendingPayments',
            'siswaTelat'
        ));
    }

    /**
     * Semua Pembayaran
     * Route: /pembayaran/semua
     */
    public function semua()
    {
        $payments = Pembayaran::with('user')
            ->orderByRaw("FIELD(status, 'pending', 'belum_bayar', 'ditolak', 'diterima')")
            ->orderBy('bulan', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();

        $groupedPayments = [
            'pending' => $payments->where('status', 'pending'),
            'belum_bayar' => $payments->where('status', 'belum_bayar'),
            'diterima' => $payments->where('status', 'diterima'),
            'ditolak' => $payments->where('status', 'ditolak'),
        ];

        return view('pembayaran.semua', compact('payments', 'groupedPayments'));
    }

    /**
     * Detail Pembayaran
     * Route: /pembayaran/{id}/detail
     */
    public function detail($id)
    {
        $payment = Pembayaran::with('user', 'tagihan')->findOrFail($id);

        $historyPembayaran = Pembayaran::where('user_id', $payment->user_id)
            ->where('id', '!=', $id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('pembayaran.detail', compact('payment', 'historyPembayaran'));
    }

    /**
     * Update Status Pembayaran
     * Route: POST /pembayaran/{id}/status
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,diterima,ditolak',
            'keterangan_pembimbing' => 'nullable|string|max:500'
        ], [
            'status.required' => 'Status pembayaran wajib dipilih!',
            'status.in' => 'Status tidak valid!',
            'keterangan_pembimbing.max' => 'Keterangan maksimal 500 karakter!'
        ]);

        $pembayaran = Pembayaran::findOrFail($id);

        if ($pembayaran->status === 'belum_bayar') {
            return back()->with('error', 'Pembayaran ini belum dilakukan oleh siswa!');
        }

        $pembayaran->update([
            'status' => $request->status,
            'keterangan_pembimbing' => $request->keterangan_pembimbing,
        ]);

        // Pesan sukses
        $message = match($request->status) {
            'diterima' => '✅ Pembayaran berhasil diterima!',
            'ditolak' => '❌ Pembayaran ditolak. Siswa dapat mengupload ulang.',
            'pending' => '⏳ Status diubah menjadi pending.',
            default => 'Status pembayaran berhasil diperbarui!'
        };

        return back()->with('success', $message);
    }

    /**
     * Pembayaran per Siswa
     * Route: /pembayaran/siswa/{userId}
     */
    public function bySiswa($userId)
    {
        $siswa = User::where('id', $userId)->where('role', 'siswa')->firstOrFail();

        $payments = Pembayaran::where('user_id', $userId)
            ->orderBy('bulan', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();

        $stats = [
            'total' => $payments->count(),
            'belum_bayar' => $payments->where('status', 'belum_bayar')->count(),
            'pending' => $payments->where('status', 'pending')->count(),
            'diterima' => $payments->where('status', 'diterima')->count(),
            'ditolak' => $payments->where('status', 'ditolak')->count(),
            'total_dibayar' => $payments->where('status', 'diterima')->sum('nominal'),
        ];

        return view('pembayaran.by_siswa', compact('siswa', 'payments', 'stats'));
    }

    /**
     * Reset Pembayaran ke Belum Bayar
     * Route: POST /pembayaran/{id}/reset
     */
    public function reset($id)
    {
        $pembayaran = Pembayaran::findOrFail($id);

        // Hapus bukti pembayaran jika ada
        if ($pembayaran->bukti && Storage::disk('public')->exists($pembayaran->bukti)) {
            Storage::disk('public')->delete($pembayaran->bukti);
        }

        // Reset ke status awal
        $pembayaran->update([
            'metode' => null,
            'bukti' => null,
            'status' => 'belum_bayar',
            'tanggal_bayar' => null,
            'keterangan_pembimbing' => null,
        ]);

        return back()->with('success', 'Pembayaran berhasil direset ke status belum bayar!');
    }

    /**
     * Hapus Pembayaran (opsional)
     * Route: DELETE /pembayaran/{id}
     */
    public function destroy($id)
    {
        $pembayaran = Pembayaran::findOrFail($id);

        // Hapus bukti pembayaran jika ada
        if ($pembayaran->bukti && Storage::disk('public')->exists($pembayaran->bukti)) {
            Storage::disk('public')->delete($pembayaran->bukti);
        }

        $pembayaran->delete();

        return back()->with('success', 'Pembayaran berhasil dihapus!');
    }
}