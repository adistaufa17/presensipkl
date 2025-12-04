<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PembayaranController extends Controller
{
    // FORM TAMBAH PEMBAYARAN (SISWA)
    public function create()
    {
        return view('pembayaran.create');
    }

    // SIMPAN PEMBAYARAN SISWA
    public function store(Request $request)
    {
        $request->validate([
            'jenis' => 'required',
            'jumlah' => 'required|integer',
            'bukti' => 'required|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        $path = $request->file('bukti')->store('bukti_pembayaran', 'public');

        Pembayaran::create([
            'user_id' => Auth::id(),
            'jenis' => $request->jenis,
            'jumlah' => $request->jumlah,   // ← sesuai blade
            'bukti' => $path,
            'status' => 'pending',
        ]);

        return redirect()->route('pembayaran.siswa')
            ->with('success', 'Pembayaran berhasil dikirim');
    }

    // SISWA LIHAT PEMBAYARAN MEREKA
    public function myPayment()
    {
        $payments = Pembayaran::where('user_id', Auth::id())->get(); // ← variabel sesuai blade
        return view('pembayaran.siswa_index', compact('payments'));
    }

    // SEMUA PEMBAYARAN (PEMBIMBING)
    public function allPayment()
    {
        $payments = Pembayaran::with('user')->get();
        return view('pembayaran.semua', compact('payments'));
    }

    // DETAIL PEMBAYARAN (PEMBIMBING)
    public function show($id)
    {
        $payment = Pembayaran::with('user')->findOrFail($id);

        return view('pembayaran.detail', compact('payment'));
    }

    // UPDATE STATUS PEMBAYARAN
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,diterima,ditolak'
        ]);

        Pembayaran::findOrFail($id)->update([
            'status' => $request->status
        ]);

        return back()->with('success', 'Status berhasil diperbarui');
    }
}
