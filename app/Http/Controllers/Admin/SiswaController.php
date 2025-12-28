<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Siswa;
use App\Models\Sekolah;
use App\Models\Tagihan;
use App\Models\TagihanSiswa;
use Illuminate\Http\Request; 
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class SiswaController extends Controller
{
    public function index()
    {
        $sekolahs = Sekolah::all();
        $siswas = Siswa::with('user', 'sekolah')->get();
        return view('Admin.Siswa-Management', compact('sekolahs', 'siswas'));
    }

    public function store(Request $request) 
    {
        DB::transaction(function () use ($request) {
            $user = User::create([
                'nama_lengkap' => $request->nama_lengkap,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'siswa'
            ]);

            $siswa = Siswa::create([
                'user_id' => $user->id,
                'sekolah_id' => $request->sekolah_id,
                'tanggal_mulai_pkl' => $request->tanggal_mulai_pkl,
                'tanggal_selesai_pkl' => $request->tanggal_selesai_pkl,
                'durasi_pkl_bulan' => $request->durasi_pkl_bulan,
                'status' => 'aktif'
            ]);

            $templates = Tagihan::all();
            foreach ($templates as $temp) {
                for ($i = 1; $i <= $siswa->durasi_pkl_bulan; $i++) {
                    TagihanSiswa::create([
                        'tagihan_id' => $temp->id,
                        'siswa_id' => $siswa->id,
                        'bulan_ke' => $i,
                        'jatuh_tempo' => Carbon::parse($siswa->tanggal_mulai_pkl)->addMonths($i-1),
                        'status' => 'belum_bayar'
                    ]);
                }
            }
        });

        return redirect()->route('admin.siswa.index')->with('success', 'Siswa dan Tagihan berhasil dibuat!');
    }

    public function update(Request $request, $id)
    {
        $siswa = Siswa::findOrFail($id);
        $user = User::findOrFail($siswa->user_id);

        $user->update([
            'nama_lengkap' => $request->nama_lengkap,
            'email' => $request->email,
        ]);

        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        $siswa->update([
            'sekolah_id' => $request->sekolah_id,
            'tanggal_mulai_pkl' => $request->tanggal_mulai_pkl,
            'tanggal_selesai_pkl' => $request->tanggal_selesai_pkl,
            'durasi_pkl_bulan' => $request->durasi_pkl_bulan,
        ]);

        return redirect()->back()->with('success', 'Data siswa berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $siswa = Siswa::findOrFail($id);
        
        User::destroy($siswa->user_id);
        $siswa->delete();

        return redirect()->back()->with('success', 'Data siswa berhasil dihapus!');
    }
}