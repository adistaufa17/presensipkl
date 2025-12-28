<?php

namespace App\Http\Controllers\Admin;

use App\Models\Siswa;
use App\Models\Tagihan;
use App\Models\TagihanSiswa;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class TagihanController extends Controller
{
    
    public function store(Request $request)
    {
        $request->validate([
            'nama_tagihan' => 'required',
            'nominal' => 'required|numeric',
            'jumlah_bulan' => 'required|integer',
            'jatuh_tempo_awal' => 'required|date',
        ]);

        $siswas = \App\Models\Siswa::all();

        if ($siswas->isEmpty()) {
            return back()->with('error', 'Gagal: Tidak ada data siswa di database.');
        }

        try {
            \DB::beginTransaction();

            foreach ($siswas as $siswa) {
                // Pastikan array di bawah ini persis seperti ini
                $tagihanMaster = \App\Models\Tagihan::create([
                    'siswa_id'     => $siswa->id, // INI HARUS ADA
                    'nama_tagihan' => $request->nama_tagihan,
                    'nominal'      => $request->nominal,
                    'jatuh_tempo'  => $request->jatuh_tempo_awal,
                    'status'       => 'belum_bayar',
                ]);

                for ($i = 1; $i <= $request->jumlah_bulan; $i++) {
                    \App\Models\TagihanSiswa::create([
                        'tagihan_id'   => $tagihanMaster->id,
                        'siswa_id'     => $siswa->id,
                        'bulan_ke'     => $i,
                        'jatuh_tempo'  => \Carbon\Carbon::parse($request->jatuh_tempo_awal)->addMonths($i - 1),
                        'status'       => 'belum_bayar',
                    ]);
                }
            }

            \DB::commit();
            return redirect()->back()->with('success', 'Tagihan berhasil dibuat.');

        } catch (\Exception $e) {
            \DB::rollback();
            // Cek error log jika masih gagal
            \Log::error($e->getMessage());
            return back()->with('error', 'Sistem Error: ' . $e->getMessage());
        }
    }

    public function detail($id)
    {
        try {
            $tagihanInduk = Tagihan::findOrFail($id);

            $details = TagihanSiswa::with(['siswa.user'])
                ->whereHas('tagihan', function($q) use ($tagihanInduk) {
                    $q->where('nama_tagihan', $tagihanInduk->nama_tagihan);
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
                'nama_tagihan' => $tagihanInduk->nama_tagihan,
                'nominal'      => number_format($tagihanInduk->nominal, 0, ',', '.'),
                'data'         => $formattedData
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_tagihan' => 'required',
            'nominal' => 'required|numeric',
            'jumlah_bulan' => 'required|integer|min:1', // Tambahkan ini
        ]);

        try {
            $tagihanAwal = Tagihan::findOrFail($id);
            $namaLama = $tagihanAwal->nama_tagihan;
            
            \DB::beginTransaction();

            // 1. Ambil semua Master Tagihan yang namanya sama (untuk semua siswa)
            $masterTagihans = Tagihan::where('nama_tagihan', $namaLama)->get();

            foreach ($masterTagihans as $master) {
                // Update Master
                $master->update([
                    'nama_tagihan' => $request->nama_tagihan,
                    'nominal' => $request->nominal
                ]);

                // 2. Logika Edit Jumlah Bulan per Siswa
                $currentDetailsCount = TagihanSiswa::where('tagihan_id', $master->id)->count();
                
                if ($request->jumlah_bulan > $currentDetailsCount) {
                    // Jika jumlah bulan ditambah, buat baris baru untuk bulan berikutnya
                    for ($i = $currentDetailsCount + 1; $i <= $request->jumlah_bulan; $i++) {
                        TagihanSiswa::create([
                            'tagihan_id'   => $master->id,
                            'siswa_id'     => $master->siswa_id,
                            'bulan_ke'     => $i,
                            'jatuh_tempo'  => \Carbon\Carbon::parse($master->jatuh_tempo)->addMonths($i - 1),
                            'status'       => 'belum_bayar',
                        ]);
                    }
                } elseif ($request->jumlah_bulan < $currentDetailsCount) {
                    // Jika jumlah bulan dikurangi, hapus bulan yang paling akhir 
                    // (Hanya hapus yang statusnya belum_bayar agar tidak merusak data keuangan)
                    TagihanSiswa::where('tagihan_id', $master->id)
                        ->where('bulan_ke', '>', $request->jumlah_bulan)
                        ->where('status', 'belum_bayar') 
                        ->delete();
                }
            }

            \DB::commit();
            return back()->with('success', 'Tagihan dan periode bulan berhasil diperbarui.');

        } catch (\Exception $e) {
            \DB::rollback();
            return back()->with('error', 'Gagal memperbarui: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $tagihan = Tagihan::findOrFail($id);
            $namaTagihan = $tagihan->nama_tagihan;
            $allRelatedTagihanIds = Tagihan::where('nama_tagihan', $namaTagihan)->pluck('id');

            \DB::beginTransaction();
            \App\Models\TagihanSiswa::whereIn('tagihan_id', $allRelatedTagihanIds)->delete();
            
            Tagihan::whereIn('id', $allRelatedTagihanIds)->delete();

            \DB::commit();
            return back()->with('success', 'Tagihan "' . $namaTagihan . '" untuk semua siswa berhasil dihapus.');

        } catch (\Exception $e) {
            \DB::rollback();
            return back()->with('error', 'Gagal menghapus tagihan: ' . $e->getMessage());
        }
    }
}