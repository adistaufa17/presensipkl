<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Siswa;
use App\Models\Sekolah;
use App\Models\Presensi;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\Controller; 
use Illuminate\Support\Facades\Auth;

class PresensiController extends Controller
{
    public function index(Request $request)
    {
        $tanggal = $request->get('tanggal', Carbon::today()->format('Y-m-d'));
        $tanggalMulai = $request->get('tanggal_mulai');
        $tanggalAkhir = $request->get('tanggal_akhir');
        $status = $request->get('status', 'all');
        $sekolah = $request->get('sekolah', 'all');
        $search = $request->get('search');
        
        $query = Presensi::with(['siswa.user', 'siswa.sekolah']);
        
        if ($tanggalMulai && $tanggalAkhir) {
            $query->whereBetween('tanggal', [$tanggalMulai, $tanggalAkhir]);
        } else {
            $query->whereDate('tanggal', $tanggal);
        }
        
        if ($status !== 'all') { $query->where('status_kehadiran', $status); }
        if ($sekolah !== 'all') {
            $query->whereHas('siswa', function($q) use ($sekolah) {
                $q->where('sekolah_id', $sekolah);
            });
        }
        
        if ($search) {
            $query->whereHas('siswa.user', function($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }


        $presensis = $query->orderBy('tanggal', 'desc')
                          ->orderBy('jam_masuk', 'desc')
                          ->paginate(20)
                          ->appends($request->all());
        
        $statsQuery = Presensi::query(); 
        if ($tanggalMulai && $tanggalAkhir) {
            $statsQuery->whereBetween('tanggal', [$tanggalMulai, $tanggalAkhir]);
        } else {
            $statsQuery->whereDate('tanggal', $tanggal);
        }

        $stats = [
            'total' => $statsQuery->count(),
            'hadir' => (clone $statsQuery)->where('status_kehadiran', 'hadir')->count(),
            'telat' => (clone $statsQuery)->where('status_kehadiran', 'telat')->count(),
            'izin' => (clone $statsQuery)->whereIn('status_kehadiran', ['izin', 'sakit'])->count(),
            'alpha' => (clone $statsQuery)->where('status_kehadiran', 'alpha')->count(),
        ];
        
        $sekolahs = Sekolah::orderBy('nama_sekolah')->get();
        
        return view('admin.presensi', compact(
            'presensis', 'stats', 'tanggal', 'tanggalMulai', 
            'tanggalAkhir', 'status', 'sekolah', 'search', 'sekolahs'
        ));
    }
    
    public function show(Request $request, $siswaId)
    {
        $siswa = Siswa::with(['user', 'sekolah'])->findOrFail($siswaId);
        
        $bulan = $request->get('bulan', Carbon::now()->format('Y-m'));
        
        $riwayat = Presensi::where('siswa_id', $siswaId)
            ->whereYear('tanggal', Carbon::parse($bulan)->year)
            ->whereMonth('tanggal', Carbon::parse($bulan)->month)
            ->orderBy('tanggal', 'desc')
            ->paginate(31)
            ->appends(['bulan' => $bulan]);
        
        $stats = [
            'hadir' => Presensi::where('siswa_id', $siswaId)
                ->whereYear('tanggal', Carbon::parse($bulan)->year)
                ->whereMonth('tanggal', Carbon::parse($bulan)->month)
                ->where('status_kehadiran', 'hadir')
                ->count(),
            'telat' => Presensi::where('siswa_id', $siswaId)
                ->whereYear('tanggal', Carbon::parse($bulan)->year)
                ->whereMonth('tanggal', Carbon::parse($bulan)->month)
                ->where('status_kehadiran', 'telat')
                ->count(),
            'izin' => Presensi::where('siswa_id', $siswaId)
                ->whereYear('tanggal', Carbon::parse($bulan)->year)
                ->whereMonth('tanggal', Carbon::parse($bulan)->month)
                ->whereIn('status_kehadiran', ['izin', 'sakit'])
                ->count(),
            'alpha' => Presensi::where('siswa_id', $siswaId)
                ->whereYear('tanggal', Carbon::parse($bulan)->year)
                ->whereMonth('tanggal', Carbon::parse($bulan)->month)
                ->where('status_kehadiran', 'alpha')
                ->count(),
        ];
        
        $totalHariKerja = Carbon::parse($bulan)->daysInMonth;
        $persentaseKehadiran = $totalHariKerja > 0 
            ? number_format(($stats['hadir'] / $totalHariKerja) * 100, 1) 
            : 0;
        
        return view('admin.presensi.show', compact(
            'siswa', 
            'riwayat', 
            'stats', 
            'bulan',
            'persentaseKehadiran',
            'totalHariKerja'
        ));
    }
    
    public function store(Request $request)
    {
        $siswaId = auth()->user()->siswa->id;
        $today = now()->format('Y-m-d');
        $jamSekarang = now()->format('H:i');
        $batasMasuk = '08:00';

        $presensi = Presensi::where('siswa_id', $siswaId)
                            ->whereDate('tanggal', $today)
                            ->first();

        $statusBaru = ($jamSekarang > $batasMasuk) ? 'telat' : 'hadir';

        if ($presensi) {
            // JIKA STATUSNYA ALPHA (Dihasilkan oleh sistem)
            if ($presensi->status_kehadiran == 'alpha') {
                $presensi->update([
                    'status_kehadiran' => $statusBaru,
                    'jam_masuk' => now(),
                    'keterangan_izin' => null, // <--- TAMBAHKAN INI: Menghapus teks "Tidak hadir tanpa keterangan"
                    'keterangan' => 'Absen setelah Alpha (Otomatis Update)'
                ]);

                return redirect()->back()->with('success', 'Berhasil melakukan presensi! Status: ' . ucfirst($statusBaru));
            } 
            
            return redirect()->back()->with('error', 'Anda sudah melakukan presensi hari ini.');
        }

        // JIKA BELUM ADA DATA SAMA SEKALI
        Presensi::create([
            'siswa_id' => $siswaId,
            'tanggal' => $today,
            'jam_masuk' => now(),
            'status_kehadiran' => $statusBaru,
            'keterangan_izin' => null, // <--- PASTIKAN INI NULL
        ]);

        return redirect()->back()->with('success', 'Berhasil absen! Status: ' . ucfirst($statusBaru));
    }

    public function exportPDF(Request $request, $siswaId)
    {
        // 1. Ambil data siswa beserta relasi sekolah
        $siswa = Siswa::with(['user', 'sekolah'])->findOrFail($siswaId);
        
        // 2. Ambil data sekolah dari relasi siswa untuk dikirim ke view
        $sekolah = $siswa->sekolah; 

        $bulan = $request->get('bulan', Carbon::now()->format('Y-m'));
        $bulanNama = Carbon::parse($bulan)->translatedFormat('F Y');
        
        $presensis = Presensi::where('siswa_id', $siswaId)
            ->whereYear('tanggal', Carbon::parse($bulan)->year)
            ->whereMonth('tanggal', Carbon::parse($bulan)->month)
            ->orderBy('tanggal', 'asc')
            ->get();
        
        $stats = [
            'hadir' => $presensis->where('status_kehadiran', 'hadir')->count(),
            'telat' => $presensis->where('status_kehadiran', 'telat')->count(),
            'izin' => $presensis->whereIn('status_kehadiran', ['izin', 'sakit'])->count(),
            'alpha' => $presensis->where('status_kehadiran', 'alpha')->count(),
        ];

        // 3. Sesuaikan dengan template Blade Anda yang meminta $rekapData
        // Karena ini export per siswa, kita buat array yang berisi 1 data saja
        $rekapData = [
            [
                'siswa' => $siswa,
                'hadir' => $stats['hadir'],
                'telat' => $stats['telat'],
                'izin' => $stats['izin'],
                'alpha' => $stats['alpha'],
            ]
        ];
        
        $totalHariKerja = Carbon::parse($bulan)->daysInMonth;
        
        $pdf = PDF::loadView('admin.presensi.pdf-rekap', compact(
            'siswa',
            'sekolah',
            'presensis',
            'stats',
            'bulanNama',
            'rekapData', 
            'totalHariKerja'
        ));
        
        $fileName = 'Laporan_Presensi_' . str_replace(' ', '_', $siswa->user->nama_lengkap) . '_' . $bulan . '.pdf';
        
        return $pdf->download($fileName);
    }
    
    public function exportRekapPDF(Request $request)
    {
        $sekolahId = $request->get('sekolah_id');
        $bulan = $request->get('bulan', Carbon::now()->format('Y-m'));
        $bulanNama = Carbon::parse($bulan)->translatedFormat('F Y');
        
        $sekolah = \App\Models\Sekolah::findOrFail($sekolahId);
        
        $siswas = Siswa::with(['user'])
            ->where('sekolah_id', $sekolahId)
            ->get();
        
        $rekapData = [];
        foreach ($siswas as $siswa) {
            $presensis = Presensi::where('siswa_id', $siswa->id)
                ->whereYear('tanggal', Carbon::parse($bulan)->year)
                ->whereMonth('tanggal', Carbon::parse($bulan)->month)
                ->get();
            
            $rekapData[] = [
                'siswa' => $siswa,
                'hadir' => $presensis->where('status_kehadiran', 'hadir')->count(),
                'telat' => $presensis->where('status_kehadiran', 'telat')->count(),
                'izin' => $presensis->whereIn('status_kehadiran', ['izin', 'sakit'])->count(),
                'alpha' => $presensis->where('status_kehadiran', 'alpha')->count(),
            ];
        }
        
        $pdf = PDF::loadView('admin.presensi.pdf-rekap', compact(
            'sekolah',
            'rekapData',
            'bulanNama'
        ));
        
        $fileName = 'Rekap_Presensi_' . str_replace(' ', '_', $sekolah->nama_sekolah) . '_' . $bulan . '.pdf';
        
        return $pdf->setPaper('a4', 'landscape')->download($fileName);
    }
    
    public function detail($id)
    {
        $presensi = Presensi::with(['siswa.user', 'siswa.sekolah'])->findOrFail($id);
        
        return response()->json([
            'success' => true,
            'data' => $presensi
        ]);
    }
    
    public function update(Request $request, $id)
    {
        $request->validate([
            'status_kehadiran' => 'required|in:hadir,telat,izin,sakit,alpha',
            'keterangan_izin' => 'nullable|string|max:500'
        ]);
        
        $presensi = Presensi::findOrFail($id);
        $presensi->update([
            'status_kehadiran' => $request->status_kehadiran,
            'keterangan_izin' => $request->keterangan_izin,
        ]);
        
        return redirect()->back()->with('success', 'Status presensi berhasil diupdate!');
    }

    public function generateAlpha()
    {
        $hariIni = date('Y-m-d');
        $semuaSiswa = \App\Models\Siswa::pluck('id');
        $sudahAbsen = \App\Models\Presensi::whereDate('tanggal', $hariIni)
                            ->pluck('siswa_id');

        $belumAbsen = $semuaSiswa->diff($sudahAbsen);

        foreach ($belumAbsen as $idSiswa) {
            \App\Models\Presensi::create([
                'siswa_id' => $idSiswa,
                'tanggal'  => $hariIni,
                'status_kehadiran'   => 'alpha',
                'keterangan_izin' => 'Tidak hadir tanpa keterangan' // Perbaiki nama kolom di sini
            ]);
        }

        return redirect()->back()->with('success', 'Data Alpha berhasil dihasilkan!');
    }
}