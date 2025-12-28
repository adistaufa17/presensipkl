<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\TagihanSiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SiswaTagihanController extends Controller
{
    public function index()
    {
        $id_siswa = Auth::user()->siswa->id;
        $tagihans = TagihanSiswa::with('tagihan')
            ->where('siswa_id', $id_siswa)
            ->orderBy('jatuh_tempo', 'asc')
            ->get();

        return view('Siswa.Tagihan', compact('tagihans'));
    }

    public function bayar(Request $request, $id)
    {
        $request->validate([
            'bukti_pembayaran' => 'required|image|mimes:jpg,png,jpeg|max:2048',
        ]);

        $tagihanSiswa = TagihanSiswa::findOrFail($id);

        if ($request->hasFile('bukti_pembayaran')) {
            $path = $request->file('bukti_pembayaran')->store('bukti_pembayaran', 'public');
            
            $tagihanSiswa->update([
                'bukti_pembayaran' => $path,
                'status' => 'menunggu_konfirmasi',
                'tanggal_bayar' => now(),
            ]);
        }

        return redirect()->back()->with('success', 'Bukti pembayaran berhasil dikirim. Menunggu konfirmasi admin.');
    }
}