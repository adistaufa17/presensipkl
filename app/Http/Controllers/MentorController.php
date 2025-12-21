<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Presence;
use App\Models\Journal;
use App\Models\Permission;
use Illuminate\Http\Request;
use Carbon\Carbon;

class MentorController extends Controller
{
    /**
     * Dashboard pembimbing - menampilkan statistik kehadiran hari ini
     */
    public function dashboard()
    {
        $today = Carbon::today()->toDateString();

        $statistics = [
            'hadir' => Presence::whereDate('date', $today)
                              ->where('status', 'hadir')
                              ->count(),
            
            'terlambat' => Presence::whereDate('date', $today)
                                  ->where('is_late', true)
                                  ->count(),
            
            'izin' => Permission::whereDate('date', $today)
                               ->where('status', 'approved')
                               ->count(),
            
            'belum_absen' => User::where('role', 'siswa')
                                ->whereDoesntHave('presences', function ($query) use ($today) {
                                    $query->whereDate('date', $today);
                                })
                                ->count(),
        ];

        return view('mentor.dashboard', $statistics);
    }

    /**
     * Daftar absensi siswa berdasarkan tanggal
     */
    public function presenceList(Request $request)
    {
        $request->validate([
            'date' => 'nullable|date'
        ]);

        $date = $request->date ?? Carbon::today()->toDateString();

        $presences = Presence::with('user')
                            ->whereDate('date', $date)
                            ->orderBy('time_in', 'asc')
                            ->get();

        return view('mentor.presence.index', compact('presences', 'date'));
    }

    /**
     * Daftar jurnal siswa dengan pagination
     */
    public function journals()
    {
        $journals = Journal::with('user')
                          ->orderBy('date', 'desc')
                          ->paginate(20);

        return view('mentor.journal.index', compact('journals'));
    }

    /**
     * Detail jurnal siswa
     */
    public function journalDetail($id)
    {
        $journal = Journal::with('user')->findOrFail($id);
        
        return view('mentor.journal.detail', compact('journal'));
    }

    /**
     * Daftar permohonan izin/sakit
     */
    public function permissionList()
    {
        $permissions = Permission::with('user')
                                ->orderBy('created_at', 'desc')
                                ->paginate(20);

        return view('mentor.permission.index', compact('permissions'));
    }

    /**
     * Verifikasi (approve/reject) permohonan izin
     */
    public function verifyPermission(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
            'note' => 'nullable|string|max:500'
        ]);

        $permission = Permission::findOrFail($id);
        
        // Cek apakah sudah diverifikasi sebelumnya
        if ($permission->status !== 'pending') {
            return back()->with('error', 'Izin ini sudah diverifikasi sebelumnya.');
        }

        $permission->update([
            'status' => $request->status,
            'note' => $request->note,
            'verified_at' => Carbon::now(),
        ]);

        $statusText = $request->status === 'approved' ? 'disetujui' : 'ditolak';
        
        return back()->with('success', "Permohonan izin berhasil {$statusText}.");
    }
}