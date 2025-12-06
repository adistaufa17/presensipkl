<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use App\Models\User;
use App\Models\Tagihan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PembayaranController extends Controller
{
    
    public function index()
    {
        // Ambil semua pembayaran milik siswa yang login
        // Diurutkan berdasarkan bulan dan tanggal dibuat
        $payments = Pembayaran::where('user_id', Auth::id())
            ->orderBy('bulan', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('pembayaran.siswa_index', compact('payments'));
    }

    public function bayar(Request $request)
    {
        // Validasi input
        $request->validate([
            'id' => 'required|exists:pembayarans,id',
            'metode' => 'required|in:cash,transfer',
            'bukti' => 'nullable|image|mimes:jpg,jpeg,png|max:2048' // Max 2MB
        ]);

        // Ambil data pembayaran
        $pembayaran = Pembayaran::findOrFail($request->id);

        // SECURITY: Cek apakah pembayaran ini milik user yang login
        if ($pembayaran->user_id !== Auth::id()) {
            return back()->with('error', 'Anda tidak memiliki akses ke pembayaran ini!');
        }

        // Cek apakah pembayaran sudah diterima (tidak boleh bayar ulang)
        if ($pembayaran->status === 'diterima') {
            return back()->with('error', 'Pembayaran ini sudah diterima dan tidak bisa diubah!');
        }

        // Inisialisasi variabel bukti
        $buktiPath = $pembayaran->bukti; // Simpan bukti lama jika ada

        // Jika metode transfer, WAJIB upload bukti
        if ($request->metode === 'transfer') {
            $request->validate([
                'bukti' => 'required|image|mimes:jpg,jpeg,png|max:2048'
            ], [
                'bukti.required' => 'Bukti transfer wajib diupload untuk metode transfer!',
                'bukti.image' => 'File harus berupa gambar!',
                'bukti.mimes' => 'Format gambar harus JPG, JPEG, atau PNG!',
                'bukti.max' => 'Ukuran gambar maksimal 2MB!'
            ]);

            // Hapus bukti lama jika ada (untuk bayar ulang)
            if ($pembayaran->bukti && Storage::disk('public')->exists($pembayaran->bukti)) {
                Storage::disk('public')->delete($pembayaran->bukti);
            }

            // Upload bukti baru
            $buktiPath = $request->file('bukti')->store('bukti_pembayaran', 'public');
        }

        // Update data pembayaran
        $pembayaran->update([
            'metode' => $request->metode,
            'bukti' => $buktiPath,
            'status' => 'pending', // Status jadi pending menunggu approval
            'tanggal_bayar' => now(),
        ]);

        return back()->with('success', 'Pembayaran berhasil dikirim! Menunggu konfirmasi dari pembimbing.');
    }

    public function dashboardSiswa()
    {
        $tagihanBelumBayar = Pembayaran::where('user_id', auth()->id())
            ->where('status', 'belum_bayar')
            ->orderBy('tenggat', 'asc')
            ->limit(3)
            ->get();
        return view('pembayaran.dashboardsiswa', compact('tagihanBelumBayar'));
    }

    public function dashboard()
    {
        // Hitung statistik pembayaran
        $totalTagihan = Pembayaran::count();
        $belumBayar = Pembayaran::where('status', 'belum_bayar')->count();
        $pending = Pembayaran::where('status', 'pending')->count();
        $diterima = Pembayaran::where('status', 'diterima')->count();
        $ditolak = Pembayaran::where('status', 'ditolak')->count();

        // Hitung total nominal yang sudah diterima
        $totalNominalDiterima = Pembayaran::where('status', 'diterima')->sum('nominal');

        // Ambil 10 pembayaran pending terbaru untuk quick access
        $pendingPayments = Pembayaran::with('user')
            ->where('status', 'pending')
            ->orderBy('tanggal_bayar', 'desc')
            ->take(10)
            ->get();

        // Statistik per siswa (siswa yang paling banyak telat bayar)
        $siswaTelat = Pembayaran::with('user')
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

    public function semua()
    {
        $payments = Pembayaran::with('user')
            ->orderByRaw("FIELD(status, 'pending', 'belum_bayar', 'ditolak', 'diterima')")
            ->orderBy('bulan', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();

        // Grouping berdasarkan status untuk tampilan yang lebih rapi (opsional)
        $groupedPayments = [
            'pending' => $payments->where('status', 'pending'),
            'belum_bayar' => $payments->where('status', 'belum_bayar'),
            'diterima' => $payments->where('status', 'diterima'),
            'ditolak' => $payments->where('status', 'ditolak'),
        ];

        return view('pembayaran.semua', compact('payments', 'groupedPayments'));
    }

    /**
     * Menampilkan detail pembayaran
     * Route: GET /pembayaran/{id}
     */
    public function detail($id)
    {
        // Ambil pembayaran dengan relasi user dan tagihan
        $payment = Pembayaran::with('user', 'tagihan')->findOrFail($id);

        // Ambil history pembayaran siswa ini untuk referensi
        $historyPembayaran = Pembayaran::where('user_id', $payment->user_id)
            ->where('id', '!=', $id) // Exclude pembayaran saat ini
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('pembayaran.detail', compact('payment', 'historyPembayaran'));
    }

    public function updateStatus(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'status' => 'required|in:pending,diterima,ditolak',
            'keterangan_pembimbing' => 'nullable|string|max:500'
        ], [
            'status.required' => 'Status pembayaran wajib dipilih!',
            'status.in' => 'Status tidak valid!',
            'keterangan_pembimbing.max' => 'Keterangan maksimal 500 karakter!'
        ]);

        // Ambil data pembayaran
        $pembayaran = Pembayaran::findOrFail($id);

        // Jika status belum bayar, tidak bisa diupdate
        if ($pembayaran->status === 'belum_bayar') {
            return back()->with('error', 'Pembayaran ini belum dilakukan oleh siswa!');
        }

        // Update status
        $pembayaran->update([
            'status' => $request->status,
            'keterangan_pembimbing' => $request->keterangan_pembimbing,
        ]);

        // Pesan sukses berdasarkan status
        $message = match($request->status) {
            'diterima' => '✅ Pembayaran berhasil diterima!',
            'ditolak' => '❌ Pembayaran ditolak. Siswa dapat mengupload ulang.',
            'pending' => '⏳ Status diubah menjadi pending.',
            default => 'Status pembayaran berhasil diperbarui!'
        };

        return back()->with('success', $message);
    }

    /**
     * Lihat pembayaran per siswa (untuk pembimbing)
     * Route: GET /pembayaran/siswa/{user_id}
     */
    public function bySiswa($userId)
    {
        // Cek apakah user adalah siswa
        $siswa = User::where('id', $userId)->where('role', 'siswa')->firstOrFail();

        // Ambil semua pembayaran siswa
        $payments = Pembayaran::where('user_id', $userId)
            ->orderBy('bulan', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();

        // Hitung statistik siswa ini
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
     * Hapus pembayaran (untuk pembimbing, jika ada kesalahan)
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

    /**
     * Reset pembayaran ke status belum bayar (untuk pembimbing)
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
}