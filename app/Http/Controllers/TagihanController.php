<?php

namespace App\Http\Controllers;

use App\Models\Tagihan;
use App\Models\Pembayaran;
use Illuminate\Http\Request;

class TagihanController extends Controller
{

    public function index()
    {
        $tagihans = Tagihan::orderBy('bulan', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('tagihan.index', compact('tagihans'));
    }

    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'nama' => 'required|string|max:255',
            'kategori' => 'required|in:kos,alat_praktik,lainnya',
            'nominal' => 'required|integer|min:0',
            'bulan' => 'required|integer|min:1|max:12',
            'tenggat' => 'required|date',
            'keterangan' => 'nullable|string',
        ]);

        $pembimbingId = auth()->id();

        // Cek duplicate tagihan untuk pembimbing dan bulan yang sama
        $exists = Tagihan::where('pembimbing_id', $pembimbingId)
            ->where('nama', $request->nama)
            ->where('bulan', $request->bulan)
            ->exists();

        if ($exists) {
            return redirect()->route('tagihan.index')
                ->with('error', 'Tagihan untuk bulan dan nama yang sama sudah ada!');
        }

        // Buat tagihan master
        $tagihan = Tagihan::create([
            'pembimbing_id' => $pembimbingId,
            'nama' => $request->nama,
            'kategori' => $request->kategori,
            'nominal' => $request->nominal,
            'bulan' => $request->bulan,
            'tenggat' => $request->tenggat,
            'keterangan' => $request->keterangan,
        ]);

        $pembimbing = auth()->user();
        $siswaList = $pembimbing->siswaBimbingan;

        if ($siswaList->isEmpty()) {
            return redirect()->route('tagihan.index')
                ->with('warning', 'Tagihan berhasil dibuat, tetapi tidak ada siswa yang terdaftar!');
        }

        // Generate pembayaran untuk setiap siswa, pastikan tidak duplikat
        $totalGenerated = 0;
        foreach ($siswaList as $siswa) {
            $existsPayment = Pembayaran::where('user_id', $siswa->id)
                ->where('tagihan_id', $tagihan->id)
                ->exists();

            if (!$existsPayment) {
                Pembayaran::create([
                    'user_id' => $siswa->id,
                    'tagihan_id' => $tagihan->id,
                    'nama_tagihan' => $tagihan->nama,
                    'kategori' => $tagihan->kategori ?? 'lainnya',
                    'nominal' => $tagihan->nominal,
                    'bulan' => $tagihan->bulan,
                    'tenggat' => $tagihan->tenggat,
                    'status' => 'belum_bayar',
                ]);
                $totalGenerated++;
            }
        }

        return redirect()->route('tagihan.index')
            ->with('success', "✅ Tagihan berhasil dibuat untuk {$totalGenerated} siswa!");
    }

    public function create()
    {
        return view('tagihan.create');
    }


    public function destroy($id)
    {
        $tagihan = Tagihan::with('pembayarans')->findOrFail($id);

        // Hapus semua pembayaran terkait (bukti otomatis dihapus via observer atau manual)
        foreach ($tagihan->pembayarans as $pembayaran) {
            if ($pembayaran->bukti) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($pembayaran->bukti);
            }
            $pembayaran->delete();
        }

        $tagihan->delete();

        return back()->with('success', "🗑️ Tagihan dan semua pembayaran terkait berhasil dihapus!");
    }
}
