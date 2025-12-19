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

require __DIR__.'/auth.php';
