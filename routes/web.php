<?php

    use App\Http\Controllers\ProfileController;
    use App\Http\Controllers\PembayaranController;
    use App\Http\Controllers\TagihanController;
    use Illuminate\Support\Facades\Route;

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

    // ==========================================
    // ROUTE SISWA (role: siswa)
    // ==========================================
    Route::middleware(['auth'])->group(function () {
        Route::get('/pembayaran/dashboardsiswa', [PembayaranController::class, 'dashboardSiswa'])->name('pembayaran.dashboardsiswa');
        Route::get('/pembayaran/saya', [PembayaranController::class, 'index'])->name('pembayaran.siswa');
        Route::post('/pembayaran/bayar', [PembayaranController::class, 'bayar'])->name('pembayaran.bayar');
    });

    // ==========================================
    // ROUTE PEMBIMBING (role: pembimbing)
    // ==========================================
    Route::middleware(['auth'])->group(function () {
        // Dashboard pembimbing
        Route::get('/pembimbing/dashboard', [PembayaranController::class, 'dashboard'])
            ->name('pembimbing.dashboard');
                
        // Manajemen tagihan pembimbing
        Route::get('/tagihan', [TagihanController::class, 'index'])->name('tagihan.index');
        Route::get('/tagihan/create', [TagihanController::class, 'create'])->name('tagihan.create');
        Route::post('/tagihan', [TagihanController::class, 'store'])->name('tagihan.store');
        Route::delete('/tagihan/{id}', [TagihanController::class, 'destroy'])->name('tagihan.destroy');
        
        // Lihat pembayaran siswa
        Route::get('/pembayaran/semua', [PembayaranController::class, 'semua'])->name('pembayaran.semua');
        Route::get('/pembayaran/siswa/{user_id}', [PembayaranController::class, 'bySiswa'])->name('pembayaran.by_siswa');
        Route::get('/pembayaran/{id}', [PembayaranController::class, 'detail'])->name('pembayaran.detail');
        Route::post('/pembayaran/{id}/status', [PembayaranController::class, 'updateStatus'])->name('pembayaran.status');
        Route::post('/pembayaran/{id}/reset', [PembayaranController::class, 'reset'])->name('pembayaran.reset');
        Route::delete('/pembayaran/{id}', [PembayaranController::class, 'destroy'])->name('pembayaran.destroy');
    });

    require __DIR__.'/auth.php';