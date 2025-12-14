<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\TagihanController;
use App\Http\Controllers\PresensiController;
use Illuminate\Support\Facades\Route;

// ============================================
// PUBLIC ROUTES
// ============================================
Route::get('/', function () {
    return view('welcome');
});

// ============================================
// AUTH ROUTES (Laravel Breeze)
// ============================================
require __DIR__.'/auth.php';

// ============================================
// PROFILE ROUTES (semua role)
// ============================================
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ============================================
// DASHBOARD AUTO-REDIRECT BY ROLE
// ============================================
Route::middleware('auth')->get('/dashboard', function () {
    $user = auth()->user();
    
    if ($user->role === 'pembimbing') {
        return redirect()->route('pembimbing.dashboard');
    }
    
    if ($user->role === 'siswa') {
        return redirect()->route('siswa.dashboard');
    }
    
    abort(403, 'Role tidak dikenali');
})->name('dashboard');

// ============================================
// SISWA ROUTES (role: siswa)
// ============================================
Route::middleware(['auth'])->group(function () {
    
    // Dashboard Siswa
    Route::get('/siswa/dashboard', [DashboardController::class, 'siswa'])
        ->name('siswa.dashboard');
    
    // Presensi
    Route::post('/presensi/masuk', [PresensiController::class, 'storeMasuk'])
        ->name('presensi.masuk');
    
    Route::post('/presensi/keluar', [PresensiController::class, 'storeKeluar'])
        ->name('presensi.keluar');
    
    Route::get('/presensi/izin', [PresensiController::class, 'createIzin'])
        ->name('presensi.izin');
    
    Route::post('/presensi/izin', [PresensiController::class, 'storeIzin'])
        ->name('presensi.izin.store');
    
    Route::get('/presensi/riwayat', [PresensiController::class, 'riwayat'])
        ->name('presensi.riwayat');
    
    // Pembayaran Siswa
    Route::get('/pembayaran/siswa', [PembayaranController::class, 'index'])
        ->name('pembayaran.siswa');
    
    Route::post('/pembayaran/bayar', [PembayaranController::class, 'bayar'])
        ->name('pembayaran.bayar');
});

// ============================================
// PEMBIMBING ROUTES (role: pembimbing)
// ============================================
Route::middleware(['auth'])->group(function () {
    
    // Dashboard Pembimbing
    Route::get('/pembimbing/dashboard', [DashboardController::class, 'adminPembimbing'])
        ->name('pembimbing.dashboard');
    
    // Kelola Siswa
    Route::get('/pembimbing/siswa/create', [UserController::class, 'createSiswa'])
        ->name('pembimbing.siswa.create');
    
    Route::post('/pembimbing/siswa/store', [UserController::class, 'storeSiswa'])
        ->name('pembimbing.siswa.store');
    
    // Kelola Tagihan
    Route::get('/tagihan', [TagihanController::class, 'index'])
        ->name('tagihan.index');
    
    Route::get('/tagihan/create', [TagihanController::class, 'create'])
        ->name('tagihan.create');
    
    Route::post('/tagihan', [TagihanController::class, 'store'])
        ->name('tagihan.store');
    
    Route::delete('/tagihan/{id}', [TagihanController::class, 'destroy'])
        ->name('tagihan.destroy');
    
    // Monitor Pembayaran
    Route::get('/pembayaran/semua', [PembayaranController::class, 'semua'])
        ->name('pembayaran.semua');
    
    Route::get('/pembayaran/{id}', [PembayaranController::class, 'detail'])
        ->name('pembayaran.detail');
    
    Route::get('/pembayaran/siswa/{user_id}', [PembayaranController::class, 'bySiswa'])
        ->name('pembayaran.by_siswa');
    
    Route::post('/pembayaran/{id}/status', [PembayaranController::class, 'updateStatus'])
        ->name('pembayaran.status');
    
    Route::post('/pembayaran/{id}/reset', [PembayaranController::class, 'reset'])
        ->name('pembayaran.reset');
    
    Route::delete('/pembayaran/{id}', [PembayaranController::class, 'destroy'])
        ->name('pembayaran.destroy');
    
    // Rekap Presensi
    Route::get('/presensi/rekap', [PresensiController::class, 'rekap'])
        ->name('presensi.rekap');
});