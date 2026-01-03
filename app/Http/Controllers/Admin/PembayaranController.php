<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\TagihanSiswa; 
use App\Models\Tagihan;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class PembayaranController extends Controller
{
    public function index(Request $request) 
    {
        $pembayaran = TagihanSiswa::with(['siswa.user', 'tagihan'])
            ->when($request->search, function($query) use ($request) {
                $query->whereHas('siswa.user', function($q) use ($request) {
                    $q->where('nama_lengkap', 'like', '%' . $request->search . '%');
                })->orWhereHas('tagihan', function($q) use ($request) {
                    $q->where('nama_tagihan', 'like', '%' . $request->search . '%');
                });
            })
            ->when($request->status, function($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $pembayaranMenunggu = TagihanSiswa::where('status', 'menunggu_konfirmasi')->count();
    
        $totalLunas = TagihanSiswa::where('status', 'dibayar')
            ->whereMonth('tanggal_bayar', now()->month)
            ->count();

        $totalBelumBayar = TagihanSiswa::where('status', 'belum_bayar')->count();

        $tagihans = Tagihan::select('nama_tagihan', 'nominal', \DB::raw('MIN(id) as id'), \DB::raw('MIN(created_at) as created_at'))
            ->groupBy('nama_tagihan', 'nominal')
            ->latest('id')
            ->get();

        return view('admin.pembayaran.index', compact(
            'pembayaran', 
            'pembayaranMenunggu', 
            'totalLunas', 
            'totalBelumBayar',
            'tagihans'
        ));
    }

    public function showDetail($id)
    {
        try {
            $referensi = Tagihan::findOrFail($id);
            $details = TagihanSiswa::with(['siswa.user'])
                ->whereHas('tagihan', function($q) use ($referensi) {
                    $q->where('nama_tagihan', $referensi->nama_tagihan);
                })
                ->get();

            $formattedData = $details->map(function($item) {
                return [
                    'siswa' => [
                        'user' => [
                            'nama_lengkap' => $item->siswa->user->nama_lengkap ?? 'N/A'
                        ]
                    ],
                    'bulan_ke' => $item->bulan_ke,
                    'status'   => $item->status,
                    'tanggal_bayar' => $item->tanggal_bayar ? \Carbon\Carbon::parse($item->tanggal_bayar)->format('d/m/Y') : '-'
                ];
            });

            return response()->json([
                'nama_tagihan' => $referensi->nama_tagihan,
                'nominal'      => number_format($referensi->nominal, 0, ',', '.'),
                'data'         => $formattedData // Sekarang berisi koleksi semua siswa
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function export(Request $request)
    {
        $data = TagihanSiswa::with(['siswa.user', 'tagihan'])
            ->when($request->search, function($query) use ($request) {
                $query->whereHas('siswa.user', function($q) use ($request) {
                    $q->where('nama_lengkap', 'like', '%' . $request->search . '%');
                });
            })
            ->when($request->status, function($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->latest()
            ->get();

            $pdf = Pdf::loadView('admin.pembayaran.pdf-rekap', compact('data'));        
            $pdf->setPaper('a4', 'portrait');

        return $pdf->download('Laporan-Pembayaran-'.date('d-m-Y').'.pdf');
    }

    public function bayar(Request $request, $id) {
        $tagihan = TagihanSiswa::find($id);
        $tagihan->update([
            'bukti_pembayaran' => $request->file('bukti')->store('pembayaran', 'public'),
            'status' => 'menunggu_konfirmasi',
            'tanggal_bayar' => now()
        ]);
        return redirect()->back()->with('success', 'Pembayaran berhasil dikirim');
    }

    public function konfirmasi(Request $request, $id)
{
    $request->validate([
        'status' => 'required',
        'catatan_admin' => 'nullable|string'
    ]);

    $tagihan = TagihanSiswa::findOrFail($id);

    $tagihan->status = $request->status;
    $tagihan->catatan_admin = $request->catatan_admin; // 🔥 INI KUNCINYA
    $tagihan->dikonfirmasi_oleh = auth()->id();
    $tagihan->save();

    return back()->with('success', 'Status pembayaran diperbarui');
}


}