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
    public function index()
    {
        $tagihans = Tagihan::with('tagihanSiswas.siswa.user')->get();
        return view('admin.tagihan.index', compact('tagihans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'siswa_id'         => 'required|array', 
            'siswa_id.*'       => 'exists:siswas,id', 
            'nama_tagihan'     => 'required',
            'nominal'          => 'required|numeric',
            'jumlah_bulan'     => 'required|integer|min:1',
            'jatuh_tempo_awal' => 'required|date',
        ]);

        try {
            \DB::beginTransaction();
            $selectedSiswa = $request->siswa_id;
            foreach ($selectedSiswa as $idSiswa) {
                $siswa = \App\Models\Siswa::findOrFail($idSiswa);

                $tagihanMaster = Tagihan::firstOrCreate(
                    [
                        'nama_tagihan' => $request->nama_tagihan,
                    ],
                    [
                        'nominal'     => $request->nominal,
                        'jatuh_tempo' => $request->jatuh_tempo_awal,
                        'status'      => 'belum_bayar',
                    ]
                );

                for ($i = 1; $i <= $request->jumlah_bulan; $i++) {
                    $jatuhTempo = \Carbon\Carbon::parse($request->jatuh_tempo_awal)->addMonths($i - 1);
                    
                    TagihanSiswa::firstOrCreate(
                        [
                            'tagihan_id' => $tagihanMaster->id,
                            'siswa_id'   => $siswa->id,
                            'bulan_ke'   => $i,
                        ],
                        [
                            'jatuh_tempo' => $jatuhTempo,
                            'status'      => 'belum_bayar',
                        ]
                    );

                }
            }

            \DB::commit();
            return redirect()->back()->with('success', 'Tagihan berhasil dibuat untuk ' . count($selectedSiswa) . ' siswa.');

        } catch (\Exception $e) {
            \DB::rollback();
            return back()->with('error', 'Gagal: ' . $e->getMessage());
        }

        $exists = Tagihan::whereIn('siswa_id', $request->siswa_id)
                     ->where('status', 'berjalan')
                     ->exists();

        if ($exists) {
            return back()->with('error', 'Salah satu siswa yang dipilih sudah memiliki tagihan yang masih aktif!');
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
                    'siswa_id' => $item->siswa_id,
                    'siswa' => [
                        'id' => $item->siswa_id, 
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
        $tagihan = Tagihan::findOrFail($id);

        $request->validate([
            'nama_tagihan' => 'required',
            'nominal' => 'required|numeric',
            'jumlah_bulan' => 'required|integer|min:1',
            'siswa_id' => 'array'
        ]);

        $tagihan->update([
            'nama_tagihan' => $request->nama_tagihan,
            'nominal' => $request->nominal,
        ]);

        $siswaLama = TagihanSiswa::where('tagihan_id', $tagihan->id)
            ->pluck('siswa_id')
            ->unique()
            ->toArray();

        $siswaBaru = $request->siswa_id ?? [];

        $siswaDihapus = array_diff($siswaLama, $siswaBaru);

        if (!empty($siswaDihapus)) {
            TagihanSiswa::where('tagihan_id', $tagihan->id)
                ->whereIn('siswa_id', $siswaDihapus)
                ->where('status', 'belum_bayar') 
                ->delete();
        }

        foreach ($siswaBaru as $siswaId) {
            for ($bulan = 1; $bulan <= $request->jumlah_bulan; $bulan++) {
                TagihanSiswa::firstOrCreate([
                    'tagihan_id' => $tagihan->id,
                    'siswa_id'   => $siswaId,
                    'bulan_ke'   => $bulan,
                ], [
                    'jatuh_tempo' => now(),
                    'status' => 'belum_bayar'
                ]);
            }
        }

        return back()->with('success', 'Tagihan berhasil diperbarui');
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