<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PengaturanJamKerja;
use Illuminate\Http\Request;

class SettingJamController extends Controller
{
    public function index()
    {
        $setting = PengaturanJamKerja::first() ?? new PengaturanJamKerja();
        
        return view('admin.setting.jam-kerja', compact('setting'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'jam_masuk'    => 'required',
            'jam_pulang'   => 'required',
            'batas_telat'  => 'required',
        ]);

        if (strtotime($request->jam_masuk) >= strtotime($request->batas_telat)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Jam masuk harus lebih awal dari batas terlambat!');
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