<?php

namespace App\Http\Controllers\Admin;

use App\Models\Sekolah;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SekolahController extends Controller
{
    public function index()
    {
        $sekolahs = Sekolah::all();
        return view('Admin.Sekolah-Management', compact('sekolahs'));
    }

    public function store(Request $request)
    {
        $request->validate(['nama_sekolah' => 'required|string|max:255']);
        Sekolah::create($request->all());
        return redirect()->back()->with('success', 'Sekolah berhasil ditambah!');
    }

    public function update(Request $request, $id)
    {
        $request->validate(['nama_sekolah' => 'required|string|max:255']);
        $sekolah = Sekolah::findOrFail($id);
        $sekolah->update($request->all());
        return redirect()->back()->with('success', 'Nama sekolah berhasil diubah!');
    }

    public function destroy($id)
    {
        $sekolah = Sekolah::findOrFail($id);
        
        if ($sekolah->siswas()->count() > 0) {
            return redirect()->back()->with('error', 'Gagal hapus! Masih ada siswa di sekolah ini.');
        }

        $sekolah->delete();
        return redirect()->back()->with('success', 'Sekolah berhasil dihapus!');
    }
}