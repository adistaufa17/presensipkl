<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\TagihanController;
use App\Http\Controllers\PresensiController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ==========================================
// ROUTE PRESENSI (FIXED)
// ==========================================
Route::middleware(['auth'])->group(function () {
    
    // 1. Dashboard Siswa (URL Khusus)
    Route::get('/pembayaran/dashboardsiswa', [PresensiController::class, 'dashboardSiswa'])
        ->name('pembayaran.dashboardsiswa');

    // 2. Dashboard Umum (PERBAIKAN DISINI)
    // Sebelumnya error karena memanggil 'index'. Kita ubah agar memanggil 'dashboardSiswa' juga.
    Route::get('/dashboard', [PresensiController::class, 'dashboardSiswa'])->name('dashboard');

    // 3. Absen Masuk & Keluar
    Route::post('/presensi/masuk', [PresensiController::class, 'storeMasuk'])->name('presensi.masuk');
    Route::post('/presensi/keluar', [PresensiController::class, 'storeKeluar'])->name('presensi.keluar');

    // 4. Izin (PERBAIKAN ROUTE NOT DEFINED)
    // Pastikan route ini bernama 'presensi.store-izin' (pakai strip) agar cocok dengan View
    Route::get('/presensi/izin', [PresensiController::class, 'createIzin'])->name('presensi.izin');
    Route::post('/presensi/store-izin', [PresensiController::class, 'storeIzin'])->name('presensi.store-izin'); 
});

// ==========================================
// ROUTE PEMBAYARAN & TAGIHAN
// ==========================================
Route::middleware(['auth'])->group(function () {
    Route::get('/pembayaran/saya', [PembayaranController::class, 'index'])->name('pembayaran.siswa');
    Route::post('/pembayaran/bayar', [PembayaranController::class, 'bayar'])->name('pembayaran.bayar');

    Route::get('/pembimbing/dashboard', [PembayaranController::class, 'dashboard'])->name('pembimbing.dashboard');
    Route::get('/tagihan', [TagihanController::class, 'index'])->name('tagihan.index');
    Route::get('/tagihan/create', [TagihanController::class, 'create'])->name('tagihan.create');
    Route::post('/tagihan', [TagihanController::class, 'store'])->name('tagihan.store');
    Route::delete('/tagihan/{id}', [TagihanController::class, 'destroy'])->name('tagihan.destroy');
    
    Route::get('/pembayaran/semua', [PembayaranController::class, 'semua'])->name('pembayaran.semua');
    Route::get('/pembayaran/siswa/{user_id}', [PembayaranController::class, 'bySiswa'])->name('pembayaran.by_siswa');
    Route::get('/pembayaran/{id}', [PembayaranController::class, 'detail'])->name('pembayaran.detail');
    Route::post('/pembayaran/{id}/status', [PembayaranController::class, 'updateStatus'])->name('pembayaran.status');
    Route::post('/pembayaran/{id}/reset', [PembayaranController::class, 'reset'])->name('pembayaran.reset');
    Route::delete('/pembayaran/{id}', [PembayaranController::class, 'destroy'])->name('pembayaran.destroy');
});
Route::middleware(['auth'])->group(function () {
    // ... route yang lain ...

    // Route Dashboard Admin
    Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
});
require __DIR__.'/auth.php';