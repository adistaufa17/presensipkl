<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kategori_tagihans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kategori');
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });

        Schema::create('tagihans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_tagihan'); // Misal: "Uang Kas Bulan Jan"
            $table->decimal('nominal', 12, 2);
            $table->date('jatuh_tempo');
            $table->enum('status', ['belum_bayar', 'pending', 'lunas'])->default('belum_bayar');
            $table->string('bukti_pembayaran')->nullable();
            $table->timestamps();
        });

        Schema::create('tagihan_siswas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tagihan_id')->constrained('tagihans');
            $table->foreignId('siswa_id')->constrained('siswas')->onDelete('cascade');
            $table->integer('bulan_ke');
            $table->date('jatuh_tempo');
            $table->enum('status', ['belum_bayar', 'menunggu_konfirmasi', 'dibayar', 'ditolak', 'terlambat'])->default('belum_bayar');
            $table->string('bukti_pembayaran')->nullable();
            $table->datetime('tanggal_bayar')->nullable();
            $table->foreignId('dikonfirmasi_oleh')->nullable()->constrained('users');
            $table->text('catatan_admin')->nullable();
            $table->timestamps();
            $table->unique(['siswa_id', 'tagihan_id', 'bulan_ke'], 'unique_tagihan_siswa');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tagihan_siswas');
        Schema::dropIfExists('tagihans');
        Schema::dropIfExists('kategori_tagihans');
    }
};