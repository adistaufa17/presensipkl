<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\PresenceController;
use App\Http\Controllers\MentorController;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'role:siswa'])->group(function () {

    // Daftar pembayaran milik siswa
    Route::get('/pembayaran/saya', [PembayaranController::class, 'myPayment'])
        ->name('pembayaran.siswa');

    // Form tambah pembayaran
    Route::get('/pembayaran/buat', [PembayaranController::class, 'create'])
        ->name('pembayaran.create');

    // Simpan pembayaran
    Route::post('/pembayaran/buat', [PembayaranController::class, 'store'])
        ->name('pembayaran.store');
});


Route::middleware(['auth', 'role:pembimbing'])->group(function () {

    // Semua pembayaran siswa
    Route::get('/pembayaran/semua', [PembayaranController::class, 'allPayment'])
        ->name('pembayaran.semua');

    // Detail pembayaran
    Route::get('/pembayaran/detail/{id}', [PembayaranController::class, 'show'])
        ->name('pembayaran.detail');

    // Update status (diterima / ditolak)
    Route::post('/pembayaran/status/{id}', [PembayaranController::class, 'updateStatus'])
        ->name('pembayaran.status');
});
Route::middleware(['auth','role:siswa'])->group(function(){
    Route::get('/presence', [PresenceController::class,'index'])->name('presence.index');
    Route::post('/presence/check-in', [PresenceController::class,'checkIn'])->name('presence.checkin');
    Route::post('/presence/check-out', [PresenceController::class,'checkOut'])->name('presence.checkout');

    // jurnal
    Route::get('/presence/journal', [PresenceController::class,'showJournalForm'])->name('presence.journal.form');
    Route::post('/presence/journal', [PresenceController::class,'submitJournal'])->name('presence.journal.submit');

    // izin/sakit
    Route::get('/presence/permission', [PresenceController::class,'showPermissionForm'])->name('presence.permission.form');
    Route::post('/presence/permission', [PresenceController::class,'submitPermission'])->name('presence.permission.submit');
});
Route::middleware(['auth', 'role:pembimbing'])->prefix('pembimbing')->group(function () {

    // Dashboard pembimbing
    Route::get('/', [PembimbingController::class, 'dashboard'])->name('pembimbing.dashboard');

    // Presensi siswa
    Route::get('/presensi', [PembimbingController::class, 'presensiList'])->name('pembimbing.presensi');
    Route::get('/presensi/{id}', [PembimbingController::class, 'presensiDetail'])->name('pembimbing.presensi.detail');

    // Jurnal
    Route::get('/jurnal', [PembimbingController::class, 'jurnalList'])->name('pembimbing.jurnal');
    Route::get('/jurnal/{id}', [PembimbingController::class, 'jurnalDetail'])->name('pembimbing.jurnal.detail');

    // Izin / Sakit
    Route::get('/izin', [PembimbingController::class, 'permissionList'])->name('pembimbing.permission');
    Route::post('/izin/{id}/approve', [PembimbingController::class, 'approvePermission'])->name('pembimbing.permission.approve');
    Route::post('/izin/{id}/reject', [PembimbingController::class, 'rejectPermission'])->name('pembimbing.permission.reject');

    // Pembayaran bila kamu pakai menu pembayaran
    Route::get('/pembayaran', [PembimbingController::class, 'paymentList'])->name('pembimbing.payment');
    Route::post('/pembayaran/{id}/approve', [PembimbingController::class, 'approvePayment'])->name('pembimbing.payment.approve');
    Route::post('/pembayaran/{id}/reject', [PembimbingController::class, 'rejectPayment'])->name('pembimbing.payment.reject');
});
require __DIR__.'/auth.php';
