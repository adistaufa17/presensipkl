<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tagihans', function (Blueprint $table) {
            // Pastikan kolom nama ada, NOT NULL, dan ada default
            if (!Schema::hasColumn('tagihans', 'nama')) {
                $table->string('nama')->default('')->after('pembimbing_id');
            } else {
                $table->string('nama')->default('')->change();
            }

            // Pastikan kolom kategori ada dan NOT NULL
            if (!Schema::hasColumn('tagihans', 'kategori')) {
                $table->string('kategori')->default('lainnya')->after('nama');
            } else {
                $table->string('kategori')->default('lainnya')->change();
            }

            // Pastikan kolom nominal ada dan NOT NULL
            if (!Schema::hasColumn('tagihans', 'nominal')) {
                $table->integer('nominal')->default(0)->after('kategori');
            } else {
                $table->integer('nominal')->default(0)->change();
            }

            // Pastikan kolom bulan ada dan NOT NULL
            if (!Schema::hasColumn('tagihans', 'bulan')) {
                $table->tinyInteger('bulan')->default(1)->after('nominal');
            } else {
                $table->tinyInteger('bulan')->default(1)->change();
            }

            // Pastikan kolom tenggat ada dan NOT NULL
            if (!Schema::hasColumn('tagihans', 'tenggat')) {
                $table->date('tenggat')->nullable(false)->after('bulan');
            }

            // Pastikan kolom keterangan ada
            if (!Schema::hasColumn('tagihans', 'keterangan')) {
                $table->text('keterangan')->nullable()->after('tenggat');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tagihans', function (Blueprint $table) {
            // Tidak dihapus agar data aman
        });
    }
};
