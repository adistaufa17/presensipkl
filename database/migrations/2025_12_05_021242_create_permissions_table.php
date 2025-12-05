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
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            $table->date('date');
            $table->enum('type', ['izin', 'sakit']);
            $table->text('reason')->nullable();
            $table->string('proof_path')->nullable();
            
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('note')->nullable()->comment('Catatan dari pembimbing saat verifikasi');
            $table->timestamp('verified_at')->nullable()->comment('Waktu diverifikasi oleh pembimbing');

            $table->timestamps();

            // Index untuk performa query
            $table->index('date');
            $table->index('status');
            $table->index(['user_id', 'date']);
            $table->index(['user_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};