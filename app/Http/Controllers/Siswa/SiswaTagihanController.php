<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\TagihanSiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SiswaTagihanController extends Controller
{
    
    public function index()
    {
        $siswaId = auth()->user()->siswa->id ?? null;

        if (!$siswaId) {
            return redirect()->back()->with('error', 'Data siswa tidak ditemukan.');
        }

        $tagihans = TagihanSiswa::with(['tagihan', 'siswa'])
            ->where('siswa_id', $siswaId)
            ->orderBy('bulan_ke', 'asc')
            ->get();

        return view('siswa.tagihan', compact('tagihans'));
    }

    public function bayar(Request $request, $id)
    {
        if (!auth()->user()->siswa) {
            return redirect()->back()->with('error', 'Data siswa tidak ditemukan. Silakan hubungi admin.');
        }

        $request->validate([
            'bukti_pembayaran' => 'required|image|mimes:jpeg,jpg,png|max:5120',
        ], [
            'bukti_pembayaran.required' => 'Bukti pembayaran wajib diupload.',
            'bukti_pembayaran.image' => 'File harus berupa gambar.',
            'bukti_pembayaran.mimes' => 'Format file harus JPG, JPEG, atau PNG.',
            'bukti_pembayaran.max' => 'Ukuran file maksimal 5MB.',
        ]);

        $tagihanSiswa = TagihanSiswa::findOrFail($id);
        $siswaId = auth()->user()->siswa->id;
        if ($tagihanSiswa->siswa_id !== $siswaId) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses ke tagihan ini.');
        }
        if (!in_array($tagihanSiswa->status, ['belum_bayar', 'ditolak'])) {
            return redirect()->back()->with('error', 'Tagihan ini sudah dalam proses atau sudah dibayar.');
        }
        try {
            if ($tagihanSiswa->bukti_pembayaran && Storage::disk('public')->exists($tagihanSiswa->bukti_pembayaran)) {
                Storage::disk('public')->delete($tagihanSiswa->bukti_pembayaran);
            }

            $file = $request->file('bukti_pembayaran');
            $fileName = 'bukti_' . $tagihanSiswa->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('bukti_pembayaran', $fileName, 'public');
            if (!$filePath) {
                throw new \Exception('Gagal menyimpan file. Periksa permission folder storage.');
            }
            $tagihanSiswa->update([
                'bukti_pembayaran' => $filePath,
                'status' => 'menunggu_konfirmasi',
                'tanggal_bayar' => now(),
                'catatan_admin' => null,
            ]);

            return redirect()->back()->with('success', 'Bukti pembayaran berhasil diupload dan menunggu konfirmasi admin.');

        } catch (\Exception $e) {
            \Log::error('Error upload bukti pembayaran: ' . $e->getMessage(), [
                'tagihan_id' => $id,
                'siswa_id' => $siswaId,
                'file_size' => $request->file('bukti_pembayaran')->getSize(),
            ]);
            
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $siswaId = auth()->user()->siswa->id ?? null;

        $tagihanSiswa = TagihanSiswa::with(['tagihan', 'siswa', 'admin'])
            ->where('siswa_id', $siswaId)
            ->findOrFail($id);

        return view('siswa.tagihan.show', compact('tagihanSiswa'));
    }
}