<?php

namespace App\Http\Controllers;

use App\Models\Presence;
use App\Models\Journal;
use App\Models\Permission;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PresenceController extends Controller
{
    /**
     * Halaman utama absensi siswa
     */
    public function index()
    {
        $user = Auth::user();
        $today = Carbon::today()->toDateString();

        $presenceToday = Presence::where('user_id', $user->id)
                                 ->whereDate('date', $today)
                                 ->first();

        $recentPresences = Presence::where('user_id', $user->id)
                                   ->orderBy('date', 'desc')
                                   ->limit(20)
                                   ->get();

        return view('presence.index', compact('presenceToday', 'recentPresences'));
    }

    /**
     * Absen masuk (check-in)
     */
    public function checkIn(Request $request)
    {
        $user = Auth::user();
        $today = Carbon::today()->toDateString();

        // Cek apakah ada izin yang disetujui untuk hari ini
        $approvedPermission = Permission::where('user_id', $user->id)
                                       ->whereDate('date', $today)
                                       ->where('status', 'approved')
                                       ->first();

        if ($approvedPermission) {
            return back()->with('error', 'Tidak bisa absen karena sudah ada izin/sakit yang disetujui untuk hari ini.');
        }

        // Cek apakah sudah absen masuk
        $presence = Presence::where('user_id', $user->id)
                           ->whereDate('date', $today)
                           ->first();

        if ($presence && $presence->time_in) {
            return back()->with('error', 'Kamu sudah melakukan absen masuk hari ini.');
        }

        // Logika absen masuk
        $now = Carbon::now();
        $checkInTime = Carbon::createFromFormat('H:i:s', '08:00:00');
        $isLate = $now->greaterThan($checkInTime);

        DB::transaction(function () use ($user, $today, $now, $isLate, &$presence) {
            $presence = Presence::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'date' => $today,
                ],
                [
                    'time_in' => $now->format('H:i:s'),
                    'status' => $isLate ? 'terlambat' : 'hadir',
                    'is_late' => $isLate,
                ]
            );
        });

        $message = 'Absen masuk tercatat pada ' . $now->format('H:i:s');
        
        if ($isLate) {
            $message .= ' (Terlambat)';
        }

        return back()->with('success', $message);
    }

    /**
     * Absen pulang (check-out)
     */
    public function checkOut(Request $request)
    {
        $user = Auth::user();
        $today = Carbon::today()->toDateString();

        $presence = Presence::where('user_id', $user->id)
                           ->whereDate('date', $today)
                           ->first();

        if (!$presence || !$presence->time_in) {
            return back()->with('error', 'Belum melakukan absen masuk hari ini.');
        }

        if ($presence->time_out) {
            return back()->with('error', 'Sudah melakukan absen pulang hari ini.');
        }

        $now = Carbon::now();
        
        $presence->update([
            'time_out' => $now->format('H:i:s')
        ]);

        // Redirect ke form jurnal agar siswa wajib isi jurnal
        return redirect()
            ->route('presence.journal.form')
            ->with('info', 'Absen pulang tercatat. Silakan isi jurnal kegiatan harian.');
    }

    /**
     * Tampilkan form pengisian jurnal
     */
    public function showJournalForm()
    {
        $user = Auth::user();
        $today = Carbon::today()->toDateString();

        // Cek apakah sudah absen pulang hari ini
        $presence = Presence::where('user_id', $user->id)
                           ->whereDate('date', $today)
                           ->whereNotNull('time_out')
                           ->first();

        if (!$presence) {
            return redirect()
                ->route('presence.index')
                ->with('error', 'Belum melakukan absen pulang hari ini.');
        }

        // Cek apakah jurnal sudah dibuat
        $journalExists = Journal::where('user_id', $user->id)
                               ->whereDate('date', $today)
                               ->exists();

        if ($journalExists) {
            return redirect()
                ->route('presence.index')
                ->with('info', 'Jurnal hari ini sudah terisi.');
        }

        return view('presence.journal', compact('today'));
    }

    /**
     * Submit jurnal kegiatan harian
     */
    public function submitJournal(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:191',
            'description' => 'required|string|max:2000',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:5120' // Max 5MB
        ]);

        $user = Auth::user();
        $today = Carbon::today()->toDateString();

        $presence = Presence::where('user_id', $user->id)
                           ->whereDate('date', $today)
                           ->first();

        if (!$presence) {
            return back()->with('error', 'Presensi hari ini tidak ditemukan.');
        }

        // Cek apakah jurnal sudah ada
        $journalExists = Journal::where('user_id', $user->id)
                               ->whereDate('date', $today)
                               ->exists();

        if ($journalExists) {
            return back()->with('error', 'Jurnal hari ini sudah terisi.');
        }

        // Upload foto jika ada
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('journals', 'public');
        }

        DB::transaction(function () use ($user, $presence, $today, $request, $photoPath) {
            Journal::create([
                'user_id' => $user->id,
                'presence_id' => $presence->id,
                'date' => $today,
                'title' => $request->title,
                'description' => $request->description,
                'photo_path' => $photoPath,
            ]);
        });

        return redirect()
            ->route('presence.index')
            ->with('success', 'Jurnal berhasil disimpan.');
    }

    /**
     * Tampilkan form pengajuan izin/sakit
     */
    public function showPermissionForm()
    {
        return view('presence.permission');
    }

    /**
     * Submit permohonan izin/sakit
     */
    public function submitPermission(Request $request)
    {
        $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'type' => 'required|in:izin,sakit',
            'reason' => 'required|string|max:500',
            'proof' => 'nullable|image|mimes:jpg,jpeg,png|max:5120' // Max 5MB
        ]);

        $user = Auth::user();

        // Cek apakah sudah ada pengajuan untuk tanggal yang sama
        $existingPermission = Permission::where('user_id', $user->id)
                                       ->whereDate('date', $request->date)
                                       ->whereIn('status', ['pending', 'approved'])
                                       ->exists();

        if ($existingPermission) {
            return back()->with('error', 'Sudah ada pengajuan izin/sakit untuk tanggal tersebut.');
        }

        // Upload bukti jika ada
        $proofPath = null;
        if ($request->hasFile('proof')) {
            $proofPath = $request->file('proof')->store('permissions', 'public');
        }

        DB::transaction(function () use ($user, $request, $proofPath) {
            Permission::create([
                'user_id' => $user->id,
                'date' => $request->date,
                'type' => $request->type,
                'reason' => $request->reason,
                'proof_path' => $proofPath,
                'status' => 'pending',
            ]);
        });

        return redirect()
            ->route('presence.index')
            ->with('success', 'Permohonan izin/sakit terkirim, menunggu verifikasi pembimbing.');
    }
}