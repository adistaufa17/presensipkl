<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PengaturanJamKerja;
use Illuminate\Http\Request;

class SettingJamController extends Controller
{
    /**
     * Tampilkan halaman form pengaturan
     */
    public function index()
    {
        // Ambil data pertama. Jika tidak ada, buat objek baru yang kosong agar view tidak error.
        $setting = PengaturanJamKerja::first() ?? new PengaturanJamKerja();
        
        return view('admin.setting.jam-kerja', compact('setting'));
    }

    /**
     * Update atau Buat pengaturan baru
     */
    public function update(Request $request)
    {
        // 1. Validasi Dasar
        $request->validate([
            'jam_masuk'    => 'required',
            'jam_pulang'   => 'required',
            'batas_telat'  => 'required',
        ]);

        // 2. Logika Validasi Custom (Agar tidak Exception/Layar Hitam)
        if (strtotime($request->jam_masuk) >= strtotime($request->batas_telat)) {
            return redirect()->back()
                ->withInput() // Agar inputan user tidak hilang
                ->with('error', 'Jam masuk harus lebih awal dari batas telat!');
        }

        // 3. Simpan Data
        PengaturanJamKerja::updateOrCreate(
            ['id' => 1],
            [
                'jam_masuk'   => $request->jam_masuk,
                'jam_pulang'  => $request->jam_pulang,
                'batas_telat' => $request->batas_telat,
                'is_active'   => $request->has('is_active') ? true : false,
            ]
        );

        return redirect()->back()->with('success', 'Pengaturan jam kerja berhasil diperbarui!');
    }
}