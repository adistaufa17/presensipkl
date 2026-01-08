<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('presensis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswas')->onDelete('cascade');
            $table->date('tanggal');
            $table->time('jam_masuk')->nullable();
            $table->string('foto_masuk')->nullable();
            $table->decimal('latitude_masuk', 10, 8)->nullable();
            $table->decimal('longitude_masuk', 11, 8)->nullable();
            $table->enum('status_kehadiran', ['hadir', 'terlambat', 'izin', 'sakit', 'alpha'])->default('alpha');
            $table->text('keterangan_izin')->nullable();
            $table->string('bukti_izin')->nullable(); 
            $table->time('jam_pulang')->nullable();
            $table->text('jurnal_kegiatan')->nullable();
            $table->timestamps();

            $table->unique(['siswa_id', 'tanggal']);
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('presensis');
    }
};
