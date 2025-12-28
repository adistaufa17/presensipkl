<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Siswa\ProfileController as ProfileSiswaController;
use App\Http\Controllers\Admin\SekolahController;
use App\Http\Controllers\Admin\TagihanController;
use App\Http\Controllers\Siswa\PresensiController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PembayaranController;
use App\Http\Controllers\Admin\SettingJamController;
use App\Http\Controllers\Siswa\SiswaTagihanController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Admin\SiswaController; // Tambahkan ini
use App\Http\Controllers\Admin\PresensiController as PresensiAdminController;

Route::get('/', function () {
    if (auth()->check()) {
        if (auth()->user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('siswa.dashboard');
    }
    return view('auth.login');
});

Route::get('login', [AuthenticatedSessionController::class, 'create'])
                ->name('login');
require __DIR__.'/auth.php';

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    
    Route::get('/siswa', [SiswaController::class, 'index'])->name('siswa.index');
    Route::post('/siswa/store', [SiswaController::class, 'store'])->name('siswa.store');
    Route::put('/siswa/update/{id}', [SiswaController::class, 'update'])->name('siswa.update'); 
    Route::delete('/siswa/delete/{id}', [SiswaController::class, 'destroy'])->name('siswa.destroy');
    Route::get('/sekolah', [SekolahController::class, 'index'])->name('sekolah.index');
    Route::post('/sekolah/store', [SekolahController::class, 'store'])->name('sekolah.store');
    Route::put('/sekolah/update/{id}', [SekolahController::class, 'update'])->name('sekolah.update');
    Route::delete('/sekolah/delete/{id}', [SekolahController::class, 'destroy'])->name('sekolah.destroy');

    Route::get('/presensi', [PresensiAdminController::class, 'index'])->name('presensi');
    Route::get('/presensi/{siswaId}', [PresensiAdminController::class, 'show'])->name('presensi.show');
    Route::get('/presensi/{id}/detail', [PresensiAdminController::class, 'detail'])->name('presensi.detail');
    Route::put('/presensi/{id}/update', [PresensiAdminController::class, 'update'])->name('presensi.update');
    Route::get('/presensi/export/pdf/{siswaId}', [PresensiAdminController::class, 'exportPDF'])->name('presensi.export.pdf');
    Route::get('/presensi/export/rekap', [PresensiAdminController::class, 'exportRekapPDF'])->name('presensi.export.rekap');
    Route::post('/presensi/generate-alpha', [PresensiAdminController::class, 'generateAlpha'])->name('presensi.alpha');    Route::get('/pembayaran', [PembayaranController::class, 'index'])->name('pembayaran.index');    Route::get('/pembayaran/export', [PembayaranController::class, 'export'])->name('pembayaran.export');
    // --- Bagian Pembayaran (Sudah Benar) ---
    Route::get('/pembayaran', [PembayaranController::class, 'index'])->name('pembayaran.index');
    Route::get('/pembayaran/export', [PembayaranController::class, 'export'])->name('pembayaran.export');
    Route::put('/pembayaran/konfirmasi/{id}', [TagihanController::class, 'konfirmasi'])->name('tagihan.konfirmasi');
    Route::get('/pembayaran/{id}/detail', [PembayaranController::class, 'showDetail'])->name('pembayaran.detail');
    Route::put('/pembayaran/konfirmasi/{id}', [PembayaranController::class, 'konfirmasi'])->name('pembayaran.konfirmasi');
    
    Route::put('/tagihan/konfirmasi/{id}', [PembayaranController::class, 'konfirmasi'])->name('pembayaran.konfirmasi');
    Route::post('/tagihan/store', [TagihanController::class, 'store'])->name('tagihan.store');
    Route::get('/tagihan/{id}/detail', [TagihanController::class, 'detail']);
    Route::put('/tagihan/{id}', [TagihanController::class, 'update'])->name('tagihan.update');
    Route::delete('/tagihan/{id}', [TagihanController::class, 'destroy'])->name('tagihan.destroy');
    
    Route::get('/setting-jam-kerja', [SettingJamController::class, 'index'])->name('setting.jam-kerja');
    Route::put('/setting-jam-kerja', [SettingJamController::class, 'update'])->name('setting.jam-kerja.update');
});

// Group Siswa
Route::middleware(['auth', 'role:siswa'])->prefix('siswa')->name('siswa.')->group(function () {
    Route::get('/profile', [ProfileSiswaController::class, 'index'])->name('profile');
    Route::get('/dashboard', [PresensiController::class, 'index'])->name('dashboard');
    Route::post('/presensi/masuk', [PresensiController::class, 'absenMasuk'])->name('absenMasuk');
    Route::post('/presensi/pulang', [PresensiController::class, 'absenPulang'])->name('absenPulang');
    Route::post('/presensi/izin', [PresensiController::class, 'ajukanIzin'])->name('ajukanIzin');
    Route::get('/riwayat-presensi', [PresensiController::class, 'riwayat'])->name('riwayat-presensi');
    Route::get('/tagihan', [SiswaTagihanController::class, 'index'])->name('tagihan.index');
    Route::post('/tagihan/bayar/{id}', [SiswaTagihanController::class, 'bayar'])->name('tagihan.bayar');
});