<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pembayarans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Tagihan dibuat pembimbing
            $table->string('jenis')->nullable(); // kos / alat praktik / lain
            $table->integer('nominal');
            $table->integer('bulan'); // 1 - 4
            $table->date('tenggat');

            // Diisi siswa
            $table->string('metode')->nullable(); // cash, transfer
            $table->string('bukti')->nullable();
            $table->foreignId('tagihan_id')->nullable()->constrained('tagihans');
            $table->string('nama_tagihan')->nullable();
            $table->string('tanggal_bayar')->nullable();

            $table->string('kategori');
            
            // Status
            $table->string('status_siswa')->default('belum_bayar'); // belum_bayar, pending
            $table->string('status')->default('pending'); // pending, diterima, ditolak

            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pembayarans', function (Blueprint $table) {
            //
        });
    }
};
