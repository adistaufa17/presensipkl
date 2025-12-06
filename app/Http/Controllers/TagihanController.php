<?php

namespace App\Http\Controllers;

use App\Models\Tagihan;
use App\Models\Pembayaran;
use App\Models\User;
use Illuminate\Http\Request;

class TagihanController extends Controller
{
    /**
     * Menampilkan list semua tagihan (untuk pembimbing)
     * Route: GET /tagihan
     */
    public function index()
    {
        $tagihans = Tagihan::orderBy('bulan', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('tagihan.index', compact('tagihans'));
    }

    /**
     * Menampilkan form buat tagihan baru
     * Route: GET /tagihan/create
     */
    public function create()
    {
        return view('tagihan.create');
    }

    /**
     * Proses buat tagihan + generate ke semua siswa
     * Route: POST /tagihan
     */
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
        ], [
            'nama.required' => 'Nama tagihan wajib diisi!',
            'kategori.required' => 'Kategori wajib dipilih!',
            'kategori.in' => 'Kategori tidak valid!',
            'nominal.required' => 'Nominal wajib diisi!',
            'nominal.integer' => 'Nominal harus berupa angka!',
            'nominal.min' => 'Nominal minimal Rp 0!',
            'bulan.required' => 'Bulan wajib dipilih!',
            'bulan.min' => 'Bulan minimal 1!',
            'bulan.max' => 'Bulan maksimal 12!',
            'tenggat.required' => 'Tanggal tenggat wajib diisi!',
            'tenggat.date' => 'Format tanggal tidak valid!',
        ]);

        // Buat tagihan master
        $tagihan = Tagihan::create([
            'nama' => $request->nama,
            'kategori' => $request->kategori,
            'nominal' => $request->nominal,
            'bulan' => $request->bulan,
            'tenggat' => $request->tenggat,
            'keterangan' => $request->keterangan,
        ]);

        // Ambil semua siswa
        $siswaList = User::where('role', 'siswa')->get();

        // Cek apakah ada siswa
        if ($siswaList->count() === 0) {
            return redirect()->route('tagihan.index')
                ->with('warning', 'Tagihan berhasil dibuat, tetapi tidak ada siswa yang terdaftar!');
        }

        // Generate pembayaran untuk setiap siswa
        $totalGenerated = 0;
        foreach ($siswaList as $siswa) {
            Pembayaran::create([
                'user_id' => $siswa->id,
                'tagihan_id' => $tagihan->id,
                'nama_tagihan' => $tagihan->nama,
                'kategori' => $tagihan->kategori,
                'nominal' => $tagihan->nominal,
                'bulan' => $tagihan->bulan,
                'tenggat' => $tagihan->tenggat,
                'status' => 'belum_bayar',
            ]);
            $totalGenerated++;
        }

        return redirect()->route('tagihan.index')
            ->with('success', "✅ Tagihan berhasil dibuat untuk {$totalGenerated} siswa!");
    }

    /**
     * Hapus tagihan (akan otomatis hapus pembayaran karena cascade)
     * Route: DELETE /tagihan/{id}
     */
    public function destroy($id)
    {
        $tagihan = Tagihan::findOrFail($id);
        
        // Hitung berapa pembayaran yang akan terhapus
        $jumlahPembayaran = $tagihan->pembayarans()->count();
        
        // Hapus tagihan (pembayaran akan terhapus otomatis karena onDelete cascade)
        $tagihan->delete();
        
        return back()->with('success', "🗑️ Tagihan berhasil dihapus! ({$jumlahPembayaran} pembayaran ikut terhapus)");
    }

    /**
     * Edit tagihan (opsional - untuk update data master)
     * Route: GET /tagihan/{id}/edit
     */
    public function edit($id)
    {
        $tagihan = Tagihan::findOrFail($id);
        return view('tagihan.edit', compact('tagihan'));
    }

    /**
     * Update tagihan (opsional)
     * Route: PUT /tagihan/{id}
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'kategori' => 'required|in:kos,alat_praktik,lainnya',
            'nominal' => 'required|integer|min:0',
            'bulan' => 'required|integer|min:1|max:12',
            'tenggat' => 'required|date',
            'keterangan' => 'nullable|string',
        ]);

        $tagihan = Tagihan::findOrFail($id);
        
        // Update tagihan master
        $tagihan->update($request->all());
        
        // OPSIONAL: Update juga semua pembayaran terkait yang belum dibayar
        $tagihan->pembayarans()
            ->where('status', 'belum_bayar')
            ->update([
                'nama_tagihan' => $request->nama,
                'kategori' => $request->kategori,
                'nominal' => $request->nominal,
                'bulan' => $request->bulan,
                'tenggat' => $request->tenggat,
            ]);
        
        return redirect()->route('tagihan.index')
            ->with('success', '✅ Tagihan berhasil diperbarui!');
    }
}