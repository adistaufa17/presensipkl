<?php

namespace App\Http\Controllers\Siswa;

use Carbon\Carbon;
use App\Models\Presensi;
use App\Models\TagihanSiswa;
use App\Models\PengaturanJamKerja;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PresensiController extends Controller
{
    public function index()
    {
        $id_siswa = Auth::user()->siswa->id;
        $now = now();
        $labelBulan = $now->locale('id')->isoFormat('MMMM YYYY');
        $countHadir = Presensi::where('siswa_id', $id_siswa)
            ->whereMonth('tanggal', $now->month)
            ->whereYear('tanggal', $now->year)
            ->where('status_kehadiran', 'hadir')->count();
        $countTerlambat = Presensi::where('siswa_id', $id_siswa)
            ->whereMonth('tanggal', $now->month)
            ->whereYear('tanggal', $now->year)
            ->where('status_kehadiran', 'terlambat')->count();
        $countAlpa = Presensi::where('siswa_id', $id_siswa)
            ->whereMonth('tanggal', $now->month)
            ->whereYear('tanggal', $now->year)
            ->whereIn('status_kehadiran', ['alpha', 'izin', 'sakit'])->count();
        $presensiHariIni = Presensi::where('siswa_id', $id_siswa)
            ->whereDate('tanggal', Carbon::today())->first();
        $logs = Presensi::where('siswa_id', $id_siswa)
        ->orderBy('tanggal', 'desc')
        ->take(8)
        ->get();
        $tagihanBelumBayar = TagihanSiswa::with('tagihan')
            ->where('siswa_id', $id_siswa)
            ->where('status', 'belum_bayar')
            ->orderBy('jatuh_tempo', 'asc')->take(3)->get();

        return view('siswa.dashboard', compact(
            'presensiHariIni', 'logs', 'tagihanBelumBayar',
            'labelBulan', 'countHadir', 'countTerlambat', 'countAlpa'
        ));
    }

    private function hitungJarak($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat/2) * sin($dLat/2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon/2) * sin($dLon/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));

        return $earthRadius * $c;
    }

    public function absenMasuk(Request $request)
    {
        try {
            if (!$request->has('foto_masuk')) {
                return response()->json(['success' => false, 'message' => 'Foto tidak ditemukan!'], 400);
            }

            $siswa = Auth::user()->siswa;
            $today = Carbon::today();
            $jamSekarang = now()->format('H:i:s');
            $status = 'hadir';

            $aturan = PengaturanJamKerja::where('is_active', true)->first();
            
            if ($aturan && isset($aturan->batas_telat)) {
                if ($jamSekarang > $aturan->batas_telat) {
                    $status = 'terlambat';
                }
            }
            
            $presensi = Presensi::where('siswa_id', $siswa->id)
                ->whereDate('tanggal', $today)
                ->first();

            if ($presensi && $presensi->status_kehadiran !== 'alpha') {
                return response()->json([
                    'success' => false, 
                    'message' => 'Anda sudah melakukan presensi hari ini (Status: '.$presensi->status_kehadiran.')'
                ], 400);
            }

            $img = $request->foto_masuk;
            $image_parts = explode(";base64,", $img);
            $image_base64 = base64_decode($image_parts[1]);
            $fileName = 'presensi_' . $siswa->id . '_' . time() . '.png';
            $filePath = 'presensi/' . $fileName;
            Storage::disk('public')->put($filePath, $image_base64);

            $dataAbsen = [
                'jam_masuk' => $jamSekarang,
                'foto_masuk' => $filePath,
                'status_kehadiran' => $status,
            ];

            if ($presensi) {
                $presensi->update($dataAbsen);
            } else {
                $dataAbsen['siswa_id'] = $siswa->id;
                $dataAbsen['tanggal'] = $today;
                Presensi::create($dataAbsen);
            }

            return response()->json([
                'success' => true,
                'message' => 'Absensi berhasil disimpan!',
                'data' => ['status' => $status]
            ], 200);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function absenPulang(Request $request)
    {
        $presensi = Presensi::where('siswa_id', auth()->user()->siswa->id)
                            ->whereDate('tanggal', now()->toDateString())
                            ->first();

        if (!$presensi) {
            return response()->json([
                'success' => false, 
                'message' => 'Data absen masuk tidak ditemukan untuk hari ini.'
            ]);
        }

        $presensi->update([
            'jam_pulang' => now()->format('H:i:s'),
            'jurnal_kegiatan' => $request->jurnal_kegiatan, 
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Berhasil simpan jurnal dan absen pulang.'
        ]);
    }

    public function ajukanIzin(Request $request)
    {
        \Log::info('Data request izin:', $request->all());
        
        try {
            $request->validate([
                'status_kehadiran' => 'required|in:izin,sakit',
                'keterangan_izin' => 'required|string|min:10',
                'bukti_izin' => 'nullable|image|mimes:jpeg,jpg,png|max:2048'
            ]);

            $siswa = Auth::user()->siswa;
            $today = today()->toDateString();
            $presensiHariIni = Presensi::where('siswa_id', $siswa->id)
                ->whereDate('tanggal', $today)
                ->first();

            \Log::info('Presensi hari ini:', ['data' => $presensiHariIni]);

            if ($presensiHariIni && $presensiHariIni->jam_masuk) {
                \Log::warning('Sudah absen hari ini');
                return redirect()->back()->with('error', 'Anda sudah melakukan presensi hari ini!');
            }

            $buktiPath = null;
            if ($request->hasFile('bukti_izin')) {
                $buktiPath = $request->file('bukti_izin')->store('presensi/izin', 'public');
                \Log::info('File uploaded:', ['path' => $buktiPath]);
            }
            $dataPresensi = [
                'status_kehadiran' => $request->status_kehadiran,
                'keterangan_izin' => $request->keterangan_izin,
                'jam_masuk' => now()->format('H:i:s'),
                'bukti_izin' => $buktiPath,
            ];

            \Log::info('Data yang akan disimpan:', $dataPresensi);

            $presensi = Presensi::updateOrCreate(
                [
                    'siswa_id' => $siswa->id, 
                    'tanggal' => $today
                ],
                $dataPresensi
            );

            \Log::info('Presensi berhasil disimpan:', ['id' => $presensi->id]);

            return redirect()->route('siswa.dashboard')
                ->with('success', 'Pengajuan ' . $request->status_kehadiran . ' berhasil dikirim!');
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error:', $e->errors());
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
                
        } catch (\Exception $e) {
            \Log::error('Error ajukan izin:', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);
            return redirect()->back()->with('error', 'Gagal mengajukan izin: ' . $e->getMessage());
        }
    }

    public function riwayat(Request $request)
    {
        $id_siswa = Auth::user()->siswa->id;
        
        $bulan = $request->get('bulan', date('m'));
        $tahun = $request->get('tahun', date('Y'));

        $riwayat = Presensi::where('siswa_id', $id_siswa)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->orderBy('tanggal', 'desc')
            ->paginate(31); 

        return view('siswa.riwayat', compact('riwayat', 'bulan', 'tahun'));
    }
}